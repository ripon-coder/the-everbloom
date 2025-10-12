<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Http\Requests\StoreCategoryRequest;
use App\Http\Requests\UpdateCategoryRequest;
use App\Repositories\Contracts\CategoryRepository;
use App\Services\CategoryService;

class CategoryController extends Controller
{

    protected $categoryService;
    protected $categoryRepository;
    
    public function __construct(CategoryService $categoryService, CategoryRepository $categoryRepository)
    {
        $this->categoryService = $categoryService;
        $this->categoryRepository = $categoryRepository;
    }

    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        $data['categories'] = $this->categoryRepository->AllWithPaginate();
        return view("admin.categories.index", $data);
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create()
    {
        $data['allCategories'] = $this->categoryRepository->allCategory();
        return view("admin.categories.create", $data);
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(StoreCategoryRequest $request)
    {
        $validated = $request->validated();
        $category = $this->categoryService->create($validated);
        return redirect()->route('admin.categories.index')->with('success', 'Category created successfully!');
    }

    /**
     * Display the specified resource.
     */
    public function show(string $id)
    {
        //
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(string $id)
    {
        $data['category'] = $this->categoryRepository->FindById($id);
        $data['allCategories'] = $this->categoryRepository->allCategory();
        return view("admin.categories.edit", $data);
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(UpdateCategoryRequest $request, string $id)
    {
        $validated = $request->validated();
        $category = $this->categoryService->update($id, $validated);
        return redirect()->back()->with('success', 'Category updated successfully!');
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(string $id)
    {
         if($this->categoryRepository->DeleteFindBuyId($id)){
            return redirect()->route("admin.categories.index")->with("danger","Category Deleted Successfully!");
         }
    }
}
