<?php

use App\Http\Middleware\IsolateAdminSession;
use Illuminate\Foundation\Application;
use Illuminate\Foundation\Configuration\Exceptions;
use Illuminate\Foundation\Configuration\Middleware;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;

return Application::configure(basePath: dirname(__DIR__))
    ->withRouting(
        web: __DIR__.'/../routes/web.php',
        commands: __DIR__.'/../routes/console.php',
        health: '/up',
        then: function () {
            Route::middleware('web')
                ->group(base_path('routes/admin.php'));
        },
    )
    ->withMiddleware(function (Middleware $middleware): void {
        $middleware->web(prepend: [
            IsolateAdminSession::class,
        ]);

        $middleware->redirectGuestsTo(fn (Request $request) => ($request->is('admin') || $request->is('admin/*')) ? route('admin.login') : route('login'));
        $middleware->redirectUsersTo(fn (Request $request) => ($request->is('admin') || $request->is('admin/*')) ? route('admin.dashboard') : route('account'));
    })
    ->withExceptions(function (Exceptions $exceptions): void {
        //
    })->create();
