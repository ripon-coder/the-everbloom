<?php
namespace App\Services\Api;

use App\Repositories\Contracts\CategoryRepository;


class CategoryServiceApi
{
    public $categoryRepository;
    public function __construct(CategoryRepository $categoryRepository)
    {
        $this->categoryRepository = $categoryRepository;
    }
    public function ParentCategory()
    {
        return $this->categoryRepository->parentCategory();
    }
}