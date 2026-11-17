<?php

namespace App\Http\Middleware;

use App\Models\SlugRedirect;
use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class HandleSlugRedirects
{
    public function handle(Request $request, Closure $next): Response
    {
        $response = $next($request);

        if ($response->getStatusCode() === 404 && $request->isMethod('GET')) {
            $path = '/' . ltrim($request->path(), '/');
            $redirect = SlugRedirect::where('old_path', $path)->first();

            if ($redirect) {
                $redirect->increment('hits');

                return redirect($redirect->new_path, $redirect->status_code ?: 301);
            }
        }

        return $response;
    }
}

