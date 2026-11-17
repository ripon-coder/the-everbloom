<?php

namespace App\Services\Checkout;

use App\Mail\OrderConfirmationMail;
use App\Models\Area;
use App\Models\Coupon;
use App\Models\CouponRedemption;
use App\Models\District;
use App\Models\Invoice;
use App\Models\Order;
use App\Models\OrderItem;
use App\Models\OrderStatusHistory;
use App\Models\Product;
use App\Models\ProductVariant;
use App\Models\Thana;
use App\Models\User;
use App\Services\Inventory\StockService;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Mail;
use Illuminate\Support\Str;
use Illuminate\Validation\ValidationException;

class PlaceOrderService
{
    public function __construct(
        private readonly StockService $stockService,
        private readonly PriceResolver $priceResolver,
    ) {}

    public function placeOrder(array $validated, ?User $user = null): Order
    {
        $district = District::where('id', $validated['district_id'])->where('is_active', true)->firstOrFail();
        $thana = Thana::where('id', $validated['thana_id'])->where('district_id', $district->id)->where('is_active', true)->firstOrFail();
        $area = Area::where('id', $validated['area_id'])->where('thana_id', $thana->id)->where('is_active', true)->firstOrFail();

        $order = DB::transaction(function () use ($validated, $district, $thana, $area, $user) {
            $subtotal = 0.0;
            $sumLineCosts = 0.0;
            $itemsCount = 0;
            $preparedItems = [];

            foreach ($validated['items'] as $line) {
                /** @var Product|null $product */
                $product = Product::with('productVariants')
                    ->where('is_active', true)
                    ->lockForUpdate()
                    ->find($line['productId']);

                if (! $product) {
                    throw ValidationException::withMessages([
                        'items' => ['One or more selected products are no longer available.'],
                    ]);
                }

                $qty = (int) $line['qty'];
                $variant = $this->resolveStrictVariant($product, $line['variantId'] ?? null);

                $pricing = $this->priceResolver->resolveUnitPrice($product, $variant);
                $unitPrice = $pricing['unit_price'];
                $originalPrice = $pricing['original_price'];
                $lineTotal = round($unitPrice * $qty, 2);

                $unitBuyPrice = (float) ($variant?->avg_buy_price > 0 ? $variant->avg_buy_price : $product->avg_buy_price);
                $lineCostTotal = round($unitBuyPrice * $qty, 2);
                $unitProfit = round($unitPrice - $unitBuyPrice, 2);
                $lineProfit = round($lineTotal - $lineCostTotal, 2);

                $consumedBatches = $this->stockService->deductForLine($product, $variant, $qty);

                $subtotal += $lineTotal;
                $sumLineCosts += $lineCostTotal;
                $itemsCount += $qty;

                $preparedItems[] = [
                    'consumed_batches' => $consumedBatches,
                    'attributes' => [
                        'product_id' => $product->id,
                        'product_variant_id' => $variant?->id,
                        'name' => $product->name,
                        'slug' => $product->slug,
                        'variant' => $variant?->title ?: ($variant?->size ?? ($line['variantId'] ?? ($product->sizes[0] ?? null))),
                        'image' => $variant?->image ?: ($product->images[0] ?? null),
                        'image_source' => $variant?->image ? ($variant->image_source ?: 'local') : ($product->image_source ?: 'local'),
                        'qty' => $qty,
                        'price' => $unitPrice,
                        'original_price' => $originalPrice,
                        'unit_buy_price' => $unitBuyPrice,
                        'line_total' => $lineTotal,
                        'line_cost_total' => $lineCostTotal,
                        'unit_profit' => $unitProfit,
                        'line_profit' => $lineProfit,
                    ],
                ];
            }

            $discountAmount = 0.0;
            $appliedCoupon = null;
            $isFreeShippingCoupon = false;

            if (! empty($validated['discountCode'])) {
                $code = strtoupper(trim($validated['discountCode']));
                $coupon = Coupon::where('code', $code)->lockForUpdate()->first();
                $startsOk = ! $coupon?->starts_at || ! $coupon->starts_at->isFuture();

                if ($coupon && $startsOk && $coupon->isValidForSubtotal($subtotal)) {
                    $appliedCoupon = $coupon;
                    if ($coupon->type === 'percentage') {
                        $discountAmount = round($subtotal * ((float) $coupon->value / 100), 2);
                        if ($coupon->max_discount !== null) {
                            $discountAmount = min($discountAmount, round((float) $coupon->max_discount, 2));
                        }
                        $discountAmount = min($subtotal, $discountAmount);
                    } elseif ($coupon->type === 'fixed') {
                        $discountAmount = min($subtotal, (float) $coupon->value);
                    } elseif ($coupon->type === 'shipping') {
                        $isFreeShippingCoupon = true;
                    }
                    $coupon->increment('used_count');
                }
            }

            $shippingCfg = config('demo.site.shipping', []);
            $freeThreshold = (float) ($shippingCfg['free_shipping_threshold'] ?? 500);
            $standardFee = (float) ($shippingCfg['standard_fee'] ?? 60);
            $expressFee = (float) ($shippingCfg['express_fee'] ?? 120);

            if ($validated['shippingMethod'] === 'express') {
                $shippingCost = $isFreeShippingCoupon ? 0.0 : $expressFee;
                $deliveryMethodLabel = 'Express Courier';
            } else {
                $shippingCost = ($isFreeShippingCoupon || ($freeThreshold > 0 && $subtotal >= $freeThreshold))
                    ? 0.0
                    : $standardFee;
                $deliveryMethodLabel = 'Standard Delivery';
            }

            $total = max(0, round($subtotal - $discountAmount + $shippingCost, 2));
            $totalCost = round($sumLineCosts, 2);
            $totalProfit = round(($subtotal - $discountAmount) - $totalCost, 2);

            $orderNumber = $this->generateUniqueOrderNumber();
            $invoiceNumber = $this->generateUniqueInvoiceNumber();

            $paymentLabel = match ($validated['paymentMethod']) {
                'cod_demo' => 'Cash on Delivery (Demo)',
                'bkash_demo' => 'bKash (Demo)',
                'nagad_demo' => 'Nagad (Demo)',
                default => 'Card (Demo)',
            };

            $customerEmail = ! empty($validated['email'])
                ? $validated['email']
                : ($user?->email ?? '');

            $order = Order::create([
                'order_number' => $orderNumber,
                'user_id' => $user?->id,
                'coupon_id' => $appliedCoupon?->id,
                'customer_email' => $customerEmail,
                'customer_phone' => $validated['phone'],
                'status' => 'Placed',
                'current_step' => 'Placed',
                'subtotal' => $subtotal,
                'discount_amount' => $discountAmount,
                'discount_code' => $appliedCoupon?->code,
                'shipping_cost' => $shippingCost,
                'tax_amount' => 0,
                'total' => $total,
                'total_cost' => $totalCost,
                'total_profit' => $totalProfit,
                'items_count' => $itemsCount,
                'delivery_method' => $deliveryMethodLabel,
                'payment_method' => $paymentLabel,
                'payment_status' => 'Pending',
                'placed_via' => 'web',
                'tracking_number' => 'Pending assignment',
                'shipping_address' => [
                    'first_name' => $validated['firstName'],
                    'last_name' => $validated['lastName'],
                    'country' => 'Bangladesh',
                    'district_id' => $district->id,
                    'thana_id' => $thana->id,
                    'area_id' => $area->id,
                    'district_name' => $district->name,
                    'thana_name' => $thana->name,
                    'area_name' => $area->name,
                    'address_line1' => $validated['address'],
                    'address_line2' => $validated['addressLine2'] ?? '',
                    'city' => $thana->name,
                    'state' => $district->name,
                    'postal_code' => $area->postal_code ?? '',
                    'phone' => $validated['phone'],
                ],
                'placed_at' => now(),
            ]);

            foreach ($preparedItems as $item) {
                $orderItem = OrderItem::create(array_merge($item['attributes'], ['order_id' => $order->id]));
                $this->stockService->recordOrderItemConsumption($orderItem, $item['consumed_batches']);
            }

            if ($appliedCoupon) {
                CouponRedemption::create([
                    'coupon_id' => $appliedCoupon->id,
                    'order_id' => $order->id,
                    'user_id' => $user?->id,
                    'email' => $customerEmail !== '' ? $customerEmail : null,
                    'discount_amount' => $discountAmount,
                ]);
            }

            OrderStatusHistory::create([
                'order_id' => $order->id,
                'from_status' => null,
                'to_status' => 'Placed',
                'note' => 'Order placed via checkout',
                'created_at' => now(),
            ]);

            Invoice::create([
                'order_id' => $order->id,
                'invoice_no' => $invoiceNumber,
                'customer_name' => trim($validated['firstName'] . ' ' . $validated['lastName']),
                'customer_email' => $customerEmail,
                'amount' => $total,
                'status' => 'Pending',
                'payment_method' => $paymentLabel,
                'issued_at' => now(),
            ]);

            if ($user && ! empty($validated['saveAddress'])) {
                $isFirstAddress = $user->addresses()->count() === 0;
                $user->addresses()->create([
                    'first_name' => $validated['firstName'],
                    'last_name' => $validated['lastName'],
                    'country' => 'Bangladesh',
                    'district_id' => $district->id,
                    'thana_id' => $thana->id,
                    'area_id' => $area->id,
                    'district_name' => $district->name,
                    'thana_name' => $thana->name,
                    'area_name' => $area->name,
                    'address_line1' => $validated['address'],
                    'phone' => $validated['phone'],
                    'is_default' => $isFirstAddress,
                ]);
            }

            Cache::forget('storefront_catalog_json');

            return $order;
        }, 3);

        if ($order->customer_email !== '') {
            Mail::to($order->customer_email)->queue(new OrderConfirmationMail($order));
        }

        return $order;
    }

    private function resolveStrictVariant(Product $product, ?string $variantId): ?ProductVariant
    {
        if ($product->productVariants->isEmpty()) {
            return null;
        }

        $activeVariants = $product->productVariants->where('is_active', true);

        if ($variantId === null || trim($variantId) === '') {
            $first = $activeVariants->first();
            if (! $first) {
                throw ValidationException::withMessages([
                    'items' => ["{$product->name} has no active variants available."],
                ]);
            }

            return $first;
        }

        $matched = $activeVariants->first(function (ProductVariant $v) use ($variantId) {
            return $v->size === $variantId
                || $v->title === $variantId
                || (string) $v->id === (string) $variantId;
        });

        if (! $matched) {
            throw ValidationException::withMessages([
                'items' => ["Selected variant for {$product->name} is invalid or unavailable."],
            ]);
        }

        return $matched;
    }

    private function generateUniqueOrderNumber(): string
    {
        do {
            $candidate = 'EVB-' . now()->format('ymd') . '-' . strtoupper(substr((string) Str::ulid(), -6));
        } while (Order::where('order_number', $candidate)->exists());

        return $candidate;
    }

    private function generateUniqueInvoiceNumber(): string
    {
        do {
            $candidate = 'INV-' . now()->format('Y') . '-' . strtoupper(substr((string) Str::ulid(), -6));
        } while (Invoice::where('invoice_no', $candidate)->exists());

        return $candidate;
    }
}

