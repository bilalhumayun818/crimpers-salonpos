<?php

namespace App\Http\Controllers;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\Invoice;
use App\Models\Appointment;
use App\Models\Customer;
use App\Models\User;
use App\Models\Product;
use App\Models\StaffAttendance;
use App\Models\Coupon;
use App\Models\DiscountRule;
use App\Models\CashReconciliation;
use App\Models\Staff;
use Carbon\Carbon;

class AdminController extends Controller
{
    public function index()
    {
        $today = now()->format('Y-m-d');

        // Auto-cancel late appointments
        Appointment::autoCancelLate();
        \App\Models\StaffAttendance::autoCheckout();
        
        $totalSalesToday = Invoice::whereDate('created_at', Carbon::today())->sum('payable_amount');
        $totalAppointmentsToday = Appointment::where('appointment_date', $today)->count();
        $completedAppointmentsToday = Appointment::where('appointment_date', $today)->where('status', 'completed')->count();

        $weekStart = Carbon::now()->startOfWeek();
        $weekEnd = Carbon::now()->endOfWeek();
        $totalSalesWeek = Invoice::whereBetween('created_at', [$weekStart, $weekEnd])->sum('payable_amount');
        $totalAppointmentsWeek = Appointment::whereBetween('appointment_date', [$weekStart, $weekEnd])->count();

        $totalRevenue      = Invoice::sum('payable_amount');
        $totalAppointments = Appointment::count();
        $totalCustomers    = Customer::count();
        $totalUsers        = User::count();

        // Product / inventory stats
        $totalProducts      = Product::count();
        $lowStockProducts   = Product::where('track_inventory', true)
                                ->whereColumn('current_stock', '<=', 'min_stock_level')
                                ->count();
        $outOfStockProducts = Product::where('track_inventory', true)
                                ->where('current_stock', '<=', 0)
                                ->count();
        $inventoryValue     = (float) Product::where('track_inventory', true)
                                ->selectRaw('SUM(current_stock * COALESCE(cost_price, 0)) as val')
                                ->value('val');
        
        $inventoryValueSell = (float) Product::where('track_inventory', true)
                                ->selectRaw('SUM(current_stock * COALESCE(selling_price, 0)) as val')
                                ->value('val');

        $lowStockList = Product::where('track_inventory', true)
                        ->whereRaw('current_stock <= min_stock_level')
                        ->orderBy('current_stock')
                        ->get();

        $recentAppointments = Appointment::with(['staff', 'service'])->latest()->take(5)->get();
        $lateAppointments = Appointment::where('status', 'cancelled')
                            ->where('appointment_date', $today)
                            ->where('notes', 'like', '%[System: Auto-discarded]%')
                            ->get();
        $recentInvoices     = Invoice::with('user')->latest()->take(5)->get();

        // Staff Presence Logic
        $allStaff = Staff::where('status', true)->get();
        $staffPresentToday = $allStaff->filter(function($s) {
            return $s->is_present_today && $s->is_on_shift;
        })->count();
        $totalStaff = $allStaff->count();
        
        $activeCoupons = Coupon::where('is_active', true)
                        ->where(function($q) use ($today) {
                            $q->whereNull('valid_until')->orWhere('valid_until', '>=', $today);
                        })->count();
        $activeDiscounts = DiscountRule::where('is_active', true)->count();
        
        $reconciliationDone = CashReconciliation::whereDate('created_at', $today)->exists();

        // Chart Data: Daily Sales (Last 7 Days)
        $dailySales = [];
        for ($i = 6; $i >= 0; $i--) {
            $date = Carbon::today()->subDays($i);
            $dailySales[] = [
                'label' => $date->format('D'),
                'value' => (float) Invoice::whereDate('created_at', $date)->sum('payable_amount')
            ];
        }

        // Chart Data: Weekly Sales (Last 4 Weeks)
        $weeklySales = [];
        for ($i = 3; $i >= 0; $i--) {
            $start = Carbon::now()->subWeeks($i)->startOfWeek();
            $end = Carbon::now()->subWeeks($i)->endOfWeek();
            $weeklySales[] = [
                'label' => 'Week ' . ($i == 0 ? ' (Current)' : '-' . $i),
                'value' => (float) Invoice::whereBetween('created_at', [$start, $end])->sum('payable_amount')
            ];
        }

        // Chart Data: Monthly Sales (Current Year)
        $monthlySales = [];
        for ($i = 1; $i <= 12; $i++) {
            $monthlySales[] = [
                'label' => Carbon::create()->month($i)->format('M'),
                'value' => (float) Invoice::whereYear('created_at', Carbon::now()->year)
                                    ->whereMonth('created_at', $i)
                                    ->sum('payable_amount')
            ];
        }

        return view('admin.index', compact(
            'totalSalesToday', 'totalAppointmentsToday', 'completedAppointmentsToday',
            'totalSalesWeek', 'totalAppointmentsWeek',
            'totalRevenue', 'totalAppointments', 'totalCustomers', 'totalUsers',
            'totalProducts', 'lowStockProducts', 'outOfStockProducts', 'inventoryValue', 'inventoryValueSell',
            'lowStockList', 'recentAppointments', 'recentInvoices', 'lateAppointments',
            'staffPresentToday', 'totalStaff', 'activeCoupons', 'activeDiscounts', 'reconciliationDone',
            'dailySales', 'weeklySales', 'monthlySales'
        ));
    }

    public function getChartData(Request $request)
    {
        $type = $request->get('type', 'daily'); // daily, weekly, monthly, custom
        $drawerMode = $request->get('drawer_mode', 'all'); // all, drawer, non_drawer
        $dateFrom = $request->get('date_from');
        $dateTo = $request->get('date_to');

        // Build queries
        $salesQuery = Invoice::query();
        $expenseQuery = \App\Models\Expense::query();

        // Filter expense drawer mode
        if ($drawerMode === 'drawer') {
            $expenseQuery->where('deducted_from_drawer', true);
        } elseif ($drawerMode === 'non_drawer') {
            $expenseQuery->where('deducted_from_drawer', false);
        }

        // Define intervals
        $labels = [];
        $salesData = [];
        $expenseData = [];
        $profitData = [];

        if ($type === 'daily') {
            // Last 7 days
            for ($i = 6; $i >= 0; $i--) {
                $date = Carbon::today()->subDays($i);
                $labels[] = $date->format('D (M d)');
                
                $sales = (float) (clone $salesQuery)->whereDate('created_at', $date)->sum('payable_amount');
                $expenses = (float) (clone $expenseQuery)->whereDate('created_at', $date)->sum('amount');
                
                $salesData[] = $sales;
                $expenseData[] = $expenses;
                $profitData[] = $sales - $expenses;
            }
        } elseif ($type === 'weekly') {
            // Last 4 weeks
            for ($i = 3; $i >= 0; $i--) {
                $start = Carbon::now()->subWeeks($i)->startOfWeek();
                $end = Carbon::now()->subWeeks($i)->endOfWeek();
                $labels[] = 'Week ' . ($i == 0 ? '(Current)' : '-' . $i);

                $sales = (float) (clone $salesQuery)->whereBetween('created_at', [$start, $end])->sum('payable_amount');
                $expenses = (float) (clone $expenseQuery)->whereBetween('created_at', [$start, $end])->sum('amount');

                $salesData[] = $sales;
                $expenseData[] = $expenses;
                $profitData[] = $sales - $expenses;
            }
        } elseif ($type === 'monthly') {
            // 12 Months of the current year
            for ($i = 1; $i <= 12; $i++) {
                $labels[] = Carbon::create()->month($i)->format('M');

                $sales = (float) (clone $salesQuery)->whereYear('created_at', Carbon::now()->year)->whereMonth('created_at', $i)->sum('payable_amount');
                $expenses = (float) (clone $expenseQuery)->whereYear('created_at', Carbon::now()->year)->whereMonth('created_at', $i)->sum('amount');

                $salesData[] = $sales;
                $expenseData[] = $expenses;
                $profitData[] = $sales - $expenses;
            }
        } elseif ($type === 'custom' && $dateFrom && $dateTo) {
            // Custom Date Range
            $start = Carbon::parse($dateFrom)->startOfDay();
            $end = Carbon::parse($dateTo)->endOfDay();
            
            $diffDays = $start->diffInDays($end);
            
            // Fast aggregate query: Fetch all records in one single query!
            $allSales = (clone $salesQuery)->whereBetween('created_at', [$start, $end])
                ->selectRaw('DATE(created_at) as date, SUM(payable_amount) as total')
                ->groupBy('date')
                ->pluck('total', 'date');

            $allExpenses = (clone $expenseQuery)->whereBetween('created_at', [$start, $end])
                ->selectRaw('DATE(created_at) as date, SUM(amount) as total')
                ->groupBy('date')
                ->pluck('total', 'date');

            if ($diffDays > 45) {
                // Group by week
                $current = clone $start;
                while ($current->lte($end)) {
                    $wStart = clone $current;
                    $wEnd = (clone $current)->endOfWeek();
                    if ($wEnd->gt($end)) {
                        $wEnd = clone $end;
                    }
                    $labels[] = $wStart->format('M d') . ' - ' . $wEnd->format('M d');

                    $salesSum = 0;
                    $expenseSum = 0;
                    
                    $tempDate = clone $wStart;
                    while ($tempDate->lte($wEnd)) {
                        $dateStr = $tempDate->format('Y-m-d');
                        $salesSum += $allSales[$dateStr] ?? 0;
                        $expenseSum += $allExpenses[$dateStr] ?? 0;
                        $tempDate->addDay();
                    }

                    $salesData[] = (float) $salesSum;
                    $expenseData[] = (float) $expenseSum;
                    $profitData[] = (float) ($salesSum - $expenseSum);

                    $current->addWeek();
                }
            } else {
                // Group by day
                $current = clone $start;
                while ($current->lte($end)) {
                    $dateStr = $current->format('Y-m-d');
                    $labels[] = $current->format('M d');

                    $sales = (float) ($allSales[$dateStr] ?? 0);
                    $expenses = (float) ($allExpenses[$dateStr] ?? 0);

                    $salesData[] = $sales;
                    $expenseData[] = $expenses;
                    $profitData[] = $sales - $expenses;

                    $current->addDay();
                }
            }
        }

        return response()->json([
            'labels' => $labels,
            'sales' => $salesData,
            'expenses' => $expenseData,
            'profit' => $profitData
        ]);
    }

    public function branchSelect()
    {
        $branches = \App\Models\Branch::where('is_active', true)->get();
        return view('admin.branch_select', compact('branches'));
    }

    public function branchSwitchFromSelect(Request $request)
    {
        $branchId = $request->input('branch_id');
        if (\App\Models\Branch::where('id', $branchId)->exists()) {
            session(['current_branch_id' => $branchId]);
        }
        return redirect()->route('admin.index')->with('success', 'Branch selected successfully.');
    }
}
