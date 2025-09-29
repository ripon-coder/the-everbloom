<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class OrderTracking extends Model
{
    use HasFactory, SoftDeletes;

    /**
     * The attributes that are mass assignable.
     *
     * @var array<int, string>
     */
    protected $fillable = [
        'order_id',
        'status',
        'location',
        'description',
        'tracking_number',
        'carrier',
        'estimated_delivery',
        'actual_delivery',
        'tracking_details',
        'notes',
    ];

    /**
     * The attributes that should be cast.
     *
     * @var array<string, string>
     */
    protected $casts = [
        'tracking_details' => 'array',
        'estimated_delivery' => 'datetime',
        'actual_delivery' => 'datetime',
    ];

    /**
     * Get the order that owns the tracking.
     */
    public function order()
    {
        return $this->belongsTo(Order::class);
    }

    /**
     * Scope a query to only include trackings with a specific status.
     */
    public function scopeWithStatus($query, $status)
    {
        return $query->where('status', $status);
    }

    /**
     * Scope a query to only include trackings with a specific carrier.
     */
    public function scopeWithCarrier($query, $carrier)
    {
        return $query->where('carrier', $carrier);
    }

    /**
     * Scope a query to only include trackings with a specific tracking number.
     */
    public function scopeWithTrackingNumber($query, $trackingNumber)
    {
        return $query->where('tracking_number', $trackingNumber);
    }

    /**
     * Scope a query to only include pending trackings.
     */
    public function scopePending($query)
    {
        return $query->where('status', 'pending');
    }

    /**
     * Scope a query to only include shipped trackings.
     */
    public function scopeShipped($query)
    {
        return $query->where('status', 'shipped');
    }

    /**
     * Scope a query to only include delivered trackings.
     */
    public function scopeDelivered($query)
    {
        return $query->where('status', 'delivered');
    }

    /**
     * Check if the tracking is delivered.
     */
    public function isDelivered()
    {
        return $this->status === 'delivered';
    }

    /**
     * Check if the tracking is shipped.
     */
    public function isShipped()
    {
        return $this->status === 'shipped';
    }

    /**
     * Check if the tracking is pending.
     */
    public function isPending()
    {
        return $this->status === 'pending';
    }

    /**
     * Mark the tracking as delivered.
     */
    public function markAsDelivered()
    {
        $this->status = 'delivered';
        $this->actual_delivery = now();
        $this->save();
    }

    /**
     * Mark the tracking as shipped.
     */
    public function markAsShipped()
    {
        $this->status = 'shipped';
        $this->save();
    }

    /**
     * Get the human-readable status.
     */
    public function getStatusText()
    {
        return match($this->status) {
            'pending' => 'Pending',
            'processing' => 'Processing',
            'shipped' => 'Shipped',
            'in_transit' => 'In Transit',
            'out_for_delivery' => 'Out for Delivery',
            'delivered' => 'Delivered',
            'cancelled' => 'Cancelled',
            'returned' => 'Returned',
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
            'processing' => 'bg-blue-100 text-blue-800',
            'shipped' => 'bg-purple-100 text-purple-800',
            'in_transit' => 'bg-indigo-100 text-indigo-800',
            'out_for_delivery' => 'bg-orange-100 text-orange-800',
            'delivered' => 'bg-green-100 text-green-800',
            'cancelled' => 'bg-red-100 text-red-800',
            'returned' => 'bg-gray-100 text-gray-800',
            default => 'bg-gray-100 text-gray-800',
        };
    }

    /**
     * Get the carrier logo URL.
     */
    public function getCarrierLogo()
    {
        return match(strtolower($this->carrier)) {
            'fedex' => 'https://www.fedex.com/favicon.ico',
            'ups' => 'https://www.ups.com/favicon.ico',
            'dhl' => 'https://www.dhl.com/favicon.ico',
            'usps' => 'https://www.usps.com/favicon.ico',
            default => null,
        };
    }

    /**
     * Get the tracking URL.
     */
    public function getTrackingUrl()
    {
        if (!$this->tracking_number || !$this->carrier) {
            return null;
        }

        return match(strtolower($this->carrier)) {
            'fedex' => "https://www.fedex.com/fedextrack/?trknbr={$this->tracking_number}",
            'ups' => "https://www.ups.com/track?tracknum={$this->tracking_number}",
            'dhl' => "https://www.dhl.com/track/?tracking-number={$this->tracking_number}",
            'usps' => "https://tools.usps.com/go/TrackConfirmAction?tLabels={$this->tracking_number}",
            default => null,
        };
    }

    /**
     * Get the estimated delivery time in human readable format.
     */
    public function getEstimatedDeliveryTime()
    {
        if (!$this->estimated_delivery) {
            return 'Not available';
        }

        return $this->estimated_delivery->diffForHumans();
    }

    /**
     * Check if the order is delayed.
     */
    public function isDelayed()
    {
        if (!$this->estimated_delivery || $this->actual_delivery) {
            return false;
        }

        return now()->isAfter($this->estimated_delivery) && !in_array($this->status, ['delivered', 'cancelled']);
    }

    /**
     * Get the delay status.
     */
    public function getDelayStatus()
    {
        if (!$this->isDelayed()) {
            return 'On Time';
        }

        $delay = now()->diffInDays($this->estimated_delivery);
        
        if ($delay <= 1) {
            return 'Slightly Delayed';
        } elseif ($delay <= 3) {
            return 'Delayed';
        } else {
            return 'Severely Delayed';
        }
    }
}
