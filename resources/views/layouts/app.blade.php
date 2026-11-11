<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}" class="h-full bg-ivory text-ink antialiased">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>@yield('title', config('demo.site.name') . ' — ' . config('demo.site.tagline'))</title>
    <meta name="description" content="@yield('meta_description', 'Discover The Everbloom: conscious, botanical skincare thoughtfully formulated for your daily ritual.')">
    <meta name="robots" content="@yield('meta_robots', 'index, follow')">
    <link rel="canonical" href="@yield('canonical', url()->current())">

    <!-- Open Graph / Social Sharing -->
    <meta property="og:site_name" content="{{ config('demo.site.name') }}">
    <meta property="og:title" content="@yield('title', config('demo.site.name') . ' — ' . config('demo.site.tagline'))">
    <meta property="og:description" content="@yield('meta_description', 'Discover The Everbloom: conscious, botanical skincare thoughtfully formulated for your daily ritual.')">
    <meta property="og:type" content="@yield('og_type', 'website')">
    <meta property="og:url" content="@yield('canonical', url()->current())">
    <meta property="og:image" content="@yield('og_image', asset('images/editorial/og-default.jpg'))">
    <meta name="twitter:card" content="summary_large_image">

    <!-- Preconnect to Google Fonts -->
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;500;600&family=Playfair+Display:ital,wght@0,400;0,500;0,600;1,400&display=swap" rel="stylesheet">

    @stack('meta')

    <!-- Organization JSON-LD -->
    <script type="application/ld+json">
    {!! json_encode([
        '@context' => 'https://schema.org',
        '@type' => 'Organization',
        'name' => config('demo.site.name'),
        'url' => config('demo.site.url'),
        'logo' => asset('images/brand/logo.svg'),
        'sameAs' => array_column(config('demo.site.socials', []), 'url'),
        'contactPoint' => [
            '@type' => 'ContactPoint',
            'telephone' => config('demo.site.contact.phone'),
            'contactType' => 'customer service',
            'areaServed' => 'US',
            'availableLanguage' => 'en',
        ],
    ], JSON_UNESCAPED_SLASHES | JSON_PRETTY_PRINT) !!}
    </script>
    @stack('structured-data')

    @vite(['resources/css/app.css', 'resources/js/app.js'])
    @stack('styles')
    @php
        $showBottomNav = !View::hasSection('hide_bottom_nav') && !request()->is('checkout*');
    @endphp
</head>
<body class="min-h-full flex flex-col bg-ivory text-ink selection:bg-sage selection:text-ink {{ $showBottomNav ? 'pb-[calc(56px+env(safe-area-inset-bottom))] lg:pb-0' : '' }}">
    <!-- Accessible Skip to Content -->
    <a href="#main-content" class="sr-only focus:not-sr-only focus:fixed focus:top-4 focus:left-4 focus:z-50 focus:px-4 focus:py-2.5 focus:bg-forest focus:text-ivory text-xs uppercase tracking-widest font-medium">
        Skip to main content
    </a>

    <!-- Global Announcement Bar -->
    <x-storefront.announcement-bar />

    <!-- Site Header & Navigation -->
    <x-storefront.header />

    <!-- Main Content Area -->
    <main id="main-content" class="flex-1 focus:outline-none" tabindex="-1">
        {{ $slot ?? '' }}
        @yield('content')
    </main>

    <!-- Site Footer -->
    <x-storefront.footer />

    <!-- Mobile Bottom Navigation Bar (Visible only below lg) -->
    @if ($showBottomNav)
        <x-storefront.bottom-nav />
    @endif

    <!-- Global Mobile Navigation Drawer -->
    <x-storefront.mobile-navigation />

    <!-- Global Cart Drawer -->
    <x-storefront.cart-drawer />

    <!-- Global Search Modal -->
    <x-storefront.search-modal />

    @stack('scripts')
</body>
</html>
