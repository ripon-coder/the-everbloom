<?php

namespace App\Models;

use App\Trait\HasImage;
use Illuminate\Database\Eloquent\Model;

class SiteSetting extends Model
{
    use HasImage;

    protected $imageColumns = ['site_logo', 'site_favicon'];

    protected $imageDisk = 'public';

    protected $fillable = [
        'site_name',
        'site_logo',
        'site_favicon',
        'site_email',
        'site_phone',
        'site_address',
        'facebook_url',
        'twitter_url',
        'instagram_url',
        'linkedin_url',
        'youtube_url',
        'footer_text',
        'meta_title',
        'meta_description',
        'meta_keywords',
    ];

    public function getLogoUrl(): string
    {
        return $this->traitGetImageUrl('site_logo', 'public');
    }

    public function getFaviconUrl(): string
    {
        return $this->traitGetImageUrl('site_favicon', 'public');
    }
}
