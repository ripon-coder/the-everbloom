@extends('layouts.admin')

@section('title', 'Warehouse Inventory')
@section('container_class', 'container-fluid')

@section('content')
@php
    $invCollection = collect($inventory);
    $totalUnits = $invCollection->sum('stock');
    $totalSkus = $invCollection->count();
    $healthyCount = $invCollection->filter(fn($i) => $i['stock'] > $i['min_threshold'])->count();
    $lowStockCount = $invCollection->filter(fn($i) => $i['stock'] > 0 && $i['stock'] <= $i['min_threshold'])->count();
    $outOfStockCount = $invCollection->filter(fn($i) => $i['stock'] <= 0)->count();
@endphp
<!-- Row 1: Stock Health Cards -->
<div class="row">
    <div class="col-md-6 col-xl-3">
        <div class="card">
            <div class="card-body">
                <div class="d-flex align-items-center justify-content-between">
                    <div>
                        <h4 class="card-title mb-2">Total Units</h4>
                        <p class="text-muted fw-medium fs-22 mb-0">{{ $totalUnits }}</p>
                    </div>
                    <div>
                        <div class="avatar-md bg-primary bg-opacity-10 rounded">
                            <iconify-icon icon="solar:box-bold-duotone" class="fs-32 text-primary avatar-title"></iconify-icon>
                        </div>
                    </div>
                </div>
            </div>
            <div class="card-footer py-2 bg-light bg-opacity-50">
                <span class="text-muted fs-12">Across {{ $totalSkus }} Active SKUs</span>
            </div>
        </div>
    </div>

    <div class="col-md-6 col-xl-3">
        <div class="card">
            <div class="card-body">
                <div class="d-flex align-items-center justify-content-between">
                    <div>
                        <h4 class="card-title mb-2">In Healthy Stock</h4>
                        <p class="text-muted fw-medium fs-22 mb-0">{{ $healthyCount }} Formulations</p>
                    </div>
                    <div>
                        <div class="avatar-md bg-success bg-opacity-10 rounded">
                            <iconify-icon icon="solar:check-circle-bold-duotone" class="fs-32 text-success avatar-title"></iconify-icon>
                        </div>
                    </div>
                </div>
            </div>
            <div class="card-footer py-2 bg-light bg-opacity-50">
                <span class="text-success fs-12">Above reorder threshold</span>
            </div>
        </div>
    </div>

    <div class="col-md-6 col-xl-3">
        <div class="card">
            <div class="card-body">
                <div class="d-flex align-items-center justify-content-between">
                    <div>
                        <h4 class="card-title mb-2">Low Stock Alert</h4>
                        <p class="text-muted fw-medium fs-22 mb-0">{{ $lowStockCount }} Batches</p>
                    </div>
                    <div>
                        <div class="avatar-md bg-warning bg-opacity-10 rounded">
                            <iconify-icon icon="solar:danger-triangle-bold-duotone" class="fs-32 text-warning avatar-title"></iconify-icon>
                        </div>
                    </div>
                </div>
            </div>
            <div class="card-footer py-2 bg-light bg-opacity-50">
                <span class="text-warning fs-12">At or below reorder threshold</span>
            </div>
        </div>
    </div>

    <div class="col-md-6 col-xl-3">
        <div class="card">
            <div class="card-body">
                <div class="d-flex align-items-center justify-content-between">
                    <div>
                        <h4 class="card-title mb-2">Out of Stock</h4>
                        <p class="text-muted fw-medium fs-22 mb-0">{{ $outOfStockCount }} Batches</p>
                    </div>
                    <div>
                        <div class="avatar-md bg-secondary bg-opacity-10 rounded">
                            <iconify-icon icon="solar:close-circle-bold-duotone" class="fs-32 text-secondary avatar-title"></iconify-icon>
                        </div>
                    </div>
                </div>
            </div>
            <div class="card-footer py-2 bg-light bg-opacity-50">
                <span class="text-muted fs-12">{{ $outOfStockCount === 0 ? 'All SKUs available' : 'Restock required' }}</span>
            </div>
        </div>
    </div>
</div>

<!-- Row 2: Inventory Table -->
<div class="row">
    <div class="col-12">
        <div class="card">
            @php
                $inventoryFilterCount = collect([
                    request('search'),
                    request('category'),
                    request('stock_status'),
                    request('sort') && request('sort') !== 'default' ? request('sort') : null,
                ])->filter()->count();
            @endphp
            <div class="card-header d-flex flex-wrap justify-content-between align-items-center gap-2">
                <div class="d-flex flex-wrap align-items-center gap-2">
                    <h4 class="card-title mb-0">Batch Stock &amp; Laboratory Inventory ({{ count($inventory) }})</h4>
                    @if($inventoryFilterCount > 0)
                        <a href="{{ route('admin.inventory.index') }}" class="badge bg-soft-danger text-danger text-decoration-none fs-11">
                            <i class="bx bx-x align-middle"></i> Clear Filters
                        </a>
                    @endif
                </div>
                <div class="d-flex flex-wrap align-items-center gap-2">
                    <button type="button" class="btn btn-sm btn-soft-secondary d-flex align-items-center gap-1 position-relative" data-bs-toggle="offcanvas" data-bs-target="#inventoryFilterDrawer" aria-controls="inventoryFilterDrawer">
                        <iconify-icon icon="solar:filter-bold-duotone" class="fs-16"></iconify-icon>
                        <span>Filter</span>
                        @if($inventoryFilterCount > 0)
                            <span class="badge bg-primary rounded-pill ms-1">{{ $inventoryFilterCount }}</span>
                        @endif
                    </button>
                    <a href="{{ route('admin.purchases.create') }}" class="btn btn-sm btn-primary d-flex align-items-center gap-1">
                        <iconify-icon icon="solar:add-circle-bold" class="fs-16"></iconify-icon>
                        <span>Receive New Batch</span>
                    </a>
                </div>
            </div>
            <div class="card-body p-0">
                <div class="table-responsive">
                    <table class="table align-middle mb-0 table-hover table-centered">
                        <thead class="bg-light-subtle">
                            <tr>
                                <th class="ps-3">Formulation</th>
                                <th>Batch / Restock</th>
                                <th>Pricing &amp; Margin</th>
                                <th>Stock Health</th>
                                <th class="text-end pe-3">Adjust Stock</th>
                            </tr>
                        </thead>
                        <tbody>
                            @forelse($inventory as $item)
                            <tr>
                                <td class="ps-3">
                                    <div class="d-flex align-items-center gap-3">
                                        <div class="rounded bg-light avatar-md d-flex align-items-center justify-content-center overflow-hidden border flex-shrink-0">
                                            <img src="{{ app(\App\Services\ImageStorageService::class)->resolveUrl($item['image'] ?? 'products/botanical-radiance-serum-01.jpg') }}" alt="{{ $item['name'] }}" class="avatar-md object-fit-cover">
                                        </div>
                                        <div>
                                            <a href="{{ route('admin.products.edit', $item['id']) }}" class="text-dark fw-semibold fs-14 d-block">
                                                {{ $item['name'] }}
                                            </a>
                                            <div class="d-flex align-items-center gap-2 mt-1">
                                                <span class="text-muted fs-12 font-monospace text-nowrap">{{ $item['sku'] }}</span>
                                                <span class="badge bg-secondary-subtle text-secondary fs-11">{{ $item['category'] }}</span>
                                            </div>
                                        </div>
                                    </div>
                                </td>
                                <td class="text-nowrap">
                                    <span class="badge bg-light text-dark border font-monospace fs-12">{{ $item['batch_lot'] }}</span>
                                    <p class="text-muted mb-0 mt-1 fs-12">{{ $item['last_restocked'] }}</p>
                                </td>
                                <td class="text-nowrap">
                                    <div class="d-flex align-items-center gap-2">
                                        <span class="fw-bold text-dark fs-14">{{ $currencySymbol }}{{ number_format($item['sell_price'] ?? 0, 2) }}</span>
                                        @php $invUnitProfit = (float) ($item['unit_profit'] ?? 0); @endphp
                                        <span class="badge {{ $invUnitProfit >= 0 ? 'bg-success-subtle text-success' : 'bg-danger-subtle text-danger' }} fs-11">
                                            {{ ($invUnitProfit >= 0 ? '+' : '-') . $currencySymbol }}{{ number_format(abs($invUnitProfit), 2) }}
                                        </span>
                                    </div>
                                    <p class="text-muted mb-0 mt-1 fs-12">Buy: {{ $currencySymbol }}{{ number_format($item['avg_buy_price'] ?? 0, 2) }}</p>
                                </td>
                                <td class="text-nowrap">
                                    <div class="d-flex align-items-center gap-2">
                                        <span class="fw-bold fs-14 text-dark">{{ $item['stock'] }} units</span>
                                        <span class="badge bg-{{ $item['status_badge'] }}-subtle text-{{ $item['status_badge'] }} fs-11">
                                            {{ $item['status'] }}
                                        </span>
                                    </div>
                                    <p class="text-muted mb-0 mt-1 fs-12">Min threshold: {{ $item['min_threshold'] }} units</p>
                                </td>
                                <td class="text-end pe-3">
                                    <div class="d-inline-flex flex-wrap align-items-center justify-content-end gap-2">
                                        <form action="{{ route('admin.inventory.adjust', $item['id']) }}" method="POST" class="d-inline-flex align-items-center">
                                            @csrf
                                            <div class="input-group input-group-sm" style="width: 215px;">
                                                <input type="number" name="stock_count" value="{{ $item['stock'] }}" min="0" class="form-control text-center" title="Available stock" aria-label="Stock count">
                                                <input type="number" name="min_threshold" value="{{ $item['min_threshold'] }}" min="0" class="form-control text-center" title="Alert threshold" aria-label="Alert threshold">
                                                <button type="submit" class="btn btn-soft-primary fw-medium">Save</button>
                                            </div>
                                        </form>
                                        @if(!empty($item['variants']))
                                            <button type="button" class="btn btn-sm btn-soft-secondary text-nowrap" data-bs-toggle="collapse" data-bs-target="#inv-variants-{{ $item['id'] }}" aria-expanded="false">
                                                <i class="bx bx-layer me-1"></i>Variants ({{ count($item['variants']) }})
                                            </button>
                                        @endif
                                    </div>
                                    @if(!empty($item['variants']))
                                        <div class="collapse mt-2" id="inv-variants-{{ $item['id'] }}">
                                            <div class="p-2 rounded bg-light bg-opacity-75 border d-inline-flex flex-column gap-1 text-start">
                                                @foreach($item['variants'] as $variant)
                                                    <form action="{{ route('admin.inventory.adjustVariant', $variant['id']) }}" method="POST" class="d-flex align-items-center justify-content-between gap-2 text-nowrap">
                                                        @csrf
                                                        <span class="text-muted fs-12 fw-medium">{{ $variant['title'] }}</span>
                                                        <div class="input-group input-group-sm" style="width: 120px;">
                                                            <input type="number" name="stock_count" value="{{ $variant['stock_count'] }}" min="0" class="form-control text-center" title="Variant stock">
                                                            <button type="submit" class="btn btn-soft-secondary">Set</button>
                                                        </div>
                                                    </form>
                                                @endforeach
                                            </div>
                                        </div>
                                    @endif
                                </td>
                            </tr>
                            @empty
                            <tr>
                                <td colspan="5" class="text-center py-4 text-muted">No inventory records found.</td>
                            </tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>
            </div>
            <div class="card-footer py-2 bg-light bg-opacity-50">
                <p class="mb-0 text-muted fs-12">Warehouse location: Primary Botanical Fulfillment Center</p>
            </div>
        </div>
    </div>
</div>

@if(!empty($lowStockItems))
<!-- Row 3: Low-Stock Alerts Widget -->
<div class="row mt-3">
    <div class="col-12">
        <div class="card border-warning">
            <div class="card-header bg-warning-subtle d-flex justify-content-between align-items-center">
                <h5 class="card-title mb-0 text-warning-emphasis">Low-Stock &amp; Out-of-Stock Alerts ({{ count($lowStockItems) }})</h5>
            </div>
            <div class="card-body p-0">
                <div class="table-responsive">
                    <table class="table align-middle mb-0 table-sm">
                        <thead class="bg-light-subtle">
                            <tr>
                                <th>Product</th>
                                <th>SKU</th>
                                <th>Available</th>
                                <th>Threshold</th>
                                <th>Status</th>
                            </tr>
                        </thead>
                        <tbody>
                            @foreach($lowStockItems as $alertItem)
                            <tr>
                                <td class="fw-medium">{{ $alertItem['name'] }}</td>
                                <td class="font-monospace fs-12">{{ $alertItem['sku'] }}</td>
                                <td class="fw-bold">{{ $alertItem['stock'] }}</td>
                                <td>{{ $alertItem['min_threshold'] }}</td>
                                <td><span class="badge bg-{{ $alertItem['status_badge'] }}-subtle text-{{ $alertItem['status_badge'] }}">{{ $alertItem['status'] }}</span></td>
                            </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
    </div>
</div>
@endif

<!-- Row 4: Stock Movement History -->
<div class="row mt-3">
    <div class="col-12">
        <div class="card">
            <div class="card-header">
                <h5 class="card-title mb-0">Recent Stock Movements</h5>
            </div>
            <div class="card-body p-0">
                <div class="table-responsive">
                    <table class="table align-middle mb-0 table-hover table-sm">
                        <thead class="bg-light-subtle">
                            <tr>
                                <th>Date</th>
                                <th>Product / Variant</th>
                                <th>Delta</th>
                                <th>Reason</th>
                                <th>Note</th>
                                <th>Admin</th>
                            </tr>
                        </thead>
                        <tbody>
                            @forelse($stockMovements ?? [] as $movement)
                            <tr>
                                <td class="text-muted fs-12">{{ $movement->created_at?->format('M d, Y H:i') }}</td>
                                <td>
                                    <span class="fw-medium">{{ $movement->product?->name ?? ('Product #' . $movement->product_id) }}</span>
                                    @if($movement->variant)
                                        <small class="text-muted d-block">{{ $movement->variant->title ?: $movement->variant->size }}</small>
                                    @endif
                                </td>
                                <td>
                                    <span class="badge {{ $movement->delta >= 0 ? 'bg-success-subtle text-success' : 'bg-danger-subtle text-danger' }}">
                                        {{ $movement->delta >= 0 ? '+' . $movement->delta : $movement->delta }}
                                    </span>
                                </td>
                                <td class="font-monospace fs-12">{{ $movement->reason }}</td>
                                <td class="text-muted fs-12">{{ $movement->note }}</td>
                                <td class="text-muted fs-12">{{ $movement->admin?->name ?? 'System' }}</td>
                            </tr>
                            @empty
                            <tr>
                                <td colspan="6" class="text-center py-3 text-muted">No stock movements recorded yet.</td>
                            </tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
    </div>
</div>
<x-admin.filter-drawer
    id="inventoryFilterDrawer"
    title="Filter Inventory"
    :action="route('admin.inventory.index')"
    :reset-url="route('admin.inventory.index')">
    <div class="mb-3">
        <label for="inv-filter-search" class="form-label fw-semibold">Search</label>
        <input type="text" id="inv-filter-search" name="search" class="form-control" placeholder="Product name, SKU, or batch lot..." value="{{ request('search') }}">
    </div>

    <div class="mb-3">
        <label for="inv-filter-category" class="form-label fw-semibold">Category</label>
        <select id="inv-filter-category" name="category" class="form-select">
            <option value="">All Categories</option>
            @foreach($categories as $cat)
                <option value="{{ $cat->slug }}" {{ request('category') === $cat->slug ? 'selected' : '' }}>{{ $cat->name }}</option>
            @endforeach
        </select>
    </div>

    <div class="mb-3">
        <label for="inv-filter-status" class="form-label fw-semibold">Stock Health</label>
        <select id="inv-filter-status" name="stock_status" class="form-select">
            <option value="">All Stock Levels</option>
            <option value="in_stock" {{ request('stock_status') === 'in_stock' ? 'selected' : '' }}>In Stock</option>
            <option value="low_stock" {{ request('stock_status') === 'low_stock' ? 'selected' : '' }}>Low Stock</option>
            <option value="out_of_stock" {{ request('stock_status') === 'out_of_stock' ? 'selected' : '' }}>Out of Stock</option>
        </select>
    </div>

    <div class="mb-3">
        <label for="inv-filter-sort" class="form-label fw-semibold">Sort By</label>
        <select id="inv-filter-sort" name="sort" class="form-select">
            <option value="default" {{ request('sort', 'default') === 'default' ? 'selected' : '' }}>Default Order</option>
            <option value="stock_asc" {{ request('sort') === 'stock_asc' ? 'selected' : '' }}>Stock: Low → High</option>
            <option value="stock_desc" {{ request('sort') === 'stock_desc' ? 'selected' : '' }}>Stock: High → Low</option>
        </select>
    </div>
</x-admin.filter-drawer>
@endsection
