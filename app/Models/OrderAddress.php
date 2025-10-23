<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class OrderAddress extends Model
{
    use HasFactory;
    protected $fillable = [
        "order_id",
        "user_id",
        "name",
        "phone_number",
        "district_id",
        "zone",
        "address",
    ];
    public function district()
    {
        return $this->belongsTo(District::class, 'district_id');
    }
}
