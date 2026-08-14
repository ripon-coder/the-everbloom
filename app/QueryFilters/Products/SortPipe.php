<?php

namespace App\QueryFilters\Products;

use Closure;

class SortPipe
{
    public function handle($request, Closure $next)
    {
        $builder = $next($request);
        $sort = request('sort', 'latest');

        switch ($sort) {
            case 'price_asc':
                return $builder->orderBy('price', 'asc');
            case 'price_desc':
                return $builder->orderBy('price', 'desc');
            case 'popular':
                return $builder->popular();
            default:
                return $builder->latest();
        }
    }
}
