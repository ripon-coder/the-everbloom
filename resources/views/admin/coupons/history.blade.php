@extends('layouts.admin')

@section('title', 'Coupon Usage History: ' . $coupon->code)
@section('container_class', 'container-xxl')

@section('content')
<div class="row">
    <div class="col-12">
        <div class="card">
            <div class="card-header d-flex justify-content-between align-items-center">
                <div>
                    <h4 class="card-title mb-0">Usage History: <span class="font-monospace text-primary">{{ $coupon->code }}</span></h4>
                    <small class="text-muted">{{ $coupon->label }} &middot; Total Redemptions: {{ $coupon->used_count }}</small>
                </div>
                <a href="{{ route('admin.coupons.index') }}" class="btn btn-sm btn-outline-secondary">Back to Coupons</a>
            </div>
            <div class="card-body p-0">
                <div class="table-responsive">
                    <table class="table align-middle mb-0 table-hover table-centered">
                        <thead class="bg-light-subtle">
                            <tr>
                                <th>Order #</th>
                                <th>Customer</th>
                                <th>Email</th>
                                <th>Discount Applied</th>
                                <th>Redeemed At</th>
                            </tr>
                        </thead>
                        <tbody>
                            @forelse($redemptions as $redemption)
                            <tr>
                                <td>
                                    @if($redemption->order)
                                        <a href="{{ route('admin.orders.show', $redemption->order->order_number) }}" class="fw-bold text-primary">
                                            #{{ $redemption->order->order_number }}
                                        </a>
                                    @else
                                        <span class="text-muted">#{{ $redemption->order_id }}</span>
                                    @endif
                                </td>
                                <td>{{ $redemption->user?->name ?? 'Guest' }}</td>
                                <td>{{ $redemption->email ?? ($redemption->user?->email ?? '—') }}</td>
                                <td class="fw-semibold text-success">{{ $currencySymbol }}{{ number_format((float) $redemption->discount_amount, 2) }}</td>
                                <td class="text-muted fs-13">{{ $redemption->created_at?->format('M d, Y H:i') }}</td>
                            </tr>
                            @empty
                            <tr>
                                <td colspan="5" class="text-center py-4 text-muted">No redemptions recorded for this coupon yet.</td>
                            </tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>
            </div>
            <div class="card-footer py-2 bg-light bg-opacity-50">
                {{ $redemptions->links('pagination::bootstrap-5') }}
            </div>
        </div>
    </div>
</div>
@endsection

