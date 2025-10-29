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
            "price" => $this->price,
            "slug" => $this->slug,
            "images" => $this->when($this->images->isNotEmpty(), function () {
                return $this->images->map(fn($image) => $image->getImageUrl());
            }),
            "variants" => SingleProductVariantResource::collection($this->whenLoaded('variants')),
        ];
    }
}
