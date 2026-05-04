<?php

namespace App\Services;

use Illuminate\Support\Facades\Storage;
use Intervention\Image\ImageManager;
use Intervention\Image\Drivers\Gd\Driver;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Str;

class ImageUploadService
{
    protected $manager;

    public function __construct()
    {
        // Use GD driver as it's common in XAMPP/standard environments
        $this->manager = new ImageManager(new Driver());
    }

    /**
     * Upload and convert image to WebP format, then store it on the specified disk.
     *
     * @param UploadedFile $file
     * @param string $folder
     * @param string $disk
     * @return string|null The path to the uploaded file, or null on failure.
     */
    public function upload(UploadedFile $file, string $folder = 'uploads', string $disk = 'backblaze'): ?string
    {
        try {
            // Generate a unique filename with .webp extension
            $filename = Str::random(40) . '.webp';
            $path = trim($folder, '/') . '/' . $filename;

            // Read the image file
            $image = $this->manager->read($file);

            // Encode to WebP with 80% quality
            $encoded = $image->toWebp(80);

            // Store to the specified disk (default is backblaze)
            Storage::disk($disk)->put($path, $encoded->toString(), [
                'visibility' => 'public',
                'ContentType' => 'image/webp'
            ]);

            return $path;
        } catch (\Exception $e) {
            \Log::error('Image Upload Failed: ' . $e->getMessage());
            return null;
        }
    }

    /**
     * Delete an image from the specified disk.
     *
     * @param string|null $path
     * @param string $disk
     * @return bool
     */
    public function delete(?string $path, string $disk = 'backblaze'): bool
    {
        if (!$path) {
            return false;
        }

        if (Storage::disk($disk)->exists($path)) {
            return Storage::disk($disk)->delete($path);
        }

        return false;
    }

    /**
     * Get the full public URL of the uploaded image.
     *
     * @param string|null $path
     * @param string $disk
     * @return string|null
     */
    public function getUrl(?string $path, string $disk = 'backblaze'): ?string
    {
        if (!$path) {
            return null;
        }

        // If it's already a full URL, return it
        if (filter_var($path, FILTER_VALIDATE_URL)) {
            return $path;
        }

        return Storage::disk($disk)->url($path);
    }
}
