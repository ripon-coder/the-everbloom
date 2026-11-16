<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Order;
use App\Models\User;

class AdminCustomerController extends Controller
{
    public function index()
    {
        $customers = User::withCount('orders')
            ->withSum('orders as total_spent', 'total')
            ->with('addresses')
            ->latest()
            ->get()
            ->map(function (User $user) {
                $defaultAddr = $user->addresses->firstWhere('is_default', true) ?? $user->addresses->first();
                $city = $defaultAddr ? trim("{$defaultAddr->city}, {$defaultAddr->state}", ', ') : 'Portland, OR';

                return [
                    'id' => $user->id,
                    'name' => $user->name,
                    'email' => $user->email,
                    'phone' => $user->phone ?? '—',
                    'avatar' => $user->avatar ?? 'admin/assets/images/users/avatar-2.jpg',
                    'orders_count' => (int) $user->orders_count,
                    'total_spent' => (float) ($user->total_spent ?? 0),
                    'city' => $city,
                    'status' => $user->status,
                    'join_date' => $user->join_date,
                ];
            })
            ->all();

        return view('admin.customers.index', compact('customers'));
    }

    public function show($id)
    {
        $user = User::with(['orders.items', 'addresses'])
            ->withCount('orders')
            ->withSum('orders as total_spent', 'total')
            ->findOrFail($id);

        $defaultAddr = $user->addresses->firstWhere('is_default', true) ?? $user->addresses->first();

        $customer = [
            'id' => $user->id,
            'name' => $user->name,
            'first_name' => $user->first_name,
            'last_name' => $user->last_name,
            'email' => $user->email,
            'phone' => $user->phone ?? '—',
            'avatar' => $user->avatar ?? 'admin/assets/images/users/avatar-2.jpg',
            'orders_count' => (int) $user->orders_count,
            'total_spent' => (float) ($user->total_spent ?? 0),
            'city' => $defaultAddr ? trim("{$defaultAddr->city}, {$defaultAddr->state}", ', ') : 'Portland, OR',
            'status' => $user->status,
            'join_date' => $user->join_date,
        ];

        $orders = $user->orders->map(fn (Order $o) => $o->toViewArray())->all();
        $addresses = $user->addresses->toArray();

        return view('admin.customers.show', compact('customer', 'orders', 'addresses'));
    }
}
