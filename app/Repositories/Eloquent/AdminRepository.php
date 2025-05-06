<?php
namespace App\Repositories\Eloquent;

use Illuminate\Support\Facades\Auth;
use App\Repositories\Contracts\AdminRepositoryInterface;

class AdminRepository implements AdminRepositoryInterface{
    public function logedIn($request){
        $credentials = $request->only("email","password");
        if (Auth::guard('admin')->attempt($credentials)) {
            return redirect()->route('admin.dashboard');
        }
        return back()->withErrors(['email' => 'Invalid credentials']);
    }
}