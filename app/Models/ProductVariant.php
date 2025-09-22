<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class ProductVariant extends Model
{
    use HasFactory, SoftDeletes;

    protected $fillable = [
        'product_id',
        'name',
        'sku',
        'price',
        'stock',
        'image'
    ];

    protected $casts = [
        'price' => 'decimal:2',
        'stock' => 'integer'
    ];

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
