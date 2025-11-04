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
        return [
            "id" => $this->id,
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
            "variants" => SingleProductVariantResource::collection($this->whenLoaded('variants')),
        ];
    }
}
