<?php 
namespace App\Services\Api;

use App\Services\BaseFilter;
use App\Services\Filter\Api\CategoryFilter;

class ProductFilter extends BaseFilter{
    protected function getFilters(){
        return [
            CategoryFilter::class
        ];
    }
}