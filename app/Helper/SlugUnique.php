<?php

namespace App\Helper;

use Illuminate\Support\Str;

class SlugUnique
{
    /**
     * Generate a unique slug for insert and update scenarios.
     *
     * @param \Illuminate\Database\Eloquent\Builder $model
     * @param string $column_name
     * @param string $title
     * @param int|null $id (optional, needed for updates)
     * @return string
     */
    public static function slugify($model, $column_name = "slug", $title, $id = null)
    {
        $slug = Str::slug($title);
        $slugBase = $slug; 
        $count_number = self::haveDuplicate($model, $column_name, $slug, $id);
        if ($count_number > 0) {
            $slug = "{$slugBase}-{$count_number}";
        }

        return $slug;
    }

    /**
     * Check if a slug already exists in the database (ignoring the current model if updating).
     *
     * @param \Illuminate\Database\Eloquent\Builder $model
     * @param string $column_name
     * @param string $slug
     * @param int|null $id (optional, for update)
     * @return int
     */
    private static function haveDuplicate($model, $column_name, $slug, $id = null)
    {
        $query = $model->where($column_name, 'LIKE', "{$slug}%");
        return $query->count();
    }
}
