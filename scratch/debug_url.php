<?php

use App\Models\Brand;
use Illuminate\Support\Facades\Storage;

require __DIR__ . '/../vendor/autoload.php';
$app = require_once __DIR__ . '/../bootstrap/app.php';
$app->make(Illuminate\Contracts\Console\Kernel::class)->bootstrap();

$brand = Brand::first();
if ($brand) {
    $media = $brand->getFirstMedia('brand_logo');
    if ($media) {
        echo "Media Disk: " . $media->disk . "\n";
        echo "Media Path: " . $media->getPath() . "\n";
        echo "Media URL: " . $media->getUrl() . "\n";
    } else {
        echo "No media found for first brand.\n";
    }
} else {
    echo "No brand found.\n";
}

echo "Backblaze Disk URL: " . Storage::disk('backblaze')->url('test.jpg') . "\n";
