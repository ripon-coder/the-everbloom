<?php

namespace App\Models;

use App\Services\ImageStorageService;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class OrderItem extends Model
{
    protected $fillable = [
        'order_id',
        'product_id',
        'product_variant_id',
        'name',
        'slug',
        'variant',
        'image',
        'image_source',
        'qty',
        'price',
        'unit_buy_price',
        'line_total',
        'line_cost_total',
        'unit_profit',
        'line_profit',
    ];

    protected function casts(): array
    {
        return [
            'qty' => 'integer',
            'price' => 'float',
            'unit_buy_price' => 'float',
            'line_total' => 'float',
            'line_cost_total' => 'float',
            'unit_profit' => 'float',
            'line_profit' => 'float',
        ];
    }

    public function order(): BelongsTo
    {
        return $this->belongsTo(Order::class);
    }

    public function product(): BelongsTo
    {
        return $this->belongsTo(Product::class);
    }

    public function resolvedImage(): string
    {
        if (empty($this->image)) {
            return '';
        }
        $source = $this->image_source ?: 'local';
        if ($source === 'local') {
            return $this->image;
        }
        return app(ImageStorageService::class)->resolveUrl($this->image, $source);
    }
}
