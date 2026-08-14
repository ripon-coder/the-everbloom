<?php

namespace App\QueryFilters\Products;

use Closure;

class PriceMinPipe
{
    public function handle($request, Closure $next)
    {
        $builder = $next($request);

        if (!request()->filled('min_price')) {
            return $builder;
        }

        $minPrice = (float) request('min_price');

        return $builder->where(function ($q) use ($minPrice) {
            $q->where('price', '>=', $minPrice)
              ->orWhereHas('variants', function ($vq) use ($minPrice) {
                  $vq->active()->where(function ($pvq) use ($minPrice) {
                      $pvq->where('sell_price', '>=', $minPrice)
                          ->orWhere('discount_price', '>=', $minPrice);
                  });
              });
        });
    }
}
