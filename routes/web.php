<?php

use Illuminate\Support\Facades\Route;

use App\Http\Controllers\POSController;
use App\Http\Controllers\ReconciliationController;
use App\Http\Controllers\InvoiceController;
use App\Http\Controllers\LoginController;
use App\Http\Controllers\BookingController;
use App\Http\Controllers\AppointmentController;
use App\Http\Controllers\AdminController;
use App\Http\Controllers\CustomerController;
use App\Http\Controllers\ServiceController;
use App\Http\Controllers\ServicePackageController;
use App\Http\Controllers\StaffController;
use App\Http\Controllers\ProductController;
use App\Http\Controllers\SupplierController;
use App\Http\Controllers\PurchaseController;
use App\Http\Controllers\InventoryController;
use App\Http\Controllers\WhatsAppController;
use App\Http\Controllers\ReportController;
use App\Http\Controllers\PromotionController;
use App\Http\Controllers\CategoryController;
use App\Http\Controllers\StaffRoleController;
use App\Http\Controllers\StaffHRMSController;
use App\Http\Controllers\Auth\TwoFactorController;
use App\Http\Controllers\BranchController;

Route::get('/login', [LoginController::class , 'showLoginForm'])->name('login');
Route::post('/login', [LoginController::class , 'login']);
Route::post('/logout', [LoginController::class , 'logout'])->name('logout');

// Password Reset Routes
Route::get('/forgot-password', function () {
    return view('auth.forgot-password');
})->middleware('guest')->name('password.request');

Route::post('/forgot-password', [App\Http\Controllers\Auth\ForgotPasswordController::class , 'sendResetLinkEmail'])
    ->middleware('guest')->name('password.email');

Route::get('/reset-password/{token}', function (string $token) {
    return view('auth.reset-password', ['token' => $token]);
})->middleware('guest')->name('password.reset');

Route::post('/reset-password', [App\Http\Controllers\Auth\ResetPasswordController::class , 'reset'])
    ->middleware('guest')->name('password.update');

Route::middleware(['auth'])->group(function () {
    // Dashboard / Landing
    Route::get('/', function() {
        $user = auth()->user();
        if ($user->hasPermission('dashboard', 'view')) {
            return app(\App\Http\Controllers\AdminController::class)->index();
        }
        
        // Dynamic redirection for users without dashboard access
        if ($user->hasPermission('pos', 'access')) return redirect()->route('pos.index');
        if ($user->hasPermission('appointments', 'view')) return redirect()->route('appointments.index');
        if ($user->hasPermission('sales', 'view')) return redirect()->route('invoices.index');
        if ($user->hasPermission('purchases', 'view')) return redirect()->route('purchases.index');
        if ($user->hasPermission('inventory', 'view')) return redirect()->route('products.index');
        if ($user->hasPermission('customers', 'view')) return redirect()->route('customers.index');
        if ($user->hasPermission('suppliers', 'view')) return redirect()->route('suppliers.index');
        if ($user->hasPermission('history', 'access')) return redirect()->route('invoices.index');
        if ($user->hasPermission('staff', 'view')) return redirect()->route('staff.index');
        if ($user->hasPermission('staff', 'attendance')) return redirect()->route('staff.attendance-all');
        
        return abort(403, 'Your account has no module permissions.');
    })->name('admin.index');

    Route::get('/admin/chart-data', [AdminController::class, 'getChartData'])->name('admin.chart-data');
    Route::get('/admin/branch-select', [AdminController::class, 'branchSelect'])->name('admin.branch.select');
    Route::post('/admin/branch-switch-from-select', [AdminController::class, 'branchSwitchFromSelect'])->name('admin.branch.switch_from_select');

    Route::post('/branch/switch', [BranchController::class, 'switch'])->name('branch.switch');
    Route::put('/branches/{branch}/hours', [BranchController::class, 'updateHours'])->name('branches.update-hours')->middleware('permission:business,view');

    // POS & Sales History
    Route::middleware(['permission:pos,access'])->group(function() {
        Route::get('/pos', [POSController::class, 'index'])->name('pos.index');
        Route::get('/pos/check-coupon', [POSController::class, 'checkCoupon'])->name('pos.check-coupon');
        Route::get('/pos/search-customer', [POSController::class, 'searchCustomer'])->name('pos.search-customer');
        Route::post('/pos/checkout', [POSController::class, 'store'])->name('pos.store');
        Route::get('/pos/payment', [POSController::class, 'payment'])->name('pos.payment');
    });

    // History & Invoices (General Access)
    Route::middleware(['permission:history,access'])->group(function() {
        Route::get('/invoices', [InvoiceController::class, 'index'])->name('invoices.index');
        Route::get('/invoices/{invoice}', [InvoiceController::class, 'show'])->name('invoices.show');
        Route::get('/invoices-export', [InvoiceController::class, 'export'])->name('invoices.export');
        Route::get('/invoices/{invoice}/whatsapp-share', [WhatsAppController::class, 'shareInvoice'])->name('invoice.whatsapp.share');
    });

    // Specific Sales/History Sub-permissions
    Route::middleware(['permission:sales,view'])->group(function() {
        // Any sales-specific routes that aren't general history would go here
    });

    Route::middleware(['permission:reconciliation,access'])->group(function() {
        Route::get('/reconciliation', [ReconciliationController::class, 'index'])->name('reconciliation.index');
        Route::post('/reconciliation', [ReconciliationController::class, 'store'])->name('reconciliation.store');
    });

    Route::middleware(['permission:expenses,create'])->group(function() {
        Route::get('/expenses/create', [App\Http\Controllers\ExpenseController::class, 'create'])->name('expenses.create');
        Route::post('/expenses', [App\Http\Controllers\ExpenseController::class, 'store'])->name('expenses.store');
    });

    // CRM
    Route::middleware(['permission:customers,view'])->group(function() {
        Route::get('/customers', [CustomerController::class, 'index'])->name('customers.index');
        Route::get('/customers/search', [CustomerController::class, 'search'])->name('customers.search');
        Route::get('/customers/create', [CustomerController::class, 'create'])->name('customers.create')->middleware('permission:customers,create');
        Route::post('/customers', [CustomerController::class, 'store'])->name('customers.store')->middleware('permission:customers,create');
        Route::get('/customers/{customer}', [CustomerController::class, 'show'])->name('customers.show');
        Route::get('/customers/{customer}/edit', [CustomerController::class, 'edit'])->name('customers.edit')->middleware('permission:customers,edit');
        Route::put('/customers/{customer}', [CustomerController::class, 'update'])->name('customers.update')->middleware('permission:customers,edit');
        Route::delete('/customers/{customer}', [CustomerController::class, 'destroy'])->name('customers.destroy')->middleware('permission:customers,delete');
    });

    Route::middleware(['permission:suppliers,view'])->group(function() {
        Route::get('/suppliers', [SupplierController::class, 'index'])->name('suppliers.index');
        Route::get('/suppliers/create', [SupplierController::class, 'create'])->name('suppliers.create')->middleware('permission:suppliers,create');
        Route::post('/suppliers', [SupplierController::class, 'store'])->name('suppliers.store')->middleware('permission:suppliers,create');
        Route::get('/suppliers/{supplier}', [SupplierController::class, 'show'])->name('suppliers.show');
        Route::get('/suppliers/{supplier}/edit', [SupplierController::class, 'edit'])->name('suppliers.edit')->middleware('permission:suppliers,edit');
        Route::put('/suppliers/{supplier}', [SupplierController::class, 'update'])->name('suppliers.update')->middleware('permission:suppliers,edit');
        Route::delete('/suppliers/{supplier}', [SupplierController::class, 'destroy'])->name('suppliers.destroy')->middleware('permission:suppliers,delete');
    });

    // Services & Packages (Grouped with Inventory or separate)
    Route::middleware(['permission:inventory,view'])->group(function() {
        Route::get('/inventory', [InventoryController::class, 'dashboard'])->name('inventory.dashboard');
        Route::get('/inventory/low-stock', [InventoryController::class, 'lowStockAlerts'])->name('inventory.low-stock');
        Route::get('/inventory/stock-report', [InventoryController::class, 'stockReport'])->name('inventory.stock-report');
        Route::get('/inventory/stock-report/export', [InventoryController::class, 'exportStockReport'])->name('inventory.stock-report.export');
        Route::get('/inventory/usage-report', [InventoryController::class, 'usageReport'])->name('inventory.usage-report');
        Route::get('/inventory/usage-report/export', [InventoryController::class, 'exportUsageReport'])->name('inventory.usage-report.export');
        
        Route::get('/products', [ProductController::class, 'index'])->name('products.index');
        Route::get('/products/create', [ProductController::class, 'create'])->name('products.create')->middleware('permission:inventory,create');
        Route::post('/products', [ProductController::class, 'store'])->name('products.store')->middleware('permission:inventory,create');
        Route::get('/products/{product}', [ProductController::class, 'show'])->name('products.show');
        Route::get('/products/{product}/edit', [ProductController::class, 'edit'])->name('products.edit')->middleware('permission:inventory,edit');
        Route::put('/products/{product}', [ProductController::class, 'update'])->name('products.update')->middleware('permission:inventory,edit');
        Route::delete('/products/{product}', [ProductController::class, 'destroy'])->name('products.destroy')->middleware('permission:inventory,delete');
        Route::get('/products/{product}/adjust-stock', [ProductController::class, 'showAdjustStock'])->name('products.adjust-stock.form')->middleware('permission:inventory,edit');
        Route::post('/products/{product}/adjust-stock', [ProductController::class, 'adjustStock'])->name('products.adjust-stock')->middleware('permission:inventory,edit');

        Route::get('/services', [ServiceController::class, 'index'])->name('services.index');
        Route::get('/services/create', [ServiceController::class, 'create'])->name('services.create');
        Route::post('/services', [ServiceController::class, 'store'])->name('services.store');
        Route::get('/services/{service}/edit', [ServiceController::class, 'edit'])->name('services.edit');
        Route::put('/services/{service}', [ServiceController::class, 'update'])->name('services.update');
        Route::delete('/services/{service}', [ServiceController::class, 'destroy'])->name('services.destroy');

        Route::get('/packages', [ServicePackageController::class, 'index'])->name('packages.index');
        Route::get('/packages/create', [ServicePackageController::class, 'create'])->name('packages.create');
        Route::post('/packages', [ServicePackageController::class, 'store'])->name('packages.store');
        Route::get('/packages/{package}/edit', [ServicePackageController::class, 'edit'])->name('packages.edit');
        Route::put('/packages/{package}', [ServicePackageController::class, 'update'])->name('packages.update');
        Route::delete('/packages/{package}', [ServicePackageController::class, 'destroy'])->name('packages.destroy');
        
        Route::post('/categories', [CategoryController::class, 'store'])->name('categories.store');
        Route::delete('/categories/{category}', [CategoryController::class, 'destroy'])->name('categories.destroy');

    });

    // Purchase Management
    Route::middleware(['permission:purchases,view'])->group(function() {
        Route::get('/purchases', function() {
            return redirect()->route('invoices.index', ['tab' => 'purchases']);
        })->name('purchases.index');
        Route::get('/purchases/create', [PurchaseController::class, 'create'])->name('purchases.create');
        Route::post('/purchases', [PurchaseController::class, 'store'])->name('purchases.store');
        Route::get('/purchases/{purchase}', [PurchaseController::class, 'show'])->name('purchases.show');
        Route::get('/purchases/{purchase}/edit', [PurchaseController::class, 'edit'])->name('purchases.edit');
        Route::put('/purchases/{purchase}', [PurchaseController::class, 'update'])->name('purchases.update');
        Route::delete('/purchases/{purchase}', [PurchaseController::class, 'destroy'])->name('purchases.destroy');
        Route::get('/purchases/{purchase}/receive', [PurchaseController::class, 'receive'])->name('purchases.receive');
        Route::post('/purchases/{purchase}/receive', [PurchaseController::class, 'processReceive'])->name('purchases.process-receive');
    });

    // Staff Management
    Route::middleware(['permission:staff,view'])->group(function() {
        Route::get('/staff', [StaffController::class, 'index'])->name('staff.index');
        Route::get('/staff/create', [StaffController::class, 'create'])->name('staff.create')->middleware('permission:staff,create');
        Route::post('/staff', [StaffController::class, 'store'])->name('staff.store')->middleware('permission:staff,create');
        Route::get('/staff/{staff}', [StaffController::class, 'show'])->name('staff.show');
        Route::get('/staff/{staff}/edit', [StaffController::class, 'edit'])->name('staff.edit')->middleware('permission:staff,edit');
        Route::put('/staff/{staff}', [StaffController::class, 'update'])->name('staff.update')->middleware('permission:staff,edit');
        Route::delete('/staff/{staff}', [StaffController::class, 'destroy'])->name('staff.destroy')->middleware('permission:staff,delete');
        Route::get('/staff-attendance', [StaffController::class, 'attendance'])->name('staff.attendance-all')->middleware('permission:staff,attendance');
        Route::get('/staff-attendance/{staff}', [StaffController::class, 'attendance'])->name('staff.attendance')->middleware('permission:staff,attendance');
        Route::post('/staff-attendance/record', [StaffController::class, 'recordAttendance'])->name('staff.record-attendance')->middleware('permission:staff,attendance');
        Route::post('/staff/{staff}/rate', [StaffController::class, 'rate'])->name('staff.rate');
        Route::post('/staff/{staff}/shifts/inline', [StaffController::class, 'storeShiftInline'])->name('staff.shifts.store-inline');

        // Staff sub-modules
        Route::get('/staff/{staff}/shifts', [StaffController::class, 'shifts'])->name('staff.shifts');
        Route::get('/staff/{staff}/shifts/create', [StaffController::class, 'createShift'])->name('staff.create-shift');
        Route::post('/staff/{staff}/shifts', [StaffController::class, 'storeShift'])->name('staff.store-shift');
        
        Route::get('/staff/leaves/requests', [StaffController::class, 'leaveRequests'])->name('staff.leave-requests');
        Route::get('/staff/{staff}/leaves/request', [StaffController::class, 'requestLeave'])->name('staff.request-leave');
        Route::post('/staff/{staff}/leaves', [StaffController::class, 'storeLeaveRequest'])->name('staff.store-leave-request');
        Route::post('/leaves/{leave}/approve', [StaffController::class, 'approveLeave'])->name('staff.approve-leave');
        Route::post('/leaves/{leave}/reject', [StaffController::class, 'rejectLeave'])->name('staff.reject-leave');
        
        Route::get('/staff-salary-dashboard', [StaffController::class, 'salaryDashboard'])->name('staff.salary-dashboard');
        Route::get('/staff/{staff}/salary-performance', [StaffController::class, 'salaryPerformance'])->name('staff.salary-performance');
        Route::post('/staff/{staff}/pay-salary', [StaffController::class, 'paySalary'])->name('staff.pay-salary');

        // Staff HRMS Routes
        Route::get('/staff-hrms', [StaffHRMSController::class, 'index'])->name('staff.hrms');
        Route::post('/staff-hrms/attendance', [StaffHRMSController::class, 'markAttendance'])->name('staff.hrms.attendance');
        Route::post('/staff-hrms/shift', [StaffHRMSController::class, 'assignShift'])->name('staff.hrms.shift');
        Route::post('/staff-hrms/salary-update', [StaffHRMSController::class, 'updateSalary'])->name('staff.hrms.salary-update');
    });

    // Business Settings & Role Management — accessible to admin OR any role with business.view permission
    Route::middleware(['permission:business,view'])->group(function() {
        Route::get('/staff-roles', [StaffRoleController::class, 'index'])->name('staff-roles.index');
        Route::get('/business-settings', [StaffRoleController::class, 'permissions'])->name('business-settings.index');
        Route::post('/business-settings/{id}', [StaffRoleController::class, 'savePermissions'])->name('business-settings.update');
        
        // Strict Admin actions for roles
        Route::middleware(['permission:admin,all'])->group(function() {
            Route::get('/staff-roles/create', [StaffRoleController::class, 'create'])->name('staff-roles.create');
            Route::post('/staff-roles', [StaffRoleController::class, 'store'])->name('staff-roles.store');
            Route::get('/staff-roles/{id}/edit', [StaffRoleController::class, 'edit'])->name('staff-roles.edit');
            Route::put('/staff-roles/{id}', [StaffRoleController::class, 'update'])->name('staff-roles.update');
            Route::delete('/staff-roles/{id}', [StaffRoleController::class, 'destroy'])->name('staff-roles.destroy');
        });
    });

    // Appointments & Reports
    Route::middleware(['permission:reports,view'])->group(function() {
        Route::get('/reports', [ReportController::class, 'index'])->name('reports.index');
        Route::get('/reports/pos', [ReportController::class, 'posReport'])->name('reports.pos');
        Route::get('/reports/pos/export', [ReportController::class, 'exportPosReport'])->name('reports.pos.export');
        
        // Staff Reports
        Route::get('/reports/staff', [ReportController::class, 'staffReport'])->name('reports.staff');
        Route::get('/reports/staff/export', [ReportController::class, 'exportStaffReport'])->name('reports.staff.export');
        
        Route::get('/reports/attendance', [ReportController::class, 'attendanceReport'])->name('reports.attendance');
        Route::get('/reports/attendance/export', [ReportController::class, 'exportAttendanceReport'])->name('reports.attendance.export');
        
        Route::get('/reports/salary', [ReportController::class, 'salaryReport'])->name('reports.salary');
        Route::get('/reports/salary/export', [ReportController::class, 'exportSalaryReport'])->name('reports.salary.export');
    });

    Route::get('/appointments', [AppointmentController::class, 'index'])->name('appointments.index');
    Route::get('/appointments/create', [AppointmentController::class, 'create'])->name('appointments.create');
    Route::get('/appointments/calendar', [AppointmentController::class, 'calendar'])->name('appointments.calendar');
    Route::get('/appointments/events', [AppointmentController::class, 'events'])->name('appointments.events');
    Route::get('/appointments/due-now', [AppointmentController::class, 'dueNow'])->name('appointments.due-now');
    Route::post('/appointments', [AppointmentController::class, 'store'])->name('appointments.store');
    Route::get('/appointments/{appointment}/edit', [AppointmentController::class, 'edit'])->name('appointments.edit');
    Route::put('/appointments/{appointment}', [AppointmentController::class, 'update'])->name('appointments.update');
    Route::delete('/appointments/{appointment}', [AppointmentController::class, 'destroy'])->name('appointments.destroy');
    Route::patch('/appointments/{appointment}/update-status', [AppointmentController::class, 'updateStatus'])->name('appointments.update-status');
    Route::patch('/appointments/{appointment}/update-time', [AppointmentController::class, 'updateTime'])->name('appointments.update-time');

    // Promotions (Admin only)
    Route::middleware(['permission:admin,all'])->group(function() {
        Route::prefix('promotions')->name('promotions.')->group(function () {
            Route::get('/discount-rules', [PromotionController::class, 'discountRules'])->name('discount-rules');
            Route::get('/discount-rules/create', [PromotionController::class, 'createDiscountRule'])->name('discount-rules.create');
            Route::post('/discount-rules', [PromotionController::class, 'storeDiscountRule'])->name('discount-rules.store');
            Route::get('/discount-rules/{discountRule}/edit', [PromotionController::class, 'editDiscountRule'])->name('discount-rules.edit');
            Route::put('/discount-rules/{discountRule}', [PromotionController::class, 'updateDiscountRule'])->name('discount-rules.update');
            Route::delete('/discount-rules/{discountRule}', [PromotionController::class, 'destroyDiscountRule'])->name('discount-rules.destroy');
            Route::get('/coupons', [PromotionController::class, 'coupons'])->name('coupons');
            Route::get('/coupons/create', [PromotionController::class, 'createCoupon'])->name('coupons.create');
            Route::post('/coupons', [PromotionController::class, 'storeCoupon'])->name('coupons.store');
            Route::get('/coupons/{coupon}/edit', [PromotionController::class, 'editCoupon'])->name('coupons.edit');
            Route::put('/coupons/{coupon}', [PromotionController::class, 'updateCoupon'])->name('coupons.update');
            Route::delete('/coupons/{coupon}', [PromotionController::class, 'destroyCoupon'])->name('coupons.destroy');
        });

        Route::get('/whatsapp', [WhatsAppController::class, 'index'])->name('whatsapp.index');
        Route::post('/whatsapp/promotion', [WhatsAppController::class, 'sendPromotion'])->name('whatsapp.promotion');
    });
});
