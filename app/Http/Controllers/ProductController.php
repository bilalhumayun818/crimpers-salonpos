<?php

namespace App\Http\Controllers;

use App\Models\Product;
use App\Models\Category;
use App\Models\Supplier;
use App\Models\Purchase;
use App\Models\PurchaseItem;
use App\Models\ProductUsage;
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

        // Filter by product category
        if ($request->filled('category_id')) {
            $query->where('category_id', $request->category_id);
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

        $products = $query->orderBy('name', 'asc')->paginate(15)->withQueryString();
        $categories = Category::where('type', 'product')->orderBy('name')->get();

        return view('products.index', compact('products', 'categories'));
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
            'invoiceItems.invoice',
            'priceHistories.user'
        ]);

        $suppliers = Supplier::where('is_active', true)->orderBy('name')->get();

        return view('products.show', compact('product', 'suppliers'));
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
        $suppliers = Supplier::where('is_active', true)->orderBy('name')->get();

        return view('products.adjust-stock', compact('product', 'suppliers'));
    }

    public function adjustStock(Request $request, Product $product)
    {
        $validated = $request->validate([
            'adjustment_type' => 'required|in:add,subtract',
            'quantity' => 'required|numeric|min:0',
            'reason' => 'required|string|max:255',
            'supplier_id' => 'nullable|exists:suppliers,id',
            'cost_price' => 'nullable|numeric|min:0',
            'selling_price' => 'nullable|numeric|min:0',
        ]);

        $oldStock = $product->current_stock;
        $oldSellingPrice = $product->selling_price;
        $oldCostPrice = $product->cost_price;

        DB::transaction(function() use ($validated, $product, $oldStock, $oldSellingPrice, $oldCostPrice) {
            $newSellingPrice = $validated['selling_price'] !== null
                ? $validated['selling_price']
                : $product->selling_price;

            $newCostPrice = $validated['cost_price'] !== null
                ? $validated['cost_price']
                : $product->cost_price;

            $product->update([
                'supplier_id' => $validated['supplier_id'] ?? null,
                'cost_price' => $newCostPrice,
                'selling_price' => $newSellingPrice,
            ]);

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
                                'unit_selling_price' => $product->selling_price ?? 0,
                                'line_total' => $validated['quantity'] * ($product->cost_price ?? 0)
                            ]);
                        }
                    }
                    break;
                case 'subtract':
                    $product->deductStock($validated['quantity']);

                    // Record removed stock in Purchase History as a negative adjustment.
                    $supplierId = $product->supplier_id ?? Supplier::first()?->id;
                    if ($supplierId) {
                        $purchase = Purchase::create([
                            'purchase_order_number' => 'ADJ-REM-' . strtoupper(bin2hex(random_bytes(3))),
                            'supplier_id' => $supplierId,
                            'order_date' => now(),
                            'status' => 'received',
                            'total_amount' => -($validated['quantity'] * ($product->cost_price ?? 0)),
                            'notes' => 'Stock removal adjustment: ' . $validated['reason'],
                        ]);

                        $purchase->update([
                            'purchase_order_number' => 'ADJ-REM-' . date('Y') . '-' . str_pad($purchase->id, 4, '0', STR_PAD_LEFT),
                        ]);

                        PurchaseItem::create([
                            'purchase_id' => $purchase->id,
                            'product_id' => $product->id,
                            'quantity_ordered' => -$validated['quantity'],
                            'quantity_received' => -$validated['quantity'],
                            'unit_cost' => $product->cost_price ?? 0,
                            'unit_selling_price' => $product->selling_price ?? 0,
                            'line_total' => -($validated['quantity'] * ($product->cost_price ?? 0)),
                        ]);
                    }
                    break;
            }

            $product->refresh();

            // Log stock & price history
            \App\Models\ProductPriceHistory::create([
                'product_id' => $product->id,
                'user_id' => auth()->id(),
                'old_stock' => $oldStock,
                'new_stock' => $product->current_stock,
                'old_selling_price' => $oldSellingPrice,
                'new_selling_price' => $product->selling_price,
                'old_cost_price' => $oldCostPrice,
                'new_cost_price' => $product->cost_price,
                'reason' => 'Stock Adjustment (' . ucfirst($validated['adjustment_type']) . ' ' . $validated['quantity'] . ' units): ' . $validated['reason'],
            ]);
        });

        return redirect()->back()->with('success', 'Stock adjusted successfully and recorded in history.');
    }

    public function updateAdjustment(Request $request, PurchaseItem $item)
    {
        $validated = $request->validate([
            'quantity' => 'required|numeric',
            'unit_cost' => 'nullable|numeric|min:0',
            'selling_price' => 'nullable|numeric|min:0',
            'notes' => 'nullable|string|max:500',
            'supplier_id' => 'nullable|exists:suppliers,id',
        ]);

        $product = $item->product;
        $purchase = $item->purchase;

        DB::transaction(function() use ($item, $product, $purchase, $validated) {
            $oldQty = (float) $item->quantity_ordered;
            $newQty = (float) $validated['quantity'];
            $qtyDiff = $newQty - $oldQty;

            $oldStock = $product ? $product->current_stock : 0;
            $oldCost = $product ? $product->cost_price : 0;
            $oldSell = $product ? $product->selling_price : 0;

            if ($product && $product->track_inventory) {
                if ($qtyDiff < 0 && ($product->current_stock + $qtyDiff) < 0) {
                    throw \Illuminate\Validation\ValidationException::withMessages([
                        'quantity' => "Cannot reduce adjustment quantity. Product current stock would become negative."
                    ]);
                }
                $product->increment('current_stock', $qtyDiff);
            }

            $unitCost = isset($validated['unit_cost']) && $validated['unit_cost'] !== null
                ? (float) $validated['unit_cost']
                : (float) $item->unit_cost;

            $newSellingPrice = isset($validated['selling_price']) && $validated['selling_price'] !== null
                ? (float) $validated['selling_price']
                : ($product ? $product->selling_price : null);

            // Check if this item belongs to the latest (most recent) active adjustment for the product
            $latestItem = PurchaseItem::where('product_id', $product->id)
                ->whereHas('purchase', function($q) {
                    $q->where('status', '!=', 'cancelled');
                })
                ->orderBy('id', 'desc')
                ->first();

            $isLatestAdjustment = ($latestItem && $latestItem->id == $item->id);

            if ($product) {
                // ONLY update the product's active cost price AND selling price if this is the latest adjustment
                // Past adjustments: only update that item's own unit_cost and unit_selling_price — never touch product prices
                if ($isLatestAdjustment) {
                    $product->update([
                        'cost_price'    => $unitCost,
                        'selling_price' => $newSellingPrice ?? $oldSell,
                    ]);
                    $product->refresh();
                }

                \App\Models\ProductPriceHistory::create([
                    'product_id'        => $product->id,
                    'user_id'           => auth()->id(),
                    'old_stock'         => $oldStock,
                    'new_stock'         => $product->current_stock,
                    'old_selling_price' => $oldSell,
                    'new_selling_price' => $isLatestAdjustment ? $product->selling_price : $oldSell,
                    'old_cost_price'    => $oldCost,
                    'new_cost_price'    => $isLatestAdjustment ? $product->cost_price : $oldCost,
                    'reason'            => ($isLatestAdjustment ? 'Edited Latest Adjustment' : 'Edited Past Adjustment')
                        . ' (Qty: ' . $oldQty . ' → ' . $newQty
                        . ', Cost: PKR ' . number_format($unitCost, 2)
                        . ', Sell: PKR ' . number_format($newSellingPrice ?? $oldSell, 2) . ')'
                        . (!empty($validated['notes']) ? ' — ' . $validated['notes'] : ''),
                ]);
            }

            $lineTotal = $newQty * $unitCost;

            $item->update([
                'quantity_ordered' => $newQty,
                'quantity_received' => $newQty,
                'unit_cost' => $unitCost,
                'unit_selling_price' => $newSellingPrice ?? $item->unit_selling_price ?? $product->selling_price,
                'line_total' => $lineTotal,
            ]);

            if ($purchase) {
                $purchaseUpdate = [
                    'total_amount' => $lineTotal,
                ];
                if (isset($validated['notes'])) {
                    $purchaseUpdate['notes'] = $validated['notes'];
                }
                if (!empty($validated['supplier_id'])) {
                    $purchaseUpdate['supplier_id'] = $validated['supplier_id'];
                }
                $purchase->update($purchaseUpdate);
            }
        });

        return redirect()->back()->with('success', 'Purchase / Inventory adjustment updated successfully.');
    }

    public function destroyAdjustment(PurchaseItem $item)
    {
        DB::transaction(function() use ($item) {
            $product = $item->product;
            $purchase = $item->purchase;
            $qtyOrdered = (float) $item->quantity_ordered;
            $oldStock = $product ? $product->current_stock : 0;

            if ($product && $product->track_inventory) {
                $product->decrement('current_stock', $qtyOrdered);
                $product->refresh();
            }

            if ($purchase) {
                if ($purchase->status === 'cancelled') {
                    // Hard delete if user clicks delete again on a cancelled entry
                    $item->delete();
                    if ($purchase->purchaseItems()->count() === 0) {
                        $purchase->delete();
                    }
                } else {
                    $latestItemBeforeCancel = PurchaseItem::where('product_id', $product->id)
                        ->whereHas('purchase', function($q) {
                            $q->where('status', '!=', 'cancelled');
                        })
                        ->orderBy('id', 'desc')
                        ->first();

                    $wasLatest = ($latestItemBeforeCancel && $latestItemBeforeCancel->id == $item->id);

                    // Soft cancellation in history log
                    $cancelNote = " [Cancelled/Deleted by " . (auth()->user()?->name ?? 'User') . " on " . now()->format('M d, Y H:i') . "]";
                    $purchase->update([
                        'status' => 'cancelled',
                        'notes' => ($purchase->notes ?? '') . $cancelNote,
                    ]);

                    if ($wasLatest && $product) {
                        $nextLatest = PurchaseItem::where('product_id', $product->id)
                            ->whereHas('purchase', function($q) {
                                $q->where('status', '!=', 'cancelled');
                            })
                            ->orderBy('id', 'desc')
                            ->first();

                        if ($nextLatest && $nextLatest->unit_cost) {
                            $product->update([
                                'cost_price' => $nextLatest->unit_cost,
                            ]);
                            $product->refresh();
                        }
                    }

                    if ($product) {
                        \App\Models\ProductPriceHistory::create([
                            'product_id' => $product->id,
                            'user_id' => auth()->id(),
                            'old_stock' => $oldStock,
                            'new_stock' => $product->current_stock,
                            'old_selling_price' => $product->selling_price,
                            'new_selling_price' => $product->selling_price,
                            'old_cost_price' => $product->cost_price,
                            'new_cost_price' => $product->cost_price,
                            'reason' => 'Cancelled Adjustment (' . ($qtyOrdered > 0 ? '-' : '+') . abs($qtyOrdered) . ' units stock reverted)',
                        ]);
                    }
                }
            } else {
                $item->delete();
            }
        });

        return redirect()->back()->with('success', 'Purchase / Inventory adjustment status updated and stock reverted.');
    }
}
