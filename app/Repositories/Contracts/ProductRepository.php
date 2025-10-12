<?php 
namespace App\Repositories\Contracts;
interface ProductRepository{

    public function index();
    public function create();
    public function store(array $data);
    public function update(int $id, array $data);
    public function edit(int $id);
    public function destroy(int $id);
    public function restore(int $id);
    public function forceDelete(int $id);
    public function allChildrenByCategoryId(int $id);
    public function getCategoryWithSiblings(int $id);

    // Api
    public function shopProduct(?int $page, ?int $perPage, ?int $offset, array $data);
    public function ShopCategoryBrand(int $categoryId, array $categoryIds);
    public function ShopAttribute(array $categoryIds);

}
