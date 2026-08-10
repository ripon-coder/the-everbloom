<?php

namespace App\Models;

use App\Trait\HasImage;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Slider extends Model
{
    use HasFactory, HasImage;
    
    protected $imageColumns = ['image'];

    protected $imageDisk = 'public';


    protected $fillable = [
        'image',
        'title',
        'subtitle',
        'btn_text',
        'btn_link',
        'status',
        'sort_order',
    ];

    public function getImageUrl()
    {
        return $this->traitGetImageUrl('image');
    }
}
