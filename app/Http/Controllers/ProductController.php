<?php

namespace App\Http\Controllers;

use App\Models\Product;
use App\Models\Category;
use App\Models\Supplier;
use App\Models\Purchase;
use App\Models\PurchaseItem;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Storage;

class ProductController extends Controller
{
    public function index(Request $request)
    {
        $query = Product::with(['category', 'supplier']);

        // Filter by product type
        if ($request->has('type') && $request->type !== 'all') {
            $query->where('product_type', $request->type);
        }

        // Filter by stock status
        if ($request->has('stock_status') && $request->stock_status !== 'all') {
            switch ($request->stock_status) {
                    case 'low_stock':
                    $query->where('current_stock', '<=', DB::raw('min_stock_level'))
                          ->where('track_inventory', true);
                    break;
                case 'out_of_stock':
                    $query->where('current_stock', '<=', 0)
                          ->where('track_inventory', true);
                    break;
                case 'in_stock':
                    $query->where('current_stock', '>', DB::raw('min_stock_level'))
                          ->where('track_inventory', true);
                    break;
            }
        }

        // Search by name or SKU
        if ($request->has('search') && !empty($request->search)) {
            $query->where(function($q) use ($request) {
                $q->where('name', 'like', '%' . $request->search . '%')
                  ->orWhere('sku', 'like', '%' . $request->search . '%')
                  ->orWhere('barcode', 'like', '%' . $request->search . '%');
            });
        }

        $products = $query->paginate(15);

        return view('products.index', compact('products'));
    }

    public function create()
    {
        $categories = Category::where('type', 'product')->get();
        $suppliers = Supplier::where('is_active', true)->get();

        return view('products.create', compact('categories', 'suppliers'));
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'category_id' => 'nullable|exists:categories,id',
            'supplier_id' => 'nullable|exists:suppliers,id',
            'name' => 'required|string|max:255',
            'description' => 'nullable|string',
            'selling_price' => 'nullable|numeric|min:0',
            'cost_price' => 'nullable|numeric|min:0',
            'current_stock' => 'required|numeric|min:0',
            'min_stock_level' => 'required|numeric|min:0',
            'product_type' => 'required|in:retail,service_supply',
            'sku' => 'nullable|string|unique:products,sku',
            'track_inventory' => 'nullable',
            'supplier_id' => 'nullable|exists:suppliers,id',
            'category_id' => 'nullable|exists:categories,id'
        ]);

        // After validation, nullable fields come through as null when left empty
        $sell = isset($validated['selling_price']) && $validated['selling_price'] !== null && $validated['selling_price'] !== '' ? (float) $validated['selling_price'] : null;
        $cost = isset($validated['cost_price']) && $validated['cost_price'] !== null && $validated['cost_price'] !== '' ? (float) $validated['cost_price'] : null;

        if ($sell === null && $cost !== null) {
            // Only cost given — selling price defaults to cost
            $validated['selling_price'] = $cost;
            $validated['cost_price'] = $cost;
        } elseif ($cost === null && $sell !== null) {
            // Only selling price given — cost defaults to selling price
            $validated['selling_price'] = $sell;
            $validated['cost_price'] = $sell;
        } elseif ($sell === null && $cost === null) {
            $validated['selling_price'] = 0;
            $validated['cost_price'] = 0;
        } else {
            $validated['selling_price'] = $sell;
            $validated['cost_price'] = $cost;
        }

        $validated['track_inventory'] = $request->has('track_inventory');

        DB::transaction(function() use ($validated, $request) {
            $product = Product::create($validated);
            
            if ($request->has('created_at') && $request->created_at) {
                $product->created_at = $request->created_at;
                $product->save();
            }

            // If initial stock is > 0, record it as a purchase history
            if ($product->current_stock > 0) {
                $supplierId = $product->supplier_id ?? Supplier::first()->id ?? null;
                
                if ($supplierId) {
                    $purchase = Purchase::create([
                        'purchase_order_number' => 'INIT-' . strtoupper(bin2hex(random_bytes(3))),
                        'supplier_id' => $supplierId,
                        'order_date' => now(),
                        'status' => 'received',
                        'total_amount' => $product->current_stock * ($product->cost_price ?? 0),
                        'notes' => 'Initial stock on product creation'
                    ]);

                    if ($purchase && $purchase->id) {
                        $purchase->update([
                            'purchase_order_number' => 'PO-' . date('Y') . '-' . str_pad($purchase->id, 4, '0', STR_PAD_LEFT)
                        ]);

                        PurchaseItem::create([
                            'purchase_id' => $purchase->id,
                            'product_id' => $product->id,
                            'quantity_ordered' => $product->current_stock,
                            'quantity_received' => $product->current_stock,
                            'unit_cost' => $product->cost_price ?? 0,
                            'line_total' => $product->current_stock * ($product->cost_price ?? 0)
                        ]);
                    }
                }
            }
        });

        return redirect()->route('products.index')->with('success', 'Product created successfully and recorded in purchase history.');
    }

    public function show(Product $product)
    {
        $product->load([
            'category', 
            'supplier', 
            'productUsages.service',
            'productUsages.invoice',
            'purchaseItems.purchase.supplier',
            'invoiceItems.invoice'
        ]);

        return view('products.show', compact('product'));
    }

    public function edit(Product $product)
    {
        $categories = Category::where('type', 'product')->get();
        $suppliers = Supplier::where('is_active', true)->get();

        return view('products.edit', compact('product', 'categories', 'suppliers'));
    }

    public function update(Request $request, Product $product)
    {
        $validated = $request->validate([
            'category_id' => 'nullable|exists:categories,id',
            'supplier_id' => 'nullable|exists:suppliers,id',
            'name' => 'required|string|max:255',
            'description' => 'nullable|string',
            'selling_price' => 'nullable|numeric|min:0',
            'cost_price' => 'nullable|numeric|min:0',
            'current_stock' => 'required|numeric|min:0',
            'min_stock_level' => 'required|numeric|min:0',
            'product_type' => 'required|in:retail,service_supply',
            'sku' => 'nullable|string|unique:products,sku,' . $product->id,
            'track_inventory' => 'nullable'
        ]);

        // After validation, nullable fields come through as null when left empty
        $sell = isset($validated['selling_price']) && $validated['selling_price'] !== null && $validated['selling_price'] !== '' ? (float) $validated['selling_price'] : null;
        $cost = isset($validated['cost_price']) && $validated['cost_price'] !== null && $validated['cost_price'] !== '' ? (float) $validated['cost_price'] : null;

        if ($sell === null && $cost !== null) {
            // Only cost given — selling price defaults to cost
            $validated['selling_price'] = $cost;
            $validated['cost_price'] = $cost;
        } elseif ($cost === null && $sell !== null) {
            // Only selling price given — cost defaults to selling price
            $validated['selling_price'] = $sell;
            $validated['cost_price'] = $sell;
        } elseif ($sell === null && $cost === null) {
            $validated['selling_price'] = 0;
            $validated['cost_price'] = 0;
        } else {
            $validated['selling_price'] = $sell;
            $validated['cost_price'] = $cost;
        }

        $validated['track_inventory'] = $request->has('track_inventory');

        $oldSellingPrice = $product->selling_price;
        $oldCostPrice = $product->cost_price;

        $product->update($validated);
        
        if ($oldSellingPrice != $product->selling_price || $oldCostPrice != $product->cost_price) {
            \App\Models\ProductPriceHistory::create([
                'product_id' => $product->id,
                'user_id' => auth()->id(),
                'old_selling_price' => $oldSellingPrice,
                'new_selling_price' => $product->selling_price,
                'old_cost_price' => $oldCostPrice,
                'new_cost_price' => $product->cost_price,
            ]);
        }

        if ($request->has('created_at') && $request->created_at) {
            $product->created_at = $request->created_at;
            $product->save();
        }

        return redirect()->route('products.index')->with('success', 'Product updated successfully.');
    }

    public function destroy(Product $product)
    {
        // We allow deletion. Database cascades will handle purchase_items and product_usages.
        // For invoice_items (morph), we clean them up manually if needed, 
        // but often we keep them for history. Here we just delete the product.
        
        DB::transaction(function() use ($product) {
            // Clean up invoice items manually since they are polymorphic and don't cascade
            \App\Models\InvoiceItem::where('itemizable_id', $product->id)
                ->where('itemizable_type', 'App\Models\Product')
                ->delete();

            $product->delete();
        });

        return redirect()->route('products.index')->with('success', 'Product and related history deleted successfully.');
    }

    public function showAdjustStock(Product $product)
    {
        return view('products.adjust-stock', compact('product'));
    }

    public function adjustStock(Request $request, Product $product)
    {
        $validated = $request->validate([
            'adjustment_type' => 'required|in:add,subtract',
            'quantity' => 'required|numeric|min:0',
            'reason' => 'required|string|max:255',
            'cost_price' => 'nullable|numeric|min:0',
            'selling_price' => 'nullable|numeric|min:0',
        ]);

        $oldStock = $product->current_stock;
        $oldSellingPrice = $product->selling_price;
        $oldCostPrice = $product->cost_price;

        DB::transaction(function() use ($validated, $product, $oldSellingPrice, $oldCostPrice) {
            $newSellingPrice = $product->product_type === 'retail' && $validated['selling_price'] !== null
                ? $validated['selling_price']
                : $product->selling_price;

            $newCostPrice = $validated['cost_price'] !== null
                ? $validated['cost_price']
                : $product->cost_price;

            $product->update([
                'cost_price' => $newCostPrice,
                'selling_price' => $newSellingPrice,
            ]);

            if ($oldSellingPrice != $product->selling_price || $oldCostPrice != $product->cost_price) {
                \App\Models\ProductPriceHistory::create([
                    'product_id' => $product->id,
                    'user_id' => auth()->id(),
                    'old_selling_price' => $oldSellingPrice,
                    'new_selling_price' => $product->selling_price,
                    'old_cost_price' => $oldCostPrice,
                    'new_cost_price' => $product->cost_price,
                ]);
            }

            switch ($validated['adjustment_type']) {
                case 'add':
                    $product->addStock($validated['quantity']);
                    
                    // Create purchase record for stock addition
                    $supplierId = $product->supplier_id ?? Supplier::first()->id ?? null;
                    if ($supplierId) {
                        $purchase = Purchase::create([
                            'purchase_order_number' => 'ADJ-' . strtoupper(bin2hex(random_bytes(3))),
                            'supplier_id' => $supplierId,
                            'order_date' => now(),
                            'status' => 'received',
                            'total_amount' => $validated['quantity'] * ($product->cost_price ?? 0),
                            'notes' => 'Stock adjustment: ' . $validated['reason']
                        ]);

                        if ($purchase && $purchase->id) {
                            $purchase->update([
                                'purchase_order_number' => 'PO-' . date('Y') . '-' . str_pad($purchase->id, 4, '0', STR_PAD_LEFT)
                            ]);

                            PurchaseItem::create([
                                'purchase_id' => $purchase->id,
                                'product_id' => $product->id,
                                'quantity_ordered' => $validated['quantity'],
                                'quantity_received' => $validated['quantity'],
                                'unit_cost' => $product->cost_price ?? 0,
                                'line_total' => $validated['quantity'] * ($product->cost_price ?? 0)
                            ]);
                        }
                    }
                    break;
                case 'subtract':
                    $product->deductStock($validated['quantity']);
                    break;
            }
        });

        return redirect()->back()->with('success', 'Stock adjusted successfully and recorded in history.');
    }
}
