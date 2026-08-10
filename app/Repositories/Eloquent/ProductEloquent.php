<?php

namespace App\Repositories\Eloquent;

use App\Models\Brand;
use App\Models\Product;
use App\Models\Category;
use App\Models\Attribute;
use Illuminate\Support\Str;
use App\Models\ProductVariant;
use Illuminate\Support\Facades\DB;
use App\Constants\ProductVariantStatus;
use App\Services\Filter\Api\BaseProductFilter;
use App\Repositories\Contracts\BrandRepository;
use App\Repositories\Contracts\ProductRepository;

class ProductEloquent implements ProductRepository
{
    protected $productFilter;
    public function __construct(BaseProductFilter $productFilter)
    {
        $this->productFilter = $productFilter;
    }
    public function index(array $filters = [])
    {
        $query = Product::select([
            'id',
            'name',
            'slug',
            'product_type',
            'is_free_delivery',
            'status',
            'created_at',
            'brand_id',
            'category_id',
            'price',
            'is_featured'
        ])->with([
            'brand:id,name',
            'category:id,name',
            'firstImage.media',
            'anyImage.media',
        ])->withCount(['variants', 'images'])
          ->withSum('variants', 'stock');

        if (!empty($filters['search'])) {
            $search = trim($filters['search']);
            $query->where(function ($q) use ($search) {
                $q->where('name', 'like', "%{$search}%")
                  ->orWhere('slug', 'like', "%{$search}%")
                  ->orWhere('id', $search);
            });
        }

        if (!empty($filters['category_id'])) {
            $query->where('category_id', $filters['category_id']);
        }

        if (!empty($filters['brand_id'])) {
            $query->where('brand_id', $filters['brand_id']);
        }

        if (!empty($filters['status'])) {
            $query->where('status', $filters['status']);
        }

        if (!empty($filters['product_type'])) {
            $query->where('product_type', $filters['product_type']);
        }

        if (isset($filters['is_featured']) && $filters['is_featured'] !== '') {
            $query->where('is_featured', $filters['is_featured']);
        }

        $sortBy = $filters['sort_by'] ?? 'latest';
        switch ($sortBy) {
            case 'oldest':
                $query->orderBy('id', 'asc');
                break;
            case 'name_asc':
                $query->orderBy('name', 'asc');
                break;
            case 'name_desc':
                $query->orderBy('name', 'desc');
                break;
            case 'price_low':
                $query->orderByRaw('COALESCE(NULLIF(products.price, 0), (SELECT MIN(pv.sell_price) FROM product_variants pv WHERE pv.product_id = products.id AND pv.deleted_at IS NULL), 0) ASC');
                break;
            case 'price_high':
                $query->orderByRaw('COALESCE(NULLIF(products.price, 0), (SELECT MAX(pv.sell_price) FROM product_variants pv WHERE pv.product_id = products.id AND pv.deleted_at IS NULL), 0) DESC');
                break;
            case 'latest':
            default:
                $query->latest('id');
                break;
        }

        return $query->paginate(15)->withQueryString();
    }

    public function create()
    {
        $data['brands'] = Brand::active()->get(['id', 'name']);
        $data['categories'] = Category::with(['parent:id,parent_id,name', 'children:id,parent_id,name'])->active()->get(['id', 'parent_id', 'name']);
        $data['attributes'] = Attribute::with('attributeValues:id,attribute_id,value')->get(["id", "name", "is_image"]);
        return $data;
    }

    public function store(array $data)
    {
        return Product::create($data);
    }

    public function edit(int $id)
    {
        $data['product'] = Product::with([
            'brand:id,name',
            'category:id,name',
            'variants:id,product_id,sku,buying_price,sell_price,discount_price,discount_amount,stock,weight,status',
            'variants.variantAttributes:id,product_variant_id,attribute_id,attribute_value_id',
            'variants.variantAttributes.attribute:id,name',
            'variants.variantAttributes.attributeValue:id,attribute_id,value',
            'images',
            'images.media',
            'variants.images:id,product_variant_id,is_default,image'
        ])->withCount(['variants', 'images'])->findOrFail($id);

        $data['brands'] = Brand::active()->get(['id', 'name']);
        $data['categories'] = Category::active()->select(['id', 'parent_id', 'name'])
            ->with(['parent:id,name', 'children:id,parent_id,name'])
            ->withCount(['children'])
            ->get();
        $data['attributes'] = Attribute::select(['id', 'name', 'status'])
            ->with('attributeValues:id,attribute_id,value,status')
            ->get();

        return $data;
    }


    public function update(int $id, array $data)
    {
        $product = Product::findOrFail($id);

        // Update slug if name changed
        if (isset($data['name']) && $data['name'] !== $product->name) {
            $slug = Str::slug($data['name']);

            // Ensure unique slug
            $originalSlug = $slug;
            $counter = 1;
            while (Product::where('slug', $slug)->where('id', '!=', $product->id)->exists()) {
                $slug = $originalSlug . '-' . $counter;
                $counter++;
            }

            $data['slug'] = $slug;
        }

        $product->update($data);
        return $product;
    }

    public function destroy(int $id)
    {
        $product = Product::findOrFail($id);
        return $product->delete();
    }

    public function restore(int $id)
    {
        $product = Product::withTrashed()->findOrFail($id);
        return $product->restore();
    }

    public function forceDelete(int $id)
    {
        $product = Product::withTrashed()->findOrFail($id);
        return $product->forceDelete();
    }

    public function allChildrenByCategoryId(int $id)
    {
        $ids = Category::where('parent_id', $id)->pluck('id')->toArray();
        foreach ($ids as $childId) {
            $ids = array_merge($ids, $this->allChildrenByCategoryId($childId));
        }
        return $ids;
    }
    public function getCategoryWithSiblings(int $id)
    {
        $category = Category::with('children')->find($id);

        if (!$category)
            return [];

        if ($category->children->isNotEmpty()) {
            return array_merge([$id], $this->allChildrenByCategoryId($id));
        } elseif ($category->parent_id) {
            $siblings = Category::where('parent_id', $category->parent_id)->pluck('id')->toArray();
            return array_merge([$id], $siblings);
        }
        return [$id];
    }

    public function shopProduct(?int $page, ?int $perPage, ?int $offset, array $dataRecive)
    {
        $query = Product::active()
            ->with(['firstImage.media'])
            ->whereHas('variants', function ($q) {
                $q->where('status', ProductVariantStatus::ACTIVE);
            });

        $filterQuery = $this->productFilter->getResults(contents: ['query' => $query, 'filter' => $dataRecive]);

        $total = (clone $filterQuery)->count();

        $query = $filterQuery->skip($offset)
            ->take($perPage);

        $data["products"] = $query->get(['id', 'name', 'price', 'slug']);
        $data['total'] = $total;
        return $data;
    }

    public function ShopCategoryBrand(?int $categoryId, array $categoryIds)
    {

        $query = Category::with([
            'parent:id,name,slug',
            'children:id,parent_id,name,slug',
            'children.children:id,parent_id,name,slug',
            'children.children.children:id,parent_id,name,slug'
        ])
            ->active();

        if (!empty($categoryIds)) {
            $query->whereIn('id', $categoryIds);
        } else {
            $query->where('id', $categoryId);
        }

        $OutData["categories"] = $query->get(['id', 'parent_id', 'name', 'slug']);

        $OutData["brands"] = app(BrandRepository::class)->ActiveAllBrand();

        return $OutData;
    }

    public function ShopAttribute(array $categoryIds)
    {
        $attributesQuery = Attribute::with([
            'attributeValues' => function ($q) use ($categoryIds) {
                $q->select('id', 'attribute_id', 'value')
                    ->when(!empty($categoryIds), function ($query) use ($categoryIds) {
                        $query->whereHas('variantAttributes.productVariant.product', function ($q2) use ($categoryIds) {
                            $q2->whereIn('category_id', $categoryIds);
                        });
                    });
            }
        ])
            ->when(!empty($categoryIds), function ($query) use ($categoryIds) {
                $query->whereHas('attributeValues.variantAttributes.productVariant.product', function ($q3) use ($categoryIds) {
                    $q3->whereIn('category_id', $categoryIds);
                });
            });

        $attributes = $attributesQuery->get(['id', 'name']);
        return ['attributes' => $attributes];
    }

    public function Product($data)
    {
        $query = Product::active()
            ->with([
                'images',
                'variants:id,product_id,sku,buying_price,sell_price,discount_price,discount_amount,stock,weight',
                'variants.images',
                'variants.variantAttributes:id,product_variant_id,attribute_id,attribute_value_id',
                'variants.variantAttributes.attribute:id,name,description,is_image',
                'variants.variantAttributes.attributeValue:id,attribute_id,value',
                'variants.product'
            ]);

        if (auth()->guard('sanctum')->check()) {
            $userId = auth()->guard('sanctum')->id();
            $query->withExists([
                'wishlists as is_wishlisted' => function ($q) use ($userId) {
                    $q->where('user_id', $userId);
                }
            ]);
        }

        if (!empty($data['slug'])) {
            $query->where('slug', $data['slug']);
        }

        if (!empty($data['flashsale'])) {
            $flashsale = $data['flashsale'];
            $query->whereHas('flashSales', function ($query) use ($flashsale) {
                $query->where('slug', $flashsale)
                    ->where('status', \App\Constants\FlashSaleStatus::ACTIVE)
                    ->where('start_date', '<=', now())
                    ->where('end_date', '>=', now());
            })
                ->with(['flashSales', 'variants.product.flashSales']);
        }

        return $query->get([
            'id',
            'category_id',
            'brand_id',
            'name',
            'description',
            'short_description',
            'is_free_delivery',
            'price',
            'slug',
        ]);
    }


    public function Variant(array $data)
    {
        $outData = [];
        if (!empty($data['variant_id'])) {
            $outData = ProductVariant::active()->where('id', $data['variant_id'])->with([
                'images',
                'variantAttributes:id,product_variant_id,attribute_id,attribute_value_id',
                'variantAttributes.attribute:id,name,description,is_image',
                'variantAttributes.attributeValue:id,attribute_id,value'
            ])->get();
        }
        return $outData;
    }

    public function getProducts(array $ids, array $fetch)
    {
        return Product::whereIn("id", $ids)->get($fetch)->toArray();
    }

    public function getVariants(array $ids, array $fetch)
    {
        return ProductVariant::whereIn('id', $ids)->get($fetch)->toArray();
    }
    // public function getVariantsWithAttribute(array $ids)
    // {
    //     return ProductVariant::active()->whereIn('id', $ids)->with([
    //         'images',
    //         'variantAttributes:id,product_variant_id,attribute_id,attribute_value_id',
    //         'variantAttributes.attribute:id,name,description,is_image',
    //         'variantAttributes.attributeValue:id,attribute_id,value'
    //     ])->get();
    // }


    public function getVariantsWithAttribute(array $data)
    {
        // সব variant ID বের করে flat array বানাও
        $ids = collect($data)
            ->pluck('variants_id')
            ->flatten()
            ->filter()
            ->unique()
            ->values()
            ->toArray();

        // Variant + Product + FlashSale preload করো
        $variants = ProductVariant::active()
            ->whereIn('id', $ids)
            ->with([
                'images',
                'variantAttributes:id,product_variant_id,attribute_id,attribute_value_id',
                'variantAttributes.attribute:id,name,description,is_image',
                'variantAttributes.attributeValue:id,attribute_id,value',
                'product.flashSales',
            ])
            ->get();

        // প্রতিটা variant এর দাম নির্ধারণ করো
        return $variants->map(function ($variant) use ($data) {
            $item = collect($data)->firstWhere('variants_id', (string) $variant->id);
            $flashSaleSlug = $item['flash_sale'] ?? null;

            $originalPrice = $variant->sell_price;
            $salePrice = $variant->discount_price ?? $originalPrice;

            $discountPercentage = null;

            if ($flashSaleSlug && $variant->product?->flashSales?->isNotEmpty()) {
                // flash sale slug match
                $flashSale = $variant->product->flashSales->firstWhere('slug', $flashSaleSlug);

                if ($flashSale) {
                    $discountPrice = $flashSale->pivot->discount_price;
                    $discountPercentage = $flashSale->pivot->discount_percentage;

                    // discount calculation
                    if ($discountPrice) {
                        $salePrice = $discountPrice; // fixed price
                    } elseif ($discountPercentage) {
                        $salePrice = $originalPrice - ($originalPrice * ($discountPercentage / 100));
                    }
                }
            }

            // Model এর ওপর dynamic attributes বসাও
            $variant->has_flash_sale = $flashSaleSlug ? true : false;
            $variant->discount_price = round($salePrice, 2);
            $variant->discount_percentage = $discountPercentage;

            return $variant;
        });
    }



    public function getProductInfo(int $id, array $fetch)
    {
        return Product::select($fetch)->find($id);
    }
    public function getVariantInfo(int $productId, ?int $variantId)
    {
        $query = ProductVariant::where('product_id', $productId);
        if (!is_null($variantId)) {
            $query->where('id', $variantId);
        }
        return $query->first();
    }

    public function justForYouProducts(?int $page, ?int $perPage, ?int $offset, array $data)
    {
        $categoryIds = (!empty($data['category_ids']) && is_array($data['category_ids']))
            ? $data['category_ids']
            : [];

        $baseQuery = Product::active()
            ->with(['firstImage.media'])
            ->whereHas('variants', function ($q) {
                $q->where('status', ProductVariantStatus::ACTIVE);
            });

        $results = collect();

        /*
    |--------------------------------------------------------------------------
    | 1. Category Products
    |--------------------------------------------------------------------------
    */
        $categoryProducts = (clone $baseQuery)
            ->when(!empty($categoryIds), function ($q) use ($categoryIds) {
                $q->whereIn('category_id', $categoryIds);
            })
            ->get(['id', 'name', 'price', 'slug']);

        $results = $results->merge($categoryProducts);

        /*
    |--------------------------------------------------------------------------
    | 2. Popular Products (if category < 30)
    |--------------------------------------------------------------------------
    */
        if (count($categoryIds) < 30) {
            $popularProducts = (clone $baseQuery)
                ->popular()
                ->get(['id', 'name', 'price', 'slug']);

            $results = $results->merge($popularProducts);
        }

        /*
    |--------------------------------------------------------------------------
    | 3. Extra Random Products (if total < 50)
    |--------------------------------------------------------------------------
    */
        if ($results->count() < 50) {
            $needed = 50 - $results->count();

            $randomProducts = (clone $baseQuery)
                ->whereNotIn('id', $results->pluck('id'))
                ->inRandomOrder()
                ->limit($needed)
                ->get(['id', 'name', 'price', 'slug']);

            $results = $results->merge($randomProducts);
        }

        /*
    |--------------------------------------------------------------------------
    | Remove Duplicate Items
    |--------------------------------------------------------------------------
    */
        $results = $results->unique('id')->values();

        /*
    |--------------------------------------------------------------------------
    | Pagination
    |--------------------------------------------------------------------------
    */
        $total = $results->count();

        $products = $results
            ->slice($offset, $perPage)
            ->values();

        return [
            "products" => $products,
            "pagination" => [
                "current_page" => $page,
                "per_page" => $perPage,
                "total" => $total,
                "last_page" => ceil($total / $perPage),
            ]
        ];
    }
}
