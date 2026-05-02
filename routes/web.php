<?php

use App\Http\Controllers\Frontend\HomeController;
use App\Http\Controllers\Frontend\ProductController;
use App\Http\Controllers\Frontend\PageController;
use Illuminate\Support\Facades\Route;

/*
|--------------------------------------------------------------------------
| Web Routes
|--------------------------------------------------------------------------
|
| Here is where you can register web routes for your application. These
| routes are loaded by the RouteServiceProvider and all of them will
| be assigned to the "web" middleware group. Make something great!
|
*/

// Home Route
Route::get('/', [HomeController::class, 'index'])->name('home');

// Shop & Product Routes
Route::get('/shop', [ProductController::class, 'index'])->name('shop');
Route::get('/product/{slug?}', [ProductController::class, 'show'])->name('product.show');

// Cart Sync Route
Route::post('/cart/sync', [\App\Http\Controllers\Frontend\CartController::class, 'sync'])->name('cart.sync');

// Page Routes
Route::get('/checkout', [PageController::class, 'checkout'])->name('checkout');
Route::get('/account', [PageController::class, 'account'])->name('account')->middleware('auth');
Route::get('/track-order', [PageController::class, 'trackOrder'])->name('track-order');
Route::get('/about', [PageController::class, 'about'])->name('about');
Route::get('/contact', [PageController::class, 'contact'])->name('contact');

// Authentication Routes
Route::middleware('guest')->group(function () {
    Route::get('/login', [PageController::class, 'login'])->name('login');
    Route::post('/login', [\App\Http\Controllers\Frontend\AuthController::class, 'postLogin'])->name('login.post');
    Route::get('/register', [PageController::class, 'register'])->name('register');
    Route::post('/register', [\App\Http\Controllers\Frontend\AuthController::class, 'postRegister'])->name('register.post');
    Route::get('/forgot-password', [PageController::class, 'forgotPassword'])->name('password.request');
    Route::get('/reset-password', [PageController::class, 'resetPassword'])->name('password.reset');
});

Route::post('/logout', [\App\Http\Controllers\Frontend\AuthController::class, 'logout'])->name('logout')->middleware('auth');

// Admin Routes
require __DIR__.'/admin.php';
