<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class FalshSaleTracker extends Model
{
    use HasFactory;

    protected $fillable = [
        "order_id",
        "product_id",
        "product_variant_id",
        'flash_sale_slug',
        "original_price",
        "discount_amount",
        "discounted_price",
        "discount_type",
        "quantity",
        "total_discounted_price",
    ];
}
