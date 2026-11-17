<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Relations\HasOne;

class Order extends Model
{
    protected $fillable = [
        'order_number',
        'user_id',
        'coupon_id',
        'customer_email',
        'customer_phone',
        'status',
        'current_step',
        'subtotal',
        'discount_amount',
        'discount_code',
        'shipping_cost',
        'tax_amount',
        'total',
        'total_cost',
        'total_profit',
        'items_count',
        'delivery_method',
        'payment_method',
        'payment_status',
        'placed_via',
        'tracking_number',
        'shipping_address',
        'notes',
        'placed_at',
    ];

    protected $appends = [
        'date',
        'iso_date',
        'shipping',
        'tax',
    ];

    protected function casts(): array
    {
        return [
            'subtotal' => 'float',
            'discount_amount' => 'float',
            'shipping_cost' => 'float',
            'tax_amount' => 'float',
            'total' => 'float',
            'total_cost' => 'float',
            'total_profit' => 'float',
            'items_count' => 'integer',
            'shipping_address' => 'array',
            'placed_at' => 'datetime',
        ];
    }

    public function getRouteKeyName(): string
    {
        return 'order_number';
    }

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    public function coupon(): BelongsTo
    {
        return $this->belongsTo(Coupon::class);
    }

    public function items(): HasMany
    {
        return $this->hasMany(OrderItem::class);
    }

    public function invoice(): HasOne
    {
        return $this->hasOne(Invoice::class);
    }

    public function statusHistories(): HasMany
    {
        return $this->hasMany(OrderStatusHistory::class);
    }

    public function getDateAttribute(): string
    {
        return ($this->placed_at ?? $this->created_at)?->format('F j, Y') ?? '';
    }

    public function getIsoDateAttribute(): string
    {
        return ($this->placed_at ?? $this->created_at)?->format('Y-m-d') ?? '';
    }

    public function getShippingAttribute(): float
    {
        return (float) $this->shipping_cost;
    }

    public function getTaxAttribute(): float
    {
        return (float) $this->tax_amount;
    }

    public function toViewArray(): array
    {
        $this->loadMissing('items');

        return [
            'id' => $this->order_number,
            'db_id' => $this->id,
            'order_number' => $this->order_number,
            'date' => $this->date,
            'iso_date' => $this->iso_date,
            'status' => $this->status,
            'current_step' => $this->current_step,
            'subtotal' => (float) $this->subtotal,
            'discount_amount' => (float) $this->discount_amount,
            'discount_code' => $this->discount_code,
            'shipping' => (float) $this->shipping_cost,
            'shipping_cost' => (float) $this->shipping_cost,
            'tax' => (float) $this->tax_amount,
            'total' => (float) $this->total,
            'items_count' => (int) $this->items_count,
            'delivery_method' => $this->delivery_method,
            'payment_method' => $this->payment_method,
            'payment_status' => $this->payment_status,
            'tracking_number' => $this->tracking_number,
            'customer_email' => $this->customer_email,
            'customer_phone' => $this->customer_phone,
            'shipping_address' => $this->shipping_address ?? [],
            'items' => $this->items->map(fn (OrderItem $item) => [
                'product_id' => $item->product_id,
                'variant_id' => $item->variant,
                'name' => $item->name,
                'slug' => $item->slug,
                'variant' => $item->variant,
                'image' => $item->resolvedImage(),
                'image_source' => $item->image_source ?: 'local',
                'qty' => (int) $item->qty,
                'price' => (float) $item->price,
                'line_total' => (float) $item->line_total,
            ])->all(),
        ];
    }

    public function toAdminArray(): array
    {
        $this->loadMissing(['items', 'invoice', 'statusHistories.admin']);

        $base = $this->toViewArray();
        $base['user_id'] = $this->user_id;
        $base['notes'] = $this->notes;
        $base['invoice_no'] = $this->invoice?->invoice_no;
        $base['total_cost'] = (float) $this->total_cost;
        $base['total_profit'] = (float) $this->total_profit;
        $base['status_histories'] = $this->statusHistories
            ->sortByDesc('id')
            ->map(fn (OrderStatusHistory $h) => [
                'from_status' => $h->from_status,
                'to_status' => $h->to_status,
                'note' => $h->note,
                'admin_name' => $h->admin?->name ?? 'System',
                'created_at' => $h->created_at?->format('M d, Y H:i') ?? '',
            ])
            ->values()
            ->all();

        $base['items'] = $this->items->map(fn (OrderItem $item) => [
            'product_id' => $item->product_id,
            'variant_id' => $item->variant,
            'name' => $item->name,
            'slug' => $item->slug,
            'variant' => $item->variant,
            'image' => $item->resolvedImage(),
            'image_source' => $item->image_source ?: 'local',
            'qty' => (int) $item->qty,
            'price' => (float) $item->price,
            'unit_buy_price' => (float) $item->unit_buy_price,
            'line_total' => (float) $item->line_total,
            'line_cost_total' => (float) $item->line_cost_total,
            'unit_profit' => (float) $item->unit_profit,
            'line_profit' => (float) $item->line_profit,
        ])->all();

        return $base;
    }
}
