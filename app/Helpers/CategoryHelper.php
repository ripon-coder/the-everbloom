<?php
namespace App\Helpers;
use Illuminate\Support\Collection;
class CategoryHelper
{

    public static function BuildTree(Collection $categories, ?int $parentId, int $level, ?int $currentId): string
    {
        if ($categories->isEmpty()) {
            return '<option value="">No categories available</option>';
        }

        $options = '';
        foreach ($categories as $category) {
            if ($category->parent_id == $parentId) {

                $indent = str_repeat('&nbsp;&nbsp;&nbsp;&nbsp;', $level);
                                $hasChildren = $categories->contains('parent_id', $category->id);
                $type = $hasChildren ? 'Parent' : 'Child';
                $prefix = $level > 0 ? '├── ' : '';

                $isSelected = (old('parent_id') == $category->id || $currentId == $category->id);
                $isCurrent = ($category->id == $currentId);
                
                $options .= '<option class="' . ($isCurrent ? "bg-red-500" : "") . '" value="' . $category->id . '" ' . ($isSelected ? 'selected' : '') . '>';
                $options .= $indent . $prefix . $category->name . ' (' . $type . ')';
                $options .= '</option>';
                $options .= self::BuildTree($categories, $category->id, $level + 1, $currentId);
            }
        }
        return $options;
    }
}
