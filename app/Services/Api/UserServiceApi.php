<?php
namespace App\Services\Api;

use App\Repositories\Contracts\UserRepository;

class UserServiceApi
{
    protected $userRepository;
    public function __construct(UserRepository $userRepository)
    {
        $this->userRepository = $userRepository;
    }

    public function UserUpdate($user_id,$data)
    {
        return $this->userRepository->UserUpdate($user_id,$data);
    }
}