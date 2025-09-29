<?php

namespace App\Repositories\Eloquent;

use App\Models\Order;
use App\Models\OrderProduct;
use App\Models\Payment;
use App\Models\OrderTracking;
use App\Models\Product;
use App\Repositories\Contracts\OrderRepository;
use Illuminate\Pagination\LengthAwarePaginator;
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
        $query = $this->model->with(['user', 'orderProducts.product']);

        // Apply filters
        if (!empty($filters['status'])) {
            $query->where('status', $filters['status']);
        }

        if (!empty($filters['payment_status'])) {
            $query->where('payment_status', $filters['payment_status']);
        }

        if (!empty($filters['search'])) {
            $search = $filters['search'];
            $query->where(function ($q) use ($search) {
                $q->where('order_number', 'like', "%{$search}%")
                  ->orWhereHas('user', function ($userQuery) use ($search) {
                      $userQuery->where('name', 'like', "%{$search}%")
                               ->orWhere('email', 'like', "%{$search}%");
                  });
            });
        }

        if (!empty($filters['date_from'])) {
            $query->whereDate('created_at', '>=', $filters['date_from']);
        }

        if (!empty($filters['date_to'])) {
            $query->whereDate('created_at', '<=', $filters['date_to']);
        }

        // Order by
        $orderBy = $filters['order_by'] ?? 'created_at';
        $orderDirection = $filters['order_direction'] ?? 'desc';
        $query->orderBy($orderBy, $orderDirection);

        return $query->paginate($perPage);
    }

    /**
     * Get an order by ID with relationships.
     */
    public function findById(int $id): ?Order
    {
        return $this->model->with(['user', 'orderProducts.product', 'payments', 'trackings'])->findOrFail($id);
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
                'total_amount' => $data['total_amount'],
                'subtotal' => $data['subtotal'],
                'discount_amount' => $data['discount_amount'] ?? 0,
                'tax_amount' => $data['tax_amount'] ?? 0,
                'shipping_amount' => $data['shipping_amount'] ?? 0,
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
                'total_amount' => $data['total_amount'] ?? $order->total_amount,
                'subtotal' => $data['subtotal'] ?? $order->subtotal,
                'discount_amount' => $data['discount_amount'] ?? $order->discount_amount,
                'tax_amount' => $data['tax_amount'] ?? $order->tax_amount,
                'shipping_amount' => $data['shipping_amount'] ?? $order->shipping_amount,
                'status' => $data['status'] ?? $order->status,
                'payment_status' => $data['payment_status'] ?? $order->payment_status,
                'payment_method' => $data['payment_method'] ?? $order->payment_method,
                'shipping_address' => $data['shipping_address'] ?? $order->shipping_address,
                'billing_address' => $data['billing_address'] ?? $order->billing_address,
                'notes' => $data['notes'] ?? $order->notes,
            ]);

            // Update order products if provided
            if (!empty($data['products'])) {
                // Remove existing products
                $order->orderProducts()->delete();

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

        $newStatus = match($latestTracking->status) {
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
        return [
            'total_orders' => $this->model->count(),
            'pending_orders' => $this->model->where('status', 'pending')->count(),
            'processing_orders' => $this->model->where('status', 'processing')->count(),
            'completed_orders' => $this->model->where('status', 'delivered')->count(),
            'total_revenue' => $this->model->where('payment_status', 'paid')->sum('total_amount'),
            'pending_payments' => $this->model->where('payment_status', 'pending')->count(),
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
}
