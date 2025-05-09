<?php

namespace App\Models;

use App\AttributeEnum;
use App\BrandEnum;
use Carbon\Carbon;
use Spatie\Sluggable\HasSlug;
use Spatie\Sluggable\SlugOptions;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Database\Eloquent\Casts\Attribute;

class Products extends Model
{
    use HasSlug, SoftDeletes;
    protected $fillable = ["brand_id", "categorie_id", "slug", "name", "thumbnail"," images", "description","price", "feature", "status"];
    protected $casts = [
        "status" => AttributeEnum::class,
    ];
    public function getSlugOptions(): SlugOptions
    {
        return SlugOptions::create()
            ->generateSlugsFrom('name')
            ->saveSlugsTo('slug');
    }
    public function scopeActive($query)
    {
        return $query->where('status', AttributeEnum::ACTIVE);
    }

    protected function createdAt(): Attribute
    {
        return Attribute::make(get: fn($value) => Carbon::parse($value)->format('d-m-Y'));
    }
}
