<?php

namespace Database\Seeders;

use App\Models\Branch;
use App\Models\Appointment;
use App\Models\Category;
use App\Models\Customer;
use App\Models\Expense;
use App\Models\Invoice;
use App\Models\InvoiceItem;
use App\Models\Product;
use App\Models\ProductUsage;
use App\Models\Purchase;
use App\Models\PurchaseItem;
use App\Models\Service;
use App\Models\ServicePackage;
use App\Models\Staff;
use App\Models\StaffAttendance;
use App\Models\StaffRole;
use App\Models\Supplier;
use App\Models\UpsellPerformance;
use App\Models\User;
use Carbon\Carbon;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Str;

class DatabaseSeeder extends Seeder
{
    public function run(): void
    {
        DB::transaction(function () {
            $branch = $this->seedBranches();
            $admin = $this->seedUsers($branch);
            $roles = $this->seedRoles($branch);
            $categories = $this->seedCategories($branch);
            $suppliers = $this->seedSuppliers($branch);
            $products = $this->seedProducts($branch, $categories, $suppliers);
            $services = $this->seedServices($branch, $categories);
            $packages = $this->seedPackages($branch, $services);
            $staff = $this->seedStaff($branch, $roles, $services);
            $customers = $this->seedCustomers($branch);

            $this->seedPurchases($branch, $suppliers, $products);
            $this->seedInvoices($branch, $admin, $customers, $staff, $products, $services, $packages);
            $this->seedAppointments($branch, $customers, $staff, $services, $packages);
            $this->seedReportData($branch, $staff, $products, $services);
            $this->seedExpenses($branch, $admin);
        });
    }

    private function saveModel(string $class, int $id, array $attributes): Model
    {
        $model = $class::withoutGlobalScopes()->find($id) ?? new $class();
        $model->exists || $model->setAttribute('id', $id);
        $model->forceFill($attributes)->save();

        return $model->refresh();
    }

    private function seedBranches(): Branch
    {
        $this->saveModel(Branch::class, 1, [
            'name' => 'Green Avenue Branch',
            'address' => 'Faisalabad City',
            'phone' => '03000000001',
            'opening_time' => '09:00:00',
            'closing_time' => '21:00:00',
            'is_active' => true,
        ]);

        $this->saveModel(Branch::class, 2, [
            'name' => 'Pearl City Branch',
            'address' => 'Faisalabad City',
            'phone' => '03000000002',
            'opening_time' => '10:00:00',
            'closing_time' => '22:00:00',
            'is_active' => true,
        ]);

        return Branch::withoutGlobalScopes()->findOrFail(1);
    }

    private function seedUsers(Branch $branch): User
    {
        $admin = User::updateOrCreate(
            ['email' => 'Sa40560@gmail.com'],
            [
                'name' => 'Admin Manager',
                'password' => Hash::make('12345678'),
                'role' => 'admin',
                'branch_id' => $branch->id,
            ]
        );

        User::updateOrCreate(
            ['email' => 'user@gmail.com'],
            [
                'name' => 'Reception Staff',
                'password' => Hash::make('12345678'),
                'role' => 'employee',
                'branch_id' => $branch->id,
            ]
        );

        return $admin;
    }

    private function seedRoles(Branch $branch): array
    {
        $roles = [
            1 => ['name' => 'Salon Manager', 'description' => null],
            2 => ['name' => 'Senior Stylist', 'description' => null],
            3 => ['name' => 'Color Specialist', 'description' => null],
            4 => ['name' => 'Nail Technician', 'description' => null],
            5 => ['name' => 'Spa Therapist', 'description' => null],
            6 => ['name' => 'Receptionist', 'description' => null],
        ];

        foreach ($roles as $id => $role) {
            $roles[$id] = $this->saveModel(StaffRole::class, $id, [
                ...$role,
                'branch_id' => $branch->id,
                'permissions' => [
                    'pos' => ['view' => '1', 'create' => '1', 'edit' => '1', 'delete' => '0'],
                    'appointments' => ['view' => '1', 'create' => '1', 'edit' => '1', 'delete' => '0'],
                    'inventory' => ['view' => '1', 'create' => '0', 'edit' => '0', 'delete' => '0'],
                    'reports' => ['view' => $id === 1 ? '1' : '0'],
                ],
            ]);
        }

        return $roles;
    }

    private function seedCategories(Branch $branch): array
    {
        $names = [
            'service' => ['Hair Styling', 'Hair Color', 'Nails', 'Skin Care', 'Waxing', 'Massage', 'Makeup', 'Brows and Lashes'],
            'product' => ['Hair Care', 'Color Supplies', 'Nail Care', 'Skin Care Retail', 'Tools', 'Consumables'],
        ];

        $categories = [];
        $id = 1;
        foreach ($names as $type => $categoryNames) {
            foreach ($categoryNames as $name) {
                $category = Category::withoutGlobalScopes()->updateOrCreate(
                    ['slug' => Str::slug($name)],
                    ['name' => $name, 'type' => $type, 'branch_id' => $branch->id]
                );
                $categories[$type][$name] = $category;
                $id = max($id, $category->id + 1);
            }
        }

        return $categories;
    }

    private function seedSuppliers(Branch $branch): array
    {
        $suppliers = [
            1 => ['name' => 'Luxe Beauty Supply Co.', 'contact_person' => 'Ayesha Khan', 'email' => 'orders@luxebeauty.test', 'phone' => '03011223344', 'address' => 'Susan Road, Faisalabad', 'payment_terms' => 'net_30'],
            2 => ['name' => 'PureGlow Professional', 'contact_person' => 'Bilal Ahmed', 'email' => 'sales@pureglow.test', 'phone' => '03022334455', 'address' => 'D Ground, Faisalabad', 'payment_terms' => 'net_15'],
            3 => ['name' => 'SalonCraft Tools', 'contact_person' => 'Hina Malik', 'email' => 'support@saloncraft.test', 'phone' => '03033445566', 'address' => 'Peoples Colony, Faisalabad', 'payment_terms' => 'cod'],
            4 => ['name' => 'NailStudio Wholesale', 'contact_person' => 'Danish Raza', 'email' => 'hello@nailstudio.test', 'phone' => '03044556677', 'address' => 'Kohinoor City, Faisalabad', 'payment_terms' => 'net_30'],
            5 => ['name' => 'DermaCare Distribution', 'contact_person' => 'Sara Noor', 'email' => 'care@dermacare.test', 'phone' => '03055667788', 'address' => 'Madina Town, Faisalabad', 'payment_terms' => 'net_60'],
            6 => ['name' => 'Fresh Linen Essentials', 'contact_person' => 'Usman Tariq', 'email' => 'orders@freshlinen.test', 'phone' => '03066778899', 'address' => 'Canal Road, Faisalabad', 'payment_terms' => 'net_15'],
        ];

        foreach ($suppliers as $id => $supplier) {
            $suppliers[$id] = $this->saveModel(Supplier::class, $id, [
                ...$supplier,
                'branch_id' => $branch->id,
                'is_active' => true,
            ]);
        }

        return $suppliers;
    }

    private function seedProducts(Branch $branch, array $categories, array $suppliers): array
    {
        $products = [
            ['Keratin Repair Shampoo', 'Hair Care', 1, 1800, 980, 42, 'retail'],
            ['Argan Smooth Conditioner', 'Hair Care', 1, 1950, 1050, 38, 'retail'],
            ['Volume Boost Hair Spray', 'Hair Care', 1, 1450, 760, 32, 'retail'],
            ['Heat Shield Serum', 'Hair Care', 1, 2100, 1180, 27, 'retail'],
            ['Ammonia Free Color 5N', 'Color Supplies', 2, 1250, 720, 55, 'service_supply'],
            ['Ammonia Free Color 7A', 'Color Supplies', 2, 1250, 720, 50, 'service_supply'],
            ['Developer Cream 20 Vol', 'Color Supplies', 2, 950, 520, 36, 'service_supply'],
            ['Bleach Powder Blue', 'Color Supplies', 2, 1650, 940, 24, 'service_supply'],
            ['Cuticle Oil Pen', 'Nail Care', 4, 850, 390, 46, 'retail'],
            ['Gel Polish Ruby Red', 'Nail Care', 4, 1350, 720, 30, 'service_supply'],
            ['Gel Polish Nude Pink', 'Nail Care', 4, 1350, 720, 31, 'service_supply'],
            ['Acetone Professional', 'Nail Care', 4, 780, 420, 26, 'service_supply'],
            ['Hydrating Facial Cleanser', 'Skin Care Retail', 5, 2250, 1320, 21, 'retail'],
            ['Vitamin C Brightening Serum', 'Skin Care Retail', 5, 3200, 1900, 18, 'retail'],
            ['Aloe Vera Cooling Mask', 'Skin Care Retail', 5, 1750, 920, 22, 'retail'],
            ['Brow Tint Kit', 'Consumables', 2, 2400, 1460, 14, 'service_supply'],
            ['Disposable Facial Towels', 'Consumables', 6, 650, 360, 80, 'service_supply'],
            ['Wax Strips Roll', 'Consumables', 6, 920, 510, 34, 'service_supply'],
            ['Ceramic Round Brush', 'Tools', 3, 1850, 980, 16, 'retail'],
            ['Professional Section Clips', 'Tools', 3, 520, 260, 64, 'retail'],
        ];

        $saved = [];
        foreach ($products as $index => [$name, $categoryName, $supplierId, $selling, $cost, $stock, $type]) {
            $id = $index + 1;
            $saved[$id] = Product::withoutGlobalScopes()->updateOrCreate(
                ['sku' => 'CRP-' . str_pad((string) $id, 4, '0', STR_PAD_LEFT)],
                [
                    'category_id' => $categories['product'][$categoryName]->id,
                    'supplier_id' => $suppliers[$supplierId]->id,
                    'name' => $name,
                    'description' => $name . ' for professional salon retail and daily service use.',
                    'selling_price' => $selling,
                    'cost_price' => $cost,
                    'current_stock' => $stock,
                    'min_stock_level' => 8,
                    'product_type' => $type,
                    'track_inventory' => true,
                    'last_restocked' => now()->subDays(($index % 12) + 1)->toDateString(),
                    'branch_id' => $branch->id,
                ]
            );
        }

        return $saved;
    }

    private function seedServices(Branch $branch, array $categories): array
    {
        $serviceNames = [
            'Hair Styling' => ['Classic Blow Dry', 'Express Blow Dry', 'Luxury Blowout', 'Women Haircut', 'Men Haircut', 'Kids Haircut', 'Hair Trim', 'Layered Cut', 'Fringe Styling', 'Formal Updo'],
            'Hair Color' => ['Root Touch Up', 'Global Hair Color', 'Half Head Highlights', 'Full Head Highlights', 'Balayage', 'Toner Refresh', 'Color Correction Consultation', 'Gloss Treatment'],
            'Nails' => ['Classic Manicure', 'Classic Pedicure', 'Gel Manicure', 'Gel Pedicure', 'French Tips', 'Nail Art Basic', 'Nail Art Premium', 'Acrylic Refill'],
            'Skin Care' => ['Express Facial', 'Hydrating Facial', 'Brightening Facial', 'Deep Cleansing Facial', 'Anti Acne Facial', 'Gold Facial', 'Dermaplaning', 'Under Eye Treatment'],
            'Waxing' => ['Upper Lip Wax', 'Chin Wax', 'Full Face Wax', 'Half Arms Wax', 'Full Arms Wax', 'Half Legs Wax', 'Full Legs Wax'],
            'Massage' => ['Head Massage', 'Neck and Shoulder Massage', 'Foot Reflexology', 'Relaxing Back Massage'],
            'Makeup' => ['Party Makeup', 'Soft Glam Makeup', 'Bridal Trial Makeup'],
            'Brows and Lashes' => ['Brow Threading', 'Brow Tint'],
        ];

        $saved = [];
        $id = 1;
        foreach ($serviceNames as $categoryName => $names) {
            foreach ($names as $name) {
                $price = 600 + ($id * 135);
                $saved[$id] = $this->saveModel(Service::class, $id, [
                    'category_id' => $categories['service'][$categoryName]->id,
                    'name' => $name,
                    'price' => $price,
                    'duration' => 20 + (($id % 5) * 15),
                    'pricing_levels_enabled' => $id % 6 === 0,
                    'pricing_levels' => $id % 6 === 0 ? ['junior' => $price - 150, 'senior' => $price, 'expert' => $price + 350] : null,
                    'peak_pricing_enabled' => $id % 7 === 0,
                    'peak_price' => $id % 7 === 0 ? $price + 250 : null,
                    'peak_start' => $id % 7 === 0 ? '17:00:00' : null,
                    'peak_end' => $id % 7 === 0 ? '21:00:00' : null,
                    'image' => null,
                    'is_popular' => $id <= 12,
                    'branch_id' => $branch->id,
                ]);
                $id++;
            }
        }

        Service::withoutGlobalScopes()->where('id', '>', 50)->delete();

        return $saved;
    }

    private function seedPackages(Branch $branch, array $services): array
    {
        $packages = [
            ['Signature Hair Refresh', 'Haircut, blow dry, and gloss finish.', 5200, [1, 4, 16]],
            ['Color Care Bundle', 'Root touch up with toner and repair blow dry.', 7800, [1, 11, 16]],
            ['Weekend Glam Combo', 'Soft glam makeup, blow dry, and brow threading.', 9500, [1, 48, 50]],
            ['Classic Nail Day', 'Classic manicure and pedicure package.', 3600, [19, 20]],
            ['Gel Nail Finish', 'Gel manicure with gel pedicure and basic nail art.', 6200, [21, 22, 24]],
            ['Hydration Facial Plan', 'Hydrating facial with under eye treatment.', 5800, [28, 34]],
            ['Bright Skin Ritual', 'Brightening facial, dermaplaning, and face wax.', 8900, [29, 33, 37]],
            ['Relax and Reset', 'Head massage, neck massage, and foot reflexology.', 4900, [44, 45, 46]],
            ['Complete Waxing Essentials', 'Full face, full arms, and half legs waxing.', 6400, [37, 40, 41]],
            ['Bridal Prep Mini', 'Trial makeup, facial, brow tint, and manicure.', 12500, [48, 28, 50, 19]],
        ];

        $saved = [];
        foreach ($packages as $index => [$name, $description, $price, $serviceIds]) {
            $id = $index + 1;
            $package = $this->saveModel(ServicePackage::class, $id, [
                'name' => $name,
                'description' => $description,
                'price' => $price,
                'duration' => collect($serviceIds)->sum(fn ($serviceId) => $services[$serviceId]->duration ?? 30),
                'is_active' => true,
                'pricing_levels_enabled' => false,
                'pricing_levels' => null,
                'peak_pricing_enabled' => $id % 3 === 0,
                'peak_price' => $id % 3 === 0 ? $price + 700 : null,
                'peak_start' => $id % 3 === 0 ? '17:00:00' : null,
                'peak_end' => $id % 3 === 0 ? '21:00:00' : null,
                'branch_id' => $branch->id,
            ]);

            $package->services()->sync($serviceIds);
            $saved[$id] = $package;
        }

        return $saved;
    }

    private function seedStaff(Branch $branch, array $roles, array $services): array
    {
        $staffRows = [
            ['Amina Siddiqui', 'amina.siddiqui@crimpers.test', '03070010001', 'Salon Manager', 1],
            ['Zara Mahmood', 'zara.mahmood@crimpers.test', '03070010002', 'Senior Stylist', 2],
            ['Mariam Sheikh', 'mariam.sheikh@crimpers.test', '03070010003', 'Color Specialist', 3],
            ['Noor Fatima', 'noor.fatima@crimpers.test', '03070010004', 'Stylist', 2],
            ['Hira Javed', 'hira.javed@crimpers.test', '03070010005', 'Nail Technician', 4],
            ['Sana Qureshi', 'sana.qureshi@crimpers.test', '03070010006', 'Nail Technician', 4],
            ['Iqra Saleem', 'iqra.saleem@crimpers.test', '03070010007', 'Spa Therapist', 5],
            ['Mehwish Rauf', 'mehwish.rauf@crimpers.test', '03070010008', 'Skin Therapist', 5],
            ['Laiba Anwar', 'laiba.anwar@crimpers.test', '03070010009', 'Makeup Artist', 2],
            ['Kinza Ali', 'kinza.ali@crimpers.test', '03070010010', 'Receptionist', 6],
        ];

        $saved = [];
        foreach ($staffRows as $index => [$name, $email, $phone, $position, $roleId]) {
            $id = $index + 1;
            $staff = $this->saveModel(Staff::class, $id, [
                'name' => $name,
                'email' => $email,
                'phone' => $phone,
                'position' => $position,
                'hourly_rate' => 450 + ($index * 40),
                'base_salary' => 42000 + ($index * 2500),
                'commission_per_customer' => 150 + ($index * 10),
                'commission_per_service' => 200 + ($index * 15),
                'total_earned_commission' => 2500 + ($index * 400),
                'hiring_date' => now()->subMonths(18 - $index)->toDateString(),
                'status' => true,
                'current_shift' => $index % 2 === 0 ? 'morning' : 'evening',
                'shift_start' => $index % 2 === 0 ? '09:00:00' : '13:00:00',
                'shift_end' => $index % 2 === 0 ? '17:00:00' : '21:00:00',
                'bio' => $name . ' is part of the Crimpers professional salon team.',
                'staff_role_id' => $roles[$roleId]->id,
                'rating' => 4,
                'rating_total' => 90 + ($index * 7),
                'rating_count' => 20 + $index,
                'last_paid_at' => now()->subDays(12 + $index),
                'branch_id' => $branch->id,
            ]);

            $serviceIds = collect($services)->keys()->slice(($index * 5) % 35, 8)->values()->all();
            $staff->services()->sync($serviceIds);
            $saved[$id] = $staff;
        }

        return $saved;
    }

    private function seedCustomers(Branch $branch): array
    {
        $names = [
            'Fatima Ahmed', 'Ayesha Malik', 'Sadia Khan', 'Maham Raza', 'Hina Butt',
            'Nimra Shah', 'Rabia Noor', 'Anum Iqbal', 'Eman Tariq', 'Sarah Ali',
            'Maryam Hassan', 'Laiba Sheikh', 'Zoya Akhtar', 'Meera Nadeem', 'Areeba Javed',
            'Komal Asif', 'Sanam Qureshi', 'Tania Waheed', 'Javeria Farooq', 'Aleena Rauf',
            'Minaal Saleem', 'Rida Ansari', 'Kinza Zahid', 'Saira Usman', 'Iqra Hameed',
        ];

        $customers = [];
        foreach ($names as $index => $name) {
            $id = $index + 1;
            $customers[$id] = $this->saveModel(Customer::class, $id, [
                'name' => $name,
                'phone' => '0314' . str_pad((string) (2200000 + $id), 7, '0', STR_PAD_LEFT),
                'email' => Str::slug($name, '.') . '@example.test',
                'birthday' => now()->subYears(22 + ($index % 18))->subDays($index * 9)->toDateString(),
                'preferences' => ['Hair care', 'Appointment reminders', 'Product recommendations'][$index % 3],
                'social_media' => ['instagram' => '@' . Str::slug($name, '')],
                'membership_type' => $index % 4 === 0 ? 'Gold' : ($index % 5 === 0 ? 'Silver' : null),
                'membership_expires' => $index % 4 === 0 ? now()->addMonths(8)->toDateString() : null,
                'prepaid_credit' => $index % 6 === 0 ? 2500 : 0,
                'notes' => 'Prefers professional consultation before new treatments.',
                'last_visit_at' => now()->subDays($index + 1),
                'branch_id' => $branch->id,
            ]);
        }

        return $customers;
    }

    private function seedPurchases(Branch $branch, array $suppliers, array $products): void
    {
        for ($i = 1; $i <= 12; $i++) {
            $supplier = $suppliers[(($i - 1) % count($suppliers)) + 1];
            $purchase = Purchase::withoutGlobalScopes()->updateOrCreate(
                ['purchase_order_number' => 'PO-2026-' . str_pad((string) $i, 4, '0', STR_PAD_LEFT)],
                [
                    'supplier_id' => $supplier->id,
                    'order_date' => now()->subDays(45 - ($i * 3))->toDateString(),
                    'expected_delivery_date' => now()->subDays(41 - ($i * 3))->toDateString(),
                    'actual_delivery_date' => $i % 4 === 0 ? null : now()->subDays(39 - ($i * 3))->toDateString(),
                    'status' => $i % 4 === 0 ? 'ordered' : ($i % 5 === 0 ? 'partially_received' : 'received'),
                    'total_amount' => 0,
                    'notes' => 'Demo purchase order for salon inventory.',
                    'branch_id' => $branch->id,
                ]
            );

            $purchase->purchaseItems()->delete();
            $total = 0;
            for ($line = 0; $line < 3; $line++) {
                $product = $products[((($i + $line) - 1) % count($products)) + 1];
                $quantity = 6 + $line + $i;
                $received = $purchase->status === 'ordered' ? 0 : ($purchase->status === 'partially_received' ? max(1, $quantity - 3) : $quantity);
                $lineTotal = $quantity * (float) $product->cost_price;
                $total += $lineTotal;
                PurchaseItem::create([
                    'purchase_id' => $purchase->id,
                    'product_id' => $product->id,
                    'quantity_ordered' => $quantity,
                    'quantity_received' => $received,
                    'unit_cost' => $product->cost_price,
                    'line_total' => $lineTotal,
                ]);
            }

            $purchase->update(['total_amount' => $total]);
        }
    }

    private function seedInvoices(Branch $branch, User $admin, array $customers, array $staff, array $products, array $services, array $packages): void
    {
        for ($i = 1; $i <= 35; $i++) {
            $customer = $customers[(($i - 1) % count($customers)) + 1];
            $staffMember = $staff[(($i - 1) % count($staff)) + 1];
            $invoice = Invoice::withoutGlobalScopes()->updateOrCreate(
                ['invoice_no' => 'INV-2026-' . str_pad((string) $i, 5, '0', STR_PAD_LEFT)],
                [
                    'user_id' => $admin->id,
                    'customer_id' => $customer->id,
                    'customer_name' => $customer->name,
                    'total_amount' => 0,
                    'tax' => 0,
                    'discount' => 0,
                    'payable_amount' => 0,
                    'payment_method' => ['cash', 'card', 'qr'][$i % 3],
                    'status' => $i % 13 === 0 ? 'pending' : 'paid',
                    'cash_received' => null,
                    'change_returned' => null,
                    'staff_id' => $staffMember->id,
                    'branch_id' => $branch->id,
                    'created_at' => Carbon::now()->subDays(35 - $i)->setTime(10 + ($i % 8), ($i * 7) % 60),
                    'updated_at' => Carbon::now()->subDays(35 - $i)->setTime(10 + ($i % 8), ($i * 7) % 60),
                ]
            );

            $invoice->items()->delete();

            $items = [
                $services[(($i - 1) % count($services)) + 1],
                $products[(($i + 2) % count($products)) + 1],
            ];
            if ($i % 5 === 0) {
                $items[] = $packages[(($i / 5 - 1) % count($packages)) + 1];
            }

            $subtotal = 0;
            foreach ($items as $item) {
                $quantity = $item instanceof Product ? (($i % 3) + 1) : 1;
                $price = (float) ($item->selling_price ?? $item->price);
                $lineSubtotal = $quantity * $price;
                $subtotal += $lineSubtotal;

                InvoiceItem::create([
                    'invoice_id' => $invoice->id,
                    'itemizable_id' => $item->id,
                    'itemizable_type' => $item::class,
                    'custom_name' => null,
                    'quantity' => $quantity,
                    'price' => $price,
                    'subtotal' => $lineSubtotal,
                ]);
            }

            $discount = $i % 6 === 0 ? 500 : 0;
            $tax = round(($subtotal - $discount) * 0.05, 2);
            $payable = $subtotal - $discount + $tax;
            $cashReceived = $invoice->payment_method === 'cash' ? ceil($payable / 500) * 500 : null;

            $invoice->forceFill([
                'total_amount' => $subtotal,
                'tax' => $tax,
                'discount' => $discount,
                'payable_amount' => $payable,
                'cash_received' => $cashReceived,
                'change_returned' => $cashReceived ? $cashReceived - $payable : null,
            ])->save();
        }
    }

    private function seedExpenses(Branch $branch, User $admin): void
    {
        $expenses = [
            ['Salon towel laundry service', 3200],
            ['Tea and refreshments for clients', 1800],
            ['Reception printer paper and ink', 2400],
            ['Emergency plumbing repair', 6500],
            ['Social media photography props', 4200],
            ['Staff meal allowance', 5500],
            ['Floor cleaning supplies', 2100],
            ['Chair hydraulic maintenance', 7300],
            ['Bridal room flower decor', 3600],
            ['Generator fuel backup', 8800],
            ['POS receipt rolls', 1600],
            ['Window display update', 4800],
            ['Monthly pest control', 3900],
            ['Internet service payment', 5200],
            ['Uniform stitching advance', 6000],
        ];

        foreach ($expenses as $index => [$description, $amount]) {
            $this->saveModel(Expense::class, $index + 1, [
                'branch_id' => $branch->id,
                'description' => $description,
                'amount' => $amount,
                'deducted_from_drawer' => $index % 2 === 0,
                'user_id' => $admin->id,
                'created_at' => now()->subDays(20 - $index),
                'updated_at' => now()->subDays(20 - $index),
            ]);
        }
    }

    private function seedAppointments(Branch $branch, array $customers, array $staff, array $services, array $packages): void
    {
        $statuses = ['scheduled', 'confirmed', 'arrived', 'completed', 'late', 'cancelled'];
        $notes = [
            'Client requested a gentle consultation before service.',
            'Prefers tea on arrival and a quiet station.',
            'Patch test already discussed with customer.',
            'Requested senior stylist if available.',
            'Customer may add retail products at checkout.',
            'First visit, keep extra consultation time.',
            'Membership customer, confirm package balance.',
            'Needs quick checkout after appointment.',
        ];

        for ($i = 1; $i <= 40; $i++) {
            $customer = $customers[(($i - 1) % count($customers)) + 1];
            $staffMember = $staff[(($i - 1) % count($staff)) + 1];
            $isPackageBooking = $i % 6 === 0;
            $bookable = $isPackageBooking
                ? $packages[(($i / 6 - 1) % count($packages)) + 1]
                : $services[(($i - 1) % count($services)) + 1];

            $date = now()->startOfDay()->addDays(($i % 12) - 1);
            $hour = 9 + (($i - 1) % 8);
            $minute = (($i * 15) % 60);
            $start = now()->setTime($hour, $minute, 0);
            $duration = min((int) ($bookable->duration ?? 45), 180);
            $end = $start->copy()->addMinutes(max($duration, 30));

            $status = $date->isPast() ? 'completed' : $statuses[$i % count($statuses)];

            $this->saveModel(Appointment::class, $i, [
                'staff_id' => $staffMember->id,
                'service_id' => $isPackageBooking ? null : $bookable->id,
                'service_package_id' => $isPackageBooking ? $bookable->id : null,
                'customer_id' => $customer->id,
                'appointment_date' => $date->toDateString(),
                'start_time' => $start->format('H:i:s'),
                'end_time' => $end->format('H:i:s'),
                'customer_name' => $customer->name,
                'customer_phone' => $customer->phone,
                'notes' => $notes[($i - 1) % count($notes)],
                'status' => $status,
                'branch_id' => $branch->id,
                'created_at' => now()->subDays(7)->addHours($i),
                'updated_at' => now()->subDays(2)->addHours($i),
            ]);
        }
    }

    private function seedReportData(Branch $branch, array $staff, array $products, array $services): void
    {
        $this->seedStaffAttendance($branch, $staff);
        $this->seedUpsellPerformance($staff);
        $this->seedProductUsage($products, $services);
    }

    private function seedStaffAttendance(Branch $branch, array $staff): void
    {
        $statuses = ['present', 'present', 'present', 'late', 'half_day', 'leave', 'absent'];

        foreach ($staff as $staffIndex => $staffMember) {
            for ($day = 0; $day < 18; $day++) {
                $date = now()->startOfDay()->subDays($day);
                $status = $statuses[($staffIndex + $day) % count($statuses)];
                $checkIn = null;
                $checkOut = null;

                if (in_array($status, ['present', 'late', 'half_day'], true)) {
                    $checkInMinuteDelay = $status === 'late' ? 35 : (($staffIndex + $day) % 4) * 5;
                    $checkIn = $date->copy()->setTime(9, 0)->addMinutes($checkInMinuteDelay);
                    $checkOut = $status === 'half_day'
                        ? $date->copy()->setTime(14, 0)
                        : $date->copy()->setTime(17, 30)->addMinutes(($staffIndex % 3) * 10);
                }

                StaffAttendance::withoutGlobalScopes()->updateOrCreate(
                    [
                        'staff_id' => $staffMember->id,
                        'attendance_date' => $date->toDateString(),
                    ],
                    [
                        'check_in_time' => $checkIn,
                        'check_out_time' => $checkOut,
                        'status' => $status,
                        'notes' => match ($status) {
                            'late' => 'Arrived late due to client handover from previous shift.',
                            'half_day' => 'Approved half-day shift for schedule balancing.',
                            'leave' => 'Approved leave day.',
                            'absent' => 'Marked absent for demo attendance reporting.',
                            default => 'Completed regular scheduled shift.',
                        },
                        'branch_id' => $branch->id,
                    ]
                );
            }
        }
    }

    private function seedUpsellPerformance(array $staff): void
    {
        foreach ($staff as $index => $staffMember) {
            $totalUpsells = 8 + ($index * 3);
            $averageValue = 650 + ($index * 95);
            $revenue = $totalUpsells * $averageValue;

            UpsellPerformance::updateOrCreate(
                ['staff_id' => $staffMember->id],
                [
                    'total_upsells' => $totalUpsells,
                    'upsell_revenue' => $revenue,
                    'conversion_rate' => 18 + ($index * 2.4),
                    'average_upsell_value' => $averageValue,
                    'last_upsell_date' => now()->subDays($index % 7)->setTime(16, 15),
                ]
            );

            $staffMember->forceFill([
                'total_earned_commission' => 2500 + ($index * 450) + round($revenue * 0.06),
            ])->save();
        }
    }

    private function seedProductUsage(array $products, array $services): void
    {
        ProductUsage::query()->delete();

        $supplyProducts = collect($products)
            ->filter(fn ($product) => $product->product_type === 'service_supply')
            ->values();

        if ($supplyProducts->isEmpty()) {
            $supplyProducts = collect($products)->values();
        }

        for ($i = 1; $i <= 60; $i++) {
            $product = $supplyProducts[(($i - 1) % $supplyProducts->count())];
            $service = $services[(($i + 4) % count($services)) + 1];

            ProductUsage::create([
                'product_id' => $product->id,
                'service_id' => $service->id,
                'invoice_id' => null,
                'quantity_used' => 1 + ($i % 3),
                'usage_date' => now()->subDays($i % 30)->toDateString(),
                'notes' => 'Demo service supply usage for reports cost analysis.',
            ]);
        }
    }
}
