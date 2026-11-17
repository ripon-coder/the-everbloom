@extends('layouts.account')

@section('title', 'Order #' . $order['id'] . ' — ' . config('demo.site.name'))

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
        <span aria-hidden="true">&middot;</span>
        <x-storefront.status-badge :status="$order['status']" />
    </div>
@endsection

@section('header_actions')
    <div class="hidden lg:flex items-center">
        <a 
            href="{{ route('account.orders') }}" 
            class="min-h-[44px] inline-flex items-center text-[13px] text-muted hover:text-forest underline underline-offset-4 transition-colors px-2 py-2"
        >
            &larr; Back to orders
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

    $baseDate = !empty($order['iso_date'])
        ? \Illuminate\Support\Carbon::parse($order['iso_date'])
        : now();

    $defaultTimeline = [
        'Placed' => $baseDate->copy()->format('M j, Y · g:i A'),
        'Processing' => $baseDate->copy()->addHours(4)->format('M j, Y · g:i A'),
        'Shipped' => $baseDate->copy()->addDay()->format('M j, Y'),
        'Delivered' => $baseDate->copy()->addDays(3)->format('M j, Y'),
    ];
    $orderTimeline = $order['timeline'] ?? [];
    $courierName = $order['courier'] ?? 'Steadfast Courier';

    $stepItems = [];
    foreach ($steps as $index => $step) {
        $isCompleted = $index < $currentStepIndex;
        $isCurrent = $index === $currentStepIndex;
        $isPending = $index > $currentStepIndex;
        $rawDate = $orderTimeline[$step] ?? $defaultTimeline[$step];

        $stepItems[] = [
            'number' => $index + 1,
            'label' => $step,
            'isCompleted' => $isCompleted,
            'isCurrent' => $isCurrent,
            'isPending' => $isPending,
            'srStatus' => $isCompleted ? 'Completed' : ($isCurrent ? 'Current step' : 'Pending'),
            'dateText' => $isPending ? 'Estimated ' . $rawDate : $rawDate,
            'isShipped' => $step === 'Shipped',
        ];
    }

    // Shipping & totals calculation consistent with checkout config
    $shippingCfg = config('demo.site.shipping', []);
    $freeThreshold = (float) ($shippingCfg['free_shipping_threshold'] ?? 500);
    $standardFee = (float) ($shippingCfg['standard_fee'] ?? 60);
    $subtotal = (float) ($order['subtotal'] ?? 0);
    $discountAmount = (float) ($order['discount_amount'] ?? 0);
    $taxAmount = (float) ($order['tax'] ?? 0);

    $rawShipping = (float) ($order['shipping'] ?? 0);
    if ($rawShipping > 0) {
        $shippingFee = $rawShipping;
    } elseif ($subtotal > 0 && $subtotal < $freeThreshold) {
        $shippingFee = $standardFee;
    } else {
        $shippingFee = 0.0;
    }
    $computedTotal = max(0, $subtotal - $discountAmount + $shippingFee + $taxAmount);

    $deliveryLabel = $order['delivery_method'] ?? 'Standard delivery (3 to 5 business days)';
    if (stripos($deliveryLabel, 'Standard') !== false) {
        $deliveryLabel = 'Standard delivery (3 to 5 business days)';
    }

    $paymentLabel = $order['payment_method'] ?? 'Cash on Delivery';
    if (stripos($paymentLabel, 'Apple Pay') !== false || stripos($paymentLabel, 'Visa') !== false || stripos($paymentLabel, 'Mastercard') !== false) {
        $paymentLabel = 'Cash on Delivery';
    }

    $addr = $order['shipping_address'] ?? [];
    $addrName = trim(($addr['first_name'] ?? '') . ' ' . ($addr['last_name'] ?? ''));
    $addrLocation = implode(', ', array_filter([
        $addr['area_name'] ?? null,
        $addr['thana_name'] ?? ($addr['city'] ?? null),
        $addr['district_name'] ?? ($addr['state'] ?? null),
        $addr['country'] ?? 'Bangladesh',
    ]));

    $itemsPayload = array_map(function ($item) {
        return [
            'productId' => (int) ($item['product_id'] ?? 0),
            'variantId' => (string) ($item['variant_id'] ?? $item['variant'] ?? ''),
            'qty' => (int) ($item['qty'] ?? 1),
        ];
    }, $order['items'] ?? []);
@endphp

<div 
    x-data="{
        addedNotice: false,
        trackingNotice: '',
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
        },
        showTrackingDemo() {
            this.trackingNotice = 'Demo only. Tracking is not connected.';
            setTimeout(() => { this.trackingNotice = ''; }, 4500);
        }
    }" 
    class="space-y-8 pb-24 lg:pb-0"
>
    <!-- Order Status Timeline -->
    <section aria-label="Order Status Timeline" class="pb-8 border-b border-[#ECEEE6]">
        <h2 class="sr-only">Order Fulfillment Progress</h2>

        <!-- DESKTOP TIMELINE (Horizontal, 4 equal columns) -->
        <ol class="hidden lg:grid lg:grid-cols-4 gap-0" role="list">
            @foreach($stepItems as $index => $step)
                <li 
                    class="relative flex flex-col items-center text-center px-2"
                    @if($step['isCurrent']) aria-current="step" @endif
                >
                    <span class="sr-only">{{ $step['srStatus'] }}</span>

                    <!-- Circle + Left/Right 1px Connectors -->
                    <div class="relative w-full h-7 flex items-center justify-center">
                        @if($index > 0)
                            <span 
                                class="absolute left-0 right-1/2 top-1/2 -translate-y-1/2 h-px {{ $index <= $currentStepIndex ? 'bg-forest' : 'bg-[#D5DBCF]' }}" 
                                aria-hidden="true"
                            ></span>
                        @endif
                        @if($index < count($stepItems) - 1)
                            <span 
                                class="absolute left-1/2 right-0 top-1/2 -translate-y-1/2 h-px {{ $index < $currentStepIndex ? 'bg-forest' : 'bg-[#D5DBCF]' }}" 
                                aria-hidden="true"
                            ></span>
                        @endif

                        @if($step['isCompleted'])
                            <span class="relative z-10 w-7 h-7 rounded-full bg-forest border border-forest text-ivory flex items-center justify-center">
                                <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24" aria-hidden="true">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7" />
                                </svg>
                            </span>
                        @elseif($step['isCurrent'])
                            <span 
                                class="relative z-10 w-7 h-7 rounded-full bg-white border-2 border-forest text-forest text-xs font-medium flex items-center justify-center lining-nums"
                                style="box-shadow: 0 0 0 4px #E8EEE5;"
                            >
                                {{ $step['number'] }}
                            </span>
                        @else
                            <span class="relative z-10 w-7 h-7 rounded-full bg-white border border-[#D5DBCF] text-muted text-xs font-medium flex items-center justify-center lining-nums">
                                {{ $step['number'] }}
                            </span>
                        @endif
                    </div>

                    <!-- Fixed-Height Centered Labels & Dates Below -->
                    <div class="mt-3 min-h-[72px] flex flex-col items-center">
                        <div class="flex items-center justify-center gap-1.5">
                            <span class="font-sans text-xs uppercase tracking-[0.2em] text-[#292D29] font-medium">
                                {{ $step['label'] }}
                            </span>
                            @if($step['isCurrent'])
                                <span class="text-[10px] uppercase tracking-[0.14em] font-medium text-forest bg-[#E8EEE5] px-1.5 py-0.5">
                                    Current
                                </span>
                            @endif
                        </div>
                        <p class="mt-1 text-xs text-muted font-sans lining-nums">
                            {{ $step['dateText'] }}
                        </p>
                        @if($step['isShipped'])
                            <p class="mt-1 text-xs text-muted font-sans">
                                {{ $courierName }} &middot;
                                <button 
                                    type="button" 
                                    @click="showTrackingDemo()" 
                                    class="text-forest hover:text-ink underline underline-offset-2 cursor-pointer"
                                >
                                    Track package
                                </button>
                            </p>
                        @endif
                    </div>
                </li>
            @endforeach
        </ol>

        <!-- MOBILE TIMELINE (Vertical list with continuous 1px connector) -->
        <ol class="lg:hidden space-y-0" role="list">
            @foreach($stepItems as $index => $step)
                <li 
                    class="relative flex items-start gap-4 {{ $index < count($stepItems) - 1 ? 'pb-6' : '' }}"
                    @if($step['isCurrent']) aria-current="step" @endif
                >
                    <span class="sr-only">{{ $step['srStatus'] }}</span>

                    @if($index < count($stepItems) - 1)
                        <span 
                            class="absolute left-[13px] top-7 bottom-0 w-px {{ $index < $currentStepIndex ? 'bg-forest' : 'bg-[#D5DBCF]' }}" 
                            aria-hidden="true"
                        ></span>
                    @endif

                    @if($step['isCompleted'])
                        <span class="relative z-10 w-7 h-7 rounded-full bg-forest border border-forest text-ivory flex items-center justify-center shrink-0">
                            <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24" aria-hidden="true">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7" />
                            </svg>
                        </span>
                    @elseif($step['isCurrent'])
                        <span 
                            class="relative z-10 w-7 h-7 rounded-full bg-white border-2 border-forest text-forest text-xs font-medium flex items-center justify-center shrink-0 lining-nums"
                            style="box-shadow: 0 0 0 4px #E8EEE5;"
                        >
                            {{ $step['number'] }}
                        </span>
                    @else
                        <span class="relative z-10 w-7 h-7 rounded-full bg-white border border-[#D5DBCF] text-muted text-xs font-medium flex items-center justify-center shrink-0 lining-nums">
                            {{ $step['number'] }}
                        </span>
                    @endif

                    <div class="min-w-0 pt-0.5">
                        <div class="flex flex-wrap items-center gap-2">
                            <span class="font-sans text-xs uppercase tracking-[0.2em] text-[#292D29] font-medium">
                                {{ $step['label'] }}
                            </span>
                            @if($step['isCurrent'])
                                <span class="text-[10px] uppercase tracking-[0.14em] font-medium text-forest bg-[#E8EEE5] px-1.5 py-0.5">
                                    Current
                                </span>
                            @endif
                        </div>
                        <p class="mt-0.5 text-xs text-muted font-sans lining-nums">
                            {{ $step['dateText'] }}
                        </p>
                        @if($step['isShipped'])
                            <p class="mt-1 text-xs text-muted font-sans">
                                {{ $courierName }} &middot;
                                <button 
                                    type="button" 
                                    @click="showTrackingDemo()" 
                                    class="text-forest hover:text-ink underline underline-offset-2 cursor-pointer"
                                >
                                    Track package
                                </button>
                            </p>
                        @endif
                    </div>
                </li>
            @endforeach
        </ol>

        <!-- Demo Tracking Message -->
        <div 
            x-show="trackingNotice" 
            x-cloak 
            x-transition 
            class="mt-4 p-3 bg-[#E8EEE5] border border-[#304C3D]/25 text-xs text-forest font-medium"
            role="status"
            aria-live="polite"
            x-text="trackingNotice"
        ></div>
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
        <span>Items added to your bag</span>
        <button type="button" @click="addedNotice = false" class="w-11 h-11 -mr-2 inline-flex items-center justify-center hover:text-sage-light text-ivory cursor-pointer" aria-label="Close notification">
            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24" aria-hidden="true">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.75" d="M6 18L18 6M6 6l12 12" />
            </svg>
        </button>
    </div>

    <!-- Main Content Grid (Desktop: Left 7/12 = items + delivery/payment; Right 5/12 = summary + shipping address) -->
    <div class="grid grid-cols-1 lg:grid-cols-12 gap-8 lg:gap-12 items-start">

        <!-- Left Column (7/12) -->
        <div class="lg:col-span-7 space-y-6">
            <div class="pb-3 border-b border-[#ECEEE6] flex items-baseline justify-between">
                <h2 class="font-serif text-xl text-ink font-normal">
                    Ordered Formulations
                </h2>
                <span class="text-xs text-muted">
                    {{ count($order['items']) }} {{ count($order['items']) === 1 ? 'item' : 'items' }}
                </span>
            </div>

            <!-- Items List (py-4, no extra bottom gap) -->
            <ul class="divide-y divide-[#ECEEE6] border-b border-[#ECEEE6]" role="list">
                @foreach($order['items'] as $item)
                    @php
                        $imgPath = !empty($item['image'])
                            ? (str_starts_with($item['image'], '/images/') ? $item['image'] : '/images/' . ltrim($item['image'], '/'))
                            : '';
                    @endphp
                    <li class="py-4 flex items-center gap-4">
                        <a href="{{ route('shop.product', $item['slug'] ?? 'botanical-radiance-serum') }}" class="relative w-16 h-16 shrink-0 bg-sage-light/40 border border-[#E3E6DD] block overflow-visible">
                            <x-storefront.image 
                                :src="$imgPath" 
                                :alt="$item['name']" 
                                aspect-ratio="aspect-square" 
                                class="w-full h-full object-cover"
                            />
                            <span class="price lining-nums absolute -top-2 -right-2 w-5 h-5 bg-forest text-ivory text-xs font-medium rounded-full flex items-center justify-center pointer-events-none z-10">
                                {{ $item['qty'] }}
                            </span>
                        </a>

                        <div class="flex-1 min-w-0">
                            <h3 class="font-serif text-base text-ink font-medium leading-snug break-words">
                                <a href="{{ route('shop.product', $item['slug'] ?? 'botanical-radiance-serum') }}" class="hover:text-forest transition-colors">
                                    {{ $item['name'] }}
                                </a>
                            </h3>
                            <p class="text-xs text-muted mt-0.5 lining-nums">
                                {{ $item['variant'] }} &middot; Qty {{ $item['qty'] }}
                            </p>
                        </div>

                        <div class="text-right shrink-0">
                            <p class="price lining-nums text-sm font-medium text-ink">
                                {{ \App\Support\Currency::format($item['price'] * $item['qty']) }}
                            </p>
                            @if($item['qty'] > 1)
                                <p class="price lining-nums text-xs text-muted mt-0.5">
                                    {{ \App\Support\Currency::format($item['price']) }} &times; {{ $item['qty'] }}
                                </p>
                            @endif
                        </div>
                    </li>
                @endforeach
            </ul>

            <!-- Desktop Delivery & Payment Blocks -->
            <div class="hidden lg:grid lg:grid-cols-2 gap-6 pt-2">
                <div class="space-y-2">
                    <h3 class="font-sans text-xs uppercase tracking-[0.2em] text-[#292D29] font-medium">
                        Delivery method
                    </h3>
                    <p class="font-sans text-[15px] leading-[1.5] text-ink">
                        {{ $deliveryLabel }}
                    </p>
                    @if(!empty($order['tracking_number']))
                        <p class="font-sans text-xs text-muted lining-nums">
                            Tracking: <span class="text-ink">{{ $order['tracking_number'] }}</span>
                        </p>
                    @endif
                </div>

                <div class="space-y-2">
                    <h3 class="font-sans text-xs uppercase tracking-[0.2em] text-[#292D29] font-medium">
                        Payment method
                    </h3>
                    <p class="font-sans text-[15px] leading-[1.5] text-ink">
                        {{ $paymentLabel }}
                    </p>
                </div>
            </div>

            <!-- Desktop Buy Again Button (Single Back to orders link is top-right) -->
            <div class="hidden lg:flex items-center pt-4 border-t border-[#ECEEE6]">
                <button 
                    type="button" 
                    @click="buyAgain()"
                    class="btn-primary min-h-[44px] cursor-pointer"
                >
                    Buy Again
                </button>
            </div>
        </div>

        <!-- Right Column (5/12): Order Summary + Shipping Address (Desktop) & Compact Info Rows (Mobile) -->
        <div class="lg:col-span-5 space-y-6">

            <!-- Order Summary Panel -->
            <div class="bg-white border border-[#E3E6DD] p-6 space-y-4">
                <h2 class="font-serif text-lg text-ink font-normal pb-3 border-b border-[#ECEEE6]">
                    Order Summary
                </h2>

                <dl class="space-y-2.5 text-sm">
                    <div class="flex justify-between text-muted">
                        <dt>Subtotal</dt>
                        <dd class="price lining-nums text-ink font-medium">{{ \App\Support\Currency::format($subtotal) }}</dd>
                    </div>

                    @if($discountAmount > 0)
                        <div class="flex justify-between text-forest font-medium">
                            <dt>Discount{{ !empty($order['discount_code']) ? ' (' . $order['discount_code'] . ')' : '' }}</dt>
                            <dd class="price lining-nums">-{{ \App\Support\Currency::format($discountAmount) }}</dd>
                        </div>
                    @endif

                    <div class="flex justify-between text-muted">
                        <dt>Shipping</dt>
                        <dd class="font-medium">
                            @if($shippingFee <= 0)
                                <span class="text-forest">Complimentary</span>
                            @else
                                <span class="price lining-nums text-ink">{{ \App\Support\Currency::format($shippingFee) }}</span>
                            @endif
                        </dd>
                    </div>

                    @if($taxAmount > 0)
                        <div class="flex justify-between text-muted">
                            <dt>Estimated Tax</dt>
                            <dd class="price lining-nums text-ink font-medium">{{ \App\Support\Currency::format($taxAmount) }}</dd>
                        </div>
                    @endif

                    <div class="flex justify-between items-baseline pt-3 border-t border-[#ECEEE6] text-ink text-base font-medium">
                        <dt class="font-serif text-lg">Total</dt>
                        <dd class="price lining-nums text-lg">{{ \App\Support\Currency::format($computedTotal) }}</dd>
                    </div>
                </dl>
            </div>

            <!-- Desktop Shipping Address -->
            <div class="hidden lg:block space-y-2 pt-2">
                <h3 class="font-sans text-xs uppercase tracking-[0.2em] text-[#292D29] font-medium">
                    Shipping address
                </h3>
                <div class="font-sans text-[15px] leading-[1.5] text-ink break-words space-y-0.5">
                    @if($addrName)
                        <p class="font-medium text-ink">{{ $addrName }}</p>
                    @endif
                    @if(!empty($addr['address_line1']))
                        <p>{{ $addr['address_line1'] }}</p>
                    @endif
                    @if($addrLocation)
                        <p>{{ $addrLocation }}</p>
                    @endif
                    @if(!empty($addr['phone']))
                        <p class="text-xs text-muted pt-1 lining-nums">{{ $addr['phone'] }}</p>
                    @endif
                </div>
            </div>

            <!-- Mobile Compact Rows: Address, Delivery, Payment -->
            <div class="lg:hidden divide-y divide-[#ECEEE6] border-y border-[#ECEEE6]">
                <div class="py-3.5 flex items-start justify-between gap-4">
                    <span class="font-sans text-xs uppercase tracking-[0.2em] text-[#292D29] font-medium shrink-0 pt-0.5">
                        Shipping address
                    </span>
                    <div class="text-right font-sans text-[15px] leading-[1.5] text-ink min-w-0">
                        @if($addrName)
                            <p class="font-medium">{{ $addrName }}</p>
                        @endif
                        <p class="text-sm text-muted">{{ implode(', ', array_filter([$addr['address_line1'] ?? null, $addrLocation])) }}</p>
                        @if(!empty($addr['phone']))
                            <p class="text-xs text-muted lining-nums mt-0.5">{{ $addr['phone'] }}</p>
                        @endif
                    </div>
                </div>

                <div class="py-3.5 flex items-start justify-between gap-4">
                    <span class="font-sans text-xs uppercase tracking-[0.2em] text-[#292D29] font-medium shrink-0 pt-0.5">
                        Delivery method
                    </span>
                    <div class="text-right font-sans text-[15px] leading-[1.5] text-ink min-w-0">
                        <p>{{ $deliveryLabel }}</p>
                        @if(!empty($order['tracking_number']))
                            <p class="text-xs text-muted lining-nums mt-0.5">{{ $order['tracking_number'] }}</p>
                        @endif
                    </div>
                </div>

                <div class="py-3.5 flex items-start justify-between gap-4">
                    <span class="font-sans text-xs uppercase tracking-[0.2em] text-[#292D29] font-medium shrink-0 pt-0.5">
                        Payment method
                    </span>
                    <p class="text-right font-sans text-[15px] leading-[1.5] text-ink">
                        {{ $paymentLabel }}
                    </p>
                </div>
            </div>

            <!-- Mobile Action Buttons (Full-width) -->
            <div class="lg:hidden space-y-3 pt-2">
                <button 
                    type="button" 
                    @click="buyAgain()"
                    class="btn-primary w-full min-h-[48px] cursor-pointer"
                >
                    Buy again
                </button>
                <a 
                    href="{{ route('account.orders') }}" 
                    class="btn-secondary w-full min-h-[48px] flex items-center justify-center text-center"
                >
                    Back to orders
                </a>
            </div>

        </div>
    </div>
</div>
@endsection
