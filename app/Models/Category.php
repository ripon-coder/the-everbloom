<?php

namespace App\Models;

use App\Services\ImageStorageService;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Category extends Model
{
    protected $fillable = [
        'parent_id',
        'level',
        'slug',
        'name',
        'short_name',
        'tagline',
        'description',
        'image',
        'image_source',
        'sort_order',
        'is_active',
        'meta_title',
        'meta_description',
    ];

    protected $appends = ['item_count'];

    protected function casts(): array
    {
        return [
            'level' => 'integer',
            'is_active' => 'boolean',
            'sort_order' => 'integer',
        ];
    }

    public function parent(): BelongsTo
    {
        return $this->belongsTo(Category::class, 'parent_id');
    }

    public function children(): HasMany
    {
        return $this->hasMany(Category::class, 'parent_id')->orderBy('sort_order')->orderBy('name');
    }

    public function assignedAttributes(): BelongsToMany
    {
        return $this->belongsToMany(Attribute::class, 'category_attribute')->withTimestamps();
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

    public function getHierarchyPathAttribute(): string
    {
        $parts = [];
        $current = $this;
        $guard = 0;
        while ($current && $guard < 5) {
            array_unshift($parts, $current->name);
            $current = $current->parent;
            $guard++;
        }

        return implode(' > ', $parts);
    }

    public function effectiveAttributeIds(): array
    {
        $this->loadMissing(['assignedAttributes', 'parent.assignedAttributes', 'parent.parent.assignedAttributes']);

        return collect()
            ->merge($this->assignedAttributes->pluck('id'))
            ->merge($this->parent?->assignedAttributes?->pluck('id') ?? [])
            ->merge($this->parent?->parent?->assignedAttributes?->pluck('id') ?? [])
            ->unique()
            ->values()
            ->all();
    }

    public function descendantAndSelfIds(): array
    {
        $ids = [$this->id];
        $children = $this->children()->with('children')->get();
        foreach ($children as $child) {
            $ids[] = $child->id;
            foreach ($child->children as $grandChild) {
                $ids[] = $grandChild->id;
            }
        }

        return array_values(array_unique($ids));
    }
}
