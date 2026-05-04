<?php
require __DIR__.'/vendor/autoload.php';
$app = require_once __DIR__.'/bootstrap/app.php';
$kernel = $app->make(Illuminate\Contracts\Console\Kernel::class);
$kernel->bootstrap();

config(['filesystems.disks.backblaze' => [
    'driver' => 's3',
    'key' => '6f198e1d044f',
    'secret' => '0069ef650dfd684437d3345f32417af7a6ff0bb53a',
    'region' => 'ca-east-006',
    'bucket' => 'feriwalarhat',
    'endpoint' => 'https://s3.ca-east-006.backblazeb2.com',
    'use_path_style_endpoint' => true,
]]);

try {
    Storage::disk('backblaze')->put('debug.txt', 'test');
    echo "Upload Success\n";
    $files = Storage::disk('backblaze')->files();
    echo "Files found: " . count($files) . "\n";
    foreach ($files as $file) {
        echo "- $file\n";
    }
} catch (\Exception $e) {
    echo "Error: " . $e->getMessage() . "\n";
    if (method_exists($e, 'getResponse')) {
        echo "Response: " . (string) $e->getResponse()->getBody() . "\n";
    }
}
