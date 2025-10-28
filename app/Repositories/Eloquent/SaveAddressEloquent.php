<?php

namespace App\Repositories\Eloquent;

use App\Models\District;
use App\Models\SaveAddress;
use App\Repositories\Contracts\SaveAddressRepository;

class SaveAddressEloquent implements SaveAddressRepository
{

    public function byId($id) {}

    public function index() {}

    public function create() {}

    public function store(array $data)
    {
        $user_id = $data['user_id'];
        $count = SaveAddress::where('user_id', $user_id)->count();
        if ($count < 3) {
            return SaveAddress::create($data);
        }
        throw new \Exception('You can not add more than 3 addresses');
    }

    public function edit(int $id) {}

    public function update(int $id, array $data) {}

    public function destroy(int $id, $user_id)
    {
        return SaveAddress::where('user_id', $user_id)->where('id', $id)->delete();
    }

    public function restore(int $id) {}

    public function forceDelete(int $id) {}

    public function getAddress($address_id, $userId)
    {
        $query = SaveAddress::where('user_id', $userId)->with('district:id,name,information')->orderBy('id', 'desc');
        if (!empty($address_id)) {
            $query->where('id', $address_id);
        }
        return $query->get(['id', 'name', 'phone_number', 'district_id', 'zone', 'address', 'type_address']);
    }
}
