<?php

namespace App\Http\Controllers\Admin;

use App\Models\Product;
use App\Models\Brand;
use App\Models\Category;
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
        return $this->productService->store(array_merge($request->all(), $request->validated()));
    }

    /**
     * Display the specified resource.
     */
    public function show(Product $product)
    {
        return view('admin.products.show', compact('product'));
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(Product $product)
    {
        $allCategories = app(CategoryRepository::class)->allCategory();
        $data = $this->productRepository->edit($product->id);
        $data = array_merge($data, ['allCategories' => $allCategories]);
        return view('admin.products.edit', $data);
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(UpdateProductRequest $request, Product $product)
    {
        return $this->productService->update($product->id, array_merge($request->all(), $request->validated()));
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
        $rules = [
            'name' => 'nullable|string|max:255',
            'status' => 'nullable|in:active,inactive',
            'is_featured' => 'nullable',
            'is_free_delivery' => 'nullable',
        ];

        if ($request->has('price')) {
            $rules['price'] = 'required|numeric|min:0';
        }

        $request->validate($rules);

        $product = Product::findOrFail($id);
        $updateData = [
            'is_featured' => filter_var($request->is_featured, FILTER_VALIDATE_BOOLEAN) ? 1 : 0,
            'is_free_delivery' => filter_var($request->is_free_delivery, FILTER_VALIDATE_BOOLEAN) ? 1 : 0,
        ];

        if ($request->filled('status')) {
            $updateData['status'] = $request->status;
        }

        if ($request->filled('name')) {
            $updateData['name'] = $request->name;
        }

        if ($request->has('price')) {
            $updateData['price'] = $request->price;
        }

        $product->update($updateData);

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
