<?php 
namespace App\Repositories\Contracts;
interface CategoryRepository{
    public function FindById($id);
    public function FindBySlug($slug);
    public function AllWithPaginate();
    public function DeleteFindBuyId($id);
    public function create(array $data);
    public function update($id, array $data);
    public function parentCategory();
    public function allCategory();
}
