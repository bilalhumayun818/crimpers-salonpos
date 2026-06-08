<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Branch;
use App\Models\Staff;
use Illuminate\Support\Facades\DB;

class BranchController extends Controller
{
    public function switch(Request $request)
    {
        $branchId = $request->input('branch_id');
        
        if (Branch::where('id', $branchId)->exists()) {
            session(['current_branch_id' => $branchId]);
        }

        return back()->with('success', 'Branch switched successfully!');
    }

    public function updateHours(Request $request, Branch $branch)
    {
        $validated = $request->validate([
            'opening_time' => 'required|date_format:H:i',
            'closing_time' => 'required|date_format:H:i|after:opening_time',
        ]);

        DB::transaction(function () use ($branch, $validated) {
            $branch->update([
                'opening_time' => $validated['opening_time'],
                'closing_time' => $validated['closing_time'],
            ]);

            Staff::withoutGlobalScopes()
                ->where('branch_id', $branch->id)
                ->update([
                    'shift_start' => $validated['opening_time'],
                    'shift_end' => $validated['closing_time'],
                ]);
        });

        return back()->with('success', "Operating hours and employee default shifts updated for {$branch->name}");
    }
}
