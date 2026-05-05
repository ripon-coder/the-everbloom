<?php

namespace App\Models;

use App\Constants\BrandStatus;
use App\Trait\HasImage;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use Spatie\MediaLibrary\HasMedia;
use Spatie\MediaLibrary\InteractsWithMedia;
use Spatie\MediaLibrary\MediaCollections\Models\Media;

class Brand extends Model implements HasMedia
{
    use HasFactory, SoftDeletes, InteractsWithMedia, HasImage;
    protected $fillable = ["slug", "name", "description", "options", "status", "image"];

    protected $casts = [
        'options' => 'json',
    ];
    public function registerMediaCollections(): void
    {
        $this->addMediaCollection('brand_logo')->singleFile();
    }

    /*
    public function registerMediaConversions(Media $media = null): void
    {
        $this->addMediaConversion('webp')
            ->format('webp')
            ->quality(80)
            ->performOnCollections('brand_logo');
    }
    */

    public function getImageUrl()
    {
        return $this->traitGetImageUrl('image');
    }

    public function scopeActive($query)
    {
        return $query->where('status', BrandStatus::ACTIVE);
    }
}
