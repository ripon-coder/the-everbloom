<?php

namespace App\Http\Middleware;

use App\Models\AdminActivityLog;
use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Symfony\Component\HttpFoundation\Response;

class LogAdminActivity
{
    private const WRITE_METHODS = ['POST', 'PUT', 'PATCH', 'DELETE'];

    public function handle(Request $request, Closure $next): Response
    {
        $response = $next($request);

        if (in_array($request->method(), self::WRITE_METHODS, true)) {
            $admin = Auth::guard('admin')->user();
            if ($admin) {
                AdminActivityLog::create([
                    'admin_id' => $admin->id,
                    'method' => $request->method(),
                    'route_name' => $request->route()?->getName(),
                    'path' => mb_substr($request->path(), 0, 255),
                    'ip_address' => $request->ip(),
                    'user_agent' => $request->userAgent() ? mb_substr((string) $request->userAgent(), 0, 255) : null,
                    'status_code' => $response->getStatusCode(),
                    'created_at' => now(),
                ]);
            }
        }

        return $response;
    }
}

