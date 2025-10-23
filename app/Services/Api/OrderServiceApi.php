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

        app(ProductRepository::class)->getVariantInfo(14, 1);
        $user_id = $data['user_id'];
        $product_list = $data['product_list'];
        $coupon_code = $data['coupon_code'] ?? null;
        $note = $data['notes'] ?? null;
        $shipping_address = array_merge($data['shipping_address'],[ 'user_id' => $user_id ]);
        $district_id = $shipping_address['district_id'];
        // Logic to create an order
        $variant_info = [];

        $product_ids = []; //flash sale for

        foreach ($product_list as $product) {
            $product_id = $product['product_id'];
            $variant_id = $product['variant_id'] ?? null;
            $quantity = $product['quantity'];
            if(!empty($product['flash_sale'])){
                $product_ids[$variant_id] = ['product_id'=>$product_id,'variant_id'=>$variant_id,'quantity'=>$quantity,'flash_sale'=>$product['flash_sale']];
            }
            $variant = app(ProductRepository::class)->getVariantInfo($product_id, $variant_id);
            $variant_info[] = $this->listVariants($variant, $quantity);

        }
        return $flashSaleDiscount =  $this->flashSale( $product_ids);
        $order =  $this->order($variant_info, $district_id, $coupon_code,$flashSaleDiscount['total_discounted_price']);
        $order_info = array_merge($order, [
            "order_number" => Order::generateOrderNumber(),
            'user_id' => $user_id,
            'note' => $note,
        ]);

      // return app(OrderRepository::class)->createOrder($order_info, $variant_info, $shipping_address);
    }

    /**
     * Summary of listVariants
     * @param object $variant
     * @param int $quantity
     * @return array{discount_amount: mixed, product_id: mixed, product_variant_id: mixed, quantity: int, total_price: float|int, unit_price: mixed, weight: mixed}
     */
    public function listVariants(object $variant, int $quantity)
    {
        return [
            'product_id' => $variant->product_id,
            'product_variant_id' => $variant->id,
            'quantity' => $quantity,
            'weight' => $variant->weight,
            'unit_price' => $variant->discount_price ?? $variant->sell_price,
            'total_price' => ($variant->discount_price ?? $variant->sell_price) * $quantity,
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
    public function order(array $variant_info, int $district_id, $coupon_code = null,$flashDiscount)
    {
        $sub_total = 0.0;
        $total_weight = 0.0;
        foreach ($variant_info as $variant) {
            $sub_total += (float) $variant['total_price'];
            $total_weight += (float) $variant['weight'] * (int) $variant['quantity'];
        }
        $shipping_charge = $this->shippingCharge($district_id, (float) $total_weight);
        $discount_amount = $this->couponDiscount($coupon_code, $sub_total);
        return [
            'weight' => round($total_weight, 2),
            'subtotal' => round($sub_total, 2),
            'shipping_amount' => $shipping_charge,
            'before_discount' => $sub_total + $shipping_charge,
            'total_amount' => round(($sub_total + $shipping_charge) - ($discount_amount+$flashDiscount), 2),
            'coupon_used' => $discount_amount > 0 ? $coupon_code : null,
            'coupon_discount_amount' => $discount_amount,
            'tax_amount' => 0.0,
            'flash_discount' => $flashDiscount
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
