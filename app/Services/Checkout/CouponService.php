<?php

namespace App\Services\Checkout;

use App\Models\Category;
use App\Models\Coupon;
use App\Models\CouponRedemption;
use App\Models\User;

class CouponService
{
    /**
     * Evaluate coupon eligibility and compute discount over eligible cart/order lines.
     *
     * @param  array<int, array{product_id: int, category_id: int|null, line_total: float}>  $lines
     * @return array{valid: bool, discount_amount: float, free_shipping: bool, reason: ?string}
     */
    public function evaluate(
        Coupon $coupon,
        array $lines,
        float $subtotal,
        ?User $user = null,
        ?string $email = null
    ): array {
        if (! $coupon->is_active) {
            return $this->invalid('This promotional code is inactive.');
        }

        if ($coupon->starts_at && $coupon->starts_at->isFuture()) {
            return $this->invalid('This promotional code is not active yet.');
        }

        if ($coupon->expires_at && $coupon->expires_at->isPast()) {
            return $this->invalid('This promotional code has expired.');
        }

        if ($coupon->usage_limit !== null && $coupon->used_count >= $coupon->usage_limit) {
            return $this->invalid('This promotional code has reached its usage limit.');
        }

        if ($subtotal > 0 && $subtotal < (float) $coupon->min_order_amount) {
            return $this->invalid('Order subtotal does not meet the minimum amount for this code.');
        }

        if ($coupon->per_customer_limit !== null && ($user || ($email !== null && trim($email) !== ''))) {
            $redemptionCount = CouponRedemption::where('coupon_id', $coupon->id)
                ->where(function ($q) use ($user, $email) {
                    if ($user) {
                        $q->where('user_id', $user->id);
                        if ($user->email) {
                            $q->orWhere('email', $user->email);
                        }
                    } elseif ($email) {
                        $q->where('email', trim($email));
                    }
                })
                ->count();

            if ($redemptionCount >= $coupon->per_customer_limit) {
                return $this->invalid('You have reached the usage limit for this promotional code.');
            }
        }

        $coupon->loadMissing(['products:id', 'categories']);
        $scopedProductIds = $coupon->products->pluck('id')->all();
        $scopedCategoryIds = [];
        foreach ($coupon->categories as $cat) {
            /** @var Category $cat */
            foreach ($cat->descendantAndSelfIds() as $cid) {
                $scopedCategoryIds[] = $cid;
            }
        }
        $scopedCategoryIds = array_values(array_unique($scopedCategoryIds));

        $hasScope = ! empty($scopedProductIds) || ! empty($scopedCategoryIds);
        $eligibleSubtotal = 0.0;
        $hasAnyFlashSaleLine = false;

        foreach ($lines as $line) {
            // Per D-03: Flash-sale lines are excluded from coupon discounts.
            if (! empty($line['is_flash_sale'])) {
                $hasAnyFlashSaleLine = true;
                continue;
            }

            if ($hasScope) {
                $matchesProduct = in_array((int) $line['product_id'], $scopedProductIds, true);
                $matchesCategory = $line['category_id'] !== null && in_array((int) $line['category_id'], $scopedCategoryIds, true);
                if (! $matchesProduct && ! $matchesCategory) {
                    continue;
                }
            }

            $eligibleSubtotal = round($eligibleSubtotal + (float) $line['line_total'], 2);
        }

        if ($eligibleSubtotal <= 0 && $coupon->type !== 'shipping') {
            if ($hasAnyFlashSaleLine) {
                return $this->invalid('Promotional codes cannot be combined with Flash Sale items.');
            }
            if ($hasScope) {
                return $this->invalid('This promotional code does not apply to the items in your bag.');
            }
        }

        $discountAmount = 0.0;
        $freeShipping = false;

        if ($coupon->type === 'percentage') {
            $discountAmount = round($eligibleSubtotal * ((float) $coupon->value / 100), 2);
            if ($coupon->max_discount !== null) {
                $discountAmount = min($discountAmount, round((float) $coupon->max_discount, 2));
            }
            $discountAmount = min($eligibleSubtotal, $discountAmount);
        } elseif ($coupon->type === 'fixed') {
            $discountAmount = min($eligibleSubtotal, round((float) $coupon->value, 2));
        } elseif ($coupon->type === 'shipping') {
            $freeShipping = true;
        }

        return [
            'valid' => true,
            'discount_amount' => $discountAmount,
            'free_shipping' => $freeShipping,
            'reason' => null,
        ];
    }

    private function invalid(string $reason): array
    {
        return [
            'valid' => false,
            'discount_amount' => 0.0,
            'free_shipping' => false,
            'reason' => $reason,
        ];
    }
}

