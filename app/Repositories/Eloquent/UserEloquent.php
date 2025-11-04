<?php

namespace App\Repositories\Eloquent;

use App\Models\User;
use Illuminate\Support\Facades\Auth;
use App\Repositories\Contracts\UserRepository;

class UserEloquent implements UserRepository
{
    public function login($data)
    {
        $login = $data['login'];
        $password = $data['password'];
        $remember = $data['remember'];
        $login_type = filter_var($login, FILTER_VALIDATE_EMAIL) ? 'email' : 'phone';
        if (!Auth::attempt([$login_type => $login, 'password' => $password], $remember)) {
            return false;
        }
        $user = Auth::user();
        $tokenName = 'auth_token' . ($remember ? '_long' : '');
        $token = $user->createToken($tokenName)->plainTextToken;
        return [
            'access_token' => $token,
            'token_type' => 'Bearer',
            'user' => $user,
        ];
    }
    public function CurrentUser()
    {
        $id = auth()->guard('sanctum')->id();
        return User::find($id);
    }
    public function GetUser($user_id){
        return User::where('id', $user_id)->first();
    }
    public function UserUpdate($user_id, $data)
    {
        $user = User::find($user_id);
        $user->update($data);
        if ($data['profile_thumbnail'] != null) {
            $user->uploadImage($data['profile_thumbnail'], "profile_thumbnail");
        }
        return $user;
    }
}
