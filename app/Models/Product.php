<?php

namespace App\Models;

use App\Constants\ProductStatus;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class Product extends Model
{
    use HasFactory, SoftDeletes;

    /**
     * The attributes that are mass assignable.
     *
     * @var array<int, string>
     */
    protected $fillable = [
        'brand_id',
        'category_id',
        'name',
        'slug',
        'description',
        'price',
        'status',
    ];

    /**
     * The attributes that should be cast.
     *
     * @var array<string, string>
     */
    protected $casts = [
        'price' => 'decimal:2',
        'status' => ProductStatus::class,
    ];

    /**
     * Get the brand that owns the product.
     */
    public function brand()
    {
        return $this->belongsTo(Brand::class);
    }

    /**
     * Get the category that owns the product.
     */
    public function category()
    {
        return $this->belongsTo(Category::class);
    }

    /**
     * Get the product variants for the product.
     */
    public function variants()
    {
        return $this->hasMany(ProductVariant::class);
    }

    /**
     * Get the images for the product.
     */
    public function images()
    {
        return $this->hasMany(ProductImage::class);
    }

    public function firstImage()
    {
        return $this->hasOne(ProductImage::class)->oldestOfMany();
    }

    /**
     * Get the products that are active.
     */
    public function scopeActive($query)
    {
        return $query->where('status', ProductStatus::ACTIVE);
    }

    /**
     * Get the flash sales associated with the product.
     */
    public function flashSales()
    {
        return $this->belongsToMany(FlashSale::class, 'flash_sale_product')
                    ->withPivot('discount_price', 'discount_percentage')
                    ->withTimestamps();
    }

    /**
     * Get the orders that include this product.
     */
    public function orders()
    {
        return $this->belongsToMany(Order::class, 'order_products')
                    ->withPivot('quantity', 'unit_price', 'total_price', 'discount_amount', 'notes')
                    ->withTimestamps();
    }

    /**
     * Get the order products for this product.
     */
    public function orderProducts()
    {
        return $this->hasMany(OrderProduct::class);
    }
}
