<?php

namespace App\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class OrderProductResource extends JsonResource
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
            "order_id" => $this->order_id,
            "product_id" => $this->product_id,
            "product_name" => $this->when($this->product, $this->product->name),
            "product_variant_id" => $this->product_variant_id,
            "quantity" => $this->quantity,
            "unit_price" => $this->unit_price,
            "total_price" => $this->total_price,
            "discount_amount" => $this->discount_amount,
            "is_free_shipping" => $this->is_free_shipping,
            "notes" => $this->notes,
            "image" => optional($this->when($this->product, $this->product->firstImage))->getImageUrl(),
            "productVariant" => new SingleProductVariantResource($this->whenLoaded("productVariant")),
        ];
    }
}
