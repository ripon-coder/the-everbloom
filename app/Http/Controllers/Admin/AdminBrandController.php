<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Brand;
use App\Services\ImageStorageService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Str;

class AdminBrandController extends Controller
{
    public function index(Request $request)
    {
        $query = Brand::withCount('products')->orderBy('sort_order')->orderBy('name');

        if ($request->filled('search')) {
            $search = trim((string) $request->search);
            $query->where(function ($q) use ($search) {
                $q->where('name', 'like', "%{$search}%")
                    ->orWhere('slug', 'like', "%{$search}%");
            });
        }

        $brands = $query->paginate(15)->withQueryString();

        return view('admin.brands.index', compact('brands'));
    }

    public function create()
    {
        return view('admin.brands.create');
    }

    public function edit($id)
    {
        $brand = Brand::findOrFail($id);

        return view('admin.brands.create', [
            'brand' => $brand,
            'isEdit' => true,
        ]);
    }

    public function store(Request $request, ImageStorageService $imageStorage)
    {
        $validated = $request->validate([
            'name' => ['required', 'string', 'max:150'],
            'slug' => ['nullable', 'string', 'max:160', 'unique:brands,slug'],
            'description' => ['nullable', 'string'],
            'logo' => ['nullable', 'image', 'mimes:jpg,jpeg,png,webp', 'max:5120', 'dimensions:max_width=6000,max_height=6000'],
            'sort_order' => ['nullable', 'integer', 'min:0'],
            'is_active' => ['nullable', 'boolean'],
            'meta_title' => ['nullable', 'string', 'max:255'],
            'meta_description' => ['nullable', 'string', 'max:1000'],
        ]);

        $slug = !empty($validated['slug']) ? Str::slug($validated['slug']) : Str::slug($validated['name']);
        if (Brand::where('slug', $slug)->exists()) {
            $slug .= '-' . Str::lower(Str::random(4));
        }

        $logoPath = null;
        $imageSource = 'local';
        if ($request->hasFile('logo')) {
            $result = $imageStorage->uploadAsWebp($request->file('logo'), 'brands', $slug);
            $logoPath = $result['path'];
            $imageSource = $result['source'];
        }

        Brand::create([
            'name' => $validated['name'],
            'slug' => $slug,
            'logo' => $logoPath,
            'image_source' => $imageSource,
            'description' => $validated['description'] ?? null,
            'sort_order' => $validated['sort_order'] ?? ((Brand::max('sort_order') ?? 0) + 1),
            'is_active' => $request->has('is_active') ? $request->boolean('is_active') : true,
            'meta_title' => $validated['meta_title'] ?? null,
            'meta_description' => $validated['meta_description'] ?? null,
        ]);

        Cache::forget('storefront_catalog_json');

        return redirect()->route('admin.brands.index')->with('success', 'Brand created successfully.');
    }

    public function update(Request $request, $id, ImageStorageService $imageStorage)
    {
        $brand = Brand::findOrFail($id);

        $validated = $request->validate([
            'name' => ['required', 'string', 'max:150'],
            'slug' => ['nullable', 'string', 'max:160', 'unique:brands,slug,' . $brand->id],
            'description' => ['nullable', 'string'],
            'logo' => ['nullable', 'image', 'mimes:jpg,jpeg,png,webp', 'max:5120', 'dimensions:max_width=6000,max_height=6000'],
            'sort_order' => ['nullable', 'integer', 'min:0'],
            'is_active' => ['nullable', 'boolean'],
            'meta_title' => ['nullable', 'string', 'max:255'],
            'meta_description' => ['nullable', 'string', 'max:1000'],
        ]);

        $newSlug = !empty($validated['slug']) ? Str::slug($validated['slug']) : $brand->slug;
        $logoPath = $brand->logo;
        $imageSource = $brand->image_source ?: 'local';

        if ($request->hasFile('logo')) {
            if ($brand->logo) {
                $imageStorage->delete($brand->logo, $brand->image_source);
            }
            $result = $imageStorage->uploadAsWebp($request->file('logo'), 'brands', $newSlug);
            $logoPath = $result['path'];
            $imageSource = $result['source'];
        }

        $brand->update([
            'name' => $validated['name'],
            'slug' => $newSlug,
            'logo' => $logoPath,
            'image_source' => $imageSource,
            'description' => $validated['description'] ?? null,
            'sort_order' => array_key_exists('sort_order', $validated) && $validated['sort_order'] !== null ? (int) $validated['sort_order'] : $brand->sort_order,
            'is_active' => $request->has('is_active') ? $request->boolean('is_active') : $brand->is_active,
            'meta_title' => array_key_exists('meta_title', $validated) ? $validated['meta_title'] : $brand->meta_title,
            'meta_description' => array_key_exists('meta_description', $validated) ? $validated['meta_description'] : $brand->meta_description,
        ]);

        // Keep legacy products.brand string synchronized
        $brand->products()->update(['brand' => $brand->name]);
        Cache::forget('storefront_catalog_json');

        return redirect()->route('admin.brands.index')->with('success', 'Brand updated successfully.');
    }

    public function destroy($id, ImageStorageService $imageStorage)
    {
        $brand = Brand::findOrFail($id);

        if ($brand->products()->exists()) {
            return redirect()->back()->withErrors([
                'brand' => 'Cannot delete this brand because it still has assigned products.',
            ]);
        }

        if ($brand->logo) {
            $imageStorage->delete($brand->logo, $brand->image_source);
        }

        $brand->delete();
        Cache::forget('storefront_catalog_json');

        return redirect()->route('admin.brands.index')->with('success', 'Brand deleted successfully.');
    }
}

