<?php

use App\Http\Controllers\ProfileController;
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

Route::get('/', function () {
    $featuredProducts = \App\Models\Product::active()
        ->where('is_featured', true)
        ->with(['firstImage'])
        ->latest()
        ->take(6)
        ->get();

    $bestSellingProducts = \App\Models\Product::active()
        ->popular()
        ->with(['firstImage'])
        ->take(12)
        ->get();

    $newArrivals = \App\Models\Product::active()
        ->latest()
        ->with(['firstImage'])
        ->take(6)
        ->get();

    $campaignProducts = \App\Models\Product::active()
        ->inRandomOrder()
        ->with(['firstImage'])
        ->take(3)
        ->get();

    return view('pages.home', compact('featuredProducts', 'bestSellingProducts', 'newArrivals', 'campaignProducts'));
})->name('home');

Route::get('/shop', function (\Illuminate\Http\Request $request) {
    $query = \App\Models\Product::active()->with(['firstImage']);

    if ($request->has('category')) {
        $query->whereHas('category', function($q) use ($request) {
            $q->where('slug', $request->category);
        });
    }

    if ($request->has('search')) {
        $query->where('name', 'like', '%' . $request->search . '%');
    }

    $sort = $request->get('sort', 'latest');
    if ($sort === 'price_asc') {
        $query->orderBy('price', 'asc');
    } elseif ($sort === 'price_desc') {
        $query->orderBy('price', 'desc');
    } elseif ($sort === 'popular') {
        $query->popular();
    } else {
        $query->latest();
    }

    $products = $query->paginate(24)->withQueryString();
    
    $categories = \App\Models\Category::active()->get();

    return view('pages.shop', compact('products', 'categories'));
})->name('shop');

Route::get('/product/{slug?}', function ($slug = null) {
    if (!$slug) return redirect()->route('home');

    $product = \App\Models\Product::where('slug', $slug)
        ->active()
        ->with(['images', 'variants', 'firstImage', 'category'])
        ->firstOrFail();
    
    $relatedProducts = \App\Models\Product::active()
        ->where('category_id', $product->category_id)
        ->where('id', '!=', $product->id)
        ->with(['firstImage'])
        ->take(6)
        ->get();

    if ($relatedProducts->isEmpty()) {
        $relatedProducts = \App\Models\Product::active()
            ->where('id', '!=', $product->id)
            ->with(['firstImage'])
            ->inRandomOrder()
            ->take(6)
            ->get();
    }

    return view('pages.product', compact('product', 'relatedProducts'));
})->name('product.show');

Route::get('/checkout', function () {
    return view('pages.checkout');
})->name('checkout');

Route::get('/account', function () {
    return view('pages.account');
})->name('account');

Route::get('/login', function () {
    return view('pages.login');
})->name('login');

Route::get('/register', function () {
    return view('pages.register');
})->name('register');

Route::get('/forgot-password', function () {
    return view('pages.forgot-password');
})->name('password.request');

Route::get('/reset-password', function () {
    return view('pages.reset-password');
})->name('password.reset');

Route::get('/track-order', function () {
    return view('pages.track-order');
})->name('track-order');

Route::get('/about', function () {
    return view('pages.about');
})->name('about');

Route::get('/contact', function () {
    return view('pages.contact');
})->name('contact');

// Route::get('/dashboard', function () {
//     return view('dashboard');
// })->middleware(['auth', 'verified'])->name('dashboard');


require __DIR__.'/admin.php';
// require __DIR__.'/auth.php';
