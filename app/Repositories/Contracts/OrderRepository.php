<?php

namespace App\Repositories\Contracts;

use App\Models\Order;
use App\Models\Payment;
use App\Models\OrderTracking;
use Illuminate\Pagination\LengthAwarePaginator;
use Illuminate\Support\Collection;

interface OrderRepository
{
    /**
     * Get all orders with pagination and filtering.
     */
    public function getAll(int $perPage = 15): LengthAwarePaginator;

    /**
     * Get an order by ID with relationships.
     */
    public function findById(int $id): ?Order;

    /**
     * Create a new order with products.
     */
    public function create(array $data): Order;

    /**
     * Update an order.
     */
    public function update(int $id, array $data): bool;

    /**
     * Delete an order.
     */
    public function delete(int $id): bool;

    /**
     * Restore a deleted order.
     */
    public function restore(int $id): bool;

    /**
     * Force delete an order.
     */
    public function forceDelete(int $id): bool;

    /**
     * Update the status of an order.
     */
    public function updateStatus(int $id, string $status): bool;

    /**
     * Update the payment status of an order.
     */
    public function updatePaymentStatus(int $id, string $paymentStatus): bool;

    /**
     * Get payments for an order.
     */
    public function getPayments(int $orderId);

    /**
     * Create a payment for an order.
     */
    public function createPayment(int $orderId, array $paymentData): Payment;

    /**
     * Update a payment.
     */
    public function updatePayment(int $paymentId, array $paymentData): bool;

    /**
     * Delete a payment.
     */
    public function deletePayment(int $paymentId): bool;

    /**
     * Get trackings for an order.
     */
    public function getTrackings(int $orderId);

    /**
     * Create a tracking for an order.
     */
    public function createTracking(int $orderId, array $trackingData): OrderTracking;

    /**
     * Update a tracking.
     */
    public function updateTracking(int $trackingId, array $trackingData): bool;

    /**
     * Delete a tracking.
     */
    public function deleteTracking(int $trackingId): bool;

    /**
     * Get order statistics.
     */
    public function getStatistics(): array;

    /**
     * Get orders by user ID.
     */
    public function getByUserId(int $userId, int $perPage = 15): LengthAwarePaginator;

    /**
     * Get recent orders.
     */
    public function getRecent(int $limit = 10): Collection;

    public function createOrder(array $order_info, array $variant_info, array $shipping_address): Order;
}
