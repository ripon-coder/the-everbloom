<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Category;
use App\Models\Order;
use App\Models\OrderItem;
use App\Models\Product;
use App\Models\PurchaseBatch;
use App\Models\PurchaseBatchItem;
use App\Models\Vendor;
use Carbon\Carbon;
use Illuminate\Http\Request;

class AdminFinancialController extends Controller
{
    public function index(Request $request)
    {
        $period = $request->query('period', 'all');
        $dateFrom = $request->query('date_from');
        $dateTo = $request->query('date_to');
        $categoryId = $request->query('category_id');
        $vendorId = $request->query('vendor_id');
        $status = $request->query('status', 'all_valid');

        [$startDate, $endDate] = $this->resolveDateRange($period, $dateFrom, $dateTo);

        $categoryIds = null;
        if ($categoryId) {
            $selectedCat = Category::find($categoryId);
            $categoryIds = $selectedCat ? $selectedCat->descendantAndSelfIds() : [(int) $categoryId];
        }

        $vendorProductIds = null;
        if ($vendorId) {
            $vendorProductIds = PurchaseBatchItem::where('vendor_id', $vendorId)
                ->distinct()
                ->pluck('product_id')
                ->all();
        }

        $orderQuery = Order::with(['items.product'])->latest('placed_at');

        if ($status === 'all_valid') {
            $orderQuery->where('status', '!=', 'Cancelled');
        } elseif ($status) {
            $orderQuery->where('status', $status);
        }

        if ($startDate) {
            $orderQuery->where('placed_at', '>=', $startDate);
        }
        if ($endDate) {
            $orderQuery->where('placed_at', '<=', $endDate);
        }

        if ($categoryIds !== null || $vendorProductIds !== null) {
            $orderQuery->whereHas('items.product', function ($q) use ($categoryIds, $vendorProductIds) {
                if ($categoryIds !== null) {
                    $q->whereIn('category_id', $categoryIds);
                }
                if ($vendorProductIds !== null) {
                    $q->whereIn('products.id', $vendorProductIds);
                }
            });
        }

        $orders = $orderQuery->get();

        $grossRevenue = (float) $orders->sum('subtotal');
        $discountsGiven = (float) $orders->sum('discount_amount');
        $netRevenue = round($grossRevenue - $discountsGiven, 2);
        $totalCogs = (float) $orders->sum('total_cost');
        $netProfit = (float) $orders->sum('total_profit');
        $profitMarginPct = $netRevenue > 0 ? round(($netProfit / $netRevenue) * 100, 1) : 0;

        $batchQuery = PurchaseBatch::query();
        if ($vendorId) {
            $batchQuery->where('vendor_id', $vendorId);
        }
        if ($startDate) {
            $batchQuery->where('purchased_at', '>=', $startDate->toDateString());
        }
        if ($endDate) {
            $batchQuery->where('purchased_at', '<=', $endDate->toDateString());
        }
        $batchOutflow = (float) $batchQuery->sum('total_cost');

        $productQuery = Product::with('categoryRelation');
        if ($categoryIds !== null) {
            $productQuery->whereIn('category_id', $categoryIds);
        }
        if ($vendorProductIds !== null) {
            $productQuery->whereIn('id', $vendorProductIds);
        }
        $filteredProducts = $productQuery->get();

        $inventoryCostValuation = round($filteredProducts->sum(fn (Product $p) => (int) $p->stock_count * (float) $p->avg_buy_price), 2);
        $projectedRetailValue = round($filteredProducts->sum(fn (Product $p) => (int) $p->stock_count * (float) $p->price), 2);
        $unrealizedStockProfit = round($projectedRetailValue - $inventoryCostValuation, 2);

        // Product Profitability Breakdown from matching order items
        $orderIds = $orders->pluck('id')->all();
        $itemQuery = OrderItem::with('product')
            ->whereIn('order_id', $orderIds);
        if ($categoryIds !== null || $vendorProductIds !== null) {
            $itemQuery->whereHas('product', function ($q) use ($categoryIds, $vendorProductIds) {
                if ($categoryIds !== null) {
                    $q->whereIn('category_id', $categoryIds);
                }
                if ($vendorProductIds !== null) {
                    $q->whereIn('products.id', $vendorProductIds);
                }
            });
        }
        $productProfitability = $itemQuery->get()
            ->groupBy('product_id')
            ->map(function ($group) {
                $first = $group->first();
                $qty = $group->sum('qty');
                $revenue = round((float) $group->sum('line_total'), 2);
                $cost = round((float) $group->sum('line_cost_total'), 2);
                $profit = round((float) $group->sum('line_profit'), 2);

                return [
                    'name' => $first->name,
                    'qty' => $qty,
                    'revenue' => $revenue,
                    'cost' => $cost,
                    'profit' => $profit,
                    'margin' => $revenue > 0 ? round(($profit / $revenue) * 100, 1) : 0,
                ];
            })
            ->sortByDesc('profit')
            ->values()
            ->all();

        $categories = Category::with('parent')->orderBy('level')->orderBy('sort_order')->get();
        $vendors = Vendor::orderBy('name')->get();

        $chartLabels = $orders->reverse()->map(fn (Order $o) => $o->placed_at?->format('M d') ?? '')->values()->all();
        $chartRevenue = $orders->reverse()->map(fn (Order $o) => round((float) $o->subtotal - (float) $o->discount_amount, 2))->values()->all();
        $chartCost = $orders->reverse()->map(fn (Order $o) => round((float) $o->total_cost, 2))->values()->all();
        $chartProfit = $orders->reverse()->map(fn (Order $o) => round((float) $o->total_profit, 2))->values()->all();

        return view('admin.financial.index', compact(
            'period',
            'dateFrom',
            'dateTo',
            'categoryId',
            'vendorId',
            'status',
            'categories',
            'vendors',
            'grossRevenue',
            'discountsGiven',
            'netRevenue',
            'totalCogs',
            'netProfit',
            'profitMarginPct',
            'batchOutflow',
            'inventoryCostValuation',
            'projectedRetailValue',
            'unrealizedStockProfit',
            'productProfitability',
            'orders',
            'chartLabels',
            'chartRevenue',
            'chartCost',
            'chartProfit'
        ));
    }

    private function resolveDateRange(string $period, ?string $dateFrom, ?string $dateTo): array
    {
        $now = Carbon::now();

        return match ($period) {
            'today' => [$now->copy()->startOfDay(), $now->copy()->endOfDay()],
            '7d' => [$now->copy()->subDays(6)->startOfDay(), $now->copy()->endOfDay()],
            'this_month' => [$now->copy()->startOfMonth(), $now->copy()->endOfMonth()],
            'last_month' => [$now->copy()->subMonthNoOverflow()->startOfMonth(), $now->copy()->subMonthNoOverflow()->endOfMonth()],
            'this_year' => [$now->copy()->startOfYear(), $now->copy()->endOfYear()],
            'custom' => [
                $dateFrom ? Carbon::parse($dateFrom)->startOfDay() : null,
                $dateTo ? Carbon::parse($dateTo)->endOfDay() : null,
            ],
            default => [null, null],
        };
    }
}

