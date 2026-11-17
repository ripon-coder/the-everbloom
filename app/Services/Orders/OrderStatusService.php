<?php

namespace App\Services\Orders;

use App\Mail\OrderStatusChangedMail;
use App\Models\Coupon;
use App\Models\Order;
use App\Models\OrderStatusHistory;
use App\Services\Inventory\StockService;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Mail;
use Illuminate\Validation\ValidationException;

class OrderStatusService
{
    private const ALLOWED_TRANSITIONS = [
        'Placed' => ['Processing', 'Cancelled'],
        'Processing' => ['Shipped', 'Cancelled'],
        'Shipped' => ['Delivered'],
        'Delivered' => [],
        'Cancelled' => [],
    ];

    public function __construct(
        private readonly StockService $stockService
    ) {}

    public function transition(
        Order $order,
        string $newStatus,
        ?string $trackingNumber = null,
        ?string $note = null,
        ?int $adminId = null
    ): Order {
        $previousStatus = $order->status;

        if ($newStatus !== $previousStatus) {
            $allowed = self::ALLOWED_TRANSITIONS[$previousStatus] ?? [];
            if (! in_array($newStatus, $allowed, true)) {
                throw ValidationException::withMessages([
                    'status' => ["Transition from {$previousStatus} to {$newStatus} is not allowed."],
                ]);
            }
        }

        DB::transaction(function () use ($order, $previousStatus, $newStatus, $trackingNumber, $note, $adminId) {
            if ($previousStatus !== 'Cancelled' && $newStatus === 'Cancelled') {
                $this->stockService->restoreForCancelledOrder($order, $adminId);

                if ($order->coupon_id) {
                    Coupon::where('id', $order->coupon_id)
                        ->where('used_count', '>', 0)
                        ->decrement('used_count');
                }
            }

            $order->update([
                'status' => $newStatus,
                'current_step' => $newStatus === 'Cancelled' ? $order->current_step : $newStatus,
                'tracking_number' => $trackingNumber !== null && trim($trackingNumber) !== ''
                    ? trim($trackingNumber)
                    : $order->tracking_number,
            ]);

            if ($newStatus !== $previousStatus) {
                OrderStatusHistory::create([
                    'order_id' => $order->id,
                    'from_status' => $previousStatus,
                    'to_status' => $newStatus,
                    'admin_id' => $adminId,
                    'note' => $note,
                    'created_at' => now(),
                ]);
            }
        }, 3);

        $order->refresh();

        if ($newStatus !== $previousStatus && in_array($newStatus, ['Shipped', 'Delivered'], true) && $order->customer_email !== '') {
            Mail::to($order->customer_email)->queue(new OrderStatusChangedMail($order));
        }

        return $order;
    }
}

