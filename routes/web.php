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
Route::get('/search/live', [ProductController::class, 'liveSearch'])->name('search.live');
Route::get('/product/{slug?}', [ProductController::class, 'show'])->name('product.show');
Route::post('/product/review', [\App\Http\Controllers\Frontend\ProductReviewController::class, 'store'])->name('product.review.store')->middleware(['auth', 'throttle:3,1']);

// Cart Routes
Route::get('/cart', [\App\Http\Controllers\Frontend\CartController::class, 'index'])->name('cart');
Route::post('/cart/sync', [\App\Http\Controllers\Frontend\CartController::class, 'sync'])->name('cart.sync');

// Wishlist Routes
Route::post('/wishlist/toggle', [\App\Http\Controllers\Frontend\WishlistController::class, 'toggle'])->name('wishlist.toggle')->middleware('auth');
Route::get('/wishlist/ids', [\App\Http\Controllers\Frontend\WishlistController::class, 'getWishlist'])->name('wishlist.ids')->middleware('auth');

// Page Routes
Route::get('/checkout', [PageController::class, 'checkout'])->name('checkout');
Route::post('/checkout/calculate', [\App\Http\Controllers\Frontend\CheckoutController::class, 'calculate'])->name('checkout.calculate');
Route::post('/checkout/place-order', [\App\Http\Controllers\Frontend\CheckoutController::class, 'placeOrder'])->name('checkout.place-order');
Route::get('/order-received/{order_number}', [PageController::class, 'orderReceived'])->name('order.received');
Route::get('/account/{section?}', [PageController::class, 'account'])->name('account')->middleware('auth');
Route::post('/account/details', [\App\Http\Controllers\Frontend\AuthController::class, 'updateDetails'])->name('account.details.update')->middleware('auth');
Route::get('/account/order/{order_number}', [PageController::class, 'orderShow'])->name('account.order.show')->middleware('auth');
Route::post('/account/addresses', [\App\Http\Controllers\Frontend\UserAddressController::class, 'store'])->name('account.addresses.store')->middleware('auth');
Route::put('/account/addresses/{id}', [\App\Http\Controllers\Frontend\UserAddressController::class, 'update'])->name('account.addresses.update')->middleware('auth');
Route::delete('/account/addresses/{id}', [\App\Http\Controllers\Frontend\UserAddressController::class, 'destroy'])->name('account.addresses.destroy')->middleware('auth');
Route::get('/track-order', [PageController::class, 'trackOrder'])->name('track-order');
Route::get('/about', [PageController::class, 'about'])->name('about');
Route::get('/contact', [PageController::class, 'contact'])->name('contact');
Route::post('/contact', [PageController::class, 'submitContact'])->name('contact.submit')->middleware('throttle:3,1');

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

// Dynamic Pages (Catch-all) - Must be last
Route::get('/{slug}', [PageController::class, 'dynamicPage'])->name('page.show');
