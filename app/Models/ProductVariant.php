<?php

namespace App\Models;

use App\Constants\ProductVariantStatus;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class ProductVariant extends Model
{
    use HasFactory, SoftDeletes;

    protected $fillable = [
        'product_id',
        'sku',
        'buying_price',
        'sell_price',
        'discount_price',
        'stock',
        'weight',
        'status'
    ];

    protected $casts = [
        'buying_price' => 'decimal:2',
        'sell_price' => 'decimal:2',
        'discount_price' => 'decimal:2',
        'stock' => 'integer',
        'weight' => 'decimal:2',
        'status' => ProductVariantStatus::class
    ];

    public function scopeActive($query){
         return $query->where('status', ProductVariantStatus::ACTIVE);
    }

    /**
     * Get the product that owns the variant.
     */
    public function product()
    {
        return $this->belongsTo(Product::class);
    }

    /**
     * Get the variant attributes for the product variant.
     */
    public function variantAttributes()
    {
        return $this->hasMany(VariantAttribute::class);
    }

    /**
     * Get the images for the product variant.
     */
    public function images()
    {
        return $this->hasMany(ProductVariantImage::class);
    }
}
