<?php

namespace App\Models;

use Carbon\Carbon;
use App\CategoryEnum;
use Spatie\Sluggable\HasSlug;
use Spatie\Sluggable\SlugOptions;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Database\Eloquent\Casts\Attribute;
use Illuminate\Database\Eloquent\Builder;

class Category extends Model
{
    use HasSlug, SoftDeletes;
    protected $fillable = ["name", "slug", "thumbnail", "parent_id", "description", "status"];
    protected $casts = [
        "status" => CategoryEnum::class,
    ];
    public function getSlugOptions(): SlugOptions
    {
        return SlugOptions::create()
            ->generateSlugsFrom('name')
            ->saveSlugsTo('slug');
    }
    public function scopeActive(Builder $query): Builder
    {
        return $query->where('status', CategoryEnum::ACTIVE);
    }

    public function parent()
    {
        return $this->belongsTo(Category::class, 'parent_id');
    }

    public function children()
    {
        return $this->hasMany(Category::class, 'parent_id');
    }
    public function childrenRecursive()
    {
        return $this->hasMany(Category::class, 'parent_id')->with('childrenRecursive');
    }


    protected function createdAt(): Attribute
    {
        return Attribute::make(get: fn($value) => Carbon::parse($value)->format('d-m-Y'));
    }
}
