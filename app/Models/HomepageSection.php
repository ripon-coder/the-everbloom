<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class HomepageSection extends Model
{
    protected $fillable = [
        'key',
        'title',
        'subtitle',
        'payload',
        'sort_order',
        'is_active',
    ];

    protected function casts(): array
    {
        return [
            'payload' => 'array',
            'sort_order' => 'integer',
            'is_active' => 'boolean',
        ];
    }
}

