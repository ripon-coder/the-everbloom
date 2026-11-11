<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;

class AdminDashboardController extends Controller
{
    public function index()
    {
        // BACKEND: Replace demo data aggregations with Eloquent/DB queries
        $products = config('demo.products') ?? [];
        $orders = config('demo.account.orders') ?? [];
        $categories = config('demo.categories') ?? [];

        $totalRevenue = array_sum(array_column($orders, 'total'));
        $totalOrders = count($orders);
        $totalProducts = count($products);
        $totalCustomers = 428; // Demo count

        $recentOrders = array_slice($orders, 0, 5);
        $topProducts = array_slice($products, 0, 5);

        return view('admin.dashboard.index', compact(
            'products',
            'orders',
            'categories',
            'totalRevenue',
            'totalOrders',
            'totalProducts',
            'totalCustomers',
            'recentOrders',
            'topProducts'
        ));
    }
}
