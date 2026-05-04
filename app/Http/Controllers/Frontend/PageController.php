<?php

namespace App\Http\Controllers\Frontend;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\View\View;

class PageController extends Controller
{
    public function checkout(): View
    {
        $districts = \App\Models\District::orderBy('name')->get();
        $userAddresses = auth()->check() ? auth()->user()->addresses()->get() : collect();
        return view('pages.checkout.index', compact('districts', 'userAddresses'));
    }

    public function account(string $section = 'dashboard'): View
    {
        $user = auth()->user();
        $addresses = $user->addresses()->with('district')->get();
        $districts = \App\Models\District::orderBy('name')->get();
        $orders = $user->orders()->latest()->get();
        $wishlist = $user->wishlists()->with('product.firstImage')->latest()->paginate(20);

        return view('pages.account.index', compact('user', 'addresses', 'districts', 'section', 'orders', 'wishlist'));
    }

    public function orderShow(string $orderNumber): View
    {
        $user = auth()->user();
        $order = $user->orders()->where('order_number', $orderNumber)
            ->with(['orderProducts.product', 'orderAddress.district'])
            ->firstOrFail();

        return view('pages.account.order-show', compact('user', 'order'));
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

    public function submitContact(Request $request)
    {
        $request->validate([
            'name' => 'required|string|max:255',
            'email' => 'required|email|max:255',
            'subject' => 'nullable|string|max:255',
            'message' => 'required|string',
        ]);

        \App\Models\ContactMessage::create($request->all());

        return redirect()->back()->with('success', 'Your message has been sent successfully. We will get back to you soon!');
    }

    public function dynamicPage(string $slug): View
    {
        $page = \App\Models\Page::where('slug', $slug)->where('is_active', true)->firstOrFail();
        return view('pages.dynamic', compact('page'));
    }
}
