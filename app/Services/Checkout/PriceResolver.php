<?php

namespace App\Services\Checkout;

use App\Models\Coupon;
use App\Models\Product;
use App\Models\ProductVariant;
use Illuminate\Support\Facades\Auth;

class PriceResolver
{
    public function __construct(
        private readonly CouponService $couponService
    ) {}
    public function resolveUnitPrice(Product $product, ?ProductVariant $variant): array
    {
        if ($variant) {
            return [
                'unit_price' => round((float) $variant->price, 2),
                'original_price' => round((float) ($variant->compare_at_price ?? $variant->price), 2),
            ];
        }

        return [
            'unit_price' => round((float) $product->price, 2),
            'original_price' => round((float) ($product->compare_at_price ?? $product->price), 2),
        ];
    }

    /**
     * Compute an authoritative server-side cart/checkout quote.
     *
     * @param  array<int, array{productId: int|string, variantId?: string|null, qty: int}>  $items
     */
    public function quote(array $items, ?string $couponCode = null, string $shippingMethod = 'standard'): array
    {
        $productIds = collect($items)->pluck('productId')->filter()->unique()->values()->all();
        $products = Product::with('productVariants')
            ->whereIn('id', $productIds)
            ->where('is_active', true)
            ->get()
            ->keyBy('id');

        $lines = [];
        $errors = [];
        $subtotal = 0.0;

        foreach ($items as $rawLine) {
            $productId = (int) ($rawLine['productId'] ?? 0);
            $variantId = isset($rawLine['variantId']) && $rawLine['variantId'] !== '' ? (string) $rawLine['variantId'] : null;
            $qty = max(1, min(10, (int) ($rawLine['qty'] ?? 1)));

            /** @var Product|null $product */
            $product = $products->get($productId);
            if (! $product) {
                $errors[] = "Product #{$productId} is no longer available.";
                continue;
            }

            $variant = $this->matchVariant($product, $variantId);
            if ($product->productVariants->isNotEmpty() && $variantId !== null && ! $variant) {
                $errors[] = "Selected option for {$product->name} is no longer available.";
                continue;
            }

            $availableStock = $variant ? max(0, (int) $variant->stock_count) : max(0, (int) $product->stock_count);
            $stockError = null;
            if ($availableStock <= 0) {
                $stockError = "{$product->name} is currently out of stock.";
                $errors[] = $stockError;
            } elseif ($qty > $availableStock) {
                $stockError = "Only {$availableStock} unit(s) of {$product->name} available.";
                $errors[] = $stockError;
            }

            $pricing = $this->resolveUnitPrice($product, $variant);
            $unitPrice = $pricing['unit_price'];
            $lineTotal = round($unitPrice * $qty, 2);
            $subtotal = round($subtotal + $lineTotal, 2);

            $lines[] = [
                'productId' => $product->id,
                'product_id' => $product->id,
                'category_id' => $product->category_id,
                'variantId' => $variant ? ($variant->size ?: (string) $variant->id) : null,
                'product_variant_id' => $variant?->id,
                'name' => $product->name,
                'slug' => $product->slug,
                'image' => $variant?->resolvedImage() ?: ($product->resolvedImages()[0] ?? null),
                'qty' => $qty,
                'unit_price' => $unitPrice,
                'original_price' => $pricing['original_price'],
                'line_total' => $lineTotal,
                'available_stock' => $availableStock,
                'max_qty' => min(10, $availableStock),
                'stock_error' => $stockError,
            ];
        }

        $discountAmount = 0.0;
        $appliedCouponCode = null;
        $appliedCouponLabel = null;
        $appliedCouponType = null;
        $couponError = null;
        $isFreeShippingCoupon = false;

        if ($couponCode !== null && trim($couponCode) !== '') {
            $normalizedCode = strtoupper(trim($couponCode));
            $coupon = Coupon::where('code', $normalizedCode)->first();

            if (! $coupon) {
                $couponError = 'This promotional code is invalid, expired, or does not meet order requirements.';
            } else {
                $eval = $this->couponService->evaluate(
                    coupon: $coupon,
                    lines: $lines,
                    subtotal: $subtotal,
                    user: Auth::guard('web')->user(),
                );

                if (! $eval['valid']) {
                    $couponError = $eval['reason'];
                } else {
                    $appliedCouponCode = $coupon->code;
                    $appliedCouponLabel = $coupon->label;
                    $appliedCouponType = $coupon->type;
                    $discountAmount = $eval['discount_amount'];
                    $isFreeShippingCoupon = $eval['free_shipping'];
                }
            }
        }

        $shippingCost = 0.0;
        if (! empty($lines)) {
            $shippingCfg = config('demo.site.shipping', []);
            $freeThreshold = (float) ($shippingCfg['free_shipping_threshold'] ?? 500);
            $standardFee = (float) ($shippingCfg['standard_fee'] ?? 60);
            $expressFee = (float) ($shippingCfg['express_fee'] ?? 120);

            if ($shippingMethod === 'express') {
                $shippingCost = $isFreeShippingCoupon ? 0.0 : $expressFee;
            } else {
                $shippingCost = ($isFreeShippingCoupon || ($freeThreshold > 0 && $subtotal >= $freeThreshold))
                    ? 0.0
                    : $standardFee;
            }
        }

        $total = max(0.0, round($subtotal - $discountAmount + $shippingCost, 2));

        return [
            'lines' => $lines,
            'errors' => $errors,
            'has_stock_errors' => ! empty($errors),
            'subtotal' => $subtotal,
            'discount_code' => $appliedCouponCode,
            'discount_label' => $appliedCouponLabel,
            'discount_type' => $appliedCouponType,
            'discount_amount' => $discountAmount,
            'coupon_error' => $couponError,
            'shipping_method' => $shippingMethod,
            'shipping_cost' => round($shippingCost, 2),
            'tax_amount' => 0.0,
            'total' => $total,
        ];
    }

    private function matchVariant(Product $product, ?string $variantId): ?ProductVariant
    {
        $activeVariants = $product->productVariants->where('is_active', true);
        if ($activeVariants->isEmpty()) {
            $activeVariants = $product->productVariants;
        }
        if ($activeVariants->isEmpty()) {
            return null;
        }
        if ($variantId === null || $variantId === '') {
            return $activeVariants->first();
        }

        return $activeVariants->first(function (ProductVariant $v) use ($variantId) {
            return $v->size === $variantId
                || $v->title === $variantId
                || (string) $v->id === $variantId;
        });
    }

    private function isCouponEligible(Coupon $coupon, float $subtotal): bool
    {
        if (! $coupon->is_active) {
            return false;
        }
        if ($coupon->starts_at && $coupon->starts_at->isFuture()) {
            return false;
        }
        if ($coupon->expires_at && $coupon->expires_at->isPast()) {
            return false;
        }
        if ($coupon->usage_limit !== null && $coupon->used_count >= $coupon->usage_limit) {
            return false;
        }
        if ($subtotal > 0 && $subtotal < (float) $coupon->min_order_amount) {
            return false;
        }

        return true;
    }
}

