<?php

namespace App\Helpers;

use Illuminate\Support\Collection;

class CategoryHelper
{
    public static function BuildTree(
        Collection $categories,
        ?int $parentId,
        int $level,
        int $currentId,
        ?int $selectedParentId
    ): string {

        $options = '';

        foreach ($categories as $category) {

            if ($category->parent_id !== $parentId) {
                continue;
            }

            $indent = str_repeat('&nbsp;&nbsp;&nbsp;&nbsp;', $level);
            $prefix = $level > 0 ? '├── ' : '';
            $hasChildren = $categories->contains('parent_id', $category->id);
            $type = $hasChildren ? 'Parent' : 'Child';

            // 🔥 Parent category highlight (bg-red)
            $highlightParent = ($category->id == $selectedParentId)
                ? 'class="bg-red-500 text-white"'
                : '';

            // 🔥 Selected check
            $isSelected = (
                old('parent_id') == $category->id ||
                $selectedParentId == $category->id
            );

            // ❌ Current category never gets red & never selectable
            $disabled = ($category->id == $currentId)
                ? 'disabled'
                : '';

            $options .= '<option value="' . $category->id . '" ' .
                        ($isSelected ? 'selected' : '') . ' ' .
                        $highlightParent . ' ' .
                        $disabled . '>';

            $options .= $indent . $prefix . $category->name . ' (' . $type . ')';
            $options .= '</option>';

            // Recursive children
            $options .= self::BuildTree(
                $categories,
                $category->id,
                $level + 1,
                $currentId,
                $selectedParentId
            );
        }

        return $options;
    }
}
