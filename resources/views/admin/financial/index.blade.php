@extends('layouts.admin')

@section('title', 'Financial Overview & Profit Ledger')
@section('container_class', 'container-xxl')

@section('content')
<!-- Filter Bar -->
@php
    $finFilterCount = collect([
        ($period && $period !== 'all') ? $period : null,
        $dateFrom ?: null,
        $dateTo ?: null,
        $categoryId ?: null,
        $vendorId ?: null,
        ($status && $status !== 'all_valid') ? $status : null,
    ])->filter()->count();
@endphp
<div class="row mb-3">
    <div class="col-12 d-flex flex-wrap justify-content-between align-items-center gap-2">
        <div class="d-flex flex-wrap align-items-center gap-2">
            <span class="badge bg-primary-subtle text-primary fs-12 px-2 py-1">
                Period: {{ ucwords(str_replace('_', ' ', $period ?: 'all')) }}
            </span>
            @if($finFilterCount > 0)
                <a href="{{ route('admin.financial.index') }}" class="badge bg-soft-danger text-danger text-decoration-none fs-11">
                    <i class="bx bx-x align-middle"></i> Reset Filters
                </a>
            @endif
        </div>
        <button type="button" class="btn btn-sm btn-soft-secondary d-flex align-items-center gap-1 position-relative" data-bs-toggle="offcanvas" data-bs-target="#financialFilterDrawer" aria-controls="financialFilterDrawer">
            <iconify-icon icon="solar:filter-bold-duotone" class="fs-16"></iconify-icon>
            <span>Filter Ledger</span>
            @if($finFilterCount > 0)
                <span class="badge bg-primary rounded-pill ms-1">{{ $finFilterCount }}</span>
            @endif
        </button>
    </div>
</div>

<!-- Row 1: Revenue, COGS, Net Profit, Batch Outflow -->
<div class="row">
    <div class="col-md-6 col-xl-3">
        <div class="card">
            <div class="card-body">
                <p class="text-muted mb-1 fs-13">Net Booked Revenue</p>
                <h3 class="fw-bold mb-1">{{ $currencySymbol }}{{ number_format($netRevenue, 2) }}</h3>
                <small class="text-muted">Gross: {{ $currencySymbol }}{{ number_format($grossRevenue, 2) }} | Discounts: -{{ $currencySymbol }}{{ number_format($discountsGiven, 2) }}</small>
            </div>
        </div>
    </div>
    <div class="col-md-6 col-xl-3">
        <div class="card">
            <div class="card-body">
                <p class="text-muted mb-1 fs-13">Total Wholesale COGS</p>
                <h3 class="fw-bold mb-1">{{ $currencySymbol }}{{ number_format($totalCogs, 2) }}</h3>
                <small class="text-muted">Batch Outflow: {{ $currencySymbol }}{{ number_format($batchOutflow, 2) }}</small>
            </div>
        </div>
    </div>
    <div class="col-md-6 col-xl-3">
        <div class="card">
            <div class="card-body">
                <p class="text-muted mb-1 fs-13">Net Realized Profit</p>
                <h3 class="fw-bold {{ $netProfit >= 0 ? 'text-success' : 'text-danger' }} mb-1">{{ ($netProfit >= 0 ? '+' : '-') . $currencySymbol }}{{ number_format(abs($netProfit), 2) }}</h3>
                <small class="text-muted">Margin: {{ $profitMarginPct }}%</small>
            </div>
        </div>
    </div>
    <div class="col-md-6 col-xl-3">
        <div class="card">
            <div class="card-body">
                <p class="text-muted mb-1 fs-13">Available Inventory Cost Valuation</p>
                <h3 class="fw-bold text-primary mb-1">{{ $currencySymbol }}{{ number_format($inventoryCostValuation, 2) }}</h3>
                <small class="text-muted">Retail: {{ $currencySymbol }}{{ number_format($projectedRetailValue, 2) }} (Unrealized: +{{ $currencySymbol }}{{ number_format($unrealizedStockProfit, 2) }})</small>
            </div>
        </div>
    </div>
</div>

<!-- Chart -->
<div class="card mb-4">
    <div class="card-header"><h5 class="card-title mb-0">Revenue vs. Wholesale Cost vs. Net Profit ({{ $currencyCode }})</h5></div>
    <div class="card-body">
        <div id="financial-profit-chart" style="min-height: 280px;"></div>
    </div>
</div>

<!-- Product Profitability & Order Ledger -->
<div class="row">
    <div class="col-lg-6">
        <div class="card">
            <div class="card-header"><h5 class="card-title mb-0">Product Profitability Breakdown</h5></div>
            <div class="card-body p-0">
                <div class="table-responsive">
                    <table class="table table-hover align-middle mb-0">
                        <thead class="bg-light-subtle">
                            <tr>
                                <th>Product</th>
                                <th>Sold</th>
                                <th>Revenue</th>
                                <th>COGS</th>
                                <th>Profit</th>
                            </tr>
                        </thead>
                        <tbody>
                            @forelse($productProfitability as $row)
                                <tr>
                                    <td class="fw-medium">{{ $row['name'] }}</td>
                                    <td>{{ $row['qty'] }}</td>
                                    <td>{{ $currencySymbol }}{{ number_format($row['revenue'], 2) }}</td>
                                    <td class="text-muted">{{ $currencySymbol }}{{ number_format($row['cost'], 2) }}</td>
                                    <td>
                                        @php $prodProfit = (float) $row['profit']; @endphp
                                        <span class="badge {{ $prodProfit >= 0 ? 'bg-success-subtle text-success' : 'bg-danger-subtle text-danger' }}">{{ ($prodProfit >= 0 ? '+' : '-') . $currencySymbol }}{{ number_format(abs($prodProfit), 2) }} ({{ $row['margin'] }}%)</span>
                                    </td>
                                </tr>
                            @empty
                                <tr><td colspan="5" class="text-center text-muted py-3">No product sales in this filter range.</td></tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
    </div>

    <div class="col-lg-6">
        <div class="card">
            <div class="card-header"><h5 class="card-title mb-0">Filtered Order Profit Ledger</h5></div>
            <div class="card-body p-0">
                <div class="table-responsive">
                    <table class="table table-hover align-middle mb-0">
                        <thead class="bg-light-subtle">
                            <tr>
                                <th>Order #</th>
                                <th>Date</th>
                                <th>Total</th>
                                <th>COGS</th>
                                <th>Profit</th>
                            </tr>
                        </thead>
                        <tbody>
                            @forelse($orders as $o)
                                <tr>
                                    <td><a href="{{ route('admin.orders.show', $o->order_number) }}" class="fw-semibold text-primary">#{{ $o->order_number }}</a></td>
                                    <td class="fs-12 text-muted">{{ $o->date }}</td>
                                    <td>{{ $currencySymbol }}{{ number_format((float) $o->total, 2) }}</td>
                                    <td class="text-muted">{{ $currencySymbol }}{{ number_format((float) $o->total_cost, 2) }}</td>
                                    <td>
                                        @php $ordProfit = (float) $o->total_profit; @endphp
                                        <span class="badge {{ $ordProfit >= 0 ? 'bg-success-subtle text-success' : 'bg-danger-subtle text-danger' }}">{{ ($ordProfit >= 0 ? '+' : '-') . $currencySymbol }}{{ number_format(abs($ordProfit), 2) }}</span>
                                    </td>
                                </tr>
                            @empty
                                <tr><td colspan="5" class="text-center text-muted py-3">No orders match the selected filters.</td></tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
    </div>
</div>
<x-admin.filter-drawer
    id="financialFilterDrawer"
    title="Filter Financial Ledger"
    :action="route('admin.financial.index')"
    :reset-url="route('admin.financial.index')">
    <div class="mb-3">
        <label for="fin-filter-period" class="form-label fw-semibold">Period</label>
        <select id="fin-filter-period" name="period" class="form-select">
            <option value="all" {{ $period === 'all' ? 'selected' : '' }}>All Time</option>
            <option value="today" {{ $period === 'today' ? 'selected' : '' }}>Today</option>
            <option value="7d" {{ $period === '7d' ? 'selected' : '' }}>Last 7 Days</option>
            <option value="this_month" {{ $period === 'this_month' ? 'selected' : '' }}>This Month</option>
            <option value="last_month" {{ $period === 'last_month' ? 'selected' : '' }}>Last Month</option>
            <option value="this_year" {{ $period === 'this_year' ? 'selected' : '' }}>This Year</option>
            <option value="custom" {{ $period === 'custom' ? 'selected' : '' }}>Custom Range</option>
        </select>
    </div>

    <div class="row g-2 mb-3">
        <div class="col-6">
            <label for="fin-filter-from" class="form-label fw-semibold">From Date</label>
            <input type="date" id="fin-filter-from" name="date_from" value="{{ $dateFrom }}" class="form-control">
        </div>
        <div class="col-6">
            <label for="fin-filter-to" class="form-label fw-semibold">To Date</label>
            <input type="date" id="fin-filter-to" name="date_to" value="{{ $dateTo }}" class="form-control">
        </div>
    </div>

    <div class="mb-3">
        <label for="fin-filter-category" class="form-label fw-semibold">Category</label>
        <select id="fin-filter-category" name="category_id" class="form-select">
            <option value="">All Categories</option>
            @foreach($categories as $cat)
                <option value="{{ $cat->id }}" {{ (string) $categoryId === (string) $cat->id ? 'selected' : '' }}>{{ $cat->hierarchy_path }}</option>
            @endforeach
        </select>
    </div>

    <div class="mb-3">
        <label for="fin-filter-vendor" class="form-label fw-semibold">Vendor</label>
        <select id="fin-filter-vendor" name="vendor_id" class="form-select">
            <option value="">All Vendors</option>
            @foreach($vendors as $v)
                <option value="{{ $v->id }}" {{ (string) $vendorId === (string) $v->id ? 'selected' : '' }}>{{ $v->name }}</option>
            @endforeach
        </select>
    </div>

    <div class="mb-3">
        <label for="fin-filter-status" class="form-label fw-semibold">Order Status</label>
        <select id="fin-filter-status" name="status" class="form-select">
            <option value="all_valid" {{ $status === 'all_valid' ? 'selected' : '' }}>All Valid</option>
            <option value="Delivered" {{ $status === 'Delivered' ? 'selected' : '' }}>Delivered</option>
            <option value="Shipped" {{ $status === 'Shipped' ? 'selected' : '' }}>Shipped</option>
            <option value="Processing" {{ $status === 'Processing' ? 'selected' : '' }}>Processing</option>
            <option value="Placed" {{ $status === 'Placed' ? 'selected' : '' }}>Placed</option>
        </select>
    </div>
</x-admin.filter-drawer>
@endsection

@push('scripts')
<script>
document.addEventListener('DOMContentLoaded', function () {
    const el = document.querySelector('#financial-profit-chart');
    if (el && typeof ApexCharts !== 'undefined') {
        new ApexCharts(el, {
            chart: { type: 'bar', height: 280, toolbar: { show: false } },
            series: [
                { name: 'Net Revenue', data: @json($chartRevenue) },
                { name: 'Wholesale COGS', data: @json($chartCost) },
                { name: 'Net Profit', data: @json($chartProfit) }
            ],
            xaxis: { categories: @json($chartLabels) },
            colors: ['#304C3D', '#A8B7A0', '#22c55e'],
            dataLabels: { enabled: false }
        }).render();
    }
});
</script>
@endpush

