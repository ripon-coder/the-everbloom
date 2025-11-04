<?php

namespace App\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class OrderDetailsResource extends JsonResource
{
    /**
     * Transform the resource into an array.
     *
     * @return array<string, mixed>
     */
    public function toArray(Request $request): array
    {
        return [
            "id" => $this->id,
            "user_id" => $this->user_id,
            "order_number" => $this->order_number,
            "status" => $this->status,
            "payment_status" => $this->payment_status,
            "subtotal" => $this->subtotal,
            "coupon_discount_amount" => $this->coupon_discount_amount,
            "flash_discount_amount" => $this->flash_discount_amount,
            "shipping_amount" => $this->shipping_amount,
            "total_amount" => $this->total_amount,
            "coupon_used" => $this->coupon_used,
            "notes" => $this->notes,
            "created_at" => $this->created_at,
            "orderAddress" => $this->when($this->orderAddress, function () {
                return OrderAddressResource::make($this->orderAddress);
            }),

            "orderProducts" => $this->when($this->orderProducts, function () {
                return OrderProductResource::collection($this->orderProducts);
            }),
        ];
    }
}
