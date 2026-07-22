<?php

namespace App\Http\Controllers;

use App\Models\Product;
use App\Models\Supplier;
use App\Models\Purchase;
use App\Models\ProductUsage;
use Illuminate\Support\Facades\DB;
use Illuminate\Http\Request;

class InventoryController extends Controller
{
    public function dashboard()
    {
        // Low stock products
        $lowStockProducts = Product::where('track_inventory', true)
            ->whereRaw('current_stock <= min_stock_level')
            ->with('supplier')
            ->orderBy('current_stock')
            ->take(10)
            ->get();

        // Out of stock products
        $outOfStockProducts = Product::where('track_inventory', true)
            ->where('current_stock', '<=', 0)
            ->with('supplier')
            ->get();

        $forSaleCostValue = Product::where('track_inventory', true)
            ->where('product_type', 'retail')
            ->sum(DB::raw('current_stock * COALESCE(cost_price, 0)'));

        $forSaleRetailValue = Product::where('track_inventory', true)
            ->where('product_type', 'retail')
            ->sum(DB::raw('current_stock * COALESCE(selling_price, 0)'));

        $shopUseCostValue = Product::where('track_inventory', true)
            ->where('product_type', 'service_supply')
            ->sum(DB::raw('current_stock * COALESCE(cost_price, 0)'));

        // Recent purchases
        $recentPurchases = Purchase::with('supplier')
            ->orderBy('created_at', 'desc')
            ->take(5)
            ->get();

        // Pending deliveries
        $pendingDeliveries = Purchase::whereIn('status', ['ordered', 'partially_received'])
            ->with('supplier')
            ->orderBy('expected_delivery_date')
            ->take(5)
            ->get();

        // Top used products this month
        $topUsedProducts = ProductUsage::select('product_id', DB::raw('SUM(quantity_used) as total_used'))
            ->whereMonth('usage_date', now()->month)
            ->whereYear('usage_date', now()->year)
            ->with('product')
            ->groupBy('product_id')
            ->orderBy('total_used', 'desc')
            ->take(5)
            ->get();

        // Inventory turnover (simplified)
        $totalProducts = Product::where('track_inventory', true)->count();
        $activeSuppliers = Supplier::where('is_active', true)->count();

        return view('inventory.dashboard', compact(
            'lowStockProducts',
            'outOfStockProducts',
            'forSaleCostValue',
            'forSaleRetailValue',
            'shopUseCostValue',
            'recentPurchases',
            'pendingDeliveries',
            'topUsedProducts',
            'totalProducts',
            'activeSuppliers'
        ));
    }

    public function lowStockAlerts()
    {
        $lowStockProducts = Product::where('track_inventory', true)
            ->whereRaw('current_stock <= min_stock_level')
            ->with(['supplier', 'category'])
            ->orderByRaw('(min_stock_level - current_stock) DESC')
            ->paginate(20);

        return view('inventory.low-stock', compact('lowStockProducts'));
    }

    public function stockReport(Request $request)
    {
        $query = Product::where('track_inventory', true)->with(['supplier', 'category']);

        // Filter by product type
        if ($request->has('type') && $request->type !== 'all') {
            $query->where('product_type', $request->type);
        }

        // Filter by stock status
        if ($request->has('status') && $request->status !== 'all') {
            switch ($request->status) {
                case 'low_stock':
                    $query->whereRaw('current_stock <= min_stock_level');
                    break;
                case 'out_of_stock':
                    $query->where('current_stock', '<=', 0);
                    break;
                case 'in_stock':
                    $query->whereRaw('current_stock > min_stock_level');
                    break;
            }
        }

        $products = $query->orderBy('name')->paginate(25);

        $totalValue = $query->sum(DB::raw('current_stock * COALESCE(cost_price, 0)'));
        $totalItems = $query->sum('current_stock');

        return view('inventory.stock-report', compact('products', 'totalValue', 'totalItems'));
    }

    public function usageReport(Request $request)
    {
        $startDate = $request->get('start_date', now()->startOfMonth());
        $endDate = $request->get('end_date', now()->endOfMonth());

        $usageData = ProductUsage::whereBetween('usage_date', [$startDate, $endDate])
            ->with(['product', 'service'])
            ->select('product_id', 'service_id', DB::raw('SUM(quantity_used) as total_used'))
            ->groupBy('product_id', 'service_id')
            ->orderBy('total_used', 'desc')
            ->get();

        return view('inventory.usage-report', compact('usageData', 'startDate', 'endDate'));
    }

    public function exportStockReport(Request $request)
    {
        $query = Product::where('track_inventory', true)->with(['supplier', 'category']);

        if ($request->has('type') && $request->type !== 'all') {
            $query->where('product_type', $request->type);
        }

        if ($request->has('status') && $request->status !== 'all') {
            switch ($request->status) {
                case 'low_stock':
                    $query->whereRaw('current_stock <= min_stock_level');
                    break;
                case 'out_of_stock':
                    $query->where('current_stock', '<=', 0);
                    break;
                case 'in_stock':
                    $query->whereRaw('current_stock > min_stock_level');
                    break;
            }
        }

        $products = $query->orderBy('name')->get();
        $format = $request->get('format', 'csv');

        $filename = 'stock-report-' . now()->format('Y-m-d') . '.' . $format;
        $headers = [
            'Content-Type' => 'text/csv',
            'Content-Disposition' => 'attachment; filename="' . $filename . '"',
        ];

        $callback = function() use ($products) {
            $file = fopen('php://output', 'w');

            fputcsv($file, [
                'Product Name',
                'SKU',
                'Type',
                'Current Stock',
                'Min Stock Level',
                'Cost Price (PKR)',
                'Selling Price (PKR)',
                'Value (Cost PKR)'
            ]);

            foreach ($products as $product) {
                fputcsv($file, [
                    $product->name,
                    $product->sku,
                    $product->product_type,
                    $product->current_stock,
                    $product->min_stock_level,
                    $product->cost_price,
                    $product->selling_price,
                    $product->current_stock * $product->cost_price
                ]);
            }

            fclose($file);
        };

        return response()->stream($callback, 200, $headers);
    }

    public function exportUsageReport(Request $request)
    {
        $startDate = $request->get('start_date', now()->startOfMonth());
        $endDate = $request->get('end_date', now()->endOfMonth());
        $format = $request->get('format', 'csv');

        $usageData = ProductUsage::whereBetween('usage_date', [$startDate, $endDate])
            ->with(['product', 'service'])
            ->select('product_id', 'service_id', DB::raw('SUM(quantity_used) as total_used'))
            ->groupBy('product_id', 'service_id')
            ->orderBy('total_used', 'desc')
            ->get();

        $filename = 'usage-report-' . now()->format('Y-m-d') . '.' . $format;
        $headers = [
            'Content-Type' => 'text/csv',
            'Content-Disposition' => 'attachment; filename="' . $filename . '"',
        ];

        $callback = function() use ($usageData) {
            $file = fopen('php://output', 'w');

            fputcsv($file, [
                'Product Consumed',
                'SKU',
                'Used In Service',
                'Total Quantity Used'
            ]);

            foreach ($usageData as $usage) {
                fputcsv($file, [
                    $usage->product->name ?? 'Unknown',
                    $usage->product->sku ?? '',
                    $usage->service->name ?? 'Direct Usage',
                    $usage->total_used
                ]);
            }

            fclose($file);
        };

        return response()->stream($callback, 200, $headers);
    }

    public function issueShopUse()
    {
        $products = Product::where('track_inventory', true)
            ->where('current_stock', '>', 0)
            ->orderBy('name')
            ->get();
        return view('inventory.issue-shop-use', compact('products'));
    }

    public function storeShopUse(Request $request)
    {
        $request->validate([
            'product_id' => 'required|exists:products,id',
            'quantity' => 'required|numeric|min:1',
            'notes' => 'nullable|string'
        ]);

        $product = Product::findOrFail($request->product_id);
        
        if ($product->current_stock < $request->quantity) {
            return back()->with('error', 'Not enough stock available.');
        }

        DB::transaction(function() use ($product, $request) {
            $product->decrement('current_stock', $request->quantity);

            ProductUsage::create([
                'product_id' => $product->id,
                'quantity_used' => $request->quantity,
                'usage_date' => now(),
                'notes' => $request->notes ?? 'Issued for shop use'
            ]);

            \App\Models\Expense::create([
                'branch_id' => $product->branch_id,
                'description' => 'Shop Use Product Issue: ' . $product->name . ' (' . $request->quantity . ' qty)',
                'amount' => ($product->cost_price ?? 0) * $request->quantity,
                'deducted_from_drawer' => false,
                'user_id' => auth()->id()
            ]);
        });

        return redirect()->back()->with('success', 'Product issued and expense added successfully.');
    }
}
