<?php

namespace App\Services;

use App\Models\Order;
use App\Models\User;
use App\Models\Product;
use App\Models\ProductVariant;
use App\Models\OrderTracking;
use App\Repositories\Contracts\OrderRepository;
use Illuminate\Pagination\LengthAwarePaginator;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use Exception;

class OrderService
{
    protected $orderRepository;

    public function __construct(OrderRepository $orderRepository)
    {
        $this->orderRepository = $orderRepository;
    }

    /**
     * Get all orders with pagination and filtering.
     */
    public function getAllOrders(array $filters = [], int $perPage = 15): LengthAwarePaginator
    {
        return $this->orderRepository->getAll($filters, $perPage);
    }

    /**
     * Get an order by ID.
     */
    public function getOrderById(int $id): Order
    {
        return $this->orderRepository->findById($id);
    }

    /**
     * Delete an order.
     */
    public function deleteOrder(int $id): bool
    {
        return $this->orderRepository->delete($id);
    }

    /**
     * Update the status of an order.
     */
    public function updateOrderStatus(int $id, string $status): bool
    {
        return $this->orderRepository->updateStatus($id, $status);
    }

    /**
     * Update the payment status of an order.
     */
    public function updateOrderPaymentStatus(int $id, string $paymentStatus): bool
    {
        return $this->orderRepository->updatePaymentStatus($id, $paymentStatus);
    }

    /**
     * Create an order.
     */
    public function createOrder(array $data): Order
    {
        return $this->orderRepository->create($data);
    }

    /**
     * Create order tracking.
     */
    public function createOrderTracking(int $id, array $data): OrderTracking
    {
        return $this->orderRepository->createTracking($id, $data);
    }


    /**
     * Get order statistics.
     */
    public function getOrderStatistics(): array
    {
        return $this->orderRepository->getStatistics();
    }

    /**
     * Get orders by user ID.
     */
    public function getOrdersByUserId(int $userId, int $perPage = 15): LengthAwarePaginator
    {
        return $this->orderRepository->getByUserId($userId, $perPage);
    }

    /**
     * Get recent orders.
     */
    public function getRecentOrders(int $limit = 10): Collection
    {
        return $this->orderRepository->getRecent($limit);
    }

    /**
     * Calculate order totals from products.
     */
    public function calculateOrderTotals(array $products): array
    {
        $subtotal = 0;
        
        foreach ($products as $product) {
            $subtotal += $product['quantity'] * $product['unit_price'];
        }

        return [
            'subtotal' => $subtotal,
        ];
    }

    /**
     * Get order status options.
     */
    public function getStatusOptions(): array
    {
        return Order::getStatusOptions();
    }

    /**
     * Get payment status options.
     */
    public function getPaymentStatusOptions(): array
    {
        return Order::getPaymentStatusOptions();
    }

    /**
     * Get tracking status options.
     */
    public function getTrackingStatusOptions(): array
    {
        return [
            'pending' => 'Pending',
            'processing' => 'Processing',
            'shipped' => 'Shipped',
            'in_transit' => 'In Transit',
            'out_for_delivery' => 'Out for Delivery',
            'delivered' => 'Delivered',
            'cancelled' => 'Cancelled',
            'returned' => 'Returned',
        ];
    }

    /**
     * Get carrier options.
     */
    public function getCarrierOptions(): array
    {
        return [
            'fedex' => 'FedEx',
            'ups' => 'UPS',
            'dhl' => 'DHL',
            'usps' => 'USPS',
            'other' => 'Other',
        ];
    }

    /**
     * Process order creation with validation and business logic.
     */
    public function processOrderCreation(array $data): Order
    {
        return DB::transaction(function () use ($data) {
            // Validate product availability and prices
            foreach ($data['products'] as $productData) {
                $product = Product::findOrFail($productData['product_id']);
                
                // Check if product variant is specified and has enough stock
                if (!empty($productData['product_variant_id'])) {
                    $variant = ProductVariant::find($productData['product_variant_id']);
                    if ($variant && $variant->track_stock && $variant->stock < $productData['quantity']) {
                        throw new Exception("Product variant '{$variant->sku}' does not have enough stock. Available: {$variant->stock}, Required: {$productData['quantity']}");
                    }
                }
                
                // Validate price matches current product price (or allow override with permission)
                if ($productData['unit_price'] != $product->price) {
                    // In a real application, you might want to check if user has permission to override prices
                    // For now, we'll allow it but log a warning
                    Log::warning("Price override for product {$product->id}: {$product->price} -> {$productData['unit_price']}");
                }
            }

            // Calculate totals
            $totals = $this->calculateOrderTotals($data['products']);
            $data['subtotal'] = $totals['subtotal'];
            $data['total_amount'] = $totals['subtotal'] + ($data['shipping_amount'] ?? 0) + ($data['tax_amount'] ?? 0) - ($data['discount_amount'] ?? 0);

            // Create the order
            $order = $this->createOrder($data);

            // Update product variant stock if tracking is enabled
            foreach ($data['products'] as $productData) {
                if (!empty($productData['product_variant_id'])) {
                    $variant = ProductVariant::find($productData['product_variant_id']);
                    if ($variant && $variant->track_stock) {
                        $variant->decrement('stock', $productData['quantity']);
                    }
                }
            }

            return $order;
        });
    }

    /**
     * Process order status change with business logic.
     */
    public function processOrderStatusChange(int $orderId, string $newStatus): Order
    {
        $order = $this->getOrderById($orderId);
        $oldStatus = $order->status;

        // Validate status transition
        $this->validateOrderStatusTransition($oldStatus, $newStatus);

        // Update the status
        $this->updateOrderStatus($orderId, $newStatus);

        // Perform additional actions based on status change
        $this->handleOrderStatusChangeActions($order, $oldStatus, $newStatus);

        return $order->fresh();
    }

    /**
     * Process tracking creation with validation.
     */
    public function processTrackingCreation(int $orderId, array $trackingData): OrderTracking
    {
        $order = $this->getOrderById($orderId);
        
        // Validate tracking status transition
        $this->validateTrackingStatusTransition($order, $trackingData['status']);

        // Create the tracking
        $tracking = $this->createOrderTracking($orderId, $trackingData);

        // Send tracking notification
        $this->sendTrackingNotification($order, $tracking);

        return $tracking;
    }

    /**
     * Validate order status transition.
     */
    private function validateOrderStatusTransition(string $oldStatus, string $newStatus): void
    {
        $validTransitions = [
            'incomplete' => ['pending', 'processing', 'cancelled'],
            'pending' => ['processing', 'cancelled'],
            'processing' => ['shipped', 'cancelled'],
            'shipped' => ['delivered', 'cancelled'],
            'delivered' => [], // Terminal state
            'cancelled' => [], // Terminal state
        ];

        if (!in_array($newStatus, $validTransitions[$oldStatus] ?? [])) {
            throw new Exception("Invalid status transition from {$oldStatus} to {$newStatus}");
        }
    }

    /**
     * Validate tracking status transition.
     */
    private function validateTrackingStatusTransition(Order $order, string $newTrackingStatus): void
    {
        $currentTrackingStatus = $order->getCurrentTrackingStatus();
        
        $validTransitions = [
            null => ['pending', 'processing'],
            'pending' => ['processing', 'shipped', 'cancelled'],
            'processing' => ['shipped', 'cancelled'],
            'shipped' => ['in_transit', 'out_for_delivery', 'delivered', 'cancelled'],
            'in_transit' => ['out_for_delivery', 'delivered', 'cancelled'],
            'out_for_delivery' => ['delivered', 'cancelled'],
            'delivered' => ['returned'],
            'cancelled' => [], // Terminal state
            'returned' => [], // Terminal state
        ];

        if (!in_array($newTrackingStatus, $validTransitions[$currentTrackingStatus] ?? [])) {
            throw new Exception("Invalid tracking status transition from {$currentTrackingStatus} to {$newTrackingStatus}");
        }
    }

    /**
     * Handle actions when order status changes.
     */
    private function handleOrderStatusChangeActions(Order $order, string $oldStatus, string $newStatus): void
    {
        // Send notifications
        $this->sendOrderStatusNotification($order, $oldStatus, $newStatus);

        // Update related records
        if ($newStatus === 'cancelled') {
            // Restore product variant stock
            foreach ($order->orderProducts as $orderProduct) {
                if ($orderProduct->product_variant_id) {
                    $variant = ProductVariant::find($orderProduct->product_variant_id);
                    if ($variant && $variant->track_stock) {
                        $variant->increment('stock', $orderProduct->quantity);
                    }
                }
            }
        }

        // Log the status change
        Log::info("Order #{$order->order_number} status changed from {$oldStatus} to {$newStatus}");
    }

    /**
     * Send order status notification.
     */
    private function sendOrderStatusNotification(Order $order, string $oldStatus, string $newStatus): void
    {
        // In a real application, you would send email/SMS notifications here
        // For now, we'll just log it
        Log::info("Notification: Order #{$order->order_number} status changed to {$newStatus}");
    }

    /**
     * Send tracking notification.
     */
    private function sendTrackingNotification(Order $order, OrderTracking $tracking): void
    {
        // In a real application, you would send email/SMS notifications here
        // For now, we'll just log it
        Log::info("Notification: Order #{$order->order_number} tracking status updated to {$tracking->status}");
    }
}
