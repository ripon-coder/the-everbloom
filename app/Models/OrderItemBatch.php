<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class OrderItemBatch extends Model
{
    protected $fillable = [
        'order_item_id',
        'purchase_batch_item_id',
        'qty',
    ];

    protected function casts(): array
    {
        return [
            'qty' => 'integer',
        ];
    }

    public function orderItem(): BelongsTo
    {
        return $this->belongsTo(OrderItem::class);
    }

    public function purchaseBatchItem(): BelongsTo
    {
        return $this->belongsTo(PurchaseBatchItem::class);
    }
}

