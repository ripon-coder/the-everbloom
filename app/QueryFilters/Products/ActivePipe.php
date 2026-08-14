<?php

namespace App\QueryFilters\Products;

use Closure;

class ActivePipe
{
    public function handle($request, Closure $next)
    {
        $builder = $next($request);
        return $builder->active();
    }
}
