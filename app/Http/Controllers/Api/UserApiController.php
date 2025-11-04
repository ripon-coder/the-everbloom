<?php

namespace App\Http\Controllers\Api;

use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use App\Http\Requests\Api\UpdateUserRequest;
use App\Http\Resources\Api\UserResource;
use App\Models\User;
use App\Services\Api\UserServiceApi;
use App\Repositories\Contracts\UserRepository;

class UserApiController extends BaseApiController
{
    private $userRepository;
    public function __construct(UserRepository $userRepository)
    {
        $this->userRepository = $userRepository;
    }
    public function Login(Request $request)
    {
        $login = $this->userRepository->login($request->all());
        if($login){
           return $this->successResponse($login,"Login Successfully",200);
        }else{
           return $this->errorResponse("Invalid Credential",401);
        }
    }

    public function GetUser(){
        $user_id = auth()->guard('sanctum')->id();
        $user = UserResource::make($this->userRepository->GetUser($user_id));
        return $this->successResponse($user,"User Fetched Successfully",200);
    }

    public function UpdateUser(UpdateUserRequest $request){
        $user_id = auth()->guard('sanctum')->id();
        $user = app(UserServiceApi::class)->UserUpdate($user_id,$request->all());
        $this->successResponse($user,"User Updated Successfully",200);
    }
}
