<?php

namespace App\Models;

use App\Trait\HasImage;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Spatie\MediaLibrary\HasMedia;
use Spatie\MediaLibrary\InteractsWithMedia;
use Spatie\MediaLibrary\MediaCollections\Models\Media;

class Slider extends Model implements HasMedia
{
    use HasFactory, InteractsWithMedia, HasImage;

    protected $fillable = [
        'image',
        'title',
        'subtitle',
        'btn_text',
        'btn_link',
        'status',
        'sort_order',
    ];

    public function registerMediaCollections(): void
    {
        $this->addMediaCollection('slider_image')->singleFile();
    }

    /*
    public function registerMediaConversions(Media $media = null): void
    {
        $this->addMediaConversion('webp')
            ->format('webp')
            ->quality(80)
            ->performOnCollections('slider_image');
    }
    */

    public function getImageUrl()
    {
        return $this->traitGetImageUrl('slider_image');
    }
}
