@extends('layouts.admin')

@section('title', 'Wholesale Purchases & Profit Margins')

@section('content')
@php
    $purchaseFilterCount = collect([
        request('search'),
        request('vendor_id'),
        request('date_from'),
        request('date_to'),
    ])->filter()->count();
@endphp
<div class="row mb-3">
    <div class="col-12 d-flex flex-wrap justify-content-between align-items-center gap-2">
        <div class="d-flex flex-wrap align-items-center gap-2">
            <div>
                <h4 class="fw-bold mb-1">Wholesale Purchases &amp; Costing</h4>
                <p class="text-muted mb-0 fs-13">Track vendor purchase batches, available-stock weighted average buy prices, and unit profit margins.</p>
            </div>
            @if($purchaseFilterCount > 0)
                <a href="{{ route('admin.purchases.index') }}" class="badge bg-soft-danger text-danger text-decoration-none fs-11">
                    <i class="bx bx-x align-middle"></i> Clear Filters
                </a>
            @endif
        </div>
        <div class="d-flex flex-wrap align-items-center gap-2">
            <button type="button" class="btn btn-sm btn-soft-secondary d-flex align-items-center gap-1 position-relative" data-bs-toggle="offcanvas" data-bs-target="#purchaseFilterDrawer" aria-controls="purchaseFilterDrawer">
                <iconify-icon icon="solar:filter-bold-duotone" class="fs-16"></iconify-icon>
                <span>Filter</span>
                @if($purchaseFilterCount > 0)
                    <span class="badge bg-primary rounded-pill ms-1">{{ $purchaseFilterCount }}</span>
                @endif
            </button>
            <a href="{{ route('admin.purchases.create') }}" class="btn btn-primary btn-sm">
                + Record Wholesale Batch
            </a>
        </div>
    </div>
</div>

<!-- Product Wholesale Cost & Profit Table -->
<div class="card mb-4">
    <div class="card-header">
        <h5 class="card-title mb-0">Product Weighted Average Buy Price (Available Stock) &amp; Profit</h5>
    </div>
    <div class="card-body p-0">
        <div class="table-responsive">
            <table class="table table-hover align-middle mb-0">
                <thead class="bg-light-subtle">
                    <tr>
                        <th>Product</th>
                        <th>Vendor Name</th>
                        <th>Batch Lot</th>
                        <th>Available Stock</th>
                        <th>Last Buy Price</th>
                        <th>Weighted Avg Buy Price</th>
                        <th>Selling Price</th>
                        <th>Unit Profit</th>
                    </tr>
                </thead>
                <tbody>
                    @foreach($products as $item)
                        <tr>
                            <td>
                                <span class="fw-semibold text-dark">{{ $item['name'] }}</span>
                                <div class="text-muted fs-12 font-monospace">{{ $item['sku'] }}</div>
                            </td>
                            <td>{{ $item['vendor_name'] }}</td>
                            <td><span class="badge bg-light text-dark font-monospace">{{ $item['batch_lot'] }}</span></td>
                            <td>{{ $item['stock_count'] }}</td>
                            <td>{{ $currencySymbol }}{{ number_format($item['last_buy_price'], 2) }}</td>
                            <td class="fw-semibold text-primary">{{ $currencySymbol }}{{ number_format($item['avg_buy_price'], 2) }}</td>
                            <td>{{ $currencySymbol }}{{ number_format($item['sell_price'], 2) }}</td>
                            <td>
                                <span class="badge {{ $item['unit_profit'] >= 0 ? 'bg-success-subtle text-success' : 'bg-danger-subtle text-danger' }}">
                                    {{ ($item['unit_profit'] >= 0 ? '+' : '-') . $currencySymbol }}{{ number_format(abs($item['unit_profit']), 2) }} ({{ $item['margin_pct'] }}%)
                                </span>
                            </td>
                        </tr>
                    @endforeach
                </tbody>
            </table>
        </div>
    </div>
</div>

<!-- Recent Wholesale Purchase Batches -->
<div class="card">
    <div class="card-header">
        <h5 class="card-title mb-0">Purchase Batch History</h5>
    </div>
    <div class="card-body p-0">
        <div class="table-responsive">
            <table class="table table-hover align-middle mb-0">
                <thead class="bg-light-subtle">
                    <tr>
                        <th>Batch Code</th>
                        <th>Vendor Name</th>
                        <th>Reference</th>
                        <th>Purchase Date</th>
                        <th>Total Qty</th>
                        <th>Total Cost</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse($batches as $batch)
                        <tr>
                            <td class="fw-semibold font-monospace text-primary">{{ $batch->batch_code }}</td>
                            <td>{{ $batch->vendor_name }}</td>
                            <td>{{ $batch->reference_no ?? '—' }}</td>
                            <td>{{ $batch->purchased_at?->format('M d, Y') }}</td>
                            <td>{{ $batch->total_qty }} units</td>
                            <td class="fw-semibold">{{ $currencySymbol }}{{ number_format((float) $batch->total_cost, 2) }}</td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="6" class="text-center text-muted py-4">No wholesale purchase batches recorded yet.</td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>
</div>
<x-admin.filter-drawer
    id="purchaseFilterDrawer"
    title="Filter Wholesale Purchases"
    :action="route('admin.purchases.index')"
    :reset-url="route('admin.purchases.index')">
    <div class="mb-3">
        <label for="pur-filter-search" class="form-label fw-semibold">Search</label>
        <input type="text" id="pur-filter-search" name="search" class="form-control" placeholder="Product, SKU, batch code, reference..." value="{{ request('search') }}">
    </div>

    <div class="mb-3">
        <label for="pur-filter-vendor" class="form-label fw-semibold">Vendor</label>
        <select id="pur-filter-vendor" name="vendor_id" class="form-select">
            <option value="">All Vendors</option>
            @foreach($vendors as $v)
                <option value="{{ $v->id }}" {{ (string) request('vendor_id') === (string) $v->id ? 'selected' : '' }}>{{ $v->name }}</option>
            @endforeach
        </select>
    </div>

    <div class="row g-2 mb-3">
        <div class="col-6">
            <label for="pur-filter-from" class="form-label fw-semibold">From Date</label>
            <input type="date" id="pur-filter-from" name="date_from" value="{{ request('date_from') }}" class="form-control">
        </div>
        <div class="col-6">
            <label for="pur-filter-to" class="form-label fw-semibold">To Date</label>
            <input type="date" id="pur-filter-to" name="date_to" value="{{ request('date_to') }}" class="form-control">
        </div>
    </div>
</x-admin.filter-drawer>
@endsection

