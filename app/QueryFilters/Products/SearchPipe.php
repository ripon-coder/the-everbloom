<?php

namespace App\QueryFilters\Products;

use Closure;

class SearchPipe
{
    public function handle($request, Closure $next)
    {
        $builder = $next($request);

        if (!request()->filled('search')) {
            return $builder;
        }

        $search = trim(request('search'));

        return $builder->where(function ($q) use ($search) {
            $q->where('name', 'LIKE', "%{$search}%")
              ->orWhere('short_description', 'LIKE', "%{$search}%")
              ->orWhere('description', 'LIKE', "%{$search}%")
              ->orWhere('sku', 'LIKE', "%{$search}%");
        });
    }
}
