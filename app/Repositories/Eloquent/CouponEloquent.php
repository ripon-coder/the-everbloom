<?php
namespace App\Repositories\Eloquent;

use App\Models\Coupon;
use App\Repositories\Contracts\CouponRepository;

class CouponEloquent implements CouponRepository
{
    public function index(array $filters = [])
    {
        $query = Coupon::select('id', 'code', 'type', 'value', 'min_order_amount', 'usage_limit', 'used_count', 'start_date', 'end_date', 'status', 'created_at');

        if (!empty($filters['search'])) {
            $search = trim($filters['search']);
            $query->where(function($q) use ($search) {
                $q->where('code', 'LIKE', "%{$search}%")
                  ->orWhere('id', $search);
            });
        }

        if (!empty($filters['status'])) {
            $query->where('status', $filters['status']);
        }

        if (!empty($filters['type'])) {
            $query->where('type', $filters['type']);
        }

        return $query->latest()->paginate(15)->withQueryString();
    }

    public function create()
    {
        $data['status_options'] = \App\Constants\CouponStatus::getOptions();
        $data['type_options'] = [
            'percentage' => 'Percentage',
            'fixed_amount' => 'Fixed Amount',
        ];
        return $data;
    }

    public function store(array $data)
    {
        return Coupon::create($data);
    }

    public function edit(int $id)
    {
        $data['coupon'] = Coupon::findOrFail($id);
        $data['status_options'] = \App\Constants\CouponStatus::getOptions();
        $data['type_options'] = [
            'percentage' => 'Percentage',
            'fixed_amount' => 'Fixed Amount',
        ];
        return $data;
    }

    public function update(int $id, array $data)
    {
        $coupon = Coupon::findOrFail($id);
        $coupon->update($data);
        return $coupon;
    }

    public function destroy(int $id)
    {
        $coupon = Coupon::findOrFail($id);
        return $coupon->delete();
    }

    public function restore(int $id)
    {
        $coupon = Coupon::withTrashed()->findOrFail($id);
        return $coupon->restore();
    }

    public function forceDelete(int $id)
    {
        $coupon = Coupon::withTrashed()->findOrFail($id);
        return $coupon->forceDelete();
    }

    public function getDiscountAmount($couponCode, float $subTotal): float
    {
        if (!$couponCode) {
            return 0.0;
        }
        $coupon = Coupon::where('code', $couponCode)
            ->where('status', \App\Constants\CouponStatus::ACTIVE)
            ->where('start_date', '<=', now())
            ->where('end_date', '>=', now())
            ->where(function ($query) use ($subTotal) {
                $query->whereNull('min_order_amount')
                    ->orWhere('min_order_amount', '<=', $subTotal);
            })
            ->where(function ($query) {
                $query->whereNull('usage_limit')
                    ->orWhereColumn('used_count', '<', 'usage_limit');
            })
            ->first();

        if (!$coupon) {
            return 0.0;
        }

        return $coupon->calculateDiscount($subTotal);
    }
    public function usedCoupon(string $couponCode): void
    {
        $coupon = Coupon::where('code', $couponCode)->first();
        if ($coupon) {
            $coupon->increment('used_count');
        }
    }
}
