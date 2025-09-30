<?php

use App\Http\Controllers\Api\CategoryControllerApi;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;

Route::prefix("v1")->group(function(){
    Route::get("parent-category",[CategoryControllerApi::class,'ParentCategory']);
});

Route::middleware('auth:sanctum')->get('/user', function (Request $request) {
    return $request->user();
});
