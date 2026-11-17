@extends('layouts.admin')

@section('title', 'Welcome!')
@section('container_class', 'container-fluid')

@section('content')
<!-- Date Range Filter Bar -->
<div class="row mb-3">
    <div class="col-12 d-flex flex-wrap justify-content-between align-items-center gap-2">
        <div class="alert alert-primary text-truncate mb-0 py-2 flex-grow-1" role="alert">
            <i class="bx bx-broadcast fs-16 align-middle me-1"></i> Storefront Active: <strong>Free shipping over {{ $currencySymbol }}{{ number_format(config('demo.site.shipping.free_shipping_threshold', 75), 0) }}</strong>.
        </div>
        <form method="GET" action="{{ route('admin.dashboard') }}" class="d-flex flex-wrap gap-2 align-items-center">
            <select name="period" class="form-select form-select-sm" style="width:auto;" onchange="this.form.submit()">
                @foreach(['all' => 'All Time', 'today' => 'Today', '7d' => 'Last 7 Days', 'this_month' => 'This Month', 'last_month' => 'Last Month', 'this_year' => 'This Year'] as $k => $lbl)
                    <option value="{{ $k }}" {{ $period === $k ? 'selected' : '' }}>{{ $lbl }}</option>
                @endforeach
            </select>
        </form>
    </div>
</div>

<!-- Row 1: KPI Cards + Performance Chart -->
<div class="row">
    <div class="col-xxl-5">
        <div class="row">
            <!-- Total Orders + Breakdown -->
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
                        <div class="d-flex flex-wrap align-items-center justify-content-between gap-1 fs-11">
                            <span class="badge bg-secondary-subtle text-secondary">{{ $placedOrdersCount }} Pending</span>
                            <span class="badge bg-warning-subtle text-warning">{{ $processingOrdersCount }} Processing</span>
                            <span class="badge bg-success-subtle text-success">{{ $completedOrdersCount }} Completed</span>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Total Customers -->
            <div class="col-md-6">
                <div class="card overflow-hidden">
                    <div class="card-body">
                        <div class="row">
                            <div class="col-5">
                                <div class="avatar-md bg-soft-primary rounded">
                                    <iconify-icon icon="solar:users-group-two-rounded-bold-duotone" class="avatar-title fs-32 text-primary"></iconify-icon>
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
                            <span class="badge bg-success-subtle text-success fs-11">{{ $activeCustomersCount }} Active Profiles</span>
                            <a href="{{ route('admin.customers.index') }}" class="text-reset fw-semibold fs-12">View All</a>
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

            <!-- Low Stock & Active SKUs -->
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

    <!-- Right Column: Real Sales & Revenue Chart -->
    <div class="col-xxl-7">
        <div class="card">
            <div class="card-body">
                <div class="d-flex justify-content-between align-items-center mb-2">
                    <h4 class="card-title mb-0">Sales & Revenue Performance</h4>
                    <div class="btn-group btn-group-sm" id="chart-period-btns">
                        <button type="button" data-period="all" class="btn btn-outline-secondary {{ $period === 'all' ? 'active' : '' }}">ALL</button>
                        <button type="button" data-period="1m" class="btn btn-outline-secondary {{ $period === '1m' ? 'active' : '' }}">1M</button>
                        <button type="button" data-period="6m" class="btn btn-outline-secondary {{ $period === '6m' ? 'active' : '' }}">6M</button>
                        <button type="button" data-period="1y" class="btn btn-outline-secondary {{ $period === '1y' ? 'active' : '' }}">1Y</button>
                    </div>
                </div>
                <div dir="ltr">
                    <div id="dash-performance-chart" class="apex-charts" style="min-height: 280px;"></div>
                </div>
            </div>
        </div>
    </div>
</div>

<!-- Flash Sale Analytics Row (P20-4) -->
<div class="row">
    <div class="col-md-4">
        <div class="card"><div class="card-body d-flex justify-content-between align-items-center">
            <div><p class="text-muted mb-1">Active Flash Sales</p><h4 class="mb-0">{{ $activeFlashSalesCount }}</h4></div>
            <a href="{{ route('admin.flash-sales.index') }}" class="btn btn-sm btn-soft-primary">Manage</a>
        </div></div>
    </div>
    <div class="col-md-4">
        <div class="card"><div class="card-body d-flex justify-content-between align-items-center">
            <div><p class="text-muted mb-1">Flash Sale Units Sold</p><h4 class="mb-0">{{ number_format($flashSaleUnitsSold) }}</h4></div>
            <iconify-icon icon="solar:bolt-bold-duotone" class="fs-28 text-warning"></iconify-icon>
        </div></div>
    </div>
    <div class="col-md-4">
        <div class="card"><div class="card-body d-flex justify-content-between align-items-center">
            <div><p class="text-muted mb-1">Flash Sale Revenue</p><h4 class="mb-0 text-success">{{ $currencySymbol }}{{ number_format($flashSaleRevenue, 2) }}</h4></div>
            <iconify-icon icon="solar:graph-up-bold-duotone" class="fs-28 text-success"></iconify-icon>
        </div></div>
    </div>
</div>

<!-- Row 2: Returning Customers + District Distribution + Low Stock Products -->
<div class="row">
    <!-- Returning Customers -->
    <div class="col-lg-4">
        <div class="card">
            <div class="card-body text-center">
                <h4 class="card-title text-start">Returning Customer Rate</h4>
                <div class="py-4">
                    <h2 class="display-5 fw-bold text-primary mb-1">{{ $returningCustomerRate }}%</h2>
                    <p class="text-muted mb-0">Customers with 2+ completed/active orders</p>
                </div>
            </div>
            <div class="card-footer py-2 bg-light bg-opacity-50 text-center">
                <a href="{{ route('admin.customers.index') }}" class="text-reset fw-semibold fs-12">View Customer Segments &rarr;</a>
            </div>
        </div>
    </div>

    <!-- District Distribution -->
    <div class="col-lg-4">
        <div class="card">
            <div class="card-header"><h4 class="card-title mb-0">Orders by District</h4></div>
            <div class="card-body p-0">
                <table class="table table-sm mb-0 align-middle">
                    <thead class="bg-light-subtle"><tr><th>District</th><th>Orders</th><th class="text-end">Revenue</th></tr></thead>
                    <tbody>
                        @forelse($districtDistribution as $row)
                            <tr>
                                <td class="fw-medium">{{ $row['district'] }}</td>
                                <td>{{ $row['orders'] }}</td>
                                <td class="text-end">{{ $currencySymbol }}{{ number_format($row['revenue'], 2) }}</td>
                            </tr>
                        @empty
                            <tr><td colspan="3" class="text-center py-3 text-muted">No order distribution data yet.</td></tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
        </div>
    </div>

    <!-- Low Stock Products List -->
    <div class="col-lg-4">
        <div class="card">
            <div class="card-header d-flex align-items-center justify-content-between">
                <h4 class="card-title mb-0">Low Stock Watchlist</h4>
                <a href="{{ route('admin.inventory.index') }}" class="text-reset fw-semibold fs-12">Manage</a>
            </div>
            <div class="card-body p-0">
                <table class="table table-sm mb-0 align-middle">
                    <thead class="bg-light-subtle"><tr><th>Product</th><th>Stock</th><th>Threshold</th></tr></thead>
                    <tbody>
                        @forelse($lowStockProducts as $lp)
                            <tr>
                                <td><a href="{{ route('admin.products.edit', $lp->id) }}" class="text-dark fw-medium">{{ \Illuminate\Support\Str::limit($lp->name, 26) }}</a></td>
                                <td><span class="badge bg-danger-subtle text-danger">{{ $lp->stock_count }}</span></td>
                                <td class="text-muted">{{ $lp->min_threshold }}</td>
                            </tr>
                        @empty
                            <tr><td colspan="3" class="text-center py-3 text-muted">All products are sufficiently stocked.</td></tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
        </div>
    </div>
</div>

<!-- Row 3: Recent Orders Table Card -->
<div class="row">
    <div class="col-12">
        <div class="card">
            <div class="card-header d-flex justify-content-between align-items-center gap-1">
                <h4 class="card-title mb-0">Recent Orders</h4>
                <a href="{{ route('admin.orders.index') }}" class="btn btn-sm btn-soft-primary">View All Orders</a>
            </div>
            <div class="card-body p-0">
                <div class="table-responsive">
                    <table class="table align-middle mb-0 table-hover table-centered">
                        <thead class="bg-light-subtle">
                            <tr>
                                <th>Order ID</th>
                                <th>Date</th>
                                <th>Items</th>
                                <th>Customer</th>
                                <th>Total</th>
                                <th>Payment Method</th>
                                <th>Status</th>
                                <th class="text-end">Action</th>
                            </tr>
                        </thead>
                        <tbody>
                            @forelse($recentOrders as $order)
                            <tr>
                                <td>
                                    <a href="{{ route('admin.orders.show', $order['id']) }}" class="fw-semibold text-primary">
                                        #{{ $order['id'] }}
                                    </a>
                                </td>
                                <td>{{ $order['date'] }}</td>
                                <td>
                                    <div class="d-flex align-items-center gap-2">
                                        <span class="fs-13 text-dark fw-medium">{{ $order['items'][0]['name'] ?? 'Botanical Treatment' }}</span>
                                        @if(($order['items_count'] ?? 1) > 1)
                                            <span class="badge bg-light text-muted">+{{ $order['items_count'] - 1 }} more</span>
                                        @endif
                                    </div>
                                </td>
                                <td>
                                    <span class="fw-medium text-dark">{{ $order['shipping_address']['first_name'] }} {{ $order['shipping_address']['last_name'] }}</span>
                                    <p class="text-muted mb-0 fs-12">{{ $order['shipping_address']['city'] }}, {{ $order['shipping_address']['state'] }}</p>
                                </td>
                                <td class="fw-bold">{{ $currencySymbol }}{{ number_format($order['total'], 2) }}</td>
                                <td><span class="badge bg-light text-muted fs-12">{{ $order['payment_method'] }}</span></td>
                                <td>
                                    @php
                                        $badgeClass = match($order['status']) {
                                            'Delivered' => 'bg-success-subtle text-success',
                                            'Shipped' => 'bg-info-subtle text-info',
                                            'Processing' => 'bg-warning-subtle text-warning',
                                            default => 'bg-secondary-subtle text-secondary',
                                        };
                                    @endphp
                                    <span class="badge {{ $badgeClass }} px-2 py-1 fs-12">{{ $order['status'] }}</span>
                                </td>
                                <td class="text-end">
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
            </div>
            <div class="card-footer py-2 bg-light bg-opacity-50">
                <div class="d-flex align-items-center justify-content-between">
                    <p class="mb-0 text-muted fs-12">Displaying {{ count($recentOrders) }} of {{ $totalOrders }} orders</p>
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
    const initialChart = @json($chartData);
    const chartEl = document.querySelector('#dash-performance-chart');
    if (!chartEl || typeof ApexCharts === 'undefined') return;

    const chart = new ApexCharts(chartEl, {
        chart: { type: 'line', height: 280, toolbar: { show: false } },
        series: [
            { name: 'Revenue', type: 'column', data: initialChart.revenue || [] },
            { name: 'Orders', type: 'line', data: initialChart.orders || [] }
        ],
        xaxis: { categories: initialChart.labels || [] },
        yaxis: [
            { title: { text: 'Revenue' } },
            { opposite: true, title: { text: 'Orders' } }
        ],
        stroke: { width: [0, 3] },
        colors: ['#3b7ddd', '#22c55e']
    });
    chart.render();

    document.querySelectorAll('#chart-period-btns button').forEach(btn => {
        btn.addEventListener('click', function () {
            document.querySelectorAll('#chart-period-btns button').forEach(b => b.classList.remove('active'));
            this.classList.add('active');
            fetch(@js(route('admin.dashboard.chart')) + '?period=' + encodeURIComponent(this.dataset.period))
                .then(r => r.json())
                .then(data => {
                    chart.updateOptions({ xaxis: { categories: data.labels || [] } });
                    chart.updateSeries([
                        { name: 'Revenue', type: 'column', data: data.revenue || [] },
                        { name: 'Orders', type: 'line', data: data.orders || [] }
                    ]);
                });
        });
    });
});
</script>
@endpush
