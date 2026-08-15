<?php

namespace App\Models;

use App\Trait\HasImage;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Spatie\MediaLibrary\HasMedia;
use Spatie\MediaLibrary\InteractsWithMedia;
use Spatie\MediaLibrary\MediaCollections\Models\Media;

class ProductImage extends Model implements HasMedia
{
    use HasFactory, InteractsWithMedia, HasImage;

    /**
     * The attributes that are mass assignable.
     *
     * @var array<int, string>
     */
    protected $fillable = [
        'product_id',
        'is_default',
        'image',
    ];

    protected $imageColumns = ['image'];

    protected $imageDisk = 'backblaze';

    protected $appends = ['image_url'];

    public function getImageUrlAttribute()
    {
        return $this->getImageUrl();
    }

    /**
     * The attributes that should be cast.
     *
     * @var array<string, string>
     */
    protected $casts = [
        'is_default' => 'boolean',
    ];

    /**
     * Get the product that owns the image.
     */
    public function product()
    {
        return $this->belongsTo(Product::class);
    }

    /**
     * Register the media collections for the model.
     */
    public function registerMediaCollections(): void
    {
        $this->addMediaCollection('product_images')->singleFile();
    }

    /**
     * Register the media conversions for the model.
     */
    /*
    public function registerMediaConversions(Media $media = null): void
    {
        $this->addMediaConversion('webp')
            ->format('webp')
            ->quality(80)
            ->performOnCollections('product_images');
    }
    */
    public function getImageUrl(): string
    {
        if ($this->image) {
            return $this->traitGetImageUrl('image');
        }

        // Fallback to Spatie MediaLibrary
        $mediaUrl = $this->getFirstMediaUrl('product_images');
        if ($mediaUrl) {
            return $mediaUrl;
        }

        return asset('images/default-logo.png');
    }

}
