<?php

use App\Http\Controllers\AccountController;
use App\Http\Controllers\CheckoutController;
use App\Http\Controllers\ContactController;
use App\Http\Controllers\HomeController;
use App\Http\Controllers\LocationController;
use App\Http\Controllers\NewsletterController;
use App\Http\Controllers\ReviewController;
use App\Http\Controllers\SearchController;
use App\Http\Controllers\ShopController;
use Illuminate\Support\Facades\Route;

/*
|--------------------------------------------------------------------------
| Web Routes — The Everbloom Frontend Architecture
|--------------------------------------------------------------------------
| View-only presentation layer driven by config/demo/ data.
| Real models, DB queries, and payment processing are decoupled.
| Integration points are explicitly annotated with // BACKEND:
*/

// Homepage
Route::get('/', [HomeController::class, 'index'])->name('home');

// Instant Search (TNTSearch)
Route::get('/search', [SearchController::class, 'index'])->name('search');

// Living Styleguide & Design Tokens
Route::get('/styleguide', function () {
    return view('frontend.styleguide');
})->name('styleguide');

// Shop Catalogue
Route::get('/shop', [ShopController::class, 'index'])->name('shop.index');

// Shop by Category
Route::get('/shop/category/{slug}', [ShopController::class, 'category'])->name('shop.category');

// Product Detail Page
Route::get('/shop/product/{slug}', [ShopController::class, 'product'])->name('shop.product');

// Shopping Bag
Route::get('/cart', function () {
    return view('frontend.cart.index');
})->name('cart.index');

// Bangladesh Cascading Locations API
Route::get('/locations/districts', [LocationController::class, 'districts'])->name('locations.districts');
Route::get('/locations/districts/{district}/thanas', [LocationController::class, 'thanas'])->name('locations.thanas');
Route::get('/locations/thanas/{thana}/areas', [LocationController::class, 'areas'])->name('locations.areas');

// Checkout & Order Placement
Route::get('/checkout', [CheckoutController::class, 'index'])->name('checkout.index');
Route::post('/checkout', [CheckoutController::class, 'store'])->name('checkout.store');
Route::post('/checkout/validate-coupon', [CheckoutController::class, 'validateCoupon'])->name('checkout.validateCoupon');

// Static / Editorial Pages
Route::get('/about', function () {
    return view('frontend.pages.about');
})->name('about');

Route::get('/contact', function () {
    return view('frontend.pages.contact');
})->name('contact');
Route::post('/contact', [ContactController::class, 'store'])->name('contact.store');
Route::post('/newsletter', [NewsletterController::class, 'store'])->name('newsletter.store');
Route::post('/shop/product/{slug}/reviews', [ReviewController::class, 'store'])->name('shop.product.reviews.store');

Route::get('/faq', function () {
    return view('frontend.pages.faq');
})->name('faq');

Route::get('/wishlist', function () {
    return view('frontend.pages.wishlist');
})->name('wishlist');

Route::get('/privacy-policy', function () {
    return view('frontend.pages.privacy-policy');
})->name('privacy');

Route::get('/terms-and-conditions', function () {
    return view('frontend.pages.terms-and-conditions');
})->name('terms');

Route::get('/shipping-and-returns', function () {
    return view('frontend.pages.shipping-and-returns');
})->name('shipping');

// Account Authentication & Customer Area
Route::middleware('guest:web')->group(function () {
    Route::get('/account/login', [AccountController::class, 'login'])->name('login');
    Route::post('/account/login', [AccountController::class, 'loginSubmit'])->name('login.submit');
    Route::get('/account/register', [AccountController::class, 'register'])->name('register');
    Route::post('/account/register', [AccountController::class, 'registerSubmit'])->name('register.submit');
    Route::get('/account/forgot-password', [AccountController::class, 'forgotPassword'])->name('password.request');
    Route::post('/account/forgot-password', [AccountController::class, 'forgotPasswordSubmit'])->name('password.email');
});

Route::middleware('auth:web')->group(function () {
    Route::get('/account', [AccountController::class, 'index'])->name('account');
    Route::get('/account/orders', [AccountController::class, 'orders'])->name('account.orders');
    Route::get('/account/orders/{id}', [AccountController::class, 'showOrder'])->name('account.order.details');
    Route::get('/account/addresses', [AccountController::class, 'addresses'])->name('account.addresses');
    Route::post('/account/addresses', [AccountController::class, 'storeAddress'])->name('account.addresses.store');
    Route::put('/account/addresses/{address}', [AccountController::class, 'updateAddress'])->name('account.addresses.update');
    Route::patch('/account/addresses/{address}/default', [AccountController::class, 'setDefaultAddress'])->name('account.addresses.default');
    Route::delete('/account/addresses/{address}', [AccountController::class, 'destroyAddress'])->name('account.addresses.destroy');
    Route::get('/account/profile', [AccountController::class, 'profile'])->name('account.profile');
    Route::put('/account/profile', [AccountController::class, 'updateProfile'])->name('account.profile.update');
    Route::put('/account/password', [AccountController::class, 'updatePassword'])->name('account.password.update');
    Route::delete('/account', [AccountController::class, 'destroyAccount'])->name('account.destroy');
    Route::post('/account/logout', [AccountController::class, 'logout'])->name('logout');
});

