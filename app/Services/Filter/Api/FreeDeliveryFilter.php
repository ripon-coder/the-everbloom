<?php 
namespace App\Services\Filter\Api;

use App\Constants\ProductVariantStatus;

class FreeDeliveryFilter {
    public function handle($payload, $next)
    {
        $query = $payload['query'];
        $filterData = $payload['filter'] ?? [];

        if (!empty($filterData['free_delivery'])) {
            $query->where('is_free_delivery',1);
        }

        $payload['query'] = $query;

        return $next($payload);
    }
}
