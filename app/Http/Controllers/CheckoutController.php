<?php

namespace App\Http\Controllers;

use App\Models\Area;
use App\Models\Coupon;
use App\Models\District;
use App\Models\Invoice;
use App\Models\Order;
use App\Models\OrderItem;
use App\Models\Product;
use App\Models\ProductVariant;
use App\Models\Thana;
use App\Services\PurchaseBatchService;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\DB;
use Illuminate\Validation\ValidationException;
use Illuminate\View\View;

class CheckoutController extends Controller
{
    public function index(): View
    {
        $districts = District::active()->get(['id', 'name', 'bn_name', 'delivery_charge']);

        $prefill = [
            'email' => '',
            'phone' => '',
            'firstName' => '',
            'lastName' => '',
            'country' => 'Bangladesh',
            'district_id' => '',
            'thana_id' => '',
            'area_id' => '',
            'address' => '',
            'apartment' => '',
            'zip' => '',
        ];

        if (Auth::guard('web')->check()) {
            $user = Auth::guard('web')->user();
            $defaultAddress = $user->addresses()->where('is_default', true)->first()
                ?? $user->addresses()->first();

            $prefill['email'] = $user->email ?? '';
            $prefill['phone'] = $defaultAddress?->phone ?: ($user->phone ?? '');
            $prefill['firstName'] = $defaultAddress?->first_name ?: ($user->first_name ?? '');
            $prefill['lastName'] = $defaultAddress?->last_name ?: ($user->last_name ?? '');

            if ($defaultAddress) {
                $prefill['district_id'] = $defaultAddress->district_id ? (string) $defaultAddress->district_id : '';
                $prefill['thana_id'] = $defaultAddress->thana_id ? (string) $defaultAddress->thana_id : '';
                $prefill['area_id'] = $defaultAddress->area_id ? (string) $defaultAddress->area_id : '';
                $prefill['address'] = $defaultAddress->address_line1 ?? '';
                $prefill['apartment'] = $defaultAddress->address_line2 ?? '';
                $prefill['zip'] = $defaultAddress->postal_code ?? '';
            }
        }

        return view('frontend.checkout.index', [
            'prefillForm' => $prefill,
            'districts' => $districts,
        ]);
    }

    public function validateCoupon(Request $request): JsonResponse
    {
        $validated = $request->validate([
            'code' => ['required', 'string', 'max:60'],
            'subtotal' => ['nullable', 'numeric', 'min:0'],
        ]);

        $code = strtoupper(trim($validated['code']));
        $subtotal = (float) ($validated['subtotal'] ?? 0);

        $coupon = Coupon::where('code', $code)->first();

        if (!$coupon || !$coupon->isValidForSubtotal($subtotal)) {
            return response()->json([
                'valid' => false,
                'message' => 'This promotional code is invalid, expired, or does not meet order requirements.',
            ], 422);
        }

        return response()->json([
            'valid' => true,
            'code' => $coupon->code,
            'type' => $coupon->type,
            'value' => (float) $coupon->value,
            'label' => $coupon->label,
        ]);
    }

    public function store(Request $request, PurchaseBatchService $purchaseBatchService): JsonResponse
    {
        $validated = $request->validate([
            'email' => ['required', 'email', 'max:191'],
            'phone' => ['nullable', 'string', 'max:40'],
            'firstName' => ['required', 'string', 'max:100'],
            'lastName' => ['required', 'string', 'max:100'],
            'district_id' => ['required', 'integer', 'exists:districts,id,is_active,1'],
            'thana_id' => ['required', 'integer', 'exists:thanas,id,is_active,1'],
            'area_id' => ['required', 'integer', 'exists:areas,id,is_active,1'],
            'address' => ['required', 'string', 'max:255'],
            'apartment' => ['nullable', 'string', 'max:255'],
            'zip' => ['nullable', 'string', 'max:30'],
            'shippingMethod' => ['required', 'in:standard,express'],
            'paymentMethod' => ['required', 'in:card_demo,digital_wallet_demo'],
            'discountCode' => ['nullable', 'string', 'max:60'],
            'items' => ['required', 'array', 'min:1'],
            'items.*.productId' => ['required', 'integer', 'exists:products,id'],
            'items.*.variantId' => ['nullable', 'string', 'max:80'],
            'items.*.qty' => ['required', 'integer', 'min:1', 'max:10'],
        ]);

        $district = District::where('id', $validated['district_id'])->where('is_active', true)->firstOrFail();
        $thana = Thana::where('id', $validated['thana_id'])->where('district_id', $district->id)->where('is_active', true)->firstOrFail();
        $area = Area::where('id', $validated['area_id'])->where('thana_id', $thana->id)->where('is_active', true)->firstOrFail();

        $order = DB::transaction(function () use ($validated, $district, $thana, $area, $purchaseBatchService) {
            $subtotal = 0.0;
            $sumLineCosts = 0.0;
            $itemsCount = 0;
            $preparedItems = [];

            foreach ($validated['items'] as $line) {
                /** @var Product $product */
                $product = Product::with('productVariants')->lockForUpdate()->findOrFail($line['productId']);
                $qty = (int) $line['qty'];

                /** @var ProductVariant|null $variant */
                $variant = null;
                if (!empty($line['variantId'])) {
                    $variant = $product->productVariants
                        ->first(fn (ProductVariant $v) => $v->size === $line['variantId'] || $v->title === $line['variantId'] || (string) $v->id === (string) $line['variantId']);
                }
                $variant = $variant ?? $product->productVariants->first();

                $unitPrice = $variant ? (float) $variant->price : (float) $product->price;
                $lineTotal = round($unitPrice * $qty, 2);

                $unitBuyPrice = (float) ($variant?->avg_buy_price > 0 ? $variant->avg_buy_price : $product->avg_buy_price);
                $lineCostTotal = round($unitBuyPrice * $qty, 2);
                $unitProfit = round($unitPrice - $unitBuyPrice, 2);
                $lineProfit = round($lineTotal - $lineCostTotal, 2);

                if ($variant) {
                    if ($variant->stock_count < $qty && $variant->stock_status === 'out_of_stock') {
                        throw ValidationException::withMessages([
                            'items' => ["{$product->name} ({$variant->size}) is currently out of stock."],
                        ]);
                    }
                    $newVariantStock = max(0, $variant->stock_count - $qty);
                    $variant->update([
                        'stock_count' => $newVariantStock,
                        'stock_status' => $this->resolveStockStatus($newVariantStock, $product->min_threshold),
                    ]);
                }

                $newProductStock = max(0, $product->stock_count - $qty);
                $product->update([
                    'stock_count' => $newProductStock,
                    'stock_status' => $this->resolveStockStatus($newProductStock, $product->min_threshold),
                ]);

                $purchaseBatchService->deductFromAvailableBatches($product->id, $qty);

                $subtotal += $lineTotal;
                $sumLineCosts += $lineCostTotal;
                $itemsCount += $qty;

                $preparedItems[] = [
                    'product_id' => $product->id,
                    'product_variant_id' => $variant?->id,
                    'name' => $product->name,
                    'slug' => $product->slug,
                    'variant' => $variant?->title ?: ($variant?->size ?? ($line['variantId'] ?? ($product->sizes[0] ?? null))),
                    'image' => $variant?->image ?: ($product->images[0] ?? null),
                    'image_source' => $variant?->image ? ($variant->image_source ?: 'local') : ($product->image_source ?: 'local'),
                    'qty' => $qty,
                    'price' => $unitPrice,
                    'unit_buy_price' => $unitBuyPrice,
                    'line_total' => $lineTotal,
                    'line_cost_total' => $lineCostTotal,
                    'unit_profit' => $unitProfit,
                    'line_profit' => $lineProfit,
                ];
            }

            $discountAmount = 0.0;
            $appliedCoupon = null;
            $isFreeShippingCoupon = false;

            if (!empty($validated['discountCode'])) {
                $code = strtoupper(trim($validated['discountCode']));
                $coupon = Coupon::where('code', $code)->lockForUpdate()->first();
                if ($coupon && $coupon->isValidForSubtotal($subtotal)) {
                    $appliedCoupon = $coupon;
                    if ($coupon->type === 'percentage') {
                        $discountAmount = round($subtotal * ((float) $coupon->value / 100), 2);
                    } elseif ($coupon->type === 'fixed') {
                        $discountAmount = min($subtotal, (float) $coupon->value);
                    } elseif ($coupon->type === 'shipping') {
                        $isFreeShippingCoupon = true;
                    }
                    $coupon->increment('used_count');
                }
            }

            $shippingRates = config('demo.site.shipping_rates', []);
            $rateKey = $validated['shippingMethod'];
            $rate = $shippingRates[$rateKey] ?? ['name' => 'Standard Ground Botanical Shipping', 'cost' => 6.00, 'free_threshold' => 75.00];

            $freeThreshold = (float) ($rate['free_threshold'] ?? 75.00);
            $shippingCost = ($isFreeShippingCoupon || ($freeThreshold > 0 && $subtotal >= $freeThreshold))
                ? 0.0
                : (float) ($rate['cost'] ?? 6.00);

            $total = max(0, round($subtotal - $discountAmount + $shippingCost, 2));
            $totalCost = round($sumLineCosts, 2);
            $totalProfit = round(($subtotal - $discountAmount) - $totalCost, 2);

            $nextSeq = (Order::max('id') ?? 0) + 9205;
            $orderNumber = 'EVB-' . $nextSeq;
            while (Order::where('order_number', $orderNumber)->exists()) {
                $nextSeq++;
                $orderNumber = 'EVB-' . $nextSeq;
            }

            $paymentLabel = $validated['paymentMethod'] === 'digital_wallet_demo'
                ? 'Apple Pay / Digital Wallet'
                : 'Credit Card (Demo)';

            $order = Order::create([
                'order_number' => $orderNumber,
                'user_id' => Auth::guard('web')->id(),
                'coupon_id' => $appliedCoupon?->id,
                'customer_email' => $validated['email'],
                'customer_phone' => $validated['phone'] ?? null,
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
                'delivery_method' => $rate['name'] ?? 'Standard Ground Botanical Shipping',
                'payment_method' => $paymentLabel,
                'payment_status' => 'Paid',
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
                    'address_line2' => $validated['apartment'] ?? '',
                    'city' => $thana->name,
                    'state' => $district->name,
                    'postal_code' => ($validated['zip'] ?? null) ?: ($area->postal_code ?? ''),
                    'phone' => $validated['phone'] ?? '',
                ],
                'placed_at' => now(),
            ]);

            foreach ($preparedItems as $item) {
                OrderItem::create(array_merge($item, ['order_id' => $order->id]));
            }

            $invoiceSeq = (Invoice::max('id') ?? 0) + 1001;
            Invoice::create([
                'order_id' => $order->id,
                'invoice_no' => 'INV-' . now()->format('Y') . '-' . $invoiceSeq,
                'customer_name' => trim($validated['firstName'] . ' ' . $validated['lastName']),
                'customer_email' => $validated['email'],
                'amount' => $total,
                'status' => 'Paid',
                'payment_method' => $paymentLabel,
                'issued_at' => now(),
            ]);

            Cache::forget('storefront_catalog_json');

            return $order;
        });

        return response()->json([
            'success' => true,
            'order_number' => $order->order_number,
            'redirect_url' => Auth::guard('web')->check()
                ? route('account.order.details', $order->order_number)
                : route('shop.index'),
        ]);
    }

    private function resolveStockStatus(int $stockCount, int $minThreshold = 10): string
    {
        if ($stockCount <= 0) {
            return 'out_of_stock';
        }
        if ($stockCount <= $minThreshold) {
            return 'low_stock';
        }

        return 'in_stock';
    }
}
