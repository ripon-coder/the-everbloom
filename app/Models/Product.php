<?php

namespace App\Models;

use App\Constants\ProductStatus;
use App\Constants\ProductVariantStatus;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use Laravel\Scout\Searchable;

class Product extends Model
{
    use HasFactory, SoftDeletes, Searchable;

    /**
     * The attributes that are mass assignable.
     *
     * @var array<int, string>
     */
    protected $fillable = [
        'admin_id',
        'brand_id',
        'category_id',
        'product_type',
        'is_free_delivery',
        'name',
        'slug',
        'short_description',
        'description',
        'price',
        'status',
        'is_featured',
        'meta_description',
        'meta_title',
    ];

    /**
     * The attributes that should be cast.
     *
     * @var array<string, string>
     */
    protected $casts = [
        'price' => 'decimal:2',
        'status' => ProductStatus::class,
        'is_free_delivery' => 'boolean',
        'is_featured' => 'boolean',
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
     * Get the default/primary variant for the product (hasOne relation).
     */
    public function defaultVariant()
    {
        return $this->hasOne(ProductVariant::class)->oldestOfMany();
    }

    /**
     * Get the active product variants.
     */
    public function activeVariants()
    {
        return $this->hasMany(ProductVariant::class)->where('status', ProductVariantStatus::ACTIVE);
    }

    /**
     * Get the first active variant for the product.
     */
    public function firstActiveVariant()
    {
        return $this->hasOne(ProductVariant::class)
            ->where('status', ProductVariantStatus::ACTIVE)
            ->oldestOfMany();
    }

    /**
     * Get the display price (considering first active variant if available).
     */
    public function getDisplayPriceAttribute()
    {
        $variant = $this->relationLoaded('firstActiveVariant')
            ? $this->firstActiveVariant
            : ($this->relationLoaded('variants')
                ? $this->variants->where('status', ProductVariantStatus::ACTIVE)->first()
                : $this->firstActiveVariant);

        if ($variant) {
            return ($variant->discount_price > 0 && $variant->discount_price < $variant->sell_price)
                ? (float) $variant->discount_price
                : (float) $variant->sell_price;
        }

        return (float) $this->price;
    }

    /**
     * Get the display old price (considering first active variant if available).
     */
    public function getDisplayOldPriceAttribute()
    {
        $variant = $this->relationLoaded('firstActiveVariant')
            ? $this->firstActiveVariant
            : ($this->relationLoaded('variants')
                ? $this->variants->where('status', ProductVariantStatus::ACTIVE)->first()
                : $this->firstActiveVariant);

        if ($variant) {
            return ($variant->discount_price > 0 && $variant->discount_price < $variant->sell_price)
                ? (float) $variant->sell_price
                : ($this->old_price ? (float) $this->old_price : null);
        }

        return $this->old_price ? (float) $this->old_price : null;
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
        return $this->hasOne(ProductImage::class)->where('is_default', true);
    }

    public function anyImage()
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
    public function scopePopular($query)
    {
        return $query->withCount('orderProducts')
            ->orderBy('order_products_count', 'desc');
    }

    public function wishlists()
    {
        return $this->hasMany(Wishlist::class, 'product_id');
    }

    /**
     * Get the reviews for the product.
     */
    public function reviews()
    {
        return $this->hasMany(ProductReview::class)->where('is_approved', true)->latest();
    }

    /**
     * Determine if the model should be searchable.
     *
     * @return bool
     */
    public function shouldBeSearchable()
    {
        return $this->status === ProductStatus::ACTIVE;
    }

    /**
     * Get the indexable data array for the model.
     *
     * @return array
     */
    public function toSearchableArray()
    {
        return [
            'id' => $this->id,
            'name' => $this->name,
            'short_description' => $this->short_description,
            'description' => $this->description,
            'price' => $this->price,
        ];
    }
}
