<?php

namespace App\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class FlashSaleProductsResource extends JsonResource
{
    /**
     * Transform the resource into an array.
     *
     * @return array<string, mixed>
     */
    public function toArray(Request $request): array
    {
        $price = $this->price;
        $flashSale = $this->flashSales->first();

        $discount_price = $flashSale?->pivot->discount_price;
        $discount_percentage = $flashSale?->pivot->discount_percentage;

        $finalPrice = $discount_price
            ? $discount_price
            : ($discount_percentage ? $price - ($price * $discount_percentage / 100) : $price);

        return [
            "id" => $this->id,
            "name" => $this->name,
            "slug" => $this->slug,
            "image" => optional($this->firstImage)->getImageUrl(),
            "flash_sale_discount_price" => $discount_price,
            "flash_sale_percentage" => $discount_percentage,
            "price" => round($finalPrice, 2),
            "original_price" => $price,
        ];
    }
}
