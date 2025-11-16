<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class ProductReview extends Model
{
    use HasFactory;

    protected $fillable = [
        'product_id',
        'variant_id',
        'order_id',
        'user_id',
        'rating',
        'title',
        'review',
        'images',
        'likes',
        'dislikes',
        'is_verified',
        'is_approved',
        'is_reported',
    ];

    protected $casts = [
        'images' => 'array',
        'is_verified' => 'boolean',
        'is_approved' => 'boolean',
        'is_reported' => 'boolean',
    ];
}
