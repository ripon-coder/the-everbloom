<?php 
namespace App\Services\Filter\Api;
class PriceFilter{
    public function handle($payload, $next)
    {
        $query = $payload['query'];
        $filterData = $payload['filter'] ?? [];

        if (!empty($filterData['max_price'])) {
            $query->where('price', '<=', $filterData['max_price']);
        }

        $payload['query'] = $query;

        return $next($payload);
    }
}