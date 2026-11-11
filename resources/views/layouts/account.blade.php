@extends('layouts.app')

@section('meta_description', 'Manage your botanical skincare orders, saved delivery addresses, and account profile at The Everbloom.')
@section('meta_robots', 'noindex, nofollow')
@section('hide_newsletter', true)

@section('content')
<div class="site-container max-w-6xl py-8 md:py-12">
    <!-- Breadcrumbs -->
    <div class="mb-6">
        @yield('breadcrumbs')
    </div>

    <!-- Page Header -->
    <div class="pb-6 border-b border-[#ECEEE6]">
        <span class="text-xs uppercase tracking-[0.2em] font-medium text-forest block mb-1">
            MY ACCOUNT
        </span>
        <div class="flex flex-col sm:flex-row sm:items-baseline sm:justify-between gap-2">
            <div>
                <h1 class="font-serif text-3xl sm:text-4xl text-ink font-normal tracking-tight">
                    @yield('page_title')
                </h1>
                @hasSection('page_subtitle')
                    <div class="mt-1">
                        @yield('page_subtitle')
                    </div>
                @endif
                <p class="text-xs text-muted font-normal mt-1.5 block">
                    @yield('demo_notice', 'Demo account — sample data for preview only.')
                </p>
            </div>
            @yield('header_actions')
        </div>

        <!-- Mobile Horizontal Scrollable Tab Row (below lg, under page title; 1px border only) -->
        <nav class="lg:hidden mt-6 -mb-6 border-t border-[#ECEEE6]" aria-label="Account navigation">
            <div class="flex flex-nowrap overflow-x-auto snap-x snap-mandatory [-ms-overflow-style:none] [scrollbar-width:none] [&::-webkit-scrollbar]:hidden -mx-3 px-3 sm:-mx-5 sm:px-5">
                @php
                    $isOverview = request()->routeIs('account');
                    $isOrders = request()->routeIs('account.orders*') || request()->routeIs('account.order.details');
                    $isAddresses = request()->routeIs('account.addresses');
                    $isWishlist = request()->routeIs('wishlist');
                    $isProfile = request()->routeIs('account.profile');
                @endphp
                
                <a 
                    href="{{ route('account') }}" 
                    class="min-h-[44px] flex items-center shrink-0 snap-start px-3.5 text-xs uppercase tracking-[0.2em] transition-colors border-b {{ $isOverview ? 'border-forest bg-sage-light/50 text-forest font-semibold' : 'border-transparent text-muted hover:text-ink' }}"
                    @if($isOverview) aria-current="page" @endif
                >
                    Overview
                </a>

                <a 
                    href="{{ route('account.orders') }}" 
                    class="min-h-[44px] flex items-center shrink-0 snap-start px-3.5 text-xs uppercase tracking-[0.2em] transition-colors border-b {{ $isOrders ? 'border-forest bg-sage-light/50 text-forest font-semibold' : 'border-transparent text-muted hover:text-ink' }}"
                    @if($isOrders) aria-current="page" @endif
                >
                    Orders
                </a>

                <a 
                    href="{{ route('account.addresses') }}" 
                    class="min-h-[44px] flex items-center shrink-0 snap-start px-3.5 text-xs uppercase tracking-[0.2em] transition-colors border-b {{ $isAddresses ? 'border-forest bg-sage-light/50 text-forest font-semibold' : 'border-transparent text-muted hover:text-ink' }}"
                    @if($isAddresses) aria-current="page" @endif
                >
                    Addresses
                </a>

                <a 
                    href="{{ route('wishlist') }}" 
                    class="min-h-[44px] flex items-center shrink-0 snap-start px-3.5 text-xs uppercase tracking-[0.2em] transition-colors border-b {{ $isWishlist ? 'border-forest bg-sage-light/50 text-forest font-semibold' : 'border-transparent text-muted hover:text-ink' }}"
                    @if($isWishlist) aria-current="page" @endif
                >
                    Wishlist
                </a>

                <a 
                    href="{{ route('account.profile') }}" 
                    class="min-h-[44px] flex items-center shrink-0 snap-start px-3.5 text-xs uppercase tracking-[0.2em] transition-colors border-b {{ $isProfile ? 'border-forest bg-sage-light/50 text-forest font-semibold' : 'border-transparent text-muted hover:text-ink' }}"
                    @if($isProfile) aria-current="page" @endif
                >
                    Profile
                </a>
            </div>
        </nav>
    </div>

    <!-- Main Account Grid (Desktop 240px sidebar + content area, gap-12) -->
    <div class="mt-8 md:mt-10 flex flex-col lg:flex-row lg:gap-12">
        <!-- Desktop Sidebar (lg and up; 1px border only) -->
        <aside class="hidden lg:block w-[240px] shrink-0" aria-label="Account sidebar">
            <nav class="space-y-1">
                @php
                    $isOverview = request()->routeIs('account');
                    $isOrders = request()->routeIs('account.orders*') || request()->routeIs('account.order.details');
                    $isAddresses = request()->routeIs('account.addresses');
                    $isWishlist = request()->routeIs('wishlist');
                    $isProfile = request()->routeIs('account.profile');
                @endphp

                <a 
                    href="{{ route('account') }}" 
                    class="min-h-[44px] flex items-center border-l pl-4 py-2 text-xs uppercase tracking-[0.2em] transition-colors {{ $isOverview ? 'border-forest bg-sage-light/40 text-forest font-semibold' : 'border-[#ECEEE6] text-muted hover:text-ink hover:border-sage' }}"
                    @if($isOverview) aria-current="page" @endif
                >
                    Overview
                </a>

                <a 
                    href="{{ route('account.orders') }}" 
                    class="min-h-[44px] flex items-center border-l pl-4 py-2 text-xs uppercase tracking-[0.2em] transition-colors {{ $isOrders ? 'border-forest bg-sage-light/40 text-forest font-semibold' : 'border-[#ECEEE6] text-muted hover:text-ink hover:border-sage' }}"
                    @if($isOrders) aria-current="page" @endif
                >
                    Orders
                </a>

                <a 
                    href="{{ route('account.addresses') }}" 
                    class="min-h-[44px] flex items-center border-l pl-4 py-2 text-xs uppercase tracking-[0.2em] transition-colors {{ $isAddresses ? 'border-forest bg-sage-light/40 text-forest font-semibold' : 'border-[#ECEEE6] text-muted hover:text-ink hover:border-sage' }}"
                    @if($isAddresses) aria-current="page" @endif
                >
                    Addresses
                </a>

                <a 
                    href="{{ route('wishlist') }}" 
                    class="min-h-[44px] flex items-center border-l pl-4 py-2 text-xs uppercase tracking-[0.2em] transition-colors {{ $isWishlist ? 'border-forest bg-sage-light/40 text-forest font-semibold' : 'border-[#ECEEE6] text-muted hover:text-ink hover:border-sage' }}"
                    @if($isWishlist) aria-current="page" @endif
                >
                    Wishlist
                </a>

                <a 
                    href="{{ route('account.profile') }}" 
                    class="min-h-[44px] flex items-center border-l pl-4 py-2 text-xs uppercase tracking-[0.2em] transition-colors {{ $isProfile ? 'border-forest bg-sage-light/40 text-forest font-semibold' : 'border-[#ECEEE6] text-muted hover:text-ink hover:border-sage' }}"
                    @if($isProfile) aria-current="page" @endif
                >
                    Profile
                </a>

                <!-- Sign out link -->
                <div class="pt-6 mt-6 border-t border-[#ECEEE6]">
                    <a 
                        href="{{ route('login') }}" 
                        class="min-h-[44px] flex items-center pl-4 py-2 text-[13px] text-muted hover:text-forest transition-colors font-medium underline underline-offset-4"
                    >
                        Sign Out
                    </a>
                </div>
            </nav>
        </aside>

        <!-- Account Subcontent Container (div instead of nested main) -->
        <div class="flex-1 min-w-0" id="account-subcontent">
            @yield('account_content')
        </div>
    </div>
</div>
@endsection
