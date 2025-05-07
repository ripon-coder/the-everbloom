<?php

use App\Http\Controllers\Admin\BrandController;
use App\Http\Controllers\Admin\CategoryController;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\Admin\LoginController;
use App\Http\Middleware\RedirectIfAuthenticated;
use App\Http\Controllers\Admin\DashboardController;
use App\Http\Controllers\Admin\ProductController;
use App\Http\Middleware\RedirectIfNotAuthenticated;

Route::middleware(RedirectIfAuthenticated::class . ":admin")->group(function () {
    Route::get("admin/login", [LoginController::class, "index"])->name('dashboard.login');
    Route::post("admin/login", [LoginController::class, "logedIn"])->name('dashboard.logedIn');
});

Route::middleware([RedirectIfNotAuthenticated::class . ":admin", 'throttle:global'])->prefix("admin")->as('admin.')->group(function () {
    Route::post('logout', [LoginController::class, 'logOut'])->name("logout");
    // Dashboard Controller
    Route::controller(DashboardController::class)->group(function () {
        Route::get('dashboard', action: 'index')->name(name: 'dashboard');
    });
    // Product Controller
    Route::resource('product', ProductController::class);

    // Brand Controller
    Route::resource('brand', BrandController::class);

    // Category Controller
    Route::resource('category', CategoryController::class);
});

