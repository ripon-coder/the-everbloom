<?php

namespace App\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class SingleProductVariantResource extends JsonResource
{
    /**
     * Transform the resource into an array.
     *
     * @return array<string, mixed>
     */
    public function toArray(Request $request): array
    {
        return [
            'id' => $this->id,
            'product_id' => $this->product_id,
            'sku' => $this->sku,
            'buying_price' => $this->buying_price,
            'sell_price' => $this->sell_price,
            "discount_price" => $this->discount_price,
            'discount_amount' => $this->discount_amount,
            'stock' => $this->stock,
            'weight' => $this->weight,
            'status' => $this->status,
            "images" => $this->when($this->images->isNotEmpty(), function () {
                return $this->images->map(fn($image) => $image->getImageUrl());
            }),
            "attributes" => SingleProductAttributeResource::collection($this->whenLoaded("variantAttributes")),
        ];
    }
}
