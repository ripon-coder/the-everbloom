<?php
namespace App\Services\Filter\Api;
class CategoryFilter
{
    public function handle($payload, $next)
    {
        $query = $payload['query'];
        $filterData = $payload['filter'] ?? [];

        if (!empty($filterData['name'])) {
            $query->where('name', 'like', '%' . $filterData['name'] . '%');
        }

        $payload['query'] = $query;

        return $next($payload);
    }
}