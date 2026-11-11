@extends('layouts.admin')

@section('title', 'Client Profile: ' . $customer['name'])
@section('container_class', 'container-xxl')

@section('content')
<div class="row">
    <!-- Left Column: Customer Profile & Saved Addresses -->
    <div class="col-xl-4 col-lg-5">
        <!-- Profile Card -->
        <div class="card text-center">
            <div class="card-body">
                <div class="position-relative d-inline-block mb-3">
                    <img src="{{ asset($customer['avatar']) }}" alt="{{ $customer['name'] }}" class="avatar-xl rounded-circle object-fit-cover border border-3 border-light shadow-sm">
                    <span class="position-absolute bottom-0 end-0 p-1 bg-success border border-light rounded-circle"></span>
                </div>
                <h4 class="mb-1 text-dark fw-bold">{{ $customer['name'] }}</h4>
                <p class="text-muted fs-13 mb-3">{{ $customer['city'] }}</p>

                <div class="d-flex justify-content-center gap-2 mb-3">
                    <span class="badge bg-primary-subtle text-primary px-3 py-1 fs-12">VIP Botanical Member</span>
                    <span class="badge bg-success-subtle text-success px-3 py-1 fs-12">{{ $customer['status'] }}</span>
                </div>

                <hr>

                <div class="text-start">
                    <p class="text-muted mb-2 fs-13"><strong class="text-dark">Email:</strong> <a href="mailto:{{ $customer['email'] }}" class="text-reset">{{ $customer['email'] }}</a></p>
                    <p class="text-muted mb-2 fs-13"><strong class="text-dark">Phone:</strong> {{ $customer['phone'] }}</p>
                    <p class="text-muted mb-2 fs-13"><strong class="text-dark">Member Since:</strong> {{ $customer['join_date'] }}</p>
                    <p class="text-muted mb-0 fs-13"><strong class="text-dark">Preferred Ritual:</strong> Evening Restorative Hydration</p>
                </div>
            </div>
        </div>

        <!-- Saved Addresses Card -->
        <div class="card">
            <div class="card-header bg-light-subtle">
                <h5 class="card-title mb-0">Saved Delivery Addresses</h5>
            </div>
            <div class="card-body">
                @foreach($addresses as $addr)
                <div class="border rounded p-3 mb-3 {{ !empty($addr['is_default']) ? 'border-primary bg-primary-subtle bg-opacity-10' : '' }}">
                    <div class="d-flex justify-content-between align-items-center mb-1">
                        <span class="fw-semibold text-dark fs-14">{{ $addr['first_name'] }} {{ $addr['last_name'] }}</span>
                        @if(!empty($addr['is_default']))
                            <span class="badge bg-primary fs-11">Default</span>
                        @endif
                    </div>
                    @if(!empty($addr['company']))
                        <p class="text-muted mb-1 fs-12">{{ $addr['company'] }}</p>
                    @endif
                    <p class="text-muted mb-0 fs-13">
                        {{ $addr['address_line1'] }}{{ !empty($addr['address_line2']) ? (', ' . $addr['address_line2']) : '' }}<br>
                        {{ $addr['city'] }}, {{ $addr['state'] }} {{ $addr['postal_code'] }}<br>
                        {{ $addr['country'] }}
                    </p>
                </div>
                @endforeach
            </div>
        </div>
    </div>

    <!-- Right Column: Lifetime Stats + Order History -->
    <div class="col-xl-8 col-lg-7">
        <!-- Lifetime Metrics -->
        <div class="row">
            <div class="col-md-4">
                <div class="card">
                    <div class="card-body text-center">
                        <p class="text-muted mb-1 fs-13">Total Orders</p>
                        <h3 class="fw-bold text-dark mb-0">{{ $customer['orders_count'] }}</h3>
                    </div>
                </div>
            </div>
            <div class="col-md-4">
                <div class="card">
                    <div class="card-body text-center">
                        <p class="text-muted mb-1 fs-13">Lifetime Spend</p>
                        <h3 class="fw-bold text-primary mb-0">${{ number_format($customer['total_spent'], 2) }}</h3>
                    </div>
                </div>
            </div>
            <div class="col-md-4">
                <div class="card">
                    <div class="card-body text-center">
                        <p class="text-muted mb-1 fs-13">Botanical Bloom Points</p>
                        <h3 class="fw-bold text-success mb-0">580 pts</h3>
                    </div>
                </div>
            </div>
        </div>

        <!-- Orders History Table Card -->
        <div class="card">
            <div class="card-header">
                <h4 class="card-title mb-0">Order History ({{ count($orders) }})</h4>
            </div>
            <div class="card-body p-0">
                <div class="table-responsive">
                    <table class="table align-middle mb-0 table-hover table-centered">
                        <thead class="bg-light-subtle">
                            <tr>
                                <th>Order ID</th>
                                <th>Date</th>
                                <th>Items</th>
                                <th>Total</th>
                                <th>Status</th>
                                <th class="text-end">Action</th>
                            </tr>
                        </thead>
                        <tbody>
                            @foreach($orders as $o)
                            <tr>
                                <td>
                                    <a href="{{ route('admin.orders.show', $o['id']) }}" class="fw-bold text-primary">#{{ $o['id'] }}</a>
                                </td>
                                <td>{{ $o['date'] }}</td>
                                <td>{{ $o['items_count'] }} item(s)</td>
                                <td class="fw-bold text-dark">${{ number_format($o['total'], 2) }}</td>
                                <td>
                                    @php
                                        $badgeClass = match($o['status']) {
                                            'Delivered' => 'bg-success-subtle text-success',
                                            'Shipped' => 'bg-info-subtle text-info',
                                            'Processing' => 'bg-warning-subtle text-warning',
                                            default => 'bg-secondary-subtle text-secondary',
                                        };
                                    @endphp
                                    <span class="badge {{ $badgeClass }}">{{ $o['status'] }}</span>
                                </td>
                                <td class="text-end">
                                    <a href="{{ route('admin.orders.show', $o['id']) }}" class="btn btn-sm btn-soft-primary" title="View Order">
                                        <iconify-icon icon="solar:eye-broken" class="fs-16"></iconify-icon>
                                    </a>
                                </td>
                            </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection
