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
<div>
    <!-- Recent Orders Section -->
    <section aria-labelledby="recent-orders-heading">
        <div class="flex items-baseline justify-between pb-4 border-b border-[#ECEEE6]">
            <h2 id="recent-orders-heading" class="font-serif text-xl sm:text-2xl text-ink font-normal">
                Recent Orders
            </h2>
            <a 
                href="{{ route('account.orders') }}" 
                class="min-h-[44px] inline-flex items-center text-sm text-[#304C3D] font-medium underline underline-offset-4 decoration-[#A8B7A0] hover:decoration-[#304C3D] focus-visible:outline-none focus-visible:ring-2 focus-visible:ring-[#304C3D] focus-visible:ring-offset-2 transition-colors"
            >
                View all orders
            </a>
        </div>

        @if(!empty($recentOrders) && count($recentOrders) > 0)
            <!-- Desktop Table (lg and up) -->
            <div class="hidden lg:block overflow-x-auto">
                <table class="w-full text-left border-collapse">
                    <thead>
                        <tr class="border-b border-[#ECEEE6] text-xs uppercase tracking-[0.2em] text-muted font-medium">
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
                                <td class="py-4 pr-4 font-bold text-ink break-words">
                                    <a href="{{ route('account.order.details', $order['id']) }}" class="font-bold text-ink hover:text-forest no-underline hover:underline underline-offset-4 lining-nums">
                                        #{{ $order['id'] }}
                                    </a>
                                </td>
                                <td class="py-4 px-4 text-muted lining-nums whitespace-nowrap">
                                    {{ $order['date'] }}
                                </td>
                                <td class="py-4 px-4 whitespace-nowrap">
                                    <x-storefront.status-badge :status="$order['status']" />
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

            <!-- Mobile Stacked Rows (below lg, no tables, full-width button) -->
            <div class="lg:hidden divide-y divide-[#ECEEE6]">
                @foreach($recentOrders as $order)
                    <div class="py-5 space-y-3">
                        <div class="flex items-start justify-between gap-4">
                            <div class="space-y-1 min-w-0">
                                <a 
                                    href="{{ route('account.order.details', $order['id']) }}" 
                                    class="text-base font-bold text-ink hover:text-forest no-underline hover:underline underline-offset-4 break-words block lining-nums"
                                >
                                    Order #{{ $order['id'] }}
                                </a>
                                <p class="text-xs text-muted lining-nums">
                                    {{ $order['date'] }}
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
        @else
            <div class="py-8 text-center text-muted text-sm">
                <p>No recent orders found.</p>
            </div>
        @endif
    </section>

    <!-- Address & Wishlist Columns (Dividers, lists and whitespace, NOT three identical boxed cards) -->
    <section aria-label="Account Shortcuts" class="py-12 border-t border-[#ECEEE6]">
        <div class="grid grid-cols-1 md:grid-cols-2 gap-8 md:gap-12 md:divide-x md:divide-[#ECEEE6]">
            
            <!-- Default Address Column -->
            <div class="space-y-3">
                <div class="flex items-baseline justify-between gap-4">
                    <h2 class="font-serif text-lg sm:text-xl text-ink font-normal">
                        Default Address
                    </h2>
                    <a 
                        href="{{ route('account.addresses') }}" 
                        class="min-h-[44px] inline-flex items-center shrink-0 text-sm text-[#304C3D] font-medium underline underline-offset-4 decoration-[#A8B7A0] hover:decoration-[#304C3D] focus-visible:outline-none focus-visible:ring-2 focus-visible:ring-[#304C3D] focus-visible:ring-offset-2 transition-colors"
                    >
                        Manage addresses
                    </a>
                </div>

                @if($defaultAddress)
                    <div class="text-sm text-ink leading-[1.5] break-words">
                        <p class="font-medium text-ink">{{ $defaultAddress['first_name'] }} {{ $defaultAddress['last_name'] }}</p>
                        <p>{{ $defaultAddress['address_line1'] }}</p>
                        @if(!empty($defaultAddress['address_line2']))
                            <p>{{ $defaultAddress['address_line2'] }}</p>
                        @endif
                        <p class="lining-nums">{{ $defaultAddress['city'] ?? '' }}{{ !empty($defaultAddress['state']) ? ', ' . $defaultAddress['state'] : '' }}</p>
                        <p>{{ $defaultAddress['country'] }}</p>
                        @if(!empty($defaultAddress['phone']))
                            <p class="text-xs text-muted mt-2 lining-nums">{{ $defaultAddress['phone'] }}</p>
                        @endif
                    </div>
                @else
                    <p class="text-sm text-muted">No default address specified.</p>
                    <a href="{{ route('account.addresses') }}" class="inline-flex items-center text-sm text-[#304C3D] font-medium underline underline-offset-4 decoration-[#A8B7A0] hover:decoration-[#304C3D] focus-visible:outline-none focus-visible:ring-2 focus-visible:ring-[#304C3D] focus-visible:ring-offset-2 transition-colors">Add an address</a>
                @endif
            </div>

            <!-- Wishlist Summary Column -->
            <div class="space-y-3 md:pl-8 lg:pl-12 pt-6 md:pt-0 border-t md:border-t-0 border-[#ECEEE6]" x-data>
                <div class="flex items-baseline justify-between gap-4">
                    <h2 class="font-serif text-lg sm:text-xl text-ink font-normal">
                        Saved Wishlist
                    </h2>
                    <a 
                        href="{{ route('wishlist') }}" 
                        class="min-h-[44px] inline-flex items-center shrink-0 text-sm text-[#304C3D] font-medium underline underline-offset-4 decoration-[#A8B7A0] hover:decoration-[#304C3D] focus-visible:outline-none focus-visible:ring-2 focus-visible:ring-[#304C3D] focus-visible:ring-offset-2 transition-colors"
                    >
                        View wishlist
                    </a>
                </div>

                <div x-show="$store.wishlist && $store.wishlist.count > 0" x-cloak class="space-y-3">
                    <p class="text-sm text-ink font-medium lining-nums">
                        <span class="price" x-text="$store.wishlist.count"></span>
                        <span x-text="$store.wishlist.count === 1 ? 'saved item' : 'saved items'"></span>
                    </p>
                    <div class="flex items-center gap-3 flex-wrap">
                        <template x-for="item in $store.wishlist.items.slice(0, 3)" :key="item.id || item">
                            <a 
                                :href="'/shop/product/' + item.slug" 
                                class="block w-14 h-14 shrink-0 border border-[#E3E6DD] bg-sage-light/40 overflow-hidden"
                                :aria-label="item.name || 'Saved item'"
                            >
                                <x-storefront.image 
                                    ::src="item.image ? '/images/' + item.image : ''" 
                                    ::alt="item.name || 'Botanical formulation'" 
                                    aspect-ratio="aspect-square" 
                                    width="56" 
                                    height="56" 
                                    class="w-14 h-14 object-cover" 
                                />
                            </a>
                        </template>
                    </div>
                </div>

                <div x-show="!$store.wishlist || $store.wishlist.count === 0" class="space-y-2">
                    <p class="text-sm text-muted">Nothing saved yet</p>
                    <a 
                        href="{{ route('shop.index') }}" 
                        class="min-h-[44px] inline-flex items-center text-sm text-[#304C3D] font-medium underline underline-offset-4 decoration-[#A8B7A0] hover:decoration-[#304C3D] focus-visible:outline-none focus-visible:ring-2 focus-visible:ring-[#304C3D] focus-visible:ring-offset-2 transition-colors"
                    >
                        Browse the shop
                    </a>
                </div>
            </div>
        </div>
    </section>

    <!-- Mobile Sign Out Button (below lg, full-width) -->
    <div class="lg:hidden pt-6 border-t border-[#ECEEE6]">
        <a 
            href="{{ route('login') }}" 
            class="btn-secondary w-full text-center"
        >
            Sign Out of Account
        </a>
    </div>
</div>
@endsection
