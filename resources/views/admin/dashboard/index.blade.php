@extends('layouts.admin')

@section('title', 'Dashboard')
@section('container_class', 'container-fluid')

@section('content')
<!-- Top Status & Period Filter Bar -->
<div class="row mb-3">
    <div class="col-12 d-flex flex-wrap justify-content-between align-items-center gap-2">
        <div class="alert alert-primary text-truncate mb-0 py-2 flex-grow-1" role="alert">
            <i class="bx bx-broadcast fs-16 align-middle me-1"></i>
            Storefront Active: <strong>Free shipping over {{ $currencySymbol }}{{ number_format(config('demo.site.shipping.free_shipping_threshold', 75), 0) }}</strong>
            &middot; <span class="fw-medium">{{ $totalCategories }} Categories</span> &middot; <span class="fw-medium">{{ $totalProducts }} Active SKUs</span>
        </div>
        <form method="GET" action="{{ route('admin.dashboard') }}" class="d-flex flex-wrap gap-2 align-items-center">
            <select name="period" class="form-select form-select-sm" style="width: auto;" onchange="this.form.submit()" aria-label="Filter dashboard period">
                @foreach(['all' => 'All Time', 'today' => 'Today', '7d' => 'Last 7 Days', 'this_month' => 'This Month', 'last_month' => 'Last Month', 'this_year' => 'This Year'] as $k => $lbl)
                    <option value="{{ $k }}" {{ $period === $k ? 'selected' : '' }}>{{ $lbl }}</option>
                @endforeach
            </select>
        </form>
    </div>
</div>

<!-- Row 1: 2x2 KPI Cards + Sales & Revenue Performance Chart -->
<div class="row">
    <div class="col-xxl-5">
        <div class="row">
            <!-- Total Orders -->
            <div class="col-md-6">
                <div class="card overflow-hidden">
                    <div class="card-body">
                        <div class="row">
                            <div class="col-5">
                                <div class="avatar-md bg-soft-primary rounded">
                                    <iconify-icon icon="solar:cart-5-bold-duotone" class="avatar-title fs-32 text-primary"></iconify-icon>
                                </div>
                            </div>
                            <div class="col-7 text-end">
                                <p class="text-muted mb-0 text-truncate">Total Orders</p>
                                <h3 class="text-dark mt-1 mb-0">{{ number_format($totalOrders) }}</h3>
                            </div>
                        </div>
                    </div>
                    <div class="card-footer py-2 bg-light bg-opacity-50">
                        <div class="d-flex align-items-center justify-content-between">
                            <div class="d-flex flex-wrap gap-1 fs-11">
                                <span class="badge bg-secondary-subtle text-secondary">{{ $placedOrdersCount }} Pending</span>
                                <span class="badge bg-warning-subtle text-warning">{{ $processingOrdersCount }} Proc.</span>
                            </div>
                            <a href="{{ route('admin.orders.index') }}" class="text-reset fw-semibold fs-12">View More</a>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Customers -->
            <div class="col-md-6">
                <div class="card overflow-hidden">
                    <div class="card-body">
                        <div class="row">
                            <div class="col-5">
                                <div class="avatar-md bg-soft-info rounded">
                                    <iconify-icon icon="solar:users-group-two-rounded-bold-duotone" class="avatar-title fs-32 text-info"></iconify-icon>
                                </div>
                            </div>
                            <div class="col-7 text-end">
                                <p class="text-muted mb-0 text-truncate">Customers</p>
                                <h3 class="text-dark mt-1 mb-0">{{ number_format($totalCustomers) }}</h3>
                            </div>
                        </div>
                    </div>
                    <div class="card-footer py-2 bg-light bg-opacity-50">
                        <div class="d-flex align-items-center justify-content-between">
                            <span class="badge bg-success-subtle text-success fs-11">{{ $activeCustomersCount }} Active</span>
                            <a href="{{ route('admin.customers.index') }}" class="text-reset fw-semibold fs-12">View More</a>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Paid Revenue -->
            <div class="col-md-6">
                <div class="card overflow-hidden">
                    <div class="card-body">
                        <div class="row">
                            <div class="col-5">
                                <div class="avatar-md bg-soft-success rounded">
                                    <iconify-icon icon="solar:wallet-money-bold-duotone" class="avatar-title fs-32 text-success"></iconify-icon>
                                </div>
                            </div>
                            <div class="col-7 text-end">
                                <p class="text-muted mb-0 text-truncate">Paid Revenue</p>
                                <h4 class="text-dark mt-1 mb-0">{{ $currencySymbol }}{{ number_format($paidRevenue, 2) }}</h4>
                            </div>
                        </div>
                    </div>
                    <div class="card-footer py-2 bg-light bg-opacity-50">
                        <div class="d-flex align-items-center justify-content-between">
                            <span class="text-muted fs-12">Booked: <strong>{{ $currencySymbol }}{{ number_format($totalRevenue, 2) }}</strong></span>
                            <a href="{{ route('admin.financial.index') }}" class="text-reset fw-semibold fs-12">Financials</a>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Low Stock Alerts -->
            <div class="col-md-6">
                <div class="card overflow-hidden">
                    <div class="card-body">
                        <div class="row">
                            <div class="col-5">
                                <div class="avatar-md bg-soft-danger rounded">
                                    <iconify-icon icon="solar:danger-triangle-bold-duotone" class="avatar-title fs-32 text-danger"></iconify-icon>
                                </div>
                            </div>
                            <div class="col-7 text-end">
                                <p class="text-muted mb-0 text-truncate">Low Stock Alerts</p>
                                <h3 class="text-dark mt-1 mb-0">{{ number_format($lowStockCount) }}</h3>
                            </div>
                        </div>
                    </div>
                    <div class="card-footer py-2 bg-light bg-opacity-50">
                        <div class="d-flex align-items-center justify-content-between">
                            <span class="text-muted fs-12">{{ $totalProducts }} Active SKUs</span>
                            <a href="{{ route('admin.inventory.index') }}" class="text-reset fw-semibold fs-12">Inventory</a>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Right Column: Larkon Mixed Performance Chart -->
    <div class="col-xxl-7">
        <div class="card">
            <div class="card-body">
                <div class="d-flex justify-content-between align-items-center mb-2">
                    <h4 class="card-title mb-0">Sales &amp; Revenue Performance</h4>
                    <div class="d-flex gap-1" id="chart-period-btns">
                        <button type="button" data-period="all" class="btn btn-sm btn-outline-light {{ $period === 'all' ? 'active' : '' }}">ALL</button>
                        <button type="button" data-period="1m" class="btn btn-sm btn-outline-light {{ $period === '1m' ? 'active' : '' }}">1M</button>
                        <button type="button" data-period="6m" class="btn btn-sm btn-outline-light {{ $period === '6m' ? 'active' : '' }}">6M</button>
                        <button type="button" data-period="1y" class="btn btn-sm btn-outline-light {{ $period === '1y' ? 'active' : '' }}">1Y</button>
                    </div>
                </div>
                <div dir="ltr">
                    <div id="dash-performance-chart" class="apex-charts" style="min-height: 313px;"></div>
                </div>
            </div>
        </div>
    </div>
</div>

<!-- Row 2: Flash Sale Analytics Strip -->
<div class="row">
    <div class="col-md-4">
        <div class="card overflow-hidden">
            <div class="card-body d-flex justify-content-between align-items-center">
                <div class="d-flex align-items-center gap-3">
                    <div class="avatar-md bg-soft-warning rounded">
                        <iconify-icon icon="solar:bolt-circle-bold-duotone" class="avatar-title fs-28 text-warning"></iconify-icon>
                    </div>
                    <div>
                        <p class="text-muted mb-1 fs-13">Active Flash Sales</p>
                        <h4 class="text-dark mb-0">{{ $activeFlashSalesCount }}</h4>
                    </div>
                </div>
                <a href="{{ route('admin.flash-sales.index') }}" class="btn btn-sm btn-soft-primary">Manage</a>
            </div>
        </div>
    </div>
    <div class="col-md-4">
        <div class="card overflow-hidden">
            <div class="card-body d-flex justify-content-between align-items-center">
                <div class="d-flex align-items-center gap-3">
                    <div class="avatar-md bg-soft-primary rounded">
                        <iconify-icon icon="solar:bag-check-bold-duotone" class="avatar-title fs-28 text-primary"></iconify-icon>
                    </div>
                    <div>
                        <p class="text-muted mb-1 fs-13">Flash Sale Units Sold</p>
                        <h4 class="text-dark mb-0">{{ number_format($flashSaleUnitsSold) }}</h4>
                    </div>
                </div>
                <span class="badge bg-primary-subtle text-primary fs-11">Campaigns</span>
            </div>
        </div>
    </div>
    <div class="col-md-4">
        <div class="card overflow-hidden">
            <div class="card-body d-flex justify-content-between align-items-center">
                <div class="d-flex align-items-center gap-3">
                    <div class="avatar-md bg-soft-success rounded">
                        <iconify-icon icon="solar:graph-up-bold-duotone" class="avatar-title fs-28 text-success"></iconify-icon>
                    </div>
                    <div>
                        <p class="text-muted mb-1 fs-13">Flash Sale Revenue</p>
                        <h4 class="text-success mb-0">{{ $currencySymbol }}{{ number_format($flashSaleRevenue, 2) }}</h4>
                    </div>
                </div>
                <span class="badge bg-success-subtle text-success fs-11">Live</span>
            </div>
        </div>
    </div>
</div>

<!-- Row 3: Returning Customer Gauge + Orders by District + Low Stock Watchlist -->
<div class="row">
    <!-- Returning Customer Rate (Larkon RadialBar Gauge) -->
    <div class="col-lg-4">
        <div class="card card-height-100">
            <div class="card-body d-flex flex-column justify-content-between">
                <div>
                    <h5 class="card-title mb-0">Returning Customer Rate</h5>
                    <div id="conversions-chart" class="apex-charts mb-2 mt-n2" style="min-height: 260px;"></div>
                </div>
                <div>
                    <div class="row text-center">
                        <div class="col-6 border-end">
                            <p class="text-muted mb-1 fs-13">Repeat Buyers</p>
                            <h4 class="text-dark mb-2">{{ number_format($repeatBuyersCount) }}</h4>
                        </div>
                        <div class="col-6">
                            <p class="text-muted mb-1 fs-13">Total Buyers</p>
                            <h4 class="text-dark mb-2">{{ number_format($buyersCount) }}</h4>
                        </div>
                    </div>
                    <div class="text-center mt-2">
                        <a href="{{ route('admin.customers.index') }}" class="btn btn-light shadow-none w-100">View Customer Segments</a>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Orders by District (Larkon Top Pages Style) -->
    <div class="col-lg-4">
        <div class="card card-height-100">
            <div class="card-header d-flex align-items-center justify-content-between gap-2">
                <h4 class="card-title flex-grow-1 mb-0">Orders by District</h4>
                <a href="{{ route('admin.locations.index') }}" class="btn btn-sm btn-soft-primary">Locations</a>
            </div>
            <div class="table-responsive">
                @php
                    $maxDistrictOrders = collect($districtDistribution)->max('orders') ?: 1;
                @endphp
                <table class="table table-hover table-nowrap table-centered m-0">
                    <thead class="bg-light bg-opacity-50">
                        <tr>
                            <th class="text-muted ps-3">District</th>
                            <th class="text-muted">Orders</th>
                            <th class="text-muted text-end pe-3">Revenue</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse($districtDistribution as $row)
                            @php
                                $pct = round(($row['orders'] / $maxDistrictOrders) * 100);
                            @endphp
                            <tr>
                                <td class="ps-3 fw-medium text-dark">{{ $row['district'] }}</td>
                                <td style="min-width: 110px;">
                                    <div class="d-flex align-items-center gap-2">
                                        <span class="fw-semibold fs-13">{{ $row['orders'] }}</span>
                                        <div class="progress progress-sm flex-grow-1" style="height: 5px;">
                                            <div class="progress-bar bg-primary" role="progressbar" style="width: {{ $pct }}%" aria-valuenow="{{ $pct }}" aria-valuemin="0" aria-valuemax="100"></div>
                                        </div>
                                    </div>
                                </td>
                                <td class="text-end pe-3">
                                    <span class="badge badge-soft-success">{{ $currencySymbol }}{{ number_format($row['revenue'], 2) }}</span>
                                </td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="3" class="text-center py-4 text-muted">No order distribution data yet.</td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
        </div>
    </div>

    <!-- Low Stock Watchlist -->
    <div class="col-lg-4">
        <div class="card card-height-100">
            <div class="card-header d-flex align-items-center justify-content-between gap-2">
                <h4 class="card-title flex-grow-1 mb-0">Low Stock Watchlist</h4>
                <a href="{{ route('admin.inventory.index') }}" class="btn btn-sm btn-soft-danger">Manage</a>
            </div>
            <div class="table-responsive">
                <table class="table table-hover table-nowrap table-centered m-0">
                    <thead class="bg-light bg-opacity-50">
                        <tr>
                            <th class="text-muted ps-3">Product</th>
                            <th class="text-muted">Stock</th>
                            <th class="text-muted text-end pe-3">Min</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse($lowStockProducts as $lp)
                            <tr>
                                <td class="ps-3">
                                    <a href="{{ route('admin.products.edit', $lp->id) }}" class="text-dark fw-medium">
                                        {{ \Illuminate\Support\Str::limit($lp->name, 26) }}
                                    </a>
                                    @if($lp->categoryRelation)
                                        <p class="text-muted mb-0 fs-11">{{ $lp->categoryRelation->name }}</p>
                                    @endif
                                </td>
                                <td>
                                    <span class="badge {{ $lp->stock_count <= 0 ? 'bg-danger-subtle text-danger' : 'bg-warning-subtle text-warning' }}">
                                        {{ $lp->stock_count }} left
                                    </span>
                                </td>
                                <td class="text-end pe-3 text-muted">{{ $lp->min_threshold }}</td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="3" class="text-center py-4 text-muted">
                                    <iconify-icon icon="solar:check-circle-bold-duotone" class="fs-24 text-success d-block mx-auto mb-1"></iconify-icon>
                                    All products are sufficiently stocked.
                                </td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
        </div>
    </div>
</div>

<!-- Row 4: Order Pipeline Breakdown + Top Performing Products -->
<div class="row">
    <!-- Order Pipeline Donut Chart -->
    <div class="col-xl-4">
        <div class="card card-height-100">
            <div class="card-header d-flex align-items-center justify-content-between">
                <h4 class="card-title mb-0">Order Pipeline</h4>
                <a href="{{ route('admin.orders.index') }}" class="text-reset fw-semibold fs-12">View All</a>
            </div>
            <div class="card-body">
                <div id="order-pipeline-chart" class="apex-charts" style="min-height: 210px;"></div>
                <div class="mt-3">
                    <div class="d-flex justify-content-between align-items-center py-1 border-bottom border-dashed">
                        <span class="text-muted fs-13"><i class="bx bxs-circle text-secondary me-2"></i>Placed / Pending</span>
                        <span class="fw-semibold text-dark">{{ $placedOrdersCount }}</span>
                    </div>
                    <div class="d-flex justify-content-between align-items-center py-1 border-bottom border-dashed">
                        <span class="text-muted fs-13"><i class="bx bxs-circle text-warning me-2"></i>Processing</span>
                        <span class="fw-semibold text-dark">{{ $processingOrdersCount }}</span>
                    </div>
                    <div class="d-flex justify-content-between align-items-center py-1 border-bottom border-dashed">
                        <span class="text-muted fs-13"><i class="bx bxs-circle text-info me-2"></i>Shipped</span>
                        <span class="fw-semibold text-dark">{{ $shippedOrdersCount }}</span>
                    </div>
                    <div class="d-flex justify-content-between align-items-center py-1 border-bottom border-dashed">
                        <span class="text-muted fs-13"><i class="bx bxs-circle text-success me-2"></i>Delivered</span>
                        <span class="fw-semibold text-dark">{{ $completedOrdersCount }}</span>
                    </div>
                    <div class="d-flex justify-content-between align-items-center pt-1">
                        <span class="text-muted fs-13"><i class="bx bxs-circle text-danger me-2"></i>Cancelled</span>
                        <span class="fw-semibold text-dark">{{ $cancelledOrdersCount }}</span>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Top Performing Products -->
    <div class="col-xl-8">
        <div class="card card-height-100">
            <div class="card-header d-flex align-items-center justify-content-between">
                <h4 class="card-title mb-0">Top Rated &amp; Popular Products</h4>
                <a href="{{ route('admin.products.index') }}" class="btn btn-sm btn-soft-primary">All Products</a>
            </div>
            <div class="table-responsive">
                <table class="table table-hover table-nowrap table-centered m-0">
                    <thead class="bg-light bg-opacity-50">
                        <tr>
                            <th class="ps-3">Product</th>
                            <th>Category</th>
                            <th>Price</th>
                            <th>Rating</th>
                            <th class="text-end pe-3">Stock</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse($topProducts as $tp)
                            <tr>
                                <td class="ps-3">
                                    <div class="d-flex align-items-center gap-2">
                                        @if(!empty($tp['image']))
                                            <img src="{{ $tp['image'] }}" alt="{{ $tp['name'] }}" class="avatar-sm rounded bg-light object-fit-cover">
                                        @else
                                            <div class="avatar-sm rounded bg-soft-primary d-flex align-items-center justify-content-center">
                                                <iconify-icon icon="solar:bottle-bold-duotone" class="text-primary fs-18"></iconify-icon>
                                            </div>
                                        @endif
                                        <div>
                                            <a href="{{ route('admin.products.edit', $tp['id']) }}" class="text-dark fw-medium">
                                                {{ \Illuminate\Support\Str::limit($tp['name'], 32) }}
                                            </a>
                                            <p class="text-muted mb-0 fs-11">SKU: {{ $tp['sku'] ?? '—' }}</p>
                                        </div>
                                    </div>
                                </td>
                                <td><span class="badge bg-light text-dark">{{ $tp['category'] ?? 'Skincare' }}</span></td>
                                <td class="fw-semibold">{{ $currencySymbol }}{{ number_format($tp['price'], 2) }}</td>
                                <td>
                                    <span class="badge bg-warning-subtle text-warning">
                                        <i class="bx bxs-star me-1"></i>{{ number_format($tp['rating'] ?? 0, 1) }}
                                    </span>
                                    <span class="text-muted fs-11 ms-1">({{ $tp['review_count'] ?? 0 }})</span>
                                </td>
                                <td class="text-end pe-3">
                                    @php
                                        $tpStock = $tp['stock_count'] ?? 0;
                                    @endphp
                                    <span class="badge {{ $tpStock > ($tp['min_threshold'] ?? 5) ? 'bg-success-subtle text-success' : ($tpStock > 0 ? 'bg-warning-subtle text-warning' : 'bg-danger-subtle text-danger') }}">
                                        {{ $tpStock }} in stock
                                    </span>
                                </td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="5" class="text-center py-4 text-muted">No products available yet.</td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
        </div>
    </div>
</div>

<!-- Row 5: Recent Orders Table Card (Larkon Full-Width Pattern) -->
<div class="row">
    <div class="col-12">
        <div class="card">
            <div class="card-body">
                <div class="d-flex align-items-center justify-content-between">
                    <h4 class="card-title mb-0">Recent Orders</h4>
                    <a href="{{ route('admin.orders.index') }}" class="btn btn-sm btn-soft-primary">
                        View All Orders <i class="bx bx-right-arrow-alt ms-1"></i>
                    </a>
                </div>
            </div>
            <div class="table-responsive table-centered">
                <table class="table mb-0 table-hover">
                    <thead class="bg-light bg-opacity-50">
                        <tr>
                            <th class="ps-3">Order ID</th>
                            <th>Date</th>
                            <th>Items</th>
                            <th>Customer</th>
                            <th>Total</th>
                            <th>Payment</th>
                            <th>Status</th>
                            <th class="text-end pe-3">Action</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse($recentOrders as $order)
                        <tr>
                            <td class="ps-3">
                                <a href="{{ route('admin.orders.show', $order['id']) }}" class="fw-semibold text-primary">
                                    #{{ $order['id'] }}
                                </a>
                            </td>
                            <td>{{ $order['date'] }}</td>
                            <td>
                                <div class="d-flex align-items-center gap-2">
                                    @if(!empty($order['items'][0]['image']))
                                        <img src="{{ $order['items'][0]['image'] }}" alt="{{ $order['items'][0]['name'] ?? 'Item' }}" class="img-fluid avatar-sm rounded bg-light object-fit-cover">
                                    @endif
                                    <div>
                                        <span class="fs-13 text-dark fw-medium">{{ $order['items'][0]['name'] ?? 'Botanical Treatment' }}</span>
                                        @if(($order['items_count'] ?? 1) > 1)
                                            <span class="badge bg-light text-muted ms-1">+{{ $order['items_count'] - 1 }} more</span>
                                        @endif
                                    </div>
                                </div>
                            </td>
                            <td>
                                <span class="fw-medium text-dark">{{ $order['shipping_address']['first_name'] }} {{ $order['shipping_address']['last_name'] }}</span>
                                <p class="text-muted mb-0 fs-12">{{ $order['shipping_address']['city'] }}, {{ $order['shipping_address']['state'] }}</p>
                            </td>
                            <td class="fw-bold text-dark">{{ $currencySymbol }}{{ number_format($order['total'], 2) }}</td>
                            <td>
                                <span class="badge bg-light text-dark fs-12">{{ $order['payment_method'] }}</span>
                            </td>
                            <td>
                                @php
                                    $statusDot = match($order['status']) {
                                        'Delivered' => 'text-success',
                                        'Shipped' => 'text-info',
                                        'Processing' => 'text-warning',
                                        'Cancelled' => 'text-danger',
                                        default => 'text-secondary',
                                    };
                                @endphp
                                <i class="bx bxs-circle {{ $statusDot }} me-1"></i>{{ $order['status'] }}
                            </td>
                            <td class="text-end pe-3">
                                <a href="{{ route('admin.orders.show', $order['id']) }}" class="btn btn-sm btn-soft-primary" title="View Order Details">
                                    <iconify-icon icon="solar:eye-broken" class="align-middle fs-16"></iconify-icon>
                                </a>
                            </td>
                        </tr>
                        @empty
                        <tr>
                            <td colspan="8" class="text-center py-4 text-muted">No recent orders found.</td>
                        </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
            <div class="card-footer border-top py-2 bg-light bg-opacity-50">
                <div class="d-flex align-items-center justify-content-between">
                    <div class="text-muted fs-12">
                        Showing <span class="fw-semibold">{{ count($recentOrders) }}</span> of <span class="fw-semibold">{{ number_format($totalOrders) }}</span> orders
                    </div>
                    <a href="{{ route('admin.orders.index') }}" class="text-reset fw-semibold fs-12">Go to Order Management &rarr;</a>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection

@push('scripts')
<script>
document.addEventListener('DOMContentLoaded', function () {
    if (typeof ApexCharts === 'undefined') return;

    const currencySymbol = @json($currencySymbol);

    // 1. Larkon Mixed Performance Chart (Column + Smooth Gradient Area)
    const initialChart = @json($chartData);
    const perfEl = document.querySelector('#dash-performance-chart');
    if (perfEl) {
        const perfChart = new ApexCharts(perfEl, {
            series: [
                { name: 'Revenue', type: 'bar', data: initialChart.revenue || [] },
                { name: 'Orders', type: 'area', data: initialChart.orders || [] }
            ],
            chart: {
                height: 313,
                type: 'line',
                toolbar: { show: false }
            },
            stroke: {
                dashArray: [0, 0],
                width: [0, 2],
                curve: 'smooth'
            },
            fill: {
                opacity: [1, 1],
                type: ['solid', 'gradient'],
                gradient: {
                    type: 'vertical',
                    inverseColors: false,
                    opacityFrom: 0.45,
                    opacityTo: 0,
                    stops: [0, 90]
                }
            },
            markers: { size: [0, 0], strokeWidth: 2, hover: { size: 4 } },
            xaxis: {
                categories: initialChart.labels || [],
                axisTicks: { show: false },
                axisBorder: { show: false }
            },
            yaxis: [
                {
                    min: 0,
                    axisBorder: { show: false },
                    labels: {
                        formatter: function (val) {
                            return currencySymbol + Number(val).toLocaleString();
                        }
                    }
                },
                {
                    opposite: true,
                    min: 0,
                    axisBorder: { show: false },
                    labels: {
                        formatter: function (val) {
                            return Math.round(val);
                        }
                    }
                }
            ],
            grid: {
                show: true,
                strokeDashArray: 3,
                xaxis: { lines: { show: false } },
                yaxis: { lines: { show: true } },
                padding: { top: 0, right: 6, bottom: 0, left: 10 }
            },
            legend: {
                show: true,
                horizontalAlign: 'center',
                offsetX: 0,
                offsetY: 5,
                markers: { width: 9, height: 9, radius: 6 },
                itemMargin: { horizontal: 10, vertical: 0 }
            },
            plotOptions: {
                bar: { columnWidth: '30%', barHeight: '70%', borderRadius: 3 }
            },
            colors: ['#ff6c2f', '#22c55e'],
            tooltip: {
                shared: true,
                y: [
                    {
                        formatter: function (val) {
                            return val !== undefined ? currencySymbol + Number(val).toLocaleString(undefined, { minimumFractionDigits: 2, maximumFractionDigits: 2 }) : val;
                        }
                    },
                    {
                        formatter: function (val) {
                            return val !== undefined ? Math.round(val) + ' orders' : val;
                        }
                    }
                ]
            }
        });
        perfChart.render();

        document.querySelectorAll('#chart-period-btns button').forEach(btn => {
            btn.addEventListener('click', function () {
                document.querySelectorAll('#chart-period-btns button').forEach(b => b.classList.remove('active'));
                this.classList.add('active');
                fetch(@js(route('admin.dashboard.chart')) + '?period=' + encodeURIComponent(this.dataset.period))
                    .then(r => r.json())
                    .then(data => {
                        perfChart.updateOptions({ xaxis: { categories: data.labels || [] } });
                        perfChart.updateSeries([
                            { name: 'Revenue', type: 'bar', data: data.revenue || [] },
                            { name: 'Orders', type: 'area', data: data.orders || [] }
                        ]);
                    });
            });
        });
    }

    // 2. Larkon Returning Customer RadialBar Gauge
    const convEl = document.querySelector('#conversions-chart');
    if (convEl) {
        const returningRate = @json((float) $returningCustomerRate);
        new ApexCharts(convEl, {
            chart: { height: 265, type: 'radialBar' },
            plotOptions: {
                radialBar: {
                    startAngle: -135,
                    endAngle: 135,
                    dataLabels: {
                        name: { fontSize: '13px', color: undefined, offsetY: 88 },
                        value: {
                            offsetY: 46,
                            fontSize: '22px',
                            fontWeight: 700,
                            color: undefined,
                            formatter: function (val) { return val + '%'; }
                        }
                    },
                    track: { background: 'rgba(170,184,197, 0.2)', margin: 0 }
                }
            },
            fill: {
                type: 'gradient',
                gradient: {
                    enabled: true,
                    shade: 'dark',
                    shadeIntensity: 0.2,
                    inverseColors: false,
                    opacityFrom: 1,
                    opacityTo: 1,
                    stops: [0, 50, 65, 91]
                }
            },
            stroke: { dashArray: 4 },
            colors: ['#ff6c2f', '#22c55e'],
            series: [returningRate],
            labels: ['Returning Rate'],
            grid: { padding: { top: 0, right: 0, bottom: 0, left: 0 } }
        }).render();
    }

    // 3. Order Pipeline Donut Chart
    const pipeEl = document.querySelector('#order-pipeline-chart');
    if (pipeEl) {
        const statusSeries = [
            @json($placedOrdersCount),
            @json($processingOrdersCount),
            @json($shippedOrdersCount),
            @json($completedOrdersCount),
            @json($cancelledOrdersCount)
        ];
        const hasOrders = statusSeries.some(v => v > 0);
        new ApexCharts(pipeEl, {
            chart: { type: 'donut', height: 210 },
            series: hasOrders ? statusSeries : [1],
            labels: hasOrders ? ['Placed', 'Processing', 'Shipped', 'Delivered', 'Cancelled'] : ['No Orders'],
            colors: hasOrders ? ['#6c757d', '#f59e0b', '#0dcaf0', '#22c55e', '#ef4444'] : ['#e5e7eb'],
            legend: { show: false },
            dataLabels: { enabled: false },
            plotOptions: {
                pie: {
                    donut: {
                        size: '70%',
                        labels: {
                            show: true,
                            total: {
                                show: true,
                                label: 'Total',
                                formatter: function () {
                                    return @json($totalOrders);
                                }
                            }
                        }
                    }
                }
            }
        }).render();
    }
});
</script>
@endpush
