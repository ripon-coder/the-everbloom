@php
    $isHome = request()->routeIs('home');
    $isShop = request()->routeIs('shop.*');
    $isWishlist = request()->routeIs('wishlist');
    $isCart = request()->routeIs('cart.*');
@endphp

<nav 
    x-data="{ inputFocused: false }"
    @focusin.window="inputFocused = ['INPUT', 'TEXTAREA', 'SELECT'].includes($event.target?.tagName) || Boolean($event.target?.isContentEditable)"
    @focusout.window="setTimeout(() => { if (!['INPUT', 'TEXTAREA', 'SELECT'].includes(document.activeElement?.tagName) && !document.activeElement?.isContentEditable) inputFocused = false; }, 50)"
    x-show="!inputFocused"
    aria-label="Primary"
    class="fixed inset-x-0 bottom-0 z-30 grid grid-cols-5 lg:hidden bg-[#F8F6F0] border-t border-[#E3E6DD]"
    style="padding-bottom: env(safe-area-inset-bottom, 0px);"
>
    <!-- 1. Home -->
    <a 
        href="{{ route('home') }}" 
        class="w-full h-14 flex flex-col items-center justify-center gap-1 relative transition-colors focus-visible:outline-none focus-visible:ring-2 focus-visible:ring-[#304C3D] focus-visible:ring-inset {{ $isHome ? 'text-[#304C3D]' : 'text-[#777D74] hover:text-[#304C3D]' }}"
        @if($isHome) aria-current="page" @endif
    >
        @if($isHome)
            <span class="absolute top-0 left-1/2 -translate-x-1/2 w-8 h-[2px] bg-[#304C3D]" aria-hidden="true"></span>
        @endif
        <svg class="w-[22px] h-[22px] shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24" stroke-width="1.75" stroke-linecap="round" stroke-linejoin="round" aria-hidden="true">
            <path d="m3 9 9-7 9 7v11a2 2 0 0 1-2 2H5a2 2 0 0 1-2-2z" />
            <polyline points="9 22 9 12 15 12 15 22" />
        </svg>
        <span class="text-[10px] uppercase tracking-wider font-medium leading-none whitespace-nowrap">Home</span>
    </a>

    <!-- 2. Shop -->
    <a 
        href="{{ route('shop.index') }}" 
        class="w-full h-14 flex flex-col items-center justify-center gap-1 relative transition-colors focus-visible:outline-none focus-visible:ring-2 focus-visible:ring-[#304C3D] focus-visible:ring-inset {{ $isShop ? 'text-[#304C3D]' : 'text-[#777D74] hover:text-[#304C3D]' }}"
        @if($isShop) aria-current="page" @endif
    >
        @if($isShop)
            <span class="absolute top-0 left-1/2 -translate-x-1/2 w-8 h-[2px] bg-[#304C3D]" aria-hidden="true"></span>
        @endif
        <svg class="w-[22px] h-[22px] shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24" stroke-width="1.75" stroke-linecap="round" stroke-linejoin="round" aria-hidden="true">
            <path d="m2 7 4.41-4.41A2 2 0 0 1 7.83 2h8.34a2 2 0 0 1 1.42.59L22 7" />
            <path d="M4 7v11a2 2 0 0 0 2 2h12a2 2 0 0 0 2-2V7" />
            <path d="M2 7h20" />
            <path d="M10 12a2 2 0 1 1-4 0" />
            <path d="M18 12a2 2 0 1 1-4 0" />
        </svg>
        <span class="text-[10px] uppercase tracking-wider font-medium leading-none whitespace-nowrap">Shop</span>
    </a>

    <!-- 3. Search -->
    <button 
        type="button" 
        @click="$dispatch('open-search')"
        class="w-full h-14 flex flex-col items-center justify-center gap-1 relative text-[#777D74] hover:text-[#304C3D] transition-colors focus-visible:outline-none focus-visible:ring-2 focus-visible:ring-[#304C3D] focus-visible:ring-inset cursor-pointer"
        aria-label="Open search modal"
    >
        <svg class="w-[22px] h-[22px] shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24" stroke-width="1.75" stroke-linecap="round" stroke-linejoin="round" aria-hidden="true">
            <circle cx="11" cy="11" r="8" />
            <path d="m21 21-4.3-4.3" />
        </svg>
        <span class="text-[10px] uppercase tracking-wider font-medium leading-none whitespace-nowrap">Search</span>
    </button>

    <!-- 4. Wishlist -->
    <a 
        href="{{ route('wishlist') }}" 
        class="w-full h-14 flex flex-col items-center justify-center gap-1 relative transition-colors focus-visible:outline-none focus-visible:ring-2 focus-visible:ring-[#304C3D] focus-visible:ring-inset {{ $isWishlist ? 'text-[#304C3D]' : 'text-[#777D74] hover:text-[#304C3D]' }}"
        @if($isWishlist) aria-current="page" @endif
    >
        @if($isWishlist)
            <span class="absolute top-0 left-1/2 -translate-x-1/2 w-8 h-[2px] bg-[#304C3D]" aria-hidden="true"></span>
        @endif
        <div class="relative shrink-0 flex items-center justify-center">
            <svg class="w-[22px] h-[22px]" fill="none" stroke="currentColor" viewBox="0 0 24 24" stroke-width="1.75" stroke-linecap="round" stroke-linejoin="round" aria-hidden="true">
                <path d="M19 14c1.49-1.46 3-3.21 3-5.5A5.5 5.5 0 0 0 16.5 3c-1.76 0-3 .5-4.5 2-1.5-1.5-2.74-2-4.5-2A5.5 5.5 0 0 0 2 8.5c0 2.3 1.5 4.05 3 5.5l7 7Z" />
            </svg>
            <span 
                x-show="$store.wishlist && $store.wishlist.count > 0" 
                x-text="$store.wishlist.count" 
                x-cloak
                class="absolute -top-1 -right-2 min-w-[14px] h-3.5 px-0.5 bg-[#304C3D] text-[#F8F6F0] text-[9px] font-sans font-medium flex items-center justify-center rounded-full leading-none pointer-events-none"
            ></span>
        </div>
        <span class="text-[10px] uppercase tracking-wider font-medium leading-none whitespace-nowrap">Wishlist</span>
    </a>

    <!-- 5. Bag -->
    <button 
        type="button" 
        @click="if (window.Alpine && $store.cart && typeof $store.cart.openDrawer === 'function') { $store.cart.openDrawer(); } else { window.location.href = '{{ route('cart.index') }}'; }"
        class="w-full h-14 flex flex-col items-center justify-center gap-1 relative transition-colors focus-visible:outline-none focus-visible:ring-2 focus-visible:ring-[#304C3D] focus-visible:ring-inset cursor-pointer {{ $isCart ? 'text-[#304C3D]' : 'text-[#777D74] hover:text-[#304C3D]' }}"
        aria-label="Open shopping bag"
        @if($isCart) aria-current="page" @endif
    >
        @if($isCart)
            <span class="absolute top-0 left-1/2 -translate-x-1/2 w-8 h-[2px] bg-[#304C3D]" aria-hidden="true"></span>
        @endif
        <div class="relative shrink-0 flex items-center justify-center">
            <svg class="w-[22px] h-[22px]" fill="none" stroke="currentColor" viewBox="0 0 24 24" stroke-width="1.75" stroke-linecap="round" stroke-linejoin="round" aria-hidden="true">
                <path d="M6 2 3 6v14a2 2 0 0 0 2 2h14a2 2 0 0 0 2-2V6l-3-4Z" />
                <path d="M3 6h18" />
                <path d="M16 10a4 4 0 0 1-8 0" />
            </svg>
            <span 
                x-show="$store.cart && $store.cart.count > 0" 
                x-text="$store.cart.count" 
                x-cloak
                class="absolute -top-1 -right-2 min-w-[14px] h-3.5 px-0.5 bg-[#304C3D] text-[#F8F6F0] text-[9px] font-sans font-medium flex items-center justify-center rounded-full leading-none pointer-events-none"
            ></span>
        </div>
        <span class="text-[10px] uppercase tracking-wider font-medium leading-none whitespace-nowrap">Bag</span>
    </button>
</nav>
