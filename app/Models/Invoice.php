<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class Invoice extends Model
{
    protected $fillable = [
        'order_id',
        'invoice_no',
        'customer_name',
        'customer_email',
        'amount',
        'status',
        'payment_method',
        'issued_at',
    ];

    protected function casts(): array
    {
        return [
            'amount' => 'float',
            'issued_at' => 'datetime',
        ];
    }

    public function order(): BelongsTo
    {
        return $this->belongsTo(Order::class);
    }
}
