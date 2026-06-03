<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;

class LoginController extends Controller
{
    public function showLoginForm(Request $request)
    {
        $type = $request->get('type', 'staff'); // 'staff' or 'admin'
        return view('auth.login', compact('type'));
    }

    public function login(Request $request)
    {
        $request->validate([
            'email' => 'required|email',
            'password' => 'required',
        ]);

        $credentials = $request->only('email', 'password');

        if (Auth::attempt($credentials, true)) {
            $request->session()->regenerate();

            $user = auth()->user();

            // ✅ Set the branch session IMMEDIATELY so hasPermission() works correctly
            // for ALL branches (Lahore & Faisalabad) before any permission checks run.
            session(['current_branch_id' => $user->branch_id ?? 1]);

            // 2FA logic removed as requested

            if ($user->role === 'admin' || $user->email === 'safullahzafar@gmail.com') {
                return redirect()->route('admin.branch.select');
            }

            // Everyone else (Staff/Generic Roles) goes to their allowed dashboard
            if ($user->hasPermission('pos', 'access')) {
                return redirect()->route('pos.index')->with('success', 'Logged in successfully.');
            } elseif ($user->hasPermission('dashboard', 'view')) {
                return redirect()->route('admin.index')->with('success', 'Logged in successfully.');
            } elseif ($user->hasPermission('appointments', 'view')) {
                return redirect()->route('appointments.index')->with('success', 'Logged in successfully.');
            } elseif ($user->hasPermission('sales', 'view')) {
                return redirect()->route('invoices.index')->with('success', 'Logged in successfully.');
            } elseif ($user->hasPermission('inventory', 'view')) {
                return redirect()->route('products.index')->with('success', 'Logged in successfully.');
            } elseif ($user->hasPermission('purchases', 'view')) {
                return redirect()->route('purchases.index')->with('success', 'Logged in successfully.');
            } elseif ($user->hasPermission('history', 'access')) {
                return redirect()->route('invoices.index')->with('success', 'Logged in successfully.');
            } elseif ($user->hasPermission('customers', 'view')) {
                return redirect()->route('customers.index')->with('success', 'Logged in successfully.');
            } elseif ($user->hasPermission('suppliers', 'view')) {
                return redirect()->route('suppliers.index')->with('success', 'Logged in successfully.');
            } elseif ($user->hasPermission('expenses', 'view')) {
                return redirect()->route('reconciliation.index')->with('success', 'Logged in successfully.');
            } elseif ($user->hasPermission('reports', 'view')) {
                return redirect()->route('reports.index')->with('success', 'Logged in successfully.');
            } elseif ($user->hasPermission('staff', 'view')) {
                return redirect()->route('staff.index')->with('success', 'Logged in successfully.');
            } elseif ($user->hasPermission('staff', 'attendance')) {
                return redirect()->route('staff.attendance-all')->with('success', 'Logged in successfully.');
            }

            // Absolute fallback gracefully logs them out if they have zero business settings permissions.
            Auth::logout();
            $request->session()->invalidate();
            $request->session()->regenerateToken();
            return redirect('login')->withErrors(['email' => 'Your account has not been assigned any modules yet. Please contact the administrator.']);
        }

        return back()->withErrors([
            'email' => 'The provided credentials do not match our records.',
        ])->onlyInput('email');
    }

    public function logout(Request $request)
    {
        Auth::logout();

        $request->session()->invalidate();
        $request->session()->regenerateToken();

        return redirect('/');
    }
}