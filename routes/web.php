<?php

use App\Http\Controllers\AccountController;
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
Route::get('/', function () {
    // BACKEND: replace with database product/content queries
    $featured = array_filter(config('demo.products'), fn($p) => $p['is_featured'] ?? false);
    return view('frontend.home', [
        'featured' => $featured,
        'categories' => config('demo.categories'),
        'testimonials' => config('demo.testimonials'),
    ]);
})->name('home');

// Living Styleguide & Design Tokens
Route::get('/styleguide', function () {
    return view('frontend.styleguide');
})->name('styleguide');

// Shop Catalogue
Route::get('/shop', function () {
    // BACKEND: replace with paginate() and search query filters
    return view('frontend.shop.index', [
        'products' => config('demo.products'),
        'categories' => config('demo.categories'),
    ]);
})->name('shop.index');

// Shop by Category
Route::get('/shop/category/{slug}', function ($slug) {
    // BACKEND: replace with Category::where('slug', $slug)->firstOrFail()
    $category = collect(config('demo.categories'))->firstWhere('slug', $slug);
    if (!$category) {
        abort(404, 'Category not found');
    }
    $products = collect(config('demo.products'))->where('category_slug', $slug)->values()->all();

    return view('frontend.shop.category', [
        'category' => $category,
        'products' => $products,
    ]);
})->name('shop.category');

// Product Detail Page
Route::get('/shop/product/{slug}', function ($slug) {
    // BACKEND: replace with Product::where('slug', $slug)->firstOrFail()
    $product = collect(config('demo.products'))->firstWhere('slug', $slug);
    if (!$product) {
        abort(404, 'Product not found');
    }
    $sameCat = collect(config('demo.products'))
        ->where('category_slug', $product['category_slug'] ?? '')
        ->where('slug', '!=', $slug)
        ->values();

    if ($sameCat->count() < 4) {
        $otherCat = collect(config('demo.products'))
            ->where('category_slug', '!=', $product['category_slug'] ?? '')
            ->where('slug', '!=', $slug)
            ->values();
        $related = $sameCat->concat($otherCat)->take(4)->all();
    } else {
        $related = $sameCat->take(4)->all();
    }

    return view('frontend.shop.product', [
        'product' => $product,
        'related' => $related,
    ]);
})->name('shop.product');

// Shopping Bag
Route::get('/cart', function () {
    return view('frontend.cart.index');
})->name('cart.index');

// Checkout Demonstration (No payment processed)
Route::get('/checkout', function () {
    return view('frontend.checkout.index');
})->name('checkout.index');

// Static / Editorial Pages
Route::get('/about', function () {
    return view('frontend.pages.about');
})->name('about');

Route::get('/contact', function () {
    // BACKEND: handle contact inquiry form submission
    return view('frontend.pages.contact');
})->name('contact');

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

// Account Authentication & Customer Area (Demo)
// BACKEND: replace with auth middleware, Fortify / Breeze / Sanctum auth controllers
Route::get('/account/login', [AccountController::class, 'login'])->name('login');
Route::get('/account/register', [AccountController::class, 'register'])->name('register');
Route::get('/account', [AccountController::class, 'index'])->name('account');
Route::get('/account/orders', [AccountController::class, 'orders'])->name('account.orders');
Route::get('/account/orders/{id}', [AccountController::class, 'showOrder'])->name('account.order.details');
Route::get('/account/addresses', [AccountController::class, 'addresses'])->name('account.addresses');
Route::get('/account/profile', [AccountController::class, 'profile'])->name('account.profile');
