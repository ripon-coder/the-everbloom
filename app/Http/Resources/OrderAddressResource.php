<?php

namespace App\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class OrderAddressResource extends JsonResource
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
            "address" => $this->address,
            "zone" => $this->zone,
            "phone_number" => $this->phone_number,
            "district_name" => $this->district->name,
            "district_id" => $this->district_id,
            "created_at" => $this->created_at,  
        ];
    }
}
