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

    public function index(array $filters = [])
    {
        $query = District::query();

        if (!empty($filters['search'])) {
            $search = trim($filters['search']);
            $query->where(function($q) use ($search) {
                $q->where('name', 'LIKE', "%{$search}%")
                  ->orWhere('information', 'LIKE', "%{$search}%")
                  ->orWhere('delivery_charge', 'LIKE', "%{$search}%")
                  ->orWhere('id', $search);
            });
        }

        return $query->orderBy("name")->paginate(20)->withQueryString();
    }

    public function create() {}

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

    public function destroy(int $id) {}

    public function restore(int $id) {}

    public function forceDelete(int $id) {}

    //Api 
    public function districtList()
    {
        return District::orderBy("name")->get(['id', 'name', 'delivery_charge', 'information']);
    }

    public function getShippingCharge(int $districtId, float $shippingWeight, float $shippingFreeWeight): array
    {
        $district = $this->byId($districtId);

        $baseCharge = $district->delivery_charge ?? 0;

        $nearRate = (float) config('eccomerce.near_courier_charge_per_kg');
        $farRate = (float) config('eccomerce.far_courier_charge_per_kg');


        $rate = $district->have_our_shop ? $nearRate : $farRate;

        $userCharge = 0;
        $shop_charge = 0;

        $shippingWeight = ceil($shippingWeight);
        $shippingFreeWeight = ceil($shippingFreeWeight);

        if ($shippingWeight > 0) {
            if ($shippingWeight <= 1) {
                $userCharge = $baseCharge;
            } else {
                $extraWeight = $shippingWeight - 1;
                $userCharge = $baseCharge + ($extraWeight * $rate);
            }
        }

        if ($shippingFreeWeight > 0) {
            if ($shippingWeight > 0) {
                $shop_charge = $shippingFreeWeight * $rate;
            } else {
                if ($shippingFreeWeight <= 1) {
                    $shop_charge = $baseCharge;
                } else {
                    $extraWeight = $shippingFreeWeight - 1;
                    $shop_charge = $baseCharge + ($extraWeight * $rate);
                }
            }
        }

        return [
            'shipping_amount' => $userCharge,
            'admin_shipping_amount' => $shop_charge,
        ];
    }
}
