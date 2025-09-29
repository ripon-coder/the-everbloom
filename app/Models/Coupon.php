<?php

namespace App\Models;

use App\Constants\CouponStatus;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class Coupon extends Model
{
    use HasFactory, SoftDeletes;

    /**
     * The attributes that are mass assignable.
     *
     * @var array<int, string>
     */
    protected $fillable = [
        'code',
        'type',
        'value',
        'min_order_amount',
        'max_discount_amount',
        'usage_limit',
        'used_count',
        'start_date',
        'end_date',
        'status',
        'description',
    ];

    /**
     * The attributes that should be cast.
     *
     * @var array<string, string>
     */
    protected $casts = [
        'value' => 'decimal:2',
        'min_order_amount' => 'decimal:2',
        'max_discount_amount' => 'decimal:2',
        'usage_limit' => 'integer',
        'used_count' => 'integer',
        'start_date' => 'datetime',
        'end_date' => 'datetime',
        'status' => CouponStatus::class,
    ];

    /**
     * The attributes that should be mutated to dates.
     *
     * @var array<string, string>
     */
    protected $dates = [
        'start_date',
        'end_date',
    ];

    /**
     * Get the coupons that are active.
     */
    public function scopeActive($query)
    {
        return $query->where('status', CouponStatus::ACTIVE)
                    ->where('start_date', '<=', now())
                    ->where('end_date', '>=', now())
                    ->where(function($q) {
                        $q->whereNull('usage_limit')
                          ->orWhereRaw('used_count < usage_limit');
                    });
    }

    /**
     * Check if coupon is valid for use.
     */
    public function isValid()
    {
        return $this->status === CouponStatus::ACTIVE &&
               $this->start_date <= now() &&
               $this->end_date >= now() &&
               ($this->usage_limit === null || $this->used_count < $this->usage_limit);
    }

    /**
     * Calculate discount amount for a given order total.
     */
    public function calculateDiscount($orderTotal)
    {
        if ($orderTotal < $this->min_order_amount) {
            return 0;
        }

        if ($this->type === 'percentage') {
            $discount = ($orderTotal * $this->value) / 100;
            return $this->max_discount_amount ? min($discount, $this->max_discount_amount) : $discount;
        }

        return min($this->value, $orderTotal);
    }
}
