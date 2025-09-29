<?php

namespace App\Models;

use App\Constants\FlashSaleStatus;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class FlashSale extends Model
{
    use HasFactory, SoftDeletes;

    /**
     * The attributes that are mass assignable.
     *
     * @var array<int, string>
     */
    protected $fillable = [
        'name',
        'slug',
        'description',
        'start_date',
        'end_date',
        'status',
        'banner_image',
    ];

    /**
     * The attributes that should be cast.
     *
     * @var array<string, string>
     */
    protected $casts = [
        'start_date' => 'datetime',
        'end_date' => 'datetime',
        'status' => FlashSaleStatus::class,
    ];

    /**
     * The attributes that should be mutated to dates.
     *
     * @var array<string, string>
     */
    protected $dates = [
        'start_date',
        'end_date',
    ];

    /**
     * Get the flash sales that are active.
     */
    public function scopeActive($query)
    {
        return $query->where('status', FlashSaleStatus::ACTIVE)
                    ->where('start_date', '<=', now())
                    ->where('end_date', '>=', now());
    }

    /**
     * Check if flash sale is valid for use.
     */
    public function isValid()
    {
        return $this->status === FlashSaleStatus::ACTIVE &&
               $this->start_date <= now() &&
               $this->end_date >= now();
    }

    /**
     * Get the products associated with the flash sale.
     */
    public function products()
    {
        return $this->belongsToMany(Product::class, 'flash_sale_product')
                    ->withPivot('discount_price', 'discount_percentage')
                    ->withTimestamps();
    }
}
