@extends('layouts.admin')

@section('title', 'Wholesale Purchases & Profit Margins')

@section('content')
<div class="row mb-3">
    <div class="col-12 d-flex flex-wrap justify-content-between align-items-center gap-2">
        <div>
            <h4 class="fw-bold mb-1">Wholesale Purchases &amp; Costing</h4>
            <p class="text-muted mb-0 fs-13">Track vendor purchase batches, available-stock weighted average buy prices, and unit profit margins.</p>
        </div>
        <a href="{{ route('admin.purchases.create') }}" class="btn btn-primary btn-sm">
            + Record Wholesale Batch
        </a>
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
                            <td>${{ number_format($item['last_buy_price'], 2) }}</td>
                            <td class="fw-semibold text-primary">${{ number_format($item['avg_buy_price'], 2) }}</td>
                            <td>${{ number_format($item['sell_price'], 2) }}</td>
                            <td>
                                <span class="badge {{ $item['unit_profit'] >= 0 ? 'bg-success-subtle text-success' : 'bg-danger-subtle text-danger' }}">
                                    +${{ number_format($item['unit_profit'], 2) }} ({{ $item['margin_pct'] }}%)
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
                            <td class="fw-semibold">${{ number_format((float) $batch->total_cost, 2) }}</td>
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
@endsection

