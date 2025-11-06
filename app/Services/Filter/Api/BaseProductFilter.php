<?php 
namespace App\Services\Filter\Api;
use App\Services\BaseFilter;
use App\Services\Filter\Api\BrandFilter;
use App\Services\Filter\Api\PriceFilter;
use App\Services\Filter\Api\InStockFilter;
use App\Services\Filter\Api\CategoryFilter;
use App\Services\Filter\Api\FreeDeliveryFilter;

class BaseProductFilter extends BaseFilter
{
    protected function getFilters()
    {
        return [
            CategoryFilter::class,
            BrandFilter::class,
            PriceFilter::class,
            InStockFilter::class,
            FreeDeliveryFilter::class
        ];
    }
}