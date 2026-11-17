<?php

namespace App\Models;

use App\Services\ImageStorageService;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use Illuminate\Database\Eloquent\Relations\HasMany;

class FlashSale extends Model
{
    public const STATUS_DRAFT = 'draft';

    public const STATUS_SCHEDULED = 'scheduled';

    public const STATUS_ACTIVE = 'active';

    public const STATUS_EXPIRED = 'expired';

    public const STATUS_DISABLED = 'disabled';

    protected $fillable = [
        'title',
        'slug',
        'description',
        'banner_image',
        'image_source',
        'starts_at',
        'ends_at',
        'status',
        'show_on_homepage',
        'created_by',
    ];

    protected function casts(): array
    {
        return [
            'starts_at' => 'datetime',
            'ends_at' => 'datetime',
            'show_on_homepage' => 'boolean',
        ];
    }

    public function items(): HasMany
    {
        return $this->hasMany(FlashSaleProduct::class);
    }

    public function products(): BelongsToMany
    {
        return $this->belongsToMany(Product::class, 'flash_sale_products')
            ->withPivot(['id', 'product_variant_id', 'sale_price', 'sale_stock', 'sold_qty', 'max_qty_per_customer', 'is_active'])
            ->withTimestamps();
    }

    public function creator(): BelongsTo
    {
        return $this->belongsTo(Admin::class, 'created_by');
    }

    public function orderItems(): HasMany
    {
        return $this->hasMany(OrderItem::class);
    }

    /**
     * Authoritative live window scope: ignores stale scheduled/active cache status
     * as long as campaign is not draft/disabled and now ∈ [starts_at, ends_at).
     */
    public function scopeLive(Builder $query): Builder
    {
        $now = now();

        return $query->whereNotIn('status', [self::STATUS_DRAFT, self::STATUS_DISABLED])
            ->where('starts_at', '<=', $now)
            ->where('ends_at', '>', $now);
    }

    public function isLiveNow(): bool
    {
        if (in_array($this->status, [self::STATUS_DRAFT, self::STATUS_DISABLED], true)) {
            return false;
        }

        $now = now();

        return $this->starts_at && $this->ends_at
            && $this->starts_at->lte($now)
            && $this->ends_at->gt($now);
    }

    public function effectiveStatus(): string
    {
        if (in_array($this->status, [self::STATUS_DRAFT, self::STATUS_DISABLED], true)) {
            return $this->status;
        }

        $now = now();
        if ($this->ends_at && $this->ends_at->lte($now)) {
            return self::STATUS_EXPIRED;
        }
        if ($this->starts_at && $this->starts_at->gt($now)) {
            return self::STATUS_SCHEDULED;
        }

        return self::STATUS_ACTIVE;
    }

    public function resolvedBannerImage(): string
    {
        if (empty($this->banner_image)) {
            return '';
        }

        $source = $this->image_source ?: 'local';
        if ($source === 'local') {
            return str_starts_with($this->banner_image, '/') || str_starts_with($this->banner_image, 'http')
                ? $this->banner_image
                : asset($this->banner_image);
        }

        return app(ImageStorageService::class)->resolveUrl($this->banner_image, $source);
    }
}

