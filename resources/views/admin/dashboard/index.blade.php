@extends('layouts.admin')

@section('title', 'Welcome!')
@section('container_class', 'container-fluid')

@section('content')
<!-- Row 1: Promo banner + KPI Cards + Performance Chart -->
<div class="row">
    <!-- Left Column: Banner + 4 KPI Cards -->
    <div class="col-xxl-5">
        <div class="alert alert-primary text-truncate mb-3" role="alert">
            <i class="bx bx-broadcast fs-16 align-middle me-1"></i> Storefront Active: <strong>Free standard ground shipping over $75</strong> promo is currently live.
        </div>

        <div class="row">
            <!-- Total Orders -->
            <div class="col-md-6">
                <div class="card overflow-hidden">
                    <div class="card-body">
                        <div class="row">
                            <div class="col-6">
                                <div class="avatar-md bg-soft-primary rounded">
                                    <iconify-icon icon="solar:cart-5-bold-duotone" class="avatar-title fs-32 text-primary"></iconify-icon>
                                </div>
                            </div>
                            <div class="col-6 text-end">
                                <p class="text-muted mb-0 text-truncate">Total Orders</p>
                                <h3 class="text-dark mt-1 mb-0">{{ number_format($totalOrders) }}</h3>
                            </div>
                        </div>
                    </div>
                    <div class="card-footer py-2 bg-light bg-opacity-50">
                        <div class="d-flex align-items-center justify-content-between">
                            <div>
                                <span class="text-success"><i class="bx bxs-up-arrow fs-12"></i> 12.5%</span>
                                <span class="text-muted ms-1 fs-12">vs last month</span>
                            </div>
                            <a href="{{ route('admin.orders.index') }}" class="text-reset fw-semibold fs-12">View All</a>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Total Customers -->
            <div class="col-md-6">
                <div class="card overflow-hidden">
                    <div class="card-body">
                        <div class="row">
                            <div class="col-6">
                                <div class="avatar-md bg-soft-primary rounded">
                                    <iconify-icon icon="solar:users-group-two-rounded-bold-duotone" class="avatar-title fs-32 text-primary"></iconify-icon>
                                </div>
                            </div>
                            <div class="col-6 text-end">
                                <p class="text-muted mb-0 text-truncate">Customers</p>
                                <h3 class="text-dark mt-1 mb-0">{{ number_format($totalCustomers) }}</h3>
                            </div>
                        </div>
                    </div>
                    <div class="card-footer py-2 bg-light bg-opacity-50">
                        <div class="d-flex align-items-center justify-content-between">
                            <div>
                                <span class="text-success"><i class="bx bxs-up-arrow fs-12"></i> 18.2%</span>
                                <span class="text-muted ms-1 fs-12">vs last month</span>
                            </div>
                            <a href="{{ route('admin.customers.index') }}" class="text-reset fw-semibold fs-12">View All</a>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Botanical SKUs -->
            <div class="col-md-6">
                <div class="card overflow-hidden">
                    <div class="card-body">
                        <div class="row">
                            <div class="col-6">
                                <div class="avatar-md bg-soft-primary rounded">
                                    <iconify-icon icon="solar:box-bold-duotone" class="avatar-title fs-32 text-primary"></iconify-icon>
                                </div>
                            </div>
                            <div class="col-6 text-end">
                                <p class="text-muted mb-0 text-truncate">Active SKUs</p>
                                <h3 class="text-dark mt-1 mb-0">{{ count($products) }}</h3>
                            </div>
                        </div>
                    </div>
                    <div class="card-footer py-2 bg-light bg-opacity-50">
                        <div class="d-flex align-items-center justify-content-between">
                            <div>
                                <span class="text-muted fs-12">{{ count($categories) }} Categories</span>
                            </div>
                            <a href="{{ route('admin.products.index') }}" class="text-reset fw-semibold fs-12">Manage</a>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Booked Revenue -->
            <div class="col-md-6">
                <div class="card overflow-hidden">
                    <div class="card-body">
                        <div class="row">
                            <div class="col-6">
                                <div class="avatar-md bg-soft-primary rounded">
                                    <iconify-icon icon="solar:wallet-bold-duotone" class="avatar-title fs-32 text-primary"></iconify-icon>
                                </div>
                            </div>
                            <div class="col-6 text-end">
                                <p class="text-muted mb-0 text-truncate">Booked Revenue</p>
                                <h3 class="text-dark mt-1 mb-0">${{ number_format($totalRevenue, 2) }}</h3>
                            </div>
                        </div>
                    </div>
                    <div class="card-footer py-2 bg-light bg-opacity-50">
                        <div class="d-flex align-items-center justify-content-between">
                            <div>
                                <span class="text-success"><i class="bx bxs-up-arrow fs-12"></i> 23.4%</span>
                                <span class="text-muted ms-1 fs-12">vs last month</span>
                            </div>
                            <a href="{{ route('admin.invoices.index') }}" class="text-reset fw-semibold fs-12">Invoices</a>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Right Column: Performance Mixed Chart Card -->
    <div class="col-xxl-7">
        <div class="card">
            <div class="card-body">
                <div class="d-flex justify-content-between align-items-center">
                    <h4 class="card-title">Botanical Sales & Traffic Performance</h4>
                    <div>
                        <button type="button" class="btn btn-sm btn-outline-light">ALL</button>
                        <button type="button" class="btn btn-sm btn-outline-light">1M</button>
                        <button type="button" class="btn btn-sm btn-outline-light">6M</button>
                        <button type="button" class="btn btn-sm btn-outline-light active">1Y</button>
                    </div>
                </div>
                <div dir="ltr">
                    <div id="dash-performance-chart" class="apex-charts"></div>
                </div>
            </div>
        </div>
    </div>
</div>

<!-- Row 2: Conversions + Sessions Map + Top Botanical Products -->
<div class="row">
    <!-- Conversions Radial Bar -->
    <div class="col-lg-4">
        <div class="card">
            <div class="card-body">
                <h4 class="card-title">Returning Customers</h4>
                <div dir="ltr">
                    <div id="conversions" class="apex-charts"></div>
                </div>
                <div class="row text-center mt-2">
                    <div class="col-6">
                        <p class="text-muted mb-1">This Week</p>
                        <h4 class="text-dark mb-0">65.2%</h4>
                    </div>
                    <div class="col-6">
                        <p class="text-muted mb-1">Last Week</p>
                        <h4 class="text-dark mb-0">58.7%</h4>
                    </div>
                </div>
            </div>
            <div class="card-footer py-2 bg-light bg-opacity-50 text-center">
                <a href="{{ route('admin.customers.index') }}" class="text-reset fw-semibold fs-12">View Customer Segments &rarr;</a>
            </div>
        </div>
    </div>

    <!-- Sessions by Country (Vector Map) -->
    <div class="col-lg-4">
        <div class="card">
            <div class="card-body">
                <h4 class="card-title">Customer Distribution</h4>
                <div id="world-map-markers" style="height: 316px"></div>
            </div>
        </div>
    </div>

    <!-- Top Botanical Formulations -->
    <div class="col-lg-4">
        <div class="card card-height-100">
            <div class="card-header d-flex align-items-center justify-content-between">
                <h4 class="card-title mb-0">Top Formulations</h4>
                <a href="{{ route('admin.products.index') }}" class="text-reset fw-semibold fs-12">View All</a>
            </div>
            <div class="card-body p-0">
                <div class="table-responsive">
                    <table class="table table-hover table-nowrap align-middle mb-0">
                        <thead class="bg-light-subtle">
                            <tr>
                                <th>Product</th>
                                <th>Category</th>
                                <th>Price</th>
                                <th>Rating</th>
                            </tr>
                        </thead>
                        <tbody>
                            @foreach($topProducts as $topP)
                            <tr>
                                <td>
                                    <div class="d-flex align-items-center gap-2">
                                        <div class="avatar-sm bg-light rounded d-flex align-items-center justify-content-center overflow-hidden">
                                            <img src="{{ asset('images/' . ($topP['images'][0] ?? 'products/botanical-radiance-serum-01.jpg')) }}" alt="" class="avatar-sm object-fit-cover">
                                        </div>
                                        <div>
                                            <a href="{{ route('admin.products.edit', $topP['id']) }}" class="text-dark fw-medium fs-13 text-truncate d-inline-block" style="max-width: 130px;">{{ $topP['name'] }}</a>
                                        </div>
                                    </div>
                                </td>
                                <td><span class="badge bg-light text-dark fs-11">{{ $topP['category'] }}</span></td>
                                <td class="fw-medium">${{ number_format($topP['price'], 2) }}</td>
                                <td><i class="bx bxs-star text-warning"></i> {{ $topP['rating'] }}</td>
                            </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
    </div>
</div>

<!-- Row 3: Recent Orders Table Card -->
<div class="row">
    <div class="col-12">
        <div class="card">
            <div class="card-header d-flex justify-content-between align-items-center gap-1">
                <h4 class="card-title mb-0">Recent Orders</h4>
                <a href="{{ route('admin.orders.index') }}" class="btn btn-sm btn-soft-primary">View All Orders</a>
            </div>
            <div class="card-body p-0">
                <div class="table-responsive">
                    <table class="table align-middle mb-0 table-hover table-centered">
                        <thead class="bg-light-subtle">
                            <tr>
                                <th>Order ID</th>
                                <th>Date</th>
                                <th>Items</th>
                                <th>Customer</th>
                                <th>Total</th>
                                <th>Payment Method</th>
                                <th>Status</th>
                                <th class="text-end">Action</th>
                            </tr>
                        </thead>
                        <tbody>
                            @forelse($recentOrders as $order)
                            <tr>
                                <td>
                                    <a href="{{ route('admin.orders.show', $order['id']) }}" class="fw-semibold text-primary">
                                        #{{ $order['id'] }}
                                    </a>
                                </td>
                                <td>{{ $order['date'] }}</td>
                                <td>
                                    <div class="d-flex align-items-center gap-2">
                                        <div class="avatar-sm bg-light rounded d-flex align-items-center justify-content-center overflow-hidden">
                                            <img src="{{ asset('images/' . ($order['items'][0]['image'] ?? 'products/botanical-radiance-serum-01.jpg')) }}" alt="" class="avatar-sm object-fit-cover">
                                        </div>
                                        <span class="fs-13 text-dark fw-medium">{{ $order['items'][0]['name'] ?? 'Botanical Treatment' }}</span>
                                        @if(($order['items_count'] ?? 1) > 1)
                                            <span class="badge bg-light text-muted">+{{ $order['items_count'] - 1 }} more</span>
                                        @endif
                                    </div>
                                </td>
                                <td>
                                    <span class="fw-medium text-dark">{{ $order['shipping_address']['first_name'] }} {{ $order['shipping_address']['last_name'] }}</span>
                                    <p class="text-muted mb-0 fs-12">{{ $order['shipping_address']['city'] }}, {{ $order['shipping_address']['state'] }}</p>
                                </td>
                                <td class="fw-bold">${{ number_format($order['total'], 2) }}</td>
                                <td><span class="badge bg-light text-muted fs-12">{{ $order['payment_method'] }}</span></td>
                                <td>
                                    @php
                                        $badgeClass = match($order['status']) {
                                            'Delivered' => 'bg-success-subtle text-success',
                                            'Shipped' => 'bg-info-subtle text-info',
                                            'Processing' => 'bg-warning-subtle text-warning',
                                            default => 'bg-secondary-subtle text-secondary',
                                        };
                                    @endphp
                                    <span class="badge {{ $badgeClass }} px-2 py-1 fs-12">{{ $order['status'] }}</span>
                                </td>
                                <td class="text-end">
                                    <a href="{{ route('admin.orders.show', $order['id']) }}" class="btn btn-sm btn-soft-primary" title="View Order Details">
                                        <iconify-icon icon="solar:eye-broken" class="align-middle fs-16"></iconify-icon>
                                    </a>
                                </td>
                            </tr>
                            @empty
                            <tr>
                                <td colspan="8" class="text-center py-4 text-muted">No recent orders found.</td>
                            </tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>
            </div>
            <div class="card-footer py-2 bg-light bg-opacity-50">
                <div class="d-flex align-items-center justify-content-between">
                    <p class="mb-0 text-muted fs-12">Displaying {{ count($recentOrders) }} of {{ count($orders) }} orders</p>
                    <a href="{{ route('admin.orders.index') }}" class="text-reset fw-semibold fs-12">Go to Order Management &rarr;</a>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection

@push('scripts')
<!-- Vector Map Js -->
<script src="{{ asset('admin/assets/vendor/jsvectormap/js/jsvectormap.min.js') }}"></script>
<script src="{{ asset('admin/assets/vendor/jsvectormap/maps/world-merc.js') }}"></script>
<script src="{{ asset('admin/assets/vendor/jsvectormap/maps/world.js') }}"></script>

<!-- Dashboard Page Js (ApexCharts + Map) -->
<script src="{{ asset('admin/assets/js/pages/dashboard.js') }}"></script>
@endpush
