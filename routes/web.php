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

// Page Routes
Route::get('/checkout', [PageController::class, 'checkout'])->name('checkout');
Route::get('/account', [PageController::class, 'account'])->name('account');
Route::get('/track-order', [PageController::class, 'trackOrder'])->name('track-order');
Route::get('/about', [PageController::class, 'about'])->name('about');
Route::get('/contact', [PageController::class, 'contact'])->name('contact');

// Authentication View Routes
Route::get('/login', [PageController::class, 'login'])->name('login');
Route::get('/register', [PageController::class, 'register'])->name('register');
Route::get('/forgot-password', [PageController::class, 'forgotPassword'])->name('password.request');
Route::get('/reset-password', [PageController::class, 'resetPassword'])->name('password.reset');

// Admin Routes
require __DIR__.'/admin.php';
