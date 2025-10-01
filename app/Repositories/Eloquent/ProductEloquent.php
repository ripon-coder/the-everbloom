<?php
namespace App\Repositories\Eloquent;

use App\Models\Brand;
use App\Models\Product;
use App\Models\Category;
use App\Models\Attribute;
use Illuminate\Support\Str;
use Illuminate\Support\Facades\DB;
use App\Repositories\Contracts\ProductRepository;

class ProductEloquent implements ProductRepository
{

    public function index()
    {
        return Product::select('id', 'name', 'status', 'created_at', 'brand_id', 'category_id')->with(['brand:id,name', 'category:id,name', 'firstImage.media'])->withCount("variants")->latest()->paginate(15);
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

    public function shopProduct(array $dataRecive)
    {
        $page = $dataRecive['current_page'] ?? 1;
        $perPage = $dataRecive['per_page'] ?? 20;
        $offset = ($page - 1) * $perPage;

        $data['products'] = Product::active()
            ->with(relations: 'firstImage.media')
            ->skip($offset)
            ->take(value: $perPage)
            ->orderByDesc("updated_at")
            ->get(['id', 'name', 'price', 'slug']);
        $total = Product::active()->count();

        $data['pagination'] = [
            "current_page" => $page,
            "per_page" => $perPage,
            "total" => $total,
            "last_page" => ceil($total / $perPage),
        ];
        return $data;
    }

    public function ShopFilter(array $data)
    {
        $category_slug = $data['category'] ?? null;
        $category_id = Category::where("slug", $category_slug)->value("id");
        $OutData = [
            "attributes" => [],
            "categories" => [],
            "brands" => []
        ];

        $query = Category::with(['parent:id,name,slug', 'children'])
            ->active();

        if ($category_id) {
            $categoryIds = $this->getCategoryWithSiblingLogic($category_id);
            $query->whereIn('id', $categoryIds);
        }

        $OutData["categories"] = $query->get(['id', 'parent_id', 'name', 'slug']);

        $OutData["brands"] = Brand::orderBy("name")->active()->get(['id', 'slug', 'name']);



        return $OutData;
    }

    public function ShopAttribute(array $data)
    {
        $category_id = null;

        if (!empty($data['category_id'])) {
            $category_id = $data['category_id'];
        } elseif (!empty($data['category'])) {
            $category_id = Category::where('slug', $data['category'])->value('id');
        }

        $OutData = [
            "attributes" => []
        ];

        if ($category_id) {
            // Get category + children/sibling ids
            $categoryIds = $this->getCategoryWithSiblingLogic($category_id);

            // Fetch attributes used only in products of these categories
            $attributesQuery = Attribute::with([
                'attributeValues' => function ($q) use ($categoryIds) {
                    $q->select('id', 'attribute_id', 'value')
                        ->whereHas('variantAttributes.productVariant.product', function ($q2) use ($categoryIds) {
                            $q2->whereIn('category_id', $categoryIds);
                        });
                }
            ])->whereHas('attributeValues.variantAttributes.productVariant.product', function ($q) use ($categoryIds) {
                $q->whereIn('category_id', $categoryIds);
            });

            $OutData["attributes"] = $attributesQuery->get(['id', 'name']);
        }

        return $OutData;
    }




    private function getAllChildrenIds($parent_id)
    {
        $ids = Category::where('parent_id', $parent_id)->pluck('id')->toArray();

        foreach ($ids as $childId) {
            $ids = array_merge($ids, $this->getAllChildrenIds($childId));
        }

        return $ids;
    }

    private function getCategoryWithSiblingLogic($category_id)
    {
        $category = Category::with('children')->find($category_id);

        if (!$category)
            return [];

        if ($category->children->isNotEmpty()) {
            return array_merge([$category_id], $this->getAllChildrenIds($category_id));
        } elseif ($category->parent_id) {
            $siblings = Category::where('parent_id', $category->parent_id)->pluck('id')->toArray();
            return array_merge([$category_id], $siblings);
        }
        return [$category_id];
    }






}
