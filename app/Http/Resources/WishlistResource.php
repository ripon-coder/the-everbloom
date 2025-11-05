<?php

namespace App\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class WishlistResource extends JsonResource
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
            'user_id' => $this->user_id,
            'product_id' => $this->product_id,
            'created_at' => $this->created_at,
            'product_name' => $this->product->name,
            'product_slug' => $this->product->slug,
            'product_price' => $this->product->price,
            'product_image' => $this->product->firstImage->getImageUrl()
        ];
    }
}
