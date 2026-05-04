<?php

use App\Http\Controllers\Admin\Auth\AuthController;
use App\Http\Controllers\Admin\AttributeController;
use App\Http\Controllers\Admin\AttributeValueController;
use App\Http\Controllers\Admin\BrandController;
use App\Http\Controllers\Admin\CategoryController;
use App\Http\Controllers\Admin\CouponController;
use App\Http\Controllers\Admin\DistrictController;
use App\Http\Controllers\Admin\FlashSaleController;
use App\Http\Controllers\Admin\OrderController;
use App\Http\Controllers\Admin\ProductController;
use App\Http\Controllers\Admin\SiteSettingController;
use App\Http\Controllers\Admin\SliderController;
use Illuminate\Support\Facades\Route;

Route::prefix('admin')->as("admin.")->group(function () {
    // Guest routes (not authenticated)
    Route::middleware('guest:admin')->group(function () {
        Route::get('/login', [AuthController::class, 'showLoginForm'])->name('login');
        Route::post('/login', [AuthController::class, 'login'])->name('login.post');
    });

    // Authenticated routes
    Route::middleware(['admin'])->group(function () {
        Route::post('/logout', [AuthController::class, 'logout'])->name('logout');
        Route::get('/dashboard', [AuthController::class, 'dashboard'])->name('dashboard');

        // Product Management
        Route::resource("products", ProductController::class);
        Route::post('products/{product}/restore', [ProductController::class, 'restore'])->name('products.restore');
        Route::delete('products/{product}/force-delete', [ProductController::class, 'forceDelete'])->name('products.force-delete');
        Route::post('products/{id}/quick-update', [ProductController::class, 'quickUpdate'])->name('products.quick-update');

        // Variants Management
        Route::get('variants', [\App\Http\Controllers\Admin\ProductVariantController::class, 'index'])->name('variants.index');
        Route::get('variants/{variant}/edit', [\App\Http\Controllers\Admin\ProductVariantController::class, 'edit'])->name('variants.edit');
        Route::put('variants/{variant}', [\App\Http\Controllers\Admin\ProductVariantController::class, 'update'])->name('variants.update');
        Route::post('variants/{variant}/status', [\App\Http\Controllers\Admin\ProductVariantController::class, 'updateStatus'])->name('variants.update-status');
        Route::post('variants/{variant}/stock', [\App\Http\Controllers\Admin\ProductVariantController::class, 'updateStock'])->name('variants.update-stock');
        Route::delete('variants/{variant}', [\App\Http\Controllers\Admin\ProductVariantController::class, 'destroy'])->name('variants.destroy');
        // Other Resources
        Route::resource("brands", BrandController::class);
        Route::resource("categories", CategoryController::class);
        Route::post('categories/{category}/toggle-featured', [CategoryController::class, 'toggleFeatured'])->name('categories.toggle-featured');
        Route::resource("attributes", AttributeController::class);
        Route::get('attributes/{attribute}/values', [AttributeController::class, 'getValues'])->name('attributes.values');
        Route::resource("attribute-values", AttributeValueController::class);

        // Coupon Management
        Route::resource("coupons", CouponController::class);
        Route::post('coupons/{coupon}/restore', [CouponController::class, 'restore'])->name('coupons.restore');
        Route::delete('coupons/{coupon}/force-delete', [CouponController::class, 'forceDelete'])->name('coupons.force-delete');

        // Flash Sale Management
        Route::resource("flash-sales", FlashSaleController::class)->parameters(['flash-sales' => 'flashSale']);
        Route::post('flash-sales/{flashSale}/restore', [FlashSaleController::class, 'restore'])->name('flash-sales.restore');
        Route::delete('flash-sales/{flashSale}/force-delete', [FlashSaleController::class, 'forceDelete'])->name('flash-sales.force-delete');

        // Order Management
        Route::resource("orders", OrderController::class)->except(['create', 'store', 'edit', 'update']);
        Route::post('orders/{order}/update-status', [OrderController::class, 'updateStatus'])->name('orders.update-status');
        Route::post('orders/{order}/update-payment-status', [OrderController::class, 'updatePaymentStatus'])->name('orders.update-payment-status');

        // Customer Management
        Route::resource("customers", \App\Http\Controllers\Admin\CustomerController::class)->only(['index', 'show', 'destroy']);
        Route::post('customers/{customer}/update-status', [\App\Http\Controllers\Admin\CustomerController::class, 'updateStatus'])->name('customers.update-status');

        // District Management
        Route::resource("district", DistrictController::class);

        // Review Management
        Route::get('reviews', [\App\Http\Controllers\Admin\ProductReviewController::class, 'index'])->name('reviews.index');
        Route::post('reviews/{review}/toggle-approval', [\App\Http\Controllers\Admin\ProductReviewController::class, 'toggleApproval'])->name('reviews.toggle-approval');
        Route::delete('reviews/{review}', [\App\Http\Controllers\Admin\ProductReviewController::class, 'destroy'])->name('reviews.destroy');

        // Site Settings
        Route::get('site-settings', [SiteSettingController::class, 'index'])->name('site-settings.index');
        Route::post('site-settings', [SiteSettingController::class, 'update'])->name('site-settings.update');

        // Page Management
        Route::resource("pages", \App\Http\Controllers\Admin\PageController::class);
        Route::post('pages/{page}/toggle-status', [\App\Http\Controllers\Admin\PageController::class, 'toggleStatus'])->name('pages.toggle-status');

        // Contact Management
        Route::resource("contacts", \App\Http\Controllers\Admin\ContactController::class)->only(['index', 'show', 'destroy']);

        // Menu Management
        Route::resource("menus", \App\Http\Controllers\Admin\MenuController::class);
        Route::post('menus/{menu}/toggle-status', [\App\Http\Controllers\Admin\MenuController::class, 'toggleStatus'])->name('menus.toggle-status');

        // Hero Slider Management
        Route::resource("sliders", SliderController::class);
        Route::post('sliders/{slider}/toggle-status', [SliderController::class, 'toggleStatus'])->name('sliders.toggle-status');

        // Profile Management
        Route::get('profile', [\App\Http\Controllers\Admin\ProfileController::class, 'edit'])->name('profile.edit');
        Route::put('profile', [\App\Http\Controllers\Admin\ProfileController::class, 'update'])->name('profile.update');
    });
});