<?php
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\Admin\LoginController;
use App\Http\Controllers\Admin\DashboardController;
use App\Http\Middleware\RedirectIfNotAuthenticated;

Route::middleware('guest:admin')->group(function () {
    Route::get("admin/login", [LoginController::class, "index"])->name('dashboard.login');
    Route::post("admin/login", [LoginController::class, "logedIn"])->name('dashboard.logedIn');
});
Route::middleware(RedirectIfNotAuthenticated::class)->prefix('admin')->as('admin.')->group(function () {
    Route::get('dashboard', [DashboardController::class, 'index'])->name(name: 'dashboard');
});