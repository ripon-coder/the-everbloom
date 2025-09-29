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
            'variants:id,product_id,sku,buying_price,sell_price,discount_price,discount_amount,stock,status',
            'variants.variantAttributes:id,product_variant_id,attribute_id,attribute_value_id',
            'variants.variantAttributes.attribute:id,name',
            'variants.variantAttributes.attributeValue:id,attribute_id,value',
            'images:id,product_id,is_default',
            'variants.images:id,product_variant_id,is_default'
        ])->withCount(['variants','images'])->findOrFail($id);

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
}
