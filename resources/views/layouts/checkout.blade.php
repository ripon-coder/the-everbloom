<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}" class="h-full bg-ivory text-ink antialiased">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>@yield('title', 'Demo Checkout — ' . config('demo.site.name'))</title>
    <meta name="description" content="Botanical skincare demo checkout for The Everbloom.">
    <meta name="robots" content="noindex, nofollow">

    <!-- Preconnect to Google Fonts -->
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@300;400;500;600;700&family=Playfair+Display:ital,wght@0,400;0,500;0,600;0,700;1,400;1,500&display=swap" rel="stylesheet">

    @stack('meta')

    <!-- Preload products catalog & discounts payload for Alpine Cart Store -->
    <script>
        window.__everbloomProducts = {!! json_encode(config('demo.products', []), JSON_UNESCAPED_SLASHES | JSON_UNESCAPED_UNICODE) !!};
        window.__everbloomDiscounts = {!! json_encode(config('demo.site.demo_discounts', []), JSON_UNESCAPED_SLASHES | JSON_UNESCAPED_UNICODE) !!};
    </script>
    <script id="everbloom-products-data" type="application/json">
    {!! json_encode(config('demo.products', []), JSON_UNESCAPED_SLASHES | JSON_UNESCAPED_UNICODE) !!}
    </script>
    <script id="everbloom-discounts-data" type="application/json">
    {!! json_encode(config('demo.site.demo_discounts', []), JSON_UNESCAPED_SLASHES | JSON_UNESCAPED_UNICODE) !!}
    </script>

    @vite(['resources/css/app.css', 'resources/js/app.js'])
    @stack('styles')
</head>
<body class="min-h-full flex flex-col bg-ivory text-ink selection:bg-sage selection:text-ink">
    <!-- Accessible Skip to Content -->
    <a href="#checkout-content" class="sr-only focus:not-sr-only focus:fixed focus:top-4 focus:left-4 focus:z-50 focus:px-4 focus:py-2.5 focus:bg-forest focus:text-ivory focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-forest text-xs uppercase tracking-widest font-medium">
        Skip to checkout content
    </a>

    <!-- 1. HEADER: Exact same announcement bar and header component as the cart page -->
    <x-storefront.announcement-bar />
    <x-storefront.header />

    <!-- Main Checkout View -->
    <main id="checkout-content" class="flex-1 focus:outline-none" tabindex="-1">
        {{ $slot ?? '' }}
        @yield('content')
    </main>

    <!-- Site Footer (Identical to cart page & app layout) -->
    <x-storefront.footer />

    <!-- Global Interactive Components for Header Functionality -->
    <x-storefront.mobile-navigation />
    <x-storefront.cart-drawer />
    <x-storefront.search-modal />

    <!-- Ensure Alpine cart store extensions (.lines & .totals) -->
    <script>
        (function() {
            function syncCartStore() {
                if (window.Alpine && typeof Alpine.store === 'function') {
                    try {
                        const cart = Alpine.store('cart');
                        if (cart) {
                            if (!('lines' in cart)) {
                                Object.defineProperty(cart, 'lines', {
                                    get() { return this.items; },
                                    enumerable: true,
                                    configurable: true
                                });
                            }
                            if (!('totals' in cart)) {
                                Object.defineProperty(cart, 'totals', {
                                    get() {
                                        return {
                                            subtotal: this.subtotal,
                                            discount: this.discountAmount,
                                            shipping: this.shippingCost,
                                            total: this.total
                                        };
                                    },
                                    enumerable: true,
                                    configurable: true
                                });
                            }
                            if (typeof cart.cleanAndValidate === 'function') {
                                cart.cleanAndValidate();
                            }
                        }
                    } catch (e) {}
                }
            }
            document.addEventListener('alpine:init', syncCartStore);
            document.addEventListener('DOMContentLoaded', syncCartStore);
            window.addEventListener('load', syncCartStore);
            syncCartStore();
        })();
    </script>

    @stack('scripts')
</body>
</html>
