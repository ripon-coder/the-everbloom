<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Attribute;
use App\Models\Category;
use App\Services\ImageStorageService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Str;
use Illuminate\Validation\ValidationException;

class AdminCategoryController extends Controller
{
    public function index()
    {
        $categories = Category::with(['parent.parent', 'assignedAttributes'])
            ->withCount('products')
            ->orderBy('level')
            ->orderBy('sort_order')
            ->get()
            ->map(function (Category $cat) {
                $arr = $cat->toArray();
                $arr['image'] = $cat->resolvedImage();
                $arr['count'] = $cat->products_count;
                $arr['hierarchy_path'] = $cat->hierarchy_path;
                return $arr;
            })
            ->all();

        return view('admin.categories.index', compact('categories'));
    }

    public function create()
    {
        $parentCategories = Category::where('level', '<', 3)
            ->with('parent')
            ->orderBy('level')
            ->orderBy('sort_order')
            ->get();

        $attributes = Attribute::where('is_active', true)->orderBy('sort_order')->get();

        return view('admin.categories.create', compact('parentCategories', 'attributes'));
    }

    public function edit($slug)
    {
        $category = Category::with('assignedAttributes')
            ->where('slug', $slug)
            ->orWhere('id', is_numeric($slug) ? (int) $slug : 0)
            ->firstOrFail();

        $excludeIds = $category->descendantAndSelfIds();

        $parentCategories = Category::where('level', '<', 3)
            ->whereNotIn('id', $excludeIds)
            ->with('parent')
            ->orderBy('level')
            ->orderBy('sort_order')
            ->get();

        $attributes = Attribute::where('is_active', true)->orderBy('sort_order')->get();

        $arr = $category->toArray();
        $arr['image'] = $category->resolvedImage();
        $arr['attribute_ids'] = $category->assignedAttributes->pluck('id')->all();

        return view('admin.categories.create', [
            'category' => $arr,
            'parentCategories' => $parentCategories,
            'attributes' => $attributes,
            'isEdit' => true,
        ]);
    }

    public function store(Request $request, ImageStorageService $imageStorage)
    {
        $validated = $request->validate([
            'parent_id' => ['nullable', 'integer', 'exists:categories,id'],
            'name' => ['required', 'string', 'max:120'],
            'short_name' => ['nullable', 'string', 'max:60'],
            'slug' => ['nullable', 'string', 'max:120', 'unique:categories,slug'],
            'tagline' => ['nullable', 'string', 'max:255'],
            'description' => ['nullable', 'string'],
            'image' => ['nullable', 'image', 'max:5120'],
            'attribute_ids' => ['nullable', 'array'],
            'attribute_ids.*' => ['integer', 'exists:attributes,id'],
        ]);

        $level = 1;
        $parentId = $validated['parent_id'] ?? null;
        if ($parentId) {
            $parent = Category::findOrFail($parentId);
            if ($parent->level >= 3) {
                throw ValidationException::withMessages([
                    'parent_id' => 'Maximum category depth is 3 steps (Main -> Subcategory -> Child Category).',
                ]);
            }
            $level = $parent->level + 1;
        }

        $slug = !empty($validated['slug']) ? Str::slug($validated['slug']) : Str::slug($validated['name']);
        if (Category::where('slug', $slug)->exists()) {
            $slug .= '-' . Str::lower(Str::random(4));
        }

        $imagePath = null;
        $imageSource = 'local';
        if ($request->hasFile('image')) {
            $result = $imageStorage->uploadAsWebp($request->file('image'), 'categories', $slug);
            $imagePath = $result['path'];
            $imageSource = $result['source'];
        }

        $category = Category::create([
            'parent_id' => $parentId,
            'level' => $level,
            'name' => $validated['name'],
            'short_name' => $validated['short_name'] ?? null,
            'slug' => $slug,
            'tagline' => $validated['tagline'] ?? null,
            'description' => $validated['description'] ?? null,
            'image' => $imagePath,
            'image_source' => $imageSource,
            'sort_order' => (Category::max('sort_order') ?? 0) + 1,
            'is_active' => true,
        ]);

        $category->assignedAttributes()->sync($request->input('attribute_ids', []));
        Cache::forget('storefront_catalog_json');

        return redirect()->route('admin.categories.index')->with('success', 'Category created successfully.');
    }

    public function update(Request $request, $slug, ImageStorageService $imageStorage)
    {
        $category = Category::where('slug', $slug)
            ->orWhere('id', is_numeric($slug) ? (int) $slug : 0)
            ->firstOrFail();

        $validated = $request->validate([
            'parent_id' => ['nullable', 'integer', 'exists:categories,id'],
            'name' => ['required', 'string', 'max:120'],
            'short_name' => ['nullable', 'string', 'max:60'],
            'slug' => ['nullable', 'string', 'max:120', 'unique:categories,slug,' . $category->id],
            'tagline' => ['nullable', 'string', 'max:255'],
            'description' => ['nullable', 'string'],
            'image' => ['nullable', 'image', 'max:5120'],
            'attribute_ids' => ['nullable', 'array'],
            'attribute_ids.*' => ['integer', 'exists:attributes,id'],
        ]);

        $level = 1;
        $parentId = $validated['parent_id'] ?? null;
        $children = $category->children()->with('children')->get();
        $subtreeOffset = 0;
        if ($children->isNotEmpty()) {
            $subtreeOffset = $children->contains(fn (Category $c) => $c->children->isNotEmpty()) ? 2 : 1;
        }

        if ($parentId) {
            if (in_array((int) $parentId, $category->descendantAndSelfIds(), true)) {
                throw ValidationException::withMessages([
                    'parent_id' => 'A category cannot be assigned to itself or one of its descendants.',
                ]);
            }
            $parent = Category::findOrFail($parentId);
            if ($parent->level >= 3) {
                throw ValidationException::withMessages([
                    'parent_id' => 'Maximum category depth is 3 steps (Main -> Subcategory -> Child Category).',
                ]);
            }
            $level = $parent->level + 1;
        }

        if ($level + $subtreeOffset > 3) {
            throw ValidationException::withMessages([
                'parent_id' => 'Moving this category would push child categories past the 3-step depth limit.',
            ]);
        }

        $newSlug = !empty($validated['slug']) ? Str::slug($validated['slug']) : $category->slug;
        $imagePath = $category->image;
        $imageSource = $category->image_source ?: 'local';

        if ($request->hasFile('image')) {
            $imageStorage->delete($category->image, $category->image_source);
            $result = $imageStorage->uploadAsWebp($request->file('image'), 'categories', $newSlug);
            $imagePath = $result['path'];
            $imageSource = $result['source'];
        }

        $category->update([
            'parent_id' => $parentId,
            'level' => $level,
            'name' => $validated['name'],
            'short_name' => $validated['short_name'] ?? null,
            'slug' => $newSlug,
            'tagline' => $validated['tagline'] ?? null,
            'description' => $validated['description'] ?? null,
            'image' => $imagePath,
            'image_source' => $imageSource,
        ]);

        foreach ($children as $child) {
            $child->update(['level' => $level + 1]);
            foreach ($child->children as $grandChild) {
                $grandChild->update(['level' => $level + 2]);
            }
        }

        $category->assignedAttributes()->sync($request->input('attribute_ids', []));
        Cache::forget('storefront_catalog_json');

        return redirect()->route('admin.categories.index')->with('success', 'Category updated successfully.');
    }

    public function destroy($slug, ImageStorageService $imageStorage)
    {
        $category = Category::where('slug', $slug)
            ->orWhere('id', is_numeric($slug) ? (int) $slug : 0)
            ->firstOrFail();

        $imageStorage->delete($category->image, $category->image_source);
        $category->delete();
        Cache::forget('storefront_catalog_json');

        return redirect()->route('admin.categories.index')->with('success', 'Category deleted successfully.');
    }
}
