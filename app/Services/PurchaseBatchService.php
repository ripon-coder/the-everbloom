<?php

namespace App\Services;

use App\Models\Product;
use App\Models\ProductVariant;
use App\Models\PurchaseBatch;
use App\Models\PurchaseBatchItem;
use App\Models\StockMovement;
use App\Models\Vendor;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\DB;

class PurchaseBatchService
{
    /**
     * Weighted average buy price calculated strictly over currently available stock.
     */
    public function calculateWeightedAveragePrice(
        int $availableStock,
        float $currentAvgBuyPrice,
        int $newBatchQty,
        float $newBatchUnitBuyPrice
    ): float {
        $available = max(0, $availableStock);
        $incoming = max(0, $newBatchQty);
        $totalQty = $available + $incoming;

        if ($totalQty <= 0) {
            return round($newBatchUnitBuyPrice, 2);
        }

        $weighted = (($available * $currentAvgBuyPrice) + ($incoming * $newBatchUnitBuyPrice)) / $totalQty;

        return round($weighted, 2);
    }

    public function createBatch(array $data): PurchaseBatch
    {
        return DB::transaction(function () use ($data) {
            $vendorName = trim((string) ($data['vendor_name'] ?? ''));
            $vendor = !empty($data['vendor_id'])
                ? Vendor::findOrFail($data['vendor_id'])
                : Vendor::firstOrCreate(
                    ['name' => $vendorName],
                    ['company_name' => $vendorName, 'is_active' => true]
                );

            $nextSeq = (PurchaseBatch::max('id') ?? 0) + 1;
            $batchCode = 'BATCH-' . now()->format('Y') . '-' . str_pad((string) $nextSeq, 4, '0', STR_PAD_LEFT);
            while (PurchaseBatch::where('batch_code', $batchCode)->exists()) {
                $nextSeq++;
                $batchCode = 'BATCH-' . now()->format('Y') . '-' . str_pad((string) $nextSeq, 4, '0', STR_PAD_LEFT);
            }

            $purchasedAt = $data['purchased_at'] ?? now()->toDateString();

            $batch = PurchaseBatch::create([
                'batch_code' => $batchCode,
                'vendor_id' => $vendor->id,
                'vendor_name' => $vendor->name,
                'reference_no' => $data['reference_no'] ?? null,
                'purchased_at' => $purchasedAt,
                'total_qty' => 0,
                'total_cost' => 0,
                'notes' => $data['notes'] ?? null,
                'created_by' => Auth::guard('admin')->id(),
            ]);

            $totalQty = 0;
            $totalCost = 0.0;

            foreach ($data['items'] as $row) {
                $product = Product::lockForUpdate()->findOrFail($row['product_id']);
                $purchasedQty = (int) $row['qty'];
                $unitBuyPrice = round((float) $row['unit_buy_price'], 2);
                $lineTotalCost = round($purchasedQty * $unitBuyPrice, 2);
                $batchLot = $row['batch_lot'] ?? ('LOT-' . now()->format('Ymd') . '-' . $product->id);

                $availableQty = max(0, (int) $product->stock_count);
                $currentAvg = (float) $product->avg_buy_price;
                $newAvgBuyPrice = $this->calculateWeightedAveragePrice(
                    $availableQty,
                    $currentAvg,
                    $purchasedQty,
                    $unitBuyPrice
                );

                $variant = !empty($row['product_variant_id'])
                    ? ProductVariant::lockForUpdate()->find($row['product_variant_id'])
                    : null;

                PurchaseBatchItem::create([
                    'purchase_batch_id' => $batch->id,
                    'vendor_id' => $vendor->id,
                    'product_id' => $product->id,
                    'product_variant_id' => $variant?->id,
                    'batch_lot' => $batchLot,
                    'purchased_qty' => $purchasedQty,
                    'remaining_qty' => $purchasedQty,
                    'unit_buy_price' => $unitBuyPrice,
                    'line_total_cost' => $lineTotalCost,
                ]);

                $product->update([
                    'avg_buy_price' => $newAvgBuyPrice,
                    'last_buy_price' => $unitBuyPrice,
                    'stock_count' => $availableQty + $purchasedQty,
                    'batch_lot' => $batchLot,
                    'last_restocked_at' => $purchasedAt,
                    'stock_status' => 'in_stock',
                ]);

                if ($variant) {
                    $vAvail = max(0, (int) $variant->stock_count);
                    $vAvg = $this->calculateWeightedAveragePrice(
                        $vAvail,
                        (float) $variant->avg_buy_price,
                        $purchasedQty,
                        $unitBuyPrice
                    );
                    $variant->update([
                        'avg_buy_price' => $vAvg,
                        'last_buy_price' => $unitBuyPrice,
                        'stock_count' => $vAvail + $purchasedQty,
                        'stock_status' => 'in_stock',
                    ]);
                } else {
                    $product->productVariants()->update([
                        'avg_buy_price' => $newAvgBuyPrice,
                        'last_buy_price' => $unitBuyPrice,
                    ]);
                    $primaryVariant = $product->productVariants()->orderBy('sort_order')->first();
                    if ($primaryVariant) {
                        $primaryVariant->update([
                            'stock_count' => max(0, (int) $primaryVariant->stock_count) + $purchasedQty,
                            'stock_status' => 'in_stock',
                        ]);
                    }
                }

                StockMovement::create([
                    'product_id' => $product->id,
                    'product_variant_id' => $variant?->id,
                    'delta' => $purchasedQty,
                    'reason' => 'purchase_received',
                    'reference_type' => PurchaseBatch::class,
                    'reference_id' => $batch->id,
                    'note' => "Purchase batch {$batch->batch_code}",
                    'admin_id' => Auth::guard('admin')->id(),
                    'created_at' => now(),
                ]);

                $totalQty += $purchasedQty;
                $totalCost += $lineTotalCost;
            }

            $batch->update([
                'total_qty' => $totalQty,
                'total_cost' => round($totalCost, 2),
            ]);

            Cache::forget('storefront_catalog_json');

            return $batch->load(['vendor', 'items.product']);
        });
    }

    /**
     * Deduct sold quantity from available purchase batch items in FIFO order.
     *
     * @return array<int, array{purchase_batch_item_id: int, qty: int}>
     */
    public function deductFromAvailableBatches(int $productId, int $qty): array
    {
        $consumed = [];
        $remainingToDeduct = $qty;
        if ($remainingToDeduct <= 0) {
            return $consumed;
        }

        $batchItems = PurchaseBatchItem::where('product_id', $productId)
            ->where('remaining_qty', '>', 0)
            ->orderBy('id')
            ->lockForUpdate()
            ->get();

        foreach ($batchItems as $item) {
            if ($remainingToDeduct <= 0) {
                break;
            }
            $take = min($remainingToDeduct, (int) $item->remaining_qty);
            $item->decrement('remaining_qty', $take);
            $consumed[] = [
                'purchase_batch_item_id' => $item->id,
                'qty' => $take,
            ];
            $remainingToDeduct -= $take;
        }

        return $consumed;
    }
}
