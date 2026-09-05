<?php

namespace App\Http\Controllers;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\CashReconciliation;
use App\Models\Invoice;
use App\Models\Expense;
use Carbon\Carbon;

class ReconciliationController extends Controller
{
    public function index()
    {
        $businessDate = CashReconciliation::getCurrentBusinessDate();
        $reconciliation = CashReconciliation::where('date', $businessDate)->first();
        
        $totalSales = Invoice::whereDate('created_at', $businessDate)
            ->where('payment_method', 'cash')
            ->sum('payable_amount');

        $totalExpenses = Expense::whereDate('created_at', $businessDate)
            ->where('deducted_from_drawer', true)
            ->sum('amount');

        return view('reconciliation.index', compact('reconciliation', 'totalSales', 'totalExpenses', 'businessDate'));
    }

    public function store(Request $request)
    {
        $request->validate([
            'opening_balance' => 'nullable|numeric',
            'actual_cash' => 'nullable|numeric',
            'date' => 'nullable|date',
        ]);

        $businessDate = $request->filled('date')
            ? Carbon::parse($request->date)->format('Y-m-d')
            : CashReconciliation::getCurrentBusinessDate();

        $reconciliation = CashReconciliation::updateOrCreate(
            ['date' => $businessDate],
            [
                'user_id' => auth()->id() ?? 1,
                'opening_balance' => $request->opening_balance ?? 0,
                'actual_cash' => $request->actual_cash ?? 0,
                'expected_cash' => $request->expected_cash ?? 0,
                'difference' => ($request->actual_cash ?? 0) - ($request->expected_cash ?? 0),
                'status' => (abs(($request->actual_cash ?? 0) - ($request->expected_cash ?? 0)) <= 1) ? 'matched' : 'discrepancy',
                'is_closed' => true,
            ]
        );

        return redirect()->route('admin.dashboard')->with('success', 'Cash reconciliation closed and marked as Done!');
    }
}
