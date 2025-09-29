<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class Payment extends Model
{
    use HasFactory, SoftDeletes;

    /**
     * The attributes that are mass assignable.
     *
     * @var array<int, string>
     */
    protected $fillable = [
        'order_id',
        'payment_method',
        'transaction_id',
        'amount',
        'currency',
        'status',
        'payment_details',
        'paid_at',
        'refunded_at',
        'failure_reason',
        'notes',
    ];

    /**
     * The attributes that should be cast.
     *
     * @var array<string, string>
     */
    protected $casts = [
        'amount' => 'decimal:2',
        'payment_details' => 'array',
        'paid_at' => 'datetime',
        'refunded_at' => 'datetime',
    ];

    /**
     * Get the order that owns the payment.
     */
    public function order()
    {
        return $this->belongsTo(Order::class);
    }

    /**
     * Scope a query to only include payments with a specific status.
     */
    public function scopeWithStatus($query, $status)
    {
        return $query->where('status', $status);
    }

    /**
     * Scope a query to only include payments with a specific payment method.
     */
    public function scopeWithPaymentMethod($query, $paymentMethod)
    {
        return $query->where('payment_method', $paymentMethod);
    }

    /**
     * Scope a query to only include completed payments.
     */
    public function scopeCompleted($query)
    {
        return $query->where('status', 'completed');
    }

    /**
     * Scope a query to only include pending payments.
     */
    public function scopePending($query)
    {
        return $query->where('status', 'pending');
    }

    /**
     * Scope a query to only include failed payments.
     */
    public function scopeFailed($query)
    {
        return $query->where('status', 'failed');
    }

    /**
     * Scope a query to only include refunded payments.
     */
    public function scopeRefunded($query)
    {
        return $query->where('status', 'refunded');
    }

    /**
     * Check if the payment is completed.
     */
    public function isCompleted()
    {
        return $this->status === 'completed';
    }

    /**
     * Check if the payment is pending.
     */
    public function isPending()
    {
        return $this->status === 'pending';
    }

    /**
     * Check if the payment is failed.
     */
    public function isFailed()
    {
        return $this->status === 'failed';
    }

    /**
     * Check if the payment is refunded.
     */
    public function isRefunded()
    {
        return $this->status === 'refunded';
    }

    /**
     * Mark the payment as completed.
     */
    public function markAsCompleted()
    {
        $this->status = 'completed';
        $this->paid_at = now();
        $this->save();
    }

    /**
     * Mark the payment as failed.
     */
    public function markAsFailed($reason = null)
    {
        $this->status = 'failed';
        $this->failure_reason = $reason;
        $this->save();
    }

    /**
     * Mark the payment as refunded.
     */
    public function markAsRefunded()
    {
        $this->status = 'refunded';
        $this->refunded_at = now();
        $this->save();
    }

    /**
     * Get the human-readable status.
     */
    public function getStatusText()
    {
        return match($this->status) {
            'pending' => 'Pending',
            'completed' => 'Completed',
            'failed' => 'Failed',
            'refunded' => 'Refunded',
            'cancelled' => 'Cancelled',
            default => ucfirst($this->status),
        };
    }

    /**
     * Get the status color class.
     */
    public function getStatusColor()
    {
        return match($this->status) {
            'pending' => 'bg-yellow-100 text-yellow-800',
            'completed' => 'bg-green-100 text-green-800',
            'failed' => 'bg-red-100 text-red-800',
            'refunded' => 'bg-purple-100 text-purple-800',
            'cancelled' => 'bg-gray-100 text-gray-800',
            default => 'bg-gray-100 text-gray-800',
        };
    }
}
