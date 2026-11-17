<?php

namespace App\Http\Controllers;

use App\Models\FlashSale;
use Illuminate\View\View;

class FlashSaleController extends Controller
{
    public function index(): View
    {
        $now = now();

        $activeSales = FlashSale::live()
            ->with(['items' => function ($q) {
                $q->where('is_active', true)
                    ->whereColumn('sold_qty', '<', 'sale_stock')
                    ->with(['product.categoryRelation', 'product.productVariants', 'variant']);
            }])
            ->orderBy('ends_at')
            ->get();

        $upcomingSales = FlashSale::query()
            ->where('status', FlashSale::STATUS_SCHEDULED)
            ->where('starts_at', '>', $now)
            ->with(['items' => function ($q) {
                $q->where('is_active', true)
                    ->with(['product.categoryRelation', 'product.productVariants', 'variant']);
            }])
            ->orderBy('starts_at')
            ->get();

        return view('frontend.flash-sales.index', compact('activeSales', 'upcomingSales'));
    }
}

