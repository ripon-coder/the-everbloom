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
        $sellPrice = (float) $this->sell_price;
        $finalPrice = (float) $this->discount_price; // default দাম

        if ($this->product->relationLoaded('flashSales') && $this->product->flashSales->isNotEmpty()) {
            $flashSale = $this->product->flashSales->first();
            $pivot = $flashSale->pivot;

            if (!empty($pivot->discount_price)) {
                $finalPrice = (float) $pivot->discount_price;
            } elseif (!empty($pivot->discount_percentage)) {
                $finalPrice = $sellPrice - ($sellPrice * ($pivot->discount_percentage / 100));
            } else {
                $finalPrice = $this->discount_price ?: $sellPrice;
            }
        } else {
            $finalPrice = $this->discount_price ?: $sellPrice;
        }

        return [
            'id' => $this->id,
            'product_id' => $this->product_id,
            'sku' => $this->sku,
            'buying_price' => $this->buying_price,
            'sell_price' => $this->sell_price,
            'discount_price' => number_format($finalPrice, 2),
            'discount_amount' => $this->discount_amount,
            'stock' => $this->stock,
            'weight' => $this->weight,
            'status' => $this->status,
            'has_flash_sale' => $this->has_flash_sale ?? false,
            'images' => $this->when($this->images->isNotEmpty(), fn() => $this->images->map(fn($img) => $img->getImageUrl())),
            'attributes' => SingleProductAttributeResource::collection($this->whenLoaded('variantAttributes')),
        ];
    }
}
