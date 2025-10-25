<?php

namespace App\Repositories\Eloquent;

use App\Models\District;
use App\Repositories\Contracts\DistrictRepository;

class DistrictEloquent implements DistrictRepository
{

    public function byId($id)
    {
        return District::find($id);
    }

    public function index()
    {
        return District::orderBy("name")->paginate(20);
    }

    public function create()
    {
    }

    public function store(array $data)
    {
        return District::create($data);
    }

    public function edit(int $id)
    {
        return $this->byId($id);
    }

    public function update(int $id, array $data)
    {
        $district = $this->byId($id);
        return $district->update($data);
    }

    public function destroy(int $id)
    {
    }

    public function restore(int $id)
    {
    }

    public function forceDelete(int $id)
    {
    }

    //Api 
    public function districtList()
    {
        return District::orderBy("name")->get(['id', 'name', 'delivery_charge', 'information']);
    }

public function getShippingCharge(int $districtId, float $shippingWeight, float $shippingFreeWeight): array
{
    $district = $this->byId($districtId);

    $baseCharge = $district->delivery_charge ?? 0;

    $nearRate = (float) env('NEAR_COURIER_CHARGE_KG', 10);
    $farRate = (float) env('FAR_COURIER_CHARGE_KG', 15);

    $rate = $district->have_our_shop ? $nearRate : $farRate;

    $userCharge = 0;
    $shopCharge = 0;

    if ($shippingWeight > 0) {
        // user_charge = baseCharge + shippingWeight এর অতিরিক্ত weight
        $userCharge = round($baseCharge + max($shippingWeight - 1, 0) * $rate, 2);

        // shop_charge = shippingFreeWeight এর যে অংশ shippingWeight এর বাইরে
        $extraWeight = max(0, $shippingFreeWeight - $shippingWeight);
        $shopCharge = round($extraWeight * $rate, 2);

    } else {
        // shippingWeight = 0 → পুরো charge shop এর
        $shopCharge = round($baseCharge + max($shippingFreeWeight - 1, 0) * $rate, 2);
    }

    return [
        'user_charge' => $userCharge,
        'shop_charge' => $shopCharge,
    ];
}






}

