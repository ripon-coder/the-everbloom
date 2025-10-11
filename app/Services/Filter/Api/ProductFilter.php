<?php 
namespace App\Services\Filter\Api;
use App\Services\BaseFilter;
use App\Services\Filter\Api\CategoryFilter;
use App\Services\Filter\Api\BrandFilter;
use App\Services\Filter\Api\PriceFilter;
class ProductFilter extends BaseFilter
{
    protected function getFilters()
    {
        return [
            CategoryFilter::class,
            BrandFilter::class,
            //PriceFilter::class,
        ];
    }
}