<?php

namespace App\Models;

use App\Services\ImageStorageService;
use Illuminate\Database\Eloquent\Model;

class Banner extends Model
{
    protected $fillable = [
        'title',
        'subtitle',
        'image',
        'image_source',
        'link_url',
        'button_text',
        'position',
        'sort_order',
        'is_active',
    ];

    protected function casts(): array
    {
        return [
            'sort_order' => 'integer',
            'is_active' => 'boolean',
        ];
    }

    public function resolvedImage(): string
    {
        if (empty($this->image)) {
            return '';
        }

        $source = $this->image_source ?: 'local';
        if ($source === 'local') {
            return str_starts_with($this->image, '/') || str_starts_with($this->image, 'http')
                ? $this->image
                : asset($this->image);
        }

        return app(ImageStorageService::class)->resolveUrl($this->image, $source);
    }
}

