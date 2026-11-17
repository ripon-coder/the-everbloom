<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Http\Requests\Admin\StoreProductRequest;
use App\Http\Requests\Admin\UpdateProductRequest;
use App\Models\Attribute;
use App\Models\AttributeValue;
use App\Models\Brand;
use App\Models\Category;
use App\Models\Product;
use App\Models\ProductImage;
use App\Models\ProductVariant;
use App\Services\ImageStorageService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\DB;
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
            $cat = Category::where('slug', $catSlug)->first();
            if ($cat) {
                $query->whereIn('category_id', $cat->descendantAndSelfIds());
            } else {
                $query->whereRaw('1 = 0');
            }
        }

        if ($request->filled('status')) {
            match ($request->status) {
                'active' => $query->where('is_active', true),
                'inactive' => $query->where('is_active', false),
                default => null,
            };
        }

        match ($request->query('sort', 'latest')) {
            'price_asc' => $query->orderBy('price', 'asc'),
            'price_desc' => $query->orderBy('price', 'desc'),
            'stock_asc' => $query->orderBy('stock_count', 'asc'),
            default => $query->latest('id'),
        };

        $products = $query->paginate(12)->withQueryString();
        $products->setCollection(
            $products->getCollection()->map(fn (Product $p) => $p->toStorefrontArray())
        );

        $categories = Category::orderBy('sort_order')->get()->toArray();

        return view('admin.products.index', compact('products', 'categories'));
    }

    public function create()
    {
        $categories = Category::with(['parent.parent', 'assignedAttributes'])
            ->orderBy('level')
            ->orderBy('sort_order')
            ->get()
            ->map(function (Category $c) {
                $arr = $c->toArray();
                $arr['hierarchy_path'] = $c->hierarchy_path;
                $arr['effective_attribute_ids'] = $c->effectiveAttributeIds();
                return $arr;
            })
            ->all();
        $attributes = Attribute::with('values')->where('is_active', true)->orderBy('sort_order')->get();
        $brands = Brand::orderBy('sort_order')->orderBy('name')->get();

        return view('admin.products.create', compact('categories', 'attributes', 'brands'));
    }

    public function edit($id)
    {
        $product = Product::with(['categoryRelation', 'brandRelation', 'productVariants'])->findOrFail($id);
        $categories = Category::with(['parent.parent', 'assignedAttributes'])
            ->orderBy('level')
            ->orderBy('sort_order')
            ->get()
            ->map(function (Category $c) {
                $arr = $c->toArray();
                $arr['hierarchy_path'] = $c->hierarchy_path;
                $arr['effective_attribute_ids'] = $c->effectiveAttributeIds();
                return $arr;
            })
            ->all();
        $attributes = Attribute::with('values')->where('is_active', true)->orderBy('sort_order')->get();
        $brands = Brand::orderBy('sort_order')->orderBy('name')->get();

        return view('admin.products.create', [
            'product' => $product->toStorefrontArray(),
            'categories' => $categories,
            'attributes' => $attributes,
            'brands' => $brands,
            'isEdit' => true,
        ]);
    }

    public function store(StoreProductRequest $request, ImageStorageService $imageStorage)
    {
        $validated = $request->validated();

        $category = Category::where('slug', $validated['category_slug'])->firstOrFail();
        $slug = !empty($validated['slug']) ? Str::slug($validated['slug']) : Str::slug($validated['name']);
        if (Product::where('slug', $slug)->exists()) {
            $slug .= '-' . Str::lower(Str::random(4));
        }

        $sku = !empty($validated['sku'])
            ? $validated['sku']
            : 'EB-SKU-' . str_pad((string) ((Product::max('id') ?? 0) + 1), 4, '0', STR_PAD_LEFT);
        while (Product::where('sku', $sku)->exists()) {
            $sku = 'EB-SKU-' . Str::upper(Str::random(6));
        }

        $uploadedFiles = $this->extractUploadedImages($request);

        $storedImages = [];
        $imageSource = 'local';
        foreach ($uploadedFiles as $file) {
            $result = $imageStorage->uploadAsWebp($file, 'products', $slug);
            $storedImages[] = $result['path'];
            $imageSource = $result['source'];
        }

        $sizes = $validated['sizes'] ?? ['30ml / 1.0 fl oz'];
        $minThreshold = (int) ($validated['min_threshold'] ?? 10);
        $stockCount = (int) ($validated['stock_count'] ?? 25);
        $stockStatus = $validated['stock_status'] ?? ($stockCount > $minThreshold ? 'in_stock' : ($stockCount > 0 ? 'low_stock' : 'out_of_stock'));

        $brandModel = !empty($validated['brand_id']) ? Brand::find($validated['brand_id']) : null;
        $brandName = $brandModel?->name ?? ($validated['brand'] ?? null);

        $product = Product::withoutSyncingToSearch(fn () => Product::create([
            'category_id' => $category->id,
            'brand_id' => $brandModel?->id,
            'slug' => $slug,
            'sku' => $sku,
            'name' => $validated['name'],
            'brand' => $brandName,
            'subtitle' => $validated['subtitle'] ?? null,
            'price' => $validated['price'],
            'compare_at_price' => $validated['compare_at_price'] ?? null,
            'short_description' => $validated['short_description'] ?? null,
            'description' => $validated['description'] ?? null,
            'ingredients' => $validated['ingredients'] ?? null,
            'key_ingredients' => $this->parseListInput($request->input('key_ingredients')),
            'benefits' => $this->parseListInput($request->input('benefits')),
            'skin_types' => $this->parseListInput($request->input('skin_types')),
            'how_to_use' => $validated['how_to_use'] ?? null,
            'sizes' => $sizes,
            'images' => $storedImages,
            'image_source' => $imageSource,
            'stock_count' => $stockCount,
            'min_threshold' => $minThreshold,
            'stock_status' => $stockStatus,
            'is_featured' => $request->boolean('is_featured'),
            'best_seller' => $request->boolean('best_seller'),
            'badge' => $validated['badge'] ?? null,
            'is_active' => $request->has('is_active') ? $request->boolean('is_active') : true,
            'meta_title' => $validated['meta_title'] ?? null,
            'meta_description' => $validated['meta_description'] ?? null,
        ]));

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
        try {
            $product->searchable();
        } catch (\Throwable $e) {
            // Do not block product persistence if the search index driver is unavailable.
        }

        return redirect()->route('admin.products.index')->with('success', 'Product created successfully.');
    }

    public function update(UpdateProductRequest $request, $id, ImageStorageService $imageStorage)
    {
        $product = Product::findOrFail($id);
        $validated = $request->validated();

        $category = Category::where('slug', $validated['category_slug'])->firstOrFail();
        $slug = !empty($validated['slug']) ? Str::slug($validated['slug']) : $product->slug;

        $storedImages = is_array($product->images) ? $product->images : [];
        $imageSource = $product->image_source ?: 'local';

        $removeImages = (array) ($validated['remove_images'] ?? []);
        if (!empty($removeImages)) {
            $remaining = [];
            foreach ($storedImages as $path) {
                if (in_array($path, $removeImages, true)) {
                    $imageStorage->delete($path, $imageSource);
                } else {
                    $remaining[] = $path;
                }
            }
            $storedImages = $remaining;
        }

        $uploadedFiles = $this->extractUploadedImages($request);
        if (!empty($uploadedFiles)) {
            foreach ($uploadedFiles as $file) {
                $result = $imageStorage->uploadAsWebp($file, 'products', $slug);
                $storedImages[] = $result['path'];
                $imageSource = $result['source'];
            }
        }

        if (!empty($removeImages) || !empty($uploadedFiles)) {
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
        $minThreshold = array_key_exists('min_threshold', $validated) && $validated['min_threshold'] !== null
            ? (int) $validated['min_threshold']
            : (int) $product->min_threshold;
        $stockCount = array_key_exists('stock_count', $validated) && $validated['stock_count'] !== null
            ? (int) $validated['stock_count']
            : $product->stock_count;
        $stockStatus = $validated['stock_status'] ?? $product->stock_status;

        $brandId = array_key_exists('brand_id', $validated) ? $validated['brand_id'] : $product->brand_id;
        $brandModel = $brandId ? Brand::find($brandId) : null;
        $brandName = $brandModel ? $brandModel->name : (array_key_exists('brand', $validated) ? $validated['brand'] : $product->brand);

        $product->update([
            'category_id' => $category->id,
            'brand_id' => $brandModel?->id,
            'slug' => $slug,
            'sku' => !empty($validated['sku']) ? $validated['sku'] : $product->sku,
            'name' => $validated['name'],
            'brand' => $brandName,
            'subtitle' => array_key_exists('subtitle', $validated) ? $validated['subtitle'] : $product->subtitle,
            'price' => $validated['price'],
            'compare_at_price' => $validated['compare_at_price'] ?? null,
            'short_description' => $validated['short_description'] ?? null,
            'description' => $validated['description'] ?? null,
            'ingredients' => $validated['ingredients'] ?? null,
            'key_ingredients' => $request->has('key_ingredients') ? $this->parseListInput($request->input('key_ingredients')) : $product->key_ingredients,
            'benefits' => $request->has('benefits') ? $this->parseListInput($request->input('benefits')) : $product->benefits,
            'skin_types' => $request->has('skin_types') ? $this->parseListInput($request->input('skin_types')) : $product->skin_types,
            'how_to_use' => $validated['how_to_use'] ?? null,
            'sizes' => $sizes,
            'images' => $storedImages,
            'image_source' => $imageSource,
            'stock_count' => $stockCount,
            'min_threshold' => $minThreshold,
            'stock_status' => $stockStatus,
            'is_featured' => $request->boolean('is_featured'),
            'best_seller' => $request->has('best_seller') ? $request->boolean('best_seller') : $product->best_seller,
            'badge' => $validated['badge'] ?? null,
            'is_active' => $request->has('is_active') ? $request->boolean('is_active') : $product->is_active,
            'meta_title' => array_key_exists('meta_title', $validated) ? $validated['meta_title'] : $product->meta_title,
            'meta_description' => array_key_exists('meta_description', $validated) ? $validated['meta_description'] : $product->meta_description,
        ]);

        $this->syncProductVariants($product, $request, $imageStorage, $validated, $product->sku, $stockCount, $stockStatus);

        Cache::forget('storefront_catalog_json');
        try {
            $product->searchable();
        } catch (\Throwable $e) {
            // Do not block product update if the search index driver is unavailable.
        }

        return redirect()->route('admin.products.index')->with('success', 'Product updated successfully.');
    }

    private function extractUploadedImages(Request $request): array
    {
        if ($request->hasFile('images')) {
            return (array) $request->file('images');
        }
        if ($request->hasFile('file')) {
            return [$request->file('file')];
        }

        return [];
    }

    private function parseListInput(mixed $value): ?array
    {
        if ($value === null || $value === '') {
            return null;
        }
        if (is_array($value)) {
            $items = array_values(array_filter(array_map(fn ($v) => trim((string) $v), $value), fn ($v) => $v !== ''));
            return empty($items) ? null : $items;
        }
        $items = array_values(array_filter(array_map('trim', explode(',', (string) $value)), fn ($v) => $v !== ''));

        return empty($items) ? null : $items;
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
            $totalVariantStock = (int) $product->productVariants()->sum('stock_count');
            $product->update([
                'sizes' => $syncedTitles,
                'stock_count' => $totalVariantStock,
                'stock_status' => $totalVariantStock > $product->min_threshold ? 'in_stock' : ($totalVariantStock > 0 ? 'low_stock' : 'out_of_stock'),
            ]);

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
        $product = Product::with('productVariants')->findOrFail($id);

        if ($product->orderItems()->exists()) {
            return redirect()->back()->withErrors([
                'product' => 'Cannot delete this product because it is referenced by existing orders. Deactivate it instead.',
            ]);
        }

        $imagesToDelete = $product->images ?? [];
        $imageSource = $product->image_source;
        $variantImages = $product->productVariants
            ->filter(fn (ProductVariant $v) => !empty($v->image))
            ->map(fn (ProductVariant $v) => ['path' => $v->image, 'source' => $v->image_source])
            ->all();

        DB::transaction(function () use ($product) {
            $product->delete();
        });

        foreach ($imagesToDelete as $path) {
            $imageStorage->delete($path, $imageSource);
        }

        foreach ($variantImages as $vImg) {
            $imageStorage->delete($vImg['path'], $vImg['source']);
        }

        Cache::forget('storefront_catalog_json');
        try {
            $product->unsearchable();
        } catch (\Throwable $e) {
            // Do not block product deletion if the search index driver is unavailable.
        }

        return redirect()->route('admin.products.index')->with('success', 'Product deleted successfully.');
    }

    public function bulkAction(Request $request, ImageStorageService $imageStorage)
    {
        $validated = $request->validate([
            'action' => ['required', 'in:activate,deactivate,delete'],
            'product_ids' => ['required', 'array', 'min:1'],
            'product_ids.*' => ['integer', 'exists:products,id'],
        ]);

        $products = Product::with('productVariants')->whereIn('id', $validated['product_ids'])->get();
        $skipped = 0;

        foreach ($products as $product) {
            if ($validated['action'] === 'activate') {
                $product->update(['is_active' => true]);
            } elseif ($validated['action'] === 'deactivate') {
                $product->update(['is_active' => false]);
            } elseif ($validated['action'] === 'delete') {
                if ($product->orderItems()->exists()) {
                    $skipped++;
                    continue;
                }
                $imagesToDelete = $product->images ?? [];
                $imageSource = $product->image_source;
                DB::transaction(fn () => $product->delete());
                foreach ($imagesToDelete as $path) {
                    $imageStorage->delete($path, $imageSource);
                }
            }
        }

        Cache::forget('storefront_catalog_json');

        $message = 'Bulk action completed.';
        if ($skipped > 0) {
            $message .= " {$skipped} product(s) were skipped because they are referenced by existing orders.";
        }

        return redirect()->route('admin.products.index')->with('success', $message);
    }
}
