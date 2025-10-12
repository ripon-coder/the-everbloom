<?php
namespace App\Helpers;

use App\Repositories\Contracts\CategoryRepository;

class CategoryHelper
{
    /**
     * Build a tree structure for categories blade
     */
    public static function BuildTree($parentId, $level, $currentId)
    {
        $categories = app(CategoryRepository::class)->allCategory();
        if (!$categories || $categories->isEmpty()) {
            return '<option value="">No categories available</option>';
        }

        $options = '';
        foreach ($categories as $category) {

            if ($category->parent_id == $parentId) {

                // if ($currentId && $category->id == $currentId) {
                //     continue;
                // }

                $indent = str_repeat('&nbsp;&nbsp;&nbsp;&nbsp;', $level);
                $hasChildren = $category->children->count() > 0;

                // Simple parent-child indicator
                $type = $hasChildren ? 'Parent' : 'Child';
                $prefix = $level > 0 ? '├── ' : '';

                $options .= '<option class="' . (($category->id == $currentId) ? "bg-red-500" : "") . '" value="' . $category->id . '" ' . ((old('parent_id') == $category->id || $currentId == $category->id) ? 'selected' : '') . '>';
                $options .= $indent . $prefix . $category->name . ' (' . $type . ')';
                $options .= '</option>';

                // Recursively add children
                $options .= self::BuildTree($category->id, $level + 1, $currentId);
            }
        }
        return $options;
    }
}