<?php

namespace App\Models;

use App\Services\ImageStorageService;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;

class ProductVariant extends Model
{
    protected $fillable = [
        'product_id',
        'size',
        'title',
        'attributes_json',
        'sku',
        'price',
        'compare_at_price',
        'avg_buy_price',
        'last_buy_price',
        'stock_count',
        'stock_status',
        'image',
        'image_source',
        'sort_order',
        'is_active',
    ];

    protected function casts(): array
    {
        return [
            'attributes_json' => 'array',
            'price' => 'float',
            'compare_at_price' => 'float',
            'avg_buy_price' => 'float',
            'last_buy_price' => 'float',
            'stock_count' => 'integer',
            'sort_order' => 'integer',
            'is_active' => 'boolean',
        ];
    }

    public function product(): BelongsTo
    {
        return $this->belongsTo(Product::class);
    }

    public function attributeValues(): BelongsToMany
    {
        return $this->belongsToMany(AttributeValue::class, 'product_variant_values')
            ->withPivot('attribute_id')
            ->withTimestamps();
    }

    public function resolvedImage(): ?string
    {
        if (empty($this->image)) {
            return null;
        }

        return app(ImageStorageService::class)->resolveUrl($this->image, $this->image_source ?: 'local');
    }
}
