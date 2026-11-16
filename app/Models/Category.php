<?php

namespace App\Models;

use App\Services\ImageStorageService;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Category extends Model
{
    protected $fillable = [
        'slug',
        'name',
        'short_name',
        'tagline',
        'description',
        'image',
        'image_source',
        'sort_order',
        'is_active',
    ];

    protected $appends = ['item_count'];

    protected function casts(): array
    {
        return [
            'is_active' => 'boolean',
            'sort_order' => 'integer',
        ];
    }

    public function products(): HasMany
    {
        return $this->hasMany(Product::class);
    }

    public function getItemCountAttribute(): int
    {
        if (array_key_exists('item_count', $this->attributes)) {
            return (int) $this->attributes['item_count'];
        }

        if (array_key_exists('products_count', $this->attributes)) {
            return (int) $this->attributes['products_count'];
        }

        return (int) $this->products()->where('is_active', true)->count();
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
