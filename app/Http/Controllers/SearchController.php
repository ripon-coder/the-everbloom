<?php

namespace App\Http\Controllers;

use App\Models\Product;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

class SearchController extends Controller
{
    public function index(Request $request): JsonResponse
    {
        $query = trim((string) $request->query('q', ''));

        if ($query === '') {
            return response()->json(['results' => []]);
        }

        $ids = Product::search($query)->keys()->all();

        if (empty($ids)) {
            return response()->json(['results' => []]);
        }

        $products = Product::with(['categoryRelation', 'productVariants'])
            ->where('is_active', true)
            ->whereIn('id', $ids)
            ->get()
            ->keyBy('id');

        $ordered = collect($ids)
            ->map(fn ($id) => $products->get($id))
            ->filter()
            ->map(fn (Product $product) => $product->toStorefrontArray())
            ->values()
            ->all();

        return response()->json(['results' => $ordered]);
    }
}
