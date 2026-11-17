<?php

namespace App\Http\Controllers;

use App\Models\Brand;
use App\Models\Category;
use App\Models\Product;
use App\Models\Review;
use Illuminate\Http\Request;
use Illuminate\View\View;

class ShopController extends Controller
{
    public function index(Request $request): View
    {
        $query = Product::with(['categoryRelation', 'productVariants'])
            ->where('is_active', true);

        if ($request->filled('search')) {
            $ids = Product::search((string) $request->query('search'))->keys()->all();
            if (empty($ids)) {
                $query->whereRaw('1 = 0');
            } else {
                $query->whereIn('id', $ids);
            }
        }

        if ($request->filled('category')) {
            $catSlug = (string) $request->query('category');
            $cat = Category::where('slug', $catSlug)->first();
            if ($cat) {
                $query->whereIn('category_id', $cat->descendantAndSelfIds());
            } else {
                $query->whereRaw('1 = 0');
            }
        }

        if ($request->filled('brand')) {
            $brandParam = (string) $request->query('brand');
            $brandModel = Brand::where('slug', $brandParam)->first();
            if ($brandModel) {
                $query->where(function ($q) use ($brandModel) {
                    $q->where('brand_id', $brandModel->id)
                        ->orWhere('brand', $brandModel->name);
                });
            } else {
                $query->where('brand', $brandParam);
            }
        }

        if ($request->boolean('in_stock')) {
            $query->where('stock_status', 'in_stock');
        }

        if ($request->boolean('on_sale')) {
            $query->whereNotNull('compare_at_price')
                ->whereColumn('compare_at_price', '>', 'price');
        }

        if ($request->filled('goal')) {
            $goals = (array) $request->query('goal');
            $query->where(function ($q) use ($goals) {
                foreach ($goals as $goal) {
                    $q->orWhereJsonContains('skin_types', $goal)
                      ->orWhereJsonContains('benefits', $goal);
                }
            });
        }

        $sort = (string) $request->query('sort', 'featured');
        match ($sort) {
            'price-asc' => $query->orderBy('price', 'asc'),
            'price-desc' => $query->orderBy('price', 'desc'),
            'rating' => $query->orderByDesc('rating'),
            'newest' => $query->latest(),
            'best_selling' => $query->orderByDesc('best_seller')->orderByDesc('review_count'),
            default => $query->orderByDesc('is_featured')->orderBy('id'),
        };

        $products = $query->get()
            ->map(fn (Product $p) => $p->toStorefrontArray())
            ->values()
            ->all();

        $categories = Category::where('is_active', true)
            ->withCount(['products as item_count' => fn ($q) => $q->where('is_active', true)])
            ->orderBy('sort_order')
            ->get()
            ->toArray();

        $totalActiveProductsCount = Product::where('is_active', true)->count();

        return view('frontend.shop.index', [
            'products' => $products,
            'categories' => $categories,
            'totalActiveProductsCount' => $totalActiveProductsCount,
        ]);
    }

    public function category(string $slug, Request $request): View
    {
        $category = Category::where('slug', $slug)
            ->where('is_active', true)
            ->firstOrFail();

        $query = Product::with(['categoryRelation', 'productVariants'])
            ->whereIn('category_id', $category->descendantAndSelfIds())
            ->where('is_active', true);

        $sort = (string) $request->query('sort', 'featured');
        match ($sort) {
            'price-asc' => $query->orderBy('price', 'asc'),
            'price-desc' => $query->orderBy('price', 'desc'),
            'rating' => $query->orderByDesc('rating'),
            'newest' => $query->latest(),
            'best_selling' => $query->orderByDesc('best_seller')->orderByDesc('review_count'),
            default => $query->orderByDesc('is_featured')->orderBy('id'),
        };

        $products = $query->get()
            ->map(fn (Product $p) => $p->toStorefrontArray())
            ->values()
            ->all();

        $categories = Category::where('is_active', true)
            ->withCount(['products as item_count' => fn ($q) => $q->where('is_active', true)])
            ->orderBy('sort_order')
            ->get()
            ->toArray();

        $totalActiveProductsCount = Product::where('is_active', true)->count();

        $categoryArr = $category->toArray();
        $categoryArr['resolved_image'] = $category->resolvedImage();

        return view('frontend.shop.category', [
            'category' => $categoryArr,
            'products' => $products,
            'categories' => $categories,
            'totalActiveProductsCount' => $totalActiveProductsCount,
        ]);
    }

    public function brand(string $slug, Request $request): View
    {
        $brand = Brand::where('slug', $slug)
            ->where('is_active', true)
            ->firstOrFail();

        $query = Product::with(['categoryRelation', 'brandRelation', 'productVariants'])
            ->where('is_active', true)
            ->where(function ($q) use ($brand) {
                $q->where('brand_id', $brand->id)
                    ->orWhere('brand', $brand->name);
            });

        $sort = (string) $request->query('sort', 'featured');
        match ($sort) {
            'price-asc' => $query->orderBy('price', 'asc'),
            'price-desc' => $query->orderBy('price', 'desc'),
            'rating' => $query->orderByDesc('rating'),
            'newest' => $query->latest(),
            'best_selling' => $query->orderByDesc('best_seller')->orderByDesc('review_count'),
            default => $query->orderByDesc('is_featured')->orderBy('id'),
        };

        $products = $query->get()
            ->map(fn (Product $p) => $p->toStorefrontArray())
            ->values()
            ->all();

        $brandArr = $brand->toArray();
        $brandArr['resolved_logo'] = $brand->resolvedLogo();

        return view('frontend.shop.brand', [
            'brand' => $brandArr,
            'products' => $products,
        ]);
    }

    public function product(string $slug): View
    {
        $product = Product::with([
            'categoryRelation',
            'productVariants',
            'reviews' => fn ($q) => $q->where('status', 'Approved')->latest(),
        ])
            ->where('slug', $slug)
            ->where('is_active', true)
            ->firstOrFail();

        $sameCat = Product::with(['categoryRelation', 'productVariants'])
            ->where('is_active', true)
            ->where('category_id', $product->category_id)
            ->where('id', '!=', $product->id)
            ->take(4)
            ->get();

        if ($sameCat->count() < 4) {
            $needed = 4 - $sameCat->count();
            $excludeIds = $sameCat->pluck('id')->push($product->id)->all();
            $others = Product::with(['categoryRelation', 'productVariants'])
                ->where('is_active', true)
                ->whereNotIn('id', $excludeIds)
                ->take($needed)
                ->get();
            $related = $sameCat->concat($others);
        } else {
            $related = $sameCat;
        }

        $reviews = $product->reviews->map(fn (Review $r) => [
            'id' => $r->id,
            'author' => $r->author,
            'variant' => $r->variant ?? ($product->sizes[0] ?? 'Standard'),
            'rating' => (int) $r->rating,
            'date' => $r->date,
            'date_iso' => $r->date_iso,
            'label' => $r->label,
            'headline' => $r->headline,
            'body' => $r->body,
        ])->values()->all();

        return view('frontend.shop.product', [
            'product' => $product->toStorefrontArray(),
            'related' => $related->map(fn (Product $p) => $p->toStorefrontArray())->values()->all(),
            'reviews' => $reviews,
        ]);
    }
}
