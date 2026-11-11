@extends('layouts.account')

@section('title', 'My Account — ' . config('demo.site.name'))

@section('breadcrumbs')
    <x-storefront.breadcrumbs :items="[['label' => 'My Account', 'url' => '']]" />
@endsection

@section('page_title', 'Welcome, ' . ($customer['first_name'] ?? 'Eleanor'))

@section('page_subtitle')
    <p class="text-sm text-muted break-words">
        {{ $customer['email'] ?? 'eleanor.vance@example.com' }}
    </p>
@endsection

@section('account_content')
<div class="space-y-12">
    <!-- Recent Orders Section -->
    <section aria-labelledby="recent-orders-heading">
        <div class="flex items-baseline justify-between pb-4 border-b border-[#ECEEE6]">
            <h2 id="recent-orders-heading" class="font-serif text-xl sm:text-2xl text-ink font-normal">
                Recent Orders
            </h2>
            <a 
                href="{{ route('account.orders') }}" 
                class="min-h-[44px] inline-flex items-center text-xs uppercase tracking-wider text-forest hover:text-forest-700 font-medium underline underline-offset-4 transition-colors"
            >
                View all orders
            </a>
        </div>

        @if(!empty($recentOrders) && count($recentOrders) > 0)
            <!-- Desktop Table (lg and up) -->
            <div class="hidden lg:block overflow-x-auto">
                <table class="w-full text-left border-collapse">
                    <thead>
                        <tr class="border-b border-[#ECEEE6] text-[11px] uppercase tracking-[0.18em] text-muted font-medium">
                            <th scope="col" class="py-3.5 pr-4 font-medium">Order</th>
                            <th scope="col" class="py-3.5 px-4 font-medium">Date</th>
                            <th scope="col" class="py-3.5 px-4 font-medium">Status</th>
                            <th scope="col" class="py-3.5 px-4 font-medium text-right">Total</th>
                            <th scope="col" class="py-3.5 pl-4 font-medium text-right"><span class="sr-only">Actions</span></th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-[#ECEEE6] text-sm">
                        @foreach($recentOrders as $order)
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
                                <td class="py-4 px-4 text-right font-medium text-ink lining-nums whitespace-nowrap">
                                    ${{ number_format($order['total'], 2) }}
                                </td>
                                <td class="py-4 pl-4 text-right whitespace-nowrap">
                                    <a 
                                        href="{{ route('account.order.details', $order['id']) }}" 
                                        class="min-h-[44px] inline-flex items-center text-xs uppercase tracking-wider text-forest hover:text-forest-700 font-medium underline underline-offset-4 transition-colors"
                                    >
                                        View details
                                    </a>
                                </td>
                            </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>

            <!-- Mobile Stacked Rows (below lg) -->
            <div class="lg:hidden divide-y divide-[#ECEEE6]">
                @foreach($recentOrders as $order)
                    <div class="py-4 space-y-3">
                        <div class="flex items-start justify-between gap-4">
                            <div class="space-y-0.5 min-w-0">
                                <a 
                                    href="{{ route('account.order.details', $order['id']) }}" 
                                    class="text-sm font-semibold text-ink hover:text-forest break-words block"
                                >
                                    Order #{{ $order['id'] }}
                                </a>
                                <p class="text-xs text-muted lining-nums">
                                    {{ $order['date'] }}
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
                                class="min-h-[44px] inline-flex items-center text-xs uppercase tracking-wider text-forest hover:text-forest-700 font-medium underline underline-offset-4"
                            >
                                View details →
                            </a>
                        </div>
                    </div>
                @endforeach
            </div>
        @else
            <div class="py-8 text-center text-muted text-sm">
                <p>No recent orders found.</p>
            </div>
        @endif
    </section>

    <!-- Address & Wishlist Columns (Dividers, lists and whitespace, NOT boxed cards) -->
    <section aria-label="Account Shortcuts" class="pt-6 border-t border-[#ECEEE6]">
        <div class="grid grid-cols-1 md:grid-cols-2 gap-8 md:gap-12 md:divide-x md:divide-[#ECEEE6]">
            
            <!-- Default Address Column -->
            <div class="space-y-3">
                <div class="flex items-baseline justify-between">
                    <h2 class="font-serif text-lg sm:text-xl text-ink font-normal">
                        Default Address
                    </h2>
                    <a 
                        href="{{ route('account.addresses') }}" 
                        class="min-h-[44px] inline-flex items-center text-xs uppercase tracking-wider text-forest hover:text-forest-700 font-medium underline underline-offset-4 transition-colors"
                    >
                        Edit
                    </a>
                </div>

                @if($defaultAddress)
                    <div class="text-sm text-ink/90 leading-relaxed break-words space-y-1">
                        <p class="font-medium text-ink">{{ $defaultAddress['first_name'] }} {{ $defaultAddress['last_name'] }}</p>
                        <p>{{ $defaultAddress['address_line1'] }}</p>
                        @if(!empty($defaultAddress['address_line2']))
                            <p>{{ $defaultAddress['address_line2'] }}</p>
                        @endif
                        <p class="lining-nums">{{ $defaultAddress['city'] }}, {{ $defaultAddress['state'] }} {{ $defaultAddress['postal_code'] }}</p>
                        <p>{{ $defaultAddress['country'] }}</p>
                        @if(!empty($defaultAddress['phone']))
                            <p class="text-xs text-muted pt-1 lining-nums">{{ $defaultAddress['phone'] }}</p>
                        @endif
                    </div>
                @else
                    <p class="text-sm text-muted">No default address specified.</p>
                    <a href="{{ route('account.addresses') }}" class="text-xs text-forest underline">Add an address</a>
                @endif
            </div>

            <!-- Wishlist Summary Column -->
            <div class="space-y-3 md:pl-8 lg:pl-12 pt-6 md:pt-0 border-t md:border-t-0 border-[#ECEEE6]">
                <div class="flex items-baseline justify-between">
                    <h2 class="font-serif text-lg sm:text-xl text-ink font-normal">
                        Wishlist
                    </h2>
                    <a 
                        href="{{ route('wishlist') }}" 
                        class="min-h-[44px] inline-flex items-center text-xs uppercase tracking-wider text-forest hover:text-forest-700 font-medium underline underline-offset-4 transition-colors"
                    >
                        View wishlist
                    </a>
                </div>

                <div class="text-sm text-muted space-y-2">
                    <p class="leading-relaxed">
                        Curate your personal ritual and save botanical preparations for future seasons.
                    </p>
                    <p class="text-ink font-medium lining-nums" x-data>
                        <span x-show="$store.wishlist && $store.wishlist.count > 0" x-cloak>
                            <span x-text="$store.wishlist.count"></span> saved formulation(s)
                        </span>
                        <span x-show="!$store.wishlist || $store.wishlist.count === 0">
                            Your saved collection is ready for new botanicals.
                        </span>
                    </p>
                </div>
            </div>
        </div>
    </section>

    <!-- Mobile Sign Out Link (below lg) -->
    <div class="lg:hidden pt-8 border-t border-[#ECEEE6] text-center">
        <a 
            href="{{ route('login') }}" 
            class="min-h-[44px] inline-flex items-center text-xs uppercase tracking-wider text-muted-dark hover:text-forest font-medium underline underline-offset-4 transition-colors"
        >
            Sign out of account
        </a>
    </div>
</div>
@endsection
