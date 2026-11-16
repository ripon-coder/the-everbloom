<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class PurchaseBatch extends Model
{
    protected $fillable = [
        'batch_code',
        'vendor_id',
        'vendor_name',
        'reference_no',
        'purchased_at',
        'total_qty',
        'total_cost',
        'notes',
        'created_by',
    ];

    protected function casts(): array
    {
        return [
            'purchased_at' => 'date',
            'total_qty' => 'integer',
            'total_cost' => 'float',
        ];
    }

    public function vendor(): BelongsTo
    {
        return $this->belongsTo(Vendor::class);
    }

    public function items(): HasMany
    {
        return $this->hasMany(PurchaseBatchItem::class);
    }
}

