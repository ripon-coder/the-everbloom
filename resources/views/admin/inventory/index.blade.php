@extends('layouts.admin')

@section('title', 'Warehouse Inventory')
@section('container_class', 'container-xxl')

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
                                <th>Botanical Formulation</th>
                                <th>SKU</th>
                                <th>Batch / Lot No.</th>
                                <th>Category</th>
                                <th>Available Stock</th>
                                <th>Avg Buy Price</th>
                                <th>Selling Price</th>
                                <th>Unit Profit</th>
                                <th>Alert Threshold</th>
                                <th>Status</th>
                                <th>Last Harvest/Restock</th>
                                <th class="text-end">Adjust Stock</th>
                            </tr>
                        </thead>
                        <tbody>
                            @forelse($inventory as $item)
                            <tr>
                                <td>
                                    <div class="d-flex align-items-center gap-3">
                                        <div class="avatar-sm bg-light rounded d-flex align-items-center justify-content-center overflow-hidden border">
                                            <img src="{{ app(\App\Services\ImageStorageService::class)->resolveUrl($item['image'] ?? 'products/botanical-radiance-serum-01.jpg') }}" alt="" class="avatar-sm object-fit-cover">
                                        </div>
                                        <a href="{{ route('admin.products.edit', $item['id']) }}" class="text-dark fw-semibold fs-14">
                                            {{ $item['name'] }}
                                        </a>
                                    </div>
                                </td>
                                <td>
                                    <span class="font-monospace text-muted fs-13">{{ $item['sku'] }}</span>
                                </td>
                                <td>
                                    <span class="badge bg-light text-dark border font-monospace">{{ $item['batch_lot'] }}</span>
                                </td>
                                <td>
                                    <span class="badge bg-secondary-subtle text-secondary">{{ $item['category'] }}</span>
                                </td>
                                <td>
                                    <span class="fw-bold fs-14 text-dark">{{ $item['stock'] }} units</span>
                                </td>
                                <td>
                                    <span class="fs-13 text-muted">{{ $currencySymbol }}{{ number_format($item['avg_buy_price'] ?? 0, 2) }}</span>
                                </td>
                                <td>
                                    <span class="fs-13 text-dark">{{ $currencySymbol }}{{ number_format($item['sell_price'] ?? 0, 2) }}</span>
                                </td>
                                <td>
                                    @php $invUnitProfit = (float) ($item['unit_profit'] ?? 0); @endphp
                                    <span class="badge {{ $invUnitProfit >= 0 ? 'bg-success-subtle text-success' : 'bg-danger-subtle text-danger' }}">{{ ($invUnitProfit >= 0 ? '+' : '-') . $currencySymbol }}{{ number_format(abs($invUnitProfit), 2) }}</span>
                                </td>
                                <td>
                                    <span class="text-muted fs-13">{{ $item['min_threshold'] }} units</span>
                                </td>
                                <td>
                                    <span class="badge bg-{{ $item['status_badge'] }}-subtle text-{{ $item['status_badge'] }} px-2 py-1 fs-12">
                                        {{ $item['status'] }}
                                    </span>
                                </td>
                                <td>
                                    <span class="text-muted fs-13">{{ $item['last_restocked'] }}</span>
                                </td>
                                <td class="text-end">
                                    <form action="{{ route('admin.inventory.adjust', $item['id']) }}" method="POST" class="d-inline-flex gap-1 align-items-center">
                                        @csrf
                                        <input type="number" name="stock_count" value="{{ $item['stock'] }}" min="0" class="form-control form-control-sm" style="width:72px;" title="Stock count">
                                        <input type="number" name="min_threshold" value="{{ $item['min_threshold'] }}" min="0" class="form-control form-control-sm" style="width:62px;" title="Alert threshold">
                                        <button type="submit" class="btn btn-sm btn-soft-primary">Save</button>
                                    </form>
                                    @if(!empty($item['variants']))
                                        <div class="mt-2 pt-2 border-top d-flex flex-column gap-1 align-items-end">
                                            @foreach($item['variants'] as $variant)
                                                <form action="{{ route('admin.inventory.adjustVariant', $variant['id']) }}" method="POST" class="d-inline-flex gap-1 align-items-center">
                                                    @csrf
                                                    <span class="text-muted fs-11 me-1">{{ $variant['title'] }}:</span>
                                                    <input type="number" name="stock_count" value="{{ $variant['stock_count'] }}" min="0" class="form-control form-control-sm" style="width:64px;" title="Variant stock">
                                                    <button type="submit" class="btn btn-sm btn-soft-secondary">Set</button>
                                                </form>
                                            @endforeach
                                        </div>
                                    @endif
                                </td>
                            </tr>
                            @empty
                            <tr>
                                <td colspan="12" class="text-center py-4 text-muted">No inventory records found.</td>
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
