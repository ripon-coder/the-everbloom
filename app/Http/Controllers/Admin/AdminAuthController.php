<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;

class AdminAuthController extends Controller
{
    public function showLogin()
    {
        return view('admin.auth.login');
    }

    public function login(Request $request)
    {
        // BACKEND: Validate admin credentials & authenticate via admin guard
        return redirect()->route('admin.dashboard')->with('success', 'Welcome to The Everbloom Admin.');
    }

    public function logout(Request $request)
    {
        // BACKEND: Invalidate session & logout
        return redirect()->route('admin.login')->with('info', 'Logged out successfully.');
    }
}
