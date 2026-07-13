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
            'amount' => 'required|numeric|min:0.01',
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

        $data = [
            'branch_id' => session('current_branch_id', 1),
            'expense_type' => $expenseType,
            'description' => $request->description ?? '',
            'amount' => $request->amount,
            'deducted_from_drawer' => $request->deducted_from_drawer,
            'user_id' => auth()->id(),
        ];
        
        if ($request->category) {
            $data['category'] = $request->category;
        }
        
        if ($expenseType === 'staff' && $request->staff_id) {
            $data['staff_id'] = $request->staff_id;
        }

        Expense::create($data);

        $typeLabel = $expenseType === 'daily' ? 'Daily Expense' : ($expenseType === 'fixed' ? 'Fixed Expense' : 'Staff Expense');
        return redirect()->back()->with('success', "{$typeLabel} successfully recorded!");
    }
}
