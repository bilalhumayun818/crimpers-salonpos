<?php

namespace App\Http\Controllers;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\Appointment;
use App\Models\Staff;
use App\Models\Service;
use Carbon\Carbon;

class AppointmentController extends Controller
{
    public function index()
    {
        // Hide appointments older than 1 day (24 hours) from active views
        $appointments = Appointment::with(['staff', 'service', 'servicePackage'])
            ->where('appointment_date', '>=', Carbon::today()->subDay())
            ->latest()
            ->paginate(20);
        return view('appointments.index', compact('appointments'));
    }

    public function calendar()
    {
        $staffMembers = Staff::available()->get()->filter(function($s) { return $s->is_on_shift; });
        $services = Service::all();
        
        $currentBranch = \App\Models\Branch::find(session('current_branch_id')) ?? \App\Models\Branch::first();
        $openingTime = $currentBranch ? \Carbon\Carbon::parse($currentBranch->opening_time)->format('H:i:s') : '09:00:00';
        $closingTime = $currentBranch ? \Carbon\Carbon::parse($currentBranch->closing_time)->format('H:i:s') : '21:00:00';
        $calendarMaxTime = $currentBranch ? \Carbon\Carbon::parse($currentBranch->closing_time)->addHour()->format('H:i:s') : '22:00:00';

        return view('appointments.calendar', compact('staffMembers', 'services', 'openingTime', 'closingTime', 'calendarMaxTime'));
    }

    public function create()
    {
        $staffMembers = Staff::available()->get()->filter(function($s) { return $s->is_on_shift; });
        $services = Service::all();
        $packages = \App\Models\ServicePackage::all();

        $currentBranch = \App\Models\Branch::find(session('current_branch_id')) ?? \App\Models\Branch::first();
        $openingTime = $currentBranch ? \Carbon\Carbon::parse($currentBranch->opening_time)->format('H:i') : '09:00';
        $closingTime = $currentBranch ? \Carbon\Carbon::parse($currentBranch->closing_time)->format('H:i') : '21:00';

        return view('appointments.create', compact('staffMembers', 'services', 'packages', 'openingTime', 'closingTime'));
    }

    public function store(Request $request)
    {
        $currentBranch = \App\Models\Branch::find(session('current_branch_id')) ?? \App\Models\Branch::first();
        $openingTime = $currentBranch ? \Carbon\Carbon::parse($currentBranch->opening_time)->format('H:i') : '09:00';
        $closingTime = $currentBranch ? \Carbon\Carbon::parse($currentBranch->closing_time)->format('H:i') : '21:00';

        $request->validate([
            'staff_id' => 'nullable|exists:staff,id',
            'service_id' => 'required_without:service_package_id|nullable|exists:services,id',
            'service_package_id' => 'required_without:service_id|nullable|exists:service_packages,id',
            'appointment_date' => 'required|date',
            'start_time' => 'required|date_format:H:i|after_or_equal:' . $openingTime . '|before:' . $closingTime,
            'end_time' => 'required|date_format:H:i|after:start_time|before_or_equal:' . $closingTime,
            'customer_name' => 'required|string',
            'customer_phone' => 'nullable|string',
            'notes' => 'nullable|string',
        ]);

        // Check for conflicts only if staff is assigned
        $conflict = false;
        if ($request->staff_id) {
            $conflict = Appointment::where('staff_id', $request->staff_id)
                ->where('appointment_date', $request->appointment_date)
                ->where(function ($query) use ($request) {
                    $query->whereBetween('start_time', [$request->start_time, $request->end_time])
                          ->orWhereBetween('end_time', [$request->start_time, $request->end_time])
                          ->orWhere(function ($q) use ($request) {
                              $q->where('start_time', '<=', $request->start_time)
                                ->where('end_time', '>=', $request->end_time);
                          });
                })
                ->exists();
        }

        if ($conflict) {
            return back()->withErrors(['conflict' => 'This time slot is already booked.']);
        }

        // ⚠️ name and phone are ENCRYPTED in the DB.
        // We must load customers and filter after decryption.
        $inputName  = strtolower(trim($request->customer_name));
        $inputPhone = preg_replace('/[^0-9]/', '', $request->customer_phone ?? '');

        $customer = \App\Models\Customer::all()->first(function ($cust) use ($inputName, $inputPhone) {
            $custName  = strtolower(trim((string)($cust->name ?? '')));
            $custPhone = preg_replace('/[^0-9]/', '', (string)($cust->phone ?? ''));
            return $custName === $inputName && $custPhone === $inputPhone;
        });

        if (!$customer) {
            $customer = \App\Models\Customer::create([
                'name' => $request->customer_name,
                'phone' => $request->customer_phone ?? 'N/A'
            ]);
        }

        Appointment::create([
            'customer_name'    => $request->customer_name,
            'customer_phone'   => $request->customer_phone,
            'customer_id'      => $customer->id,
            'service_id'       => $request->service_id ?: null,
            'service_package_id' => $request->service_package_id ?: null,
            'staff_id'         => $request->staff_id ?: null,
            'appointment_date' => $request->appointment_date,
            'start_time'       => $request->start_time,
            'end_time'         => $request->end_time,
            'notes'            => $request->notes,
            'branch_id'        => session('current_branch_id', 1),
            'status'           => 'scheduled',
        ]);

        return redirect()->route('appointments.index')->with('success', 'Appointment created successfully');
    }

    public function edit(Appointment $appointment)
    {
        $staffMembers = Staff::available()->get()->filter(function($s) { return $s->is_on_shift; });
        $services = Service::all();
        $packages = \App\Models\ServicePackage::all();

        $currentBranch = \App\Models\Branch::find(session('current_branch_id')) ?? \App\Models\Branch::first();
        $openingTime = $currentBranch ? \Carbon\Carbon::parse($currentBranch->opening_time)->format('H:i') : '09:00';
        $closingTime = $currentBranch ? \Carbon\Carbon::parse($currentBranch->closing_time)->format('H:i') : '21:00';

        return view('appointments.edit', compact('appointment', 'staffMembers', 'services', 'packages', 'openingTime', 'closingTime'));
    }

    public function update(Request $request, Appointment $appointment)
    {
        $currentBranch = \App\Models\Branch::find(session('current_branch_id')) ?? \App\Models\Branch::first();
        $openingTime = $currentBranch ? \Carbon\Carbon::parse($currentBranch->opening_time)->format('H:i') : '09:00';
        $closingTime = $currentBranch ? \Carbon\Carbon::parse($currentBranch->closing_time)->format('H:i') : '21:00';

        $request->validate([
            'staff_id' => 'nullable|exists:staff,id',
            'service_id' => 'required_without:service_package_id|nullable|exists:services,id',
            'service_package_id' => 'required_without:service_id|nullable|exists:service_packages,id',
            'appointment_date' => 'required|date',
            'start_time' => 'required|date_format:H:i|after_or_equal:' . $openingTime . '|before:' . $closingTime,
            'end_time' => 'required|date_format:H:i|after:start_time|before_or_equal:' . $closingTime,
            'customer_name' => 'required|string',
            'customer_phone' => 'nullable|string',
            'notes' => 'nullable|string',
        ]);

        // Check for conflicts excluding current appointment only if staff assigned
        $conflict = false;
        if ($request->staff_id) {
            $conflict = Appointment::where('staff_id', $request->staff_id)
                ->where('appointment_date', $request->appointment_date)
                ->where('id', '!=', $appointment->id)
                ->where(function ($query) use ($request) {
                    $query->whereBetween('start_time', [$request->start_time, $request->end_time])
                          ->orWhereBetween('end_time', [$request->start_time, $request->end_time])
                          ->orWhere(function ($q) use ($request) {
                              $q->where('start_time', '<=', $request->start_time)
                                ->where('end_time', '>=', $request->end_time);
                          });
                })
                ->exists();
        }

        if ($conflict) {
            return back()->withErrors(['conflict' => 'This time slot is already booked.']);
        }

        // ⚠️ name and phone are ENCRYPTED in the DB.
        $inputName  = strtolower(trim($request->customer_name));
        $inputPhone = preg_replace('/[^0-9]/', '', $request->customer_phone ?? '');

        $customer = \App\Models\Customer::all()->first(function ($cust) use ($inputName, $inputPhone) {
            $custName  = strtolower(trim((string)($cust->name ?? '')));
            $custPhone = preg_replace('/[^0-9]/', '', (string)($cust->phone ?? ''));
            return $custName === $inputName && $custPhone === $inputPhone;
        });

        if (!$customer) {
            $customer = \App\Models\Customer::create([
                'name' => $request->customer_name,
                'phone' => $request->customer_phone ?? 'N/A'
            ]);
        }

        $appointment->update([
            'customer_name'    => $request->customer_name,
            'customer_phone'   => $request->customer_phone,
            'customer_id'      => $customer->id,
            'service_id'       => $request->service_id ?: null,
            'service_package_id' => $request->service_package_id ?: null,
            'staff_id'         => $request->staff_id ?: null,
            'appointment_date' => $request->appointment_date,
            'start_time'       => $request->start_time,
            'end_time'         => $request->end_time,
            'notes'            => $request->notes,
        ]);

        return redirect()->route('appointments.index')->with('success', 'Appointment updated successfully');
    }

    public function events()
    {
        $appointments = Appointment::with(['staff', 'service', 'servicePackage'])->get();
        $events = [];

        foreach ($appointments as $appointment) {
            $displayName = 'Unassigned';
            if ($appointment->service) {
                $displayName = $appointment->service->name;
            } elseif ($appointment->servicePackage) {
                $displayName = $appointment->servicePackage->name;
            }

            $events[] = [
                'id' => $appointment->id,
                'title' => $appointment->customer_name . ' - ' . $displayName,
                'start' => $appointment->appointment_date->format('Y-m-d') . 'T' . $appointment->start_time->format('H:i:s'),
                'end' => $appointment->appointment_date->format('Y-m-d') . 'T' . $appointment->end_time->format('H:i:s'),
                'backgroundColor' => match($appointment->status) {
                    'arrived'   => '#22c55e', // Green
                    'late'      => '#eab308', // Yellow
                    'discarded' => '#ef4444', // Red
                    'completed' => '#a855f7', // Purple
                    'cancelled' => '#64748b', // Slate
                    default     => '#3b82f6', // Blue (scheduled)
                },
                'borderColor' => 'transparent',
                'extendedProps' => [
                    'staff' => $appointment->staff ? $appointment->staff->name : 'Unassigned',
                    'staff_id' => $appointment->staff_id,
                    'service' => $displayName,
                    'customer' => $appointment->customer_name,
                    'status' => $appointment->status,
                ]
            ];
        }

        return response()->json($events);
    }

    public function updateTime(Request $request, Appointment $appointment)
    {
        $request->validate([
            'start' => 'required|date',
            'end' => 'required|date',
        ]);

        $start = Carbon::parse($request->start);
        $end = Carbon::parse($request->end);

        // Check for conflicts
        $conflict = Appointment::where('staff_id', $appointment->staff_id)
            ->where('appointment_date', $start->format('Y-m-d'))
            ->where('id', '!=', $appointment->id)
            ->where(function ($query) use ($start, $end) {
                $query->whereBetween('start_time', [$start->format('H:i'), $end->format('H:i')])
                      ->orWhereBetween('end_time', [$start->format('H:i'), $end->format('H:i')])
                      ->orWhere(function ($q) use ($start, $end) {
                          $q->where('start_time', '<=', $start->format('H:i'))
                            ->where('end_time', '>=', $end->format('H:i'));
                      });
            })
            ->exists();

        if ($conflict) {
            return response()->json(['error' => 'Time slot conflict'], 422);
        }

        $appointment->update([
            'appointment_date' => $start->format('Y-m-d'),
            'start_time' => $start->format('H:i'),
            'end_time' => $end->format('H:i'),
        ]);

        return response()->json(['success' => true]);
    }

    public function destroy(Appointment $appointment)
    {
        $appointment->delete();
        return redirect()->route('appointments.index')->with('success', 'Appointment deleted successfully');
    }

    public function dueNow()
    {
        $now = now();
        $today = $now->format('Y-m-d');
        
        // Auto-discard appointments that are past their time and still 'scheduled'
        Appointment::where('status', 'scheduled')
            ->where(function($q) use ($today, $now) {
                $q->where('appointment_date', '<', $today)
                  ->orWhere(function($sq) use ($today, $now) {
                      $sq->where('appointment_date', $today)
                        ->where('start_time', '<', $now->copy()->subMinutes(10)->format('H:i:s'));
                  });
            })
            ->update(['status' => 'discarded']);

        // Fetch appointments that have arrived (within the last 10 minutes) and haven't been acted upon
        $appointments = Appointment::with(['staff', 'service'])
            ->where('appointment_date', $today)
            ->where('status', 'scheduled')
            ->where('start_time', '<=', $now->copy()->addMinutes(1)->format('H:i:s')) // 1 min buffer ahead
            ->where('start_time', '>=', $now->copy()->subMinutes(10)->format('H:i:s'))
            ->get()
            ->map(function($a) {
                return [
                    'id'            => $a->id,
                    'customer_name' => $a->customer_name,
                    'service'       => $a->service ? $a->service->name : '—',
                    'staff'         => $a->staff ? $a->staff->name : '—',
                    'start_time'    => $a->start_time ? $a->start_time->format('g:i A') : '—',
                ];
            });

        return response()->json($appointments);
    }

    public function updateStatus(Request $request, Appointment $appointment)
    {
        $request->validate(['status' => 'required|string']);
        $appointment->update(['status' => $request->status]);
        
        if ($request->ajax() || $request->wantsJson()) {
            return response()->json(['success' => true]);
        }
        
        return back()->with('success', 'Status updated.');
    }
}