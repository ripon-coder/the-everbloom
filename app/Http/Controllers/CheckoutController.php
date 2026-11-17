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

        $isGuest = !Auth::guard('web')->check();
        $savedAddresses = [];

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
            'addressLine2' => '',
            'saveAddress' => false,
        ];

        if (!$isGuest) {
            $user = Auth::guard('web')->user();
            $dbAddresses = $user->addresses()
                ->with(['district', 'thana', 'area'])
                ->orderByDesc('is_default')
                ->get();

            if ($dbAddresses->isNotEmpty()) {
                $savedAddresses = $dbAddresses->map(function ($addr, $idx) use ($user) {
                    $recipient = trim(($addr->first_name ?? '') . ' ' . ($addr->last_name ?? ''));
                    if ($recipient === '') {
                        $recipient = trim(($user->first_name ?? '') . ' ' . ($user->last_name ?? ''));
                    }

                    return [
                        'id' => 'db_' . $addr->id,
                        'label' => $addr->label ?: ($idx === 0 ? 'Home' : 'Address ' . ($idx + 1)),
                        'is_default' => (bool) $addr->is_default,
                        'recipient_name' => $recipient ?: 'Customer',
                        'first_name' => $addr->first_name ?: ($user->first_name ?? ''),
                        'last_name' => $addr->last_name ?: ($user->last_name ?? ''),
                        'phone' => $addr->phone ?: ($user->phone ?? ''),
                        'country' => 'Bangladesh',
                        'district_id' => $addr->district_id ? (string) $addr->district_id : '',
                        'thana_id' => $addr->thana_id ? (string) $addr->thana_id : '',
                        'area_id' => $addr->area_id ? (string) $addr->area_id : '',
                        'district_name' => $addr->district?->name ?? '',
                        'thana_name' => $addr->thana?->name ?? '',
                        'area_name' => $addr->area?->name ?? '',
                        'address' => $addr->address_line1 ?? '',
                        'address_line2' => $addr->address_line2 ?? '',
                    ];
                })->values()->all();
            } else {
                $demoAddresses = config('demo.account.addresses', []);
                $savedAddresses = collect($demoAddresses)->map(function ($addr, $idx) {
                    $district = !empty($addr['district'])
                        ? District::where('name', $addr['district'])->first()
                        : null;
                    $thana = ($district && !empty($addr['thana']))
                        ? Thana::where('district_id', $district->id)->where('name', $addr['thana'])->first()
                        : null;
                    $area = ($thana && !empty($addr['area']))
                        ? Area::where('thana_id', $thana->id)->where('name', $addr['area'])->first()
                        : null;

                    $recipient = $addr['recipient_name']
                        ?? trim(($addr['first_name'] ?? '') . ' ' . ($addr['last_name'] ?? ''));

                    return [
                        'id' => $addr['id'] ?? ('demo_' . ($idx + 1)),
                        'label' => $addr['label'] ?? ($idx === 0 ? 'Home' : 'Address ' . ($idx + 1)),
                        'is_default' => (bool) ($addr['is_default'] ?? false),
                        'recipient_name' => $recipient ?: 'Customer',
                        'first_name' => $addr['first_name'] ?? '',
                        'last_name' => $addr['last_name'] ?? '',
                        'phone' => $addr['phone'] ?? '',
                        'country' => $addr['country'] ?? 'Bangladesh',
                        'district_id' => $district ? (string) $district->id : '',
                        'thana_id' => $thana ? (string) $thana->id : '',
                        'area_id' => $area ? (string) $area->id : '',
                        'district_name' => $district?->name ?? ($addr['district'] ?? ''),
                        'thana_name' => $thana?->name ?? ($addr['thana'] ?? ''),
                        'area_name' => $area?->name ?? ($addr['area'] ?? ''),
                        'address' => $addr['address_line1'] ?? ($addr['address'] ?? ''),
                        'address_line2' => $addr['address_line2'] ?? '',
                    ];
                })->values()->all();
            }

            $defaultAddr = collect($savedAddresses)->firstWhere('is_default', true) ?? ($savedAddresses[0] ?? null);

            $prefill['email'] = $user->email ?? '';
            $prefill['phone'] = $defaultAddr['phone'] ?? ($user->phone ?? '');
            $prefill['firstName'] = $defaultAddr['first_name'] ?? ($user->first_name ?? '');
            $prefill['lastName'] = $defaultAddr['last_name'] ?? ($user->last_name ?? '');

            if ($defaultAddr) {
                $prefill['district_id'] = $defaultAddr['district_id'] ?? '';
                $prefill['thana_id'] = $defaultAddr['thana_id'] ?? '';
                $prefill['area_id'] = $defaultAddr['area_id'] ?? '';
                $prefill['address'] = $defaultAddr['address'] ?? '';
                $prefill['addressLine2'] = $defaultAddr['address_line2'] ?? '';
            }
        }

        return view('frontend.checkout.index', [
            'prefillForm' => $prefill,
            'districts' => $districts,
            'savedAddresses' => $savedAddresses,
            'isGuest' => $isGuest,
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
            'email' => ['nullable', 'email', 'max:191'],
            'phone' => ['required', 'string', 'max:40'],
            'firstName' => ['required', 'string', 'max:100'],
            'lastName' => ['required', 'string', 'max:100'],
            'district_id' => ['required', 'integer', 'exists:districts,id,is_active,1'],
            'thana_id' => ['required', 'integer', 'exists:thanas,id,is_active,1'],
            'area_id' => ['required', 'integer', 'exists:areas,id,is_active,1'],
            'address' => ['required', 'string', 'max:500'],
            'addressLine2' => ['nullable', 'string', 'max:255'],
            'shippingMethod' => ['required', 'in:standard,express'],
            'paymentMethod' => ['required', 'in:cod_demo,bkash_demo,nagad_demo,card_demo'],
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

            $shippingCfg = config('demo.site.shipping', []);
            $freeThreshold = (float) ($shippingCfg['free_shipping_threshold'] ?? 500);
            $standardFee = (float) ($shippingCfg['standard_fee'] ?? 60);
            $expressFee = (float) ($shippingCfg['express_fee'] ?? 120);

            $rateKey = $validated['shippingMethod'];
            if ($rateKey === 'express') {
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

            $nextSeq = (Order::max('id') ?? 0) + 9205;
            $orderNumber = 'EVB-' . $nextSeq;
            while (Order::where('order_number', $orderNumber)->exists()) {
                $nextSeq++;
                $orderNumber = 'EVB-' . $nextSeq;
            }

            $paymentLabel = match ($validated['paymentMethod']) {
                'cod_demo' => 'Cash on Delivery (Demo)',
                'bkash_demo' => 'bKash (Demo)',
                'nagad_demo' => 'Nagad (Demo)',
                default => 'Card (Demo)',
            };

            $customerEmail = !empty($validated['email'])
                ? $validated['email']
                : (Auth::guard('web')->user()?->email ?? '');

            $order = Order::create([
                'order_number' => $orderNumber,
                'user_id' => Auth::guard('web')->id(),
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
                    'address_line2' => $validated['addressLine2'] ?? '',
                    'city' => $thana->name,
                    'state' => $district->name,
                    'postal_code' => $area->postal_code ?? '',
                    'phone' => $validated['phone'],
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
                'customer_email' => $customerEmail,
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
