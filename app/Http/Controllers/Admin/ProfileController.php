<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Illuminate\Validation\Rules\Password;

class ProfileController extends Controller
{
    /**
     * Show the profile edit form.
     */
    public function edit()
    {
        $admin = Auth::guard('admin')->user();
        return view('admin.profile.edit', compact('admin'));
    }

    /**
     * Update the admin profile.
     */
    public function update(Request $request)
    {
        /** @var \App\Models\Admin $admin */
        $admin = Auth::guard('admin')->user();

        $validated = $request->validate([
            'name' => ['required', 'string', 'max:255'],
            'email' => ['required', 'string', 'email', 'max:255', 'unique:admins,email,' . $admin->id],
            'mobile' => ['nullable', 'string', 'max:20'],
            'current_password' => ['nullable', 'required_with:password', 'current_password:admin'],
            'password' => ['nullable', 'confirmed', Password::defaults()],
        ]);

        $admin->name = $validated['name'];
        $admin->email = $validated['email'];
        $admin->mobile = $validated['mobile'];

        if ($request->filled('password')) {
            $admin->password = Hash::make($validated['password']);
        }

        $admin->save();

        return back()->with('success', 'Profile updated successfully.');
    }
}
