<?php
namespace App\Services;

use App\Repositories\Contracts\CategoryRepository;

class CategoryService
{
    protected $categoryRepository;
    
    public function __construct(CategoryRepository $categoryRepository)
    {
        $this->categoryRepository = $categoryRepository;
    }

    public function create(array $validated)
    {
        $category = $this->categoryRepository->create($validated);
        if (array_key_exists("image", $validated) && $validated['image']) {
            $category->uploadImage($validated['image'], "category_image");
        }
        return $category;
    }

    public function update($id, array $validated)
    {
        $category = $this->categoryRepository->update($id, $validated);
        if (array_key_exists("image", $validated) && $validated['image']) {
            $category->uploadImage($validated['image'], "category_image");
        }
        return $category;
    }
}
