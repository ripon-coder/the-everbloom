<?php
namespace App\Repositories\Eloquent;

use Illuminate\Support\Facades\Auth;
use App\Repositories\Contracts\AdminRepositoryInterface;

class AdminRepository implements AdminRepositoryInterface{
    public function logedIn($request){
        $credentials = $request->only("email","password");
        $remember = $request->filled('remember');

        if (Auth::guard('admin')->attempt($credentials, $remember)) {
            return redirect()->route('admin.dashboard');
        }
        return back()->withErrors(['email' => 'Invalid credentials']);
    }

    public function logOut(){
        return Auth::guard('admin')->logout();
    }
}