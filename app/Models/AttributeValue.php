<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class AttributeValue extends Model
{
    use HasFactory, SoftDeletes;

    protected $fillable = [
        'attribute_id',
        'value',
        'additional_price',
        'status'
    ];

    protected $casts = [
        'additional_price' => 'decimal:2',
        'status' => 'string'
    ];

    /**
     * Get the attribute that owns the value.
     */
    public function attribute()
    {
        return $this->belongsTo(Attribute::class);
    }

    /**
     * Scope a query to only include values for a specific attribute.
     */
    public function scopeForAttribute($query, $attributeId)
    {
        return $query->where('attribute_id', $attributeId);
    }

    /**
     * Scope a query to only include active values.
     */
    public function scopeActive($query)
    {
        return $query->where('status', 'active');
    }

    /**
     * Get the status as a formatted string.
     */
    public function getStatusFormattedAttribute()
    {
        return ucfirst($this->status);
    }

    /**
     * Get the display value.
     */
    public function getDisplayValueAttribute()
    {
        return $this->value;
    }


    /**
     * Check if the value is valid.
     */
    public function isValid()
    {
        // Basic validation - value should not be empty
        return !empty($this->value);
    }
}
