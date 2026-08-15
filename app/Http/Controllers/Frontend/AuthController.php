<?php

namespace App\Http\Controllers\Frontend;

use App\Http\Controllers\Controller;
use App\Models\User;
use App\Models\Wishlist;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Illuminate\Validation\Rules;

class AuthController extends Controller
{
    public function postLogin(Request $request)
    {
        $loginInput = $request->input('login', $request->input('phone', $request->input('email')));
        $request->merge(['login' => $loginInput]);

        $request->validate([
            'login' => ['required', 'string'],
            'password' => ['required', 'string'],
        ]);

        $fieldType = filter_var($loginInput, FILTER_VALIDATE_EMAIL) ? 'email' : 'phone';

        if (Auth::attempt([$fieldType => $loginInput, 'password' => $request->password], $request->boolean('remember'))) {
            $request->session()->regenerate();

            if (!Auth::user()->is_active) {
                Auth::logout();
                $request->session()->invalidate();
                $request->session()->regenerateToken();
                return back()->withErrors([
                    'login' => 'Your account is currently inactive. Please contact support.',
                ])->onlyInput('login');
            }

            return redirect()->intended(route('home'))->with('success', 'You are logged in.');
        }

        return back()->withErrors([
            'login' => 'The provided credentials do not match our records.',
        ])->onlyInput('login');
    }

    public function postRegister(Request $request)
    {
        $request->validate([
            'first_name' => ['required', 'string', 'max:255'],
            'last_name' => ['required', 'string', 'max:255'],
            'email' => ['required_without:phone', 'nullable', 'string', 'email', 'max:255', 'unique:users,email'],
            'phone' => ['required_without:email', 'nullable', 'string', 'digits:11', 'unique:users,phone'],
            'password' => ['required', 'string', 'min:8', 'confirmed'],
            'terms' => ['required', 'accepted']
        ], [
            'email.required_without' => 'Please provide an email address or mobile number.',
            'phone.required_without' => 'Please provide a mobile number or email address.',
        ]);

        $user = User::create([
            'name' => trim($request->first_name . ' ' . $request->last_name),
            'email' => $request->email ?: null,
            'phone' => $request->phone ?: null,
            'password' => Hash::make($request->password),
            'is_active' => true,
        ]);

        Auth::login($user);

        return redirect()->route('home')->with('success', 'Registration successful.');
    }

    public function logout(Request $request)
    {
        Auth::logout();
        
        $request->session()->invalidate();
        $request->session()->regenerateToken();
        
        return redirect()->route('home')->with('success', 'You have been logged out.');
    }

    public function updateDetails(Request $request)
    {
        $user = Auth::user();
        
        $request->validate([
            'first_name' => ['required', 'string', 'max:255'],
            'last_name' => ['required', 'string', 'max:255'],
            'email' => ['nullable', 'string', 'email', 'max:255', 'unique:users,email,' . $user->id],
            'phone' => ['nullable', 'string', 'digits:11', 'unique:users,phone,' . $user->id],
            'current_password' => ['nullable', 'required_with:new_password', 'current_password'],
            'new_password' => ['nullable', 'string', 'min:8', 'confirmed'],
        ]);

        $user->name = trim($request->first_name . ' ' . $request->last_name);
        $user->email = $request->email ?: null;
        $user->phone = $request->phone ?: null;

        if ($request->filled('new_password')) {
            $user->password = Hash::make($request->new_password);
        }

        $user->save();

        return redirect()->back()->with('success', 'Account details updated successfully.');
    }
}
