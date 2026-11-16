<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class Review extends Model
{
    protected $fillable = [
        'product_id',
        'user_id',
        'author',
        'author_email',
        'location',
        'variant',
        'rating',
        'headline',
        'body',
        'verified',
        'is_featured_testimonial',
        'status',
    ];

    protected $appends = [
        'date',
        'date_iso',
        'label',
        'quote',
        'product_name',
    ];

    protected function casts(): array
    {
        return [
            'rating' => 'integer',
            'verified' => 'boolean',
            'is_featured_testimonial' => 'boolean',
        ];
    }

    public function product(): BelongsTo
    {
        return $this->belongsTo(Product::class);
    }

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    public function getDateAttribute(): string
    {
        return $this->created_at?->format('F d, Y') ?? '';
    }

    public function getDateIsoAttribute(): string
    {
        return $this->created_at?->format('Y-m-d') ?? '';
    }

    public function getLabelAttribute(): string
    {
        return $this->verified ? 'Verified buyer' : 'Customer review';
    }

    public function getQuoteAttribute(): string
    {
        return $this->body;
    }

    public function getProductNameAttribute(): string
    {
        return $this->product?->name ?? '';
    }
}
