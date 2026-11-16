<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class IsolateAdminSession
{
    public function handle(Request $request, Closure $next): Response
    {
        if ($request->is('admin') || $request->is('admin/*')) {
            config([
                'session.cookie' => env('ADMIN_SESSION_COOKIE', 'everbloom_admin_session'),
                'session.path'   => '/admin',
            ]);
        } else {
            config([
                'session.cookie' => env('SESSION_COOKIE', 'everbloom_user_session'),
                'session.path'   => '/',
            ]);
        }

        return $next($request);
    }
}
