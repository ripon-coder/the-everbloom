<?php
namespace App\Repositories\Eloquent;

use App\Models\Category;
use App\Repositories\Contracts\CategoryRepository;

class CategoryEloquent implements CategoryRepository
{


    public function FindById($id)
    {
        return Category::findOrFail($id);
    }
    public function FindBySlug($slug)
    {
        return Category::where("slug", $slug)->first();
    }
    public function AllWithPaginate(array $filters = [])
    {
        $query = Category::with([
            'parent:id,parent_id,name,slug,status,created_at',
            'children:id,parent_id,name,slug,status,created_at',
            'media'
        ]);

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

        if (isset($filters['is_featured']) && $filters['is_featured'] !== '') {
            $query->where('is_featured', $filters['is_featured']);
        }

        return $query->orderByDesc("id")->paginate(20, ['id','parent_id', 'name', 'slug', 'status', 'is_featured', 'created_at', 'image'])->withQueryString();
    }

    public function DeleteFindBuyId($id)
    {
        return $this->FindById($id)->delete();
    }

    public function create(array $data)
    {
        return Category::create(attributes: $data);
    }

    public function update($id, array $data)
    {
        $category = $this->FindById($id);
        $category->update($data);
        return $category;
    }
    public function parentCategory()
    {
        return Category::whereNull("parent_id")->active()->get(['id', 'name', 'slug', 'is_featured', 'image']);
    }
    public function allCategory()
    {
        return Category::with([
            'parent:id,name,slug',
            'children:id,parent_id,name,slug',
            'children.children:id,parent_id,name,slug',
            'children.children.children:id,parent_id,name,slug',
        ])
            ->active()
            ->get(['id', 'parent_id', 'name', 'slug', 'is_featured', 'image']);
    }


    public function toggleFeatured($id)
    {
        $category = $this->FindById($id);
        $category->update(['is_featured' => !$category->is_featured]);
        return $category;
    }


}
