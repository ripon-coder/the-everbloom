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

// Route::get('/dashboard', function () {
//     return view('dashboard');
// })->middleware(['auth', 'verified'])->name('dashboard');


require __DIR__.'/admin.php';
// require __DIR__.'/auth.php';
