<?php

namespace App\Models;

use App\Trait\HasImage;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use Spatie\MediaLibrary\HasMedia;
use Spatie\MediaLibrary\InteractsWithMedia;
class Brand extends Model implements HasMedia
{
    use HasFactory, SoftDeletes, InteractsWithMedia, HasImage;
    protected $fillable = ["slug", "name", "description", "options", "status"];

    protected $casts = [
        'options' => 'json',
    ];
    public function registerMediaCollections(): void
    {
        $this->addMediaCollection('brand_logo')->singleFile();
    }
}
