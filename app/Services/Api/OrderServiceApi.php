<?php

namespace App\Services\Api;

use App\Constants\ProductStatus;
use App\Models\Order;
use App\Repositories\Contracts\OrderRepository;
use App\Repositories\Contracts\CouponRepository;
use App\Repositories\Contracts\ProductRepository;
use App\Repositories\Contracts\DistrictRepository;
use App\Repositories\Contracts\FlashSaleRepository;
use Exception;

class OrderServiceApi
{
    public function createOrder(array $data)
    {
        $user_id = $data['user_id'];
        $product_list = $data['product_list'];
        $coupon_code = $data['coupon_code'] ?? null;
        $note = $data['notes'] ?? null;
        $shipping_address = array_merge($data['shipping_address'] ?? [], ['user_id' => $user_id]);
        $district_id = $shipping_address['district_id'] ?? null;

        $variant_info = [];
        $product_ids = []; // For flash sale

        // Fetch all products and variants
        $productAll = $this->ProductAll(array_unique(array_column($product_list, 'product_id')));
        $variant_all = $this->VarinatsAll($product_list);

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
            if (!$variant) {
                throw new \Exception("Variant Not Found");
            }

            // Quantity check
            $this->Quantitycheck($variant, $quantity);

            // Flash sale logic
            if (!empty($product['flash_sale'])) {
                $product_ids[$variant_id] = [
                    'product_id' => $product_id,
                    'variant_id' => $variant_id,
                    'quantity' => $quantity,
                    'flash_sale' => $product['flash_sale']
                ];
            }

            // Push variant info
            $variant_info[] = $this->listVariants(
                $variant,
                $quantity,
                $productAll[$product_id]['is_free_delivery'] ?? false
            );
        }

        // Flash sale discount
        $flashSaleDiscount = $this->flashSale($product_ids);

        // Create order data
        $order = $this->order(
            $variant_info,
            $district_id,
            $coupon_code,
            $flashSaleDiscount['total_discounted_price'] ?? 0
        );
        return $order;
        $order_info = array_merge($order, [
            "order_number" => Order::generateOrderNumber(),
            'user_id' => $user_id,
            'notes' => $note,
        ]);

        return app(OrderRepository::class)->createOrder(
            $order_info,
            $variant_info,
            $shipping_address,
            $flashSaleDiscount['products'] ?? []
        );
    }


    /**
     * Summary of listVariants
     * @param object $variant
     * @param int $quantity
     * @return array{discount_amount: mixed, product_id: mixed, product_variant_id: mixed, quantity: int, total_price: float|int, unit_price: mixed, weight: mixed}
     */
    public function listVariants(array $variant, int $quantity, $is_free_shipping)
    {
        return [
            'product_id' => $variant['product_id'],
            'product_variant_id' => $variant['id'],
            'quantity' => $quantity,
            'weight' => $variant['weight'],
            'unit_price' => $variant['discount_price'] ?? $variant['sell_price'],
            'total_price' => ($variant['discount_price'] ?? $variant['sell_price']) * $quantity,
            'discount_amount' => 0.0,
            'is_free_shipping' => $is_free_shipping,
            "buying_price" => $variant['buying_price'],
        ];
    }
    /**
     * Summary of order
     * @param array $variant_info
     * @param int $district_id
     * @param string $coupon_code
     * @return array{discount_amount: float, shipping_amount: mixed, sub_total: float, total_amount: float, total_weight: float, usage_coupon: null}
     */
    public function order(array $variant_info, int $district_id, $coupon_code = null, float $flashDiscount = 0.0)
    {
        $sub_total = 0.0;
        $total_weight = 0.0;
        $total_weight_for_shipping_charge = 0.0;
        $total_weight_for_free_delivery = 0.0;

        $products = $this->ProductsAllForDeliverCharge($variant_info); // check free delivery

        foreach ($variant_info as $variant) {
            $weight = (float) ($variant['weight'] ?? 0);
            $quantity = (int) ($variant['quantity'] ?? 1);
            $product_id = $variant['product_id'] ?? null;

            if (!($products[$product_id]['is_free_delivery'])) {
                $total_weight_for_shipping_charge += $weight * $quantity;
            } else {
                $total_weight_for_free_delivery += $weight * $quantity;
            }

            $sub_total += (float) ($variant['total_price'] ?? 0);
            $total_weight += $weight * $quantity;
        }
        return $shipping_charge = $this->shippingCharge($district_id, $total_weight_for_shipping_charge, $total_weight_for_free_delivery);
        $discount_amount = $this->couponDiscount($coupon_code, $sub_total);

        return [
            'weight' => round($total_weight, 2),
            'subtotal' => round($sub_total, 2),
            'shipping_amount' => $shipping_charge,
            'before_discount' => $sub_total + $shipping_charge,
            'total_amount' => round(($sub_total + $shipping_charge) - ($discount_amount + $flashDiscount), 2),
            'coupon_used' => $discount_amount > 0 ? $coupon_code : null,
            'coupon_discount_amount' => $discount_amount,
            'tax_amount' => 0.0,
            'flash_discount_amount' => $flashDiscount
        ];
    }


    public function couponDiscount($coupon_code, float $sub_total)
    {
        return app(CouponRepository::class)->getDiscountAmount($coupon_code, $sub_total);
    }

    public function shippingCharge(int $district_id, float $shippingWeight, $shippingFreeWeight)
    {
        return app(DistrictRepository::class)->getShippingCharge($district_id, $shippingWeight, $shippingFreeWeight);
    }

    public function flashSale(array $productIds)
    {
        return app(FlashSaleRepository::class)->getFlashSaleDiscounts($productIds);
    }


    // Helper

    private function ProductsAllForDeliverCharge($variant_info)
    {
        $productIds = array_column($variant_info, 'product_id');
        $products = app(ProductRepository::class)->getProducts($productIds, ['id', 'is_free_delivery']);
        return $products = collect($products)->keyBy('id')->toArray();
    }

    private function VarinatsAll($product_list)
    {
        $variant_ids = array_column($product_list, 'variant_id');
        $variant_all = app(ProductRepository::class)->getVariants($variant_ids, ['product_id', 'id', 'buying_price', 'weight', 'discount_price', 'sell_price', 'stock']);
        return collect($variant_all)->keyBy("id")->toArray();
    }

    private function ProductAll(array $product_ids)
    {
        $products = app(ProductRepository::class)->getProducts($product_ids, ['id', 'is_free_delivery', 'status']);
        return collect($products)->keyBy("id")->toArray();
    }

    private function Quantitycheck($variant, $quantity)
    {
        if ($quantity > $variant['stock']) {
            throw new \Exception("Only {$variant['stock']} items available for product ID {$variant['product_id']} (variant {$variant['id']}).");
        }
    }

    // **************************************************
    public function getOrder(int $id)
    {
        // Logic to retrieve an order by ID
    }

    public function updateOrder(int $id, array $data)
    {
        // Logic to update an order
    }

    public function deleteOrder(int $id)
    {
        // Logic to delete an order
    }
}
