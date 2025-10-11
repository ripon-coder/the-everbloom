<?php
namespace App\Services\Filter\Api;

use App\Models\Category;

class CategoryFilter
{
    public function handle($payload, $next)
    {
        $query = $payload['query'];
        $filterData = $payload['filter'] ?? [];
        if (isset($filterData['category']) && !empty($filterData['category'])) {
            $category_id = Category::where("slug", $filterData['category'])->value('id');
            if ($category_id) {
                $query->where(function ($q) use ($category_id) {
                    $q->where('category_id', $category_id)
                        ->orWhereHas('category', function ($q2) use ($category_id) {
                            $q2->where('parent_id', $category_id);
                        });
                });
            }
        }
        $payload['query'] = $query;

        return $next($payload);
    }
}