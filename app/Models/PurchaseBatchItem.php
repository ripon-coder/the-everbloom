<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class PurchaseBatchItem extends Model
{
    protected $fillable = [
        'purchase_batch_id',
        'vendor_id',
        'product_id',
        'product_variant_id',
        'batch_lot',
        'purchased_qty',
        'remaining_qty',
        'unit_buy_price',
        'line_total_cost',
    ];

    protected function casts(): array
    {
        return [
            'purchased_qty' => 'integer',
            'remaining_qty' => 'integer',
            'unit_buy_price' => 'float',
            'line_total_cost' => 'float',
        ];
    }

    public function batch(): BelongsTo
    {
        return $this->belongsTo(PurchaseBatch::class, 'purchase_batch_id');
    }

    public function vendor(): BelongsTo
    {
        return $this->belongsTo(Vendor::class);
    }

    public function product(): BelongsTo
    {
        return $this->belongsTo(Product::class);
    }

    public function variant(): BelongsTo
    {
        return $this->belongsTo(ProductVariant::class, 'product_variant_id');
    }
}

