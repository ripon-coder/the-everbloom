<?php
namespace App\Repositories\Eloquent;

use App\Models\FlashSale;
use App\Repositories\Contracts\ProductRepository;
use App\Repositories\Contracts\FlashSaleRepository;

class FlashSaleEloquent implements FlashSaleRepository
{
    public function index()
    {
        return FlashSale::select('id', 'name', 'slug', 'start_date', 'end_date', 'status', 'created_at')
            ->latest()
            ->paginate(15);
    }

    public function create()
    {
        $data['status_options'] = \App\Constants\FlashSaleStatus::getOptions();
        $data['products'] = \App\Models\Product::active()->get();
        return $data;
    }

    public function store(array $data)
    {
        $flashSale = FlashSale::create($data);

        // Sync products if provided
        if (isset($data['products'])) {
            $syncData = [];
            foreach ($data['products'] as $productId) {
                $syncData[$productId] = [
                    'discount_price' => $data['discount_price'] ?? null,
                    'discount_percentage' => $data['discount_percentage'] ?? null,
                ];
            }
            $flashSale->products()->sync($syncData);
        }

        return $flashSale;
    }

    public function edit(int $id)
    {
        $data['flashSale'] = FlashSale::findOrFail($id);
        $data['status_options'] = \App\Constants\FlashSaleStatus::getOptions();
        $data['products'] = \App\Models\Product::active()->get();
        return $data;
    }

    public function update(int $id, array $data)
    {
        $flashSale = FlashSale::findOrFail($id);
        $flashSale->update($data);

        // Sync products if provided
        if (isset($data['products'])) {
            $syncData = [];
            foreach ($data['products'] as $productId) {
                $syncData[$productId] = [
                    'discount_price' => $data['discount_price'] ?? null,
                    'discount_percentage' => $data['discount_percentage'] ?? null,
                ];
            }
            $flashSale->products()->sync($syncData);
        } else {
            // Remove all products if none selected
            $flashSale->products()->detach();
        }

        return $flashSale;
    }

    public function destroy(int $id)
    {
        $flashSale = FlashSale::findOrFail($id);
        return $flashSale->delete();
    }

    public function restore(int $id)
    {
        $flashSale = FlashSale::withTrashed()->findOrFail($id);
        return $flashSale->restore();
    }

    public function forceDelete(int $id)
    {
        $flashSale = FlashSale::withTrashed()->findOrFail($id);
        return $flashSale->forceDelete();
    }

    public function getFlashSaleDiscounts(array $productIds)
    {
        if (empty($productIds)) {
            return [
                'products' => [],
                'total_discounted_price' => 0,
            ];
        }

        $collection = collect($productIds);
        $groupedByFlashSale = $collection->groupBy('flash_sale');

        $allDiscountResults = [];
        $totalDiscountedPrice = 0;

        foreach ($groupedByFlashSale as $flashSaleCode => $items) {
            $flashSale = FlashSale::where('slug', $flashSaleCode)
                ->where('status', \App\Constants\FlashSaleStatus::ACTIVE)
                ->where('start_date', '<=', now())
                ->where('end_date', '>=', now())
                ->first();

            if (!$flashSale) {
                continue;
            }

            $productIdList = $items->pluck('product_id')->unique();

            $flashProducts = $flashSale->products()
                ->whereIn('products.id', $productIdList)
                ->get()
                ->keyBy('id');

            $discounts = [];

            foreach ($items as $item) {
                $productId = $item['product_id'];
                $variantId = $item['variant_id'];
                $quantity = (int) $item['quantity'];

                $product = $flashProducts->firstWhere('id', $productId);
                if (!$product)
                    continue;

                $discounts[$variantId] = [
                    'product_id' => $productId,
                    'variant_id' => $variantId,
                    'quantity' => $quantity,
                    'discount_price' => $product->pivot->discount_price ?? 0,
                    'discount_percentage' => $product->pivot->discount_percentage ?? 0,
                ];
            }

            if (empty($discounts)) {
                continue;
            }
            $result = $this->getFlashSaleDiscountsAmounts($discounts);
            $allDiscountResults = array_merge($allDiscountResults, $result['products']);
            $totalDiscountedPrice += $result['total_discounted_price'];
        }

        return [
            'products' => $allDiscountResults,
            'total_discounted_price' => $totalDiscountedPrice,
        ];
    }


    /**
     * Summary of getFlashSaleDiscountsAmounts
     * @param array $discounts
     * @return array
     */
    public function getFlashSaleDiscountsAmounts(array $discounts)
    {
        $results = [];
        $totalDiscountedPrice = 0;

        foreach ($discounts as $discount) {
            $product_id = $discount['product_id'];
            $variant_id = $discount['variant_id'];
            $quantity = $discount['quantity'] ?? 1;
            $discount_price = $discount['discount_price'];
            $discount_percentage = $discount['discount_percentage'];
            $variant_info = app(ProductRepository::class)->getVariantInfo($product_id, $variant_id);
            $original_price = $variant_info->sell_price ?? 0;

            if ($discount_price > 0 && $discount_price < $original_price) {
                $discounted_price = $discount_price;
                $final_discount = $original_price - $discounted_price;
                $discount_type = 'fixed';
            } elseif ($discount_percentage > 0) {
                $final_discount = ($original_price * $discount_percentage) / 100;
                $discounted_price = $final_discount;
                $discount_type = 'percentage';
            } else {
                $final_discount = 0;
                $discounted_price = $original_price;
                $discount_type = 'none';
            }


            $results[$variant_id] = [
                'product_id' => $product_id,
                'variant_id' => $variant_id,
                'original_price' => $original_price,
                'discount_amount' => $final_discount,
                'discounted_price' => $discounted_price,
                'discount_type' => $discount_type,
                'quantity' => $quantity,
                'total_discounted_price' => $discounted_price * $quantity,
            ];

            $totalDiscountedPrice += $discounted_price * $quantity;
        }

        return [
            'products' => $results,
            'total_discounted_price' => $totalDiscountedPrice,
        ];
    }




}
