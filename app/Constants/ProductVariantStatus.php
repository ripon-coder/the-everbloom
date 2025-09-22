<?php

namespace App\Constants;

use Illuminate\Contracts\Database\Eloquent\CastsAttributes;

class ProductVariantStatus implements CastsAttributes
{
    public const ACTIVE = "active";
    public const INACTIVE = "inactive";

    /**
     * Cast the given value.
     *
     * @param  \Illuminate\Database\Eloquent\Model  $model
     * @param  string  $key
     * @param  mixed  $value
     * @param  array  $attributes
     * @return mixed
     */
    public function get($model, $key, $value, $attributes)
    {
        return $value;
    }

    /**
     * Prepare the given value for storage.
     *
     * @param  \Illuminate\Database\Eloquent\Model  $model
     * @param  string  $key
     * @param  mixed  $value
     * @param  array  $attributes
     * @return mixed
     */
    public function set($model, $key, $value, $attributes)
    {
        return $value;
    }

    /**
     * Get all available statuses.
     *
     * @return array
     */
    public static function getAll(): array
    {
        return [
            self::ACTIVE,
            self::INACTIVE,
        ];
    }

    /**
     * Get status options for dropdown/select.
     *
     * @return array
     */
    public static function getOptions(): array
    {
        return [
            self::ACTIVE => 'Active',
            self::INACTIVE => 'Inactive',
        ];
    }
}
