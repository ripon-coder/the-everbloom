<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class Address extends Model
{
    protected $fillable = [
        'user_id',
        'is_default',
        'first_name',
        'last_name',
        'company',
        'country',
        'district_id',
        'thana_id',
        'area_id',
        'district_name',
        'thana_name',
        'area_name',
        'address_line1',
        'address_line2',
        'postal_code',
        'phone',
    ];

    protected $appends = [
        'city',
        'state',
    ];

    protected function casts(): array
    {
        return [
            'is_default' => 'boolean',
        ];
    }

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    public function district(): BelongsTo
    {
        return $this->belongsTo(District::class);
    }

    public function thana(): BelongsTo
    {
        return $this->belongsTo(Thana::class);
    }

    public function area(): BelongsTo
    {
        return $this->belongsTo(Area::class);
    }

    public function getCityAttribute(): string
    {
        return $this->thana?->name ?? $this->thana_name ?? '';
    }

    public function getStateAttribute(): string
    {
        return $this->district?->name ?? $this->district_name ?? '';
    }
}
