<?php

namespace App\Http\Controllers;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\Customer;
use Illuminate\Support\Facades\Storage;

class CustomerController extends Controller
{
    public function index(Request $request)
    {
        $query = Customer::query();

        if ($request->filled('search')) {
            $search = $request->search;
            $query->where(function($q) use ($search) {
                $q->where('name', 'like', "%{$search}%")
                  ->orWhere('phone', 'like', "%{$search}%");
            });
        }

        $customers = $query->latest()->paginate(12);

        return view('customers.index', compact('customers'));
    }

    public function create()
    {
        return view('customers.create');
    }

    public function show(Customer $customer)
    {
        $appointments = $customer->appointments()->with(['service', 'staff'])->latest()->get();
        $invoices = $customer->invoices()->with(['items', 'staff'])->latest()->get();
        $lastVisit = $invoices->first()?->created_at;
        
        return view('customers.show', compact('customer', 'appointments', 'invoices', 'lastVisit'));
    }

    public function store(Request $request)
    {
        $request->validate([
            'name' => 'required|string|max:255',
            'phone' => 'required|string|max:20',
            'image' => 'nullable|image|max:2048',
            'social_media' => 'nullable|array',
        ]);

        $data = $request->only(['name', 'phone', 'social_media', 'email', 'notes']);

        if ($request->hasFile('image')) {
            $data['image_path'] = $request->file('image')->store('customers', 'public');
        }

        Customer::create($data);

        return redirect()->route('customers.index')->with('success', 'Customer added successfully.');
    }

    public function edit(Customer $customer)
    {
        return view('customers.edit', compact('customer'));
    }

    public function update(Request $request, Customer $customer)
    {
        $request->validate([
            'name' => 'required|string|max:255',
            'phone' => 'required|string|max:20',
            'image' => 'nullable|image|max:2048',
            'social_media' => 'nullable|array',
        ]);

        $data = $request->only(['name', 'phone', 'social_media', 'email', 'notes']);

        if ($request->hasFile('image')) {
            // Delete old image if exists
            if ($customer->image_path) {
                Storage::disk('public')->delete($customer->image_path);
            }
            $data['image_path'] = $request->file('image')->store('customers', 'public');
        }

        $customer->update($data);

        return redirect()->route('customers.index')->with('success', 'Customer updated successfully.');
    }

    public function destroy(Customer $customer)
    {
        if ($customer->image_path) {
            Storage::disk('public')->delete($customer->image_path);
        }
        $customer->delete();

        return response()->json([
            'success' => true,
            'message' => 'Customer deleted successfully.'
        ]);
    }

    public function search(Request $request)
    {
        $term = trim(strtolower($request->query('term', '')));
        
        if (empty($term)) {
            return response()->json([]);
        }

        // ⚠️ name and phone are ENCRYPTED in the DB.
        // We must load customers and filter after decryption.
        $cleanTerm = preg_replace('/[^0-9]/', '', $term);

        $customers = Customer::all()->filter(function ($cust) use ($term, $cleanTerm) {
            $decryptedName  = strtolower((string)($cust->name ?? ''));
            $decryptedPhone = strtolower((string)($cust->phone ?? ''));
            $cleanCustPhone = preg_replace('/[^0-9]/', '', $decryptedPhone);

            if ($cleanTerm && $cleanCustPhone && str_contains($cleanCustPhone, $cleanTerm)) {
                return true;
            }

            if ($term && str_contains($decryptedName, $term)) {
                return true;
            }

            return false;
        })->take(10)->map(function ($cust) {
            return [
                'name' => $cust->name,
                'phone' => $cust->phone
            ];
        })->values();

        return response()->json($customers);
    }
}
