<?php
namespace App\Repositories\Eloquent;

use App\Models\Brand;
use App\Repositories\Contracts\BrandRepository;

class BrandEloquent implements BrandRepository
{
    public function FindById($id)
    {
        return Brand::findOrFail($id);
    }

    public function All(array $filters = [])
    {
        $query = Brand::with("media");

        if (!empty($filters['search'])) {
            $search = trim($filters['search']);
            $query->where(function($q) use ($search) {
                $q->where('name', 'LIKE', "%{$search}%")
                  ->orWhere('slug', 'LIKE', "%{$search}%")
                  ->orWhere('id', $search);
            });
        }

        if (!empty($filters['status'])) {
            $query->where('status', $filters['status']);
        }

        return $query->orderByDesc("id")->paginate(20)->withQueryString();
    }

    public function DeleteFindBuyId($id)
    {
        return $this->FindById($id)->delete();
    }

    public function create(array $data)
    {
        return Brand::create(attributes: $data);
    }

    public function update($id, array $data)
    {
        $brand = $this->FindById($id);
        $brand->update($data);
        return $brand;
    }

    public function FindBySlug($slug)
    {
        return Brand::where("slug", $slug)->first();
    }

    public function ActiveAllBrand()
    {
        return Brand::orderBy("name")->active()->get(['id', 'slug', 'name', 'image']);
    }
}
