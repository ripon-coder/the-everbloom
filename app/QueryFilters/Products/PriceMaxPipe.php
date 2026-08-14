<?php

namespace App\QueryFilters\Products;

use Closure;

class PriceMaxPipe
{
    public function handle($request, Closure $next)
    {
        $builder = $next($request);

        if (!request()->filled('max_price')) {
            return $builder;
        }

        $maxPrice = (float) request('max_price');

        return $builder->where(function ($q) use ($maxPrice) {
            $q->where(function ($pq) use ($maxPrice) {
                $pq->where('price', '<=', $maxPrice)
                   ->orWhereHas('variants', function ($vq) use ($maxPrice) {
                       $vq->active()->where(function ($pvq) use ($maxPrice) {
                           $pvq->where('sell_price', '<=', $maxPrice)
                               ->orWhere('discount_price', '<=', $maxPrice);
                       });
                   });
            });
        });
    }
}
