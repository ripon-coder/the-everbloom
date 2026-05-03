<?php 
namespace App\Repositories\Contracts;
interface UserRepository{

    public function login(array $data);
    public function LogOut();
    public function CurrentUser();
    public function GetUser($user_id);
    public function UserUpdate($user_id,$data);
    public function ChangePassword($user_id,$data);
    public function getAllCustomers(array $filters = [], int $perPage = 15);


}