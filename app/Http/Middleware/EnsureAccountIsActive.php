<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Symfony\Component\HttpFoundation\Response;

class EnsureAccountIsActive
{
    public function handle(Request $request, Closure $next, string $guard = 'web'): Response
    {
        $user = Auth::guard($guard)->user();

        if (! $user) {
            return $next($request);
        }

        $isActive = $guard === 'admin'
            ? (bool) $user->is_active
            : ($user->status === 'Active');

        if (! $isActive) {
            Auth::guard($guard)->logout();
            $request->session()->invalidate();
            $request->session()->regenerateToken();

            if ($request->expectsJson()) {
                return response()->json([
                    'message' => 'Your account has been deactivated.',
                ], 403);
            }

            if ($guard === 'admin') {
                return redirect()->route('admin.login')->withErrors([
                    'email' => 'Your admin account is inactive.',
                ]);
            }

            return redirect()->route('login')->withErrors([
                'email' => 'Your account is currently inactive.',
            ]);
        }

        return $next($request);
    }
}

