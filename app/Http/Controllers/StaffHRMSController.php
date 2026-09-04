<?php

namespace App\Http\Controllers;

use App\Models\Staff;
use App\Models\StaffAttendance;
use App\Models\StaffShift;
use Illuminate\Http\Request;
use Carbon\Carbon;

class StaffHRMSController extends Controller
{
    public function index()
    {
        StaffAttendance::autoCheckout();
        
        $staff = Staff::all();
        $today = Carbon::today()->toDateString();
        
        // Get today's attendance
        $attendances = StaffAttendance::where('attendance_date', $today)->get()->keyBy('staff_id');
        
        return view('staff.hrms', compact('staff', 'attendances'));
    }

    public function markAttendance(Request $request)
    {
        $staff = Staff::findOrFail($request->staff_id);
        $today = Carbon::today()->toDateString();
        
        $attendance = StaffAttendance::updateOrCreate(
            ['staff_id' => $staff->id, 'attendance_date' => $today],
            [
                'status' => $request->status,
                'check_in_time' => $request->status === 'present' ? now() : null,
                'check_out_time' => $request->status === 'absent' ? null : null,
            ]
        );

        return response()->json(['success' => true, 'attendance' => $attendance]);
    }

    public function assignShift(Request $request)
    {
        $staff = Staff::findOrFail($request->staff_id);
        $staff->update([
            'shift_start' => $request->shift_start,
            'shift_end' => $request->shift_end,
        ]);
        
        return response()->json(['success' => true]);
    }

    public function updateSalary(Request $request)
    {
        $staff = Staff::findOrFail($request->staff_id);
        $salaryVal = $request->base_salary ?? $request->salary;
        $staff->update([
            'salary'                  => $salaryVal,
            'base_salary'             => $salaryVal,
            'commission_per_customer' => $request->commission_per_customer,
            'commission_per_service'  => $request->commission_per_service,
            'shift_start'             => $request->shift_start,
            'shift_end'               => $request->shift_end,
        ]);

        return response()->json(['success' => true]);
    }
}
