<?php
require 'vendor/autoload.php';
$app = require_once 'bootstrap/app.php';
$app->make('Illuminate\Contracts\Console\Kernel')->bootstrap();

echo "BACKBLAZE_KEY_ID: " . env('BACKBLAZE_KEY_ID') . "\n";
echo "BACKBLAZE_APPLICATION_KEY: " . (env('BACKBLAZE_APPLICATION_KEY') ? 'SET' : 'NOT SET') . "\n";
echo "BACKBLAZE_REGION: " . env('BACKBLAZE_REGION') . "\n";
echo "BACKBLAZE_BUCKET: " . env('BACKBLAZE_BUCKET') . "\n";
echo "BACKBLAZE_ENDPOINT: " . env('BACKBLAZE_ENDPOINT') . "\n";

try {
    echo "Attempting to upload to backblaze...\n";
    $result = Storage::disk('backblaze')->put('test_debug.txt', 'test content');
    if ($result) {
        echo "Upload successful!\n";
    } else {
        echo "Upload failed without exception.\n";
    }
} catch (\Exception $e) {
    echo "Caught exception: " . $e->getMessage() . "\n";
}
