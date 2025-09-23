<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Spatie\MediaLibrary\HasMedia;
use Spatie\MediaLibrary\InteractsWithMedia;

class ProductVariantImage extends Model implements HasMedia
{
    use HasFactory, InteractsWithMedia;

    /**
     * The attributes that are mass assignable.
     *
     * @var array<int, string>
     */
    protected $fillable = [
        'product_variant_id',
        'is_default',
    ];

    /**
     * The attributes that should be cast.
     *
     * @var array<string, string>
     */
    protected $casts = [
        'is_default' => 'boolean',
    ];

    /**
     * Get the product variant that owns the image.
     */
    public function productVariant()
    {
        return $this->belongsTo(ProductVariant::class);
    }

    /**
     * Register the media collections for the model.
     */
    public function registerMediaCollections(): void
    {
        $this->addMediaCollection('variant_images')
            ->useDisk('public')
            ->acceptsMimeTypes(['image/jpeg', 'image/png', 'image/webp', 'image/gif'])
            ->onlyKeepLatest(10) // Keep only latest 10 images per variant image record
            ->registerMediaConversions(function () {
                $this->addMediaConversion('thumb')
                    ->width(150)
                    ->height(150)
                    ->sharpen(10);

                $this->addMediaConversion('medium')
                    ->width(400)
                    ->height(400)
                    ->sharpen(10);

                $this->addMediaConversion('large')
                    ->width(800)
                    ->height(800)
                    ->sharpen(10);
            });
    }

    /**
     * Get the URL of the image.
     *
     * @param string $conversion
     * @return string|null
     */
    public function getImageUrl($conversion = '')
    {
        $media = $this->getFirstMedia('variant_images');
        
        if (!$media) {
            return asset('/images/default-logo.png');
        }

        return $conversion ? $media->getUrl($conversion) : $media->getUrl();
    }

    /**
     * Get the thumbnail URL.
     *
     * @return string|null
     */
    public function getThumbnailUrl()
    {
        return $this->getImageUrl('thumb');
    }

    /**
     * Get the medium image URL.
     *
     * @return string|null
     */
    public function getMediumUrl()
    {
        return $this->getImageUrl('medium');
    }

    /**
     * Get the large image URL.
     *
     * @return string|null
     */
    public function getLargeUrl()
    {
        return $this->getImageUrl('large');
    }
}
