<?php

namespace App\Http\Controllers;

use App\Models\Staff;
use App\Models\StaffAttendance;
use App\Models\StaffShift;
use App\Models\LeaveRequest;
use App\Models\UpsellPerformance;
use App\Models\Service;
use App\Models\StaffRole;
use App\Models\Branch;
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
        $currentBranch = Branch::find(session('current_branch_id', auth()->user()->branch_id ?? 1));

        return view('staff.create', compact('services', 'roles', 'currentBranch'));
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'date_of_birth' => 'required|date',
            'phone' => 'required|string',
            'emergency_number' => 'required|string',
            'cnic' => 'required|string',
            'address' => 'required|string',
            'salary' => 'required|numeric|min:0',
            'email' => 'nullable|email|unique:staff,email|unique:users,email',
            'password' => 'nullable|string|min:8|confirmed',
            'staff_role_id' => 'nullable|exists:staff_roles,id',
            'status' => 'nullable|boolean',
        ]);

        $validated['hourly_rate'] = 0;
        $validated['base_salary'] = $validated['salary'] ?? 0;
        $validated['commission_per_customer'] = 0;
        $validated['commission_per_service'] = $request->input('commission_per_service', 10);
        $validated['total_earned_commission'] = 0;
        $validated['rating_total'] = 0;
        $validated['rating_count'] = 0;
        $validated['hiring_date'] = now();
        $validated['position'] = 'Employee';
        $validated['shift_start'] = '00:00:00';
        $validated['shift_end'] = '23:59:59';

        $staff = Staff::create($validated);
        
        // Assign all services to the employee by default
        $services = \App\Models\Service::pluck('id')->toArray();
        $staff->services()->sync($services);
        
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
            'date_of_birth' => 'required|date',
            'phone' => 'required|string',
            'emergency_number' => 'required|string',
            'cnic' => 'required|string',
            'address' => 'required|string',
            'salary' => 'required|numeric|min:0',
            'email' => 'nullable|email|unique:staff,email,' . $staff->id,
            'password' => 'nullable|string|min:8|confirmed',
            'staff_role_id' => 'nullable|exists:staff_roles,id',
            'status' => 'nullable|boolean',
            'commission_per_service' => 'nullable|numeric|min:0|max:100',
        ]);

        $validated['status'] = $request->has('status');
        $staff->update($validated);
        
        // Ensure all services are assigned to the employee
        $services = \App\Models\Service::pluck('id')->toArray();
        $staff->services()->sync($services);

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
            ->paginate(15, ['*'], 'invoices_page');

        $staffExpenses = \App\Models\Expense::where('staff_id', $staff->id)
            ->latest()
            ->paginate(10, ['*'], 'expenses_page');

        return view('staff.salary-performance', compact('staff', 'invoices', 'staffExpenses'));
    }

    public function paySalary(Staff $staff)
    {
        $base = (float) ($staff->base_salary ?? 0);
        $comm = (float) ($staff->total_earned_commission ?? 0);
        $adv  = $staff->current_cycle_advances;
        $ded  = $staff->current_cycle_deductions;
        $netPaid = $staff->net_salary_payable;

        // Record the expense in the expenses history
        \App\Models\Expense::create([
            'branch_id' => auth()->user()->branch_id ?? 1,
            'expense_type' => 'staff',
            'category' => 'full_salary',
            'staff_id' => $staff->id,
            'description' => "Full Salary Paid to " . $staff->name . " (Base: PKR " . number_format($base, 2) . 
                             ", Commission: PKR " . number_format($comm, 2) . 
                             ", Advances: PKR " . number_format($adv, 2) . 
                             ", Deductions: PKR " . number_format($ded, 2) . 
                             ", Net Paid: PKR " . number_format($netPaid, 2) . ")",
            'amount' => $netPaid,
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
