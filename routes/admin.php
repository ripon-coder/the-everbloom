<?php

use App\Http\Controllers\Admin\AdminAttributeController;
use App\Http\Controllers\Admin\AdminAuthController;
use App\Http\Controllers\Admin\AdminBrandController;
use App\Http\Controllers\Admin\AdminCategoryController;
use App\Http\Controllers\Admin\AdminCouponController;
use App\Http\Controllers\Admin\AdminCustomerController;
use App\Http\Controllers\Admin\AdminDashboardController;
use App\Http\Controllers\Admin\AdminFinancialController;
use App\Http\Controllers\Admin\AdminInventoryController;
use App\Http\Controllers\Admin\AdminInvoiceController;
use App\Http\Controllers\Admin\AdminLocationController;
use App\Http\Controllers\Admin\AdminOrderController;
use App\Http\Controllers\Admin\AdminProductController;
use App\Http\Controllers\Admin\AdminPurchaseController;
use App\Http\Controllers\Admin\AdminReviewController;
use App\Http\Controllers\Admin\AdminSettingController;
use Illuminate\Support\Facades\Route;

/*
|--------------------------------------------------------------------------
| Admin Console Routes — The Everbloom Management
|--------------------------------------------------------------------------
| Powered by Larkon Bootstrap 5.3.3 Theme (Isolated from Storefront Tailwind)
*/

// Admin Authentication
Route::get('/admin/login', [AdminAuthController::class, 'showLogin'])->name('admin.login');
Route::post('/admin/login', [AdminAuthController::class, 'login'])->middleware('throttle:login')->name('admin.login.submit');
Route::post('/admin/logout', [AdminAuthController::class, 'logout'])->name('admin.logout');

// Authenticated Admin Console Area
Route::prefix('admin')->name('admin.')->middleware(['auth:admin', 'active.account:admin', 'admin.log'])->group(function () {

    // Dashboard
    Route::get('/', [AdminDashboardController::class, 'index'])->name('dashboard');

    // Financial Reports & Ledger
    Route::get('financial', [AdminFinancialController::class, 'index'])->name('financial.index');

    // Products Management
    Route::post('products/bulk', [AdminProductController::class, 'bulkAction'])->name('products.bulk');
    Route::resource('products', AdminProductController::class)->except('show');

    // Product Attributes (Brand, Size, Color, etc.)
    Route::get('attributes', [AdminAttributeController::class, 'index'])->name('attributes.index');
    Route::post('attributes', [AdminAttributeController::class, 'store'])->name('attributes.store');
    Route::put('attributes/{attribute}', [AdminAttributeController::class, 'update'])->name('attributes.update');
    Route::post('attributes/{attribute}/values', [AdminAttributeController::class, 'storeValue'])->name('attributes.values.store');
    Route::delete('attributes/values/{value}', [AdminAttributeController::class, 'destroyValue'])->name('attributes.values.destroy');

    // Categories Management
    Route::resource('categories', AdminCategoryController::class)->except('show');

    // Brands Management
    Route::resource('brands', AdminBrandController::class)->except('show');

    // Orders Management
    Route::get('orders', [AdminOrderController::class, 'index'])->name('orders.index');
    Route::get('orders/{id}', [AdminOrderController::class, 'show'])->name('orders.show');
    Route::post('orders/{id}/status', [AdminOrderController::class, 'updateStatus'])->name('orders.updateStatus');

    // Inventory & Batch Tracking
    Route::get('inventory', [AdminInventoryController::class, 'index'])->name('inventory.index');
    Route::post('inventory/{product}/adjust', [AdminInventoryController::class, 'updateStock'])->name('inventory.adjust');
    Route::post('inventory/variants/{variant}/adjust', [AdminInventoryController::class, 'updateVariantStock'])->name('inventory.adjustVariant');

    // Wholesale Purchases
    Route::get('purchases', [AdminPurchaseController::class, 'index'])->name('purchases.index');
    Route::get('purchases/create', [AdminPurchaseController::class, 'create'])->name('purchases.create');
    Route::post('purchases', [AdminPurchaseController::class, 'store'])->name('purchases.store');

    // Invoices
    Route::get('invoices', [AdminInvoiceController::class, 'index'])->name('invoices.index');
    Route::get('invoices/{id}', [AdminInvoiceController::class, 'show'])->name('invoices.show');

    // Customers
    Route::get('customers', [AdminCustomerController::class, 'index'])->name('customers.index');
    Route::get('customers/export', [AdminCustomerController::class, 'export'])->name('customers.export');
    Route::get('customers/{id}', [AdminCustomerController::class, 'show'])->name('customers.show');
    Route::post('customers/{id}/toggle-status', [AdminCustomerController::class, 'toggleStatus'])->name('customers.toggleStatus');

    // Coupons & Promotions
    Route::resource('coupons', AdminCouponController::class)->only(['index', 'create', 'store', 'destroy']);

    // Customer Reviews Moderation
    Route::get('reviews', [AdminReviewController::class, 'index'])->name('reviews.index');
    Route::post('reviews/{id}/toggle', [AdminReviewController::class, 'toggleStatus'])->name('reviews.toggleStatus');

    // Store Settings
    Route::get('settings', [AdminSettingController::class, 'index'])->name('settings.index');
    Route::post('settings', [AdminSettingController::class, 'update'])->name('settings.update');

    // Bangladesh Locations Management (District -> Thana -> Area)
    Route::get('locations', [AdminLocationController::class, 'index'])->name('locations.index');
    Route::post('locations/districts', [AdminLocationController::class, 'storeDistrict'])->name('locations.districts.store');
    Route::put('locations/districts/{district}', [AdminLocationController::class, 'updateDistrict'])->name('locations.districts.update');
    Route::post('locations/districts/{district}/toggle', [AdminLocationController::class, 'toggleDistrict'])->name('locations.districts.toggle');
    Route::post('locations/thanas', [AdminLocationController::class, 'storeThana'])->name('locations.thanas.store');
    Route::put('locations/thanas/{thana}', [AdminLocationController::class, 'updateThana'])->name('locations.thanas.update');
    Route::post('locations/thanas/{thana}/toggle', [AdminLocationController::class, 'toggleThana'])->name('locations.thanas.toggle');
    Route::post('locations/areas', [AdminLocationController::class, 'storeArea'])->name('locations.areas.store');
    Route::put('locations/areas/{area}', [AdminLocationController::class, 'updateArea'])->name('locations.areas.update');
    Route::post('locations/areas/{area}/toggle', [AdminLocationController::class, 'toggleArea'])->name('locations.areas.toggle');
});
