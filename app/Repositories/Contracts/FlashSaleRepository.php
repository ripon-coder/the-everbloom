<?php

namespace App\Repositories\Contracts;

interface FlashSaleRepository
{
    public function index();

    public function create();

    public function store(array $data);

    public function edit(int $id);

    public function update(int $id, array $data);

    public function destroy(int $id);

    public function restore(int $id);

    public function forceDelete(int $id);

    public function getFlashSaleDiscounts(array $productIds);

    public function getFlashSaleProducts(array $data);
}
