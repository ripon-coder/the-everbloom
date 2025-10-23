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

    public function getShippingCharge(int $districtId, float $totalWeight): float
    {
        if($totalWeight == 0){
            return 0.0;
        }
        $district = $this->byId($districtId);

        $baseCharge = $district->delivery_charge ?? 0;

        $nearRate = (float) env('NEAR_COURIER_CHARGE_KG', 10);
        $farRate = (float) env('FAR_COURIER_CHARGE_KG', 15);

        $weightCharge = 0;

        if ($district->have_our_shop) {
            if ($totalWeight > 1) {
                $extraWeight = $totalWeight - 1;
                $weightCharge = $extraWeight * $nearRate;
            }
        } else {
            if ($totalWeight > 1) {
                $extraWeight = $totalWeight - 1;
                $weightCharge = $extraWeight * $farRate;
            }
        }

        return round($baseCharge + $weightCharge, 2);
    }
}

