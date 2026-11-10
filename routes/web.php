<?php

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
    $related = collect(config('demo.products'))
        ->where('category_slug', $product['category_slug'] ?? '')
        ->where('slug', '!=', $slug)
        ->take(4)
        ->values()
        ->all();

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

// Account Authentication & Dashboard (Demo)
Route::get('/account/login', function () {
    // BACKEND: replace with Laravel Fortify / Breeze / Sanctum auth
    return view('frontend.account.login');
})->name('login');

Route::get('/account/register', function () {
    // BACKEND: replace with user registration logic
    return view('frontend.account.register');
})->name('register');

Route::get('/account', function () {
    // BACKEND: replace with auth middleware and User model
    return view('frontend.account.index');
})->name('account');

Route::get('/account/orders', function () {
    // BACKEND: replace with $user->orders()->latest()->paginate()
    return view('frontend.account.orders');
})->name('account.orders');

Route::get('/account/orders/{id}', function ($id) {
    // BACKEND: replace with Order::findOrFail($id)
    return view('frontend.account.order-details', ['id' => $id]);
})->name('account.order.details');
