<?php

namespace App\Models;

use App\Services\ImageStorageService;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Laravel\Scout\Searchable;

class Product extends Model
{
    use Searchable;

    public function searchableAs(): string
    {
        return 'products_index';
    }

    public function shouldBeSearchable(): bool
    {
        return (bool) $this->is_active;
    }

    public function toSearchableArray(): array
    {
        $this->loadMissing('categoryRelation');

        return [
            'id' => $this->id,
            'name' => $this->name,
            'slug' => $this->slug,
            'sku' => $this->sku,
            'category' => $this->categoryRelation?->name ?? '',
            'short_description' => $this->short_description,
            'description' => $this->description,
            'ingredients' => $this->ingredients,
            'skin_types' => is_array($this->skin_types) ? implode(' ', $this->skin_types) : '',
            'benefits' => is_array($this->benefits) ? implode(' ', $this->benefits) : '',
            'badge' => $this->badge,
        ];
    }
    protected $fillable = [
        'category_id',
        'slug',
        'sku',
        'name',
        'subtitle',
        'price',
        'compare_at_price',
        'avg_buy_price',
        'last_buy_price',
        'rating',
        'review_count',
        'short_description',
        'description',
        'ingredients',
        'key_ingredients',
        'benefits',
        'skin_types',
        'how_to_use',
        'sizes',
        'images',
        'image_source',
        'stock_count',
        'min_threshold',
        'stock_status',
        'batch_lot',
        'last_restocked_at',
        'is_featured',
        'best_seller',
        'badge',
        'is_active',
    ];

    protected $appends = [
        'category',
        'category_slug',
        'variants',
    ];

    protected function casts(): array
    {
        return [
            'sizes' => 'array',
            'images' => 'array',
            'key_ingredients' => 'array',
            'benefits' => 'array',
            'skin_types' => 'array',
            'is_featured' => 'boolean',
            'best_seller' => 'boolean',
            'is_active' => 'boolean',
            'price' => 'float',
            'compare_at_price' => 'float',
            'avg_buy_price' => 'float',
            'last_buy_price' => 'float',
            'rating' => 'float',
            'review_count' => 'integer',
            'stock_count' => 'integer',
            'min_threshold' => 'integer',
            'last_restocked_at' => 'date',
        ];
    }

    public function categoryRelation(): BelongsTo
    {
        return $this->belongsTo(Category::class, 'category_id');
    }

    public function productVariants(): HasMany
    {
        return $this->hasMany(ProductVariant::class)->orderBy('sort_order');
    }

    public function productImages(): HasMany
    {
        return $this->hasMany(ProductImage::class)->orderBy('sort_order');
    }

    public function reviews(): HasMany
    {
        return $this->hasMany(Review::class);
    }

    public function getCategoryAttribute(): string
    {
        return $this->categoryRelation?->name ?? 'Skincare';
    }

    public function getCategorySlugAttribute(): string
    {
        return $this->categoryRelation?->slug ?? 'face-care';
    }

    public function getVariantsAttribute(): array
    {
        return $this->productVariants->map(fn (ProductVariant $v) => [
            'id' => $v->id,
            'title' => $v->title ?: $v->size,
            'size' => $v->title ?: $v->size,
            'attributes' => $v->attributes_json ?? [],
            'sku' => $v->sku,
            'price' => (float) $v->price,
            'compare_at_price' => $v->compare_at_price !== null ? (float) $v->compare_at_price : null,
            'stock_status' => $v->stock_status,
            'stock_count' => (int) $v->stock_count,
            'image' => $v->resolvedImage(),
            'raw_image' => $v->image,
            'image_source' => $v->image_source ?: 'local',
        ])->values()->all();
    }

    public function resolvedImages(): array
    {
        $resolver = app(ImageStorageService::class);
        $source = $this->image_source ?: 'local';
        $imgs = is_array($this->images) ? $this->images : [];

        return array_values(array_map(function ($img) use ($resolver, $source) {
            if ($source === 'local') {
                return $img;
            }
            return $resolver->resolveUrl($img, $source);
        }, $imgs));
    }

    public function toStorefrontArray(): array
    {
        return [
            'id' => $this->id,
            'slug' => $this->slug,
            'sku' => $this->sku,
            'name' => $this->name,
            'subtitle' => $this->subtitle,
            'category' => $this->category,
            'category_slug' => $this->category_slug,
            'price' => (float) $this->price,
            'compare_at_price' => $this->compare_at_price !== null ? (float) $this->compare_at_price : null,
            'rating' => (float) $this->rating,
            'review_count' => (int) $this->review_count,
            'short_description' => $this->short_description,
            'description' => $this->description,
            'ingredients' => $this->ingredients,
            'how_to_use' => $this->how_to_use,
            'sizes' => $this->sizes ?? [],
            'variants' => $this->variants,
            'stock_status' => $this->stock_status,
            'stock_count' => (int) $this->stock_count,
            'is_featured' => (bool) $this->is_featured,
            'best_seller' => (bool) $this->best_seller,
            'badge' => $this->badge,
            'images' => $this->resolvedImages(),
            'image_source' => $this->image_source ?: 'local',
        ];
    }
}
