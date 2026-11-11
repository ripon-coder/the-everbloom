<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;

class AdminInventoryController extends Controller
{
    public function index()
    {
        // BACKEND: replace with ProductVariant::with('product', 'warehouse')->paginate(20)
        $products = config('demo.products') ?? [];

        $inventory = array_map(function($p) {
            $stock = $p['stock_count'] ?? 25;
            $status = $stock > 15 ? 'In Stock' : ($stock > 0 ? 'Low Stock' : 'Out of Stock');
            $statusBadge = $stock > 15 ? 'success' : ($stock > 0 ? 'warning' : 'danger');

            return [
                'id' => $p['id'],
                'name' => $p['name'],
                'sku' => $p['sku'] ?? ('EB-SKU-' . str_pad($p['id'], 4, '0', STR_PAD_LEFT)),
                'image' => $p['images'][0] ?? 'products/botanical-radiance-serum-01.jpg',
                'category' => $p['category_name'] ?? 'Botanical Treatment',
                'stock' => $stock,
                'min_threshold' => 10,
                'status' => $status,
                'status_badge' => $statusBadge,
                'batch_lot' => 'LOT-2026-B' . $p['id'],
                'last_restocked' => 'Nov 02, 2026',
            ];
        }, $products);

        return view('admin.inventory.index', compact('inventory'));
    }
}
