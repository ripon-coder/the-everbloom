<?php

namespace App\Services\Api;

use App\Models\Order;
use App\Repositories\Contracts\OrderRepository;
use App\Repositories\Contracts\CouponRepository;
use App\Repositories\Contracts\ProductRepository;
use App\Repositories\Contracts\DistrictRepository;
use App\Repositories\Contracts\FlashSaleRepository;

class OrderServiceApi
{
    public function createOrder(array $data)
    {
        $user_id = $data['user_id'];
        $product_list = $data['product_list'];
        $coupon_code = $data['coupon_code'] ?? null;
        $note = $data['notes'] ?? null;
        $shipping_address = array_merge($data['shipping_address'], ['user_id' => $user_id]);
        $district_id = $shipping_address['district_id'];
        // Logic to create an order
        $variant_info = [];
        $product_ids = []; //flash sale for
        $variant_all = $this->VarinatsAll($product_list);
        foreach ($product_list as $product) {
            $product_id = $product['product_id'];
            $variant_id = $product['variant_id'] ?? null;
            $quantity = $product['quantity'];
            if (!empty($product['flash_sale'])) {
                $product_ids[$variant_id] = ['product_id' => $product_id, 'variant_id' => $variant_id, 'quantity' => $quantity, 'flash_sale' => $product['flash_sale']];
            }
            $variant = $variant_all[$variant_id];
            $variant_info[] = $this->listVariants($variant, $quantity);
        }
        $flashSaleDiscount =  $this->flashSale($product_ids);
        $order =  $this->order($variant_info, $district_id, $coupon_code, $flashSaleDiscount['total_discounted_price']);
        $order_info = array_merge($order, [
            "order_number" => Order::generateOrderNumber(),
            'user_id' => $user_id,
            'notes' => $note,
        ]);

        return app(OrderRepository::class)->createOrder($order_info, $variant_info, $shipping_address);
    }

    /**
     * Summary of listVariants
     * @param object $variant
     * @param int $quantity
     * @return array{discount_amount: mixed, product_id: mixed, product_variant_id: mixed, quantity: int, total_price: float|int, unit_price: mixed, weight: mixed}
     */
    public function listVariants(array $variant, int $quantity)
    {
        return [
            'product_id' => $variant['product_id'],
            'product_variant_id' => $variant['id'],
            'quantity' => $quantity,
            'weight' => $variant['weight'],
            'unit_price' => $variant['discount_price'] ?? $variant['sell_price'],
            'total_price' => ($variant['discount_price'] ?? $variant['sell_price']) * $quantity,
            'discount_amount' => 0.0,
        ];
    }
    /**
     * Summary of order
     * @param array $variant_info
     * @param int $district_id
     * @param string $coupon_code
     * @return array{discount_amount: float, shipping_amount: mixed, sub_total: float, total_amount: float, total_weight: float, usage_coupon: null}
     */
    public function order(array $variant_info, int $district_id, $coupon_code = null, $flashDiscount)
    {

        $sub_total = 0.0;
        $total_weight = 0.0;
        $total_weight_for_shipping_charge = 0.0;
        $products = $this->ProductsAllForDeliverCharge($variant_info); // for check deliver charge free or not
        foreach ($variant_info as $variant) {
            if (!$products[$variant['product_id']]['is_free_delivery']) {
                $total_weight_for_shipping_charge += (float) $variant['weight'] * (int) $variant['quantity'];
            }
            $sub_total += (float) $variant['total_price'];
            $total_weight += (float) $variant['weight'] * (int) $variant['quantity'];
        }
        $shipping_charge = $this->shippingCharge($district_id, (float) $total_weight_for_shipping_charge);
        $discount_amount = $this->couponDiscount($coupon_code, $sub_total);
        return [
            'weight' => round($total_weight, 2),
            'subtotal' => round($sub_total, 2),
            'shipping_amount' => $shipping_charge,
            'before_discount' => $sub_total + $shipping_charge,
            'total_amount' => round(floatval(($sub_total + $shipping_charge) - ($discount_amount + $flashDiscount)), 2),
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

    public function shippingCharge(int $district_id, float $totalWeight)
    {
        return app(DistrictRepository::class)->getShippingCharge($district_id, $totalWeight);
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
        $variant_ids =  array_column($product_list, 'variant_id');
        $variant_all =  app(ProductRepository::class)->getVariants($variant_ids, ['product_id', 'id', 'weight', 'discount_price', 'sell_price']);
        return collect($variant_all)->keyBy("id")->toArray();
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
