<?php

namespace App\Models;

use App\Trait\HasImage;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Spatie\MediaLibrary\HasMedia;
use Spatie\MediaLibrary\InteractsWithMedia;
use Spatie\MediaLibrary\MediaCollections\Models\Media;
class ProductVariantImage extends Model implements HasMedia
{
    use HasFactory, InteractsWithMedia, HasImage;
    
    protected $appends = ['image_url'];

    public function getImageUrlAttribute()
    {
        return $this->getImageUrl();
    }

    /**
     * The attributes that are mass assignable.
     *
     * @var array<int, string>
     */
    protected $fillable = [
        'product_variant_id',
        'is_default',
        'image',
    ];

    protected $imageColumns = ['image'];

    protected $imageDisk = 'public';

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
        $this->addMediaCollection('variant_images')->singleFile();
    }
    /*
    public function registerMediaConversions(Media $media = null): void
    {
        $this->addMediaConversion('webp')
            ->format('webp')
            ->quality(80)
            ->performOnCollections('variant_images');
    }
    */
    /**
     * Get the URL of the image.
     *
     * @return string|null
     */
    public function getImageUrl()
    {
        return $this->traitGetImageUrl('image');
    }

}
