<?php

namespace App\Http\Controllers\Frontend;

use App\Http\Controllers\Controller;
use Illuminate\View\View;

class PageController extends Controller
{
    public function checkout(): View
    {
        return view('pages.checkout');
    }

    public function account(): View
    {
        return view('pages.account');
    }

    public function login(): View
    {
        return view('pages.login');
    }

    public function register(): View
    {
        return view('pages.register');
    }

    public function forgotPassword(): View
    {
        return view('pages.forgot-password');
    }

    public function resetPassword(): View
    {
        return view('pages.reset-password');
    }

    public function trackOrder(): View
    {
        return view('pages.track-order');
    }

    public function about(): View
    {
        return view('pages.about');
    }

    public function contact(): View
    {
        return view('pages.contact');
    }
}
