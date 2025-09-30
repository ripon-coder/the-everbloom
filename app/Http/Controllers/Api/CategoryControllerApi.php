<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Http\Resources\ParentCategoryResource;
use App\Services\Api\CategoryServiceApi;
use Illuminate\Http\Request;

class CategoryControllerApi extends BaseApiController
{

    public $categoryService;
    public function __construct(CategoryServiceApi $categoryService)
    {
        $this->categoryService = $categoryService;
    }
    public function ParentCategory()
    {
        $category = $this->categoryService->ParentCategory();
        $data = ParentCategoryResource::collection($category);
        return $this->successResponse($data, 'Category fetched successfully');
    }
    public function AllCategory(){
        return $this->categoryService->AllCategory();
    }
}
