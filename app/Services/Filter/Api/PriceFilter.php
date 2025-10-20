<?php 
namespace App\Services\Filter\Api;

class PriceFilter {
    public function handle($payload, $next)
    {
        $query = $payload['query'];
        $filterData = $payload['filter'] ?? [];

        if (!empty($filterData['min_price']) || !empty($filterData['max_price'])) {
            $min = $filterData['min_price'] ?? 0;
            $max = $filterData['max_price'] ?? PHP_INT_MAX;

            $query->whereBetween('price', [$min, $max]);
        }

        $payload['query'] = $query;

        return $next($payload);
    }
}
