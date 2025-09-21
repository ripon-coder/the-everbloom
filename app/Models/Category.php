<?php

namespace App\Models;

use App\Trait\HasImage;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use Spatie\MediaLibrary\HasMedia;
use Spatie\MediaLibrary\InteractsWithMedia;

class Category extends Model implements HasMedia
{
    use HasFactory, SoftDeletes, InteractsWithMedia, HasImage;
    
    protected $fillable = [
        "parent_id", 
        "slug", 
        "name", 
        "description", 
        "options", 
        "status"
    ];

    protected $casts = [
        'options' => 'json',
        'parent_id' => 'integer',
    ];

    public function registerMediaCollections(): void
    {
        $this->addMediaCollection('category_image')->singleFile();
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
