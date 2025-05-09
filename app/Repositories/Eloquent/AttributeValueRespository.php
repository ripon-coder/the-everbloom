<?php
namespace App\Repositories\Eloquent;

use App\AttributeEnum;
use App\Models\Attribute;
use RiponCoder\FileUpload\FileUpload;
use App\Repositories\Contracts\AttributeValueRespositoryInterface;

class AttributeValueRespository implements AttributeValueRespositoryInterface
{
    protected $model;
    public function __construct()
    {
        $this->model = new \App\Models\AttributeValue();
    }
    public function idBy($id)
    {
        return $this->model->where("id", $id)->first();
    }
    public function pagination($limit = 20)
    {
        return $this->model->with('attribute')->orderBy('id', 'desc')->paginate($limit);
    }
    public function store(array $data)
    {
        if (isset($data['thumbnail'])) {
            $data['thumbnail'] = FileUpload::path("dynamic-assets/brand")->uploadFile($data['thumbnail']);
        }
        return $this->model->create($data);
    }
    public function update(array $data, $id)
    {
        $attribute = $this->idBy($id);
        if (isset($data['thumbnail'])) {
            $data['thumbnail'] = FileUpload::path("dynamic-assets/brand")->removeFile($attribute->thumbnail ?? '')->uploadFile($data['thumbnail']);
        }
        return $attribute->update($data);
    }
    public function destroy($id)
    {
        $attribute = $this->idBy($id);
        return $attribute->delete();
    }
    public function attributes()
    {
        return Attribute::active()->orderByDesc("id")->get();
    }
}