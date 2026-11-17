<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class FlashSaleProduct extends Model
{
    protected $fillable = [
        'flash_sale_id',
        'product_id',
        'product_variant_id',
        'sale_price',
        'sale_stock',
        'sold_qty',
        'max_qty_per_customer',
        'is_active',
    ];

    protected function casts(): array
    {
        return [
            'sale_price' => 'float',
            'sale_stock' => 'integer',
            'sold_qty' => 'integer',
            'max_qty_per_customer' => 'integer',
            'is_active' => 'boolean',
        ];
    }

    public function flashSale(): BelongsTo
    {
        return $this->belongsTo(FlashSale::class);
    }

    public function product(): BelongsTo
    {
        return $this->belongsTo(Product::class);
    }

    public function variant(): BelongsTo
    {
        return $this->belongsTo(ProductVariant::class, 'product_variant_id');
    }

    public function remainingStock(): int
    {
        return max(0, (int) $this->sale_stock - (int) $this->sold_qty);
    }

    public function discountPercent(): int
    {
        $regular = $this->variant?->price ?? $this->product?->price ?? 0;
        if ($regular <= 0 || $this->sale_price >= $regular) {
            return 0;
        }

        return (int) round((($regular - $this->sale_price) / $regular) * 100);
    }
}

