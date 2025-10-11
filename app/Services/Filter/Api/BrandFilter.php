<?php
namespace App\Services\Filter\Api;

use App\Models\Brand;

class BrandFilter
{
    public function handle($payload, $next)
    {
        $query = $payload['query'];
        $filterData = $payload['filter'] ?? [];
        if (isset($filterData['brand']) && !empty($filterData['brand'])) {
            $brand_id = Brand::where("slug", $filterData['brand'])->value('id');
            $query->where('brand_id', $brand_id);
        }
        $payload['query'] = $query;
        return $next($payload);
    }
}