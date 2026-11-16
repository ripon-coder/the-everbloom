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
<div 
    x-data="{
        filter: 'All',
        statuses: {{ Js::from(array_values(array_map(fn($o) => $o['status'], $orders ?? []))) }},
        get filteredCount() {
            if (this.filter === 'All') return this.statuses.length;
            return this.statuses.filter(s => s === this.filter).length;
        }
    }"
    class="space-y-6"
>
    @if(!empty($orders) && count($orders) > 0)
        <!-- Heading Row: All orders + count on left, status filter on right -->
        <div class="flex flex-col md:flex-row md:items-baseline md:justify-between gap-4 pb-4 border-b border-[#ECEEE6]">
            <div class="flex items-baseline gap-3">
                <h2 class="font-serif text-2xl text-ink font-normal">
                    All orders
                </h2>
                <span 
                    class="text-xs text-muted lining-nums" 
                    x-text="filteredCount === 1 ? '1 order' : filteredCount + ' orders'"
                >
                    {{ count($orders) }} {{ count($orders) === 1 ? 'order' : 'orders' }}
                </span>
            </div>

            <!-- Desktop Status Filter Tabs -->
            <div class="hidden md:flex items-center gap-5" role="group" aria-label="Filter orders by status">
                @foreach(['All', 'Processing', 'Shipped', 'Delivered'] as $statusOption)
                    <button
                        type="button"
                        @click="filter = '{{ $statusOption }}'"
                        class="min-h-[44px] inline-flex items-center text-xs uppercase tracking-[0.15em] font-medium transition-colors cursor-pointer focus-visible:outline-none focus-visible:ring-2 focus-visible:ring-[#304C3D] focus-visible:ring-offset-2"
                        :class="filter === '{{ $statusOption }}' ? 'text-[#304C3D] underline underline-offset-4 decoration-[#304C3D]' : 'text-muted hover:text-ink no-underline'"
                    >
                        {{ $statusOption }}
                    </button>
                @endforeach
            </div>

            <!-- Mobile Status Filter Native Select -->
            <div class="md:hidden">
                <label for="mobile-order-status-filter" class="sr-only">Filter orders by status</label>
                <div class="relative">
                    <select
                        id="mobile-order-status-filter"
                        x-model="filter"
                        class="input-field !h-11 !min-h-[44px] !py-2 pr-10 appearance-none cursor-pointer bg-white text-sm"
                    >
                        <option value="All">All statuses</option>
                        <option value="Processing">Processing</option>
                        <option value="Shipped">Shipped</option>
                        <option value="Delivered">Delivered</option>
                    </select>
                    <div class="pointer-events-none absolute inset-y-0 right-0 flex items-center pr-3 text-forest">
                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24" aria-hidden="true">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7" />
                        </svg>
                    </div>
                </div>
            </div>
        </div>

        <!-- Desktop Orders Table (md and up) -->
        <div class="hidden md:block overflow-x-auto" x-show="filteredCount > 0">
            <table class="w-full text-left border-collapse">
                <thead>
                    <tr class="border-b border-[#ECEEE6] text-xs uppercase tracking-[0.2em] text-muted font-medium">
                        <th scope="col" class="py-3.5 pr-4 font-medium text-left">Order</th>
                        <th scope="col" class="py-3.5 px-4 font-medium text-left">Date</th>
                        <th scope="col" class="py-3.5 px-4 font-medium text-left">Status</th>
                        <th scope="col" class="py-3.5 px-4 font-medium text-left">Items</th>
                        <th scope="col" class="py-3.5 px-4 font-medium text-right">Total</th>
                        <th scope="col" class="py-3.5 pl-4 font-medium text-right"><span class="sr-only">Actions</span></th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-[#ECEEE6] text-sm">
                    @foreach($orders as $order)
                        <tr 
                            x-show="filter === 'All' || filter === @js($order['status'])"
                            class="hover:bg-beige-light/30 transition-colors"
                        >
                            <td class="py-4 pr-4 font-bold text-ink text-left break-words">
                                <a href="{{ route('account.order.details', $order['id']) }}" class="font-bold text-ink hover:text-forest no-underline hover:underline underline-offset-4 lining-nums">
                                    #{{ $order['id'] }}
                                </a>
                            </td>
                            <td class="py-4 px-4 text-muted text-left lining-nums whitespace-nowrap">
                                {{ $order['date'] }}
                            </td>
                            <td class="py-4 px-4 text-left whitespace-nowrap">
                                <x-storefront.status-badge :status="$order['status']" />
                            </td>
                            <td class="py-4 px-4 text-muted text-left lining-nums whitespace-nowrap">
                                {{ $order['items_count'] ?? count($order['items'] ?? []) }} {{ ($order['items_count'] ?? count($order['items'] ?? [])) === 1 ? 'item' : 'items' }}
                            </td>
                            <td class="py-4 px-4 text-right price text-ink whitespace-nowrap">
                                {{ $currencySymbol }}{{ number_format($order['total'], 2) }}
                            </td>
                            <td class="py-4 pl-4 text-right whitespace-nowrap">
                                <a 
                                    href="{{ route('account.order.details', $order['id']) }}" 
                                    class="min-h-[44px] inline-flex items-center text-[13px] text-forest hover:text-ink font-medium underline underline-offset-4 transition-colors"
                                >
                                    View details
                                </a>
                            </td>
                        </tr>
                    @endforeach
                </tbody>
            </table>
        </div>

        <!-- Mobile Stacked Order Rows (below md, no tables, full-width button) -->
        <div class="md:hidden divide-y divide-[#ECEEE6]" x-show="filteredCount > 0">
            @foreach($orders as $order)
                <div 
                    x-show="filter === 'All' || filter === @js($order['status'])"
                    class="py-5 space-y-3"
                >
                    <div class="flex items-start justify-between gap-4">
                        <div class="space-y-1 min-w-0">
                            <a 
                                href="{{ route('account.order.details', $order['id']) }}" 
                                class="text-base font-bold text-ink hover:text-forest no-underline hover:underline underline-offset-4 break-words block lining-nums"
                            >
                                Order #{{ $order['id'] }}
                            </a>
                            <p class="text-xs text-muted lining-nums">
                                {{ $order['date'] }} · {{ $order['items_count'] ?? count($order['items'] ?? []) }} {{ ($order['items_count'] ?? count($order['items'] ?? [])) === 1 ? 'item' : 'items' }}
                            </p>
                        </div>
                        <div class="text-right shrink-0 space-y-1.5">
                            <x-storefront.status-badge :status="$order['status']" />
                            <p class="price text-sm text-ink block">
                                {{ $currencySymbol }}{{ number_format($order['total'], 2) }}
                            </p>
                        </div>
                    </div>
                    
                    <div>
                        <a 
                            href="{{ route('account.order.details', $order['id']) }}" 
                            class="btn-secondary w-full text-center"
                        >
                            View Order Details
                        </a>
                    </div>
                </div>
            @endforeach
        </div>

        <!-- Empty Filter Result -->
        <div x-show="filteredCount === 0" x-cloak class="py-12 text-center border-b border-[#ECEEE6] text-sm text-muted">
            No orders match this filter.
        </div>
    @else
        <!-- Empty State -->
        <div class="py-16 text-center border border-[#E3E6DD] bg-white p-8 space-y-4 max-w-lg mx-auto">
            <div class="w-14 h-14 mx-auto rounded-full bg-sage-light/50 flex items-center justify-center text-forest/70">
                <svg class="w-7 h-7" fill="none" stroke="currentColor" viewBox="0 0 24 24" aria-hidden="true">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M16 11V7a4 4 0 00-8 0v4M5 9h14l1 12H4L5 9z" />
                </svg>
            </div>
            <h2 class="font-serif text-2xl text-ink font-normal">
                No orders yet
            </h2>
            <p class="text-sm text-muted leading-relaxed">
                You have not placed any botanical orders yet. When you complete an order, its details and shipment status will appear here.
            </p>
            <div class="pt-2">
                <a href="{{ route('shop.index') }}" class="btn-primary w-full sm:w-auto">
                    Start shopping
                </a>
            </div>
        </div>
    @endif
</div>
@endsection
