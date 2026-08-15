<?php 
namespace App\Repositories\Contracts;
interface CategoryRepository{
    public function FindById($id);
    public function AllWithPaginate(array $filters = []);
    public function DeleteFindBuyId($id);
    public function create(array $data);
    public function update($id, array $data);
    public function parentCategory();
    public function allCategory();
    public function toggleFeatured($id);
}
