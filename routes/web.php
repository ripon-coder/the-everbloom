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
    return view('pages.product', compact('slug'));
})->name('product.show');

// Route::get('/dashboard', function () {
//     return view('dashboard');
// })->middleware(['auth', 'verified'])->name('dashboard');


require __DIR__.'/admin.php';
// require __DIR__.'/auth.php';
