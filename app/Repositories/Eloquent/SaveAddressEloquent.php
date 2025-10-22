<?php

namespace App\Repositories\Eloquent;

use App\Models\District;
use App\Models\SaveAddress;
use App\Repositories\Contracts\SaveAddressRepository;

class SaveAddressEloquent implements SaveAddressRepository
{

    public function byId($id)
    {

    }

    public function index()
    {

    }

    public function create()
    {
    }

    public function store(array $data)
    {
       return SaveAddress::create($data);
    }

    public function edit(int $id)
    {

    }

    public function update(int $id, array $data)
    {

    }

    public function destroy(int $id)
    {
        return SaveAddress::destroy($id);
    }

    public function restore(int $id)
    {
    }

    public function forceDelete(int $id)
    {
    }

    public function getAddress($address_id, $userId)
    {
        $query = SaveAddress::where('user_id', $userId);
        if (!empty($address_id)) {
            $query->where('id', $address_id);
        }
        return $query->get();
    }

}
