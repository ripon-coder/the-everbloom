@extends('layouts.admin')

@section('title', 'Product List')
@section('container_class', 'container-fluid')

@section('content')
<div class="row">
    <div class="col-12">
        <div class="card">
            <!-- Card Header with Filter Drawer Trigger & Add Product -->
            @php
                $activeFilterCount = collect([
                    request('search'),
                    request('category'),
                    request('status'),
                    request('sort') && request('sort') !== 'latest' ? request('sort') : null,
                ])->filter()->count();
            @endphp
            <div class="card-header d-flex flex-wrap justify-content-between align-items-center gap-2">
                <div class="d-flex flex-wrap align-items-center gap-2">
                    <h4 class="card-title mb-0">All Botanical Formulations ({{ $products->total() }})</h4>
                    @if($activeFilterCount > 0)
                        <a href="{{ route('admin.products.index') }}" class="badge bg-soft-danger text-danger text-decoration-none fs-11">
                            <i class="bx bx-x align-middle"></i> Clear Filters
                        </a>
                    @endif
                </div>

                <div class="d-flex flex-wrap align-items-center gap-2">
                    <button type="button" class="btn btn-sm btn-soft-secondary d-flex align-items-center gap-1 position-relative" data-bs-toggle="offcanvas" data-bs-target="#productFilterDrawer" aria-controls="productFilterDrawer">
                        <iconify-icon icon="solar:filter-bold-duotone" class="fs-16"></iconify-icon>
                        <span>Filter</span>
                        @if($activeFilterCount > 0)
                            <span class="badge bg-primary rounded-pill ms-1">{{ $activeFilterCount }}</span>
                        @endif
                    </button>

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
                                        <input type="checkbox" class="form-check-input product-bulk-checkbox" name="product_ids[]" form="bulk-products-form" value="{{ $product['id'] }}">
                                    </div>
                                </td>
                                <td>
                                    <div class="d-flex align-items-center gap-3">
                                        <div class="rounded bg-light avatar-md d-flex align-items-center justify-content-center overflow-hidden border">
                                            <img src="{{ app(\App\Services\ImageStorageService::class)->resolveUrl($product['images'][0] ?? 'products/botanical-radiance-serum-01.jpg') }}" alt="{{ $product['name'] }}" class="avatar-md object-fit-cover">
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
                                    <div class="fw-bold text-dark">{{ $currencySymbol }}{{ number_format($product['price'], 2) }}</div>
                                    @if(!empty($product['compare_at_price']) && $product['compare_at_price'] > $product['price'])
                                        <small class="text-muted text-decoration-line-through">{{ $currencySymbol }}{{ number_format($product['compare_at_price'], 2) }}</small>
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
            <div class="card-footer border-top py-3 bg-light bg-opacity-50">
                <div class="row align-items-center g-3">
                    <div class="col-md-auto">
                        <form id="bulk-products-form" action="{{ route('admin.products.bulk') }}" method="POST" class="d-flex align-items-center">
                            @csrf
                            <div class="input-group input-group-sm" style="width: 230px;">
                                <select name="action" class="form-select" required>
                                    <option value="">Bulk Actions...</option>
                                    <option value="activate">Activate Selected</option>
                                    <option value="deactivate">Deactivate Selected</option>
                                    <option value="delete">Delete Selected</option>
                                </select>
                                <button type="submit" class="btn btn-soft-primary fw-medium">Apply</button>
                            </div>
                        </form>
                    </div>
                    <div class="col-md text-md-center text-muted fs-13">
                        Showing {{ $products->firstItem() ?? 0 }} to {{ $products->lastItem() ?? 0 }} of {{ $products->total() }} formulations
                    </div>
                    @if($products->hasPages())
                    <div class="col-md-auto">
                        <ul class="pagination m-0 justify-content-md-end">
                            <li class="page-item {{ $products->onFirstPage() ? 'disabled' : '' }}">
                                <a href="{{ $products->previousPageUrl() ?? '#' }}" class="page-link" aria-label="Previous">
                                    <i class="bx bx-left-arrow-alt"></i>
                                </a>
                            </li>
                            @foreach($products->getUrlRange(1, $products->lastPage()) as $page => $url)
                                <li class="page-item {{ $page === $products->currentPage() ? 'active' : '' }}">
                                    <a href="{{ $url }}" class="page-link">{{ $page }}</a>
                                </li>
                            @endforeach
                            <li class="page-item {{ $products->hasMorePages() ? '' : 'disabled' }}">
                                <a href="{{ $products->nextPageUrl() ?? '#' }}" class="page-link" aria-label="Next">
                                    <i class="bx bx-right-arrow-alt"></i>
                                </a>
                            </li>
                        </ul>
                    </div>
                    @endif
                </div>
            </div>
        </div>
    </div>
</div>
<!-- Right-Side Filter Offcanvas Drawer -->
<x-admin.filter-drawer
    id="productFilterDrawer"
    title="Filter Formulations"
    :action="route('admin.products.index')"
    :reset-url="route('admin.products.index')">
    <div class="mb-3">
        <label for="filter-search" class="form-label fw-semibold">Search</label>
        <input type="text" id="filter-search" name="search" class="form-control" placeholder="Name, SKU, or keyword..." value="{{ request('search') }}">
    </div>

    <div class="mb-3">
        <label for="filter-category" class="form-label fw-semibold">Category</label>
        <select id="filter-category" name="category" class="form-select">
            <option value="">All Categories</option>
            @foreach($categories as $cat)
                <option value="{{ $cat['slug'] }}" {{ request('category') === $cat['slug'] ? 'selected' : '' }}>
                    {{ $cat['name'] }}
                </option>
            @endforeach
        </select>
    </div>

    <div class="mb-3">
        <label for="filter-status" class="form-label fw-semibold">Status</label>
        <select id="filter-status" name="status" class="form-select">
            <option value="">All Status</option>
            <option value="active" {{ request('status') === 'active' ? 'selected' : '' }}>Active</option>
            <option value="inactive" {{ request('status') === 'inactive' ? 'selected' : '' }}>Inactive</option>
        </select>
    </div>

    <div class="mb-3">
        <label for="filter-sort" class="form-label fw-semibold">Sort By</label>
        <select id="filter-sort" name="sort" class="form-select">
            <option value="latest" {{ request('sort', 'latest') === 'latest' ? 'selected' : '' }}>Latest</option>
            <option value="price_asc" {{ request('sort') === 'price_asc' ? 'selected' : '' }}>Price: Low → High</option>
            <option value="price_desc" {{ request('sort') === 'price_desc' ? 'selected' : '' }}>Price: High → Low</option>
            <option value="stock_asc" {{ request('sort') === 'stock_asc' ? 'selected' : '' }}>Stock: Low → High</option>
        </select>
    </div>
</x-admin.filter-drawer>

<script>
    document.getElementById('checkAll')?.addEventListener('change', function () {
        document.querySelectorAll('.product-bulk-checkbox').forEach(cb => {
            cb.checked = this.checked;
        });
    });
</script>
@endsection
