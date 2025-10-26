<?php
namespace App\Repositories\Eloquent;

use App\Repositories\Contracts\UserRepository;
use Illuminate\Support\Facades\Auth;

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
}