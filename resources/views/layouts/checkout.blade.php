<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}" class="h-full bg-ivory text-ink antialiased">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>@yield('title', 'Checkout — ' . config('demo.site.name'))</title>
    <meta name="description" content="Secure and encrypted botanical checkout demonstration for The Everbloom.">
    <meta name="robots" content="noindex, nofollow">

    <!-- Preconnect to Google Fonts -->
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@300;400;500;600;700&family=Playfair+Display:ital,wght@0,400;0,500;0,600;0,700;1,400;1,500&display=swap" rel="stylesheet">

    @stack('meta')
    @vite(['resources/css/app.css', 'resources/js/app.js'])
    @stack('styles')
</head>
<body class="min-h-full flex flex-col bg-ivory text-ink selection:bg-sage selection:text-ink">
    <!-- Accessible Skip to Content -->
    <a href="#checkout-content" class="sr-only focus:not-sr-only focus:fixed focus:top-4 focus:left-4 focus:z-50 focus:px-4 focus:py-2.5 focus:bg-forest focus:text-ivory focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-forest text-xs uppercase tracking-widest font-medium">
        Skip to checkout content
    </a>

    <!-- Minimal Distraction-Free Checkout Header -->
    <header class="border-b border-beige/60 bg-ivory/95 backdrop-blur-sm sticky top-0 z-30">
        <div class="site-container py-4 md:py-5 flex items-center justify-between">
            <a href="/cart" class="inline-flex items-center gap-1.5 text-xs uppercase tracking-widest text-muted hover:text-forest transition-colors font-medium">
                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24" aria-hidden="true">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.75" d="M15 19l-7-7 7-7" />
                </svg>
                <span>Return to Bag</span>
            </a>

            <!-- Brand Logo -->
            <a href="/" class="text-center group" aria-label="{{ config('demo.site.name') }} Home">
                <span class="font-serif text-xl md:text-2xl font-normal tracking-wide text-ink group-hover:text-forest transition-colors block">
                    THE EVERBLOOM
                </span>
                <span class="text-[9px] uppercase tracking-[0.28em] text-muted block -mt-0.5">
                    Apothecary
                </span>
            </a>

            <!-- Secure Badge -->
            <div class="inline-flex items-center gap-1.5 text-xs text-muted font-medium">
                <svg class="w-4 h-4 text-forest" fill="none" stroke="currentColor" viewBox="0 0 24 24" aria-hidden="true">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.75" d="M12 15v2m-6 4h12a2 2 0 002-2v-6a2 2 0 00-2-2H6a2 2 0 00-2 2v6a2 2 0 002 2zm10-10V7a4 4 0 00-8 0v4h8z" />
                </svg>
                <span class="hidden sm:inline">Encrypted Checkout Demo</span>
            </div>
        </div>
    </header>

    <!-- Main Checkout View -->
    <main id="checkout-content" class="flex-1 focus:outline-none py-8 md:py-12" tabindex="-1">
        {{ $slot ?? '' }}
        @yield('content')
    </main>

    <!-- Minimal Distraction-Free Footer -->
    <footer class="border-t border-beige/60 py-6 text-center text-xs text-muted bg-ivory">
        <div class="site-container flex flex-col sm:flex-row items-center justify-between gap-3">
            <p>© {{ date('Y') }} {{ config('demo.site.name') }}. Turnkey Frontend Architecture.</p>
            <div class="flex items-center gap-5">
                <a href="/privacy-policy" class="hover:text-forest transition-colors">Privacy</a>
                <a href="/terms-and-conditions" class="hover:text-forest transition-colors">Terms</a>
                <a href="/faq" class="hover:text-forest transition-colors">Help</a>
            </div>
        </div>
    </footer>

    @stack('scripts')
</body>
</html>
