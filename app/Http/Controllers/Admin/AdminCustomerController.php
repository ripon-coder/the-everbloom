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
                $city = $defaultAddr ? trim("{$defaultAddr->city}, {$defaultAddr->state}", ', ') : 'Dhaka, Bangladesh';

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

        $allClientsCount = count($customers);
        $activeProfilesCount = collect($customers)->where('status', 'Active')->count();
        $averageLtv = $allClientsCount > 0 ? round(collect($customers)->avg('total_spent'), 2) : 0;
        $repeatCount = collect($customers)->where('orders_count', '>', 1)->count();
        $repeatRate = $allClientsCount > 0 ? round(($repeatCount / $allClientsCount) * 100, 1) : 0;

        return view('admin.customers.index', compact(
            'customers',
            'allClientsCount',
            'activeProfilesCount',
            'averageLtv',
            'repeatRate'
        ));
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
            'city' => $defaultAddr ? trim("{$defaultAddr->city}, {$defaultAddr->state}", ', ') : 'Dhaka, Bangladesh',
            'status' => $user->status,
            'join_date' => $user->join_date,
        ];

        $orders = $user->orders->map(fn (Order $o) => $o->toViewArray())->all();
        $addresses = $user->addresses->toArray();

        return view('admin.customers.show', compact('customer', 'orders', 'addresses'));
    }
}
