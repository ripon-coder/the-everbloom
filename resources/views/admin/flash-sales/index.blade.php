@extends('layouts.admin')

@section('title', 'Flash Sales | The Everbloom Admin')

@section('content')
<div class="row">
    <div class="col-12">
        <div class="page-title-box">
            <h4 class="page-title">Flash Sales</h4>
            <div class="page-title-right">
                <a href="{{ route('admin.flash-sales.create') }}" class="btn btn-primary btn-sm">
                    <iconify-icon icon="solar:bolt-bold-duotone" class="me-1"></iconify-icon> Create Flash Sale
                </a>
            </div>
        </div>
    </div>
</div>

<div class="card">
    <div class="card-header d-flex flex-wrap justify-content-between align-items-center gap-2">
        <ul class="nav nav-pills card-header-pills">
            @foreach(['all' => 'All', 'active' => 'Active', 'scheduled' => 'Scheduled', 'draft' => 'Draft', 'expired' => 'Expired', 'disabled' => 'Disabled'] as $key => $label)
                <li class="nav-item">
                    <a class="nav-link {{ ($statusFilter ?: 'all') === $key ? 'active' : '' }}" href="{{ route('admin.flash-sales.index', ['status' => $key, 'search' => $search]) }}">{{ $label }}</a>
                </li>
            @endforeach
        </ul>
        <form method="GET" action="{{ route('admin.flash-sales.index') }}" class="d-flex gap-2">
            @if($statusFilter)<input type="hidden" name="status" value="{{ $statusFilter }}">@endif
            <input type="text" name="search" value="{{ $search }}" class="form-control form-control-sm" placeholder="Search flash sales...">
            <button type="submit" class="btn btn-soft-secondary btn-sm">Search</button>
        </form>
    </div>

    <div class="card-body p-0">
        <div class="table-responsive">
            <table class="table align-middle mb-0 table-hover">
                <thead class="bg-light-subtle">
                    <tr>
                        <th>Campaign</th>
                        <th>Schedule Window</th>
                        <th>Status</th>
                        <th>Products</th>
                        <th>Units Sold</th>
                        <th>Homepage</th>
                        <th class="text-end">Actions</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse($flashSales as $sale)
                        @php
                            $effective = $sale->effectiveStatus();
                            $badgeClass = match($effective) {
                                'active' => 'bg-success-subtle text-success',
                                'scheduled' => 'bg-info-subtle text-info',
                                'draft' => 'bg-secondary-subtle text-secondary',
                                'expired' => 'bg-warning-subtle text-warning',
                                default => 'bg-danger-subtle text-danger',
                            };
                        @endphp
                        <tr>
                            <td>
                                <div class="d-flex align-items-center gap-2">
                                    @if($sale->banner_image)
                                        <img src="{{ $sale->resolvedBannerImage() }}" alt="{{ $sale->title }}" class="rounded" style="width:44px;height:44px;object-fit:cover;">
                                    @endif
                                    <div>
                                        <a href="{{ route('admin.flash-sales.show', $sale) }}" class="fw-semibold text-dark">{{ $sale->title }}</a>
                                        <div class="text-muted fs-12">{{ $sale->slug }}</div>
                                    </div>
                                </div>
                            </td>
                            <td class="fs-13">
                                <div>{{ $sale->starts_at?->format('M d, Y H:i') }}</div>
                                <div class="text-muted">to {{ $sale->ends_at?->format('M d, Y H:i') }}</div>
                            </td>
                            <td><span class="badge {{ $badgeClass }} text-uppercase">{{ $effective }}</span></td>
                            <td>{{ $sale->items_count }}</td>
                            <td>{{ (int) ($sale->total_sold_qty ?? 0) }}</td>
                            <td>
                                @if($sale->show_on_homepage)
                                    <span class="badge bg-primary-subtle text-primary">Yes</span>
                                @else
                                    <span class="badge bg-light text-muted">No</span>
                                @endif
                            </td>
                            <td class="text-end">
                                <div class="d-inline-flex gap-1">
                                    <a href="{{ route('admin.flash-sales.products', $sale) }}" class="btn btn-soft-primary btn-sm">Products</a>
                                    <a href="{{ route('admin.flash-sales.show', $sale) }}" class="btn btn-soft-info btn-sm">Analytics</a>
                                    <a href="{{ route('admin.flash-sales.edit', $sale) }}" class="btn btn-soft-secondary btn-sm">Edit</a>
                                    <form method="POST" action="{{ route('admin.flash-sales.toggle', $sale) }}" class="d-inline">
                                        @csrf
                                        <button type="submit" class="btn btn-soft-warning btn-sm">
                                            {{ in_array($sale->status, ['disabled', 'draft'], true) ? 'Activate' : 'Disable' }}
                                        </button>
                                    </form>
                                    <form method="POST" action="{{ route('admin.flash-sales.destroy', $sale) }}" class="d-inline" onsubmit="return confirm('Delete this flash sale?');">
                                        @csrf
                                        @method('DELETE')
                                        <button type="submit" class="btn btn-soft-danger btn-sm">Delete</button>
                                    </form>
                                </div>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="7" class="text-center py-4 text-muted">No flash sales found.</td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>
    @if($flashSales->hasPages())
        <div class="card-footer">{{ $flashSales->links() }}</div>
    @endif
</div>
@endsection

