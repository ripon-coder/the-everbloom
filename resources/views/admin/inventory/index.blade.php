@extends('layouts.admin')

@section('title', 'Warehouse Inventory')
@section('container_class', 'container-xxl')

@section('content')
<!-- Row 1: Stock Health Cards -->
<div class="row">
    <div class="col-md-6 col-xl-3">
        <div class="card">
            <div class="card-body">
                <div class="d-flex align-items-center justify-content-between">
                    <div>
                        <h4 class="card-title mb-2">Total Units</h4>
                        <p class="text-muted fw-medium fs-22 mb-0">384</p>
                    </div>
                    <div>
                        <div class="avatar-md bg-primary bg-opacity-10 rounded">
                            <iconify-icon icon="solar:box-bold-duotone" class="fs-32 text-primary avatar-title"></iconify-icon>
                        </div>
                    </div>
                </div>
            </div>
            <div class="card-footer py-2 bg-light bg-opacity-50">
                <span class="text-muted fs-12">Across 12 Active SKUs</span>
            </div>
        </div>
    </div>

    <div class="col-md-6 col-xl-3">
        <div class="card">
            <div class="card-body">
                <div class="d-flex align-items-center justify-content-between">
                    <div>
                        <h4 class="card-title mb-2">In Healthy Stock</h4>
                        <p class="text-muted fw-medium fs-22 mb-0">9 Formulations</p>
                    </div>
                    <div>
                        <div class="avatar-md bg-success bg-opacity-10 rounded">
                            <iconify-icon icon="solar:check-circle-bold-duotone" class="fs-32 text-success avatar-title"></iconify-icon>
                        </div>
                    </div>
                </div>
            </div>
            <div class="card-footer py-2 bg-light bg-opacity-50">
                <span class="text-success fs-12">&gt; 15 units available</span>
            </div>
        </div>
    </div>

    <div class="col-md-6 col-xl-3">
        <div class="card">
            <div class="card-body">
                <div class="d-flex align-items-center justify-content-between">
                    <div>
                        <h4 class="card-title mb-2">Low Stock Alert</h4>
                        <p class="text-muted fw-medium fs-22 mb-0">3 Batches</p>
                    </div>
                    <div>
                        <div class="avatar-md bg-warning bg-opacity-10 rounded">
                            <iconify-icon icon="solar:danger-triangle-bold-duotone" class="fs-32 text-warning avatar-title"></iconify-icon>
                        </div>
                    </div>
                </div>
            </div>
            <div class="card-footer py-2 bg-light bg-opacity-50">
                <span class="text-warning fs-12">Below 10 unit reorder threshold</span>
            </div>
        </div>
    </div>

    <div class="col-md-6 col-xl-3">
        <div class="card">
            <div class="card-body">
                <div class="d-flex align-items-center justify-content-between">
                    <div>
                        <h4 class="card-title mb-2">Out of Stock</h4>
                        <p class="text-muted fw-medium fs-22 mb-0">0 Batches</p>
                    </div>
                    <div>
                        <div class="avatar-md bg-secondary bg-opacity-10 rounded">
                            <iconify-icon icon="solar:close-circle-bold-duotone" class="fs-32 text-secondary avatar-title"></iconify-icon>
                        </div>
                    </div>
                </div>
            </div>
            <div class="card-footer py-2 bg-light bg-opacity-50">
                <span class="text-muted fs-12">All SKUs available</span>
            </div>
        </div>
    </div>
</div>

<!-- Row 2: Inventory Table -->
<div class="row">
    <div class="col-12">
        <div class="card">
            <div class="card-header d-flex justify-content-between align-items-center">
                <h4 class="card-title mb-0">Batch Stock & Laboratory Inventory</h4>
                <a href="{{ route('admin.products.create') }}" class="btn btn-sm btn-primary">
                    <iconify-icon icon="solar:add-circle-bold" class="me-1"></iconify-icon> Receive New Batch
                </a>
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
                                <th>Alert Threshold</th>
                                <th>Status</th>
                                <th>Last Harvest/Restock</th>
                            </tr>
                        </thead>
                        <tbody>
                            @forelse($inventory as $item)
                            <tr>
                                <td>
                                    <div class="d-flex align-items-center gap-3">
                                        <div class="avatar-sm bg-light rounded d-flex align-items-center justify-content-center overflow-hidden border">
                                            <img src="{{ asset('images/' . $item['image']) }}" alt="" class="avatar-sm object-fit-cover">
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
                            </tr>
                            @empty
                            <tr>
                                <td colspan="8" class="text-center py-4 text-muted">No inventory records found.</td>
                            </tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>
            </div>
            <div class="card-footer py-2 bg-light bg-opacity-50">
                <p class="mb-0 text-muted fs-12">Warehouse location: Portland Botanical Fulfillment Center (OR-97201)</p>
            </div>
        </div>
    </div>
</div>
@endsection
