<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Page extends Model
{
    protected $fillable = [
        'slug',
        'title',
        'content',
        'meta_title',
        'meta_description',
        'is_active',
    ];

    protected function casts(): array
    {
        return [
            'is_active' => 'boolean',
        ];
    }

    /**
     * Allow-list HTML sanitizer to prevent XSS while permitting editorial formatting.
     */
    public function sanitizedContent(): string
    {
        $raw = (string) ($this->content ?? '');
        $allowed = strip_tags($raw, '<p><br><h2><h3><h4><ul><ol><li><strong><em><b><i><a><blockquote>');

        // Strip inline event handlers (onload, onclick, etc.) and javascript: URLs
        $clean = preg_replace('/\son\w+\s*=\s*(?:"[^"]*"|\'[^\']*\'|[^\s>]+)/i', '', $allowed) ?? $allowed;
        $clean = preg_replace('/href\s*=\s*(["\'])\s*javascript:.*?\1/i', 'href="#"', $clean) ?? $clean;

        return $clean;
    }
}

