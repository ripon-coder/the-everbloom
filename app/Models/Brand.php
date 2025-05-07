<?php

namespace App\Models;

use App\BrandEnum;
use Carbon\Carbon;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Casts\Attribute;

class Brand extends Model
{
    protected $fillable = ["name", "slug", "thumbnail", "description", "status"];
    protected $casts = [
        "status" => BrandEnum::class,
    ];
    protected function createdAt(): Attribute
    {
        return Attribute::make(get: fn($value) => Carbon::parse($value)->format('d-m-Y'));
    }
}
