<?php
namespace App\Repositories\Contracts;
use App\Models\Admin;
interface AdminRepositoryInterface{
    public function login(Admin $admin);
}