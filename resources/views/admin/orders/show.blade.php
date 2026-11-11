@extends('layouts.admin')

@section('title', 'Order #' . $order['id'])
@section('container_class', 'container-xxl')

@section('content')
<div class="row">
    <!-- Left Column: Order Header, 5-Step Fulfillment Progress & Line Items -->
    <div class="col-xl-9 col-lg-8">

        <!-- Card 1: Order Header & 5-Step Fulfillment Bar -->
        <div class="card">
            <div class="card-header border-bottom">
                <div class="d-flex flex-wrap align-items-center justify-content-between gap-2">
                    <div>
                        <h4 class="card-title mb-1">
                            Order #{{ $order['id'] }}
                            <span class="badge bg-success-subtle text-success fs-12 ms-2">Paid</span>
                            @php
                                $badgeClass = match($order['status']) {
                                    'Delivered' => 'bg-success-subtle text-success',
                                    'Shipped' => 'bg-info-subtle text-info',
                                    'Processing' => 'bg-warning-subtle text-warning',
                                    default => 'bg-secondary-subtle text-secondary',
                                };
                            @endphp
                            <span class="badge {{ $badgeClass }} fs-12">{{ $order['status'] }}</span>
                        </h4>
                        <p class="text-muted mb-0 fs-13">Placed on {{ $order['date'] }} via {{ $order['delivery_method'] }}</p>
                    </div>

                    <div class="d-flex gap-2">
                        <a href="{{ route('admin.invoices.show', $order['id']) }}" class="btn btn-sm btn-outline-secondary">
                            <iconify-icon icon="solar:printer-bold" class="me-1"></iconify-icon> Print Invoice
                        </a>
                        <form action="{{ route('admin.orders.updateStatus', $order['id']) }}" method="POST">
                            @csrf
                            <button type="submit" class="btn btn-sm btn-primary">
                                <iconify-icon icon="solar:check-read-bold" class="me-1"></iconify-icon> Update Status
                            </button>
                        </form>
                    </div>
                </div>
            </div>

            <!-- 5-Step Progress Bar -->
            <div class="card-body">
                @php
                    $step = $order['current_step'] ?? $order['status'];
                    $isProcessing = in_array($step, ['Processing', 'Shipped', 'Delivered']);
                    $isShipped = in_array($step, ['Shipped', 'Delivered']);
                    $isDelivered = $step === 'Delivered';
                @endphp

                <div class="row row-cols-xxl-5 row-cols-md-3 row-cols-1 g-3">
                    <!-- Step 1: Placed -->
                    <div class="col">
                        <div class="progress" style="height: 8px;">
                            <div class="progress-bar progress-bar-striped progress-bar-animated bg-success" style="width: 100%"></div>
                        </div>
                        <p class="mb-0 mt-2 fs-13 fw-semibold text-success">
                            <i class="bx bx-check-circle me-1"></i> Order Placed
                        </p>
                    </div>

                    <!-- Step 2: Payment Confirmed -->
                    <div class="col">
                        <div class="progress" style="height: 8px;">
                            <div class="progress-bar progress-bar-striped progress-bar-animated bg-success" style="width: 100%"></div>
                        </div>
                        <p class="mb-0 mt-2 fs-13 fw-semibold text-success">
                            <i class="bx bx-check-circle me-1"></i> Payment Cleared
                        </p>
                    </div>

                    <!-- Step 3: Packaging / Processing -->
                    <div class="col">
                        <div class="progress" style="height: 8px;">
                            @if($isShipped || $isDelivered)
                                <div class="progress-bar progress-bar-striped progress-bar-animated bg-success" style="width: 100%"></div>
                            @elseif($isProcessing)
                                <div class="progress-bar progress-bar-striped progress-bar-animated bg-warning" style="width: 60%"></div>
                            @else
                                <div class="progress-bar bg-light" style="width: 0%"></div>
                            @endif
                        </div>
                        <div class="d-flex align-items-center gap-1 mt-2">
                            <p class="mb-0 fs-13 fw-semibold {{ $isProcessing && !$isShipped ? 'text-warning' : ($isShipped ? 'text-success' : 'text-muted') }}">
                                Botanical Packaging
                            </p>
                            @if($isProcessing && !$isShipped)
                                <div class="spinner-border spinner-border-sm text-warning" role="status"></div>
                            @endif
                        </div>
                    </div>

                    <!-- Step 4: Shipped -->
                    <div class="col">
                        <div class="progress" style="height: 8px;">
                            @if($isDelivered)
                                <div class="progress-bar progress-bar-striped progress-bar-animated bg-success" style="width: 100%"></div>
                            @elseif($isShipped)
                                <div class="progress-bar progress-bar-striped progress-bar-animated bg-warning" style="width: 60%"></div>
                            @else
                                <div class="progress-bar bg-light" style="width: 0%"></div>
                            @endif
                        </div>
                        <p class="mb-0 mt-2 fs-13 fw-semibold {{ $isShipped ? 'text-warning' : 'text-muted' }}">
                            Courier In Transit
                        </p>
                    </div>

                    <!-- Step 5: Delivered -->
                    <div class="col">
                        <div class="progress" style="height: 8px;">
                            <div class="progress-bar progress-bar-striped progress-bar-animated {{ $isDelivered ? 'bg-success' : 'bg-light' }}" style="width: {{ $isDelivered ? '100%' : '0%' }}"></div>
                        </div>
                        <p class="mb-0 mt-2 fs-13 fw-semibold {{ $isDelivered ? 'text-success' : 'text-muted' }}">
                            Delivered to Client
                        </p>
                    </div>
                </div>
            </div>
        </div>

        <!-- Card 2: Line Items -->
        <div class="card">
            <div class="card-header">
                <h4 class="card-title mb-0">Ordered Botanical Items ({{ count($order['items'] ?? []) }})</h4>
            </div>
            <div class="card-body p-0">
                <div class="table-responsive">
                    <table class="table align-middle mb-0 table-hover">
                        <thead class="bg-light-subtle">
                            <tr>
                                <th>Formulation</th>
                                <th>Size Variant</th>
                                <th>Unit Price</th>
                                <th>Qty</th>
                                <th class="text-end">Total</th>
                            </tr>
                        </thead>
                        <tbody>
                            @foreach($order['items'] ?? [] as $item)
                            <tr>
                                <td>
                                    <div class="d-flex align-items-center gap-3">
                                        <div class="avatar-md bg-light rounded d-flex align-items-center justify-content-center overflow-hidden border">
                                            <img src="{{ asset('images/' . ($item['image'] ?? 'products/botanical-radiance-serum-01.jpg')) }}" alt="" class="avatar-md object-fit-cover">
                                        </div>
                                        <div>
                                            <h5 class="fs-14 fw-semibold text-dark mb-1">{{ $item['name'] }}</h5>
                                            <p class="text-muted mb-0 fs-12">Amber Glass Dropper Vessel</p>
                                        </div>
                                    </div>
                                </td>
                                <td>
                                    <span class="badge bg-light text-dark border">{{ $item['variant'] ?? '30ml / 1.0 fl oz' }}</span>
                                </td>
                                <td>${{ number_format($item['price'], 2) }}</td>
                                <td>{{ $item['qty'] }}</td>
                                <td class="text-end fw-bold text-dark">
                                    ${{ number_format($item['price'] * $item['qty'], 2) }}
                                </td>
                            </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>
            </div>
        </div>

        <!-- Card 3: Fulfillment & Tracking Details -->
        <div class="card">
            <div class="card-header">
                <h4 class="card-title mb-0">Shipment Tracking & Laboratory Dispatch</h4>
            </div>
            <div class="card-body">
                <div class="row g-3">
                    <div class="col-md-4">
                        <p class="text-muted mb-1 fs-12">Carrier & Courier Service</p>
                        <h6 class="fw-semibold text-dark mb-0">{{ $order['delivery_method'] }}</h6>
                    </div>
                    <div class="col-md-4">
                        <p class="text-muted mb-1 fs-12">Tracking Number</p>
                        <h6 class="fw-semibold text-primary mb-0 font-monospace">{{ $order['tracking_number'] ?? 'Pending assignment' }}</h6>
                    </div>
                    <div class="col-md-4">
                        <p class="text-muted mb-1 fs-12">Laboratory Hub</p>
                        <h6 class="fw-semibold text-dark mb-0">Portland Botanical Studio (OR-97201)</h6>
                    </div>
                </div>
            </div>
        </div>

    </div>

    <!-- Right Column: Summary, Customer, Shipping & Payment Cards -->
    <div class="col-xl-3 col-lg-4">

        <!-- Card 1: Order Financial Summary -->
        <div class="card">
            <div class="card-header bg-light-subtle">
                <h5 class="card-title mb-0">Order Summary</h5>
            </div>
            <div class="card-body">
                <div class="d-flex justify-content-between mb-2">
                    <span class="text-muted">Subtotal</span>
                    <span class="fw-medium">${{ number_format($order['subtotal'], 2) }}</span>
                </div>
                <div class="d-flex justify-content-between mb-2">
                    <span class="text-muted">Botanical Shipping</span>
                    <span class="text-success fw-medium">
                        {{ $order['shipping'] > 0 ? ('$' . number_format($order['shipping'], 2)) : 'Complimentary' }}
                    </span>
                </div>
                <div class="d-flex justify-content-between mb-3">
                    <span class="text-muted">Estimated Tax</span>
                    <span class="fw-medium">${{ number_format($order['tax'], 2) }}</span>
                </div>
                <hr>
                <div class="d-flex justify-content-between">
                    <span class="fw-bold text-dark fs-16">Total Booked</span>
                    <span class="fw-bold text-primary fs-18">${{ number_format($order['total'], 2) }}</span>
                </div>
            </div>
        </div>

        <!-- Card 2: Customer Profile -->
        <div class="card">
            <div class="card-header bg-light-subtle">
                <h5 class="card-title mb-0">Client Details</h5>
            </div>
            <div class="card-body">
                <div class="d-flex align-items-center gap-3 mb-3">
                    <div class="avatar-md bg-primary-subtle text-primary rounded-circle d-flex align-items-center justify-content-center fw-bold fs-16">
                        {{ substr($order['shipping_address']['first_name'], 0, 1) }}{{ substr($order['shipping_address']['last_name'], 0, 1) }}
                    </div>
                    <div>
                        <h5 class="fs-15 fw-semibold mb-1">
                            {{ $order['shipping_address']['first_name'] }} {{ $order['shipping_address']['last_name'] }}
                        </h5>
                        <p class="text-muted mb-0 fs-12">Everbloom Client #428</p>
                    </div>
                </div>

                <div class="mb-2">
                    <p class="text-muted mb-1 fs-12">Email</p>
                    <a href="mailto:{{ $customer['email'] ?? 'eleanor.vance@example.com' }}" class="text-dark fw-medium fs-13">
                        {{ $customer['email'] ?? 'eleanor.vance@example.com' }}
                    </a>
                </div>
                <div>
                    <p class="text-muted mb-1 fs-12">Phone</p>
                    <span class="text-dark fw-medium fs-13">{{ $order['shipping_address']['phone'] ?? $customer['phone'] ?? '+1 (503) 555-0192' }}</span>
                </div>
            </div>
        </div>

        <!-- Card 3: Shipping Address -->
        <div class="card">
            <div class="card-header bg-light-subtle">
                <h5 class="card-title mb-0">Shipping Destination</h5>
            </div>
            <div class="card-body">
                <p class="fw-semibold text-dark mb-1">
                    {{ $order['shipping_address']['first_name'] }} {{ $order['shipping_address']['last_name'] }}
                </p>
                <p class="text-muted mb-0 fs-13">
                    {{ $order['shipping_address']['address_line1'] }}<br>
                    @if(!empty($order['shipping_address']['address_line2']))
                        {{ $order['shipping_address']['address_line2'] }}<br>
                    @endif
                    {{ $order['shipping_address']['city'] }}, {{ $order['shipping_address']['state'] }} {{ $order['shipping_address']['postal_code'] }}<br>
                    {{ $order['shipping_address']['country'] }}
                </p>
            </div>
        </div>

        <!-- Card 4: Payment Details -->
        <div class="card">
            <div class="card-header bg-light-subtle">
                <h5 class="card-title mb-0">Payment Method</h5>
            </div>
            <div class="card-body">
                <div class="d-flex align-items-center gap-2 mb-2">
                    <iconify-icon icon="solar:card-2-bold-duotone" class="fs-24 text-primary"></iconify-icon>
                    <span class="fw-semibold text-dark">{{ $order['payment_method'] }}</span>
                </div>
                <p class="text-muted mb-0 fs-12">Status: <span class="badge bg-success-subtle text-success">Captured</span></p>
            </div>
        </div>

    </div>
</div>
@endsection
