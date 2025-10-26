<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Repositories\Contracts\UserRepository;
use Illuminate\Http\Request;

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
}
