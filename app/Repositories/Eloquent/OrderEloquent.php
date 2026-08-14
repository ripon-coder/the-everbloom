<?php

namespace App\Repositories\Eloquent;

use App\Models\Order;
use App\Models\OrderProduct;
use App\Models\Payment;
use App\Models\OrderTracking;
use App\Models\Product;
use App\Repositories\Contracts\CouponRepository;
use App\Repositories\Contracts\OrderRepository;
use Illuminate\Pagination\LengthAwarePaginator;
use App\Models\Category;
use App\Models\User;
use App\Models\Brand;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\DB;

class OrderEloquent implements OrderRepository
{
    protected $model;

    public function __construct(Order $order)
    {
        $this->model = $order;
    }

    /**
     * Get all orders with pagination and filtering.
     */
    public function getAll(array $filters = [], int $perPage = 15): LengthAwarePaginator
    {
        $query = Order::with([
            'user',
            'orderAddress.district',
            'orderProducts.product.firstImage',
            'orderProducts.productVariant'
        ]);

        if (!empty($filters['status'])) {
            $query->where('status', $filters['status']);
        }

        if (!empty($filters['payment_status'])) {
            $query->where('payment_status', $filters['payment_status']);
        }

        if (!empty($filters['search'])) {
            $search = $filters['search'];
            $query->where(function($q) use ($search) {
                $q->where('order_number', 'LIKE', "%{$search}%")
                  ->orWhereHas('user', function($userQuery) use ($search) {
                      $userQuery->where('name', 'LIKE', "%{$search}%")
                               ->orWhere('email', 'LIKE', "%{$search}%");
                  });
            });
        }

        return $query->orderByDesc("id")->paginate($perPage);
    }


    /**
     * Get an order by ID with relationships.
     */
    public function findById(int $id): ?Order
    {
        return $this->model
            ->select([
                'id',
                'user_id',
                'order_number',
                'status',
                'payment_status',
                'subtotal',
                'coupon_discount_amount',
                'flash_discount_amount',
                'tax_amount',
                'shipping_amount',
                'admin_shipping_amount',
                'profit',
                'total_amount',
                'coupon_used',
                'payment_method',
                'payment_account',
                'notes',
                'created_at'
            ])
            ->with([
                'user:id,name,email',
                'trackings:id,order_id,status,created_at',
                'orderAddress:id,order_id,name,address,zone,phone_number,district_id',
                'orderAddress.district:id,name',
                'orderProducts:id,order_id,product_id,product_variant_id,quantity,unit_price,total_price,is_free_shipping,buying_price',
                'orderProducts.product:id,name,slug',
                'orderProducts.product.firstImage',
                'orderProducts.productVariant:id,product_id,buying_price,sku',
                'orderProducts.productVariant.variantAttributes:id,product_variant_id,attribute_id,attribute_value_id',
                'orderProducts.productVariant.variantAttributes.attribute:id,name',
                'orderProducts.productVariant.variantAttributes.attributeValue:id,value',
            ])
            ->findOrFail($id);
    }


    /**
     * Create a new order with products.
     */
    public function create(array $data): Order
    {
        return DB::transaction(function () use ($data) {
            // Generate order number if not provided
            if (empty($data['order_number'])) {
                $data['order_number'] = Order::generateOrderNumber();
            }

            // Create the order
            $order = $this->model->create([
                'user_id' => $data['user_id'],
                'order_number' => $data['order_number'],
                'subtotal' => $data['subtotal'],
                'coupon_discount_amount' => $data['coupon_discount_amount'] ?? 0,
                'flash_discount_amount' => $data['flash_discount_amount'] ?? 0,
                'tax_amount' => $data['tax_amount'] ?? 0,
                'shipping_amount' => $data['shipping_amount'] ?? 0,
                'admin_shipping_amount' => $data['admin_shipping_amount'] ?? 0,
                'total_amount' => $data['total_amount'],
                'coupon_used' => $data['coupon_used'] ?? null,
                'profit' => $data['profit'] ?? 0,
                'weight' => $data['weight'] ?? 0,
                'status' => $data['status'] ?? 'pending',
                'payment_status' => $data['payment_status'] ?? 'pending',
                'payment_method' => $data['payment_method'] ?? null,
                'shipping_address' => $data['shipping_address'],
                'billing_address' => $data['billing_address'] ?? $data['shipping_address'],
                'notes' => $data['notes'] ?? null,
            ]);

            // Create order products
            if (!empty($data['products'])) {
                foreach ($data['products'] as $productData) {
                    $order->orderProducts()->create([
                        'product_id' => $productData['product_id'],
                        'product_variant_id' => $productData['product_variant_id'] ?? null,
                        'quantity' => $productData['quantity'],
                        'unit_price' => $productData['unit_price'],
                        'total_price' => $productData['total_price'],
                        'discount_amount' => $productData['discount_amount'] ?? 0,
                        'notes' => $productData['notes'] ?? null,
                    ]);
                }
            }

            return $order->fresh(['user', 'orderProducts.product']);
        });
    }

    /**
     * Update an order.
     */
    public function update(int $id, array $data): bool
    {
        return DB::transaction(function () use ($id, $data) {
            $order = $this->model->findOrFail($id);

            // Update order details
            $order->update([
                'user_id' => $data['user_id'] ?? $order->user_id,
                'subtotal' => $data['subtotal'] ?? $order->subtotal,
                'coupon_discount_amount' => $data['coupon_discount_amount'] ?? $order->coupon_discount_amount,
                'flash_discount_amount' => $data['flash_discount_amount'] ?? $order->flash_discount_amount,
                'tax_amount' => $data['tax_amount'] ?? $order->tax_amount,
                'shipping_amount' => $data['shipping_amount'] ?? $order->shipping_amount,
                'admin_shipping_amount' => $data['admin_shipping_amount'] ?? $order->admin_shipping_amount,
                'total_amount' => $data['total_amount'] ?? $order->total_amount,
                'coupon_used' => $data['coupon_used'] ?? $order->coupon_used,
                'profit' => $data['profit'] ?? $order->profit,
                'weight' => $data['weight'] ?? $order->weight,
                'status' => $data['status'] ?? $order->status,
                'payment_status' => $data['payment_status'] ?? $order->payment_status,
                'payment_method' => $data['payment_method'] ?? $order->payment_method,
                'shipping_address' => $data['shipping_address'] ?? $order->shipping_address,
                'billing_address' => $data['billing_address'] ?? $order->billing_address,
                'notes' => $data['notes'] ?? $order->notes,
            ]);

            // Update order products if provided
            if (!empty($data['products'])) {
                // Remove existing products permanently to prevent duplicate rows
                OrderProduct::withTrashed()->where('order_id', $order->id)->forceDelete();

                // Add new products
                foreach ($data['products'] as $productData) {
                    $order->orderProducts()->create([
                        'product_id' => $productData['product_id'],
                        'product_variant_id' => $productData['product_variant_id'] ?? null,
                        'quantity' => $productData['quantity'],
                        'unit_price' => $productData['unit_price'],
                        'total_price' => $productData['total_price'],
                        'discount_amount' => $productData['discount_amount'] ?? 0,
                        'notes' => $productData['notes'] ?? null,
                    ]);
                }
            }

            return true;
        });
    }

    /**
     * Delete an order.
     */
    public function delete(int $id): bool
    {
        $order = $this->model->findOrFail($id);
        return $order->delete();
    }

    /**
     * Restore a deleted order.
     */
    public function restore(int $id): bool
    {
        $order = $this->model->withTrashed()->findOrFail($id);
        return $order->restore();
    }

    /**
     * Force delete an order.
     */
    public function forceDelete(int $id): bool
    {
        $order = $this->model->withTrashed()->findOrFail($id);
        return $order->forceDelete();
    }

    /**
     * Update the status of an order.
     */
    public function updateStatus(int $id, string $status): bool
    {
        $order = $this->model->findOrFail($id);
        $order->status = $status;
        return $order->save();
    }

    /**
     * Update the payment status of an order.
     */
    public function updatePaymentStatus(int $id, string $paymentStatus): bool
    {
        $order = $this->model->findOrFail($id);
        $order->payment_status = $paymentStatus;
        return $order->save();
    }

    /**
     * Get payments for an order.
     */
    public function getPayments(int $orderId)
    {
        return $this->model->findOrFail($orderId)->payments()->latest()->get();
    }

    /**
     * Create a payment for an order.
     */
    public function createPayment(int $orderId, array $paymentData): Payment
    {
        $order = $this->model->findOrFail($orderId);
        $payment = $order->payments()->create($paymentData);

        // Update order payment status based on payment
        $this->updateOrderPaymentStatus($order);

        return $payment;
    }

    /**
     * Update a payment.
     */
    public function updatePayment(int $paymentId, array $paymentData): bool
    {
        $payment = Payment::findOrFail($paymentId);
        $result = $payment->update($paymentData);

        // Update order payment status based on payment changes
        $this->updateOrderPaymentStatus($payment->order);

        return $result;
    }

    /**
     * Delete a payment.
     */
    public function deletePayment(int $paymentId): bool
    {
        $payment = Payment::findOrFail($paymentId);
        $order = $payment->order;
        $result = $payment->delete();

        // Update order payment status based on payment deletion
        $this->updateOrderPaymentStatus($order);

        return $result;
    }

    /**
     * Update order payment status based on payments.
     */
    private function updateOrderPaymentStatus(Order $order): void
    {
        $calculatedStatus = $order->getCalculatedPaymentStatus();

        // Only update if different from current status
        if ($order->payment_status !== $calculatedStatus) {
            $order->payment_status = $calculatedStatus;
            $order->save();
        }
    }

    /**
     * Get trackings for an order.
     */
    public function getTrackings(int $orderId)
    {
        return $this->model->findOrFail($orderId)->trackings()->latest()->get();
    }

    /**
     * Create a tracking for an order.
     */
    public function createTracking(int $orderId, array $trackingData): OrderTracking
    {
        $order = $this->model->findOrFail($orderId);
        $tracking = $order->trackings()->create($trackingData);

        // Update order status based on tracking
        $this->updateOrderStatusFromTracking($order);

        return $tracking;
    }

    /**
     * Update a tracking.
     */
    public function updateTracking(int $trackingId, array $trackingData): bool
    {
        $tracking = OrderTracking::findOrFail($trackingId);
        $result = $tracking->update($trackingData);

        // Update order status based on tracking changes
        $this->updateOrderStatusFromTracking($tracking->order);

        return $result;
    }

    /**
     * Delete a tracking.
     */
    public function deleteTracking(int $trackingId): bool
    {
        $tracking = OrderTracking::findOrFail($trackingId);
        $order = $tracking->order;
        $result = $tracking->delete();

        // Update order status based on tracking deletion
        $this->updateOrderStatusFromTracking($order);

        return $result;
    }

    /**
     * Update order status based on tracking.
     */
    private function updateOrderStatusFromTracking(Order $order): void
    {
        $latestTracking = $order->latestTracking();

        if (!$latestTracking) {
            return;
        }

        $newStatus = match ($latestTracking->status) {
            'delivered' => 'delivered',
            'shipped', 'in_transit', 'out_for_delivery' => 'shipped',
            'cancelled' => 'cancelled',
            'returned' => 'cancelled',
            default => $order->status, // Keep current status for other tracking statuses
        };

        // Only update if different from current status
        if ($order->status !== $newStatus) {
            $order->status = $newStatus;
            $order->save();
        }
    }

    /**
     * Get order statistics.
     */
    public function getStatistics(): array
    {
        $last7Days = now()->subDays(6)->startOfDay();

        $revenueLast7Days = $this->model
            ->where('payment_status', 'paid')
            ->where('created_at', '>=', $last7Days)
            ->selectRaw('DATE(created_at) as date, SUM(total_amount) as total')
            ->groupBy('date')
            ->pluck('total', 'date')
            ->toArray();

        $orderCountsLast7Days = $this->model
            ->where('created_at', '>=', $last7Days)
            ->selectRaw('DATE(created_at) as date, COUNT(*) as count')
            ->groupBy('date')
            ->pluck('count', 'date')
            ->toArray();

        // Fill missing days with 0
        $dates = [];
        $displayLabels = [];
        for ($i = 6; $i >= 0; $i--) {
            $date = now()->subDays($i)->format('Y-m-d');
            $dates[] = $date;
            $displayLabels[] = now()->subDays($i)->format('D');
        }

        $revenueData = [];
        $orderData = [];
        $profitData = [];
        foreach ($dates as $date) {
            $revenueData[] = $revenueLast7Days[$date] ?? 0;
            $orderData[] = $orderCountsLast7Days[$date] ?? 0;
            
            // Get profit for this date
            $dayProfit = $this->model->where('payment_status', 'paid')
                ->whereDate('created_at', $date)
                ->sum('profit');
            $profitData[] = $dayProfit;
        }

        // Top categories by product count (as a proxy for activity)
        $topCategories = Category::withCount('products')
            ->orderBy('products_count', 'desc')
            ->take(4)
            ->get();

        // Top products by quantity sold
        $topProducts = DB::table('order_products')
            ->join('products', 'order_products.product_id', '=', 'products.id')
            ->select('products.name', DB::raw('SUM(order_products.quantity) as total_sold'), DB::raw('SUM(order_products.total_price) as total_revenue'))
            ->groupBy('products.id', 'products.name')
            ->orderBy('total_sold', 'desc')
            ->take(5)
            ->get();

        // Profit calculations
        $thisMonthProfit = $this->model->where('payment_status', 'paid')
            ->whereMonth('created_at', now()->month)
            ->whereYear('created_at', now()->year)
            ->sum('profit');

        $lastMonthProfit = $this->model->where('payment_status', 'paid')
            ->whereMonth('created_at', now()->subMonth()->month)
            ->whereYear('created_at', now()->subMonth()->year)
            ->sum('profit');

        return [
            'total_orders' => $this->model->count(),
            'pending_orders' => $this->model->where('status', 'pending')->count(),
            'processing_orders' => $this->model->where('status', 'processing')->count(),
            'completed_orders' => $this->model->where('status', 'delivered')->count(),
            'total_revenue' => $this->model->where('payment_status', 'paid')->sum('total_amount'),
            'this_month_profit' => $thisMonthProfit,
            'last_month_profit' => $lastMonthProfit,
            'pending_payments' => $this->model->where('payment_status', 'pending')->count(),
            'total_products' => Product::count(),
            'total_customers' => User::count(),
            'total_categories' => Category::count(),
            'total_brands' => Brand::count(),
            'chart_labels' => $displayLabels,
            'revenue_chart_data' => $revenueData,
            'order_chart_data' => $orderData,
            'profit_chart_data' => $profitData,
            'top_categories' => $topCategories,
            'top_products' => $topProducts,
        ];
    }

    /**
     * Get orders by user ID.
     */
    public function getByUserId(int $userId, int $perPage = 15): LengthAwarePaginator
    {
        return $this->model->where('user_id', $userId)
            ->with(['orderProducts.product'])
            ->latest()
            ->paginate($perPage);
    }

    /**
     * Get recent orders.
     */
    public function getRecent(int $limit = 10): Collection
    {
        return $this->model->with(['user', 'orderProducts.product'])
            ->latest()
            ->limit($limit)
            ->get();
    }

    public function createOrder(array $order_info, array $variant_info, array $shipping_address, $flashSaleDiscount)
    {
        try {
            DB::beginTransaction();

            // 1️⃣ Coupon update
            app(CouponRepository::class)->usedCoupon($order_info['coupon_used'] ?? '');

            // 2️⃣ Order create
            $order = $this->model->create($order_info);

            // 3️⃣ Order products create
            $order->orderProducts()->createMany($variant_info);

            // 4️⃣ Shipping address create
            $order->orderAddress()->create($shipping_address);

            // 5️⃣ Flash Sale info create
            if (!empty($flashSaleDiscount)) {
                $order->flashSale()->createMany($flashSaleDiscount);
            }

            // 6️⃣ Now decrease stock for all product variants
            foreach ($variant_info as $variant) {
                if (!empty($variant['product_variant_id']) && !empty($variant['quantity'])) {
                    DB::table('product_variants')
                        ->where('id', $variant['product_variant_id'])
                        ->where('stock', '>=', $variant['quantity'])
                        ->decrement('stock', $variant['quantity']);
                }
            }

            DB::commit();
            return $order;
        } catch (\Exception $e) {
            DB::rollBack();
            throw $e;
        }
    }

    public function getOrder($user_id, $current_page, $per_page)
    {
        $offset = ($current_page - 1) * $per_page;

        $baseQuery = $this->model->where('user_id', $user_id);

        $total = (clone $baseQuery)->count();

        $orders = (clone $baseQuery)
            ->select([
                'id',
                'user_id',
                'order_number',
                'total_amount',
                'status',
                'created_at',
            ])
            ->withCount('orderProducts')
            ->latest()
            ->skip($offset)
            ->take($per_page)
            ->get();

        return [
            'orders' => $orders,
            'total' => $total,
        ];
    }


    public function getOrderDetails($order_id, $user_id)
    {
        return $this->model->where('user_id', $user_id)->where('id', $order_id)->with([
            'orderProducts',
            'orderAddress',
            'orderProducts.product',
            'orderProducts.product.firstImage',
            'orderProducts.productVariant.product',
            'orderProducts.productVariant.images',
            'orderProducts.productVariant.variantAttributes:id,product_variant_id,attribute_id,attribute_value_id',
            'orderProducts.productVariant.variantAttributes.attribute:id,name',
            'orderProducts.productVariant.variantAttributes.attributeValue:id,value',
            'flashSale',
            'trackings'
        ])->first();
    }
}
