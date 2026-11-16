<?php

namespace App\Services;

use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;
use Intervention\Image\Laravel\Facades\Image;

class ImageStorageService
{
    /**
     * Convert an uploaded image to WebP and store it on the configured third-party disk.
     *
     * @return array{path: string, source: string, url: string}
     */
    public function uploadAsWebp(
        UploadedFile $file,
        string $directory = 'products',
        ?string $customName = null,
        int $quality = 85,
        ?string $disk = null
    ): array {
        $sourceDisk = $disk ?: env('FILESYSTEM_CLOUD_DISK', config('filesystems.cloud', 's3'));

        $baseName = $customName
            ? Str::slug($customName) . '-' . Str::lower(Str::random(6))
            : Str::uuid()->toString();

        $relativePath = trim($directory, '/') . '/' . $baseName . '.webp';

        $encodedWebp = Image::read($file->getRealPath())
            ->scaleDown(width: 1600, height: 2000)
            ->toWebp($quality);

        Storage::disk($sourceDisk)->put($relativePath, (string) $encodedWebp, 'public');

        return [
            'path'   => $relativePath,
            'source' => $sourceDisk,
            'url'    => $this->resolveUrl($relativePath, $sourceDisk),
        ];
    }

    /**
     * Resolve the public URL of an image based on its path and image_source column.
     */
    public function resolveUrl(?string $path, ?string $source = 'local'): string
    {
        if (empty($path)) {
            return '';
        }

        if (Str::startsWith($path, ['http://', 'https://', '/'])) {
            return $path;
        }

        $source = $source ?: 'local';

        if ($source === 'local') {
            $clean = ltrim($path, '/');
            if (Str::startsWith($clean, ['images/', 'admin/'])) {
                return asset($clean);
            }

            return asset('images/' . $clean);
        }

        return Storage::disk($source)->url($path);
    }

    /**
     * Delete an image from its recorded third-party source disk (never deletes seeded 'local' demo assets).
     */
    public function delete(?string $path, ?string $source = null): void
    {
        if (empty($path) || empty($source) || $source === 'local') {
            return;
        }

        Storage::disk($source)->delete($path);
    }
}
