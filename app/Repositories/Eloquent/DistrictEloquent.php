<?php

namespace App\Repositories\Eloquent;

use App\Models\District;
use App\Repositories\Contracts\DistrictRepository;

class DistrictEloquent implements DistrictRepository
{

    public function byId($id) {
        return District::findOrFail($id);
    }

    public function index() {
        return District::orderBy("name")->paginate(20);
    }

    public function create() {}

    public function store(array $data)
    {
        return District::create($data);
    }

    public function edit(int $id) {
        return $this->byId($id);
    }

    public function update(int $id, array $data) {
        $district = $this->byId($id);
        return $district->update($data);
    }

    public function destroy(int $id) {}

    public function restore(int $id) {}

    public function forceDelete(int $id) {}

    //Api 
    public function districtList(){
        return District::orderBy("name")->get(['id','name','delivery_charge','information']);
    }
}
