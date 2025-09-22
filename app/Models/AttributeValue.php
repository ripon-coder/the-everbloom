<?php

namespace App\Models;

use App\Constants\AttributeValueStatus;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class AttributeValue extends Model
{
    use HasFactory, SoftDeletes;

    protected $fillable = [
        'attribute_id',
        'value',
        'status'
    ];

    protected $casts = [
        'status' => 'string',
    ];

    /**
     * Get the attribute that owns the value.
     */
    public function attribute()
    {
        return $this->belongsTo(Attribute::class);
    }

    /**
     * Get the variant attributes for this attribute value.
     */
    public function variantAttributes()
    {
        return $this->hasMany(VariantAttribute::class);
    }

    /**
     * Scope a query to only include active values.
     */
    public function scopeActive($query)
    {
        return $query->where('status', AttributeValueStatus::ACTIVE);
    }
}
