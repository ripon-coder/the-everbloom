<?php
namespace App\Repositories\Eloquent;

use RiponCoder\FileUpload\FileUpload;
use App\Repositories\Contracts\CategoryRespositoryInterface;

class CategoryRespository implements CategoryRespositoryInterface
{
    protected $model;
    public function __construct()
    {
        $this->model = new \App\Models\Category();
    }
    public function idBy($id)
    {
        return $this->model->where("id", $id)->first();
    }
    public function category()
    {
        return $this->model->active()->with('childrenRecursive')->whereNull('parent_id')->get();
    }
    public function pagination($limit = 20)
    {
        return $this->model->orderBy('id', 'desc')->paginate($limit);
    }
    public function store(array $data)
    {
        if (isset($data['thumbnail'])) {
            $data['thumbnail'] = FileUpload::path("dynamic-assets/category")->uploadFile($data['thumbnail']);
        }
        return $this->model->create($data);
    }
    public function update(array $data, $id)
    {
        $brand = $this->idBy($id);
        if (isset($data['thumbnail'])) {
            $data['thumbnail'] = FileUpload::path("dynamic-assets/category")->removeFile($brand->thumbnail ?? '')->uploadFile($data['thumbnail']);
        }
        return $brand->update($data);
    }
    public function destroy($id){
        $brand = $this->idBy($id);
        return $brand->delete();
    }
}