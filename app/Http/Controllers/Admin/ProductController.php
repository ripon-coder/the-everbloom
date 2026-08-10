<?php

namespace App\Http\Controllers\Admin;

use App\Models\Product;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use App\Http\Requests\StoreProductRequest;
use App\Http\Requests\UpdateProductRequest;
use App\Repositories\Contracts\CategoryRepository;
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
        $allCategories = app(CategoryRepository::class)->allCategory();
        $data = $this->productRepository->create();
        $data = array_merge($data, ['allCategories' => $allCategories]);
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
        $allCategories = app(CategoryRepository::class)->allCategory();
        $data = $this->productRepository->edit($product->id);
        $data = array_merge($data, ['allCategories' => $allCategories]);
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
    /**
     * Quick update for basic product fields.
     */
    public function quickUpdate(Request $request, $id)
    {
        $request->validate([
            'name' => 'required|string|max:255',
            'price' => 'required|numeric|min:0',
            'status' => 'required|in:active,inactive',
            'is_featured' => 'nullable',
            'is_free_delivery' => 'nullable',
        ]);

        $product = Product::findOrFail($id);
        $product->update([
            'name' => $request->name,
            'price' => $request->price,
            'status' => $request->status,
            'is_featured' => $request->is_featured,
            'is_free_delivery' => $request->is_free_delivery,
        ]);

        return response()->json(['success' => true, 'message' => 'Product updated successfully.']);
    }

    /**
     * Live check slug availability & format validation via AJAX.
     */
    public function checkSlug(Request $request)
    {
        $rawSlug = trim($request->query('slug', ''));
        $excludeId = $request->query('exclude_id');

        if (empty($rawSlug)) {
            return response()->json(['available' => false, 'message' => 'Slug cannot be empty']);
        }

        // Validate format: Slugs must only contain lowercase alphanumeric characters and single hyphens (no spaces allowed)
        if (preg_match('/\s/', $rawSlug)) {
            return response()->json([
                'available' => false,
                'message' => 'Invalid slug format: Spaces are not allowed! Use hyphens (e.g. redmi-note-10)'
            ]);
        }

        if (!preg_match('/^[a-z0-9]+(?:-[a-z0-9]+)*$/i', $rawSlug)) {
            return response()->json([
                'available' => false,
                'message' => 'Invalid slug format! Only letters, numbers, and single hyphens are allowed.'
            ]);
        }

        $formattedSlug = \Illuminate\Support\Str::slug($rawSlug);

        $query = Product::where('slug', $formattedSlug);
        if ($excludeId) {
            $query->where('id', '!=', $excludeId);
        }

        $exists = $query->exists();

        return response()->json([
            'available' => !$exists,
            'slug' => $formattedSlug,
            'message' => $exists ? 'Slug already taken!' : 'Slug is available!'
        ]);
    }
}
