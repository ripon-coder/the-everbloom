@extends('layouts.admin')

@section('title', 'Categories List')
@section('container_class', 'container-xxl')

@section('content')
<!-- Row 1: Highlighted Category Cards -->
<div class="row">
    @foreach(array_slice($categories, 0, 4) as $cat)
    <div class="col-md-6 col-xl-3">
        <div class="card text-center overflow-hidden">
            <div class="card-body">
                <div class="rounded bg-light-subtle p-3 mb-3 d-inline-block">
                    <img src="{{ app(\App\Services\ImageStorageService::class)->resolveUrl($cat['image'] ?? 'categories/face-care.jpg') }}" alt="{{ $cat['name'] }}" class="avatar-xl rounded object-fit-cover shadow-sm">
                </div>
                <h4 class="mb-1 fw-bold fs-16">{{ $cat['name'] }}</h4>
                <p class="text-muted fs-12 mb-2">{{ $cat['tagline'] ?? 'Botanical Collection' }}</p>
                <span class="badge bg-primary-subtle text-primary fs-12 px-2 py-1">
                    {{ $cat['count'] ?? 0 }} Formulations
                </span>
            </div>
            <div class="card-footer py-2 bg-light bg-opacity-50">
                <a href="{{ route('admin.categories.edit', $cat['slug']) }}" class="text-reset fw-semibold fs-12">Edit Details &rarr;</a>
            </div>
        </div>
    </div>
    @endforeach
</div>

<!-- Row 2: All Categories Table Card -->
<div class="row">
    <div class="col-12">
        <div class="card">
            @php
                $categoryFilterCount = collect([
                    request('search'),
                    request('level'),
                    request('sort') && request('sort') !== 'hierarchy' ? request('sort') : null,
                ])->filter()->count();
            @endphp
            <div class="card-header d-flex flex-wrap justify-content-between align-items-center gap-2">
                <div class="d-flex flex-wrap align-items-center gap-2">
                    <h4 class="card-title mb-0">All Botanical Categories ({{ count($categories) }})</h4>
                    @if($categoryFilterCount > 0)
                        <a href="{{ route('admin.categories.index') }}" class="badge bg-soft-danger text-danger text-decoration-none fs-11">
                            <i class="bx bx-x align-middle"></i> Clear Filters
                        </a>
                    @endif
                </div>
                <div class="d-flex flex-wrap align-items-center gap-2">
                    <button type="button" class="btn btn-sm btn-soft-secondary d-flex align-items-center gap-1 position-relative" data-bs-toggle="offcanvas" data-bs-target="#categoryFilterDrawer" aria-controls="categoryFilterDrawer">
                        <iconify-icon icon="solar:filter-bold-duotone" class="fs-16"></iconify-icon>
                        <span>Filter</span>
                        @if($categoryFilterCount > 0)
                            <span class="badge bg-primary rounded-pill ms-1">{{ $categoryFilterCount }}</span>
                        @endif
                    </button>
                    <a href="{{ route('admin.categories.create') }}" class="btn btn-sm btn-primary d-flex align-items-center gap-1">
                        <iconify-icon icon="solar:add-circle-bold" class="fs-16"></iconify-icon>
                        <span>Create Category</span>
                    </a>
                </div>
            </div>
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
                                <th>Category</th>
                                <th>URL Slug</th>
                                <th>Tagline & Philosophy</th>
                                <th>Active SKUs</th>
                                <th class="text-end">Actions</th>
                            </tr>
                        </thead>
                        <tbody>
                            @forelse($categories as $category)
                            <tr>
                                <td>
                                    <div class="form-check ms-1">
                                        <input type="checkbox" class="form-check-input">
                                    </div>
                                </td>
                                <td>
                                    <div class="d-flex align-items-center gap-3">
                                        <div class="avatar-md bg-light rounded d-flex align-items-center justify-content-center overflow-hidden border">
                                            <img src="{{ app(\App\Services\ImageStorageService::class)->resolveUrl($category['image'] ?? 'categories/face-care.jpg') }}" alt="{{ $category['name'] }}" class="avatar-md object-fit-cover">
                                        </div>
                                        <div>
                                            <a href="{{ route('admin.categories.edit', $category['slug']) }}" class="text-dark fw-bold fs-14">
                                                {{ $category['hierarchy_path'] ?? $category['name'] }}
                                            </a>
                                            <p class="text-muted mb-0 fs-12">
                                                @php
                                                    $lvl = (int) ($category['level'] ?? 1);
                                                    $lvlLabel = $lvl === 1 ? 'Step 1: Main' : ($lvl === 2 ? 'Step 2: Subcategory' : 'Step 3: Child');
                                                @endphp
                                                <span class="badge bg-primary-subtle text-primary me-1">{{ $lvlLabel }}</span>
                                                {{ $category['short_name'] ?? $category['name'] }}
                                            </p>
                                        </div>
                                    </div>
                                </td>
                                <td>
                                    <code class="text-primary fs-12">/shop/category/{{ $category['slug'] }}</code>
                                </td>
                                <td>
                                    <p class="text-muted mb-0 fs-13" style="max-width: 320px;">
                                        {{ $category['description'] ?? $category['tagline'] ?? 'Curated botanical formulations.' }}
                                    </p>
                                </td>
                                <td>
                                    <span class="badge bg-light text-dark border fs-12">
                                        {{ $category['count'] ?? 0 }} products
                                    </span>
                                </td>
                                <td class="text-end">
                                    <div class="d-flex align-items-center justify-content-end gap-1">
                                        <a href="{{ route('shop.category', $category['slug']) }}" target="_blank" class="btn btn-sm btn-soft-secondary" title="View Category on Storefront">
                                            <iconify-icon icon="solar:eye-broken" class="fs-16"></iconify-icon>
                                        </a>
                                        <a href="{{ route('admin.categories.edit', $category['slug']) }}" class="btn btn-sm btn-soft-primary" title="Edit Category">
                                            <iconify-icon icon="solar:pen-2-broken" class="fs-16"></iconify-icon>
                                        </a>
                                        <form action="{{ route('admin.categories.destroy', $category['slug']) }}" method="POST" class="d-inline" onsubmit="return confirm('Delete this category?');">
                                            @csrf
                                            @method('DELETE')
                                            <button type="submit" class="btn btn-sm btn-soft-danger" title="Delete Category">
                                                <iconify-icon icon="solar:trash-bin-minimalistic-2-broken" class="fs-16"></iconify-icon>
                                            </button>
                                        </form>
                                    </div>
                                </td>
                            </tr>
                            @empty
                            <tr>
                                <td colspan="6" class="text-center py-4 text-muted">No categories found.</td>
                            </tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>
            </div>
            <div class="card-footer py-2 bg-light bg-opacity-50">
                <p class="mb-0 text-muted fs-12">Showing {{ count($categories) }} botanical skincare categories</p>
            </div>
        </div>
    </div>
</div>
<x-admin.filter-drawer
    id="categoryFilterDrawer"
    title="Filter Categories"
    :action="route('admin.categories.index')"
    :reset-url="route('admin.categories.index')">
    <div class="mb-3">
        <label for="cat-filter-search" class="form-label fw-semibold">Search</label>
        <input type="text" id="cat-filter-search" name="search" class="form-control" placeholder="Category name or slug..." value="{{ request('search') }}">
    </div>

    <div class="mb-3">
        <label for="cat-filter-level" class="form-label fw-semibold">Hierarchy Level</label>
        <select id="cat-filter-level" name="level" class="form-select">
            <option value="">All Levels</option>
            <option value="1" {{ request('level') === '1' ? 'selected' : '' }}>Step 1: Main</option>
            <option value="2" {{ request('level') === '2' ? 'selected' : '' }}>Step 2: Subcategory</option>
            <option value="3" {{ request('level') === '3' ? 'selected' : '' }}>Step 3: Child</option>
        </select>
    </div>

    <div class="mb-3">
        <label for="cat-filter-sort" class="form-label fw-semibold">Sort By</label>
        <select id="cat-filter-sort" name="sort" class="form-select">
            <option value="hierarchy" {{ request('sort', 'hierarchy') === 'hierarchy' ? 'selected' : '' }}>Hierarchy Order</option>
            <option value="products_desc" {{ request('sort') === 'products_desc' ? 'selected' : '' }}>Most Products</option>
            <option value="name_asc" {{ request('sort') === 'name_asc' ? 'selected' : '' }}>Name: A → Z</option>
        </select>
    </div>
</x-admin.filter-drawer>
@endsection
