@extends('layouts.admin')

@section('title', 'Invoice #' . $order['id'])
@section('container_class', 'container-xxl')

@section('content')
<div class="row justify-content-center">
    <div class="col-lg-10">
        <div class="card">
            <div class="card-body p-4 p-md-5">
                <!-- Header with Logo and Print Button -->
                <div class="d-flex justify-content-between align-items-center mb-4 pb-3 border-bottom">
                    <div>
                        <img src="{{ asset('admin/assets/images/logo-dark.png') }}" alt="The Everbloom" height="32" class="mb-2">
                        <p class="text-muted mb-0 fs-13">Artisanal Botanical Skincare & Laboratory</p>
                    </div>
                    <div class="text-end">
                        <button type="button" class="btn btn-sm btn-primary d-print-none" onclick="window.print()">
                            <iconify-icon icon="solar:printer-bold" class="me-1"></iconify-icon> Print Invoice
                        </button>
                    </div>
                </div>

                <!-- Invoice Meta & Addresses -->
                <div class="row g-4 mb-4">
                    <div class="col-sm-6">
                        <h6 class="text-muted text-uppercase fs-12 fw-semibold">Billed & Shipped To</h6>
                        <h5 class="fw-bold text-dark mb-1">{{ $order['shipping_address']['first_name'] }} {{ $order['shipping_address']['last_name'] }}</h5>
                        <p class="text-muted mb-0 fs-13">
                            {{ $order['shipping_address']['address_line1'] }}<br>
                            @if(!empty($order['shipping_address']['address_line2']))
                                {{ $order['shipping_address']['address_line2'] }}<br>
                            @endif
                            {{ $order['shipping_address']['city'] }}, {{ $order['shipping_address']['state'] }} {{ $order['shipping_address']['postal_code'] }}<br>
                            {{ $order['shipping_address']['country'] }}<br>
                            Phone: {{ $order['shipping_address']['phone'] }}
                        </p>
                    </div>

                    <div class="col-sm-6 text-sm-end">
                        <h6 class="text-muted text-uppercase fs-12 fw-semibold">Invoice Information</h6>
                        <h4 class="fw-bold text-primary mb-1">#INV-2026-{{ substr($order['id'], -4) }}</h4>
                        <p class="text-muted mb-1 fs-13"><strong class="text-dark">Order Ref:</strong> #{{ $order['id'] }}</p>
                        <p class="text-muted mb-1 fs-13"><strong class="text-dark">Date:</strong> {{ $order['date'] }}</p>
                        <p class="text-muted mb-0 fs-13"><strong class="text-dark">Payment Method:</strong> {{ $order['payment_method'] }}</p>
                    </div>
                </div>

                <!-- Line Items Table -->
                <div class="table-responsive mb-4">
                    <table class="table table-bordered mb-0">
                        <thead class="bg-light-subtle">
                            <tr>
                                <th style="width: 50px;">#</th>
                                <th>Botanical Formulation Description</th>
                                <th style="width: 140px;">Size Variant</th>
                                <th class="text-end" style="width: 120px;">Unit Price</th>
                                <th class="text-center" style="width: 80px;">Qty</th>
                                <th class="text-end" style="width: 140px;">Total</th>
                            </tr>
                        </thead>
                        <tbody>
                            @foreach($order['items'] ?? [] as $idx => $item)
                            <tr>
                                <td>{{ $idx + 1 }}</td>
                                <td>
                                    <strong class="text-dark">{{ $item['name'] }}</strong>
                                    <p class="text-muted mb-0 fs-12">Amber Glass Vessel &bull; Cold-Pressed Botanical Extract</p>
                                </td>
                                <td>{{ $item['variant'] ?? '30ml / 1.0 fl oz' }}</td>
                                <td class="text-end">${{ number_format($item['price'], 2) }}</td>
                                <td class="text-center">{{ $item['qty'] }}</td>
                                <td class="text-end fw-semibold">${{ number_format($item['price'] * $item['qty'], 2) }}</td>
                            </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>

                <!-- Totals Breakdown -->
                <div class="row justify-content-end mb-4">
                    <div class="col-sm-6 col-md-5">
                        <table class="table table-borderless table-sm">
                            <tbody>
                                <tr>
                                    <td class="text-muted">Subtotal:</td>
                                    <td class="text-end fw-medium">${{ number_format($order['subtotal'], 2) }}</td>
                                </tr>
                                <tr>
                                    <td class="text-muted">Botanical Ground Shipping:</td>
                                    <td class="text-end text-success fw-medium">
                                        {{ $order['shipping'] > 0 ? ('$' . number_format($order['shipping'], 2)) : 'Complimentary' }}
                                    </td>
                                </tr>
                                <tr>
                                    <td class="text-muted">Estimated Sales Tax:</td>
                                    <td class="text-end fw-medium">${{ number_format($order['tax'], 2) }}</td>
                                </tr>
                                <tr class="border-top">
                                    <td class="fw-bold text-dark fs-15">Grand Total (USD):</td>
                                    <td class="text-end fw-bold text-primary fs-18">${{ number_format($order['total'], 2) }}</td>
                                </tr>
                            </tbody>
                        </table>
                    </div>
                </div>

                <!-- Footer Note -->
                <div class="border-top pt-3 text-center text-muted fs-12">
                    <p class="mb-1">Thank you for curating your botanical skincare rituals with The Everbloom.</p>
                    <p class="mb-0">Client Concierge: concierge@the-everbloom.com &bull; 740 Botanical Way, Suite 400, Portland, OR 97201</p>
                </div>

            </div>
        </div>
    </div>
</div>
@endsection
