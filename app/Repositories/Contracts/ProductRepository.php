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

    // Api
    public function shopProduct(array $data);

}
