<?php

namespace App\Http\Controllers;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\Category;
use App\Models\Service;
use App\Models\Product;
use App\Models\ServicePackage;
use App\Models\Invoice;
use App\Models\InvoiceItem;
use App\Models\ProductUsage;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Str;
use App\Models\Coupon;
use App\Models\Customer;
use App\Models\Staff;

class POSController extends Controller
{
    public function index()
    {
        $categories = Category::all();
        $services = Service::all();
        $products = Product::where('current_stock', '>', 0)->get();
        $packages = ServicePackage::where('is_active', true)->get();
        $popularServices = Service::where('is_popular', true)->get();

        return view('pos.index', compact('categories', 'services', 'products', 'packages', 'popularServices'));
    }

    public function payment()
    {
        $staff = Staff::available()->get()->filter(function ($s) {
            return $s->is_on_shift;
        });
        return view('pos.payment', compact('staff'));
    }

    public function store(Request $request)
    {
        $request->validate([
            'items' => 'required|array',
            'payment_method' => 'required|string',
            'total_amount' => 'required|numeric',
            'payable_amount' => 'required|numeric',
            'customer_id' => 'nullable|exists:customers,id',
            'discount' => 'nullable|numeric',
        ]);

        return DB::transaction(function () use ($request) {
            $customerId = $request->customer_id;

            // Auto-lookup/create customer based on phone (Unique identifier)
            if (!$customerId && $request->customer_phone) {
                // ⚠️ phone is ENCRYPTED — cannot use SQL WHERE/LIKE.
                // Load all customers and compare after decryption.
                $inputPhoneClean = preg_replace('/[^0-9]/', '', $request->customer_phone);

                $customer = Customer::all()->first(function ($cust) use ($inputPhoneClean) {
                    $custPhoneClean = preg_replace('/[^0-9]/', '', (string) ($cust->phone ?? ''));
                    return $custPhoneClean && str_contains($custPhoneClean, $inputPhoneClean);
                });

                if ($customer) {
                    $customerId = $customer->id;
                    // Sync name if it's not a generic walk-in
                    if ($request->customer_name && $request->customer_name !== 'Walk-in Customer') {
                        $customer->update(['name' => $request->customer_name]);
                    }
                } elseif ($request->customer_name && $request->customer_name !== 'Walk-in Customer') {
                    // No customer found with this phone → CREATE NEW
                    $newCustomer = Customer::create([
                        'name' => $request->customer_name,
                        'phone' => $request->customer_phone,
                        'membership_type' => 'Standard'
                    ]);
                    $customerId = $newCustomer->id;
                }
            }

            $invoice = Invoice::create([
                'invoice_no' => 'INV-' . strtoupper(Str::random(8)),
                'user_id' => Auth::id() ?? 1, // Fallback for dev
                'customer_id' => $customerId,
                'customer_name' => $request->customer_name ?? 'Walk-in Customer',
                'total_amount' => $request->total_amount,
                'tax' => $request->tax ?? 0,
                'discount' => $request->discount ?? 0,
                'payable_amount' => $request->payable_amount,
                'payment_method' => $request->payment_method,
                'cash_received' => $request->cash_received,
                'change_returned' => $request->change_returned,
                'status' => 'paid',
                'staff_id' => $request->staff_id,
            ]);

            foreach ($request->items as $item) {
                InvoiceItem::create([
                    'invoice_id' => $invoice->id,
                    'itemizable_id' => $item['id'],
                    'itemizable_type' => $item['type'] === 'service' ? Service::class : ($item['type'] === 'package' ? ServicePackage::class : Product::class),
                    'custom_name' => $item['name'],
                    'quantity' => $item['quantity'],
                    'price' => $item['price'],
                    'subtotal' => $item['subtotal'],
                ]);

                // Update Staff HRMS Data if staff selected
                if ($request->staff_id) {
                    $performer = Staff::find($request->staff_id);
                    if ($performer) {
                        // Add commission if it's a service or package
                        if ($item['type'] === 'service' || $item['type'] === 'package') {
                            // Commission is now a percentage of the subtotal
                            $commissionAmount = ($item['subtotal'] * ($performer->commission_per_service / 100));
                            $performer->increment('total_earned_commission', $commissionAmount);
                        }
                    }
                }

                if ($item['type'] === 'product') {
                    $product = Product::find($item['id']);
                    $product->decrement('current_stock', $item['quantity']);

                    // Track product usage
                    ProductUsage::create([
                        'product_id' => $item['id'],
                        'invoice_id' => $invoice->id,
                        'quantity_used' => $item['quantity'],
                        'usage_date' => now(),
                        'notes' => 'Sold via POS'
                    ]);
                } elseif ($item['type'] === 'service') {
                    // Auto-deduct service supplies if configured
                    $this->deductServiceSupplies($item['id'], $item['quantity'], $invoice->id);
                } elseif ($item['type'] === 'package') {
                    // Handle package services and their supplies
                    $package = ServicePackage::with('services')->find($item['id']);
                    if ($package) {
                        foreach ($package->services as $service) {
                            $this->deductServiceSupplies($service->id, $item['quantity'], $invoice->id);
                        }
                    }
                }
            }

            // Update Staff Rating and Per-Customer Commission once per invoice
            if ($request->staff_id) {
                $performer = Staff::find($request->staff_id);
                if ($performer) {
                    if ($request->rating) {
                        $performer->increment('rating_total', (int) $request->rating);
                        $performer->increment('rating_count');
                    }
                    
                    // Add Per-Customer Commission (Percentage of total payable amount)
                    if ($performer->commission_per_customer > 0) {
                        $customerCommission = ($request->payable_amount * ($performer->commission_per_customer / 100));
                        $performer->increment('total_earned_commission', $customerCommission);
                    }
                }
            }

            return response()->json([
                'success' => true,
                'message' => 'Invoice generated successfully',
                'invoice' => $invoice->load('items.itemizable'),
            ]);
        });
    }

    private function deductServiceSupplies($serviceId, $quantity, $invoiceId)
    {
        // Get service supplies that need to be auto-deducted
        $serviceSupplies = \App\Models\ServiceSupply::where('service_id', $serviceId)
            ->where('is_active', true)
            ->with('product')
            ->get();

        foreach ($serviceSupplies as $supply) {
            $product = $supply->product;
            $totalQuantity = $supply->quantity_per_service * $quantity;

            // Check if product tracks inventory and has sufficient stock
            if ($product && $product->track_inventory && $product->current_stock >= $totalQuantity) {
                // Deduct from inventory
                $product->decrement('current_stock', $totalQuantity);

                // Track product usage
                ProductUsage::create([
                    'product_id' => $product->id,
                    'service_id' => $serviceId,
                    'invoice_id' => $invoiceId,
                    'quantity_used' => $totalQuantity,
                    'usage_date' => now(),
                    'notes' => 'Auto-deducted for service performance'
                ]);
            } else {
                // Log insufficient stock or skip deduction
                // You might want to add logging here or notify staff
                Log::warning("Insufficient stock for auto-deduction: Product {$product->name} needs {$totalQuantity} units, has {$product->current_stock}");
            }
        }
    }

    public function checkCoupon(Request $request)
    {
        $code = $request->input('code');
        $totalAmount = $request->input('total_amount', 0);

        $coupon = Coupon::where('code', $code)->first();

        if (!$coupon) {
            return response()->json(['success' => false, 'message' => 'Coupon not found']);
        }

        if (!$coupon->isValid(null, $totalAmount)) {
            return response()->json(['success' => false, 'message' => 'Coupon is not valid or requirements not met']);
        }

        return response()->json([
            'success' => true,
            'coupon' => [
                'code' => $coupon->code,
                'type' => $coupon->type, // fixed or percentage
                'value' => $coupon->value,
                'name' => $coupon->name
            ]
        ]);
    }

    public function searchCustomer(Request $request)
    {
        $q = trim($request->input('q', ''));
        $name = trim($request->input('name', ''));
        $phone = trim($request->input('phone', ''));

        if (empty($q) && empty($name) && empty($phone)) {
            return response()->json(['success' => false, 'message' => 'Query is empty']);
        }

        // ⚠️  CRITICAL: phone & name are ENCRYPTED in the DB.
        // SQL LIKE queries search against ciphertext and NEVER match.
        // We MUST load all customers and filter after Laravel decrypts them.
        $cleanInputPhone = preg_replace('/[^0-9]/', '', $phone ?: $q);
        $nameQuery = strtolower($name ?: $q);

        $customer = Customer::all()->first(function ($cust) use ($cleanInputPhone, $nameQuery) {
            $decryptedPhone = strtolower((string) ($cust->phone ?? ''));
            $decryptedName = strtolower((string) ($cust->name ?? ''));
            $cleanCustPhone = preg_replace('/[^0-9]/', '', $decryptedPhone);

            // Match by phone digits
            if (
                $cleanInputPhone && $cleanCustPhone &&
                str_contains($cleanCustPhone, $cleanInputPhone)
            ) {
                return true;
            }

            // Match by name (partial, case-insensitive)
            if (
                $nameQuery && $nameQuery !== 'walk-in customer' &&
                str_contains($decryptedName, $nameQuery)
            ) {
                return true;
            }

            return false;
        });

        if (!$customer) {
            return response()->json(['success' => false, 'message' => 'Customer not found']);
        }

        $hasMembership = $customer->membership_status === 'Active';
        $discountPercent = 0;
        if ($hasMembership) {
            $discountPercent = stripos($customer->membership_type, 'VIP') !== false ? 15 : 10;
        }

        $totalSpent = $customer->invoices()->sum('payable_amount');
        $lastInvoice = $customer->invoices()->latest()->first();
        $lastVisit = $lastInvoice ? $lastInvoice->created_at->format('d M, Y') : null;

        return response()->json([
            'success' => true,
            'customer' => [
                'id' => $customer->id,
                'name' => $customer->name,
                'phone' => $customer->phone,
                'membership_type' => $customer->membership_type,
                'membership_status' => $customer->membership_status,
                'discount_percent' => $discountPercent,
                'total_spent' => $totalSpent,
                'last_visit' => $lastVisit,
            ],
        ]);
    }
}
