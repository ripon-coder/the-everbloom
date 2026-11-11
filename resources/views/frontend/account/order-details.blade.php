@extends('layouts.account')

@section('title', 'Order #' . $order['id'] . ' — ' . config('demo.site.name'))

@section('demo_notice', 'Demo order — sample data for preview only.')

@section('breadcrumbs')
    <x-storefront.breadcrumbs :items="[
        ['label' => 'My Account', 'url' => route('account')],
        ['label' => 'Orders', 'url' => route('account.orders')],
        ['label' => 'Order #' . $order['id'], 'url' => ''],
    ]" />
@endsection

@section('page_title', 'Order #' . $order['id'])

@section('page_subtitle')
    <div class="flex flex-wrap items-center gap-3 text-xs text-muted mt-1">
        <span class="lining-nums">Placed on {{ $order['date'] }}</span>
        <span aria-hidden="true">·</span>
        <x-storefront.status-badge :status="$order['status']" />
        <span class="inline-flex items-center text-xs uppercase tracking-[0.2em] text-muted border border-[#E3E6DD] bg-white px-2.5 py-0.5">
            Sample Order
        </span>
    </div>
@endsection

@section('header_actions')
    <div class="hidden lg:flex items-center gap-3">
        <a 
            href="{{ route('account.orders') }}" 
            class="min-h-[44px] inline-flex items-center text-[13px] text-muted hover:text-forest underline underline-offset-4 transition-colors px-2 py-2"
        >
            ← Back to orders
        </a>
    </div>
@endsection

@section('account_content')
@php
    $steps = ['Placed', 'Processing', 'Shipped', 'Delivered'];
    $currentStep = $order['current_step'] ?? $order['status'];
    $currentStepIndex = array_search($currentStep, $steps);
    if ($currentStepIndex === false) {
        $currentStepIndex = 1;
    }

    $itemsPayload = array_map(function($item) {
        return [
            'productId' => (int) $item['product_id'],
            'variantId' => (string) $item['variant_id'],
            'qty' => (int) $item['qty'],
        ];
    }, $order['items']);
@endphp

<div 
    x-data="{
        addedNotice: false,
        buyAgain() {
            const items = {{ Js::from($itemsPayload) }};
            items.forEach(item => {
                if (window.Alpine && Alpine.store('cart')) {
                    Alpine.store('cart').add(item.productId, item.qty, item.variantId, false);
                }
            });
            this.addedNotice = true;
            setTimeout(() => { this.addedNotice = false; }, 3500);
            if (window.Alpine && Alpine.store('cart') && typeof Alpine.store('cart').openDrawer === 'function') {
                Alpine.store('cart').openDrawer();
            }
        }
    }" 
    class="space-y-10"
>
    <!-- Order Status Timeline (1px soft borders, no ring-4 or border-2) -->
    <section aria-label="Order Status Timeline" class="pb-8 border-b border-[#ECEEE6]">
        <h2 class="sr-only">Order Fulfillment Progress</h2>

        <!-- DESKTOP TIMELINE (Horizontal) -->
        <div class="hidden lg:block">
            <div class="relative flex items-center justify-between">
                <!-- Connecting Line Track (1px) -->
                <div class="absolute left-6 right-6 top-4 h-px bg-[#ECEEE6] -z-0" aria-hidden="true"></div>
                <div 
                    class="absolute left-6 top-4 h-px bg-forest transition-all duration-300 -z-0" 
                    style="width: {{ ($currentStepIndex / (count($steps) - 1)) * 100 }}%;" 
                    aria-hidden="true"
                ></div>

                @foreach($steps as $index => $step)
                    @php
                        $isCompleted = $index < $currentStepIndex;
                        $isCurrent = $index === $currentStepIndex;
                    @endphp
                    <div class="relative z-10 flex flex-col items-center text-center">
                        <div class="w-8 h-8 rounded-full flex items-center justify-center text-xs font-medium border transition-colors {{ $isCurrent ? 'bg-forest border-forest text-ivory' : ($isCompleted ? 'bg-forest border-forest text-ivory' : 'bg-ivory border-[#D5DBCF] text-muted') }}">
                            @if($isCompleted)
                                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24" aria-hidden="true">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7" />
                                </svg>
                            @else
                                <span class="price">{{ $index + 1 }}</span>
                            @endif
                        </div>
                        <span class="mt-2 text-xs uppercase tracking-[0.2em] font-medium {{ $isCurrent ? 'text-forest font-semibold' : ($isCompleted ? 'text-ink' : 'text-muted') }}">
                            {{ $step }}
                        </span>
                        @if($isCurrent)
                            <span class="text-xs text-forest mt-0.5">Current Status</span>
                        @endif
                    </div>
                @endforeach
            </div>
        </div>

        <!-- MOBILE TIMELINE (Vertical, 1px line) -->
        <div class="lg:hidden pl-2">
            <ol class="relative border-l border-[#ECEEE6] space-y-6 ml-3">
                @foreach($steps as $index => $step)
                    @php
                        $isCompleted = $index < $currentStepIndex;
                        $isCurrent = $index === $currentStepIndex;
                    @endphp
                    <li class="pl-6 relative">
                        <span class="absolute -left-4 top-0.5 w-8 h-8 rounded-full flex items-center justify-center text-xs font-medium border {{ $isCurrent ? 'bg-forest border-forest text-ivory' : ($isCompleted ? 'bg-forest border-forest text-ivory' : 'bg-ivory border-[#D5DBCF] text-muted') }}">
                            @if($isCompleted)
                                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24" aria-hidden="true">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7" />
                                </svg>
                            @else
                                <span class="price">{{ $index + 1 }}</span>
                            @endif
                        </span>
                        <div class="min-h-[32px]">
                            <h3 class="text-xs uppercase tracking-[0.2em] font-medium {{ $isCurrent ? 'text-forest font-semibold' : ($isCompleted ? 'text-ink' : 'text-muted') }}">
                                {{ $step }}
                            </h3>
                            @if($isCurrent)
                                <p class="text-xs text-forest mt-0.5">Current active status</p>
                            @elseif($isCompleted)
                                <p class="text-xs text-muted mt-0.5">Completed</p>
                            @else
                                <p class="text-xs text-muted mt-0.5">Pending</p>
                            @endif
                        </div>
                    </li>
                @endforeach
            </ol>
        </div>
    </section>

    <!-- Buy Again Alert Toast -->
    <div 
        x-show="addedNotice" 
        x-cloak 
        x-transition:enter="transition ease-out duration-200"
        x-transition:enter-start="opacity-0 -translate-y-1"
        x-transition:enter-end="opacity-100 translate-y-0"
        x-transition:leave="transition ease-in duration-150"
        x-transition:leave-start="opacity-100 translate-y-0"
        x-transition:leave-end="opacity-0 -translate-y-1"
        class="p-4 bg-forest text-ivory text-xs uppercase tracking-[0.2em] flex items-center justify-between"
        role="status"
        aria-live="polite"
    >
        <span>Items added to your shopping bag.</span>
        <button type="button" @click="addedNotice = false" class="w-11 h-11 -mr-2 inline-flex items-center justify-center hover:text-sage-light text-ivory cursor-pointer" aria-label="Close notification">✕</button>
    </div>

    <!-- Main Content Grid (Items left 7/12, Summary right 5/12) -->
    <div class="grid grid-cols-1 lg:grid-cols-12 gap-10 lg:gap-12 items-start">
        
        <!-- Left Column: Items (7/12) -->
        <div class="lg:col-span-7 space-y-6">
            <div class="pb-3 border-b border-[#ECEEE6] flex items-baseline justify-between">
                <h2 class="font-serif text-xl text-ink font-normal">
                    Ordered Formulations
                </h2>
                <span class="text-xs text-muted">
                    {{ count($order['items']) }} {{ count($order['items']) === 1 ? 'item' : 'items' }}
                </span>
            </div>

            <!-- Items List -->
            <ul class="divide-y divide-[#ECEEE6]" role="list">
                @foreach($order['items'] as $item)
                    @php
                        $imgPath = !empty($item['image'])
                            ? (str_starts_with($item['image'], '/images/') ? $item['image'] : '/images/' . ltrim($item['image'], '/'))
                            : '';
                    @endphp
                    <li class="py-5 flex items-start gap-4 sm:gap-6">
                        <!-- Desktop 80px thumbnail, Mobile 64px thumbnail -->
                        <a href="{{ route('shop.product', $item['slug'] ?? 'botanical-radiance-serum') }}" class="w-16 h-16 sm:w-20 sm:h-20 shrink-0 bg-sage-light/40 border border-[#E3E6DD] overflow-hidden block">
                            <x-storefront.image 
                                :src="$imgPath" 
                                :alt="$item['name']" 
                                aspect-ratio="aspect-square" 
                                class="w-full h-full object-cover"
                            />
                        </a>

                        <div class="flex-1 min-w-0 space-y-1">
                            <h3 class="font-serif text-base text-ink font-medium leading-snug break-words">
                                <a href="{{ route('shop.product', $item['slug'] ?? 'botanical-radiance-serum') }}" class="hover:text-forest transition-colors">
                                    {{ $item['name'] }}
                                </a>
                            </h3>
                            <p class="text-xs text-muted">
                                Size: {{ $item['variant'] }}
                            </p>
                            <p class="text-xs text-muted lining-nums">
                                Quantity: <span class="price">{{ $item['qty'] }}</span>
                            </p>
                        </div>

                        <div class="text-right shrink-0">
                            <p class="price text-sm font-medium text-ink">
                                ${{ number_format($item['price'] * $item['qty'], 2) }}
                            </p>
                            @if($item['qty'] > 1)
                                <p class="price text-xs text-muted mt-0.5">
                                    ${{ number_format($item['price'], 2) }} each
                                </p>
                            @endif
                        </div>
                    </li>
                @endforeach
            </ul>

            <!-- Buy Again & Back to Orders (Desktop) -->
            <div class="hidden lg:flex items-center gap-4 pt-6 border-t border-[#ECEEE6]">
                <button 
                    type="button" 
                    @click="buyAgain()"
                    class="btn-primary min-h-[44px] cursor-pointer"
                >
                    Buy Again
                </button>
                <a 
                    href="{{ route('account.orders') }}" 
                    class="min-h-[44px] inline-flex items-center text-[13px] text-muted hover:text-forest underline underline-offset-4"
                >
                    Back to orders
                </a>
            </div>
        </div>

        <!-- Right Column: Summary, Addresses, Totals (5/12) -->
        <div class="lg:col-span-5 space-y-8 pt-8 lg:pt-0 border-t lg:border-t-0 border-[#ECEEE6]">
            
            <!-- Totals Section -->
            <div class="bg-white border border-[#E3E6DD] p-6 space-y-4">
                <h2 class="font-serif text-lg text-ink font-normal pb-3 border-b border-[#ECEEE6]">
                    Order Summary
                </h2>

                <dl class="space-y-2.5 text-sm">
                    <div class="flex justify-between text-muted">
                        <dt>Subtotal</dt>
                        <dd class="price text-ink font-medium">${{ number_format($order['subtotal'], 2) }}</dd>
                    </div>

                    <div class="flex justify-between text-muted">
                        <dt>Shipping</dt>
                        <dd class="text-forest font-medium">
                            @if(($order['shipping'] ?? 0) == 0)
                                Complimentary
                            @else
                                <span class="price text-ink">${{ number_format($order['shipping'], 2) }}</span>
                            @endif
                        </dd>
                    </div>

                    @if(!empty($order['tax']) && $order['tax'] > 0)
                        <div class="flex justify-between text-muted">
                            <dt>Estimated Tax</dt>
                            <dd class="price text-ink font-medium">${{ number_format($order['tax'], 2) }}</dd>
                        </div>
                    @endif

                    <div class="flex justify-between items-baseline pt-3 border-t border-[#ECEEE6] text-ink text-base font-medium">
                        <dt class="font-serif text-lg">Total</dt>
                        <dd class="price text-lg">${{ number_format($order['total'], 2) }}</dd>
                    </div>
                </dl>
            </div>

            <!-- Shipping & Delivery Section -->
            <div class="space-y-3 pt-2">
                <h3 class="text-xs uppercase tracking-[0.2em] text-muted font-medium">
                    Shipping Address
                </h3>
                <div class="text-sm text-ink leading-relaxed break-words">
                    <p class="font-medium text-ink">{{ $order['shipping_address']['first_name'] }} {{ $order['shipping_address']['last_name'] }}</p>
                    <p>{{ $order['shipping_address']['address_line1'] }}</p>
                    @if(!empty($order['shipping_address']['address_line2']))
                        <p>{{ $order['shipping_address']['address_line2'] }}</p>
                    @endif
                    <p class="lining-nums">{{ $order['shipping_address']['city'] }}, {{ $order['shipping_address']['state'] }} {{ $order['shipping_address']['postal_code'] }}</p>
                    <p>{{ $order['shipping_address']['country'] }}</p>
                    @if(!empty($order['shipping_address']['phone']))
                        <p class="text-xs text-muted pt-1 lining-nums">{{ $order['shipping_address']['phone'] }}</p>
                    @endif
                </div>
            </div>

            <!-- Delivery Method -->
            <div class="space-y-1 pt-6 border-t border-[#ECEEE6]">
                <h3 class="text-xs uppercase tracking-[0.2em] text-muted font-medium">
                    Delivery Method
                </h3>
                <p class="text-sm text-ink leading-relaxed">
                    {{ $order['delivery_method'] }}
                </p>
                @if(!empty($order['tracking_number']))
                    <p class="text-xs text-muted pt-1">
                        Tracking: <span class="font-mono text-ink lining-nums">{{ $order['tracking_number'] }}</span>
                    </p>
                @endif
            </div>

            <!-- Payment Method -->
            <div class="space-y-1 pt-6 border-t border-[#ECEEE6]">
                <h3 class="text-xs uppercase tracking-[0.2em] text-muted font-medium">
                    Payment Method (Demo)
                </h3>
                <p class="text-sm text-ink leading-relaxed">
                    {{ $order['payment_method'] }}
                </p>
            </div>

            <!-- Mobile Action Buttons (Full-width) -->
            <div class="lg:hidden space-y-3 pt-6 border-t border-[#ECEEE6]">
                <button 
                    type="button" 
                    @click="buyAgain()"
                    class="btn-primary w-full min-h-[44px] cursor-pointer"
                >
                    Buy Again
                </button>
                <a 
                    href="{{ route('account.orders') }}" 
                    class="btn-secondary w-full min-h-[44px] flex items-center justify-center text-center"
                >
                    Back to Orders
                </a>
            </div>
        </div>
    </div>
</div>
@endsection
