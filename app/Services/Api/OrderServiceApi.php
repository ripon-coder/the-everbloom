<?php

namespace App\Services\Api;

use App\Constants\ProductStatus;
use App\Models\Order;
use App\Repositories\Contracts\OrderRepository;
use App\Repositories\Contracts\CouponRepository;
use App\Repositories\Contracts\ProductRepository;
use App\Repositories\Contracts\DistrictRepository;
use App\Repositories\Contracts\FlashSaleRepository;
use Illuminate\Support\Facades\DB;
use Exception;

class OrderServiceApi
{
    public function createOrder(array $data)
    {
        return DB::transaction(function () use ($data) {
            $user_id = $data['user_id'];
            $product_list = $data['product_list'];
            $coupon_code = $data['coupon_code'] ?? null;
            $note = $data['notes'] ?? null;
            $shipping_address = array_merge($data['shipping_address'] ?? [], ['user_id' => $user_id]);
            $district_id = $shipping_address['district_id'] ?? null;

            $variant_info = [];
            $product_ids = []; // For flash sale

            // ✅ Fetch all products and variants
            $productAll = $this->ProductAll(array_unique(array_column($product_list, 'product_id')));
            $variant_all = $this->VariantsAll($product_list);

            foreach ($product_list as $product) {
                $product_id = $product['product_id'];
                $variant_id = $product['variant_id'] ?? null;
                $quantity = $product['quantity'] ?? 1;
                $variant = $variant_all[$variant_id] ?? null;

                // 🔹 Product validation
                if (!isset($productAll[$product_id]['status']) || $productAll[$product_id]['status'] == ProductStatus::INACTIVE) {
                    throw new Exception("Product Not Active or Not Found");
                }

                // 🔹 Variant validation
                if (!$variant || $variant['status'] == "inactive") {
                    throw new Exception("Variant Not Found");
                }

                // 🔹 Quantity check
                $this->Quantitycheck($variant, $quantity);

                // 🔹 Flash sale logic
                $isFlashSale = !empty($product['flash_sale'] ?? null);
                if ($isFlashSale) {
                    $product_ids[$variant_id] = [
                        'product_id' => $product_id,
                        'variant_id' => $variant_id,
                        'quantity' => $quantity,
                        'flash_sale' => $product['flash_sale']
                    ];
                }

                // 🔹 Collect variant info
                $variant_info[] = $this->listVariants(
                    $variant,
                    $quantity,
                    $productAll[$product_id]['is_free_delivery'] ?? false,
                    $isFlashSale
                );
            }

            // ✅ Flash sale discount
            $flashSaleDiscount = $this->flashSale($product_ids);

            // ✅ Calculate order totals
            $order = $this->order(
                $variant_info,
                $district_id,
                $coupon_code,
                $flashSaleDiscount['total_discounted_price'] ?? 0
            );

            $order_info = array_merge($order, [
                "order_number" => Order::generateOrderNumber(),
                'user_id' => $user_id,
                'notes' => $note,
            ]);

            // ✅ Create order (Repository)
            return app(OrderRepository::class)->createOrder(
                $order_info,
                $variant_info,
                $shipping_address,
                $flashSaleDiscount['products'] ?? []
            );
        });
    }

    // 🔸 Helper methods ----------------------------------------------------

    public function listVariants(array $variant, int $quantity, $is_free_shipping, bool $isFlashSale)
    {
        $unit_price = $isFlashSale ? $variant['sell_price'] : ($variant['discount_price'] ?? $variant['sell_price']);
        return [
            'product_id' => $variant['product_id'],
            'product_variant_id' => $variant['id'],
            'quantity' => $quantity,
            'weight' => $variant['weight'],
            'unit_price' => $unit_price,
            'total_price' => $unit_price * $quantity,
            'discount_amount' => 0.0,
            'is_free_shipping' => $is_free_shipping,
            "buying_price" => $variant['buying_price'],
        ];
    }

    public function order(array $variant_info, int $district_id, $coupon_code = null, float $flashDiscount = 0.0)
    {
        $sub_total = 0.0;
        $total_weight = 0.0;
        $total_weight_for_shipping_charge = 0.0;
        $total_weight_for_free_delivery = 0.0;
        $totalBuyingPrice = 0.0;

        $products = $this->ProductsAllForDeliverCharge($variant_info); // check free delivery

        foreach ($variant_info as $variant) {
            $weight = (float) ($variant['weight'] ?? 0);
            $quantity = (int) ($variant['quantity'] ?? 1);
            $product_id = $variant['product_id'] ?? null;

            if (!($products[$product_id]['is_free_delivery'] ?? false)) {
                $total_weight_for_shipping_charge += $weight * $quantity;
            } else {
                $total_weight_for_free_delivery += $weight * $quantity;
            }

            $sub_total += (float) ($variant['total_price'] ?? 0);
            $total_weight += $weight * $quantity;

            $totalBuyingPrice += (float) ($variant['buying_price'] ?? 0) * $quantity;
        }

        $shipping_cost = $this->shippingCharge($district_id, $total_weight_for_shipping_charge, $total_weight_for_free_delivery);
        $shipping_charge = $shipping_cost['shipping_amount'];
        $admin_shipping_amount = $shipping_cost['admin_shipping_amount'];

        $discount_amount = $this->couponDiscount($coupon_code, $sub_total);

        $total_amount = round(($sub_total + $shipping_charge) - ($discount_amount + $flashDiscount), 2);

        $profit = $total_amount - ($totalBuyingPrice + $shipping_charge + $admin_shipping_amount);

        return [
            'weight' => round($total_weight, 2),
            'subtotal' => round($sub_total, 2),
            'shipping_amount' => $shipping_charge,
            'admin_shipping_amount' => $admin_shipping_amount,
            'before_discount' => $sub_total + $shipping_charge,
            'total_amount' => $total_amount,
            'coupon_used' => $discount_amount > 0 ? $coupon_code : null,
            'coupon_discount_amount' => $discount_amount,
            'tax_amount' => 0.0,
            'flash_discount_amount' => $flashDiscount,
            'profit' => round($profit, 2)
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

    private function ProductsAllForDeliverCharge($variant_info)
    {
        $productIds = array_column($variant_info, 'product_id');
        $products = app(ProductRepository::class)->getProducts($productIds, ['id', 'is_free_delivery']);
        return collect($products)->keyBy('id')->toArray();
    }

    private function VariantsAll($product_list)
    {
        $variant_ids = array_column($product_list, 'variant_id');
        $variant_all = app(ProductRepository::class)->getVariants($variant_ids, ['product_id', 'id', 'buying_price', 'sell_price', 'weight', 'discount_price', 'stock', 'status']);
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
            throw new Exception("Only {$variant['stock']} items available for product ID {$variant['product_id']} (variant {$variant['id']}).");
        }
    }

    // Order
    public function getOrder($user_id, $current_page, $per_page)
    {
        return app(OrderRepository::class)->getOrder($user_id,$current_page, $per_page);
    }

    public function getOrderDetails($order_id, $user_id)
    {
        return app(OrderRepository::class)->getOrderDetails($order_id, $user_id);
    }
}
