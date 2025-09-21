<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Support\Str;

class Attribute extends Model
{
    use HasFactory, SoftDeletes;

    protected $fillable = [
        'name',
        'description',
        'status'
    ];

    protected $casts = [
        'status' => 'string'
    ];

    /**
     * Get the attribute values for this attribute.
     */
    public function attributeValues()
    {
        return $this->hasMany(AttributeValue::class);
    }

    /**
     * Scope a query to only include active attributes.
     */
    public function scopeActive($query)
    {
        return $query->where('status', 'active');
    }

    /**
     * Scope a query to order by name.
     */
    public function scopeOrdered($query)
    {
        return $query->orderBy('name');
    }

    /**
     * Get the parsed options as an array.
     */
    public function getParsedOptionsAttribute()
    {
        if (empty($this->options)) {
            return [];
        }

        if (is_array($this->options)) {
            return $this->options;
        }

        return json_decode($this->options, true) ?? [];
    }

    /**
     * Check if the attribute has options.
     */
    public function hasOptions()
    {
        return !empty($this->parsed_options);
    }

    /**
     * Get the available attribute types.
     */
    public static function getTypes()
    {
        return [
            'text' => 'Text Input',
            'textarea' => 'Textarea',
            'select' => 'Select Dropdown',
            'checkbox' => 'Checkbox',
            'radio' => 'Radio Button',
            'color' => 'Color Picker',
            'date' => 'Date Picker',
            'number' => 'Number Input',
            'file' => 'File Upload'
        ];
    }

    /**
     * Get the human-readable type name.
     */
    public function getTypeNameAttribute()
    {
        return 'Text Input'; // Default type since we don't have type field
    }


    /**
     * Get the is_required attribute.
     */
    public function getIsRequiredAttribute()
    {
        return false; // Default value since we don't have is_required field
    }

    /**
     * Get the is_active attribute.
     */
    public function getIsActiveAttribute()
    {
        return $this->status === 'active';
    }

    /**
     * Get the sort_order attribute.
     */
    public function getSortOrderAttribute()
    {
        return 0; // Default value since we don't have sort_order field
    }

    /**
     * Get the status as a formatted string.
     */
    public function getStatusFormattedAttribute()
    {
        return ucfirst($this->status);
    }
}
