<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Product;
use App\Models\ProductVariant;
use App\Models\StockMovement;
use App\Services\Inventory\StockService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

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
                    'variants' => $p->productVariants->map(fn (ProductVariant $v) => [
                        'id' => $v->id,
                        'title' => $v->title ?: $v->size,
                        'sku' => $v->sku,
                        'stock_count' => (int) $v->stock_count,
                        'stock_status' => $v->stock_status,
                    ])->values()->all(),
                ];
            })
            ->all();

        $lowStockItems = collect($inventory)
            ->filter(fn (array $item) => $item['stock'] <= $item['min_threshold'])
            ->values()
            ->all();

        $stockMovements = StockMovement::with(['product', 'variant', 'admin'])
            ->latest('id')
            ->take(25)
            ->get();

        return view('admin.inventory.index', compact('inventory', 'lowStockItems', 'stockMovements'));
    }

    public function updateStock(Request $request, Product $product, StockService $stockService)
    {
        $validated = $request->validate([
            'stock_count' => ['required', 'integer', 'min:0'],
            'min_threshold' => ['nullable', 'integer', 'min:0'],
            'batch_lot' => ['nullable', 'string', 'max:60'],
            'note' => ['nullable', 'string', 'max:255'],
        ]);

        $stockService->adjustProductStock(
            product: $product,
            newStockCount: (int) $validated['stock_count'],
            minThreshold: isset($validated['min_threshold']) ? (int) $validated['min_threshold'] : null,
            batchLot: $validated['batch_lot'] ?? null,
            note: $validated['note'] ?? null,
            adminId: Auth::guard('admin')->id(),
        );

        return redirect()->back()->with('success', 'Inventory updated successfully.');
    }

    public function updateVariantStock(Request $request, ProductVariant $variant, StockService $stockService)
    {
        $validated = $request->validate([
            'stock_count' => ['required', 'integer', 'min:0'],
            'note' => ['nullable', 'string', 'max:255'],
        ]);

        $stockService->adjustVariantStock(
            variant: $variant,
            newStockCount: (int) $validated['stock_count'],
            note: $validated['note'] ?? null,
            adminId: Auth::guard('admin')->id(),
        );

        return redirect()->back()->with('success', 'Variant inventory updated successfully.');
    }
}
