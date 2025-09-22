<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class VariantAttribute extends Model
{
    use HasFactory;

    protected $fillable = [
        'product_variant_id',
        'attribute_id',
        'attribute_value_id'
    ];

    /**
     * Get the product variant that owns the variant attribute.
     */
    public function productVariant()
    {
        return $this->belongsTo(ProductVariant::class);
    }

    /**
     * Get the attribute that owns the variant attribute.
     */
    public function attribute()
    {
        return $this->belongsTo(Attribute::class);
    }

    /**
     * Get the attribute value that owns the variant attribute.
     */
    public function attributeValue()
    {
        return $this->belongsTo(AttributeValue::class);
    }
}
