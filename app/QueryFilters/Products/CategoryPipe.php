<?php

namespace App\QueryFilters\Products;

use App\Models\Category;
use Closure;

class CategoryPipe
{
    public function handle($request, Closure $next)
    {
        $builder = $next($request);

        if (!request()->filled('category')) {
            return $builder;
        }

        $categorySlug = request('category');
        $category = Category::where('slug', $categorySlug)->first();

        if ($category) {
            $categoryIds = Category::where('parent_id', $category->id)
                ->pluck('id')
                ->push($category->id);

            return $builder->whereIn('category_id', $categoryIds);
        }

        return $builder;
    }
}
