<?php

namespace App\Models;

use Carbon\Carbon;
use App\AttributeEnum;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Database\Eloquent\Casts\Attribute as EloquentAttribute;

class AttributeValue extends Model
{
    use SoftDeletes;
    protected $fillable = ["attribute_id","value","status"];
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
    public function attribute()
    {
        return $this->belongsTo(Attribute::class, 'attribute_id');
    }
}
