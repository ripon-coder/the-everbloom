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
        return Product::with(['brand', 'category', 'variants'])->latest()->paginate(15);
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
        $data['product'] = Product::with(['brand', 'category', 'variants.variantAttributes.attribute', 'variants.variantAttributes.attributeValue', 'images', 'variants.images'])->findOrFail($id);
        $data['brands'] = Brand::active()->get(['id', 'name']);
        $data['categories'] = Category::active()->get();
        $data['attributes'] = Attribute::with('attributeValues')->get();
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
