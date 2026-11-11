@extends('layouts.admin')

@section('title', 'Product List')
@section('container_class', 'container-fluid')

@section('content')
<div class="row">
    <div class="col-12">
        <div class="card">
            <!-- Card Header with Search, Filter & Add Product -->
            <div class="card-header d-flex flex-wrap justify-content-between align-items-center gap-2">
                <h4 class="card-title mb-0">All Botanical Formulations ({{ count($products) }})</h4>

                <div class="d-flex flex-wrap align-items-center gap-2">
                    <!-- Category Filter -->
                    <form method="GET" action="{{ route('admin.products.index') }}" class="d-flex align-items-center gap-2">
                        <select name="category" class="form-select form-select-sm" onchange="this.form.submit()" style="min-width: 160px;">
                            <option value="">All Categories</option>
                            @foreach($categories as $cat)
                                <option value="{{ $cat['slug'] }}" {{ request('category') === $cat['slug'] ? 'selected' : '' }}>
                                    {{ $cat['name'] }}
                                </option>
                            @endforeach
                        </select>

                        <!-- Search -->
                        <div class="input-group input-group-sm" style="max-width: 220px;">
                            <input type="text" name="search" class="form-control" placeholder="Search formulations..." value="{{ request('search') }}">
                            <button class="btn btn-outline-secondary" type="submit">
                                <iconify-icon icon="solar:magnifer-linear"></iconify-icon>
                            </button>
                        </div>

                        @if(request()->hasAny(['category', 'search']))
                            <a href="{{ route('admin.products.index') }}" class="btn btn-sm btn-soft-danger" title="Clear Filters">
                                <i class="bx bx-x"></i>
                            </a>
                        @endif
                    </form>

                    <a href="{{ route('admin.products.create') }}" class="btn btn-sm btn-primary d-flex align-items-center gap-1">
                        <iconify-icon icon="solar:add-circle-bold" class="fs-16"></iconify-icon>
                        <span>Add Product</span>
                    </a>
                </div>
            </div>

            <!-- Responsive Table -->
            <div class="card-body p-0">
                <div class="table-responsive">
                    <table class="table align-middle mb-0 table-hover table-centered">
                        <thead class="bg-light-subtle">
                            <tr>
                                <th style="width: 20px;">
                                    <div class="form-check ms-1">
                                        <input type="checkbox" class="form-check-input" id="checkAll">
                                    </div>
                                </th>
                                <th>Product Name & Bottle Sizes</th>
                                <th>SKU</th>
                                <th>Price</th>
                                <th>Stock Status</th>
                                <th>Category</th>
                                <th>Rating</th>
                                <th class="text-end">Actions</th>
                            </tr>
                        </thead>
                        <tbody>
                            @forelse($products as $product)
                            <tr>
                                <td>
                                    <div class="form-check ms-1">
                                        <input type="checkbox" class="form-check-input">
                                    </div>
                                </td>
                                <td>
                                    <div class="d-flex align-items-center gap-3">
                                        <div class="rounded bg-light avatar-md d-flex align-items-center justify-content-center overflow-hidden border">
                                            <img src="{{ asset('images/' . ($product['images'][0] ?? 'products/botanical-radiance-serum-01.jpg')) }}" alt="{{ $product['name'] }}" class="avatar-md object-fit-cover">
                                        </div>
                                        <div>
                                            <div class="d-flex align-items-center gap-2">
                                                <a href="{{ route('admin.products.edit', $product['id']) }}" class="text-dark fw-semibold fs-14">
                                                    {{ $product['name'] }}
                                                </a>
                                                @if(!empty($product['badge']))
                                                    <span class="badge bg-primary-subtle text-primary fs-11">{{ $product['badge'] }}</span>
                                                @endif
                                            </div>
                                            <p class="text-muted mb-0 mt-1 fs-12">
                                                <span class="text-secondary fw-medium">Sizes:</span> {{ implode(', ', $product['sizes'] ?? []) }}
                                            </p>
                                        </div>
                                    </div>
                                </td>
                                <td>
                                    <span class="text-muted fs-13 font-monospace">{{ $product['sku'] ?? ('EB-' . str_pad($product['id'], 4, '0', STR_PAD_LEFT)) }}</span>
                                </td>
                                <td>
                                    <div class="fw-bold text-dark">${{ number_format($product['price'], 2) }}</div>
                                    @if(!empty($product['compare_at_price']) && $product['compare_at_price'] > $product['price'])
                                        <small class="text-muted text-decoration-line-through">${{ number_format($product['compare_at_price'], 2) }}</small>
                                    @endif
                                </td>
                                <td>
                                    @php
                                        $stockCount = $product['stock_count'] ?? 30;
                                        $stockStatus = $product['stock_status'] ?? 'in_stock';
                                        $stockBadge = match($stockStatus) {
                                            'in_stock' => 'bg-success-subtle text-success',
                                            'low_stock' => 'bg-warning-subtle text-warning',
                                            default => 'bg-danger-subtle text-danger',
                                        };
                                        $stockLabel = match($stockStatus) {
                                            'in_stock' => 'In Stock',
                                            'low_stock' => 'Low Stock',
                                            default => 'Out of Stock',
                                        };
                                    @endphp
                                    <span class="badge {{ $stockBadge }} mb-1">{{ $stockLabel }}</span>
                                    <p class="text-muted mb-0 fs-12">{{ $stockCount }} units</p>
                                </td>
                                <td>
                                    <span class="badge bg-light text-dark fs-12 border">{{ $product['category'] ?? 'Botanical' }}</span>
                                </td>
                                <td>
                                    <div class="d-flex align-items-center">
                                        <span class="badge p-1 bg-light text-dark fs-12 me-1 border">
                                            <i class="bx bxs-star align-text-top fs-14 text-warning me-1"></i> {{ $product['rating'] ?? 5.0 }}
                                        </span>
                                        <span class="text-muted fs-12">({{ $product['review_count'] ?? 0 }})</span>
                                    </div>
                                </td>
                                <td class="text-end">
                                    <div class="d-flex align-items-center justify-content-end gap-1">
                                        <!-- View on Live Store -->
                                        <a href="{{ route('shop.product', $product['slug']) }}" target="_blank" class="btn btn-sm btn-soft-secondary" title="View on Live Store">
                                            <iconify-icon icon="solar:eye-broken" class="fs-16"></iconify-icon>
                                        </a>
                                        <!-- Edit -->
                                        <a href="{{ route('admin.products.edit', $product['id']) }}" class="btn btn-sm btn-soft-primary" title="Edit Product">
                                            <iconify-icon icon="solar:pen-2-broken" class="fs-16"></iconify-icon>
                                        </a>
                                        <!-- Delete -->
                                        <form action="{{ route('admin.products.destroy', $product['id']) }}" method="POST" class="d-inline" onsubmit="return confirm('Delete this formulation?');">
                                            @csrf
                                            @method('DELETE')
                                            <button type="submit" class="btn btn-sm btn-soft-danger" title="Delete Product">
                                                <iconify-icon icon="solar:trash-bin-minimalistic-2-broken" class="fs-16"></iconify-icon>
                                            </button>
                                        </form>
                                    </div>
                                </td>
                            </tr>
                            @empty
                            <tr>
                                <td colspan="8" class="text-center py-5 text-muted">
                                    <iconify-icon icon="solar:box-minimalistic-broken" class="fs-48 text-muted mb-2"></iconify-icon>
                                    <p class="mb-0">No botanical formulations match your filters.</p>
                                </td>
                            </tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>
            </div>

            <!-- Card Footer -->
            <div class="card-footer py-2 bg-light bg-opacity-50">
                <div class="d-flex align-items-center justify-content-between">
                    <p class="mb-0 text-muted fs-12">Showing 1 to {{ count($products) }} of {{ count($products) }} formulations</p>
                    <nav aria-label="Page navigation">
                        <ul class="pagination pagination-sm mb-0">
                            <li class="page-item disabled"><a class="page-link" href="#">&laquo;</a></li>
                            <li class="page-item active"><a class="page-link" href="#">1</a></li>
                            <li class="page-item disabled"><a class="page-link" href="#">&raquo;</a></li>
                        </ul>
                    </nav>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection
