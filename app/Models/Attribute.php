<?php

namespace App\Models;

use App\Constants\AttributeStatus;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class Attribute extends Model
{
    use HasFactory, SoftDeletes;

    protected $fillable = [
        'name',
        'description',
        'type',
        'options',
        'status'
    ];

    protected $casts = [
        'status' => 'string',
        'options' => 'array'
    ];

    /**
     * Get the attribute values for this attribute.
     */
    public function attributeValues()
    {
        return $this->hasMany(AttributeValue::class);
    }

    /**
     * Get the variant attributes for this attribute.
     */
    public function variantAttributes()
    {
        return $this->hasMany(VariantAttribute::class);
    }

    /**
     * Scope a query to only include active attributes.
     */
    public function scopeActive($query)
    {
        return $query->where('status', AttributeStatus::ACTIVE);
    }

    /**
     * Scope a query to order by creation date.
     */
    public function scopeOrdered($query)
    {
        return $query->orderBy('created_at', 'desc');
    }

    /**
     * Get the type name as a formatted string.
     */
    public function getTypeNameAttribute()
    {
        return ucfirst(str_replace('_', ' ', $this->type));
    }
}
