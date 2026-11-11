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
                    <img src="{{ asset('images/' . ($cat['image'] ?? 'categories/face-care.jpg')) }}" alt="{{ $cat['name'] }}" class="avatar-xl rounded object-fit-cover shadow-sm">
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
            <div class="card-header d-flex justify-content-between align-items-center">
                <h4 class="card-title mb-0">All Botanical Categories ({{ count($categories) }})</h4>
                <a href="{{ route('admin.categories.create') }}" class="btn btn-sm btn-primary d-flex align-items-center gap-1">
                    <iconify-icon icon="solar:add-circle-bold" class="fs-16"></iconify-icon>
                    <span>Create Category</span>
                </a>
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
                                            <img src="{{ asset('images/' . ($category['image'] ?? 'categories/face-care.jpg')) }}" alt="{{ $category['name'] }}" class="avatar-md object-fit-cover">
                                        </div>
                                        <div>
                                            <a href="{{ route('admin.categories.edit', $category['slug']) }}" class="text-dark fw-bold fs-14">
                                                {{ $category['name'] }}
                                            </a>
                                            <p class="text-muted mb-0 fs-12">{{ $category['short_name'] ?? $category['name'] }}</p>
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
@endsection
