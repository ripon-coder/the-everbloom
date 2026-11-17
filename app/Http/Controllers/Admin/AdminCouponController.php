<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Http\Requests\Admin\StoreCouponRequest;
use App\Models\Category;
use App\Models\Coupon;
use App\Models\CouponRedemption;
use App\Models\Product;
use Illuminate\Support\Facades\Cache;

class AdminCouponController extends Controller
{
    public function index()
    {
        $currencySymbol = config('demo.site.currency.symbol', '৳');

        $coupons = Coupon::latest()->paginate(15);
        $coupons->setCollection(
            $coupons->getCollection()->map(function (Coupon $c) use ($currencySymbol) {
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
                        'fixed' => ($currencySymbol . number_format((float) $c->value, 2) . ' OFF'),
                        default => 'FREE SHIP',
                    },
                    'label' => $c->label,
                    'usage_limit' => $c->usage_limit,
                    'used_count' => (int) $c->used_count,
                    'is_active' => (bool) $c->is_active,
                    'status' => $c->is_active ? 'Active' : 'Inactive',
                    'status_badge' => $c->is_active ? 'success' : 'secondary',
                    'expires_at' => $c->expires_at?->format('M d, Y') ?? 'No expiry',
                ];
            })
        );

        return view('admin.coupons.index', compact('coupons'));
    }

    public function create()
    {
        $products = Product::orderBy('name')->get(['id', 'name', 'sku']);
        $categories = Category::orderBy('sort_order')->orderBy('name')->get(['id', 'name']);

        return view('admin.coupons.create', compact('products', 'categories'));
    }

    public function edit($id)
    {
        $coupon = Coupon::with(['products:id', 'categories:id'])->findOrFail($id);
        $products = Product::orderBy('name')->get(['id', 'name', 'sku']);
        $categories = Category::orderBy('sort_order')->orderBy('name')->get(['id', 'name']);

        return view('admin.coupons.create', [
            'coupon' => $coupon,
            'products' => $products,
            'categories' => $categories,
            'isEdit' => true,
        ]);
    }

    public function store(StoreCouponRequest $request)
    {
        $validated = $request->validated();
        $code = strtoupper(trim($validated['code']));

        $coupon = Coupon::create([
            'code' => $code,
            'type' => $validated['type'],
            'value' => $validated['value'],
            'max_discount' => $validated['max_discount'] ?? null,
            'label' => !empty($validated['label']) ? $validated['label'] : ($code . ' Promotion'),
            'min_order_amount' => $validated['min_order'] ?? 0,
            'usage_limit' => $validated['usage_limit'] ?? null,
            'per_customer_limit' => $validated['per_customer_limit'] ?? null,
            'is_active' => $request->has('is_active') ? $request->boolean('is_active') : true,
            'starts_at' => $validated['starts_at'] ?? null,
            'expires_at' => $validated['expires_at'] ?? null,
        ]);

        $coupon->products()->sync($validated['product_ids'] ?? []);
        $coupon->categories()->sync($validated['category_ids'] ?? []);

        Cache::forget('storefront_discounts_json');

        return redirect()->route('admin.coupons.index')->with('success', 'Coupon created successfully.');
    }

    public function update(StoreCouponRequest $request, $id)
    {
        $coupon = Coupon::findOrFail($id);
        $validated = $request->validated();
        $code = strtoupper(trim($validated['code']));

        $coupon->update([
            'code' => $code,
            'type' => $validated['type'],
            'value' => $validated['value'],
            'max_discount' => $validated['max_discount'] ?? null,
            'label' => !empty($validated['label']) ? $validated['label'] : ($code . ' Promotion'),
            'min_order_amount' => $validated['min_order'] ?? 0,
            'usage_limit' => $validated['usage_limit'] ?? null,
            'per_customer_limit' => $validated['per_customer_limit'] ?? null,
            'is_active' => $request->has('is_active') ? $request->boolean('is_active') : $coupon->is_active,
            'starts_at' => $validated['starts_at'] ?? null,
            'expires_at' => $validated['expires_at'] ?? null,
        ]);

        $coupon->products()->sync($validated['product_ids'] ?? []);
        $coupon->categories()->sync($validated['category_ids'] ?? []);

        Cache::forget('storefront_discounts_json');

        return redirect()->route('admin.coupons.index')->with('success', 'Coupon updated successfully.');
    }

    public function toggle($id)
    {
        $coupon = Coupon::findOrFail($id);
        $coupon->update(['is_active' => ! $coupon->is_active]);
        Cache::forget('storefront_discounts_json');

        return redirect()->back()->with('success', 'Coupon status updated.');
    }

    public function history($id)
    {
        $coupon = Coupon::findOrFail($id);
        $redemptions = CouponRedemption::with(['order:id,order_number', 'user:id,first_name,last_name,email'])
            ->where('coupon_id', $coupon->id)
            ->latest()
            ->paginate(20);

        return view('admin.coupons.history', compact('coupon', 'redemptions'));
    }

    public function destroy($id)
    {
        $coupon = Coupon::findOrFail($id);
        $coupon->delete();
        Cache::forget('storefront_discounts_json');

        return redirect()->route('admin.coupons.index')->with('success', 'Coupon deleted successfully.');
    }
}
