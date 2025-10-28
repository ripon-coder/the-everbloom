<?php

use App\Http\Controllers\Api\BrandControllerApi;
use App\Http\Controllers\Api\CategoryControllerApi;
use App\Http\Controllers\Api\CheckoutApiController;
use App\Http\Controllers\Api\OrderApiController;
use App\Http\Controllers\Api\ProductControllerApi;
use App\Http\Controllers\Api\SaveAddressApiController;
use App\Http\Controllers\Api\UserApiController;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;

Route::prefix("v1")->group(function () {

    // User Login
    Route::post('login', [UserApiController::class, 'Login']);

    //Route::get("parent-category",[CategoryControllerApi::class,'ParentCategory']);
    //Route::get("all-category",[CategoryControllerApi::class,'AllCategory']);
    Route::get('shop-products', [ProductControllerApi::class, 'ShopProducts']);
    Route::get('shop-category-brand', [ProductControllerApi::class, 'ShopCategoryBrand']);
    Route::get('shop-attribute', [ProductControllerApi::class, 'ShopAttribute']);
    //Route::get('all-brands',[BrandControllerApi::class,'AllBrand']);
    // Single Product
    Route::get('product', [ProductControllerApi::class, 'Product']);
    // variant
    Route::get('variant', [ProductControllerApi::class, 'Variant']);
    Route::post('variants', [ProductControllerApi::class, 'Variants']);

    // will be auth section
    Route::get('district-list', [CheckoutApiController::class, 'DistrictList']);

    // Order routes will be here
    Route::post('create-order', [OrderApiController::class, 'CreateOrder']);
});

Route::middleware('auth:sanctum')->prefix("v1")->group(function () {
    // Save Address routes will be here
    Route::get('get-address', [SaveAddressApiController::class, 'GetAddress']);
    Route::post('save-address', [SaveAddressApiController::class, 'SaveAddress']);
    Route::delete('delete-address', [SaveAddressApiController::class, 'DeleteAddress']);

    // Checkout routes will be here
    Route::post('checkout-calculate', [CheckoutApiController::class, 'Calculate']);
});
