<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Expense;
use App\Models\Staff;

class ExpenseController extends Controller
{
    public function create()
    {
        $staff = Staff::where('status', true)->orderBy('name')->get();
        return view('expenses.create', compact('staff'));
    }

    public function store(Request $request)
    {
        $expenseType = $request->expense_type ?? 'daily';
        
        // Validate based on expense type
        $rules = [
            'expense_type' => 'required|in:daily,fixed,staff',
            'amount' => 'required|numeric|min:0.00',
            'deducted_from_drawer' => 'required|boolean',
        ];
        
        if ($expenseType === 'daily') {
            $rules['description'] = 'required|string|max:1000';
        } elseif ($expenseType === 'fixed') {
            $rules['category'] = 'required|string|max:100';
            $rules['description'] = 'required|string|max:1000';
        } elseif ($expenseType === 'staff') {
            $rules['staff_id'] = 'required|exists:staff,id';
            $rules['category'] = 'required|string|max:100';
            $rules['description'] = 'nullable|string|max:1000';
        }
        
        $request->validate($rules);

        $staffMember = null;
        if ($expenseType === 'staff' && $request->staff_id) {
            $staffMember = Staff::find($request->staff_id);
        }

        $category = $request->category ?? '';
        $amount = (float) $request->amount;
        $description = $request->description ?? '';

        // If category is full salary, calculate net salary payable and override amount & description
        if ($expenseType === 'staff' && $staffMember && in_array($category, ['full_salary', 'salary'])) {
            $base = (float) ($staffMember->base_salary ?? 0);
            $comm = (float) ($staffMember->total_earned_commission ?? 0);
            $adv  = $staffMember->current_cycle_advances;
            $ded  = $staffMember->current_cycle_deductions;
            $net  = $staffMember->net_salary_payable;

            $amount = $net;
            $description = "Full Salary Paid to {$staffMember->name} (Base: PKR " . number_format($base, 2) . 
                           ", Commission: PKR " . number_format($comm, 2) . 
                           ", Advances: PKR " . number_format($adv, 2) . 
                           ", Deductions: PKR " . number_format($ded, 2) . 
                           ", Net Paid: PKR " . number_format($net, 2) . ")";
            if ($request->filled('description')) {
                $description .= " - Note: " . $request->description;
            }
        }

        $data = [
            'branch_id' => session('current_branch_id', 1),
            'expense_type' => $expenseType,
            'description' => $description,
            'amount' => $amount,
            'deducted_from_drawer' => $request->deducted_from_drawer,
            'user_id' => auth()->id(),
        ];
        
        if ($category) {
            $data['category'] = $category;
        }
        
        if ($expenseType === 'staff' && $request->staff_id) {
            $data['staff_id'] = $request->staff_id;
        }

        Expense::create($data);

        // Reset commission cycle ONLY if full salary is paid
        if ($expenseType === 'staff' && $staffMember && in_array($category, ['full_salary', 'salary'])) {
            $staffMember->update([
                'total_earned_commission' => 0,
                'last_paid_at'            => now(),
            ]);
        }

        $typeLabel = $expenseType === 'daily' ? 'Daily Expense' : ($expenseType === 'fixed' ? 'Fixed Expense' : 'Staff Expense');
        return redirect()->back()->with('success', "{$typeLabel} successfully recorded!");
    }
}
