@extends('layouts.admin')

@section('title', 'Brands')
@section('container_class', 'container-xxl')

@section('content')
<div class="row">
    <div class="col-12">
        <div class="card">
            <div class="card-header d-flex flex-wrap justify-content-between align-items-center gap-2">
                <h4 class="card-title mb-0">Botanical Brands ({{ $brands->total() }})</h4>

                <div class="d-flex flex-wrap align-items-center gap-2">
                    <form method="GET" action="{{ route('admin.brands.index') }}" class="d-flex align-items-center gap-2">
                        <div class="input-group input-group-sm" style="max-width: 220px;">
                            <input type="text" name="search" class="form-control" placeholder="Search brands..." value="{{ request('search') }}">
                            <button class="btn btn-outline-secondary" type="submit">
                                <iconify-icon icon="solar:magnifer-linear"></iconify-icon>
                            </button>
                        </div>
                        @if(request()->filled('search'))
                            <a href="{{ route('admin.brands.index') }}" class="btn btn-sm btn-soft-danger" title="Clear Filters">
                                <i class="bx bx-x"></i>
                            </a>
                        @endif
                    </form>

                    <a href="{{ route('admin.brands.create') }}" class="btn btn-sm btn-primary d-flex align-items-center gap-1">
                        <iconify-icon icon="solar:add-circle-bold" class="fs-16"></iconify-icon>
                        <span>Add Brand</span>
                    </a>
                </div>
            </div>

            <div class="card-body p-0">
                <div class="table-responsive">
                    <table class="table align-middle mb-0 table-hover table-centered">
                        <thead class="bg-light-subtle">
                            <tr>
                                <th>Brand</th>
                                <th>Slug</th>
                                <th>Products</th>
                                <th>Sort</th>
                                <th>Status</th>
                                <th class="text-end">Actions</th>
                            </tr>
                        </thead>
                        <tbody>
                            @forelse($brands as $brand)
                            <tr>
                                <td>
                                    <div class="d-flex align-items-center gap-2">
                                        @if($brand->logo)
                                            <img src="{{ $brand->resolvedLogo() }}" alt="{{ $brand->name }}" class="avatar-sm rounded object-fit-cover border">
                                        @endif
                                        <a href="{{ route('admin.brands.edit', $brand->id) }}" class="fw-semibold text-dark">
                                            {{ $brand->name }}
                                        </a>
                                    </div>
                                </td>
                                <td class="font-monospace text-muted fs-13">{{ $brand->slug }}</td>
                                <td>{{ $brand->products_count }}</td>
                                <td>{{ $brand->sort_order }}</td>
                                <td>
                                    <span class="badge {{ $brand->is_active ? 'bg-success-subtle text-success' : 'bg-secondary-subtle text-secondary' }}">
                                        {{ $brand->is_active ? 'Active' : 'Inactive' }}
                                    </span>
                                </td>
                                <td class="text-end">
                                    <div class="d-flex align-items-center justify-content-end gap-1">
                                        <a href="{{ route('shop.brand', $brand->slug) }}" target="_blank" class="btn btn-sm btn-soft-secondary" title="View Brand Page">
                                            <iconify-icon icon="solar:eye-broken" class="fs-16"></iconify-icon>
                                        </a>
                                        <a href="{{ route('admin.brands.edit', $brand->id) }}" class="btn btn-sm btn-soft-primary" title="Edit Brand">
                                            <iconify-icon icon="solar:pen-2-broken" class="fs-16"></iconify-icon>
                                        </a>
                                        <form action="{{ route('admin.brands.destroy', $brand->id) }}" method="POST" class="d-inline" onsubmit="return confirm('Delete this brand?');">
                                            @csrf
                                            @method('DELETE')
                                            <button type="submit" class="btn btn-sm btn-soft-danger" title="Delete Brand">
                                                <iconify-icon icon="solar:trash-bin-minimalistic-2-broken" class="fs-16"></iconify-icon>
                                            </button>
                                        </form>
                                    </div>
                                </td>
                            </tr>
                            @empty
                            <tr>
                                <td colspan="6" class="text-center py-5 text-muted">No brands found.</td>
                            </tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>
            </div>

            <div class="card-footer py-2 bg-light bg-opacity-50">
                <div class="d-flex flex-wrap align-items-center justify-content-between gap-2">
                    <p class="mb-0 text-muted fs-12">Showing {{ $brands->firstItem() ?? 0 }} to {{ $brands->lastItem() ?? 0 }} of {{ $brands->total() }} brands</p>
                    <div>{{ $brands->links('pagination::bootstrap-5') }}</div>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection

