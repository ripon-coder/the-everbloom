<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Category;
use App\Models\Order;
use App\Models\Product;
use App\Models\User;

class AdminDashboardController extends Controller
{
    public function index()
    {
        $products = Product::with('categoryRelation')->orderBy('id')->get()
            ->map(fn (Product $p) => $p->toStorefrontArray())
            ->all();

        $orders = Order::with('items')->latest('placed_at')->get()
            ->map(fn (Order $o) => $o->toAdminArray())
            ->all();

        $categories = Category::withCount('products')->orderBy('sort_order')->get()->toArray();

        $totalRevenue = (float) Order::where('status', '!=', 'Cancelled')->sum('total');
        $totalOrders = Order::count();
        $totalProducts = Product::count();
        $totalCustomers = User::count();
        $activeCustomersCount = User::where('status', 'Active')->count();
        $pendingOrdersCount = Order::whereIn('status', ['Placed', 'Processing'])->count();
        $lowStockCount = Product::whereColumn('stock_count', '<=', 'min_threshold')->count();

        $recentOrders = Order::with('items')->latest('placed_at')->take(5)->get()
            ->map(fn (Order $o) => $o->toAdminArray())
            ->all();

        $topProducts = Product::with('categoryRelation')->orderByDesc('review_count')->take(5)->get()
            ->map(fn (Product $p) => $p->toStorefrontArray())
            ->all();

        return view('admin.dashboard.index', compact(
            'products',
            'orders',
            'categories',
            'totalRevenue',
            'totalOrders',
            'totalProducts',
            'totalCustomers',
            'activeCustomersCount',
            'pendingOrdersCount',
            'lowStockCount',
            'recentOrders',
            'topProducts'
        ));
    }
}
