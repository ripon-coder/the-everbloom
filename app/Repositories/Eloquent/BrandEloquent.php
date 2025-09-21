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
    public function All()
    {
        return Brand::with("media")->orderByDesc("id")->paginate(20);
    }

    public function DeleteFindBuyId($id){
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
}
