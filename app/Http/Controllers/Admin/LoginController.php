<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Repositories\Contracts\AdminRepositoryInterface;
use Illuminate\Http\Request;

class LoginController extends Controller
{
    protected $adminRepository;
    public function __construct(AdminRepositoryInterface $admin_repository){
        $this->adminRepository = $admin_repository;
    }
    public function index(){
        return view("admin.auth.login");
    }
    public function logedIn(Request $request){
        return $this->adminRepository->logedIn($request);
    }
}
