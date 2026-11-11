@extends('layouts.admin')

@section('title', 'Coupons List')
@section('container_class', 'container-xxl')

@section('content')
<div class="row">
    <div class="col-12">
        <div class="card">
            <div class="card-header d-flex justify-content-between align-items-center">
                <h4 class="card-title mb-0">Active Promotional Codes & Coupons ({{ count($coupons) }})</h4>
                <a href="{{ route('admin.coupons.create') }}" class="btn btn-sm btn-primary d-flex align-items-center gap-1">
                    <iconify-icon icon="solar:add-circle-bold" class="fs-16"></iconify-icon>
                    <span>Create Coupon</span>
                </a>
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
                                    <div class="d-flex align-items-center gap-2">
                                        <div class="progress flex-grow-1" style="height: 6px; width: 80px;">
                                            <div class="progress-bar bg-primary" style="width: {{ ($c['used_count'] / $c['usage_limit']) * 100 }}%"></div>
                                        </div>
                                        <span class="text-muted fs-12">{{ $c['used_count'] }}/{{ $c['usage_limit'] }}</span>
                                    </div>
                                </td>
                                <td>
                                    <span class="fs-13 text-muted">{{ $c['expires_at'] }}</span>
                                </td>
                                <td>
                                    <span class="badge bg-success-subtle text-success px-2 py-1 fs-12">{{ $c['status'] }}</span>
                                </td>
                                <td class="text-end">
                                    <button type="button" class="btn btn-sm btn-soft-danger" title="Deactivate Coupon">
                                        <iconify-icon icon="solar:trash-bin-minimalistic-2-broken" class="fs-16"></iconify-icon>
                                    </button>
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
                <p class="mb-0 text-muted fs-12">Discounts configured in <code>config/demo/site.php</code></p>
            </div>
        </div>
    </div>
</div>
@endsection
