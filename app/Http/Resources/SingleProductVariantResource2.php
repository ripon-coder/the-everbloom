<?php

namespace App\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class SingleProductVariantResource2 extends JsonResource
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
            //'buying_price' => $this->buying_price,
            //'sell_price' => $this->sell_price,
            'discount_price' => $this->has_flash_sale ? number_format($this->sell_price, 2) : number_format($this->discount_price, 2),
            'discount_price_cart' =>$this->discount_price,
            //'discount_amount' => $this->discount_amount,
            'stock' => $this->stock,
            'weight' => $this->weight,
            'status' => $this->status,
            'has_flash_sale' => $this->has_flash_sale ?? false,
            'images' => $this->when($this->images->isNotEmpty(), fn() => $this->images->map(fn($img) => $img->getImageUrl())),
            'attributes' => SingleProductAttributeResource::collection($this->whenLoaded('variantAttributes')),
        ];
    }
}
