<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Coupon;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Cache;

class AdminCouponController extends Controller
{
    public function index()
    {
        $coupons = Coupon::latest()->get()->map(function (Coupon $c) {
            return [
                'id' => $c->id,
                'code' => $c->code,
                'type' => match ($c->type) {
                    'percentage' => 'Percentage',
                    'fixed' => 'Fixed Amount',
                    default => 'Free Shipping',
                },
                'discount' => match ($c->type) {
                    'percentage' => ((float) $c->value . '% OFF'),
                    'fixed' => ('$' . number_format((float) $c->value, 2) . ' OFF'),
                    default => 'FREE SHIP',
                },
                'label' => $c->label,
                'usage_limit' => (int) ($c->usage_limit ?? 500),
                'used_count' => (int) $c->used_count,
                'status' => $c->is_active ? 'Active' : 'Inactive',
                'status_badge' => $c->is_active ? 'success' : 'secondary',
                'expires_at' => $c->expires_at?->format('M d, Y') ?? 'No expiry',
            ];
        })->all();

        return view('admin.coupons.index', compact('coupons'));
    }

    public function create()
    {
        return view('admin.coupons.create');
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'code' => ['required', 'string', 'max:60', 'unique:coupons,code'],
            'type' => ['required', 'in:percentage,fixed,shipping'],
            'value' => ['required', 'numeric', 'min:0'],
            'label' => ['nullable', 'string', 'max:255'],
            'usage_limit' => ['nullable', 'integer', 'min:1'],
            'min_order' => ['nullable', 'numeric', 'min:0'],
            'expires_at' => ['nullable', 'date'],
        ]);

        Coupon::create([
            'code' => strtoupper(trim($validated['code'])),
            'type' => $validated['type'],
            'value' => $validated['value'],
            'label' => $validated['label'] ?: strtoupper(trim($validated['code'])) . ' Promotion',
            'min_order_amount' => $validated['min_order'] ?? 0,
            'usage_limit' => $validated['usage_limit'] ?? null,
            'is_active' => $request->boolean('is_active', true),
            'expires_at' => $validated['expires_at'] ?? null,
        ]);

        Cache::forget('storefront_discounts_json');

        return redirect()->route('admin.coupons.index')->with('success', 'Coupon created successfully.');
    }

    public function destroy($id)
    {
        $coupon = Coupon::findOrFail($id);
        $coupon->delete();
        Cache::forget('storefront_discounts_json');

        return redirect()->route('admin.coupons.index')->with('success', 'Coupon deleted successfully.');
    }
}
