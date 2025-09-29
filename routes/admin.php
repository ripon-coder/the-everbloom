<?php

use App\Http\Controllers\Admin\Auth\AuthController;
use App\Http\Controllers\Admin\AttributeController;
use App\Http\Controllers\Admin\AttributeValueController;
use App\Http\Controllers\Admin\BrandController;
use App\Http\Controllers\Admin\CategoryController;
use App\Http\Controllers\Admin\CouponController;
use App\Http\Controllers\Admin\FlashSaleController;
use App\Http\Controllers\Admin\ProductController;
use App\Http\Controllers\Admin\ProductVariantController;
use App\Http\Controllers\Admin\ProductImageController;
use App\Http\Controllers\Admin\ProductVariantImageController;
use App\Http\Controllers\Admin\VariantAttributeController;
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
        
        
        // Other Resources
        Route::resource("brands", BrandController::class);
        Route::resource("categories", CategoryController::class);
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
    });
});
