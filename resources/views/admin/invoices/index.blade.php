@extends('layouts.admin')

@section('title', 'Invoices List')
@section('container_class', 'container-xxl')

@section('content')
<div class="row">
    <div class="col-12">
        <div class="card">
            @php
                $invoiceFilterCount = collect([
                    request('search'),
                    request('status'),
                    request('date_from'),
                    request('date_to'),
                ])->filter()->count();
            @endphp
            <div class="card-header d-flex flex-wrap justify-content-between align-items-center gap-2">
                <div class="d-flex flex-wrap align-items-center gap-2">
                    <h4 class="card-title mb-0">Customer Invoices &amp; Billing Records ({{ count($invoices) }})</h4>
                    @if($invoiceFilterCount > 0)
                        <a href="{{ route('admin.invoices.index') }}" class="badge bg-soft-danger text-danger text-decoration-none fs-11">
                            <i class="bx bx-x align-middle"></i> Clear Filters
                        </a>
                    @endif
                </div>
                <button type="button" class="btn btn-sm btn-soft-secondary d-flex align-items-center gap-1 position-relative" data-bs-toggle="offcanvas" data-bs-target="#invoiceFilterDrawer" aria-controls="invoiceFilterDrawer">
                    <iconify-icon icon="solar:filter-bold-duotone" class="fs-16"></iconify-icon>
                    <span>Filter</span>
                    @if($invoiceFilterCount > 0)
                        <span class="badge bg-primary rounded-pill ms-1">{{ $invoiceFilterCount }}</span>
                    @endif
                </button>
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
                                    <span class="fw-bold text-dark fs-14">{{ $currencySymbol }}{{ number_format($inv['amount'], 2) }}</span>
                                </td>
                                <td>
                                    <span class="badge bg-light text-muted border fs-12">{{ $inv['payment_method'] }}</span>
                                </td>
                                <td>
                                    <span class="badge bg-{{ $inv['status_badge'] }}-subtle text-{{ $inv['status_badge'] }} px-2 py-1 fs-12">{{ $inv['status'] }}</span>
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
<x-admin.filter-drawer
    id="invoiceFilterDrawer"
    title="Filter Invoices"
    :action="route('admin.invoices.index')"
    :reset-url="route('admin.invoices.index')">
    <div class="mb-3">
        <label for="inv-doc-search" class="form-label fw-semibold">Search</label>
        <input type="text" id="inv-doc-search" name="search" class="form-control" placeholder="Invoice #, order #, client..." value="{{ request('search') }}">
    </div>

    <div class="mb-3">
        <label for="inv-doc-status" class="form-label fw-semibold">Billing Status</label>
        <select id="inv-doc-status" name="status" class="form-select">
            <option value="">All Statuses</option>
            <option value="Paid" {{ request('status') === 'Paid' ? 'selected' : '' }}>Paid</option>
            <option value="Pending" {{ request('status') === 'Pending' ? 'selected' : '' }}>Pending</option>
            <option value="Unpaid" {{ request('status') === 'Unpaid' ? 'selected' : '' }}>Unpaid</option>
        </select>
    </div>

    <div class="row g-2 mb-3">
        <div class="col-6">
            <label for="inv-doc-from" class="form-label fw-semibold">From Date</label>
            <input type="date" id="inv-doc-from" name="date_from" value="{{ request('date_from') }}" class="form-control">
        </div>
        <div class="col-6">
            <label for="inv-doc-to" class="form-label fw-semibold">To Date</label>
            <input type="date" id="inv-doc-to" name="date_to" value="{{ request('date_to') }}" class="form-control">
        </div>
    </div>
</x-admin.filter-drawer>
@endsection
