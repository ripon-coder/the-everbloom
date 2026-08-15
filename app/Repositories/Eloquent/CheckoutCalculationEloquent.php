<?php

namespace App\Repositories\Eloquent;

use App\Models\Product;
use App\Models\ProductVariant;
use App\Models\District;
use App\Repositories\Contracts\CheckoutCalculationRepository;
use App\Repositories\Contracts\CouponRepository;

class CheckoutCalculationEloquent implements CheckoutCalculationRepository
{
    protected CouponRepository $couponRepository;

    public function __construct(CouponRepository $couponRepository)
    {
        $this->couponRepository = $couponRepository;
    }

    public function calculate(array $cartItems, ?string $districtId = null, ?string $couponCode = null): array
    {
        $validatedItems = [];
        $subtotal = 0;
        $errors = [];

        foreach ($cartItems as $item) {
            $productId = $item['product_id'] ?? null;
            $variantId = $item['variant_id'] ?? null;
            $requestedQty = (int) ($item['quantity'] ?? 1);

            if (!$productId)
                continue;

            $product = Product::with([
                'flashSales' => function ($q) {
                    $q->active();
                }
            ])->find($productId);

            if (!$product || $product->status !== \App\Constants\ProductStatus::ACTIVE) {
                $errors[] = "Product \"{$product?->name}\" is no longer available.";
                $availableStock = 0;
                continue;
            }

            $unitOriginalPrice = 0;
            $unitFinalPrice = 0;
            $availableStock = 0;

            if ($variantId) {
                $variant = ProductVariant::find($variantId);
                if (!$variant || $variant->status !== \App\Constants\ProductVariantStatus::ACTIVE) {
                    $errors[] = "Selected variant for \"{$product->name}\" is no longer available.";
                    continue;
                }

                $availableStock = $variant->stock;

                // Original base price is the variant's regular sell_price
                $unitOriginalPrice = (float) $variant->sell_price;
                $unitFinalPrice = $unitOriginalPrice;

                if ($variant->discount_price > 0) {
                    $unitFinalPrice = (float) $variant->discount_price;
                }

                // Check flash sale
                if ($product->flashSales && $product->flashSales->isNotEmpty()) {
                    $pivot = $product->flashSales->first()->pivot;
                    $discountPercentage = (float) ($pivot->discount_percentage ?? 0);
                    $discountAmount = (float) ($pivot->discount_price ?? 0);

                    if ($discountPercentage > 0) {
                        $unitFinalPrice = max(0, $unitFinalPrice - ($unitFinalPrice * ($discountPercentage / 100)));
                    } elseif ($discountAmount > 0 && $product->price > 0) {
                        $computedPercentage = ($discountAmount / $product->price) * 100;
                        $unitFinalPrice = max(0, $unitFinalPrice - ($unitFinalPrice * ($computedPercentage / 100)));
                    }
                }

            } else {
                // Products don't have their own stock — sum from all active variants
                $availableStock = $product->variants()->active()->sum('stock');
                $unitOriginalPrice = (float) $product->price;
                $unitFinalPrice = $unitOriginalPrice;

                if ($product->flashSales && $product->flashSales->isNotEmpty()) {
                    $pivot = $product->flashSales->first()->pivot;
                    $discountPercentage = (float) ($pivot->discount_percentage ?? 0);
                    $discountAmount = (float) ($pivot->discount_price ?? 0);

                    if ($discountPercentage > 0) {
                        $unitFinalPrice = max(0, $unitFinalPrice - ($unitFinalPrice * ($discountPercentage / 100)));
                    } elseif ($discountAmount > 0) {
                        $unitFinalPrice = max(0, $unitFinalPrice - $discountAmount);
                    }
                }
            }

            // Cap quantity at 30 per item
            $cappedQty = $requestedQty;
            if ($cappedQty > 30) {
                $errors[] = "Maximum 30 items allowed per product for {$product->name}.";
                $cappedQty = 30;
            }

            $isItemAvailable = true;
            if ($availableStock <= 0) {
                $errors[] = "\"{$product->name}\" is out of stock.";
                $isItemAvailable = false;
            } elseif ($cappedQty > $availableStock) {
                $errors[] = "Only {$availableStock} unit(s) available for \"{$product->name}\".";
                $isItemAvailable = false;
            }

            $lineTotal = $unitFinalPrice * $cappedQty;
            if ($isItemAvailable) {
                $subtotal += $lineTotal;
            }

            $itemData = [
                'product_id' => $product->id,
                'variant_id' => $variantId,
                'name' => $product->name,
                'attributes' => $item['attributes'] ?? [],
                'image' => $item['image'] ?? null,
                'unit_base_price' => $unitOriginalPrice,
                'unit_final_price' => $unitFinalPrice,
                'quantity' => $cappedQty,
                'available_stock' => $availableStock,
                'available' => $isItemAvailable,
                'line_total' => $lineTotal,
                'is_free_delivery' => (bool) $product->is_free_delivery,
            ];

            $validatedItems[] = $itemData;
        }

        $isAllFreeDelivery = !empty($validatedItems) && collect($validatedItems)->every(function ($item) {
            return !empty($item['is_free_delivery']);
        });

        $shippingCost = 0;
        if ($districtId && !$isAllFreeDelivery) {
            $district = District::find($districtId);
            if ($district) {
                $shippingCost = (float) $district->delivery_charge;
            }
        }

        // Coupon discount
        $couponDiscount = 0;
        $couponError = null;
        if ($couponCode) {
            $couponDiscount = $this->couponRepository->getDiscountAmount($couponCode, $subtotal);
            if ($couponDiscount <= 0) {
                $couponError = 'Invalid or expired coupon code.';
            }
        }

        $total = $subtotal + $shippingCost - $couponDiscount;

        return [
            'items' => $validatedItems,
            'subtotal' => $subtotal,
            'shipping_cost' => $shippingCost,
            'discount' => $couponDiscount,
            'coupon_error' => $couponError,
            'total' => $total,
            'errors' => $errors,
        ];
    }
}
