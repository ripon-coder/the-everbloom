@extends('layouts.admin')

@section('title', 'Coupons List')
@section('container_class', 'container-xxl')

@section('content')
<div class="row">
    <div class="col-12">
        <div class="card">
            @php
                $couponFilterCount = collect([
                    request('search'),
                    request('type'),
                    request('status'),
                ])->filter()->count();
            @endphp
            <div class="card-header d-flex flex-wrap justify-content-between align-items-center gap-2">
                <div class="d-flex flex-wrap align-items-center gap-2">
                    <h4 class="card-title mb-0">Active Promotional Codes &amp; Coupons ({{ $coupons->total() }})</h4>
                    @if($couponFilterCount > 0)
                        <a href="{{ route('admin.coupons.index') }}" class="badge bg-soft-danger text-danger text-decoration-none fs-11">
                            <i class="bx bx-x align-middle"></i> Clear Filters
                        </a>
                    @endif
                </div>
                <div class="d-flex flex-wrap align-items-center gap-2">
                    <button type="button" class="btn btn-sm btn-soft-secondary d-flex align-items-center gap-1 position-relative" data-bs-toggle="offcanvas" data-bs-target="#couponFilterDrawer" aria-controls="couponFilterDrawer">
                        <iconify-icon icon="solar:filter-bold-duotone" class="fs-16"></iconify-icon>
                        <span>Filter</span>
                        @if($couponFilterCount > 0)
                            <span class="badge bg-primary rounded-pill ms-1">{{ $couponFilterCount }}</span>
                        @endif
                    </button>
                    <a href="{{ route('admin.coupons.create') }}" class="btn btn-sm btn-primary d-flex align-items-center gap-1">
                        <iconify-icon icon="solar:add-circle-bold" class="fs-16"></iconify-icon>
                        <span>Create Coupon</span>
                    </a>
                </div>
            </div>
            <div class="card-body p-0">
                <div class="table-responsive">
                    <table class="table align-middle mb-0 table-hover table-centered">
                        <thead class="bg-light-subtle">
                            <tr>
                                <th>Coupon Code</th>
                                <th>Promotion Campaign</th>
                                <th>Discount</th>
                                <th>Discount Type</th>
                                <th>Usage Count</th>
                                <th>Expiration</th>
                                <th>Status</th>
                                <th class="text-end">Action</th>
                            </tr>
                        </thead>
                        <tbody>
                            @forelse($coupons as $c)
                            <tr>
                                <td>
                                    <span class="badge bg-primary-subtle text-primary font-monospace fs-13 px-2 py-1">
                                        {{ $c['code'] }}
                                    </span>
                                </td>
                                <td>
                                    <span class="fw-semibold text-dark fs-13">{{ $c['label'] }}</span>
                                </td>
                                <td>
                                    <span class="fw-bold text-success">{{ $c['discount'] }}</span>
                                </td>
                                <td>
                                    <span class="badge bg-light text-muted border">{{ $c['type'] }}</span>
                                </td>
                                <td>
                                    @php
                                        $limit = (int) ($c['usage_limit'] ?? 0);
                                        $pct = $limit > 0 ? min(100, round(($c['used_count'] / $limit) * 100)) : 0;
                                    @endphp
                                    <div class="d-flex align-items-center gap-2">
                                        <div class="progress flex-grow-1" style="height: 6px; width: 80px;">
                                            <div class="progress-bar bg-primary" style="width: {{ $pct }}%"></div>
                                        </div>
                                        <span class="text-muted fs-12">{{ $c['used_count'] }}/{{ $limit > 0 ? $limit : '∞' }}</span>
                                    </div>
                                </td>
                                <td>
                                    <span class="fs-13 text-muted">{{ $c['expires_at'] }}</span>
                                </td>
                                <td>
                                    <span class="badge bg-{{ $c['status_badge'] }}-subtle text-{{ $c['status_badge'] }} px-2 py-1 fs-12">{{ $c['status'] }}</span>
                                </td>
                                <td class="text-end">
                                    <div class="d-flex align-items-center justify-content-end gap-1">
                                        <a href="{{ route('admin.coupons.history', $c['id']) }}" class="btn btn-sm btn-soft-secondary" title="Usage History">
                                            <iconify-icon icon="solar:history-broken" class="fs-16"></iconify-icon>
                                        </a>
                                        <a href="{{ route('admin.coupons.edit', $c['id']) }}" class="btn btn-sm btn-soft-primary" title="Edit Coupon">
                                            <iconify-icon icon="solar:pen-2-broken" class="fs-16"></iconify-icon>
                                        </a>
                                        <form action="{{ route('admin.coupons.toggle', $c['id']) }}" method="POST" class="d-inline">
                                            @csrf
                                            <button type="submit" class="btn btn-sm {{ $c['is_active'] ? 'btn-soft-warning' : 'btn-soft-success' }}" title="{{ $c['is_active'] ? 'Deactivate' : 'Activate' }}">
                                                <iconify-icon icon="{{ $c['is_active'] ? 'solar:pause-circle-broken' : 'solar:play-circle-broken' }}" class="fs-16"></iconify-icon>
                                            </button>
                                        </form>
                                        <form action="{{ route('admin.coupons.destroy', $c['id']) }}" method="POST" class="d-inline" onsubmit="return confirm('Delete this coupon?');">
                                            @csrf
                                            @method('DELETE')
                                            <button type="submit" class="btn btn-sm btn-soft-danger" title="Delete Coupon">
                                                <iconify-icon icon="solar:trash-bin-minimalistic-2-broken" class="fs-16"></iconify-icon>
                                            </button>
                                        </form>
                                    </div>
                                </td>
                            </tr>
                            @empty
                            <tr>
                                <td colspan="8" class="text-center py-4 text-muted">No promotional coupons found.</td>
                            </tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>
            </div>
            <div class="card-footer py-2 bg-light bg-opacity-50">
                <div class="d-flex flex-wrap align-items-center justify-content-between gap-2">
                    <p class="mb-0 text-muted fs-12">Showing {{ $coupons->firstItem() ?? 0 }} to {{ $coupons->lastItem() ?? 0 }} of {{ $coupons->total() }} coupons</p>
                    <div>{{ $coupons->links('pagination::bootstrap-5') }}</div>
                </div>
            </div>
        </div>
    </div>
</div>
<x-admin.filter-drawer
    id="couponFilterDrawer"
    title="Filter Coupons"
    :action="route('admin.coupons.index')"
    :reset-url="route('admin.coupons.index')">
    <div class="mb-3">
        <label for="cpn-filter-search" class="form-label fw-semibold">Search</label>
        <input type="text" id="cpn-filter-search" name="search" class="form-control" placeholder="Coupon code or campaign label..." value="{{ request('search') }}">
    </div>

    <div class="mb-3">
        <label for="cpn-filter-type" class="form-label fw-semibold">Discount Type</label>
        <select id="cpn-filter-type" name="type" class="form-select">
            <option value="">All Types</option>
            <option value="percentage" {{ request('type') === 'percentage' ? 'selected' : '' }}>Percentage</option>
            <option value="fixed" {{ request('type') === 'fixed' ? 'selected' : '' }}>Fixed Amount</option>
            <option value="free_shipping" {{ request('type') === 'free_shipping' ? 'selected' : '' }}>Free Shipping</option>
        </select>
    </div>

    <div class="mb-3">
        <label for="cpn-filter-status" class="form-label fw-semibold">Status</label>
        <select id="cpn-filter-status" name="status" class="form-select">
            <option value="">All Statuses</option>
            <option value="active" {{ request('status') === 'active' ? 'selected' : '' }}>Active</option>
            <option value="inactive" {{ request('status') === 'inactive' ? 'selected' : '' }}>Inactive</option>
        </select>
    </div>
</x-admin.filter-drawer>
@endsection
