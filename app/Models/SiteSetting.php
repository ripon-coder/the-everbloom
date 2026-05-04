<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class SiteSetting extends Model
{
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
}
