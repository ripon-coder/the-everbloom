<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Http\Requests\CreateProductRequest;
use App\Models\Products;
use App\Repositories\Contracts\BrandRespositoryInterface;
use App\Repositories\Contracts\CategoryRespositoryInterface;
use App\Repositories\Contracts\ProductRespositoryInterface;
use Illuminate\Http\Request;

class ProductController extends Controller
{
    public $product_respository;
    public function __construct(ProductRespositoryInterface $product_respository){
        $this->product_respository = $product_respository;
    }
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        $products = $this->product_respository->pagination(20);
        return view("admin.product.index", compact("products"));
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create(CategoryRespositoryInterface $categoryRespository,BrandRespositoryInterface $brandRespository)
    {
        $brands = $brandRespository->brand();
        $categories = $categoryRespository->category();
        return view("admin.product.create", compact("brands","categories"));
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(CreateProductRequest $request)
    {
        return $request->all();
    }

    /**
     * Display the specified resource.
     */
    public function show(Products $products)
    {
        //
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(Products $products)
    {
        //
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, Products $products)
    {
        //
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(Products $products)
    {
        //
    }
}
