<?php

namespace App\Models;

use Carbon\Carbon;
use App\AttributeEnum;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Casts\Attribute as EloquentAttribute;

class Attribute extends Model
{
    use SoftDeletes;
    protected $fillable = ["name","status"];
    protected $casts = [
        "status" => AttributeEnum::class,
    ];

    public function scopeActive(Builder $query): Builder{
        return $query->where('status',AttributeEnum::ACTIVE);
    }

    protected function createdAt(): EloquentAttribute
    {
        return EloquentAttribute::make(get: fn($value) => Carbon::parse($value)->format('d-m-Y'));
    }
}
