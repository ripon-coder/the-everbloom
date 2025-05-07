<?php

namespace App\Models;

use App\BrandEnum;
use Illuminate\Database\Eloquent\Model;

class Brand extends Model
{
    protected $fillable = ["name","slug","thumbnail", "description", "status"];
    protected $casts = [
        "status" => BrandEnum::class,
    ];
}
