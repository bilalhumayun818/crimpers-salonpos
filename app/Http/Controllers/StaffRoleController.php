<?php

namespace App\Http\Controllers;

use App\Models\StaffRole;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;

class StaffRoleController extends Controller
{
    public function index()
    {
        $roles = StaffRole::withCount('staff')->latest()->paginate(15);
        return view('staff_roles.index', compact('roles'));
    }

    public function create()
    {
        return view('staff_roles.create');
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'name' => 'required|string|unique:staff_roles|max:255',
            'description' => 'nullable|string',
            'email' => 'nullable|email|unique:staff_roles|unique:users',
            'password' => 'nullable|string|min:8',
        ]);

        if ($request->filled('password')) {
            $validated['password'] = Hash::make($request->password);
        }

        $role = StaffRole::create($validated);

        if ($request->filled('email') && $request->filled('password')) {
            User::create([
                'name' => $role->name . ' Account',
                'email' => $role->email,
                'password' => $role->password, // Already hashed
                'role' => 'staff',
                'staff_role_id' => $role->id,
            ]);
        }

        return redirect()->route('staff-roles.index')->with('success', 'Role created successfully.');
    }

    public function edit($id)
    {
        $staffRole = StaffRole::withoutGlobalScopes()->findOrFail($id);
        return view('staff_roles.edit', ['staffRole' => $staffRole]);
    }

    public function update(Request $request, $id)
    {
        $staffRole = StaffRole::withoutGlobalScopes()->findOrFail($id);
        
        $userToIgnore = User::where('staff_role_id', $staffRole->id)->first();
        $ignoreUserId = $userToIgnore ? ',' . $userToIgnore->id : '';

        $validated = $request->validate([
            'name' => 'required|string|max:255|unique:staff_roles,name,' . $staffRole->id,
            'description' => 'nullable|string',
            'email' => 'nullable|email|unique:staff_roles,email,' . $staffRole->id . '|unique:users,email' . $ignoreUserId,
            'password' => 'nullable|string|min:8',
        ]);

        $oldEmail = $staffRole->email;

        if ($request->filled('password')) {
            $validated['password'] = Hash::make($request->password);
        } else {
            unset($validated['password']);
        }

        $staffRole->update($validated);

        // Sync with User
        if ($staffRole->email) {
            $user = User::where('email', $oldEmail)->first();
            if ($user && $user->staff_role_id == $staffRole->id) {
                $user->update([
                    'name' => $staffRole->name . ' Account',
                    'email' => $staffRole->email,
                ]);
                if ($request->filled('password')) {
                    $user->update(['password' => $staffRole->password]);
                }
            } elseif (!$user) {
                // Create if not exists but we have email/pass now
                if ($request->filled('password')) {
                    User::create([
                        'name' => $staffRole->name . ' Account',
                        'email' => $staffRole->email,
                        'password' => $staffRole->password,
                        'role' => 'staff',
                        'staff_role_id' => $staffRole->id,
                    ]);
                }
            }
        }

        return redirect()->route('staff-roles.index')->with('success', 'Role information updated.');
    }

    public function permissions(Request $request)
    {
        $roles = StaffRole::all();
        $selectedRole = null;
        if ($request->filled('role_id')) {
            $selectedRole = StaffRole::find($request->role_id);
        }
        return view('staff_roles.permissions', compact('roles', 'selectedRole'));
    }

    public function savePermissions(Request $request, $id)
    {
        $staffRole = StaffRole::withoutGlobalScopes()->findOrFail($id);
        
        // 1. Update Permissions & Branch Assignment
        $branchId = $request->role_branch_id === 'all' ? null : $request->role_branch_id;

        $staffRole->update([
            'permissions' => $request->input('permissions', []),
            'branch_id' => $branchId
        ]);

        // 2. Sync branch_id with ALL Users assigned to this role
        // This ensures staff members instantly move to the new branch assignment.
        User::where('staff_role_id', $staffRole->id)->update(['branch_id' => $branchId]);

        // 3. Update Role-Level Account (Master)
        if ($request->filled('role_email')) {
            $oldRoleEmail = $staffRole->email;
            $staffRole->email = $request->role_email;
            if ($request->filled('role_password')) {
                $staffRole->password = Hash::make($request->role_password);
            }
            $staffRole->save();

            // Sync with Role-Level User record
            $roleUser = User::where('staff_role_id', $staffRole->id)->where('email', $oldRoleEmail)->first();
            if ($roleUser) {
                $roleUser->email = $request->role_email;
                if ($request->filled('role_password')) {
                    $roleUser->password = $staffRole->password;
                }
                $roleUser->save();
            }
        }

        // Process Individual Staff User Logins
        $emails = $request->input('user_emails', []);
        $passwords = $request->input('user_passwords', []);

        foreach ($emails as $userId => $email) {
            $user = \App\Models\User::find($userId);
            if ($user && $user->staff_role_id == $staffRole->id) {
                $oldEmail = $user->email;
                $user->email = $email;

                if (!empty($passwords[$userId])) {
                    $user->password = \Illuminate\Support\Facades\Hash::make($passwords[$userId]);
                }

                $user->save();

                // Also update the Staff record if it exists and email matches
                \App\Models\Staff::where('email', $oldEmail)->update(['email' => $email]);
            }
        }

        return redirect()->back()->with('success', 'Business settings and user logins updated for ' . $staffRole->name);
    }

    public function destroy($id)
    {
        $staffRole = StaffRole::withoutGlobalScopes()->findOrFail($id);

        if ($staffRole->staff()->count() > 0) {
            return redirect()->back()->with('error', 'Cannot delete role which is assigned to active employees. Reassign them first.');
        }

        // Dissociate or delete User logins assigned to this role
        $users = User::where('staff_role_id', $staffRole->id)->get();
        foreach ($users as $user) {
            try {
                $user->delete();
            } catch (\Exception $e) {
                // If user has historical records (like cash reconciliations), dissociate instead of hard deleting
                $user->update([
                    'staff_role_id' => null,
                    'role' => 'inactive'
                ]);
            }
        }

        $staffRole->delete();
        return redirect()->route('staff-roles.index')->with('success', 'Role and its associated login successfully deleted.');
    }
}
