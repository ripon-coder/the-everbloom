<?php

namespace App\Services\FlashSale;

use App\Models\FlashSale;
use App\Models\FlashSaleProduct;
use App\Models\Order;
use App\Models\OrderItem;
use App\Models\Product;
use App\Models\ProductVariant;
use App\Models\User;
use Carbon\CarbonInterface;
use Illuminate\Support\Facades\DB;
use Illuminate\Validation\ValidationException;

class FlashSaleService
{
    /**
     * Resolve the active FlashSaleProduct for a product/variant if a live sale applies.
     * Authoritative rule (BUSINESS_LOGIC §8): checks timestamps and non-draft/non-disabled status,
     * ignoring stale 'scheduled' cache values.
     */
    public function resolveActiveItem(Product $product, ?ProductVariant $variant = null): ?FlashSaleProduct
    {
        $now = now();

        $query = FlashSaleProduct::query()
            ->with('flashSale')
            ->where('product_id', $product->id)
            ->where('is_active', true)
            ->whereColumn('sold_qty', '<', 'sale_stock')
            ->whereHas('flashSale', function ($q) use ($now) {
                $q->whereNotIn('status', [FlashSale::STATUS_DRAFT, FlashSale::STATUS_DISABLED])
                    ->where('starts_at', '<=', $now)
                    ->where('ends_at', '>', $now);
            });

        if ($variant) {
            $variantItem = (clone $query)
                ->where('product_variant_id', $variant->id)
                ->first();
            if ($variantItem) {
                return $variantItem;
            }
        }

        return $query->whereNull('product_variant_id')->first();
    }

    /**
     * Compute how many units of a FlashSaleProduct a customer has already purchased
     * in non-cancelled orders.
     */
    public function customerPurchasedQty(
        FlashSaleProduct $item,
        ?User $user = null,
        ?string $phone = null,
        ?string $email = null
    ): int {
        $normalizedPhone = $phone ? preg_replace('/\D+/', '', $phone) : '';
        $trimmedEmail = $email ? trim(strtolower($email)) : '';

        if (! $user && $normalizedPhone === '' && $trimmedEmail === '') {
            return 0;
        }

        return (int) OrderItem::query()
            ->where('flash_sale_product_id', $item->id)
            ->whereHas('order', function ($q) use ($user, $normalizedPhone, $trimmedEmail) {
                $q->where('status', '!=', 'Cancelled');
                $q->where(function ($identity) use ($user, $normalizedPhone, $trimmedEmail) {
                    if ($user) {
                        $identity->where('user_id', $user->id);
                    }
                    if ($normalizedPhone !== '') {
                        $identity->orWhere('customer_phone', $normalizedPhone)
                            ->orWhere('customer_phone', 'like', '%' . $normalizedPhone);
                    }
                    if ($trimmedEmail !== '') {
                        $identity->orWhereRaw('LOWER(customer_email) = ?', [$trimmedEmail]);
                    }
                });
            })
            ->sum('qty');
    }

    /**
     * Lock and atomically consume flash-sale stock for an order line inside a DB transaction.
     *
     * @throws ValidationException
     */
    public function consumeForOrderLine(
        FlashSaleProduct $item,
        int $qty,
        ?User $user = null,
        ?string $phone = null,
        ?string $email = null
    ): FlashSaleProduct {
        /** @var FlashSaleProduct|null $locked */
        $locked = FlashSaleProduct::with(['flashSale', 'product'])
            ->lockForUpdate()
            ->find($item->id);

        if (! $locked || ! $locked->is_active || ! $locked->flashSale?->isLiveNow()) {
            throw ValidationException::withMessages([
                'items' => ['A flash sale item in your bag is no longer active. Please review your cart.'],
            ]);
        }

        if ($locked->max_qty_per_customer !== null) {
            $alreadyBought = $this->customerPurchasedQty($locked, $user, $phone, $email);
            $remainingAllowed = max(0, $locked->max_qty_per_customer - $alreadyBought);
            if ($qty > $remainingAllowed) {
                $productName = $locked->product?->name ?? 'this product';
                throw ValidationException::withMessages([
                    'items' => ["Flash sale limit exceeded for {$productName}. You may purchase at most {$locked->max_qty_per_customer} unit(s) (remaining allowance: {$remainingAllowed})."],
                ]);
            }
        }

        $affected = DB::table('flash_sale_products')
            ->where('id', $locked->id)
            ->whereRaw('sold_qty + ? <= sale_stock', [$qty])
            ->update([
                'sold_qty' => DB::raw("sold_qty + {$qty}"),
                'updated_at' => now(),
            ]);

        if ($affected === 0) {
            $remaining = $locked->remainingStock();
            $productName = $locked->product?->name ?? 'this product';
            throw ValidationException::withMessages([
                'items' => ["Flash sale stock exhausted for {$productName}. Only {$remaining} flash unit(s) remain."],
            ]);
        }

        $locked->refresh();

        return $locked;
    }

    /**
     * Per D-04: Restore sold_qty on order cancellation ONLY if the flash sale is still active.
     */
    public function restoreForCancelledOrder(Order $order): void
    {
        $order->loadMissing('items.flashSaleProduct.flashSale');

        foreach ($order->items as $item) {
            if (! $item->flash_sale_product_id) {
                continue;
            }

            $fsp = $item->flashSaleProduct;
            if (! $fsp || ! $fsp->flashSale || ! $fsp->flashSale->isLiveNow()) {
                continue;
            }

            DB::table('flash_sale_products')
                ->where('id', $fsp->id)
                ->update([
                    'sold_qty' => DB::raw('CASE WHEN sold_qty >= ' . (int) $item->qty . ' THEN sold_qty - ' . (int) $item->qty . ' ELSE 0 END'),
                    'updated_at' => now(),
                ]);
        }
    }

    /**
     * Sync cached flash_sales.status values based on current timestamps.
     *
     * @return array{activated: int, expired: int}
     */
    public function syncStatuses(): array
    {
        $now = now();

        $activated = FlashSale::where('status', FlashSale::STATUS_SCHEDULED)
            ->where('starts_at', '<=', $now)
            ->where('ends_at', '>', $now)
            ->update([
                'status' => FlashSale::STATUS_ACTIVE,
                'updated_at' => $now,
            ]);

        $expired = FlashSale::whereIn('status', [FlashSale::STATUS_SCHEDULED, FlashSale::STATUS_ACTIVE])
            ->where('ends_at', '<=', $now)
            ->update([
                'status' => FlashSale::STATUS_EXPIRED,
                'updated_at' => $now,
            ]);

        return [
            'activated' => $activated,
            'expired' => $expired,
        ];
    }

    /**
     * Per D-08: Disallow overlapping scheduled/active flash sales on the same product/variant.
     */
    public function findOverlappingSale(
        int $productId,
        ?int $variantId,
        CarbonInterface $startsAt,
        CarbonInterface $endsAt,
        ?int $ignoreFlashSaleId = null
    ): ?FlashSale {
        $conflict = FlashSaleProduct::query()
            ->with('flashSale')
            ->where('product_id', $productId)
            ->when(
                $variantId !== null,
                fn ($q) => $q->where(fn ($sub) => $sub->where('product_variant_id', $variantId)->orWhereNull('product_variant_id')),
                fn ($q) => $q->whereNull('product_variant_id')
            )
            ->whereHas('flashSale', function ($q) use ($startsAt, $endsAt, $ignoreFlashSaleId) {
                if ($ignoreFlashSaleId !== null) {
                    $q->where('id', '!=', $ignoreFlashSaleId);
                }
                $q->whereNotIn('status', [FlashSale::STATUS_DRAFT, FlashSale::STATUS_DISABLED, FlashSale::STATUS_EXPIRED])
                    ->where('starts_at', '<', $endsAt)
                    ->where('ends_at', '>', $startsAt);
            })
            ->first();

        return $conflict?->flashSale;
    }
}

