<?php

namespace App\Models;

use App\BrandEnum;
use Carbon\Carbon;
use Spatie\Sluggable\HasSlug;
use App\Observers\BrandObserver;
use Spatie\Sluggable\SlugOptions;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Casts\Attribute;
use Illuminate\Database\Eloquent\Attributes\ObservedBy;
// #[ObservedBy(BrandObserver::class)]
class Brand extends Model
{
    use HasSlug;
    protected $fillable = ["name", "slug", "thumbnail", "description", "status"];
    protected $casts = [
        "status" => BrandEnum::class,
    ];
    public function getSlugOptions(): SlugOptions
    {
        return SlugOptions::create()
            ->generateSlugsFrom('name')
            ->saveSlugsTo('slug');
    }

    protected function createdAt(): Attribute
    {
        return Attribute::make(get: fn($value) => Carbon::parse($value)->format('d-m-Y'));
    }
}
