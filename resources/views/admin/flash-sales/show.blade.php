@extends('layouts.admin')

@section('title', 'Flash Sale Analytics | The Everbloom Admin')

@section('content')
<div class="row">
    <div class="col-12">
        <div class="page-title-box">
            <h4 class="page-title">{{ $flashSale->title }} — Analytics</h4>
            <div class="page-title-right d-flex gap-2">
                <a href="{{ route('admin.flash-sales.products', $flashSale) }}" class="btn btn-primary btn-sm">Manage Products</a>
                <a href="{{ route('admin.flash-sales.index') }}" class="btn btn-soft-secondary btn-sm">Back to List</a>
            </div>
        </div>
    </div>
</div>

<div class="row">
    <div class="col-md-3">
        <div class="card"><div class="card-body">
            <p class="text-muted mb-1">Flash Revenue</p>
            <h4 class="mb-0">@money((float) ($orderStats->revenue ?? 0))</h4>
        </div></div>
    </div>
    <div class="col-md-3">
        <div class="card"><div class="card-body">
            <p class="text-muted mb-1">Estimated Profit</p>
            <h4 class="mb-0 text-success">@money((float) ($orderStats->profit ?? 0))</h4>
        </div></div>
    </div>
    <div class="col-md-3">
        <div class="card"><div class="card-body">
            <p class="text-muted mb-1">Units Sold / Cap</p>
            <h4 class="mb-0">{{ $totalUnitsSold }} / {{ $totalSaleStock }}</h4>
        </div></div>
    </div>
    <div class="col-md-3">
        <div class="card"><div class="card-body">
            <p class="text-muted mb-1">Orders Count</p>
            <h4 class="mb-0">{{ (int) ($orderStats->orders_count ?? 0) }}</h4>
        </div></div>
    </div>
</div>

<div class="card">
    <div class="card-header"><h5 class="card-title mb-0">Product Sell-Through</h5></div>
    <div class="card-body p-0">
        <table class="table mb-0 align-middle">
            <thead>
                <tr>
                    <th>Product</th>
                    <th>Sale Price</th>
                    <th>Regular Price</th>
                    <th>Discount</th>
                    <th>Sold / Stock</th>
                    <th>Remaining</th>
                </tr>
            </thead>
            <tbody>
                @forelse($flashSale->items as $item)
                    @php
                        $regular = $item->variant ? (float) $item->variant->price : (float) ($item->product?->price ?? 0);
                    @endphp
                    <tr>
                        <td>{{ $item->product?->name ?? 'Deleted Product' }}</td>
                        <td>@money($item->sale_price)</td>
                        <td class="text-muted text-decoration-line-through">@money($regular)</td>
                        <td><span class="badge bg-danger-subtle text-danger">-{{ $item->discountPercent() }}%</span></td>
                        <td>{{ $item->sold_qty }} / {{ $item->sale_stock }}</td>
                        <td>{{ $item->remainingStock() }}</td>
                    </tr>
                @empty
                    <tr><td colspan="6" class="text-center py-3 text-muted">No products assigned yet.</td></tr>
                @endforelse
            </tbody>
        </table>
    </div>
</div>
@endsection

