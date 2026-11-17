<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Category;
use App\Models\FlashSale;
use App\Models\Order;
use App\Models\OrderItem;
use App\Models\Product;
use App\Models\User;
use Carbon\Carbon;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\View\View;

class AdminDashboardController extends Controller
{
    public function index(Request $request): View
    {
        $period = $request->string('period')->toString() ?: 'all';
        [$from, $to] = $this->resolveDateRange($period, $request->input('date_from'), $request->input('date_to'));

        $orderQuery = Order::query()
            ->when($from && $to, fn ($q) => $q->whereBetween('placed_at', [$from, $to]));

        $totalOrders = (clone $orderQuery)->count();
        $totalRevenue = (float) (clone $orderQuery)->where('status', '!=', 'Cancelled')->sum('total');
        $paidRevenue = (float) (clone $orderQuery)
            ->where('status', '!=', 'Cancelled')
            ->where('payment_status', 'Paid')
            ->sum('total');

        $statusCounts = (clone $orderQuery)
            ->selectRaw('status, COUNT(*) as cnt')
            ->groupBy('status')
            ->pluck('cnt', 'status');

        $placedOrdersCount = (int) ($statusCounts['Placed'] ?? 0);
        $processingOrdersCount = (int) ($statusCounts['Processing'] ?? 0);
        $completedOrdersCount = (int) ($statusCounts['Delivered'] ?? 0);
        $pendingOrdersCount = $placedOrdersCount + $processingOrdersCount;

        $totalProducts = Product::where('is_active', true)->count();
        $totalCategories = Category::count();
        $totalCustomers = User::count();
        $activeCustomersCount = User::where('status', 'Active')->count();

        $lowStockCount = Product::whereColumn('stock_count', '<=', 'min_threshold')->count();
        $lowStockProducts = Product::with('categoryRelation')
            ->whereColumn('stock_count', '<=', 'min_threshold')
            ->orderBy('stock_count')
            ->take(5)
            ->get();

        // Returning customer rate (customers with >= 2 non-cancelled orders / customers with >= 1)
        $customerOrderCounts = DB::table('orders')
            ->whereNotNull('user_id')
            ->where('status', '!=', 'Cancelled')
            ->selectRaw('user_id, COUNT(*) as order_cnt')
            ->groupBy('user_id')
            ->get();
        $buyersCount = $customerOrderCounts->count();
        $repeatBuyersCount = $customerOrderCounts->where('order_cnt', '>=', 2)->count();
        $returningCustomerRate = $buyersCount > 0 ? round(($repeatBuyersCount / $buyersCount) * 100, 1) : 0.0;

        // District-based order distribution
        $districtDistribution = (clone $orderQuery)
            ->where('status', '!=', 'Cancelled')
            ->select(['id', 'shipping_address', 'total'])
            ->get()
            ->groupBy(fn ($o) => $o->shipping_address['district_name'] ?? ($o->shipping_address['state'] ?? 'Other'))
            ->map(fn ($group, $district) => [
                'district' => $district,
                'orders' => $group->count(),
                'revenue' => round((float) $group->sum('total'), 2),
            ])
            ->sortByDesc('orders')
            ->take(6)
            ->values()
            ->all();

        // Flash Sale analytics cards (P20-4)
        $activeFlashSalesCount = FlashSale::live()->count();
        $flashStats = OrderItem::query()
            ->whereNotNull('flash_sale_id')
            ->whereHas('order', function ($q) use ($from, $to) {
                $q->where('status', '!=', 'Cancelled')
                    ->when($from && $to, fn ($sub) => $sub->whereBetween('placed_at', [$from, $to]));
            })
            ->selectRaw('COALESCE(SUM(qty), 0) as units_sold, COALESCE(SUM(line_total), 0) as revenue')
            ->first();
        $flashSaleUnitsSold = (int) ($flashStats->units_sold ?? 0);
        $flashSaleRevenue = (float) ($flashStats->revenue ?? 0);

        $recentOrders = (clone $orderQuery)
            ->with('items')
            ->latest('placed_at')
            ->take(5)
            ->get()
            ->map(fn (Order $o) => $o->toAdminArray())
            ->all();

        $topProducts = Product::with('categoryRelation')
            ->orderByDesc('review_count')
            ->take(5)
            ->get()
            ->map(fn (Product $p) => $p->toStorefrontArray())
            ->all();

        $chartData = $this->buildChartData($from, $to);

        return view('admin.dashboard.index', compact(
            'period',
            'totalRevenue',
            'paidRevenue',
            'totalOrders',
            'placedOrdersCount',
            'processingOrdersCount',
            'completedOrdersCount',
            'pendingOrdersCount',
            'totalProducts',
            'totalCategories',
            'totalCustomers',
            'activeCustomersCount',
            'returningCustomerRate',
            'lowStockCount',
            'lowStockProducts',
            'districtDistribution',
            'activeFlashSalesCount',
            'flashSaleUnitsSold',
            'flashSaleRevenue',
            'recentOrders',
            'topProducts',
            'chartData'
        ));
    }

    public function chartData(Request $request): JsonResponse
    {
        $period = $request->string('period')->toString() ?: '1y';
        [$from, $to] = $this->resolveDateRange($period, $request->input('date_from'), $request->input('date_to'));

        return response()->json($this->buildChartData($from, $to));
    }

    private function buildChartData(?Carbon $from, ?Carbon $to): array
    {
        $orders = Order::query()
            ->where('status', '!=', 'Cancelled')
            ->when($from && $to, fn ($q) => $q->whereBetween('placed_at', [$from, $to]))
            ->orderBy('placed_at')
            ->get(['placed_at', 'total', 'payment_status']);

        $useDaily = $from && $to && $from->diffInDays($to) <= 45;
        $grouped = $orders->groupBy(function (Order $order) use ($useDaily) {
            $dt = $order->placed_at ?? now();

            return $useDaily ? $dt->format('M d') : $dt->format('M Y');
        });

        $labels = [];
        $revenue = [];
        $ordersCount = [];

        foreach ($grouped as $label => $bucket) {
            $labels[] = $label;
            $revenue[] = round((float) $bucket->sum('total'), 2);
            $ordersCount[] = $bucket->count();
        }

        return [
            'labels' => $labels,
            'revenue' => $revenue,
            'orders' => $ordersCount,
        ];
    }

    /**
     * @return array{0: ?Carbon, 1: ?Carbon}
     */
    private function resolveDateRange(string $period, ?string $dateFrom, ?string $dateTo): array
    {
        $now = now();

        return match ($period) {
            'today' => [$now->copy()->startOfDay(), $now->copy()->endOfDay()],
            '7d' => [$now->copy()->subDays(6)->startOfDay(), $now->copy()->endOfDay()],
            '1m', 'this_month' => [$now->copy()->startOfMonth(), $now->copy()->endOfDay()],
            '6m' => [$now->copy()->subMonths(6)->startOfDay(), $now->copy()->endOfDay()],
            '1y', 'this_year' => [$now->copy()->startOfYear(), $now->copy()->endOfDay()],
            'last_month' => [$now->copy()->subMonthNoOverflow()->startOfMonth(), $now->copy()->subMonthNoOverflow()->endOfMonth()],
            'custom' => [
                $dateFrom ? Carbon::parse($dateFrom)->startOfDay() : null,
                $dateTo ? Carbon::parse($dateTo)->endOfDay() : null,
            ],
            default => [null, null],
        };
    }
}
