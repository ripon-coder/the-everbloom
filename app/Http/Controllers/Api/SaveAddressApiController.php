<?php

namespace App\Http\Controllers\Api;

use Exception;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use App\Http\Requests\Api\SaveAddressRequest;
use Illuminate\Validation\ValidationException;
use App\Repositories\Contracts\SaveAddressRepository;

class SaveAddressApiController extends BaseApiController
{
    private $saveAddressRepository;
    public function __construct(SaveAddressRepository $saveAddressRepository)
    {
        $this->saveAddressRepository = $saveAddressRepository;
    }

    public function GetAddress(Request $request)
    {
        $userId = 1;
        $address_id = $request->input('address_id');
        $addresses = $this->saveAddressRepository->getAddress($address_id, $userId);
        return $this->successResponse($addresses, 'Addresses retrieved successfully');
    }
    public function SaveAddress(SaveAddressRequest $request)
    {
        $data = $request->validated();
        $address = $this->saveAddressRepository->store(array_merge($data, ['user_id' => 1]));
        return $this->successResponse($address, 'Address saved successfully');
    }

    public function DeleteAddress(Request $request)
    {
        try {
            $addressId = $request->input('address_id');
            if (!$addressId) {
                return $this->errorResponse('Address ID is required', 422);
            }
            $this->saveAddressRepository->destroy($addressId);
            return $this->successResponse(null, 'Address deleted successfully');
        } catch (Exception $e) {
            return $this->errorResponse('Failed to delete address', 500, $e->getMessage());
        }
    }
}
