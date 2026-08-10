<?php

namespace App\Models;

use App\Trait\HasImage;
use Spatie\MediaLibrary\HasMedia;
use Illuminate\Database\Eloquent\Model;
use App\Models\Scopes\ExcludeDeletedScope;
use Spatie\MediaLibrary\InteractsWithMedia;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Spatie\MediaLibrary\MediaCollections\Models\Media;

class Category extends Model implements HasMedia
{
    use HasFactory, SoftDeletes, InteractsWithMedia, HasImage;

    protected $fillable = [
        "parent_id",
        "slug",
        "name",
        "description",
        "options",
        "status",
        "is_featured",
        "image"
    ];

    protected $imageColumns = ['image'];

    protected $imageDisk = 'public';

    protected $casts = [
        'options' => 'json'
    ];

    // protected static function booted()
    // {
    //     static::addGlobalScope(new ExcludeDeletedScope);
    // }

    public function registerMediaCollections(): void
    {
        $this->addMediaCollection('category_image')->singleFile();
    }

    /*
    public function registerMediaConversions(Media $media = null): void
    {
        $this->addMediaConversion('webp')
            ->format('webp')
            ->quality(80)
            ->performOnCollections('category_image');
    }
    */

    public function getImageUrl()
    {
        return $this->traitGetImageUrl('image');
    }

    /**
     * Get the parent category.
     */
    public function parent()
    {
        return $this->belongsTo(Category::class, 'parent_id');
    }

    /**
     * Get the child categories.
     */
    public function children()
    {
        return $this->hasMany(Category::class, 'parent_id');
    }

    /**
     * Get products that belong to this category.
     */
    public function products()
    {
        return $this->hasMany(Product::class);
    }

    /**
     * Scope a query to only include active categories.
     */
    public function scopeActive($query)
    {
        return $query->where('status', \App\Constants\CategoryStatus::ACTIVE);
    }

    /**
     * Scope a query to only include root categories (no parent).
     */
    public function scopeRoot($query)
    {
        return $query->whereNull('parent_id');
    }

    /**
     * Scope a query to order by sort order and name.
     */
    public function scopeOrdered($query)
    {
        return $query->orderBy('name');
    }
}
