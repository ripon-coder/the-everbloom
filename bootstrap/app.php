<?php

use App\Exceptions\InsufficientStockException;
use App\Http\Middleware\EnsureAccountIsActive;
use App\Http\Middleware\HandleSlugRedirects;
use App\Http\Middleware\IsolateAdminSession;
use App\Http\Middleware\LogAdminActivity;
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
        $middleware->web(append: [
            HandleSlugRedirects::class,
        ]);

        $middleware->alias([
            'active.account' => EnsureAccountIsActive::class,
            'admin.log' => LogAdminActivity::class,
        ]);

        $middleware->redirectGuestsTo(fn (Request $request) => ($request->is('admin') || $request->is('admin/*')) ? route('admin.login') : route('login'));
        $middleware->redirectUsersTo(fn (Request $request) => ($request->is('admin') || $request->is('admin/*')) ? route('admin.dashboard') : route('account'));
    })
    ->withExceptions(function (Exceptions $exceptions): void {
        $exceptions->render(function (InsufficientStockException $e, Request $request) {
            if ($request->expectsJson()) {
                return response()->json([
                    'message' => $e->getMessage(),
                    'errors' => [
                        'items' => [$e->getMessage()],
                    ],
                ], 422);
            }

            return redirect()->back()->withErrors([
                'items' => $e->getMessage(),
            ])->withInput();
        });
    })->create();
