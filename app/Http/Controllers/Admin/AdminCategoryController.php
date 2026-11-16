<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Category;
use App\Services\ImageStorageService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Str;

class AdminCategoryController extends Controller
{
    public function index()
    {
        $categories = Category::withCount('products')
            ->orderBy('sort_order')
            ->get()
            ->map(function (Category $cat) {
                $arr = $cat->toArray();
                $arr['count'] = $cat->products_count;
                return $arr;
            })
            ->all();

        return view('admin.categories.index', compact('categories'));
    }

    public function create()
    {
        return view('admin.categories.create');
    }

    public function edit($slug)
    {
        $category = Category::where('slug', $slug)
            ->orWhere('id', is_numeric($slug) ? (int) $slug : 0)
            ->firstOrFail();

        return view('admin.categories.create', [
            'category' => $category->toArray(),
            'isEdit' => true,
        ]);
    }

    public function store(Request $request, ImageStorageService $imageStorage)
    {
        $validated = $request->validate([
            'name' => ['required', 'string', 'max:120'],
            'short_name' => ['nullable', 'string', 'max:60'],
            'slug' => ['nullable', 'string', 'max:120', 'unique:categories,slug'],
            'tagline' => ['nullable', 'string', 'max:255'],
            'description' => ['nullable', 'string'],
            'image' => ['nullable', 'image', 'max:5120'],
        ]);

        $slug = !empty($validated['slug']) ? Str::slug($validated['slug']) : Str::slug($validated['name']);
        $imagePath = null;
        $imageSource = 'local';

        if ($request->hasFile('image')) {
            $result = $imageStorage->uploadAsWebp($request->file('image'), 'categories', $slug);
            $imagePath = $result['path'];
            $imageSource = $result['source'];
        }

        Category::create([
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

        Cache::forget('storefront_catalog_json');

        return redirect()->route('admin.categories.index')->with('success', 'Category created successfully.');
    }

    public function update(Request $request, $slug, ImageStorageService $imageStorage)
    {
        $category = Category::where('slug', $slug)
            ->orWhere('id', is_numeric($slug) ? (int) $slug : 0)
            ->firstOrFail();

        $validated = $request->validate([
            'name' => ['required', 'string', 'max:120'],
            'short_name' => ['nullable', 'string', 'max:60'],
            'slug' => ['nullable', 'string', 'max:120', 'unique:categories,slug,' . $category->id],
            'tagline' => ['nullable', 'string', 'max:255'],
            'description' => ['nullable', 'string'],
            'image' => ['nullable', 'image', 'max:5120'],
        ]);

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
            'name' => $validated['name'],
            'short_name' => $validated['short_name'] ?? null,
            'slug' => $newSlug,
            'tagline' => $validated['tagline'] ?? null,
            'description' => $validated['description'] ?? null,
            'image' => $imagePath,
            'image_source' => $imageSource,
        ]);

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
