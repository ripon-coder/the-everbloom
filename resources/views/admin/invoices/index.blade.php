@extends('layouts.admin')

@section('title', 'Invoices List')
@section('container_class', 'container-xxl')

@section('content')
<div class="row">
    <div class="col-12">
        <div class="card">
            <div class="card-header d-flex justify-content-between align-items-center">
                <h4 class="card-title mb-0">Customer Invoices & Billing Records ({{ count($invoices) }})</h4>
            </div>
            <div class="card-body p-0">
                <div class="table-responsive">
                    <table class="table align-middle mb-0 table-hover table-centered">
                        <thead class="bg-light-subtle">
                            <tr>
                                <th>Invoice No.</th>
                                <th>Order Ref</th>
                                <th>Client Name</th>
                                <th>Issue Date</th>
                                <th>Amount</th>
                                <th>Payment Method</th>
                                <th>Billing Status</th>
                                <th class="text-end">Actions</th>
                            </tr>
                        </thead>
                        <tbody>
                            @forelse($invoices as $inv)
                            <tr>
                                <td>
                                    <a href="{{ route('admin.invoices.show', $inv['order_id']) }}" class="fw-bold font-monospace text-primary">
                                        {{ $inv['invoice_no'] }}
                                    </a>
                                </td>
                                <td>
                                    <a href="{{ route('admin.orders.show', $inv['order_id']) }}" class="text-muted">
                                        #{{ $inv['order_id'] }}
                                    </a>
                                </td>
                                <td>
                                    <span class="text-dark fw-medium fs-13 d-block">{{ $inv['customer_name'] }}</span>
                                    <small class="text-muted">{{ $inv['customer_email'] }}</small>
                                </td>
                                <td>
                                    <span class="fs-13">{{ $inv['date'] }}</span>
                                </td>
                                <td>
                                    <span class="fw-bold text-dark fs-14">${{ number_format($inv['amount'], 2) }}</span>
                                </td>
                                <td>
                                    <span class="badge bg-light text-muted border fs-12">{{ $inv['payment_method'] }}</span>
                                </td>
                                <td>
                                    <span class="badge bg-success-subtle text-success px-2 py-1 fs-12">{{ $inv['status'] }}</span>
                                </td>
                                <td class="text-end">
                                    <a href="{{ route('admin.invoices.show', $inv['order_id']) }}" class="btn btn-sm btn-soft-primary" title="View Printable Invoice">
                                        <iconify-icon icon="solar:printer-bold" class="fs-16"></iconify-icon>
                                    </a>
                                </td>
                            </tr>
                            @empty
                            <tr>
                                <td colspan="8" class="text-center py-4 text-muted">No invoices found.</td>
                            </tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>
            </div>
            <div class="card-footer py-2 bg-light bg-opacity-50">
                <p class="mb-0 text-muted fs-12">Displaying {{ count($invoices) }} invoices</p>
            </div>
        </div>
    </div>
</div>
@endsection
