<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;

class AdminCustomerController extends Controller
{
    private function getDemoCustomers()
    {
        return [
            [
                'id' => 1,
                'name' => 'Eleanor Vance',
                'email' => 'eleanor.vance@example.com',
                'phone' => '+1 (503) 555-0192',
                'avatar' => 'admin/assets/images/users/avatar-2.jpg',
                'orders_count' => 4,
                'total_spent' => 284.00,
                'city' => 'Portland, OR',
                'status' => 'Active',
                'join_date' => 'October 14, 2025',
            ],
            [
                'id' => 2,
                'name' => 'Sophia Laurent',
                'email' => 'sophia.laurent@example.com',
                'phone' => '+1 (415) 555-0811',
                'avatar' => 'admin/assets/images/users/avatar-3.jpg',
                'orders_count' => 7,
                'total_spent' => 540.00,
                'city' => 'San Francisco, CA',
                'status' => 'Active',
                'join_date' => 'July 22, 2025',
            ],
            [
                'id' => 3,
                'name' => 'Emma Chen',
                'email' => 'emma.chen@example.com',
                'phone' => '+1 (206) 555-0348',
                'avatar' => 'admin/assets/images/users/avatar-4.jpg',
                'orders_count' => 3,
                'total_spent' => 192.00,
                'city' => 'Seattle, WA',
                'status' => 'Active',
                'join_date' => 'January 09, 2026',
            ],
            [
                'id' => 4,
                'name' => 'Lucas Wright',
                'email' => 'lucas.wright@example.com',
                'phone' => '+1 (312) 555-0729',
                'avatar' => 'admin/assets/images/users/avatar-5.jpg',
                'orders_count' => 2,
                'total_spent' => 128.00,
                'city' => 'Chicago, IL',
                'status' => 'Inactive',
                'join_date' => 'August 30, 2025',
            ],
        ];
    }

    public function index()
    {
        // BACKEND: User::where('role', 'customer')->withCount('orders')->paginate(15)
        $customers = $this->getDemoCustomers();
        return view('admin.customers.index', compact('customers'));
    }

    public function show($id)
    {
        // BACKEND: User::with(['orders', 'addresses'])->findOrFail($id)
        $customers = $this->getDemoCustomers();
        $customer = collect($customers)->firstWhere('id', (int) $id) ?? $customers[0];
        $orders = config('demo.account.orders') ?? [];
        $addresses = config('demo.account.addresses') ?? [];

        return view('admin.customers.show', compact('customer', 'orders', 'addresses'));
    }
}
