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
    public function index(Request $request)
    {
        $branchId = session('current_branch_id', auth()->user()->branch_id ?? 1);
        
        $dateParam = $request->get('date');
        if ($dateParam) {
            $businessDate = Carbon::parse($dateParam);
        } else {
            $today = Carbon::today();
            $todayInvoicesCount = Invoice::where(function($q) use ($branchId) {
                $q->where('branch_id', $branchId)->orWhereNull('branch_id');
            })->whereDate('created_at', $today->format('Y-m-d'))->count();

            if ($todayInvoicesCount > 0) {
                $businessDate = $today;
            } else {
                $bDate = CashReconciliation::getCurrentBusinessDate();
                $businessDate = $bDate instanceof Carbon ? $bDate : Carbon::parse($bDate);
            }
        }

        $reconciliation = CashReconciliation::where(function($q) use ($branchId) {
                $q->where('branch_id', $branchId)->orWhereNull('branch_id');
            })
            ->where('date', $businessDate)
            ->first();
        
        // Fetch Invoices for target business date
        $invoices = Invoice::where(function($q) use ($branchId) {
                $q->where('branch_id', $branchId)->orWhereNull('branch_id');
            })
            ->whereDate('created_at', $businessDate)
            ->get();

        $grossSales = (float) $invoices->sum('payable_amount');

        // Card Payments
        $cardSales = (float) $invoices->where('payment_method', 'card')->sum('payable_amount');

        // Bank / Online / QR / EasyPaisa / JazzCash Payments
        $bankSales = (float) $invoices->whereIn('payment_method', ['bank', 'jazzcash', 'easypaisa', 'hbl', 'qr', 'online'])->sum('payable_amount');

        // Split payments bank portion
        $splitBankSales = (float) $invoices->where('payment_method', 'split')->sum('split_bank');

        // Total non-cash payments (Not in physical drawer)
        $nonCashSales = $cardSales + $bankSales + $splitBankSales;

        // Cash Sales (Actual physical cash received in drawer)
        $cashSales = (float) $invoices->where('payment_method', 'cash')->sum('payable_amount');
        if ($invoices->where('payment_method', 'split')->count() > 0) {
            foreach ($invoices->where('payment_method', 'split') as $splitInv) {
                $cashSales += max(0, (float)$splitInv->payable_amount - (float)$splitInv->split_bank);
            }
        }

        // Drawer Expenses
        $totalExpenses = (float) Expense::where(function($q) use ($branchId) {
                $q->where('branch_id', $branchId)->orWhereNull('branch_id');
            })
            ->whereDate('created_at', $businessDate)
            ->where('deducted_from_drawer', true)
            ->sum('amount');

        $openingBalance = (float) ($reconciliation->opening_balance ?? 0);
        $expectedCash = $openingBalance + $cashSales - $totalExpenses;

        // Auto-show opening balance modal if opening balance is not yet set (or zero)
        $showOpeningBalanceModal = (!$reconciliation || $reconciliation->opening_balance === null || (float)$reconciliation->opening_balance == 0);

        return view('reconciliation.index', compact(
            'reconciliation',
            'businessDate',
            'grossSales',
            'cardSales',
            'bankSales',
            'splitBankSales',
            'nonCashSales',
            'cashSales',
            'totalExpenses',
            'openingBalance',
            'expectedCash',
            'showOpeningBalanceModal'
        ));
    }

    public function saveOpeningBalance(Request $request)
    {
        $request->validate([
            'opening_balance' => 'required|numeric|min:0',
            'date' => 'nullable|date',
        ]);

        $businessDate = $request->filled('date')
            ? Carbon::parse($request->date)->format('Y-m-d')
            : CashReconciliation::getCurrentBusinessDate();

        $reconciliation = CashReconciliation::updateOrCreate(
            ['date' => $businessDate],
            [
                'user_id' => auth()->id() ?? 1,
                'opening_balance' => (float) $request->opening_balance,
            ]
        );

        if ($request->wantsJson() || $request->ajax()) {
            return response()->json([
                'success' => true,
                'message' => 'Opening balance set successfully!',
                'opening_balance' => $reconciliation->opening_balance,
            ]);
        }

        return redirect()->back()->with('success', 'Opening balance updated successfully!');
    }

    public function store(Request $request)
    {
        $request->validate([
            'opening_balance' => 'nullable|numeric',
            'actual_cash' => 'required|numeric|min:0',
            'date' => 'nullable|date',
        ]);

        $businessDate = $request->filled('date')
            ? Carbon::parse($request->date)->format('Y-m-d')
            : CashReconciliation::getCurrentBusinessDate();

        $reconciliation = CashReconciliation::updateOrCreate(
            ['date' => $businessDate],
            [
                'user_id' => auth()->id() ?? 1,
                'opening_balance' => (float) ($request->opening_balance ?? 0),
                'actual_cash' => (float) $request->actual_cash,
                'expected_cash' => (float) ($request->expected_cash ?? 0),
                'difference' => ((float)$request->actual_cash) - ((float)($request->expected_cash ?? 0)),
                'status' => (abs(((float)$request->actual_cash) - ((float)($request->expected_cash ?? 0))) <= 1) ? 'matched' : 'discrepancy',
                'is_closed' => true,
            ]
        );

        return redirect()->route('admin.index')->with('success', 'Cash reconciliation closed and marked as Done!');
    }
}

