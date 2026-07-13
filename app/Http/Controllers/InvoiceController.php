<?php

namespace App\Http\Controllers;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;

use App\Models\Invoice;
use App\Models\Purchase;
use App\Models\CashReconciliation;
use App\Models\Supplier;
use App\Models\Expense;
use Carbon\Carbon;

class InvoiceController extends Controller
{
    public function index(Request $request)
    {
        $user = auth()->user();
        
        // Determine the allowed tabs
        $canViewSales = $user->hasPermission('sales', 'view');
        $canViewPurchases = $user->hasPermission('purchases', 'view');
        $canViewReconciliation = $user->hasPermission('reconciliation', 'view');
        $canViewExpenses = $user->hasPermission('sales', 'view'); // Same as sales

        // Total fallback: If they have NO history permissions, block them
        if (!$canViewSales && !$canViewPurchases && !$canViewReconciliation && !$canViewExpenses) {
            abort(403, 'Access Denied: You do not have permission to view any history records.');
        }

        // Determine which tab to show
        $tab = $request->input('tab');

        // If no tab specified, pick the first one they are allowed to see
        if (!$tab) {
            if ($canViewSales) $tab = 'sales';
            elseif ($canViewPurchases) $tab = 'purchases';
            elseif ($canViewReconciliation) $tab = 'reconciliation';
            elseif ($canViewExpenses) $tab = 'expenses';
            else $tab = 'sales'; // Default to sales if nothing else
        }

        // We will handle specific permission checks INSIDE the sections below or in the view
        // to show a friendly "Forbidden" message instead of a hard 403 redirect.

        if ($tab === 'purchases') {
            return $this->purchaseHistory($request);
        } elseif ($tab === 'reconciliation') {
            return $this->reconciliationHistory($request);
        } elseif ($tab === 'expenses') {
            return $this->expenseHistory($request);
        }

        $query = Invoice::with(['customer', 'user', 'staff', 'items.itemizable']);

        // Apply date range filter
        if ($request->filled('date_from') && $request->filled('date_to')) {
            $query->whereDate('created_at', '>=', $request->date_from)
                  ->whereDate('created_at', '<=', $request->date_to);
        }

        // Apply period filter
        if ($request->filled('period')) {
            switch ($request->period) {
                case 'today':
                    $query->whereDate('created_at', Carbon::today());
                    break;
                case 'yesterday':
                    $query->whereDate('created_at', Carbon::yesterday());
                    break;
                case 'this_week':
                    $query->whereBetween('created_at', [
                        Carbon::now()->startOfWeek(),
                        Carbon::now()->endOfWeek()
                    ]);
                    break;
                case 'last_week':
                    $query->whereBetween('created_at', [
                        Carbon::now()->subWeek()->startOfWeek(),
                        Carbon::now()->subWeek()->endOfWeek()
                    ]);
                    break;
                case 'this_month':
                    $query->whereMonth('created_at', Carbon::now()->month)
                          ->whereYear('created_at', Carbon::now()->year);
                    break;
                case 'last_month':
                    $query->whereMonth('created_at', Carbon::now()->subMonth()->month)
                          ->whereYear('created_at', Carbon::now()->subMonth()->year);
                    break;
                case 'this_year':
                    $query->whereYear('created_at', Carbon::now()->year);
                    break;
                case 'last_year':
                    $query->whereYear('created_at', Carbon::now()->subYear()->year);
                    break;
            }
        }

        // Apply payment method filter
        if ($request->filled('payment_method')) {
            $query->where('payment_method', $request->payment_method);
        }

        // Apply status filter
        if ($request->filled('status')) {
            $query->where('status', $request->status);
        }

        // Apply customer filter
        if ($request->filled('customer')) {
            $searchTerm = $request->customer;
            $customerIds = \App\Models\Customer::all()->filter(function($c) use ($searchTerm) {
                return stripos($c->name, $searchTerm) !== false;
            })->pluck('id');

            $query->where(function($q) use ($customerIds, $searchTerm) {
                if ($customerIds->isNotEmpty()) {
                    $q->whereIn('customer_id', $customerIds);
                } else {
                    // Fallback to purely customer_name if no customer matched (so we don't accidentally match all if it's empty, though it's empty only if no match found)
                    $q->where('customer_id', -1); // Force false condition for the relation
                }
                $q->orWhere('customer_name', 'like', '%' . $searchTerm . '%');
            });
        }

        // Apply customer phone filter
        if ($request->filled('customer_phone')) {
            $searchTerm = $request->customer_phone;
            $customerIds = \App\Models\Customer::all()->filter(function($c) use ($searchTerm) {
                return stripos($c->phone, $searchTerm) !== false;
            })->pluck('id');
            $query->whereIn('customer_id', $customerIds->isEmpty() ? [-1] : $customerIds);
        }

        // Apply invoice number filter
        if ($request->filled('invoice_no')) {
            $query->where('invoice_no', 'like', '%' . $request->invoice_no . '%');
        }

        // Apply staff filter
        if ($request->filled('staff_name')) {
            $searchTerm = $request->staff_name;
            $staffIds = \App\Models\Staff::all()->filter(function($s) use ($searchTerm) {
                return stripos($s->name, $searchTerm) !== false;
            })->pluck('id');
            $query->whereIn('staff_id', $staffIds->isEmpty() ? [-1] : $staffIds);
        }

        // Apply amount range filters
        if ($request->filled('min_amount')) {
            $query->where('payable_amount', '>=', $request->min_amount);
        }
        if ($request->filled('max_amount')) {
            $query->where('payable_amount', '<=', $request->max_amount);
        }

        $invoices = $query->latest()->paginate(20)->withQueryString();

        // Calculate summary statistics for filtered results
        $totalSales = $query->sum('payable_amount');
        $totalInvoices = $query->count();

        // Calculate period invoices (for the current period if period filter is applied)
        $periodQuery = Invoice::query();
        if ($request->filled('period')) {
            switch ($request->period) {
                case 'today':
                    $periodQuery->whereDate('created_at', Carbon::today());
                    break;
                case 'yesterday':
                    $periodQuery->whereDate('created_at', Carbon::yesterday());
                    break;
                case 'this_week':
                    $periodQuery->whereBetween('created_at', [
                        Carbon::now()->startOfWeek(),
                        Carbon::now()->endOfWeek()
                    ]);
                    break;
                case 'last_week':
                    $periodQuery->whereBetween('created_at', [
                        Carbon::now()->subWeek()->startOfWeek(),
                        Carbon::now()->subWeek()->endOfWeek()
                    ]);
                    break;
                case 'this_month':
                    $periodQuery->whereMonth('created_at', Carbon::now()->month)
                               ->whereYear('created_at', Carbon::now()->year);
                    break;
                case 'last_month':
                    $periodQuery->whereMonth('created_at', Carbon::now()->subMonth()->month)
                               ->whereYear('created_at', Carbon::now()->subMonth()->year);
                    break;
                case 'this_year':
                    $periodQuery->whereYear('created_at', Carbon::now()->year);
                    break;
                case 'last_year':
                    $periodQuery->whereYear('created_at', Carbon::now()->subYear()->year);
                    break;
            }
        } else {
            // If no period filter, show today's count
            $periodQuery->whereDate('created_at', Carbon::today());
        }
        $periodInvoices = $periodQuery->count();

        return view('invoices.index', compact(
            'invoices', 'totalSales', 'totalInvoices', 'periodInvoices',
            'canViewSales', 'canViewPurchases', 'canViewReconciliation', 'canViewExpenses'
        ));
    }

    /**
     * USER DEMAND: "no ticket no recetp i want to print"
     * Consolidated all viewing logic to a professional administrative layout.
     * Thermal ticket view (pos.ticket) is no longer utilized.
     */
    public function show(Invoice $invoice)
    {
        $invoice->load(['items.itemizable', 'customer', 'user', 'staff']);
        return view('invoices.history-detail', compact('invoice'));
    }

    public function historyShow(Invoice $invoice)
    {
        $invoice->load(['items.itemizable', 'customer', 'user', 'staff']);
        return view('invoices.history-detail', compact('invoice'));
    }

    public function export(Request $request)
    {
        $query = Invoice::with(['customer', 'user', 'staff']);

        // Apply same filters as index method
        if ($request->filled('date_from') && $request->filled('date_to')) {
            $query->whereDate('created_at', '>=', $request->date_from)
                  ->whereDate('created_at', '<=', $request->date_to);
        }

        // Apply period filter
        if ($request->filled('period')) {
            switch ($request->period) {
                case 'today':
                    $query->whereDate('created_at', Carbon::today());
                    break;
                case 'yesterday':
                    $query->whereDate('created_at', Carbon::yesterday());
                    break;
                case 'this_week':
                    $query->whereBetween('created_at', [
                        Carbon::now()->startOfWeek(),
                        Carbon::now()->endOfWeek()
                    ]);
                    break;
                case 'last_week':
                    $query->whereBetween('created_at', [
                        Carbon::now()->subWeek()->startOfWeek(),
                        Carbon::now()->subWeek()->endOfWeek()
                    ]);
                    break;
                case 'this_month':
                    $query->whereMonth('created_at', Carbon::now()->month)
                          ->whereYear('created_at', Carbon::now()->year);
                    break;
                case 'last_month':
                    $query->whereMonth('created_at', Carbon::now()->subMonth()->month)
                          ->whereYear('created_at', Carbon::now()->subMonth()->year);
                    break;
                case 'this_year':
                    $query->whereYear('created_at', Carbon::now()->year);
                    break;
                case 'last_year':
                    $query->whereYear('created_at', Carbon::now()->subYear()->year);
                    break;
            }
        }

        // Apply payment method filter
        if ($request->filled('payment_method')) {
            $query->where('payment_method', $request->payment_method);
        }

        // Apply status filter
        if ($request->filled('status')) {
            $query->where('status', $request->status);
        }

        // Apply customer filter
        if ($request->filled('customer')) {
            $searchTerm = $request->customer;
            $customerIds = \App\Models\Customer::all()->filter(function($c) use ($searchTerm) {
                return stripos($c->name, $searchTerm) !== false;
            })->pluck('id');

            $query->where(function($q) use ($customerIds, $searchTerm) {
                if ($customerIds->isNotEmpty()) {
                    $q->whereIn('customer_id', $customerIds);
                } else {
                    $q->where('customer_id', -1);
                }
                $q->orWhere('customer_name', 'like', '%' . $searchTerm . '%');
            });
        }

        // Apply invoice number filter
        if ($request->filled('invoice_no')) {
            $query->where('invoice_no', 'like', '%' . $request->invoice_no . '%');
        }

        // Apply staff filter
        if ($request->filled('staff_name')) {
            $searchTerm = $request->staff_name;
            $staffIds = \App\Models\Staff::all()->filter(function($s) use ($searchTerm) {
                return stripos($s->name, $searchTerm) !== false;
            })->pluck('id');
            $query->whereIn('staff_id', $staffIds->isEmpty() ? [-1] : $staffIds);
        }

        // Apply amount range filters
        if ($request->filled('min_amount')) {
            $query->where('payable_amount', '>=', $request->min_amount);
        }
        if ($request->filled('max_amount')) {
            $query->where('payable_amount', '<=', $request->max_amount);
        }

        $invoices = $query->latest()->get();

        if ($request->format === 'pdf') {
            $pdf = app('dompdf.wrapper');
            $pdf->loadView('invoices.export-pdf', compact('invoices'));
            return $pdf->download('sales-report-' . now()->format('Y-m-d') . '.pdf');
        }

        // CSV Export
        $filename = 'sales-report-' . now()->format('Y-m-d') . '.csv';
        $headers = [
            'Content-Type' => 'text/csv',
            'Content-Disposition' => 'attachment; filename="' . $filename . '"',
        ];

        $callback = function() use ($invoices) {
            $file = fopen('php://output', 'w');

            // CSV headers
            fputcsv($file, [
                'Invoice #',
                'Date',
                'Customer',
                'Employee',
                'Total Amount',
                'Payment Method',
                'Status',
                'Items'
            ]);

            // CSV data
            foreach ($invoices as $invoice) {
                fputcsv($file, [
                    $invoice->invoice_no,
                    $invoice->created_at->format('Y-m-d H:i:s'),
                    $invoice->customer ? $invoice->customer->name : ($invoice->customer_name ?? 'Walk-in Customer'),
                    $invoice->staff ? $invoice->staff->name : '-',
                    number_format($invoice->payable_amount, 2),
                    $invoice->payment_method,
                    $invoice->status,
                    $invoice->items->count() . ' items'
                ]);
            }

            fclose($file);
        };

        return response()->stream($callback, 200, $headers);
    }
    public function purchaseHistory(Request $request)
    {
        $query = Purchase::with(['supplier', 'purchaseItems.product']);

        if ($request->filled('date_from') && $request->filled('date_to')) {
            $query->whereDate('order_date', '>=', $request->date_from)
                  ->whereDate('order_date', '<=', $request->date_to);
        }

        if ($request->filled('supplier_id')) {
            $query->where('supplier_id', $request->supplier_id);
        }

        if ($request->filled('supplier_name')) {
            $query->whereHas('supplier', function($q) use ($request) {
                $q->where('name', 'like', '%' . $request->supplier_name . '%');
            });
        }

        if ($request->filled('status')) {
            $query->where('status', $request->status);
        }

        $purchases = $query->latest('order_date')->paginate(20)->withQueryString();
        $suppliers = Supplier::where('is_active', true)->get();
        $user = auth()->user();
        $canViewSales = $user->hasPermission('sales', 'view');
        $canViewPurchases = $user->hasPermission('purchases', 'view');
        $canViewReconciliation = $user->hasPermission('reconciliation', 'view');
        $canViewExpenses = $user->hasPermission('sales', 'view');

        return view('invoices.index', compact(
            'purchases', 'suppliers',
            'canViewSales', 'canViewPurchases', 'canViewReconciliation', 'canViewExpenses'
        ));
    }

    public function reconciliationHistory(Request $request)
    {
        $user = auth()->user();
        $query = CashReconciliation::with('user');

        if ($request->filled('date_from') && $request->filled('date_to')) {
            $query->whereDate('date', '>=', $request->date_from)
                  ->whereDate('date', '<=', $request->date_to);
        }

        if ($request->filled('status')) {
            $query->where('status', $request->status);
        }

        $reconciliations = $query->latest('date')->paginate(20)->withQueryString();

        $canViewSales = $user->hasPermission('sales', 'view');
        $canViewPurchases = $user->hasPermission('purchases', 'view');
        $canViewReconciliation = $user->hasPermission('reconciliation', 'view');
        $canViewExpenses = $user->hasPermission('sales', 'view');

        return view('invoices.index', compact(
            'reconciliations',
            'canViewSales', 'canViewPurchases', 'canViewReconciliation', 'canViewExpenses'
        ));
    }

    public function expenseHistory(Request $request)
    {
        $user = auth()->user();
        $query = Expense::with('user', 'staff');

        if ($request->filled('date_from') && $request->filled('date_to')) {
            $query->whereDate('created_at', '>=', $request->date_from)
                  ->whereDate('created_at', '<=', $request->date_to);
        }

        if ($request->filled('deducted_from_drawer')) {
            $query->where('deducted_from_drawer', $request->deducted_from_drawer);
        }

        if ($request->filled('expense_type')) {
            $query->where('expense_type', $request->expense_type);
        }

        // Fetch all matching expenses so we can decrypt and filter in-memory
        $expensesCollection = $query->latest('created_at')->get();

        if ($request->filled('search')) {
            $searchVal = strtolower($request->search);
            $expensesCollection = $expensesCollection->filter(function($exp) use ($searchVal) {
                $desc = strtolower($exp->description ?? '');
                $userName = strtolower($exp->user->name ?? '');
                $staffName = strtolower($exp->staff?->name ?? '');
                
                return str_contains($desc, $searchVal) || str_contains($userName, $searchVal) || str_contains($staffName, $searchVal);
            });
        }

        // Paginate the collection manually
        $currentPage = \Illuminate\Pagination\LengthAwarePaginator::resolveCurrentPage();
        $perPage = 20;
        $currentItems = $expensesCollection->slice(($currentPage - 1) * $perPage, $perPage)->values();
        
        $expenses = new \Illuminate\Pagination\LengthAwarePaginator(
            $currentItems,
            $expensesCollection->count(),
            $perPage,
            $currentPage,
            ['path' => \Illuminate\Pagination\LengthAwarePaginator::resolveCurrentPath(), 'query' => $request->query()]
        );

        $canViewSales = $user->hasPermission('sales', 'view');
        $canViewPurchases = $user->hasPermission('purchases', 'view');
        $canViewReconciliation = $user->hasPermission('reconciliation', 'view');
        $canViewExpenses = $user->hasPermission('sales', 'view');

        return view('invoices.index', compact(
            'expenses',
            'canViewSales', 'canViewPurchases', 'canViewReconciliation', 'canViewExpenses'
        ));
    }
}
