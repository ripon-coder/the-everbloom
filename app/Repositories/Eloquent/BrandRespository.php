<?php
namespace App\Repositories\Eloquent;

use RiponCoder\FileUpload\FileUpload;
use App\Repositories\Contracts\BrandRespositoryInterface;

class BrandRespository implements BrandRespositoryInterface
{
    protected $model;
    public function __construct()
    {
        $this->model = new \App\Models\Brand();
    }
    public function idBy($id)
    {
        return $this->model->where("id", $id)->first();
    }
    public function pagination($limit =20){
        return $this->model->paginate($limit);
    }
    public function store(array $data)
    {
        if (isset($data['thumbnail'])) {
            $data['thumbnail'] = FileUpload::path("dynamic-assets/brand")->uploadFile($data['thumbnail']);
        }
        return $this->model->create($data);
    }
    public function update(array $data, $id){
        $brand = $this->idBy($id);
        if (isset($data['thumbnail'])) {
            $data['thumbnail'] = FileUpload::path("dynamic-assets/brand")->removeFile($brand->thumbnail ?? '')->uploadFile($data['thumbnail']);
        }
        return $brand->update($data);
    }
}