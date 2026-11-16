<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Product;
use App\Models\PurchaseBatch;
use App\Models\Vendor;
use App\Services\PurchaseBatchService;
use Illuminate\Http\Request;

class AdminPurchaseController extends Controller
{
    public function index()
    {
        $batches = PurchaseBatch::with(['vendor', 'items.product'])
            ->latest('purchased_at')
            ->latest('id')
            ->get();

        $products = Product::with(['categoryRelation'])
            ->with(['productVariants'])
            ->orderBy('id')
            ->get()
            ->map(function (Product $p) {
                $latestBatchItem = \App\Models\PurchaseBatchItem::with('vendor')
                    ->where('product_id', $p->id)
                    ->latest('id')
                    ->first();

                $sellPrice = (float) $p->price;
                $avgBuy = (float) $p->avg_buy_price;
                $unitProfit = round($sellPrice - $avgBuy, 2);
                $marginPct = $sellPrice > 0 ? round(($unitProfit / $sellPrice) * 100, 1) : 0;

                return [
                    'id' => $p->id,
                    'name' => $p->name,
                    'sku' => $p->sku,
                    'vendor_name' => $latestBatchItem?->vendor?->name ?? '—',
                    'batch_lot' => $p->batch_lot ?? '—',
                    'stock_count' => (int) $p->stock_count,
                    'last_buy_price' => (float) $p->last_buy_price,
                    'avg_buy_price' => $avgBuy,
                    'sell_price' => $sellPrice,
                    'unit_profit' => $unitProfit,
                    'margin_pct' => $marginPct,
                ];
            })
            ->all();

        return view('admin.purchases.index', compact('batches', 'products'));
    }

    public function create()
    {
        $vendors = Vendor::where('is_active', true)->orderBy('name')->get();
        $products = Product::with('productVariants')->orderBy('name')->get(['id', 'name', 'sku', 'price', 'stock_count', 'avg_buy_price', 'batch_lot']);

        return view('admin.purchases.create', compact('vendors', 'products'));
    }

    public function store(Request $request, PurchaseBatchService $service)
    {
        $validated = $request->validate([
            'vendor_name' => ['required', 'string', 'max:150'],
            'reference_no' => ['nullable', 'string', 'max:100'],
            'purchased_at' => ['required', 'date'],
            'notes' => ['nullable', 'string', 'max:1000'],
            'items' => ['required', 'array', 'min:1'],
            'items.*.product_id' => ['required', 'integer', 'exists:products,id'],
            'items.*.product_variant_id' => ['nullable', 'integer', 'exists:product_variants,id'],
            'items.*.batch_lot' => ['nullable', 'string', 'max:60'],
            'items.*.qty' => ['required', 'integer', 'min:1'],
            'items.*.unit_buy_price' => ['required', 'numeric', 'min:0'],
        ]);

        $batch = $service->createBatch($validated);

        return redirect()->route('admin.purchases.index')
            ->with('success', "Wholesale batch {$batch->batch_code} recorded and weighted average buy prices updated.");
    }
}

