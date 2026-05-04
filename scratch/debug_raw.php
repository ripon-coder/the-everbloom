<?php
require 'vendor/autoload.php';
$app = require_once 'bootstrap/app.php';
$app->make('Illuminate\Contracts\Console\Kernel')->bootstrap();

use Aws\S3\S3Client;
use Aws\Exception\AwsException;

$key = env('BACKBLAZE_KEY_ID');
$secret = env('BACKBLAZE_APPLICATION_KEY');
$region = env('BACKBLAZE_REGION');
$endpoint = env('BACKBLAZE_ENDPOINT');
$bucket = env('BACKBLAZE_BUCKET');

echo "Testing with raw AWS SDK...\n";
echo "Key: $key\n";
echo "Endpoint: $endpoint\n";

$s3 = new S3Client([
    'version' => 'latest',
    'region'  => $region,
    'endpoint' => $endpoint,
    'use_path_style_endpoint' => true,
    'credentials' => [
        'key'    => $key,
        'secret' => $secret,
    ],
]);

try {
    $result = $s3->putObject([
        'Bucket' => $bucket,
        'Key'    => 'test_raw.txt',
        'Body'   => 'test content raw',
        // 'ACL'    => 'public-read', // Let's try without ACL first
    ]);
    echo "Raw upload successful! URL: " . $result['ObjectURL'] . "\n";
} catch (AwsException $e) {
    echo "AWS Error: " . $e->getAwsErrorMessage() . "\n";
    echo "Error Code: " . $e->getAwsErrorCode() . "\n";
    echo "Request ID: " . $e->getAwsRequestId() . "\n";
} catch (\Exception $e) {
    echo "General Error: " . $e->getMessage() . "\n";
}
