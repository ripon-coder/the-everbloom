<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\View\View;

class AccountController extends Controller
{
    /**
     * Account Overview / Dashboard
     * 
     * // BACKEND: replace with auth()->user(), $user->orders()->latest()->take(3)->get(), $user->addresses()->where('is_default', true)->first()
     */
    public function index(): View
    {
        // BACKEND: replace with real customer data from authenticated session
        $customer = config('demo.account.customer', [
            'first_name' => 'Eleanor',
            'last_name' => 'Vance',
            'email' => 'eleanor.vance@example.com',
            'phone' => '+1 (503) 555-0192',
        ]);

        // BACKEND: replace with real orders query
        $allOrders = config('demo.account.orders', []);
        $recentOrders = array_slice($allOrders, 0, 3);

        // BACKEND: replace with real default address query
        $addresses = config('demo.account.addresses', []);
        $defaultAddress = collect($addresses)->firstWhere('is_default', true) ?? ($addresses[0] ?? null);

        return view('frontend.account.index', [
            'customer' => $customer,
            'recentOrders' => $recentOrders,
            'defaultAddress' => $defaultAddress,
        ]);
    }

    /**
     * Order History List
     * 
     * // BACKEND: replace with $user->orders()->latest()->paginate(10)
     */
    public function orders(Request $request): View
    {
        // BACKEND: replace with database pagination and order filtering
        $orders = config('demo.account.orders', []);

        // Optional empty state demonstration via ?empty=1
        if ($request->query('empty')) {
            $orders = [];
        }

        return view('frontend.account.orders', [
            'orders' => $orders,
        ]);
    }

    /**
     * Order Details
     * 
     * // BACKEND: replace with $user->orders()->findOrFail($id)
     */
    public function showOrder(string $id): View
    {
        // BACKEND: replace with database lookup: Order::with('items.product')->where('order_number', $id)->firstOrFail()
        $orders = config('demo.account.orders', []);
        $order = collect($orders)->firstWhere('id', $id);

        if (!$order) {
            // Fallback for demonstration URLs: take the first order with requested ID
            $fallback = $orders[0] ?? null;
            if ($fallback) {
                $order = $fallback;
                $order['id'] = $id;
            } else {
                abort(404, 'Order not found');
            }
        }

        return view('frontend.account.order-details', [
            'order' => $order,
            'id' => $id,
        ]);
    }

    /**
     * Saved Addresses Management
     * 
     * // BACKEND: replace with $user->addresses()->get()
     */
    public function addresses(): View
    {
        // BACKEND: replace with real address records from database
        $addresses = config('demo.account.addresses', []);

        return view('frontend.account.addresses', [
            'addresses' => $addresses,
        ]);
    }

    /**
     * Account Profile and Password Change
     * 
     * // BACKEND: replace with $user profile data and password update action
     */
    public function profile(): View
    {
        // BACKEND: replace with real customer profile data
        $customer = config('demo.account.customer', [
            'first_name' => 'Eleanor',
            'last_name' => 'Vance',
            'email' => 'eleanor.vance@example.com',
            'phone' => '+1 (503) 555-0192',
        ]);

        return view('frontend.account.profile', [
            'customer' => $customer,
        ]);
    }

    /**
     * Customer Login View
     * 
     * // BACKEND: replace with Laravel Fortify / Breeze / Auth login controller
     */
    public function login(): View
    {
        return view('frontend.account.login');
    }

    /**
     * Customer Registration View
     * 
     * // BACKEND: replace with Laravel Fortify / Breeze / Auth registration controller
     */
    public function register(): View
    {
        return view('frontend.account.register');
    }
}
