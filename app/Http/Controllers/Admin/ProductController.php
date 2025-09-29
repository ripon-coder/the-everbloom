<?php

namespace App\Http\Controllers\Admin;

use App\Models\Product;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use App\Http\Requests\StoreProductRequest;
use App\Http\Requests\UpdateProductRequest;
use App\Repositories\Contracts\ProductRepository;
use App\Services\ProductService;

class ProductController extends Controller
{
    protected $productRepository;
    protected $productService;

    public function __construct(ProductRepository $productRepository, ProductService $productService)
    {
        $this->productRepository = $productRepository;
        $this->productService = $productService;
    }

    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        $data["products"] = $this->productRepository->index();
        return view("admin.products.index", $data);
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create()
    {
        $data = $this->productRepository->create();
        return view("admin.products.create", $data);
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(StoreProductRequest $request)
    {
        return $this->productService->store($request->all());
    }

    /**
     * Display the specified resource.
     */
    public function show(Product $product)
    {

        $this->productService->show($product);
        return view("admin.products.show", compact('product'));
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(Product $product)
    {
        $data = $this->productRepository->edit($product->id);
        return view("admin.products.edit", $data);
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(UpdateProductRequest $request, Product $product)
    {
        return $this->productService->update($product->id, $request->all());
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(Product $product)
    {
        return $this->productService->destroy($product->id);
    }

    /**
     * Restore the specified resource from storage.
     */
    public function restore($id)
    {
        return $this->productService->restore($id);
    }

    /**
     * Force delete the specified resource from storage.
     */
    public function forceDelete($id)
    {
        return $this->productService->forceDelete($id);
    }
}
