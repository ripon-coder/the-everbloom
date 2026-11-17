<?php

namespace App\Console\Commands;

use App\Services\FlashSale\FlashSaleService;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\Cache;

class SyncFlashSalesCommand extends Command
{
    protected $signature = 'flashsales:sync';

    protected $description = 'Activate scheduled flash sales within window and expire ended flash sales.';

    public function handle(FlashSaleService $flashSaleService): int
    {
        $result = $flashSaleService->syncStatuses();

        if ($result['activated'] > 0 || $result['expired'] > 0) {
            Cache::forget('storefront_catalog_json');
        }

        $this->info("Flash sales synced: {$result['activated']} activated, {$result['expired']} expired.");

        return self::SUCCESS;
    }
}

