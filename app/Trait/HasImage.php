<?php

namespace App\Trait;

use Intervention\Image\ImageManager;
use Intervention\Image\Drivers\Gd\Driver;

trait HasImage
{
    public static $defaultImage = "/images/default-logo.png";

    /**
     * Upload and convert image to WebP
     */
    public function uploadImage($image, $collection)
    {
        if (!$image) {
            return null;
        }

        $this->clearMediaCollection($collection);

        // Convert to WebP using Intervention Image v3
        $manager = new ImageManager(new Driver());
        $img = $manager->read($image);
        $encoded = $img->toWebp(80);

        // Determine filename
        $filename = 'image';
        if ($image instanceof \Illuminate\Http\UploadedFile) {
            $filename = pathinfo($image->getClientOriginalName(), PATHINFO_FILENAME);
        }

        return $this->addMediaFromString($encoded)
            ->usingFileName($filename . '.webp')
            ->toMediaCollection($collection);
    }

    /**
     * Get image URL easily
     */
    public function traitGetImageUrl($collection, $conversion = '')
    {
        $media = $this->getMedia($collection)->last();
        if ($media) {
            // Since we upload as webp original, we just return the original URL
            return $media->getUrl();
        } else {
            return asset(self::$defaultImage);
        }
    }
}