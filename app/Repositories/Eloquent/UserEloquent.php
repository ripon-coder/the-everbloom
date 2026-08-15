<?php

namespace App\Repositories\Eloquent;

use App\Models\User;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
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
    public function LogOut()
    {
        $user = auth()->guard('sanctum')->user();

        if ($user && $user->currentAccessToken()) {
            $user->currentAccessToken()->delete();
            return true;
        }
        return false;
    }

    public function CurrentUser()
    {
        $id = auth()->guard('sanctum')->id();
        return User::find($id);
    }
    public function GetUser($user_id)
    {
        return User::where('id', $user_id)->first();
    }
    public function UserUpdate($user_id, $data)
    {
        $user = User::find($user_id);
        $user->update($data);
        if (isset($data['profile_thumbnail']) && $data['profile_thumbnail'] != null) {
            $user->uploadImage($data['profile_thumbnail'], "profile_thumbnail");
        }
        return $user;
    }
    public function ChangePassword($user_id, $data)
    {
        $user = User::findOrFail($user_id);
        if (!Hash::check($data['current_password'], $user->password)) {
            return response()->json([
                'status' => false,
                'message' => 'Current password is incorrect.',
            ], 400);
        }

        if ($data['current_password'] === $data['new_password']) {
            return response()->json([
                'status' => false,
                'message' => 'New password cannot be the same as the old one.',
            ], 400);
        }
        $user->password = Hash::make($data['new_password']);
        $user->save();
        return response()->json([
            'status' => true,
            'message' => 'Password updated successfully.',
        ], 200);
    }

    public function getAllCustomers(array $filters = [], int $perPage = 15)
    {
        $query = User::withCount('orders');

        if (!empty($filters['search'])) {
            $search = trim($filters['search']);
            $query->where(function($q) use ($search) {
                $q->where('name', 'LIKE', "%{$search}%")
                  ->orWhere('email', 'LIKE', "%{$search}%")
                  ->orWhere('phone', 'LIKE', "%{$search}%")
                  ->orWhere('id', $search);
            });
        }

        if (isset($filters['status']) && $filters['status'] !== '') {
            $query->where('is_active', $filters['status']);
        }

        return $query->latest()->paginate($perPage)->withQueryString();
    }
}
