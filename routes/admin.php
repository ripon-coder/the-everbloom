<?php

use App\Http\Controllers\Admin\Auth\AuthController;
use App\Http\Controllers\Admin\AttributeController;
use App\Http\Controllers\Admin\AttributeValueController;
use App\Http\Controllers\Admin\BrandController;
use App\Http\Controllers\Admin\CategoryController;
use App\Http\Controllers\Admin\ProductController;
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
        Route::resource("products",ProductController::class);
        Route::resource("brands",BrandController::class);
        Route::resource("categories",CategoryController::class);
        Route::resource("attributes",AttributeController::class);
        Route::resource("attribute-values",AttributeValueController::class);
        
        // Attribute specific routes
        Route::post('attributes/update-sort-order', [AttributeController::class, 'updateSortOrder'])->name('attributes.update-sort-order');
        Route::get('attributes/category/{categoryId}', [AttributeController::class, 'getByCategory'])->name('attributes.by-category');
        
        // Attribute value specific routes
        Route::get('attribute-values/product/{productId}', [AttributeValueController::class, 'getProductAttributes'])->name('attribute-values.product-attributes');
        Route::post('attribute-values/bulk-update/{productId}', [AttributeValueController::class, 'bulkUpdate'])->name('attribute-values.bulk-update');
        Route::get('attribute-values/unique-values/{attributeId}', [AttributeValueController::class, 'getUniqueValues'])->name('attribute-values.unique-values');
    });
});
