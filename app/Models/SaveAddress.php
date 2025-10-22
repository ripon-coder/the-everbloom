<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class SaveAddress extends Model
{
    use HasFactory;

    protected $fillable = [
        "user_id",
        'name',
        'phone_number',
        'district_id',
        'zone',
        'address',
    ];
}
