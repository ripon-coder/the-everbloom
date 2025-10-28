<?php

namespace App\Services\Api;

use App\Constants\ProductStatus;
use App\Repositories\Contracts\ProductRepository;
use Illuminate\Support\Facades\Log;

class CheckoutServiceApi
{
    private $checkoutRepository;
    public function __construct()
    {
        $this->checkoutRepository;
    }

    public function CheckoutCalculate(array $data)
    {
        $orderServiceApi =  app(OrderServiceApi::class);
        if (isset($data['product_list'])) {
            $product_list = $data['product_list'];
        } else {
            throw new \Exception("Product list not found");
        }
        $coupon_code = $data['coupon_code'] ?? null;
        $district_id = $data['district_id'] ?? null;

        $variant_info = [];
        $product_ids = []; // For flash sale

        // Fetch all products and variants
        $productAll = $this->ProductAll(array_unique(array_column($product_list, 'product_id')));
        $variant_all = $this->VariantsAll($product_list);

        foreach ($product_list as $product) {
            $product_id = $product['product_id'];
            $variant_id = $product['variant_id'] ?? null;
            $quantity = $product['quantity'] ?? 1;
            $variant = $variant_all[$variant_id] ?? null;

            // Product validation
            if (!isset($productAll[$product_id]['status']) || $productAll[$product_id]['status'] == ProductStatus::INACTIVE) {
                throw new \Exception("Product Not Active or Not Found");
            }
            // Variant validation
            if (!$variant || $variant['status'] == "inactive") {
                throw new \Exception("Variant Not Found");
            }

            // Quantity check
            $this->Quantitycheck($variant, $quantity);

            // Flash sale logic
            $isFlashSale = !empty($product['flash_sale'] ?? null);

            if ($isFlashSale) {
                $product_ids[$variant_id] = [
                    'product_id' => $product_id,
                    'variant_id' => $variant_id,
                    'quantity' => $quantity,
                    'flash_sale' => $product['flash_sale']
                ];
            }

            // Push variant info
            $variant_info[] = $orderServiceApi->listVariants(
                $variant,
                $quantity,
                $productAll[$product_id]['is_free_delivery'] ?? false,
                $isFlashSale
            );
        }
        // Flash sale discount
        $flashSaleDiscount = $orderServiceApi->flashSale($product_ids);

        // Create order data
        $order = $orderServiceApi->order(
            $variant_info,
            $district_id,
            $coupon_code,
            $flashSaleDiscount['total_discounted_price'] ?? 0
        );
        return [
            "weight" => $order["weight"],
            "subtotal" => $order['subtotal'],
            "shipping_amount" => $order['shipping_amount'],
            "total_amount" => $order['total_amount'],
            "coupon_used" => $order['coupon_used'] ?? null,
            "coupon_discount_amount" => $order['coupon_discount_amount'] ?? 0,
            "flash_discount_amount" => $flashSaleDiscount['total_discounted_price'] ?? 0,
        ];
    }

    private function ProductAll(array $product_ids)
    {
        $products = app(ProductRepository::class)->getProducts($product_ids, ['id', 'is_free_delivery', 'status']);
        return collect($products)->keyBy("id")->toArray();
    }
    private function VariantsAll($product_list)
    {
        $variant_ids = array_column($product_list, 'variant_id');
        $variant_all = app(ProductRepository::class)->getVariants($variant_ids, ['product_id', 'id', 'buying_price', 'sell_price', 'weight', 'discount_price', 'sell_price', 'stock', 'status']);
        return collect($variant_all)->keyBy("id")->toArray();
    }
    private function Quantitycheck($variant, $quantity)
    {
        if ($quantity > $variant['stock']) {
            throw new \Exception("Only {$variant['stock']} items available for product ID {$variant['product_id']} (variant {$variant['id']}).");
        }
    }
}
