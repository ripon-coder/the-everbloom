<?php

namespace App\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class SingleProductResource extends JsonResource
{
    /**
     * Transform the resource into an array.
     *
     * @return array<string, mixed>
     */
    public function toArray(Request $request): array
    {
        $hasFlashSale = $this->relationLoaded('flashSales') && $this->flashSales->isNotEmpty();
        return [
            "id" => $this->id,
            "category_id" => $this->category_id,
            "brand_id" => $this->brand_id,
            "name" => $this->name,
            "description" => $this->description,
            "short_description" => $this->short_description,
            "is_free_delivery" => $this->is_free_delivery,
            "price" => $this->price,
            "slug" => $this->slug,
            "image" => optional($this->firstImage)->getImageUrl(),
            "images" => $this->when($this->images->isNotEmpty(), function () {
                return $this->images->map(fn($image) => $image->getImageUrl());
            }),
            "is_wishlisted" => $this->is_wishlisted,
            "variants" => SingleProductVariantResource::collection($this->whenLoaded('variants')),
            "flash_sale_name" => $this->when($hasFlashSale, fn() => $this->flashSales->first()->name),
            "flash_sale_slug" => $this->when($hasFlashSale, fn() => $this->flashSales->first()->slug),
            "flash_sale_start_date" => $this->when($hasFlashSale, fn() => $this->flashSales->first()->start_date),
            "flash_sale_end_date" => $this->when($hasFlashSale, fn() => $this->flashSales->first()->end_date),

        ];
    }
}
