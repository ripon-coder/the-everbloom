<?php

namespace App\Services\Inventory;

use App\Exceptions\InsufficientStockException;
use App\Models\Order;
use App\Models\OrderItem;
use App\Models\OrderItemBatch;
use App\Models\Product;
use App\Models\ProductVariant;
use App\Models\PurchaseBatchItem;
use App\Models\StockMovement;
use App\Services\PurchaseBatchService;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\DB;

class StockService
{
    public function __construct(
        private readonly PurchaseBatchService $purchaseBatchService
    ) {}

    public function resolveStockStatus(int $stockCount, int $threshold): string
    {
        if ($stockCount <= 0) {
            return 'out_of_stock';
        }

        if ($stockCount <= $threshold) {
            return 'low_stock';
        }

        return 'in_stock';
    }

    /**
     * Atomically lock and decrement stock for an order line.
     *
     * @return array<int, array{purchase_batch_item_id: int, qty: int}>
     */
    public function deductForLine(Product $product, ?ProductVariant $variant, int $qty): array
    {
        if ($qty <= 0) {
            throw new InsufficientStockException('Quantity must be at least 1.', $product->id, $variant?->id, 0, $qty);
        }

        $lockedProduct = Product::lockForUpdate()->findOrFail($product->id);

        if ($variant) {
            $lockedVariant = ProductVariant::lockForUpdate()->findOrFail($variant->id);

            if ((int) $lockedVariant->stock_count < $qty) {
                throw new InsufficientStockException(
                    "{$lockedProduct->name} ({$lockedVariant->size}) has only {$lockedVariant->stock_count} unit(s) available.",
                    $lockedProduct->id,
                    $lockedVariant->id,
                    (int) $lockedVariant->stock_count,
                    $qty
                );
            }

            $affected = DB::table('product_variants')
                ->where('id', $lockedVariant->id)
                ->where('stock_count', '>=', $qty)
                ->decrement('stock_count', $qty);

            if ($affected === 0) {
                throw new InsufficientStockException(
                    "{$lockedProduct->name} ({$lockedVariant->size}) is out of stock.",
                    $lockedProduct->id,
                    $lockedVariant->id,
                    0,
                    $qty
                );
            }

            $lockedVariant->refresh();
            $lockedVariant->update([
                'stock_status' => $this->resolveStockStatus((int) $lockedVariant->stock_count, (int) $lockedProduct->min_threshold),
            ]);

            $newProductStock = max(0, (int) $lockedProduct->stock_count - $qty);
            $lockedProduct->update([
                'stock_count' => $newProductStock,
                'stock_status' => $this->resolveStockStatus($newProductStock, (int) $lockedProduct->min_threshold),
            ]);
        } else {
            if ((int) $lockedProduct->stock_count < $qty) {
                throw new InsufficientStockException(
                    "{$lockedProduct->name} has only {$lockedProduct->stock_count} unit(s) available.",
                    $lockedProduct->id,
                    null,
                    (int) $lockedProduct->stock_count,
                    $qty
                );
            }

            $affected = DB::table('products')
                ->where('id', $lockedProduct->id)
                ->where('stock_count', '>=', $qty)
                ->decrement('stock_count', $qty);

            if ($affected === 0) {
                throw new InsufficientStockException(
                    "{$lockedProduct->name} is out of stock.",
                    $lockedProduct->id,
                    null,
                    0,
                    $qty
                );
            }

            $lockedProduct->refresh();
            $lockedProduct->update([
                'stock_status' => $this->resolveStockStatus((int) $lockedProduct->stock_count, (int) $lockedProduct->min_threshold),
            ]);
        }

        return $this->purchaseBatchService->deductFromAvailableBatches($lockedProduct->id, $qty);
    }

    /**
     * Record consumed FIFO batch links and stock movement for a created OrderItem.
     *
     * @param  array<int, array{purchase_batch_item_id: int, qty: int}>  $consumedBatches
     */
    public function recordOrderItemConsumption(OrderItem $orderItem, array $consumedBatches): void
    {
        foreach ($consumedBatches as $batch) {
            OrderItemBatch::create([
                'order_item_id' => $orderItem->id,
                'purchase_batch_item_id' => $batch['purchase_batch_item_id'],
                'qty' => $batch['qty'],
            ]);
        }

        if ($orderItem->product_id) {
            $this->logMovement(
                productId: (int) $orderItem->product_id,
                variantId: $orderItem->product_variant_id ? (int) $orderItem->product_variant_id : null,
                delta: -((int) $orderItem->qty),
                reason: 'order_placed',
                referenceType: Order::class,
                referenceId: (int) $orderItem->order_id,
                note: "Order #{$orderItem->order_id} item #{$orderItem->id}",
            );
        }
    }

    /**
     * Restore stock and exact FIFO purchase batch quantities when an order is cancelled.
     */
    public function restoreForCancelledOrder(Order $order, ?int $adminId = null): void
    {
        $order->loadMissing('items.batches');

        foreach ($order->items as $item) {
            $qty = (int) $item->qty;
            if ($qty <= 0 || ! $item->product_id) {
                continue;
            }

            $product = Product::lockForUpdate()->find($item->product_id);
            if ($product) {
                $newStock = (int) $product->stock_count + $qty;
                $product->update([
                    'stock_count' => $newStock,
                    'stock_status' => $this->resolveStockStatus($newStock, (int) $product->min_threshold),
                ]);
            }

            if ($item->product_variant_id) {
                $variant = ProductVariant::lockForUpdate()->find($item->product_variant_id);
                if ($variant) {
                    $newVarStock = (int) $variant->stock_count + $qty;
                    $variant->update([
                        'stock_count' => $newVarStock,
                        'stock_status' => $this->resolveStockStatus($newVarStock, (int) ($product?->min_threshold ?? 10)),
                    ]);
                }
            }

            if ($item->batches->isNotEmpty()) {
                foreach ($item->batches as $consumed) {
                    $batchItem = PurchaseBatchItem::lockForUpdate()->find($consumed->purchase_batch_item_id);
                    if ($batchItem) {
                        $batchItem->increment('remaining_qty', (int) $consumed->qty);
                    }
                }
            } else {
                $batchItem = PurchaseBatchItem::where('product_id', $item->product_id)->latest('id')->lockForUpdate()->first();
                if ($batchItem) {
                    $batchItem->increment('remaining_qty', $qty);
                }
            }

            $this->logMovement(
                productId: (int) $item->product_id,
                variantId: $item->product_variant_id ? (int) $item->product_variant_id : null,
                delta: $qty,
                reason: 'order_cancelled',
                referenceType: Order::class,
                referenceId: (int) $order->id,
                note: "Order {$order->order_number} cancelled",
                adminId: $adminId,
            );
        }

        Cache::forget('storefront_catalog_json');
    }

    public function adjustProductStock(
        Product $product,
        int $newStockCount,
        ?int $minThreshold = null,
        ?string $batchLot = null,
        ?string $note = null,
        ?int $adminId = null
    ): void {
        DB::transaction(function () use ($product, $newStockCount, $minThreshold, $batchLot, $note, $adminId) {
            $locked = Product::lockForUpdate()->findOrFail($product->id);
            $previousStock = (int) $locked->stock_count;
            $threshold = $minThreshold ?? (int) $locked->min_threshold;
            $delta = $newStockCount - $previousStock;

            $locked->update([
                'stock_count' => $newStockCount,
                'min_threshold' => $threshold,
                'stock_status' => $this->resolveStockStatus($newStockCount, $threshold),
                'batch_lot' => $batchLot ?? $locked->batch_lot,
                'last_restocked_at' => now()->toDateString(),
            ]);

            if ($delta !== 0) {
                $this->logMovement(
                    productId: $locked->id,
                    variantId: null,
                    delta: $delta,
                    reason: 'manual_adjustment',
                    referenceType: Product::class,
                    referenceId: $locked->id,
                    note: $note ?: "Manual stock set from {$previousStock} to {$newStockCount}",
                    adminId: $adminId,
                );
            }
        });

        Cache::forget('storefront_catalog_json');
    }

    public function adjustVariantStock(
        ProductVariant $variant,
        int $newStockCount,
        ?string $note = null,
        ?int $adminId = null
    ): void {
        DB::transaction(function () use ($variant, $newStockCount, $note, $adminId) {
            $lockedVariant = ProductVariant::lockForUpdate()->findOrFail($variant->id);
            $lockedProduct = Product::lockForUpdate()->findOrFail($lockedVariant->product_id);
            $previousStock = (int) $lockedVariant->stock_count;
            $delta = $newStockCount - $previousStock;

            $lockedVariant->update([
                'stock_count' => $newStockCount,
                'stock_status' => $this->resolveStockStatus($newStockCount, (int) $lockedProduct->min_threshold),
            ]);

            $totalProductStock = (int) $lockedProduct->productVariants()->sum('stock_count');
            $lockedProduct->update([
                'stock_count' => $totalProductStock,
                'stock_status' => $this->resolveStockStatus($totalProductStock, (int) $lockedProduct->min_threshold),
                'last_restocked_at' => now()->toDateString(),
            ]);

            if ($delta !== 0) {
                $this->logMovement(
                    productId: $lockedProduct->id,
                    variantId: $lockedVariant->id,
                    delta: $delta,
                    reason: 'manual_adjustment',
                    referenceType: ProductVariant::class,
                    referenceId: $lockedVariant->id,
                    note: $note ?: "Variant ({$lockedVariant->size}) stock set from {$previousStock} to {$newStockCount}",
                    adminId: $adminId,
                );
            }
        });

        Cache::forget('storefront_catalog_json');
    }

    public function logMovement(
        int $productId,
        ?int $variantId,
        int $delta,
        string $reason,
        ?string $referenceType = null,
        ?int $referenceId = null,
        ?string $note = null,
        ?int $adminId = null
    ): StockMovement {
        return StockMovement::create([
            'product_id' => $productId,
            'product_variant_id' => $variantId,
            'delta' => $delta,
            'reason' => $reason,
            'reference_type' => $referenceType,
            'reference_id' => $referenceId,
            'note' => $note,
            'admin_id' => $adminId,
            'created_at' => now(),
        ]);
    }
}

