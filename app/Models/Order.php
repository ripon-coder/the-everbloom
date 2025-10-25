<?php

namespace App\Models;

use App\Models\FalshSaleTracker;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class Order extends Model
{
    use HasFactory, SoftDeletes;

    /**
     * The attributes that are mass assignable.
     *
     * @var array<int, string>
     */
    protected $fillable = [
        'user_id',
        'order_number',
        'before_discount',
        'total_amount',
        'subtotal',
        'discount_amount',
        'tax_amount',
        'shipping_amount',
        'admin_shipping_amount',
        'weight',
        'coupon_used',
        'coupon_discount_amount',
        'flash_discount_amount',
        'status',
        'payment_status',
        'payment_method',
        'shipping_address',
        'billing_address',
        'notes',
    ];

    /**
     * The attributes that should be cast.
     *
     * @var array<string, string>
     */
    protected $casts = [
        'total_amount' => 'decimal:2',
        'subtotal' => 'decimal:2',
        'discount_amount' => 'decimal:2',
        'tax_amount' => 'decimal:2',
        'shipping_amount' => 'decimal:2',
        'shipping_address' => 'array',
        'billing_address' => 'array',
    ];

    /**
     * The accessors to append to the model's array form.
     *
     * @var array
     */
    protected $appends = [
        'total_paid',
        'total_refunded',
    ];

    /**
     * Get the user that owns the order.
     */
    public function user()
    {
        return $this->belongsTo(User::class);
    }

    /**
     * Get the order products for the order.
     */
    public function orderProducts()
    {
        return $this->hasMany(OrderProduct::class);
    }

    /**
     * Get the products associated with the order.
     */
    public function products()
    {
        return $this->belongsToMany(Product::class, 'order_products')
                    ->withPivot('quantity', 'unit_price', 'total_price', 'discount_amount', 'notes')
                    ->withTimestamps();
    }

    public function orderAddress(){
        return $this->hasOne(OrderAddress::class);
    }

    public function flashSale(){
        return $this->hasMany(FalshSaleTracker::class,'order_id');
    }
    /**
     * Get the payments for the order.
     */
    public function payments()
    {
        return $this->hasMany(Payment::class);
    }

    /**
     * Get the tracking information for the order.
     */
    public function trackings()
    {
        return $this->hasMany(OrderTracking::class);
    }

    /**
     * Get the latest tracking information for the order.
     */
    public function latestTracking()
    {
        return $this->trackings()->latest()->first();
    }

    /**
     * Get the current tracking status for the order.
     */
    public function getCurrentTrackingStatus()
    {
        $latestTracking = $this->latestTracking();
        return $latestTracking ? $latestTracking->status : null;
    }

    /**
     * Get the tracking number for the order.
     */
    public function getTrackingNumber()
    {
        $latestTracking = $this->latestTracking();
        return $latestTracking ? $latestTracking->tracking_number : null;
    }

    /**
     * Get the carrier for the order.
     */
    public function getCarrier()
    {
        $latestTracking = $this->latestTracking();
        return $latestTracking ? $latestTracking->carrier : null;
    }

    /**
     * Check if the order has tracking information.
     */
    public function hasTracking()
    {
        return $this->trackings()->exists();
    }

    /**
     * Check if the order is shipped.
     */
    public function isShipped()
    {
        return $this->trackings()->shipped()->exists();
    }

    /**
     * Check if the order is delivered.
     */
    public function isDelivered()
    {
        return $this->trackings()->delivered()->exists();
    }

    /**
     * Check if the order is in transit.
     */
    public function isInTransit()
    {
        return $this->trackings()->where('status', 'in_transit')->exists();
    }

    /**
     * Check if the order is out for delivery.
     */
    public function isOutForDelivery()
    {
        return $this->trackings()->where('status', 'out_for_delivery')->exists();
    }

    /**
     * Get the latest payment for the order.
     */
    public function latestPayment()
    {
        return $this->payments()->latest()->first();
    }

    /**
     * Get the total paid amount for the order.
     */
    public function getTotalPaidAttribute()
    {
        return $this->payments()->completed()->sum('amount');
    }

    /**
     * Get the total refunded amount for the order.
     */
    public function getTotalRefundedAttribute()
    {
        return $this->payments()->refunded()->sum('amount');
    }

    /**
     * Check if the order is fully paid.
     */
    public function isFullyPaid()
    {
        return $this->total_paid >= $this->total_amount;
    }

    /**
     * Check if the order has any payments.
     */
    public function hasPayments()
    {
        return $this->payments()->exists();
    }

    /**
     * Get the payment status based on payments.
     */
    public function getCalculatedPaymentStatus()
    {
        if (!$this->hasPayments()) {
            return 'pending';
        }

        if ($this->isFullyPaid()) {
            return 'paid';
        }

        if ($this->payments()->failed()->exists()) {
            return 'failed';
        }

        if ($this->payments()->refunded()->exists()) {
            return 'refunded';
        }

        return 'partially_paid';
    }

    /**
     * Scope a query to only include orders with a specific status.
     */
    public function scopeWithStatus($query, $status)
    {
        return $query->where('status', $status);
    }

    /**
     * Scope a query to only include orders with a specific payment status.
     */
    public function scopeWithPaymentStatus($query, $paymentStatus)
    {
        return $query->where('payment_status', $paymentStatus);
    }

    /**
     * Generate a unique order number.
     */
    public static function generateOrderNumber()
    {
        do {
            $orderNumber = 'ORD-' . strtoupper(uniqid()) . '-' . rand(1000, 9999);
        } while (self::where('order_number', $orderNumber)->exists());

        return $orderNumber;
    }

    /**
     * Get order status options.
     */
    public static function getStatusOptions(): array
    {
        return [
            'pending' => 'Pending',
            'processing' => 'Processing',
            'shipped' => 'Shipped',
            'delivered' => 'Delivered',
            'cancelled' => 'Cancelled',
        ];
    }

    /**
     * Get payment status options.
     */
    public static function getPaymentStatusOptions(): array
    {
        return [
            'pending' => 'Pending',
            'paid' => 'Paid',
            'failed' => 'Failed',
            'refunded' => 'Refunded',
            'partially_paid' => 'Partially Paid',
        ];
    }

    /**
     * Get the human-readable status.
     */
    public function getStatusText()
    {
        return self::getStatusOptions()[$this->status] ?? ucfirst($this->status);
    }

    /**
     * Get the human-readable payment status.
     */
    public function getPaymentStatusText()
    {
        return self::getPaymentStatusOptions()[$this->payment_status] ?? ucfirst($this->payment_status);
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
            'delivered' => 'bg-green-100 text-green-800',
            'cancelled' => 'bg-red-100 text-red-800',
            default => 'bg-gray-100 text-gray-800',
        };
    }

    /**
     * Get the payment status color class.
     */
    public function getPaymentStatusColor()
    {
        return match($this->payment_status) {
            'pending' => 'bg-yellow-100 text-yellow-800',
            'paid' => 'bg-green-100 text-green-800',
            'failed' => 'bg-red-100 text-red-800',
            'refunded' => 'bg-purple-100 text-purple-800',
            'partially_paid' => 'bg-orange-100 text-orange-800',
            default => 'bg-gray-100 text-gray-800',
        };
    }
}
