<?php

use App\Http\Controllers\Api\BrandControllerApi;
use App\Http\Controllers\Api\CategoryControllerApi;
use App\Http\Controllers\Api\ProductControllerApi;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;

Route::prefix("v1")->group(function(){
    Route::get("parent-category",[CategoryControllerApi::class,'ParentCategory']);
    Route::get("all-category",[CategoryControllerApi::class,'AllCategory']);
    Route::get('shop-products',[ProductControllerApi::class,'ShopProducts']);
    Route::get('all-brands',[BrandControllerApi::class,'AllBrand']);
});

Route::middleware('auth:sanctum')->get('/user', function (Request $request) {
    return $request->user();
});
