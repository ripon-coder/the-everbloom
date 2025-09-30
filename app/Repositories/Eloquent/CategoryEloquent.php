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
    public function All()
    {
        return Category::with("media")->orderByDesc("id")->paginate(15);
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
        return Category::with('media')->whereNull("parent_id")->active()->get(['id', 'name', 'slug']);
    }

    public function allCategory()
    {
return Category::with([
        'parent:id,name,slug',
        'children:id,parent_id,name,slug'
    ])
    ->active()
    ->get(['id','parent_id','name','slug']);
    }
}
