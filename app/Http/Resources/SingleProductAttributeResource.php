<?php

namespace App\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class SingleProductAttributeResource extends JsonResource
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
            "product_variant_id" => $this->product_variant_id,
            "attribute_id" => $this->attribute_id,
            "attribute_value_id" => $this->attribute_value_id,
            "attribute_name" => optional($this->attribute)->name,
            "is_image" => optional($this->attribute)->is_image,
            "attribute_value" => optional($this->attributeValue)->value
        ];
    }
}
