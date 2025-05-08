<?php 
namespace App\Repositories\Contracts;
interface BrandRespositoryInterface{
    public function idBy($id);
    public function pagination($limit =20);
    public function store(array $data);
    public function update(array $data, $id);
    public function destroy($id);
}