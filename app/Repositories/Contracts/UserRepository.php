<?php 
namespace App\Repositories\Contracts;
interface UserRepository{
    public function login(array $data);
}