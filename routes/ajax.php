<?php

use App\Http\Controllers\Admin\AttributeController;
use Illuminate\Support\Facades\Route;
use App\Http\Middleware\RedirectIfNotAuthenticated;

Route::middleware([RedirectIfNotAuthenticated::class . ":admin", 'throttle:global'])->prefix("admin")->as('admin.')->group(function () {
    Route::get('get-attribute-value', action: [AttributeController::class,'getAttributeValueById'])->name(name: 'get-attribute-value');
});

