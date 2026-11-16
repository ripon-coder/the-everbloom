<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Attribute;
use App\Models\AttributeValue;
use App\Models\Category;
use App\Models\Product;
use App\Models\ProductImage;
use App\Models\ProductVariant;
use App\Services\ImageStorageService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Str;

class AdminProductController extends Controller
{
    public function index(Request $request)
    {
        $query = Product::with(['categoryRelation', 'productVariants']);

        if ($request->filled('search')) {
            $ids = Product::search((string) $request->search)->keys()->all();
            if (empty($ids)) {
                $query->whereRaw('1 = 0');
            } else {
                $query->whereIn('id', $ids);
            }
        }

        if ($request->filled('category')) {
            $catSlug = (string) $request->category;
            $query->whereHas('categoryRelation', fn ($q) => $q->where('slug', $catSlug));
        }

        $products = $query->latest('id')->paginate(12)->withQueryString();
        $products->setCollection(
            $products->getCollection()->map(fn (Product $p) => $p->toStorefrontArray())
        );

        $categories = Category::orderBy('sort_order')->get()->toArray();

        return view('admin.products.index', compact('products', 'categories'));
    }

    public function create()
    {
        $categories = Category::orderBy('sort_order')->get()->toArray();
        $attributes = Attribute::with('values')->where('is_active', true)->orderBy('sort_order')->get();

        return view('admin.products.create', compact('categories', 'attributes'));
    }

    public function edit($id)
    {
        $product = Product::with(['categoryRelation', 'productVariants'])->findOrFail($id);
        $categories = Category::orderBy('sort_order')->get()->toArray();
        $attributes = Attribute::with('values')->where('is_active', true)->orderBy('sort_order')->get();

        return view('admin.products.create', [
            'product' => $product->toStorefrontArray(),
            'categories' => $categories,
            'attributes' => $attributes,
            'isEdit' => true,
        ]);
    }

    public function store(Request $request, ImageStorageService $imageStorage)
    {
        $validated = $request->validate([
            'name' => ['required', 'string', 'max:180'],
            'category_slug' => ['required', 'string', 'exists:categories,slug'],
            'slug' => ['nullable', 'string', 'max:160', 'unique:products,slug'],
            'badge' => ['nullable', 'string', 'max:60'],
            'stock_status' => ['nullable', 'in:in_stock,low_stock,out_of_stock'],
            'sizes' => ['nullable', 'array'],
            'sizes.*' => ['string', 'max:80'],
            'variants' => ['nullable', 'array'],
            'variants.*.title' => ['nullable', 'string', 'max:180'],
            'variants.*.sku' => ['nullable', 'string', 'max:80'],
            'variants.*.price' => ['nullable', 'numeric', 'min:0'],
            'variants.*.compare_at_price' => ['nullable', 'numeric', 'min:0'],
            'variants.*.stock_count' => ['nullable', 'integer', 'min:0'],
            'variants.*.stock_status' => ['nullable', 'in:in_stock,low_stock,out_of_stock'],
            'variants.*.attributes' => ['nullable', 'array'],
            'variant_images' => ['nullable', 'array'],
            'variant_images.*' => ['nullable', 'image', 'max:5120'],
            'is_featured' => ['nullable', 'boolean'],
            'short_description' => ['nullable', 'string'],
            'description' => ['nullable', 'string'],
            'ingredients' => ['nullable', 'string'],
            'how_to_use' => ['nullable', 'string'],
            'price' => ['required', 'numeric', 'min:0'],
            'compare_at_price' => ['nullable', 'numeric', 'min:0'],
            'sku' => ['nullable', 'string', 'max:60', 'unique:products,sku'],
            'stock_count' => ['nullable', 'integer', 'min:0'],
            'images' => ['nullable', 'array'],
            'images.*' => ['image', 'max:5120'],
            'file' => ['nullable', 'image', 'max:5120'],
        ]);

        $category = Category::where('slug', $validated['category_slug'])->firstOrFail();
        $slug = !empty($validated['slug']) ? Str::slug($validated['slug']) : Str::slug($validated['name']);
        if (Product::where('slug', $slug)->exists()) {
            $slug .= '-' . Str::lower(Str::random(4));
        }

        $sku = !empty($validated['sku'])
            ? $validated['sku']
            : 'EB-SKU-' . str_pad((string) ((Product::max('id') ?? 0) + 1), 4, '0', STR_PAD_LEFT);

        $uploadedFiles = [];
        if ($request->hasFile('images')) {
            $uploadedFiles = $request->file('images');
        } elseif ($request->hasFile('file')) {
            $uploadedFiles = [$request->file('file')];
        }

        $storedImages = [];
        $imageSource = 'local';
        foreach ($uploadedFiles as $file) {
            $result = $imageStorage->uploadAsWebp($file, 'products', $slug);
            $storedImages[] = $result['path'];
            $imageSource = $result['source'];
        }

        $sizes = $validated['sizes'] ?? ['30ml / 1.0 fl oz'];
        $stockCount = (int) ($validated['stock_count'] ?? 25);
        $stockStatus = $validated['stock_status'] ?? ($stockCount > 10 ? 'in_stock' : ($stockCount > 0 ? 'low_stock' : 'out_of_stock'));

        $product = Product::create([
            'category_id' => $category->id,
            'slug' => $slug,
            'sku' => $sku,
            'name' => $validated['name'],
            'price' => $validated['price'],
            'compare_at_price' => $validated['compare_at_price'] ?? null,
            'short_description' => $validated['short_description'] ?? null,
            'description' => $validated['description'] ?? null,
            'ingredients' => $validated['ingredients'] ?? null,
            'how_to_use' => $validated['how_to_use'] ?? null,
            'sizes' => $sizes,
            'images' => $storedImages,
            'image_source' => $imageSource,
            'stock_count' => $stockCount,
            'stock_status' => $stockStatus,
            'is_featured' => $request->boolean('is_featured'),
            'badge' => $validated['badge'] ?? null,
            'is_active' => true,
        ]);

        foreach ($storedImages as $idx => $path) {
            ProductImage::create([
                'product_id' => $product->id,
                'image' => $path,
                'image_source' => $imageSource,
                'is_primary' => $idx === 0,
                'sort_order' => $idx,
            ]);
        }

        $this->syncProductVariants($product, $request, $imageStorage, $validated, $sku, $stockCount, $stockStatus);

        Cache::forget('storefront_catalog_json');
        $product->searchable();

        return redirect()->route('admin.products.index')->with('success', 'Product created successfully.');
    }

    public function update(Request $request, $id, ImageStorageService $imageStorage)
    {
        $product = Product::findOrFail($id);

        $validated = $request->validate([
            'name' => ['required', 'string', 'max:180'],
            'category_slug' => ['required', 'string', 'exists:categories,slug'],
            'slug' => ['nullable', 'string', 'max:160', 'unique:products,slug,' . $product->id],
            'badge' => ['nullable', 'string', 'max:60'],
            'stock_status' => ['nullable', 'in:in_stock,low_stock,out_of_stock'],
            'sizes' => ['nullable', 'array'],
            'sizes.*' => ['string', 'max:80'],
            'variants' => ['nullable', 'array'],
            'variants.*.title' => ['nullable', 'string', 'max:180'],
            'variants.*.sku' => ['nullable', 'string', 'max:80'],
            'variants.*.price' => ['nullable', 'numeric', 'min:0'],
            'variants.*.compare_at_price' => ['nullable', 'numeric', 'min:0'],
            'variants.*.stock_count' => ['nullable', 'integer', 'min:0'],
            'variants.*.stock_status' => ['nullable', 'in:in_stock,low_stock,out_of_stock'],
            'variants.*.attributes' => ['nullable', 'array'],
            'variant_images' => ['nullable', 'array'],
            'variant_images.*' => ['nullable', 'image', 'max:5120'],
            'is_featured' => ['nullable', 'boolean'],
            'short_description' => ['nullable', 'string'],
            'description' => ['nullable', 'string'],
            'ingredients' => ['nullable', 'string'],
            'how_to_use' => ['nullable', 'string'],
            'price' => ['required', 'numeric', 'min:0'],
            'compare_at_price' => ['nullable', 'numeric', 'min:0'],
            'sku' => ['nullable', 'string', 'max:60', 'unique:products,sku,' . $product->id],
            'stock_count' => ['nullable', 'integer', 'min:0'],
            'images' => ['nullable', 'array'],
            'images.*' => ['image', 'max:5120'],
            'file' => ['nullable', 'image', 'max:5120'],
        ]);

        $category = Category::where('slug', $validated['category_slug'])->firstOrFail();
        $slug = !empty($validated['slug']) ? Str::slug($validated['slug']) : $product->slug;

        $storedImages = $product->images ?? [];
        $imageSource = $product->image_source ?: 'local';

        $uploadedFiles = [];
        if ($request->hasFile('images')) {
            $uploadedFiles = $request->file('images');
        } elseif ($request->hasFile('file')) {
            $uploadedFiles = [$request->file('file')];
        }

        if (!empty($uploadedFiles)) {
            $newImages = [];
            foreach ($uploadedFiles as $file) {
                $result = $imageStorage->uploadAsWebp($file, 'products', $slug);
                $newImages[] = $result['path'];
                $imageSource = $result['source'];
            }
            $storedImages = $newImages;

            $product->productImages()->delete();
            foreach ($storedImages as $idx => $path) {
                ProductImage::create([
                    'product_id' => $product->id,
                    'image' => $path,
                    'image_source' => $imageSource,
                    'is_primary' => $idx === 0,
                    'sort_order' => $idx,
                ]);
            }
        }

        $sizes = $validated['sizes'] ?? ($product->sizes ?: ['30ml / 1.0 fl oz']);
        $stockCount = array_key_exists('stock_count', $validated) && $validated['stock_count'] !== null
            ? (int) $validated['stock_count']
            : $product->stock_count;
        $stockStatus = $validated['stock_status'] ?? $product->stock_status;

        $product->update([
            'category_id' => $category->id,
            'slug' => $slug,
            'sku' => $validated['sku'] ?? $product->sku,
            'name' => $validated['name'],
            'price' => $validated['price'],
            'compare_at_price' => $validated['compare_at_price'] ?? null,
            'short_description' => $validated['short_description'] ?? null,
            'description' => $validated['description'] ?? null,
            'ingredients' => $validated['ingredients'] ?? null,
            'how_to_use' => $validated['how_to_use'] ?? null,
            'sizes' => $sizes,
            'images' => $storedImages,
            'image_source' => $imageSource,
            'stock_count' => $stockCount,
            'stock_status' => $stockStatus,
            'is_featured' => $request->boolean('is_featured'),
            'badge' => $validated['badge'] ?? null,
        ]);

        $this->syncProductVariants($product, $request, $imageStorage, $validated, $product->sku, $stockCount, $stockStatus);

        Cache::forget('storefront_catalog_json');
        $product->searchable();

        return redirect()->route('admin.products.index')->with('success', 'Product updated successfully.');
    }

    private function syncProductVariants(
        Product $product,
        Request $request,
        ImageStorageService $imageStorage,
        array $validated,
        string $sku,
        int $defaultStockCount,
        string $defaultStockStatus
    ): void {
        $submittedVariants = $request->input('variants', []);
        if (!empty($submittedVariants) && is_array($submittedVariants)) {
            $existingVariants = $product->productVariants()->get()->keyBy('id');
            $keptIds = [];
            $syncedTitles = [];

            foreach (array_values($submittedVariants) as $idx => $vData) {
                $attributes = array_filter($vData['attributes'] ?? [], fn ($val) => $val !== null && $val !== '');
                $title = trim((string) ($vData['title'] ?? ''));
                if ($title === '' && !empty($attributes)) {
                    $title = implode(' / ', array_values($attributes));
                }
                if ($title === '') {
                    $title = 'Standard';
                }

                $existing = !empty($vData['id']) ? $existingVariants->get((int) $vData['id']) : null;
                $vImage = $existing?->image;
                $vImageSource = $existing?->image_source ?: 'local';

                if (!empty($vData['remove_image']) && $vImage) {
                    $imageStorage->delete($vImage, $vImageSource);
                    $vImage = null;
                    $vImageSource = 'local';
                }

                if ($request->hasFile("variant_images.{$idx}")) {
                    if ($vImage) {
                        $imageStorage->delete($vImage, $vImageSource);
                    }
                    $uploaded = $imageStorage->uploadAsWebp(
                        $request->file("variant_images.{$idx}"),
                        'products/variants',
                        $product->slug . '-v' . ($idx + 1)
                    );
                    $vImage = $uploaded['path'];
                    $vImageSource = $uploaded['source'];
                }

                $vStock = isset($vData['stock_count']) && $vData['stock_count'] !== ''
                    ? (int) $vData['stock_count']
                    : $defaultStockCount;
                $vStatus = $vData['stock_status'] ?? ($vStock > 0 ? $defaultStockStatus : 'out_of_stock');

                $variant = $existing
                    ? tap($existing)->update([
                        'size' => $title,
                        'title' => $title,
                        'attributes_json' => $attributes,
                        'sku' => !empty($vData['sku']) ? $vData['sku'] : ($sku . '-V' . ($idx + 1)),
                        'price' => isset($vData['price']) && $vData['price'] !== '' ? (float) $vData['price'] : (float) $validated['price'],
                        'compare_at_price' => isset($vData['compare_at_price']) && $vData['compare_at_price'] !== '' ? (float) $vData['compare_at_price'] : ($validated['compare_at_price'] ?? null),
                        'stock_count' => $vStock,
                        'stock_status' => $vStatus,
                        'image' => $vImage,
                        'image_source' => $vImageSource,
                        'sort_order' => $idx,
                        'is_active' => true,
                    ])
                    : ProductVariant::create([
                        'product_id' => $product->id,
                        'size' => $title,
                        'title' => $title,
                        'attributes_json' => $attributes,
                        'sku' => !empty($vData['sku']) ? $vData['sku'] : ($sku . '-V' . ($idx + 1)),
                        'price' => isset($vData['price']) && $vData['price'] !== '' ? (float) $vData['price'] : (float) $validated['price'],
                        'compare_at_price' => isset($vData['compare_at_price']) && $vData['compare_at_price'] !== '' ? (float) $vData['compare_at_price'] : ($validated['compare_at_price'] ?? null),
                        'stock_count' => $vStock,
                        'stock_status' => $vStatus,
                        'image' => $vImage,
                        'image_source' => $vImageSource,
                        'sort_order' => $idx,
                        'is_active' => true,
                    ]);

                // Sync pivot product_variant_values when matching AttributeValue exists
                $pivotData = [];
                foreach ($attributes as $attrName => $attrVal) {
                    $attrValueModel = AttributeValue::where('value', $attrVal)
                        ->whereHas('attribute', fn ($q) => $q->where('name', $attrName))
                        ->first();
                    if ($attrValueModel) {
                        $pivotData[$attrValueModel->id] = ['attribute_id' => $attrValueModel->attribute_id];
                    }
                }
                $variant->attributeValues()->sync($pivotData);

                $keptIds[] = $variant->id;
                $syncedTitles[] = $title;
            }

            $product->productVariants()->whereNotIn('id', $keptIds)->delete();
            $product->update(['sizes' => $syncedTitles]);

            return;
        }

        // Legacy fallback when simple sizes[] checkboxes are submitted
        $sizes = $validated['sizes'] ?? ($product->sizes ?: ['30ml / 1.0 fl oz']);
        $product->productVariants()->whereNotIn('size', $sizes)->delete();
        foreach ($sizes as $idx => $sizeLabel) {
            $product->productVariants()->updateOrCreate(
                ['size' => $sizeLabel],
                [
                    'title' => $sizeLabel,
                    'sku' => $sku . '-V' . ($idx + 1),
                    'price' => $validated['price'],
                    'compare_at_price' => $validated['compare_at_price'] ?? null,
                    'stock_count' => $defaultStockCount,
                    'stock_status' => $defaultStockStatus,
                    'sort_order' => $idx,
                ]
            );
        }
    }

    public function destroy($id, ImageStorageService $imageStorage)
    {
        $product = Product::findOrFail($id);

        foreach ($product->images ?? [] as $path) {
            $imageStorage->delete($path, $product->image_source);
        }

        $product->unsearchable();
        $product->delete();
        Cache::forget('storefront_catalog_json');

        return redirect()->route('admin.products.index')->with('success', 'Product deleted successfully.');
    }
}
