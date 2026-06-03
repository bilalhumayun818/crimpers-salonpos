<?php

namespace App\Http\Controllers;

use App\Models\Staff;
use App\Models\StaffAttendance;
use App\Models\StaffShift;
use App\Models\LeaveRequest;
use App\Models\UpsellPerformance;
use App\Models\Service;
use App\Models\StaffRole;
use App\Models\User;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Illuminate\Http\Request;

class StaffController extends Controller
{
    public function index()
    {
        $staff = Staff::with(['role', 'services'])->latest()->paginate(12);
        return view('staff.index', compact('staff'));
    }

    public function create()
    {
        $services = Service::all();
        $roles = StaffRole::all();
        return view('staff.create', compact('services', 'roles'));
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'email' => 'nullable|email|unique:staff,email|unique:users,email',
            'password' => 'nullable|string|min:8|confirmed',
            'phone' => 'nullable|string',
            'staff_role_id' => 'nullable|exists:staff_roles,id',
            'hourly_rate' => 'nullable|numeric|min:0',
            'base_salary' => 'nullable|numeric|min:0',
            'commission_per_customer' => 'nullable|numeric|min:0',
            'commission_per_service' => 'nullable|numeric|min:0',
            'hiring_date' => 'nullable|date',
            'shift_start' => 'required',
            'shift_end' => 'required',
            'status' => 'nullable|boolean',
            'bio' => 'nullable|string',
            'service_ids' => 'nullable|array',
        ]);

        $validated['hourly_rate'] = $validated['hourly_rate'] ?? 0;
        $validated['base_salary'] = $validated['base_salary'] ?? 0;
        $validated['commission_per_customer'] = $validated['commission_per_customer'] ?? 0;
        $validated['commission_per_service'] = $validated['commission_per_service'] ?? 0;
        $validated['total_earned_commission'] = 0;
        $validated['rating_total'] = 0;
        $validated['rating_count'] = 0;
        $validated['hiring_date'] = $validated['hiring_date'] ?? now();
        $validated['position'] = 'Employee';

        $staff = Staff::create($validated);
        $staff->services()->sync($request->input('service_ids', []));
        UpsellPerformance::create(['staff_id' => $staff->id]);

        // Create a User login account ONLY if email and password are provided
        if (!empty($validated['email']) && !empty($validated['password'])) {
            User::create([
                'name' => $validated['name'],
                'email' => $validated['email'],
                'password' => Hash::make($validated['password']),
                'role' => 'staff',
                'staff_role_id' => $validated['staff_role_id'] ?? null,
            ]);
            $msg = 'Staff member added and login account created.';
        } else {
            $msg = 'Staff member added (No login account created).';
        }

        return redirect()->route('staff.index')->with('success', $msg);
    }

    public function show(Staff $staff)
    {
        $attendances = $staff->attendances()->latest()->paginate(10);
        $shifts = $staff->shifts()->latest()->paginate(10);
        $leaveRequests = $staff->leaveRequests()->latest()->paginate(10);
        $upsellPerformance = $staff->upsellPerformance;

        $monthlyHours = $staff->attendances()
            ->whereYear('attendance_date', now()->year)
            ->whereMonth('attendance_date', now()->month)
            ->sum('check_out_time') - $staff->attendances()
                ->whereYear('attendance_date', now()->year)
                ->whereMonth('attendance_date', now()->month)
                ->sum('check_in_time');

        return view('staff.show', compact('staff', 'attendances', 'shifts', 'leaveRequests', 'upsellPerformance', 'monthlyHours'));
    }

    public function edit(Staff $staff)
    {
        $services = Service::all();
        $roles = StaffRole::all();
        return view('staff.edit', compact('staff', 'services', 'roles'));
    }

    public function update(Request $request, Staff $staff)
    {
        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'email' => 'nullable|email|unique:staff,email,' . $staff->id,
            'password' => 'nullable|string|min:8|confirmed',
            'phone' => 'nullable|string',
            'staff_role_id' => 'nullable|exists:staff_roles,id',
            'hourly_rate' => 'nullable|numeric|min:0',
            'base_salary' => 'nullable|numeric|min:0',
            'commission_per_customer' => 'nullable|numeric|min:0',
            'commission_per_service' => 'nullable|numeric|min:0',
            'hiring_date' => 'nullable|date',
            'shift_start' => 'required',
            'shift_end' => 'required',
            'status' => 'nullable|boolean',
            'bio' => 'nullable|string',
            'service_ids' => 'nullable|array',
        ]);

        $validated['hourly_rate'] = $validated['hourly_rate'] ?? 0;
        $validated['base_salary'] = $validated['base_salary'] ?? 0;
        $validated['commission_per_customer'] = $validated['commission_per_customer'] ?? 0;
        $validated['commission_per_service'] = $validated['commission_per_service'] ?? 0;
        $validated['status'] = $request->has('status');

        $staff->update($validated);
        $staff->services()->sync($request->input('service_ids', []));

        // Sync the linked User login account (email + role + optional password)
        $user = User::where('email', $staff->getOriginal('email'))->first();
        if ($user) {
            $userUpdate = [
                'name' => $validated['name'],
                'email' => $validated['email'],
                'staff_role_id' => $validated['staff_role_id'] ?? null,
            ];
            if (!empty($validated['password'])) {
                $userUpdate['password'] = Hash::make($validated['password']);
            }
            $user->update($userUpdate);
        }

        return redirect()->route('staff.index')->with('success', 'Staff member updated.');
    }

    public function destroy(Staff $staff)
    {
        $staff->attendances()->delete();
        $staff->shifts()->delete();
        $staff->leaveRequests()->delete();
        if ($staff->upsellPerformance) {
            $staff->upsellPerformance->delete();
        }
        $staff->services()->detach();

        $staff->delete();
        return redirect()->route('staff.index')->with('success', 'Staff member and all their records successfully deleted.');
    }

    public function attendance(Request $request, Staff $staff = null)
    {
        if ($staff) {
            $records = $staff->attendances()->latest('attendance_date')->paginate(15);
            return view('staff.attendance', compact('staff', 'records'));
        }

        $query = StaffAttendance::with('staff');

        if ($request->filled('date_from')) {
            $query->whereDate('attendance_date', '>=', $request->date_from);
        }
        if ($request->filled('date_to')) {
            $query->whereDate('attendance_date', '<=', $request->date_to);
        }

        $records = $query->orderBy('attendance_date', 'desc')
            ->orderBy('staff_id')
            ->get();

        return view('staff.attendance-all', compact('records'));
    }

    public function recordAttendance(Request $request)
    {
        $validated = $request->validate([
            'staff_id' => 'required|exists:staff,id',
            'attendance_date' => 'nullable|date',
            'status' => 'required|in:present,absent,late,half_day,leave',
        ]);

        // Default to today if no date provided
        $date = $validated['attendance_date'] ?? now()->format('Y-m-d');

        StaffAttendance::updateOrCreate(
            ['staff_id' => $validated['staff_id'], 'attendance_date' => $date],
            ['staff_id' => $validated['staff_id'], 'attendance_date' => $date, 'status' => $validated['status']]
        );

        return redirect()->back()->with('success', 'Attendance recorded for ' . now()->parse($date)->format('M d, Y') . '.');
    }

    public function storeShiftInline(Request $request, Staff $staff)
    {
        $validated = $request->validate([
            'shift_date' => 'required|date',
            'shift_type' => 'required|in:morning,afternoon,evening,full_day',
            'start_time' => 'required|date_format:H:i',
            'end_time' => 'required|date_format:H:i',
            'break_duration' => 'nullable|numeric|min:0',
            'notes' => 'nullable|string|max:255',
        ]);

        $staff->shifts()->create($validated);

        return redirect()->route('staff.show', $staff)->with('success', 'Shift scheduled successfully.');
    }

    public function rate(Request $request, Staff $staff)
    {
        $request->validate(['rating' => 'required|numeric|min:1|max:5']);
        $staff->update(['rating' => $request->rating]);
        return redirect()->back()->with('success', 'Rating updated!');
    }

    public function shifts(Staff $staff)
    {
        $shifts = $staff->shifts()->paginate(15);
        return view('staff.shifts', compact('staff', 'shifts'));
    }

    public function createShift(Staff $staff)
    {
        return view('staff.create-shift', compact('staff'));
    }

    public function storeShift(Request $request, Staff $staff)
    {
        $validated = $request->validate([
            'shift_date' => 'required|date',
            'start_time' => 'required|date_format:H:i',
            'end_time' => 'required|date_format:H:i',
            'shift_type' => 'required|in:morning,afternoon,evening,full_day',
            'break_duration' => 'nullable|numeric|min:0',
            'notes' => 'nullable|string',
        ]);

        $staff->shifts()->create($validated);

        return redirect()->route('staff.shifts', $staff)->with('success', 'Shift scheduled.');
    }

    public function leaveRequests(Staff $staff = null)
    {
        if ($staff) {
            $requests = $staff->leaveRequests()->paginate(12);
            return view('staff.leave-requests', compact('staff', 'requests'));
        }
        $requests = LeaveRequest::with('staff')->where('status', 'pending')->paginate(15);
        return view('staff.leave-requests-all', compact('requests'));
    }

    public function requestLeave(Staff $staff)
    {
        return view('staff.request-leave', compact('staff'));
    }

    public function storeLeaveRequest(Request $request, Staff $staff)
    {
        $validated = $request->validate([
            'leave_type' => 'required|in:sick,vacation,personal,unpaid',
            'start_date' => 'required|date',
            'end_date' => 'required|date|after_or_equal:start_date',
            'reason' => 'nullable|string',
        ]);

        $staff->leaveRequests()->create([
            ...$validated,
            'status' => 'pending',
        ]);

        return redirect()->back()->with('success', 'Leave request submitted.');
    }

    public function approveLeave(LeaveRequest $leave)
    {
        $leave->update([
            'status' => 'approved',
            'approved_by' => Auth::id() ?? 1,
            'approved_at' => now(),
        ]);

        return redirect()->back()->with('success', 'Leave request approved.');
    }

    public function rejectLeave(LeaveRequest $leave)
    {
        $leave->update(['status' => 'rejected']);
        return redirect()->back()->with('success', 'Leave request rejected.');
    }

    public function salaryDashboard()
    {
        $staff = Staff::all();
        return view('staff.salary-dashboard', compact('staff'));
    }

    public function salaryPerformance(Staff $staff)
    {
        $invoices = \App\Models\Invoice::where('staff_id', $staff->id)
            ->with(['customer', 'items.itemizable'])
            ->latest()
            ->paginate(15);

        return view('staff.salary-performance', compact('staff', 'invoices'));
    }

    public function paySalary(Staff $staff)
    {
        $totalPaid = $staff->base_salary + $staff->total_earned_commission;

        // Record the expense in the expenses history
        \App\Models\Expense::create([
            'branch_id' => auth()->user()->branch_id ?? 1,
            'description' => "Salary Paid to " . $staff->name . " (Base: PKR " . number_format($staff->base_salary, 2) . ", Commission: PKR " . number_format($staff->total_earned_commission, 2) . ")",
            'amount' => $totalPaid,
            'deducted_from_drawer' => false,
            'user_id' => auth()->id() ?? 1,
        ]);

        $staff->update([
            'total_earned_commission' => 0,
            'last_paid_at' => now(),
        ]);

        return redirect()->back()->with('success', 'Salary successfully marked as Paid. Commissions and work days have been reset for the new cycle!');
    }
}
