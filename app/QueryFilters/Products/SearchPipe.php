<?php

namespace App\QueryFilters\Products;

use App\Models\Product;
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

        try {
            $matchingIds = Product::search($search)->keys()->toArray();
            if (!empty($matchingIds)) {
                return $builder->whereIn('id', $matchingIds);
            }
        } catch (\Throwable $e) {
            // Fallback if index not initialized yet
        }

        return $builder->where(function ($q) use ($search) {
            $q->where('name', 'LIKE', "%{$search}%")
              ->orWhere('short_description', 'LIKE', "%{$search}%")
              ->orWhere('description', 'LIKE', "%{$search}%")
              ->orWhere('sku', 'LIKE', "%{$search}%");
        });
    }
}
