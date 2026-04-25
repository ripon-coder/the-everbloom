<?php
require __DIR__.'/vendor/autoload.php';
$app = require_once __DIR__.'/bootstrap/app.php';
$kernel = $app->make(Illuminate\Contracts\Console\Kernel::class);
$kernel->bootstrap();

$flashSale = \App\Models\FlashSale::active()->first();
if ($flashSale) {
    echo "Active FlashSale: " . $flashSale->name . "\n";
    echo "Product Count: " . $flashSale->products()->count() . "\n";
} else {
    echo "No active FlashSale found.\n";
}
