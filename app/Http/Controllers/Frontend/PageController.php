<?php

namespace App\Http\Controllers\Frontend;

use App\Http\Controllers\Controller;
use Illuminate\View\View;

class PageController extends Controller
{
    public function checkout(): View
    {
        $districts = \App\Models\District::orderBy('name')->get();
        $userAddresses = auth()->check() ? auth()->user()->addresses()->get() : collect();
        return view('pages.checkout.index', compact('districts', 'userAddresses'));
    }

    public function account(): View
    {
        $user = auth()->user();
        $addresses = $user->addresses()->with('district')->get();
        $districts = \App\Models\District::orderBy('name')->get();

        return view('pages.account.index', compact('user', 'addresses', 'districts'));
    }

    public function login(): View
    {
        return view('pages.auth.login');
    }

    public function register(): View
    {
        return view('pages.auth.register');
    }

    public function forgotPassword(): View
    {
        return view('pages.auth.forgot-password');
    }

    public function resetPassword(): View
    {
        return view('pages.auth.reset-password');
    }

    public function trackOrder(): View
    {
        return view('pages.track-order');
    }

    public function about(): View
    {
        return view('pages.info.about');
    }

    public function contact(): View
    {
        return view('pages.info.contact');
    }
}
