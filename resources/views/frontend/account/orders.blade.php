@extends('layouts.account')

@section('title', 'Order History — ' . config('demo.site.name'))

@section('breadcrumbs')
    <x-storefront.breadcrumbs :items="[
        ['label' => 'My Account', 'url' => route('account')],
        ['label' => 'Orders', 'url' => ''],
    ]" />
@endsection

@section('page_title', 'Orders')

@section('account_content')
<div class="space-y-6">
    @if(!empty($orders) && count($orders) > 0)
        <!-- Desktop Orders Table (lg and up) -->
        <div class="hidden lg:block overflow-x-auto">
            <table class="w-full text-left border-collapse">
                <thead>
                    <tr class="border-b border-[#ECEEE6] text-[11px] uppercase tracking-[0.18em] text-muted font-medium">
                        <th scope="col" class="py-3.5 pr-4 font-medium">Order</th>
                        <th scope="col" class="py-3.5 px-4 font-medium">Date</th>
                        <th scope="col" class="py-3.5 px-4 font-medium">Status</th>
                        <th scope="col" class="py-3.5 px-4 font-medium">Items</th>
                        <th scope="col" class="py-3.5 px-4 font-medium text-right">Total</th>
                        <th scope="col" class="py-3.5 pl-4 font-medium text-right"><span class="sr-only">Actions</span></th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-[#ECEEE6] text-sm">
                    @foreach($orders as $order)
                        <tr class="hover:bg-beige-light/30 transition-colors">
                            <td class="py-4 pr-4 font-medium text-ink break-words">
                                <a href="{{ route('account.order.details', $order['id']) }}" class="hover:text-forest underline underline-offset-4">
                                    #{{ $order['id'] }}
                                </a>
                            </td>
                            <td class="py-4 px-4 text-muted lining-nums whitespace-nowrap">
                                {{ $order['date'] }}
                            </td>
                            <td class="py-4 px-4 whitespace-nowrap">
                                <x-storefront.status-badge :status="$order['status']" />
                            </td>
                            <td class="py-4 px-4 text-muted lining-nums whitespace-nowrap">
                                {{ $order['items_count'] ?? count($order['items'] ?? []) }} {{ ($order['items_count'] ?? count($order['items'] ?? [])) === 1 ? 'item' : 'items' }}
                            </td>
                            <td class="py-4 px-4 text-right font-medium text-ink lining-nums whitespace-nowrap">
                                ${{ number_format($order['total'], 2) }}
                            </td>
                            <td class="py-4 pl-4 text-right whitespace-nowrap">
                                <a 
                                    href="{{ route('account.order.details', $order['id']) }}" 
                                    class="min-h-[44px] inline-flex items-center text-xs uppercase tracking-wider text-forest hover:text-forest-700 font-medium underline underline-offset-4 transition-colors"
                                >
                                    View
                                </a>
                            </td>
                        </tr>
                    @endforeach
                </tbody>
            </table>
        </div>

        <!-- Mobile Stacked Order Rows (below lg) -->
        <div class="lg:hidden divide-y divide-[#ECEEE6]">
            @foreach($orders as $order)
                <div class="py-5 space-y-3">
                    <div class="flex items-start justify-between gap-4">
                        <div class="space-y-0.5 min-w-0">
                            <a 
                                href="{{ route('account.order.details', $order['id']) }}" 
                                class="text-base font-semibold text-ink hover:text-forest break-words block"
                            >
                                Order #{{ $order['id'] }}
                            </a>
                            <p class="text-xs text-muted lining-nums">
                                {{ $order['date'] }} · {{ $order['items_count'] ?? count($order['items'] ?? []) }} {{ ($order['items_count'] ?? count($order['items'] ?? [])) === 1 ? 'item' : 'items' }}
                            </p>
                        </div>
                        <div class="text-right shrink-0 space-y-1">
                            <x-storefront.status-badge :status="$order['status']" />
                            <p class="text-sm font-semibold text-ink lining-nums">
                                ${{ number_format($order['total'], 2) }}
                            </p>
                        </div>
                    </div>
                    
                    <div>
                        <a 
                            href="{{ route('account.order.details', $order['id']) }}" 
                            class="min-h-[44px] w-full flex items-center justify-center border border-forest text-forest hover:bg-forest hover:text-ivory text-xs uppercase tracking-wider font-medium transition-colors"
                        >
                            View details
                        </a>
                    </div>
                </div>
            @endforeach
        </div>
    @else
        <!-- Empty State -->
        <div class="py-16 text-center space-y-4 max-w-md mx-auto">
            <div class="w-12 h-12 mx-auto rounded-full bg-forest/5 flex items-center justify-center text-forest/70">
                <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24" aria-hidden="true">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M16 11V7a4 4 0 00-8 0v4M5 9h14l1 12H4L5 9z" />
                </svg>
            </div>
            <h2 class="font-serif text-2xl text-ink font-normal">
                No Orders Yet
            </h2>
            <p class="text-sm text-muted leading-relaxed">
                You have not placed any botanical orders yet. When you complete an order, its details and shipment tracking will appear here.
            </p>
            <div class="pt-2">
                <a href="{{ route('shop.index') }}" class="btn-primary min-h-[44px]">
                    Start shopping
                </a>
            </div>
        </div>
    @endif
</div>
@endsection
