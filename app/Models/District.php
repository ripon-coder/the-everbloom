<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;

class District extends Model
{
    protected $fillable = [
        'name',
        'bn_name',
        'slug',
        'delivery_charge',
        'sort_order',
        'is_active',
    ];

    protected function casts(): array
    {
        return [
            'delivery_charge' => 'float',
            'sort_order' => 'integer',
            'is_active' => 'boolean',
        ];
    }

    public function scopeActive(Builder $query): Builder
    {
        return $query->where('is_active', true)->orderBy('sort_order')->orderBy('name');
    }

    public function thanas(): HasMany
    {
        return $this->hasMany(Thana::class)->orderBy('sort_order')->orderBy('name');
    }

    public function areas(): HasMany
    {
        return $this->hasMany(Area::class)->orderBy('sort_order')->orderBy('name');
    }
}

