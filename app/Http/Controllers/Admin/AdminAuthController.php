<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Admin;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use RuntimeException;

class AdminAuthController extends Controller
{
    public function showLogin()
    {
        if (Auth::guard('admin')->check()) {
            return redirect()->route('admin.dashboard');
        }

        return view('admin.auth.login');
    }

    public function login(Request $request)
    {
        $credentials = $request->validate([
            'email' => ['required', 'email'],
            'password' => ['required', 'string'],
        ]);

        $admin = Admin::where('email', $credentials['email'])
            ->where('is_active', true)
            ->first();

        if ($admin && $this->verifyAndUpgradePassword($admin, $credentials['password'])) {
            Auth::guard('admin')->login($admin, $request->boolean('remember'));
            $request->session()->regenerate();
            $admin->forceFill(['last_login_at' => now()])->save();

            return redirect()->intended(route('admin.dashboard'))
                ->with('success', 'Welcome to The Everbloom Admin.');
        }

        return back()->withErrors([
            'email' => 'Invalid admin credentials.',
        ])->onlyInput('email');
    }

    private function verifyAndUpgradePassword(Admin $admin, string $plainPassword): bool
    {
        $storedHash = (string) $admin->password;

        try {
            if (Hash::check($plainPassword, $storedHash)) {
                if (Hash::needsRehash($storedHash)) {
                    $admin->forceFill(['password' => $plainPassword])->save();
                }

                return true;
            }

            return false;
        } catch (RuntimeException) {
            // Support legacy $2a$ bcrypt hashes without crashing BcryptHasher algorithm verification.
            if (password_verify($plainPassword, $storedHash)) {
                $admin->forceFill(['password' => $plainPassword])->save();

                return true;
            }

            return false;
        }
    }

    public function logout(Request $request)
    {
        Auth::guard('admin')->logout();
        $request->session()->invalidate();
        $request->session()->regenerateToken();

        return redirect()->route('admin.login')->with('info', 'Logged out successfully.');
    }
}
