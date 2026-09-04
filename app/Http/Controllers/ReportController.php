<?php

namespace App\Http\Controllers;

use App\Http\Controllers\Controller;
use App\Models\Customer;
use App\Models\Invoice;
use App\Models\InvoiceItem;
use App\Models\Product;
use App\Models\ProductUsage;
use App\Models\Service;
use App\Models\Staff;
use App\Models\StaffAttendance;
use Illuminate\Support\Facades\DB;
use Carbon\Carbon;

class ReportController extends Controller
{
    public function index()
    {
        $now = Carbon::now();
        $today = Carbon::today();
        $yesterday = Carbon::yesterday();
        $last30Days = Carbon::today()->subDays(29)->startOfDay();

        $totalSalesToday = Invoice::whereDate('created_at', $today)->sum('payable_amount');
        $transactionCountToday = Invoice::whereDate('created_at', $today)->count();
        $yesterdaySales = Invoice::whereDate('created_at', $yesterday)->sum('payable_amount');
        $salesTrend = $yesterdaySales > 0
            ? round((($totalSalesToday - $yesterdaySales) / $yesterdaySales) * 100, 1)
            : null;

        $serviceRevenue = InvoiceItem::where('itemizable_type', Service::class)->sum('subtotal');
        $productRevenue = InvoiceItem::where('itemizable_type', Product::class)->sum('subtotal');
        $itemRevenueTotal = $serviceRevenue + $productRevenue;

        $serviceRevenueShare = $itemRevenueTotal > 0
            ? round(($serviceRevenue / $itemRevenueTotal) * 100, 1)
            : 0;
        $productRevenueShare = $itemRevenueTotal > 0
            ? round(($productRevenue / $itemRevenueTotal) * 100, 1)
            : 0;

        $topServicesRaw = InvoiceItem::selectRaw('itemizable_id, SUM(quantity) as total_quantity, SUM(subtotal) as total_revenue')
            ->where('itemizable_type', Service::class)
            ->groupBy('itemizable_id')
            ->orderByDesc('total_revenue')
            ->limit(5)
            ->get();

        $serviceIds = $topServicesRaw->pluck('itemizable_id')->all();
        $services = Service::whereIn('id', $serviceIds)->get()->keyBy('id');
        $topServices = $topServicesRaw->map(function ($row) use ($services) {
            return [
                'service' => $services->get($row->itemizable_id),
                'quantity' => (int) $row->total_quantity,
                'revenue' => (float) $row->total_revenue,
            ];
        })->filter(fn ($item) => $item['service'] !== null);

        $busyHours = Invoice::selectRaw('HOUR(created_at) as hour, COUNT(*) as invoice_count, SUM(payable_amount) as revenue')
            ->groupBy('hour')
            ->orderByDesc('invoice_count')
            ->limit(6)
            ->get()
            ->sortBy('hour');

        $staffPerformance = Staff::with('upsellPerformance')
            ->get()
            ->sortByDesc(fn ($staff) => $staff->upsellPerformance?->upsell_revenue ?? 0)
            ->take(5);

        $activeCustomers = Customer::whereHas('invoices', function ($query) use ($last30Days, $now) {
            $query->whereBetween('created_at', [$last30Days, $now]);
        })->count();

        $newCustomers = Customer::whereHas('invoices', function ($query) use ($last30Days, $now) {
            $query->whereBetween('created_at', [$last30Days, $now]);
        })->whereDoesntHave('invoices', function ($query) use ($last30Days) {
            $query->whereDate('created_at', '<', $last30Days);
        })->count();

        $returningCustomers = Customer::whereHas('invoices', function ($query) use ($last30Days, $now) {
            $query->whereBetween('created_at', [$last30Days, $now]);
        })->whereHas('invoices', function ($query) use ($last30Days) {
            $query->whereDate('created_at', '<', $last30Days);
        })->count();

        $retentionRate = $activeCustomers > 0
            ? round(($returningCustomers / $activeCustomers) * 100, 1)
            : 0;

        $directProductCost = InvoiceItem::where('itemizable_type', Product::class)
            ->join('products', 'invoice_items.itemizable_id', '=', 'products.id')
            ->selectRaw('SUM(invoice_items.quantity * COALESCE(products.cost_price, 0)) as total_cost')
            ->value('total_cost') ?? 0;

        $serviceSupplyCost = DB::table('product_usages')
            ->join('products', 'product_usages.product_id', '=', 'products.id')
            ->selectRaw('SUM(product_usages.quantity_used * COALESCE(products.cost_price, 0)) as total_cost')
            ->value('total_cost') ?? 0;

        $totalCost = $directProductCost + $serviceSupplyCost;
        $totalRevenue = Invoice::sum('payable_amount');
        $grossProfit = $totalRevenue - $totalCost;
        $profitMargin = $totalRevenue > 0
            ? round(($grossProfit / $totalRevenue) * 100, 1)
            : 0;

        return view('reports.index', compact(
            'totalSalesToday',
            'transactionCountToday',
            'salesTrend',
            'serviceRevenue',
            'productRevenue',
            'serviceRevenueShare',
            'productRevenueShare',
            'topServices',
            'busyHours',
            'staffPerformance',
            'activeCustomers',
            'newCustomers',
            'returningCustomers',
            'retentionRate',
            'directProductCost',
            'serviceSupplyCost',
            'totalCost',
            'totalRevenue',
            'grossProfit',
            'profitMargin'
        ));
    }

    public function posReport(\Illuminate\Http\Request $request)
    {
        $dateFrom = $request->get('date_from', Carbon::today()->format('Y-m-d'));
        $dateTo = $request->get('date_to', Carbon::today()->format('Y-m-d'));

        $start = Carbon::parse($dateFrom)->startOfDay();
        $end = Carbon::parse($dateTo)->endOfDay();

        $invoices = Invoice::with(['user', 'staff', 'customer', 'items.itemizable'])
            ->whereBetween('created_at', [$start, $end])
            ->latest()
            ->get();

        $totalSales = $invoices->sum('payable_amount');
        $totalTax = $invoices->sum('tax');
        $totalDiscount = $invoices->sum('discount');
        
        $paymentMethods = [
            'cash' => $invoices->where('payment_method', 'cash')->sum('payable_amount'),
            'card' => $invoices->where('payment_method', 'card')->sum('payable_amount'),
            'jazzcash' => $invoices->where('payment_method', 'jazzcash')->sum('payable_amount'),
            'easypaisa' => $invoices->where('payment_method', 'easypaisa')->sum('payable_amount'),
            'multiple' => $invoices->where('payment_method', 'multiple')->sum('payable_amount'),
        ];

        return view('reports.pos', compact(
            'invoices', 'totalSales', 'totalTax', 'totalDiscount', 
            'paymentMethods', 'dateFrom', 'dateTo'
        ));
    }

    public function exportPosReport(\Illuminate\Http\Request $request)
    {
        $dateFrom = $request->get('date_from', Carbon::today()->format('Y-m-d'));
        $dateTo = $request->get('date_to', Carbon::today()->format('Y-m-d'));
        $format = $request->get('format', 'csv'); // csv or xls

        $start = Carbon::parse($dateFrom)->startOfDay();
        $end = Carbon::parse($dateTo)->endOfDay();

        $invoices = Invoice::with(['user', 'staff', 'customer', 'items.itemizable'])
            ->whereBetween('created_at', [$start, $end])
            ->latest()
            ->get();

        $filename = 'pos-report-' . now()->format('Y-m-d') . '.' . $format;
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
                'Staff',
                'Payment Method',
                'Amount',
                'Tax',
                'Discount'
            ]);

            // CSV data
            foreach ($invoices as $invoice) {
                fputcsv($file, [
                    $invoice->invoice_no,
                    $invoice->created_at->format('Y-m-d H:i:s'),
                    $invoice->customer_name ?? ($invoice->customer->name ?? 'Walk-in'),
                    $invoice->staff->name ?? 'N/A',
                    $invoice->payment_method,
                    $invoice->payable_amount,
                    $invoice->tax,
                    $invoice->discount
                ]);
            }

            fclose($file);
        };

        return response()->stream($callback, 200, $headers);
    }

    public function staffReport(\Illuminate\Http\Request $request)
    {
        $search   = $request->get('search', '');
        $roleId   = $request->get('role_id', '');
        $status   = $request->get('status', '');

        $query = Staff::with('role');

        if ($search) {
            $query->where(function ($q) use ($search) {
                $q->where('name', 'like', '%' . $search . '%')
                  ->orWhere('phone', 'like', '%' . $search . '%')
                  ->orWhere('email', 'like', '%' . $search . '%');
            });
        }

        if ($roleId !== '') {
            $query->where('staff_role_id', $roleId);
        }

        if ($status !== '') {
            $query->where('status', (bool) $status);
        }

        $staffs = $query->get();
        $roles  = \App\Models\StaffRole::orderBy('name')->get();

        return view('reports.staff', compact('staffs', 'roles', 'search', 'roleId', 'status'));
    }

    public function exportStaffReport(\Illuminate\Http\Request $request)
    {
        $format = $request->get('format', 'csv');
        $staffs = Staff::with('role')->get();
        
        $filename = 'staff-report-' . now()->format('Y-m-d') . '.' . $format;
        $headers = [
            'Content-Type' => 'text/csv',
            'Content-Disposition' => 'attachment; filename="' . $filename . '"',
        ];

        $callback = function() use ($staffs) {
            $file = fopen('php://output', 'w');
            fputcsv($file, ['Name', 'Role', 'Email', 'Phone', 'Status', 'Hiring Date']);
            
            foreach ($staffs as $staff) {
                fputcsv($file, [
                    $staff->name,
                    $staff->role->name ?? 'N/A',
                    $staff->email,
                    $staff->phone,
                    $staff->status ? 'Active' : 'Inactive',
                    $staff->hiring_date ? $staff->hiring_date->format('Y-m-d') : 'N/A'
                ]);
            }
            fclose($file);
        };

        return response()->stream($callback, 200, $headers);
    }

    public function attendanceReport(\Illuminate\Http\Request $request)
    {
        $dateFrom   = $request->get('date_from', Carbon::today()->startOfMonth()->format('Y-m-d'));
        $dateTo     = $request->get('date_to', Carbon::today()->format('Y-m-d'));
        $staffSearch = $request->get('staff_search', '');
        $statusFilter = $request->get('status_filter', '');

        $query = StaffAttendance::with('staff')
            ->whereBetween('attendance_date', [$dateFrom, $dateTo]);

        if ($staffSearch) {
            $query->whereHas('staff', function ($q) use ($staffSearch) {
                $q->where('name', 'like', '%' . $staffSearch . '%');
            });
        }

        if ($statusFilter !== '') {
            $query->where('status', $statusFilter);
        }

        $attendances = $query->orderBy('attendance_date', 'desc')->get();

        return view('reports.attendance', compact('attendances', 'dateFrom', 'dateTo', 'staffSearch', 'statusFilter'));
    }

    public function exportAttendanceReport(\Illuminate\Http\Request $request)
    {
        $dateFrom = $request->get('date_from', Carbon::today()->startOfMonth()->format('Y-m-d'));
        $dateTo = $request->get('date_to', Carbon::today()->format('Y-m-d'));
        $format = $request->get('format', 'csv');

        $attendances = StaffAttendance::with('staff')
            ->whereBetween('attendance_date', [$dateFrom, $dateTo])
            ->orderBy('attendance_date', 'desc')
            ->get();

        $filename = 'attendance-report-' . now()->format('Y-m-d') . '.' . $format;
        $headers = [
            'Content-Type' => 'text/csv',
            'Content-Disposition' => 'attachment; filename="' . $filename . '"',
        ];

        $callback = function() use ($attendances) {
            $file = fopen('php://output', 'w');
            fputcsv($file, ['Date', 'Staff', 'Status', 'Check In', 'Check Out', 'Hours Worked']);
            
            foreach ($attendances as $attendance) {
                fputcsv($file, [
                    $attendance->attendance_date ? $attendance->attendance_date->format('Y-m-d') : '',
                    $attendance->staff->name ?? 'N/A',
                    ucfirst($attendance->status),
                    $attendance->check_in_time ? $attendance->check_in_time->format('h:i A') : '',
                    $attendance->check_out_time ? $attendance->check_out_time->format('h:i A') : '',
                    $attendance->hours_worked
                ]);
            }
            fclose($file);
        };

        return response()->stream($callback, 200, $headers);
    }

    public function salaryReport(\Illuminate\Http\Request $request)
    {
        $search = $request->get('search', '');

        $query = Staff::where('status', true);

        if ($search) {
            $query->where('name', 'like', '%' . $search . '%');
        }

        $staffs = $query->get();

        return view('reports.salary', compact('staffs', 'search'));
    }

    public function exportSalaryReport(\Illuminate\Http\Request $request)
    {
        $format = $request->get('format', 'csv');
        $staffs = Staff::where('status', true)->get();
        
        $filename = 'salary-report-' . now()->format('Y-m-d') . '.' . $format;
        $headers = [
            'Content-Type' => 'text/csv',
            'Content-Disposition' => 'attachment; filename="' . $filename . '"',
        ];

        $callback = function() use ($staffs) {
            $file = fopen('php://output', 'w');
            fputcsv($file, ['Staff Name', 'Base Salary', 'Earned Commission', 'Daily Salaries Paid', 'Absent Deductions', 'Advances Taken', 'Deductions', 'Net Payable', 'Last Paid At']);
            
            foreach ($staffs as $staff) {
                fputcsv($file, [
                    $staff->name,
                    $staff->base_salary,
                    $staff->total_earned_commission,
                    $staff->current_cycle_daily_salaries,
                    $staff->current_cycle_absent_deductions,
                    $staff->current_cycle_advances,
                    $staff->current_cycle_deductions,
                    $staff->net_salary_payable,
                    $staff->last_paid_at ? $staff->last_paid_at->format('Y-m-d') : 'Never'
                ]);
            }
            fclose($file);
        };

        return response()->stream($callback, 200, $headers);
    }
    public function customerPurchasesReport(\Illuminate\Http\Request $request)
    {
        $dateFrom = $request->get('date_from', Carbon::today()->startOfMonth()->format('Y-m-d'));
        $dateTo = $request->get('date_to', Carbon::today()->format('Y-m-d'));

        $start = Carbon::parse($dateFrom)->startOfDay();
        $end = Carbon::parse($dateTo)->endOfDay();

        $invoices = Invoice::with(['items.itemizable', 'customer', 'staff'])
            ->whereBetween('created_at', [$start, $end])
            ->latest()
            ->get();

        // Group by customer
        $customers = [];
        foreach ($invoices as $invoice) {
            $custId = $invoice->customer_id ?? 'walk-in-' . $invoice->id; // treat null as individual walk-in or group by name
            if (!$invoice->customer_id) {
                // Group walk-ins by name if possible
                $custId = 'walkin_' . md5(strtolower(trim($invoice->customer_name)));
            }
            
            if (!isset($customers[$custId])) {
                $customers[$custId] = [
                    'name' => $invoice->customer_name ?? ($invoice->customer->name ?? 'Walk-in Customer'),
                    'phone' => $invoice->customer->phone ?? '—',
                    'invoices' => [],
                    'total_billed' => 0,
                    'total_paid' => 0,
                    'total_pending' => 0,
                    'items_bought' => []
                ];
            }

            $customers[$custId]['invoices'][] = $invoice;
            $customers[$custId]['total_billed'] += $invoice->payable_amount;
            
            $pending = floatval($invoice->pending_amount);
            $customers[$custId]['total_pending'] += $pending;
            $customers[$custId]['total_paid'] += ($invoice->payable_amount - $pending);

            foreach ($invoice->items as $item) {
                $itemName = $item->custom_name ?? ($item->itemizable->name ?? 'Unknown Item');
                $customers[$custId]['items_bought'][] = $itemName . ' (Qty: ' . $item->quantity . ')';
            }
        }

        return view('reports.customer-purchases', compact('customers', 'dateFrom', 'dateTo'));
    }

    public function employeeCustomersReport(\Illuminate\Http\Request $request)
    {
        $dateFrom = $request->get('date_from', Carbon::today()->startOfMonth()->format('Y-m-d'));
        $dateTo = $request->get('date_to', Carbon::today()->format('Y-m-d'));

        $start = Carbon::parse($dateFrom)->startOfDay();
        $end = Carbon::parse($dateTo)->endOfDay();

        $invoices = Invoice::with(['items', 'customer'])
            ->whereBetween('created_at', [$start, $end])
            ->latest()
            ->get();

        $employeeRecords = [];

        foreach ($invoices as $invoice) {
            $staffList = [];
            if (!empty($invoice->staff_names)) {
                $names = array_map('trim', explode(',', $invoice->staff_names));
                foreach ($names as $name) {
                    if ($name) $staffList[] = $name;
                }
            } elseif ($invoice->staff) {
                $staffList[] = $invoice->staff->name;
            } else {
                $staffList[] = 'Unassigned';
            }

            foreach ($staffList as $staffName) {
                if (!isset($employeeRecords[$staffName])) {
                    $employeeRecords[$staffName] = [];
                }

                $services = $invoice->items->pluck('custom_name')->toArray();
                
                $employeeRecords[$staffName][] = [
                    'date' => $invoice->created_at,
                    'invoice_no' => $invoice->invoice_no,
                    'customer' => $invoice->customer_name ?? ($invoice->customer->name ?? 'Walk-in Customer'),
                    'services' => implode(', ', $services),
                    'invoice_total' => $invoice->payable_amount
                ];
            }
        }

        // Sort records by date descending inside each employee
        foreach ($employeeRecords as $name => &$records) {
            usort($records, function($a, $b) {
                return $b['date'] <=> $a['date'];
            });
        }

        return view('reports.employee-customers', compact('employeeRecords', 'dateFrom', 'dateTo'));
    }
}
