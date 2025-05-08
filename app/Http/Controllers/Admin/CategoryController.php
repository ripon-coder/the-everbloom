<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Category;
use App\Repositories\Contracts\CategoryRespositoryInterface;
use Illuminate\Http\Request;

class CategoryController extends Controller
{
    protected $respository;
    public function __construct(CategoryRespositoryInterface $category_respository)
    {
        $this->respository = $category_respository;
    }
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        $categories = $this->respository->pagination(20);
        return view("admin.category.index", compact("categories"));
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create()
    {
        $categories = $this->respository->category();
        return view("admin.category.create", compact("categories"));
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        $this->respository->store($request->all());
        return back()->with("success", "Created Successfully!");
    }

    /**
     * Display the specified resource.
     */
    public function show(Category $category)
    {
        //
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(Category $category)
    {
        $categories = $this->respository->category();
        $category = $this->respository->idBy($category->id);
        return view('admin.category.edit', compact('category','categories'));
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, Category $category)
    {
        $this->respository->update($request->all(), $category->id);
        return back()->with("success", "Updated Successfully!");
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(Category $category)
    {
        $this->respository->destroy($category->id);
        return back()->with("success", "Deleted Successfully!");
    }
}
