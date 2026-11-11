@extends('layouts.admin')

@section('title', 'Orders List')
@section('container_class', 'container-xxl')

@section('content')
<!-- Row 1: Status Counter Cards -->
<div class="row">
    <!-- All Orders -->
    <div class="col-md-6 col-xl-3">
        <div class="card">
            <div class="card-body">
                <div class="d-flex align-items-center justify-content-between">
                    <div>
                        <h4 class="card-title mb-2">All Orders</h4>
                        <p class="text-muted fw-medium fs-22 mb-0">{{ $allCount }}</p>
                    </div>
                    <div>
                        <div class="avatar-md bg-primary bg-opacity-10 rounded">
                            <iconify-icon icon="solar:bag-smile-bold-duotone" class="fs-32 text-primary avatar-title"></iconify-icon>
                        </div>
                    </div>
                </div>
            </div>
            <div class="card-footer py-2 bg-light bg-opacity-50">
                <a href="{{ route('admin.orders.index') }}" class="text-reset fw-semibold fs-12">View All &rarr;</a>
            </div>
        </div>
    </div>

    <!-- Processing -->
    <div class="col-md-6 col-xl-3">
        <div class="card">
            <div class="card-body">
                <div class="d-flex align-items-center justify-content-between">
                    <div>
                        <h4 class="card-title mb-2">Processing</h4>
                        <p class="text-muted fw-medium fs-22 mb-0">{{ $processingCount }}</p>
                    </div>
                    <div>
                        <div class="avatar-md bg-warning bg-opacity-10 rounded">
                            <iconify-icon icon="solar:clock-circle-bold-duotone" class="fs-32 text-warning avatar-title"></iconify-icon>
                        </div>
                    </div>
                </div>
            </div>
            <div class="card-footer py-2 bg-light bg-opacity-50">
                <a href="{{ route('admin.orders.index', ['status' => 'processing']) }}" class="text-reset fw-semibold fs-12">Filter Processing &rarr;</a>
            </div>
        </div>
    </div>

    <!-- Shipped -->
    <div class="col-md-6 col-xl-3">
        <div class="card">
            <div class="card-body">
                <div class="d-flex align-items-center justify-content-between">
                    <div>
                        <h4 class="card-title mb-2">Dispatched</h4>
                        <p class="text-muted fw-medium fs-22 mb-0">{{ $shippedCount }}</p>
                    </div>
                    <div>
                        <div class="avatar-md bg-info bg-opacity-10 rounded">
                            <iconify-icon icon="solar:box-bold-duotone" class="fs-32 text-info avatar-title"></iconify-icon>
                        </div>
                    </div>
                </div>
            </div>
            <div class="card-footer py-2 bg-light bg-opacity-50">
                <a href="{{ route('admin.orders.index', ['status' => 'shipped']) }}" class="text-reset fw-semibold fs-12">Filter Dispatched &rarr;</a>
            </div>
        </div>
    </div>

    <!-- Delivered -->
    <div class="col-md-6 col-xl-3">
        <div class="card">
            <div class="card-body">
                <div class="d-flex align-items-center justify-content-between">
                    <div>
                        <h4 class="card-title mb-2">Delivered</h4>
                        <p class="text-muted fw-medium fs-22 mb-0">{{ $deliveredCount }}</p>
                    </div>
                    <div>
                        <div class="avatar-md bg-success bg-opacity-10 rounded">
                            <iconify-icon icon="solar:clipboard-check-bold-duotone" class="fs-32 text-success avatar-title"></iconify-icon>
                        </div>
                    </div>
                </div>
            </div>
            <div class="card-footer py-2 bg-light bg-opacity-50">
                <a href="{{ route('admin.orders.index', ['status' => 'delivered']) }}" class="text-reset fw-semibold fs-12">Filter Delivered &rarr;</a>
            </div>
        </div>
    </div>
</div>

<!-- Row 2: All Orders Table -->
<div class="row">
    <div class="col-12">
        <div class="card">
            <div class="card-header d-flex flex-wrap justify-content-between align-items-center gap-2">
                <h4 class="card-title mb-0">Customer Orders ({{ count($orders) }})</h4>

                <div class="d-flex flex-wrap align-items-center gap-2">
                    <!-- Status Filter Tabs -->
                    <div class="btn-group btn-group-sm" role="group">
                        <a href="{{ route('admin.orders.index') }}" class="btn btn-outline-secondary {{ !request('status') ? 'active' : '' }}">All</a>
                        <a href="{{ route('admin.orders.index', ['status' => 'processing']) }}" class="btn btn-outline-secondary {{ request('status') === 'processing' ? 'active' : '' }}">Processing</a>
                        <a href="{{ route('admin.orders.index', ['status' => 'shipped']) }}" class="btn btn-outline-secondary {{ request('status') === 'shipped' ? 'active' : '' }}">Shipped</a>
                        <a href="{{ route('admin.orders.index', ['status' => 'delivered']) }}" class="btn btn-outline-secondary {{ request('status') === 'delivered' ? 'active' : '' }}">Delivered</a>
                    </div>
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
                                <th>Order ID</th>
                                <th>Date</th>
                                <th>Customer</th>
                                <th>Items</th>
                                <th>Total</th>
                                <th>Payment Method</th>
                                <th>Fulfillment Status</th>
                                <th class="text-end">Actions</th>
                            </tr>
                        </thead>
                        <tbody>
                            @forelse($orders as $order)
                            <tr>
                                <td>
                                    <div class="form-check ms-1">
                                        <input type="checkbox" class="form-check-input">
                                    </div>
                                </td>
                                <td>
                                    <a href="{{ route('admin.orders.show', $order['id']) }}" class="fw-bold text-primary">
                                        #{{ $order['id'] }}
                                    </a>
                                </td>
                                <td>
                                    <span class="fs-13">{{ $order['date'] }}</span>
                                </td>
                                <td>
                                    <div class="d-flex align-items-center gap-2">
                                        <div class="avatar-xs bg-primary-subtle text-primary rounded-circle d-flex align-items-center justify-content-center fw-bold fs-12">
                                            {{ substr($order['shipping_address']['first_name'], 0, 1) }}{{ substr($order['shipping_address']['last_name'], 0, 1) }}
                                        </div>
                                        <div>
                                            <span class="fw-medium text-dark d-block fs-13">
                                                {{ $order['shipping_address']['first_name'] }} {{ $order['shipping_address']['last_name'] }}
                                            </span>
                                            <small class="text-muted">{{ $order['shipping_address']['city'] }}, {{ $order['shipping_address']['state'] }}</small>
                                        </div>
                                    </div>
                                </td>
                                <td>
                                    <div class="d-flex align-items-center gap-2">
                                        <div class="avatar-sm bg-light rounded d-flex align-items-center justify-content-center overflow-hidden border">
                                            <img src="{{ asset('images/' . ($order['items'][0]['image'] ?? 'products/botanical-radiance-serum-01.jpg')) }}" alt="" class="avatar-sm object-fit-cover">
                                        </div>
                                        <div>
                                            <span class="fs-13 fw-medium text-dark">{{ $order['items'][0]['name'] ?? 'Botanical Treatment' }}</span>
                                            @if(($order['items_count'] ?? 1) > 1)
                                                <p class="mb-0 text-muted fs-11">+{{ $order['items_count'] - 1 }} additional item(s)</p>
                                            @endif
                                        </div>
                                    </div>
                                </td>
                                <td class="fw-bold text-dark">
                                    ${{ number_format($order['total'], 2) }}
                                </td>
                                <td>
                                    <span class="badge bg-light text-muted fs-12 border">
                                        {{ $order['payment_method'] }}
                                    </span>
                                </td>
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
                                    <div class="d-flex align-items-center justify-content-end gap-1">
                                        <a href="{{ route('admin.orders.show', $order['id']) }}" class="btn btn-sm btn-soft-primary" title="View Full Order Details">
                                            <iconify-icon icon="solar:eye-broken" class="fs-16"></iconify-icon>
                                        </a>
                                        <a href="{{ route('admin.invoices.show', $order['id']) }}" class="btn btn-sm btn-soft-secondary" title="View Printable Invoice">
                                            <iconify-icon icon="solar:bill-list-broken" class="fs-16"></iconify-icon>
                                        </a>
                                    </div>
                                </td>
                            </tr>
                            @empty
                            <tr>
                                <td colspan="9" class="text-center py-5 text-muted">No orders found matching this filter.</td>
                            </tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>
            </div>

            <div class="card-footer py-2 bg-light bg-opacity-50">
                <p class="mb-0 text-muted fs-12">Displaying {{ count($orders) }} botanical skincare orders</p>
            </div>
        </div>
    </div>
</div>
@endsection
