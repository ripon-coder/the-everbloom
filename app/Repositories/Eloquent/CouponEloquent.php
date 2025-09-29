<?php
namespace App\Repositories\Eloquent;

use App\Models\Coupon;
use App\Repositories\Contracts\CouponRepository;

class CouponEloquent implements CouponRepository
{
    public function index()
    {
        return Coupon::select('id', 'code', 'type', 'value', 'min_order_amount', 'usage_limit', 'used_count', 'start_date', 'end_date', 'status', 'created_at')
            ->latest()
            ->paginate(15);
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
}
