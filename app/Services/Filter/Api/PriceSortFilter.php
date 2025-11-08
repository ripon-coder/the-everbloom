<?php 
namespace App\Services\Filter\Api;

class PriceSortFilter {
    public function handle($payload, $next)
    {
        $query = $payload['query'];
        $filterData = $payload['filter'] ?? [];

        if (!empty($filterData['price_sort'])) {
            if ($filterData['price_sort'] == 'low_to_high') {
                $query->orderBy('price', 'asc');
            } elseif ($filterData['price_sort'] == 'high_to_low') {
                $query->orderBy('price', 'desc');
            }
        }

        $payload['query'] = $query;

        return $next($payload);
    }
}
