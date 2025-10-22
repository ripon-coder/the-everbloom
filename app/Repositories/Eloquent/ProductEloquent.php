<?php

namespace App\Repositories\Eloquent;

use App\Models\Brand;
use App\Models\Product;
use App\Models\Category;
use App\Models\Attribute;
use App\Models\ProductVariant;
use App\Repositories\Contracts\BrandRepository;
use Illuminate\Support\Str;
use Illuminate\Support\Facades\DB;
use App\Repositories\Contracts\ProductRepository;
use App\Services\Filter\Api\ProductFilter;

class ProductEloquent implements ProductRepository
{
    protected $productFilter;
    public function __construct(ProductFilter $productFilter)
    {
        $this->productFilter = $productFilter;
    }
    public function index()
    {
        return Product::select([
            'id',
            'name',
            'status',
            'created_at',
            'brand_id',
            'category_id',
            'price'
        ])->with([
                    'brand:id,name',
                    'category:id,name',
                    'firstImage.media'
                ])->withCount(['variants', 'images'])
            ->latest()
            ->paginate(15);
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
            'images:id,product_id,is_default',
            'variants.images:id,product_variant_id,is_default'
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
            ->with(relations: 'firstImage.media')
            ->orderByDesc("updated_at");

        $filterQuery = $this->productFilter->getResults(contents: ['query' => $query, 'filter' => $dataRecive]);

        $total = (clone $filterQuery)->count();

        $query = $filterQuery->skip($offset)
            ->take(value: $perPage);

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
        if (!empty($data['slug'])) {
            $slug = $data['slug'];
            return Product::active()->where("slug", $slug)->with([
                'images',
                'variants:id,product_id,sku,buying_price,sell_price,discount_price,discount_amount,stock,weight',
                'variants.images',
                'variants.variantAttributes:id,product_variant_id,attribute_id,attribute_value_id',
                'variants.variantAttributes.attribute:id,name,description,is_image',
                'variants.variantAttributes.attributeValue:id,attribute_id,value'
            ])->get(['id', 'name', 'description', 'price', 'slug']);
        }
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
    public function getVariantInfo(int $productId, ?int $variantId)
    {
        $query = ProductVariant::where('product_id', $productId);

        if (!is_null($variantId)) {
            $query->where('id', $variantId);
        }

        return $query->first();
    }
}