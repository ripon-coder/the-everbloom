<?php

namespace App\Trait;

use Intervention\Image\ImageManager;
use Intervention\Image\Drivers\Gd\Driver;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;
use Illuminate\Support\Facades\Schema;

trait HasImage
{
    /**
     * Default image path if no image is found.
     * Can be overridden in the model.
     */
    public static $defaultImage = "/images/default-logo.png";

    /**
     * Boot the trait and register model events.
     */
    public static function bootHasImage()
    {
        static::deleting(function ($model) {
            // Only delete images if the model is being permanently deleted (handles SoftDeletes)
            if (!method_exists($model, 'isForceDeleting') || $model->isForceDeleting()) {
                $model->deleteModelImages();
            }
        });
    }

    /**
     * Delete all images associated with the model's defined image columns.
     */
    public function deleteModelImages()
    {
        $columns = $this->getImageColumns();
        $disk = $this->getImageDisk();

        foreach ($columns as $column) {
            if ($this->{$column}) {
                $this->deleteImage($this->{$column}, $disk);
            }
        }
    }

    /**
     * Get the columns that contain image paths.
     * Override in model: protected $imageColumns = ['image', 'banner'];
     */
    protected function getImageColumns(): array
    {
        if (property_exists($this, 'imageColumns')) {
            return (array) $this->imageColumns;
        }

        return ['image'];
    }

    /**
     * Get the storage disk to use.
     * Override in model: protected $imageDisk = 's3';
     */
    protected function getImageDisk(): string
    {
        if (property_exists($this, 'imageDisk')) {
            return $this->imageDisk;
        }

        return 'public';
    }

    /**
     * Upload an image, convert to WebP, and update the model.
     *
     * @param mixed $image The image file or source
     * @param string $folder Destination folder
     * @param string|null $column Model column to update
     * @param string|null $disk Storage disk
     * @param bool $save Whether to save the model immediately
     * @return string|null The stored image path
     */
    public function uploadImage($image, string $folder, ?string $column = null, ?string $disk = null, bool $save = true): ?string
    {
        if (!$image) {
            return null;
        }

        if ($column === null) {
            $columns = $this->getImageColumns();
            $column = $columns[0] ?? 'image';
        }

        $disk = $disk ?: $this->getImageDisk();

        // Delete old image if it exists to keep storage clean
        if ($this->{$column}) {
            $this->deleteImage($this->{$column}, $disk);
        }

        // Generate a secure, unique filename with .webp extension
        $filename = Str::random(30) . '_' . time() . '.webp';
        $path = trim($folder, '/') . '/' . $filename;

        // Process image using Intervention Image v3 (convert to WebP)
        $manager = new ImageManager(new Driver());
        $img = $manager->read($image);
        $encoded = $img->toWebp(80);

        // Store to disk
        Storage::disk($disk)->put($path, (string) $encoded);

        // Update model attribute
        $this->{$column} = $path;

        // Save if requested and column is valid
        if ($save && (in_array($column, $this->getFillable()) || Schema::hasColumn($this->getTable(), $column))) {
            $this->save();
        }

        return $path;
    }

    /**
     * Delete an image from storage.
     */
    public function deleteImage(?string $path, ?string $disk = null): void
    {
        if (!$path || filter_var($path, FILTER_VALIDATE_URL)) {
            return;
        }

        $disk = $disk ?: $this->getImageDisk();

        if (Storage::disk($disk)->exists($path)) {
            Storage::disk($disk)->delete($path);
        }
    }

    /**
     * Get the full URL for an image.
     *
     * @param string|null $column The column name
     * @param string|null $disk The disk name
     * @return string
     */
    public function traitGetImageUrl(?string $column = null, ?string $disk = null): string
    {
        if ($column === null) {
            $columns = $this->getImageColumns();
            $column = $columns[0] ?? 'image';
        }

        $path = $this->{$column};

        // Fallback: If the specified column is empty but 'image' exists and has a value, use it.
        // This helps when transitioning from MediaLibrary where collection names were used.
        if (!$path && $column !== 'image' && isset($this->image) && $this->image) {
            $path = $this->image;
        }

        // Fallback: Try Spatie MediaLibrary if path is still empty
        if (!$path && method_exists($this, 'getFirstMediaUrl')) {
            $mediaUrl = $this->getFirstMediaUrl();
            if ($mediaUrl) {
                return $mediaUrl;
            }
        }

        if (!$path) {
            return asset(static::$defaultImage);
        }

        // If it's already a full URL, return it
        if (filter_var($path, FILTER_VALIDATE_URL)) {
            return $path;
        }

        $disk = $disk ?: $this->getImageDisk();

        // Optimization: For remote disks (S3, Backblaze), we often skip existence checks 
        // to avoid slow API calls. Local/Public disks are checked for safety.
        $isLocal = in_array($disk, ['local', 'public']);

        if (!$isLocal || Storage::disk($disk)->exists($path)) {
            return Storage::disk($disk)->url($path);
        }

        return asset(static::$defaultImage);
    }
}