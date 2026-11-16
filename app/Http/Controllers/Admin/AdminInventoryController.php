<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Product;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Cache;

class AdminInventoryController extends Controller
{
    public function index()
    {
        $inventory = Product::with(['categoryRelation', 'productVariants'])
            ->orderBy('id')
            ->get()
            ->map(function (Product $p) {
                $stock = (int) $p->stock_count;
                $threshold = (int) $p->min_threshold;
                $status = $stock > $threshold ? 'In Stock' : ($stock > 0 ? 'Low Stock' : 'Out of Stock');
                $statusBadge = $stock > $threshold ? 'success' : ($stock > 0 ? 'warning' : 'danger');

                $avgBuy = (float) $p->avg_buy_price;
                $sellPrice = (float) $p->price;
                $unitProfit = round($sellPrice - $avgBuy, 2);

                return [
                    'id' => $p->id,
                    'name' => $p->name,
                    'sku' => $p->sku,
                    'image' => $p->resolvedImages()[0] ?? 'products/botanical-radiance-serum-01.jpg',
                    'category' => $p->category,
                    'stock' => $stock,
                    'min_threshold' => $threshold,
                    'avg_buy_price' => $avgBuy,
                    'sell_price' => $sellPrice,
                    'unit_profit' => $unitProfit,
                    'status' => $status,
                    'status_badge' => $statusBadge,
                    'batch_lot' => $p->batch_lot ?? ('LOT-2026-B' . $p->id),
                    'last_restocked' => $p->last_restocked_at?->format('M d, Y') ?? 'Nov 02, 2026',
                ];
            })
            ->all();

        return view('admin.inventory.index', compact('inventory'));
    }

    public function updateStock(Request $request, Product $product)
    {
        $validated = $request->validate([
            'stock_count' => ['required', 'integer', 'min:0'],
            'min_threshold' => ['nullable', 'integer', 'min:0'],
            'batch_lot' => ['nullable', 'string', 'max:60'],
        ]);

        $stockCount = (int) $validated['stock_count'];
        $threshold = (int) ($validated['min_threshold'] ?? $product->min_threshold);
        $stockStatus = $stockCount <= 0
            ? 'out_of_stock'
            : ($stockCount <= $threshold ? 'low_stock' : 'in_stock');

        $product->update([
            'stock_count' => $stockCount,
            'min_threshold' => $threshold,
            'stock_status' => $stockStatus,
            'batch_lot' => $validated['batch_lot'] ?? $product->batch_lot,
            'last_restocked_at' => now()->toDateString(),
        ]);

        Cache::forget('storefront_catalog_json');

        return redirect()->back()->with('success', 'Inventory updated successfully.');
    }
}
