<?php

use App\Http\Controllers\Api\BrandControllerApi;
use App\Http\Controllers\Api\CategoryControllerApi;
use App\Http\Controllers\Api\CheckoutApiController;
use App\Http\Controllers\Api\FlashSaleControllerApi;
use App\Http\Controllers\Api\OrderApiController;
use App\Http\Controllers\Api\ProductControllerApi;
use App\Http\Controllers\Api\SaveAddressApiController;
use App\Http\Controllers\Api\UserApiController;
use App\Http\Controllers\Api\WishlistApiController;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;

Route::prefix("v1")->group(function () {

    // User Login
    Route::post('login', [UserApiController::class, 'Login']);

    Route::get("parent-category", [CategoryControllerApi::class, 'ParentCategory']);
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

    // Flash Sale Products
    Route::post('flash-sale-products', [FlashSaleControllerApi::class, 'FlashSaleProducts']);
});

Route::middleware('auth:sanctum')->prefix("v1")->group(function () {

    // User routes will be here
    Route::get('user', [UserApiController::class, 'GetUser']);
    Route::post('update-user', [UserApiController::class, 'UpdateUser']);
    Route::post('logout', [UserApiController::class, 'Logout']);

    // Save Address routes will be here
    Route::get('get-address', [SaveAddressApiController::class, 'GetAddress']);
    Route::post('save-address', [SaveAddressApiController::class, 'SaveAddress']);
    Route::delete('delete-address', [SaveAddressApiController::class, 'DeleteAddress']);

    // Checkout routes will be here
    Route::post('checkout-calculate', [CheckoutApiController::class, 'Calculate']);

    // Order routes will be here
    Route::post('create-order', [OrderApiController::class, 'CreateOrder']);
    Route::post('get-orders', [OrderApiController::class, 'GetOrder']);
    Route::post('get-order-details', [OrderApiController::class, 'GetOrderDetails']);

    // Wishlist routes will be here
    Route::post('add-wishlist', [WishlistApiController::class, 'AddWishlist']);
    Route::delete('delete-wishlist', [WishlistApiController::class, 'DeleteWishlist']);
    Route::post('get-wishlist', [WishlistApiController::class, 'GetWishlist']);

    // Changed Password routes will be here
    Route::post('change-password', [UserApiController::class, 'ChangePassword']);

    // Forgot Password routes will be here
    Route::post('forgot-password', [UserApiController::class, 'ForgotPassword']);
    Route::post('reset-password', [UserApiController::class, 'ResetPassword']);
});
