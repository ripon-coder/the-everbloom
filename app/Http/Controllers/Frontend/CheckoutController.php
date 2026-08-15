<?php

namespace App\Http\Controllers\Frontend;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\DB;
use App\Repositories\Contracts\CheckoutCalculationRepository;
use App\Repositories\Contracts\OrderRepository;
use App\Repositories\Contracts\CouponRepository;
use App\Models\Order;
use App\Models\Product;
use App\Models\ProductVariant;

class CheckoutController extends Controller
{
    protected $checkoutCalculationRepository;
    protected $orderRepository;
    protected $couponRepository;

    public function __construct(
        CheckoutCalculationRepository $checkoutCalculationRepository,
        OrderRepository $orderRepository,
        CouponRepository $couponRepository
    ) {
        $this->checkoutCalculationRepository = $checkoutCalculationRepository;
        $this->orderRepository = $orderRepository;
        $this->couponRepository = $couponRepository;
    }

    public function calculate(Request $request)
    {
        $cart = $request->input('cart', []);
        $districtId = $request->input('district_id');
        $couponCode = $request->input('coupon_code');

        $result = $this->checkoutCalculationRepository->calculate($cart, $districtId, $couponCode);

        return response()->json([
            'success' => true,
            'data' => $result,
            'has_errors' => !empty($result['errors'])
        ]);
    }

    /**
     * Save/update incomplete (draft) order as user fills out checkout form.
     */
    public function saveIncompleteOrder(Request $request)
    {
        // Step 1: Request data validation
        $validator = \Illuminate\Support\Facades\Validator::make($request->all(), [
            'full_name'      => 'nullable|string|max:255',
            'phone'          => 'nullable|string|max:20',
            'address'        => 'nullable|string|max:500',
            'district_id'    => 'nullable|integer|exists:districts,id',
            'cart'           => 'required|array|min:1',
            'cart.*.product_id' => 'required|integer',
            'cart.*.quantity'   => 'required|integer|min:1|max:30',
        ]);

        if ($validator->fails()) {
            return response()->json([
                'success' => false,
                'message' => 'Validation failed. Draft order not saved.',
                'errors'  => $validator->errors()
            ], 422);
        }

        $cart = $request->input('cart', []);
        $fullName = trim($request->input('full_name', ''));
        $phone = trim($request->input('phone', ''));
        $address = trim($request->input('address', ''));
        $districtId = $request->input('district_id');
        $draftOrderId = $request->input('draft_order_id') ?: session('draft_order_id');

        // Require at least a non-empty name or phone number
        if (empty($phone) && empty($fullName)) {
            return response()->json([
                'success' => false,
                'message' => 'Customer name or phone number is required to save draft order.'
            ], 422);
        }

        // Validate cart items against DB
        $validation = $this->validateCartItems($cart);
        if (!empty($validation['errors']) || empty($validation['items'])) {
            return response()->json([
                'success' => false,
                'message' => 'Cart validation failed. Draft order not saved.',
                'validation_errors' => $validation['errors'] ?? ['No valid items in cart.']
            ], 422);
        }

        $validatedItems = $validation['items'];
        $subtotal = collect($validatedItems)->sum('line_total');

        $isAllFreeDelivery = !empty($validatedItems) && collect($validatedItems)->every(function ($item) {
            return !empty($item['is_free_delivery']);
        });

        $shippingCost = 0;
        if ($districtId && !$isAllFreeDelivery) {
            $district = \App\Models\District::find($districtId);
            if ($district) {
                $shippingCost = (float) $district->delivery_charge;
            }
        }

        $totalAmount = max(0, $subtotal + $shippingCost);
        $user = auth()->user();
        $userId = $user ? $user->id : null;
        $sessionId = session()->getId();

        try {
            return DB::transaction(function () use ($draftOrderId, $userId, $sessionId, $fullName, $phone, $address, $districtId, $subtotal, $shippingCost, $totalAmount, $validatedItems) {
                $order = null;

                if ($draftOrderId || $sessionId) {
                    $order = Order::where('status', 'incomplete')
                        ->where(function ($q) use ($draftOrderId, $sessionId) {
                            if ($draftOrderId) {
                                $q->where('id', $draftOrderId);
                            }
                            if ($sessionId) {
                                $q->orWhere('session_id', $sessionId);
                            }
                        })->first();
                }

                if (!$order) {
                    $order = new Order();
                    $order->order_number = Order::generateOrderNumber();
                    $order->status = 'incomplete';
                }

                $order->user_id = $userId;
                $order->session_id = $sessionId;
                $order->subtotal = $subtotal;
                $order->shipping_amount = $shippingCost;
                $order->total_amount = $totalAmount;
                $order->payment_status = 'pending';
                $order->payment_method = 'cod';
                $order->save();

                // Save or update shipping address
                $orderAddress = $order->orderAddress ?: new \App\Models\OrderAddress();
                $orderAddress->order_id = $order->id;
                $orderAddress->user_id = $userId;
                $orderAddress->name = $fullName ?: 'Guest Customer';
                $orderAddress->phone_number = $phone ?: 'N/A';
                $orderAddress->address = $address ?: 'N/A';
                $orderAddress->district_id = $districtId ?: null;
                $orderAddress->save();

                // Save order products (permanently purge previous draft products to prevent duplicate rows)
                \App\Models\OrderProduct::withTrashed()->where('order_id', $order->id)->forceDelete();
                foreach ($validatedItems as $item) {
                    $order->orderProducts()->create([
                        'product_id'         => $item['product_id'],
                        'product_variant_id' => $item['variant_id'] ?? null,
                        'quantity'           => $item['quantity'],
                        'weight'             => $item['weight'] ?? 0,
                        'buying_price'       => $item['buying_price'] ?? 0,
                        'unit_price'         => $item['unit_final_price'],
                        'total_price'        => $item['line_total'],
                    ]);
                }

                session()->put('draft_order_id', $order->id);

                return response()->json([
                    'success'        => true,
                    'draft_order_id' => $order->id,
                    'order_number'   => $order->order_number,
                ]);
            });
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Failed to save draft order.',
                'error'   => config('app.debug') ? $e->getMessage() : null
            ], 500);
        }
    }

    /**
     * Place a COD order with cache lock to prevent double submissions.
     */
    public function placeOrder(Request $request)
    {
        $request->validate([
            'full_name'      => 'required|string|max:255',
            'phone'          => 'required|string|max:20',
            'address'        => 'required|string|max:500',
            'district_id'    => 'required|integer|exists:districts,id',
            'payment_method' => 'required|in:cod,online',
            'cart'           => 'required|array|min:1',
            'cart.*.product_id' => 'required|integer',
            'cart.*.quantity'    => 'required|integer|min:1|max:30',
            'coupon_code'    => 'nullable|string',
        ]);

        $user = auth()->user();
        $userId = $user ? $user->id : null;

        // Cache lock: prevent duplicate order submission (10 second lock per user/ip)
        $lockKey = $userId ? 'order_lock_user_' . $userId : 'order_lock_ip_' . md5($request->ip() . '_' . $request->input('phone'));
        $lock = Cache::lock($lockKey, 10);

        if (!$lock->get()) {
            return response()->json([
                'success' => false,
                'message' => 'Your order is already being processed. Please wait.'
            ], 429);
        }

        try {
            $cart = $request->input('cart');
            $districtId = $request->input('district_id');
            $couponCode = $request->input('coupon_code', '');
            $draftOrderId = $request->input('draft_order_id') ?: session('draft_order_id');

            // ========================================
            // STEP 1: Validate every cart item against DB
            // ========================================
            $validation = $this->validateCartItems($cart);

            if (!empty($validation['errors'])) {
                return response()->json([
                    'success' => false,
                    'message' => 'Some items failed validation. Please review your cart.',
                    'validation_errors' => $validation['errors'],
                ], 422);
            }

            $validatedItems = $validation['items'];

            if (empty($validatedItems)) {
                return response()->json([
                    'success' => false,
                    'message' => 'No valid items in your cart.'
                ], 422);
            }

            // ========================================
            // STEP 2: Validate coupon (if provided)
            // ========================================
            $subtotal = collect($validatedItems)->sum('line_total');
            $couponDiscount = 0;

            if ($couponCode) {
                $couponDiscount = $this->couponRepository->getDiscountAmount($couponCode, $subtotal);
                if ($couponDiscount <= 0) {
                    return response()->json([
                        'success' => false,
                        'message' => 'Invalid or expired coupon code.',
                    ], 422);
                }
            }

            // ========================================
            // STEP 3: Validate shipping district & cost
            // ========================================
            $district = \App\Models\District::find($districtId);
            if (!$district) {
                return response()->json([
                    'success' => false,
                    'message' => 'Invalid district selected.',
                ], 422);
            }

            $isAllFreeDelivery = !empty($validatedItems) && collect($validatedItems)->every(function ($item) {
                return !empty($item['is_free_delivery']);
            });

            $shippingCost = $isAllFreeDelivery ? 0.0 : (float) $district->delivery_charge;

            // ========================================
            // STEP 4: Calculate final totals (server-side only)
            // ========================================
            $totalAmount = $subtotal + $shippingCost - $couponDiscount;

            // Calculate profit: total amount - total buying cost
            $totalBuyingCost = collect($validatedItems)->sum(function ($item) {
                return $item['buying_price'] * $item['quantity'];
            });
            $totalProfit = $totalAmount - $totalBuyingCost - $shippingCost;

            // Calculate total weight
            $totalWeight = collect($validatedItems)->sum(function ($item) {
                return $item['weight'] * $item['quantity'];
            });

            // Check if existing draft order should be converted
            $orderNumber = null;
            $sessionId = session()->getId();
            if ($draftOrderId || $sessionId) {
                $existingDraft = Order::where('status', 'incomplete')
                    ->where(function ($q) use ($draftOrderId, $sessionId) {
                        if ($draftOrderId) {
                            $q->where('id', $draftOrderId);
                        }
                        if ($sessionId) {
                            $q->orWhere('session_id', $sessionId);
                        }
                    })->first();

                if ($existingDraft) {
                    $orderNumber = $existingDraft->order_number;
                    \App\Models\OrderProduct::withTrashed()->where('order_id', $existingDraft->id)->forceDelete();
                    $existingDraft->orderAddress()->delete();
                    $existingDraft->flashSale()->delete();
                    $existingDraft->forceDelete();
                }
            }

            // Build order data
            $orderInfo = [
                'user_id'                => $userId,
                'session_id'             => $sessionId,
                'order_number'           => $orderNumber ?: Order::generateOrderNumber(),
                'subtotal'               => $subtotal,
                'total_amount'           => $totalAmount,
                'shipping_amount'        => $shippingCost,
                'coupon_discount_amount' => $couponDiscount,
                'coupon_used'            => $couponCode ?: null,
                'profit'                 => $totalProfit,
                'weight'                 => $totalWeight,
                'payment_method'         => 'cod',
                'payment_status'         => 'pending',
                'status'                 => 'pending',
            ];

            // Build variant/product info for order_products
            $variantInfo = [];
            $flashSaleDiscount = [];

            foreach ($validatedItems as $item) {
                $variantInfo[] = [
                    'product_id'         => $item['product_id'],
                    'product_variant_id' => $item['variant_id'] ?? null,
                    'quantity'           => $item['quantity'],
                    'weight'             => $item['weight'],
                    'buying_price'       => $item['buying_price'],
                    'unit_price'         => $item['unit_final_price'],
                    'total_price'        => $item['line_total'],
                ];

                // Track flash sale discount if applicable
                if (!empty($item['flash_sale_data'])) {
                    $flashSaleDiscount[] = $item['flash_sale_data'];
                }
            }

            // Build shipping address
            $shippingAddress = [
                'user_id'      => $userId,
                'name'         => $request->input('full_name'),
                'phone_number' => $request->input('phone'),
                'address'      => $request->input('address'),
                'district_id'  => $districtId,
            ];

            // ========================================
            // STEP 5: Create order (DB transaction + stock decrement)
            // ========================================
            $order = $this->orderRepository->createOrder($orderInfo, $variantInfo, $shippingAddress, $flashSaleDiscount);

            // Clear session cart & draft order
            session()->forget('draft_order_id');
            if ($request->boolean('is_buy_now') || $request->input('type') === 'buy_now') {
                session()->forget('buy_now_cart');
            } else {
                session()->forget('cart');
            }

            return response()->json([
                'success'      => true,
                'message'      => 'Order placed successfully!',
                'order_number' => $order->order_number,
                'order_id'     => $order->id,
            ]);

        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Something went wrong while placing your order. Please try again.',
                'error'   => config('app.debug') ? $e->getMessage() : null,
            ], 500);
        } finally {
            $lock->release();
        }
    }

    /**
     * Validate each cart item against the database.
     * Checks: product existence, status, variant existence, status, stock, price, and flash sale.
     */
    private function validateCartItems(array $cartItems): array
    {
        $validatedItems = [];
        $errors = [];

        // Consolidate/merge duplicate items in incoming cart payload by product_id and variant_id
        $consolidatedCart = [];
        foreach ($cartItems as $item) {
            $pId = $item['product_id'] ?? null;
            $vId = $item['variant_id'] ?? null;
            if (!$pId) continue;
            $key = $pId . '_' . ($vId ?: '0');
            if (!isset($consolidatedCart[$key])) {
                $consolidatedCart[$key] = $item;
            } else {
                $consolidatedCart[$key]['quantity'] = (int) ($consolidatedCart[$key]['quantity'] ?? 1) + (int) ($item['quantity'] ?? 1);
            }
        }
        $cartItems = array_values($consolidatedCart);

        foreach ($cartItems as $index => $item) {
            $productId = $item['product_id'] ?? null;
            $variantId = $item['variant_id'] ?? null;
            $requestedQty = (int) ($item['quantity'] ?? 1);

            // --- Product existence ---
            if (!$productId) {
                $errors[] = "Item #" . ($index + 1) . ": Missing product ID.";
                continue;
            }

            $product = Product::with([
                'flashSales' => fn($q) => $q->active()
            ])->find($productId);

            if (!$product) {
                $errors[] = "Product not found (ID: {$productId}).";
                continue;
            }

            // --- Product active status ---
            if ($product->status !== \App\Constants\ProductStatus::ACTIVE) {
                $errors[] = "\"{$product->name}\" is no longer available for purchase.";
                continue;
            }

            // --- Variant validation ---
            $unitOriginalPrice = 0;
            $unitFinalPrice = 0;
            $availableStock = 0;
            $buyingPrice = 0;

            if ($variantId) {
                $variant = ProductVariant::where('id', $variantId)
                    ->where('product_id', $productId) // Ensure variant belongs to product
                    ->first();

                if (!$variant) {
                    $errors[] = "Variant not found for \"{$product->name}\".";
                    continue;
                }

                // --- Variant active status ---
                if ($variant->status !== \App\Constants\ProductVariantStatus::ACTIVE) {
                    $errors[] = "Selected variant for \"{$product->name}\" is no longer available.";
                    continue;
                }

                $availableStock = $variant->stock;
                $buyingPrice = (float) $variant->buying_price;
                $unitOriginalPrice = (float) $variant->sell_price;
                $itemWeight = (float) $variant->weight;
                $unitFinalPrice = $unitOriginalPrice;

                // Apply variant discount_price if set
                if ($variant->discount_price > 0) {
                    $unitFinalPrice = (float) $variant->discount_price;
                }

                // Apply flash sale discount on top
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
                // No variant — use product-level data
                $availableStock = $product->variants()->active()->sum('stock');
                $itemWeight = (float) $product->variants()->active()->first()?->weight ?: 0;
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

            // --- Stock validation ---
            if ($availableStock <= 0) {
                $errors[] = "\"{$product->name}\" is out of stock.";
                continue;
            }

            if ($requestedQty > $availableStock) {
                $errors[] = "Only {$availableStock} unit(s) of \"{$product->name}\" available. You requested {$requestedQty}.";
                continue;
            }

            // --- Quantity cap ---
            if ($requestedQty > 30) {
                $errors[] = "Maximum 30 units allowed per product for \"{$product->name}\".";
                continue;
            }

            // --- Build validated item ---
            $lineTotal = $unitFinalPrice * $requestedQty;

            $validatedItem = [
                'product_id'       => $product->id,
                'variant_id'       => $variantId,
                'quantity'         => $requestedQty,
                'buying_price'     => $buyingPrice,
                'unit_base_price'  => $unitOriginalPrice,
                'unit_final_price' => $unitFinalPrice,
                'line_total'       => $lineTotal,
                'weight'           => $itemWeight,
                'is_free_delivery' => (bool) $product->is_free_delivery,
                'flash_sale_data'  => null,
            ];

            // Build flash sale tracker data
            if ($unitOriginalPrice > $unitFinalPrice && $product->flashSales && $product->flashSales->isNotEmpty()) {
                $flashSale = $product->flashSales->first();
                $pivot = $flashSale->pivot;
                $discountPerUnit = $unitOriginalPrice - $unitFinalPrice;

                $validatedItem['flash_sale_data'] = [
                    'product_id'            => $product->id,
                    'product_variant_id'    => $variantId,
                    'flash_sale_slug'       => $flashSale->slug ?? '',
                    'original_price'        => $unitOriginalPrice,
                    'discount_amount'       => $discountPerUnit,
                    'discounted_price'      => $unitFinalPrice,
                    'discount_type'         => ($pivot->discount_percentage ?? 0) > 0 ? 'percentage' : 'fixed',
                    'quantity'              => $requestedQty,
                    'total_discounted_price' => $discountPerUnit * $requestedQty,
                ];
            }

            $validatedItems[] = $validatedItem;
        }

        return [
            'items'  => $validatedItems,
            'errors' => $errors,
        ];
    }
}

