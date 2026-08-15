<?php 
namespace App\Repositories\Contracts;

interface BrandRepository {
    public function FindById($id);
    public function All(array $filters = []);
    public function FindBySlug($slug);
    public function ActiveAllBrand();
    public function DeleteFindBuyId($id);
    public function create(array $data);
    public function update($id, array $data);
}
