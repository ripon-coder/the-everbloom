@extends('layouts.admin')

@section('title', 'Customer List')
@section('container_class', 'container-xxl')

@section('content')
<!-- Row 1: Variant C Metric Cards -->
<div class="row">
    <!-- All Customers -->
    <div class="col-md-6 col-xl-3">
        <div class="card">
            <div class="card-body">
                <div class="d-flex align-items-center gap-2 mb-3">
                    <div class="avatar-md bg-primary bg-opacity-10 rounded">
                        <iconify-icon icon="solar:users-group-two-rounded-bold-duotone" class="fs-32 text-primary avatar-title"></iconify-icon>
                    </div>
                    <div>
                        <h4 class="mb-0 fs-16">All Clients</h4>
                    </div>
                </div>
                <div class="d-flex align-items-center justify-content-between">
                    <p class="text-muted fw-medium fs-22 mb-0">{{ $allClientsCount ?? $customers->total() }}</p>
                    <div>
                        <span class="badge text-success bg-success-subtle fs-12"><i class="bx bx-up-arrow-alt"></i> Live</span>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Active Accounts -->
    <div class="col-md-6 col-xl-3">
        <div class="card">
            <div class="card-body">
                <div class="d-flex align-items-center gap-2 mb-3">
                    <div class="avatar-md bg-success bg-opacity-10 rounded">
                        <iconify-icon icon="solar:user-check-bold-duotone" class="fs-32 text-success avatar-title"></iconify-icon>
                    </div>
                    <div>
                        <h4 class="mb-0 fs-16">Active Profiles</h4>
                    </div>
                </div>
                <div class="d-flex align-items-center justify-content-between">
                    <p class="text-muted fw-medium fs-22 mb-0">{{ $activeProfilesCount ?? 0 }}</p>
                    <div>
                        <span class="badge text-success bg-success-subtle fs-12">Active</span>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Average Order Value -->
    <div class="col-md-6 col-xl-3">
        <div class="card">
            <div class="card-body">
                <div class="d-flex align-items-center gap-2 mb-3">
                    <div class="avatar-md bg-info bg-opacity-10 rounded">
                        <iconify-icon icon="solar:wallet-money-bold-duotone" class="fs-32 text-info avatar-title"></iconify-icon>
                    </div>
                    <div>
                        <h4 class="mb-0 fs-16">Average LTV</h4>
                    </div>
                </div>
                <div class="d-flex align-items-center justify-content-between">
                    <p class="text-muted fw-medium fs-22 mb-0">{{ $currencySymbol }}{{ number_format($averageLtv ?? 0, 2) }}</p>
                    <div>
                        <span class="badge text-success bg-success-subtle fs-12">LTV</span>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Repeat Purchase Rate -->
    <div class="col-md-6 col-xl-3">
        <div class="card">
            <div class="card-body">
                <div class="d-flex align-items-center gap-2 mb-3">
                    <div class="avatar-md bg-warning bg-opacity-10 rounded">
                        <iconify-icon icon="solar:heart-bold-duotone" class="fs-32 text-warning avatar-title"></iconify-icon>
                    </div>
                    <div>
                        <h4 class="mb-0 fs-16">Repeat Rate</h4>
                    </div>
                </div>
                <div class="d-flex align-items-center justify-content-between">
                    <p class="text-muted fw-medium fs-22 mb-0">{{ number_format($repeatRate ?? 0, 1) }}%</p>
                    <div>
                        <span class="badge text-success bg-success-subtle fs-12">Repeat</span>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<!-- Row 2: Customer Table Card -->
<div class="row">
    <div class="col-12">
        <div class="card">
            <div class="card-header d-flex flex-wrap justify-content-between align-items-center gap-2">
                <h4 class="card-title mb-0">Registered Botanical Skincare Clients ({{ $customers->total() }})</h4>

                <div class="d-flex flex-wrap align-items-center gap-2">
                    <form method="GET" action="{{ route('admin.customers.index') }}" class="d-flex flex-wrap align-items-center gap-2">
                        <select name="status" class="form-select form-select-sm" onchange="this.form.submit()" style="min-width: 120px;">
                            <option value="">All Status</option>
                            <option value="Active" {{ request('status') === 'Active' ? 'selected' : '' }}>Active</option>
                            <option value="Inactive" {{ request('status') === 'Inactive' ? 'selected' : '' }}>Inactive</option>
                        </select>

                        <div class="input-group input-group-sm" style="max-width: 220px;">
                            <input type="text" name="search" class="form-control" placeholder="Name, email or phone..." value="{{ request('search') }}">
                            <button class="btn btn-outline-secondary" type="submit">
                                <iconify-icon icon="solar:magnifer-linear"></iconify-icon>
                            </button>
                        </div>

                        @if(request()->hasAny(['search', 'status']))
                            <a href="{{ route('admin.customers.index') }}" class="btn btn-sm btn-soft-danger" title="Clear Filters">
                                <i class="bx bx-x"></i>
                            </a>
                        @endif
                    </form>

                    <a href="{{ route('admin.customers.export', request()->query()) }}" class="btn btn-sm btn-soft-secondary">
                        <iconify-icon icon="solar:download-minimalistic-linear" class="me-1"></iconify-icon> Export CSV
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
                                <th>Client Profile</th>
                                <th>Contact Information</th>
                                <th>Location</th>
                                <th>Orders</th>
                                <th>Total Spent</th>
                                <th>Client Since</th>
                                <th>Status</th>
                                <th class="text-end">Actions</th>
                            </tr>
                        </thead>
                        <tbody>
                            @forelse($customers as $c)
                            <tr>
                                <td>
                                    <div class="form-check ms-1">
                                        <input type="checkbox" class="form-check-input">
                                    </div>
                                </td>
                                <td>
                                    <div class="d-flex align-items-center gap-3">
                                        <img src="{{ asset($c['avatar']) }}" alt="{{ $c['name'] }}" class="avatar-sm rounded-circle object-fit-cover border">
                                        <div>
                                            <a href="{{ route('admin.customers.show', $c['id']) }}" class="text-dark fw-semibold fs-14">
                                                {{ $c['name'] }}
                                            </a>
                                            <p class="text-muted mb-0 fs-12">ID #EB-CL{{ str_pad($c['id'], 3, '0', STR_PAD_LEFT) }}</p>
                                        </div>
                                    </div>
                                </td>
                                <td>
                                    <span class="d-block fs-13 text-dark">{{ $c['email'] }}</span>
                                    <small class="text-muted">{{ $c['phone'] }}</small>
                                </td>
                                <td>
                                    <span class="fs-13 text-dark">{{ $c['city'] }}</span>
                                </td>
                                <td>
                                    <span class="badge bg-light text-dark border fs-12">{{ $c['orders_count'] }} orders</span>
                                </td>
                                <td>
                                    <span class="fw-bold text-dark fs-14">{{ $currencySymbol }}{{ number_format($c['total_spent'], 2) }}</span>
                                </td>
                                <td>
                                    <span class="fs-13 text-muted">{{ $c['join_date'] }}</span>
                                </td>
                                <td>
                                    <span class="badge {{ $c['status'] === 'Active' ? 'bg-success-subtle text-success' : 'bg-secondary-subtle text-secondary' }} px-2 py-1 fs-12">
                                        {{ $c['status'] }}
                                    </span>
                                </td>
                                <td class="text-end">
                                    <div class="d-flex align-items-center justify-content-end gap-1">
                                        <a href="{{ route('admin.customers.show', $c['id']) }}" class="btn btn-sm btn-soft-primary" title="View Profile">
                                            <iconify-icon icon="solar:eye-broken" class="fs-16"></iconify-icon>
                                        </a>
                                        <form action="{{ route('admin.customers.toggleStatus', $c['id']) }}" method="POST" class="d-inline">
                                            @csrf
                                            <button type="submit" class="btn btn-sm {{ $c['status'] === 'Active' ? 'btn-soft-warning' : 'btn-soft-success' }}" title="{{ $c['status'] === 'Active' ? 'Deactivate Customer' : 'Activate Customer' }}">
                                                <iconify-icon icon="{{ $c['status'] === 'Active' ? 'solar:user-block-broken' : 'solar:user-check-broken' }}" class="fs-16"></iconify-icon>
                                            </button>
                                        </form>
                                    </div>
                                </td>
                            </tr>
                            @empty
                            <tr>
                                <td colspan="9" class="text-center py-4 text-muted">No customers found.</td>
                            </tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>
            </div>
            <div class="card-footer py-2 bg-light bg-opacity-50">
                <div class="d-flex flex-wrap align-items-center justify-content-between gap-2">
                    <p class="mb-0 text-muted fs-12">Showing {{ $customers->firstItem() ?? 0 }} to {{ $customers->lastItem() ?? 0 }} of {{ $customers->total() }} clients</p>
                    <div>{{ $customers->links('pagination::bootstrap-5') }}</div>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection
