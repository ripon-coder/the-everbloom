<?php

use App\Http\Controllers\Admin\Auth\AuthController;
use App\Http\Controllers\Admin\AttributeController;
use App\Http\Controllers\Admin\AttributeValueController;
use App\Http\Controllers\Admin\BrandController;
use App\Http\Controllers\Admin\CategoryController;
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
        
        // Product Variants
        Route::resource("product-variants", ProductVariantController::class);
        Route::post('product-variants/{product_variant}/restore', [ProductVariantController::class, 'restore'])->name('product-variants.restore');
        Route::delete('product-variants/{product_variant}/force-delete', [ProductVariantController::class, 'forceDelete'])->name('product-variants.force-delete');
        
        
        // Variant Attributes
        Route::resource("variant-attributes", VariantAttributeController::class);
        
        // Other Resources
        Route::resource("brands", BrandController::class);
        Route::resource("categories", CategoryController::class);
        Route::resource("attributes", AttributeController::class);
        Route::get('attributes/{attribute}/values', [AttributeController::class, 'getValues'])->name('attributes.values');
        Route::resource("attribute-values", AttributeValueController::class);
    });
});
