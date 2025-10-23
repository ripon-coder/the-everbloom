<?php

namespace App\Repositories\Contracts;

interface CouponRepository
{
    public function index();

    public function create();

    public function store(array $data);

    public function edit(int $id);

    public function update(int $id, array $data);

    public function destroy(int $id);

    public function restore(int $id);

    public function forceDelete(int $id);

    public function getDiscountAmount(string $couponCode, float $subTotal): float;

    public function usedCoupon(string $couponCode): void;
}
