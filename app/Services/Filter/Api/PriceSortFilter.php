<?php 
namespace App\Services\Filter\Api;

class PriceSortFilter {
    public function handle($payload, $next)
    {
        $query = $payload['query'];
        $filterData = $payload['filter'] ?? [];

        if (!empty($filterData['price_sort'])) {
            if ($filterData['price_sort'] == 'low_to_high') {
                $query->orderByRaw('COALESCE(NULLIF(products.price, 0), (SELECT MIN(pv.sell_price) FROM product_variants pv WHERE pv.product_id = products.id AND pv.deleted_at IS NULL), 0) ASC');
            } elseif ($filterData['price_sort'] == 'high_to_low') {
                $query->orderByRaw('COALESCE(NULLIF(products.price, 0), (SELECT MAX(pv.sell_price) FROM product_variants pv WHERE pv.product_id = products.id AND pv.deleted_at IS NULL), 0) DESC');
            }
        }

        $payload['query'] = $query;

        return $next($payload);
    }
}
