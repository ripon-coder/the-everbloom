@extends('layouts.checkout')

@section('title', 'Order Confirmed — ' . config('demo.site.name'))

@section('content')
<div class="site-container max-sm:!px-4 py-12 lg:py-16">
    <div class="max-w-2xl mx-auto bg-white border border-[#E3E6DD] p-6 sm:p-10 space-y-6">
        <div class="text-center space-y-3 border-b border-[#ECEEE6] pb-6">
            <div class="w-12 h-12 rounded-full bg-forest text-ivory flex items-center justify-center mx-auto">
                <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24" aria-hidden="true">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7" />
                </svg>
            </div>
            <span class="text-xs uppercase tracking-[0.2em] font-medium text-forest block">Order Confirmed</span>
            <h1 class="font-serif text-3xl text-ink font-normal">Thank you for your order</h1>
            <p class="text-sm text-muted">
                Order Number: <strong class="text-ink font-mono">{{ $order->order_number }}</strong>
            </p>
        </div>

        <div class="space-y-3">
            <h2 class="text-xs uppercase tracking-[0.2em] font-semibold text-ink">Items Ordered</h2>
            <div class="divide-y divide-[#ECEEE6] border-t border-b border-[#ECEEE6]">
                @foreach($order->items as $item)
                    <div class="py-3 flex items-center justify-between gap-4 text-sm">
                        <div>
                            <p class="font-medium text-ink">{{ $item->name }}</p>
                            @if($item->variant)
                                <p class="text-xs text-muted">{{ $item->variant }} &times; {{ $item->qty }}</p>
                            @else
                                <p class="text-xs text-muted">Qty: {{ $item->qty }}</p>
                            @endif
                        </div>
                        <span class="font-medium text-ink">{{ format_price($item->line_total) }}</span>
                    </div>
                @endforeach
            </div>
        </div>

        <div class="space-y-1.5 text-sm border-b border-[#ECEEE6] pb-5">
            <div class="flex justify-between text-muted">
                <span>Subtotal</span>
                <span>{{ format_price($order->subtotal) }}</span>
            </div>
            @if($order->discount_amount > 0)
                <div class="flex justify-between text-forest">
                    <span>Discount ({{ $order->discount_code }})</span>
                    <span>-{{ format_price($order->discount_amount) }}</span>
                </div>
            @endif
            <div class="flex justify-between text-muted">
                <span>Shipping ({{ $order->delivery_method }})</span>
                <span>{{ $order->shipping_cost > 0 ? format_price($order->shipping_cost) : 'Free' }}</span>
            </div>
            <div class="flex justify-between text-base font-semibold text-ink pt-2">
                <span>Total</span>
                <span>{{ format_price($order->total) }}</span>
            </div>
        </div>

        <div class="text-xs text-muted space-y-1">
            <p class="uppercase tracking-[0.15em] font-semibold text-ink">Delivery Details</p>
            <p>{{ trim(($order->shipping_address['first_name'] ?? '') . ' ' . ($order->shipping_address['last_name'] ?? '')) }}</p>
            <p>{{ $order->shipping_address['address_line1'] ?? '' }}</p>
            <p>{{ trim(($order->shipping_address['area_name'] ?? '') . ', ' . ($order->shipping_address['thana_name'] ?? '') . ', ' . ($order->shipping_address['district_name'] ?? ''), ', ') }}</p>
            <p>{{ $order->customer_phone }} &middot; Payment: {{ $order->payment_status }}</p>
        </div>

        <div class="pt-2 flex flex-wrap gap-3 justify-center">
            <a href="{{ route('shop.index') }}" class="btn-primary">Continue Shopping</a>
            @auth('web')
                <a href="{{ route('account.order.details', $order->order_number) }}" class="btn-secondary">View Order Details</a>
            @endauth
        </div>
    </div>
</div>
@endsection

