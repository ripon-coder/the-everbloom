<?php 
namespace App\Repositories\Contracts;
interface AttributeRespositoryInterface{
    public function idBy($id);
    public function attribute();
    public function pagination($limit =20);
    public function store(array $data);
    public function update(array $data, $id);
    public function destroy($id);
    public function getAttributeValueById($id);
}