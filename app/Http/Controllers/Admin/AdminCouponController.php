<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;

class AdminCouponController extends Controller
{
    public function index()
    {
        // BACKEND: Coupon::latest()->paginate(15)
        $discounts = config('demo.site.demo_discounts') ?? [];

        $coupons = [];
        $i = 1;
        foreach ($discounts as $code => $data) {
            $coupons[] = [
                'id' => $i++,
                'code' => $code,
                'type' => $data['type'] === 'percentage' ? 'Percentage' : 'Free Shipping',
                'discount' => $data['type'] === 'percentage' ? ($data['value'] . '% OFF') : 'FREE SHIP',
                'label' => $data['label'],
                'usage_limit' => 500,
                'used_count' => rand(40, 320),
                'status' => 'Active',
                'status_badge' => 'success',
                'expires_at' => 'Dec 31, 2026',
            ];
        }

        return view('admin.coupons.index', compact('coupons'));
    }

    public function create()
    {
        return view('admin.coupons.create');
    }

    public function store(Request $request)
    {
        // BACKEND: Validate and save coupon
        return redirect()->route('admin.coupons.index')->with('success', 'Coupon created successfully.');
    }
}
