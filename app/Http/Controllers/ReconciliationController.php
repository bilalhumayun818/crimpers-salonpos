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

        $grossSales = (float) $invoices->sum(fn ($invoice) => (float) $invoice->payable_amount + (float) ($invoice->extra_amount ?? 0));

        // Card Payments
        $cardSales = (float) $invoices->where('payment_method', 'card')->sum(fn ($invoice) => $invoice->cash_received !== null
            ? (float) $invoice->cash_received
            : (float) $invoice->payable_amount + (float) ($invoice->extra_amount ?? 0));

        // Bank / Online / QR / EasyPaisa / JazzCash Payments
        $bankInvoices = $invoices->whereIn('payment_method', ['bank', 'jazzcash', 'easypaisa', 'hbl', 'qr', 'online']);
        $bankSales = (float) $bankInvoices->sum(fn ($invoice) => $invoice->cash_received !== null
            ? (float) $invoice->cash_received
            : (float) $invoice->payable_amount + (float) ($invoice->extra_amount ?? 0));

        // Split payments bank portion
        $splitInvoices = $invoices->where('payment_method', 'split');
        $splitBankSales = (float) $splitInvoices->sum(fn ($invoice) => (float) $invoice->split_bank + (float) ($invoice->extra_bank_amount ?? 0));
        $splitCardSales = (float) $splitInvoices->sum(fn ($invoice) => (float) ($invoice->split_card ?? 0) + (float) ($invoice->extra_card_amount ?? 0));

        // Total non-cash payments (Not in physical drawer)
        $nonCashSales = $cardSales + $bankSales + $splitBankSales + $splitCardSales;

        // Cash Sales (Actual physical cash received in drawer)
        $cashSales = (float) $invoices->where('payment_method', 'cash')->sum(function ($invoice) {
            return $invoice->cash_received !== null
                ? max(0, (float) $invoice->cash_received - (float) ($invoice->change_returned ?? 0))
                : (float) $invoice->payable_amount;
        });
        foreach ($splitInvoices as $splitInv) {
            $splitCash = $splitInv->split_cash !== null
                ? (float) $splitInv->split_cash
                : max(0, (float) $splitInv->payable_amount - (float) $splitInv->split_bank - (float) ($splitInv->split_card ?? 0) - (float) ($splitInv->pending_amount ?? 0));
            $cashSales += $splitCash - (float) ($splitInv->change_returned ?? 0)
                + (float) ($splitInv->extra_cash_amount ?? 0);
        }
        // When a non-cash payment exceeds the bill, the difference is returned
        // from the physical drawer and must reduce its expected balance.
        $cashSales -= (float) $invoices->whereIn('payment_method', ['card', 'bank', 'jazzcash', 'easypaisa', 'hbl', 'qr', 'online'])
            ->sum(fn ($invoice) => (float) ($invoice->change_returned ?? 0));

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
            'splitCardSales',
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

