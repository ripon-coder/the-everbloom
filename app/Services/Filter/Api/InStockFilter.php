<?php 
namespace App\Services\Filter\Api;

use App\Constants\ProductVariantStatus;

class InStockFilter {
    public function handle($payload, $next)
    {
        $query = $payload['query'];
        $filterData = $payload['filter'] ?? [];

        if (!empty($filterData['stock_in'])) {
            $query->whereHas('variants', function ($q) {
                $q->where('stock', '>', 0);
            });
        }

        $payload['query'] = $query;

        return $next($payload);
    }
}
