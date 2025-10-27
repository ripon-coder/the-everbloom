<?php

namespace App\Http\Controllers\Api;

use Exception;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Auth;
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
        $userId = auth()->guard('sanctum')->id();
        $address_id = $request->input('address_id');
        $addresses = $this->saveAddressRepository->getAddress($address_id, $userId);
        return $this->successResponse($addresses, 'Addresses retrieved successfully');
    }
    public function SaveAddress(SaveAddressRequest $request)
    {
        $user_id = auth()->guard('sanctum')->id();
        $data = $request->validated();
        $address = $this->saveAddressRepository->store(array_merge($data, ['user_id' => $user_id]));
        return $this->successResponse($address, 'Address saved successfully');
    }

    public function DeleteAddress(Request $request)
    {
        $user_id = auth()->guard('sanctum')->id();
        try {
            $addressId = $request->input('address_id');
            if (!$addressId) {
                return $this->errorResponse('Address ID is required', 422);
            }
            $this->saveAddressRepository->destroy($addressId,$user_id);
            return $this->successResponse(null, 'Address deleted successfully');
        } catch (Exception $e) {
            return $this->errorResponse('Failed to delete address', 500, $e->getMessage());
        }
    }
}
