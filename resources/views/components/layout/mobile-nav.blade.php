<div x-data="{
        cartCount: 0,
        loadCartCount() {
            try {
                let localCart = localStorage.getItem('cart');
                let parsed = localCart ? JSON.parse(localCart) : [];
                if (Array.isArray(parsed)) {
                    this.cartCount = parsed.reduce((total, item) => total + parseInt(item.quantity || 0), 0);
                } else {
                    this.cartCount = 0;
                }
            } catch(e) {
                this.cartCount = 0;
            }
        }
    }"
    x-init="
        loadCartCount();
        window.addEventListener('cart-updated', () => loadCartCount());
        window.addEventListener('cart-updated-internal', () => loadCartCount());
        window.addEventListener('storage', () => loadCartCount());
    "
    class="fixed bottom-0 left-0 right-0 bg-white border-t border-gray-200 flex items-center justify-around py-2.5 px-3 z-[999] md:hidden shadow-[0_-2px_15px_rgba(0,0,0,0.1)]">
    
    <a href="tel:{{ $site_setting->site_phone ?? '+8801700000000' }}" class="flex flex-col items-center gap-1 text-slate-700 hover:text-primary transition-colors">
        <svg class="w-5 h-5" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
            <path stroke-linecap="round" stroke-linejoin="round" d="M3 5a2 2 0 012-2h3.28a1 1 0 01.948.684l1.498 4.493a1 1 0 01-.502 1.21l-2.257 1.13a11.042 11.042 0 005.516 5.516l1.13-2.257a1 1 0 011.21-.502l4.493 1.498a1 1 0 01.684.949V19a2 2 0 01-2 2h-1C9.716 21 3 14.284 3 6V5z"></path>
        </svg>
        <span class="text-[10px] font-extrabold uppercase tracking-wide">Call</span>
    </a>
    
    @php
        $whatsappNumber = preg_replace('/[^0-9]/', '', $site_setting->site_whatsapp ?? $site_setting->site_phone ?? '8801700000000');
    @endphp
    <a href="https://wa.me/{{ $whatsappNumber }}" target="_blank" class="flex flex-col items-center gap-1 text-emerald-600 hover:text-emerald-700 transition-colors">
        <svg class="w-5 h-5" fill="currentColor" viewBox="0 0 24 24">
            <path d="M17.472 14.382c-.297-.149-1.758-.867-2.03-.967-.273-.099-.471-.148-.67.15-.197.297-.767.966-.94 1.164-.173.199-.347.223-.644.075-.297-.15-1.255-.463-2.39-1.475-.883-.788-1.48-1.761-1.653-2.059-.173-.297-.018-.458.13-.606.134-.133.298-.347.446-.52.149-.174.198-.298.298-.497.099-.198.05-.371-.025-.52-.075-.149-.669-1.612-.916-2.207-.242-.579-.487-.5-.669-.51-.173-.008-.371-.01-.57-.01-.198 0-.52.074-.792.372-.272.297-1.04 1.016-1.04 2.479 0 1.462 1.065 2.875 1.213 3.074.149.198 2.096 3.2 5.077 4.487.709.306 1.262.489 1.694.625.712.227 1.36.195 1.871.118.571-.085 1.758-.719 2.006-1.413.248-.694.248-1.289.173-1.413-.074-.124-.272-.198-.57-.347m-5.421 7.403h-.004a9.87 9.87 0 01-5.031-1.378l-.361-.214-3.741.982.998-3.648-.235-.374a9.86 9.86 0 01-1.51-5.26c.001-5.45 4.436-9.884 9.888-9.884 2.64 0 5.122 1.03 6.988 2.898a9.825 9.825 0 012.893 6.994c-.003 5.45-4.437 9.884-9.885 9.884m8.413-18.297A11.815 11.815 0 0012.05 0C5.495 0 .16 5.335.157 11.892c0 2.096.547 4.142 1.588 5.945L.057 24l6.305-1.654a11.882 11.882 0 005.683 1.448h.005c6.554 0 11.89-5.335 11.893-11.893a11.821 11.821 0 00-3.48-8.413z"></path>
        </svg>
        <span class="text-[10px] font-extrabold uppercase tracking-wide">WhatsApp</span>
    </a>

    <a href="{{ route('home') }}" class="flex flex-col items-center gap-1 {{ request()->routeIs('home') ? 'text-primary' : 'text-slate-700' }} hover:text-primary transition-colors">
        <svg class="w-5 h-5" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
            <path stroke-linecap="round" stroke-linejoin="round" d="M3 12l2-2m0 0l7-7 7 7M5 10v10a1 1 0 001 1h3m10-11l2 2m0 0l-7-7-7 7M19 10v10a1 1 0 01-1 1h-3m-6 0a1 1 0 001-1v-4a1 1 0 011-1h2a1 1 0 011 1v4a1 1 0 001 1m-6 0h6"></path>
        </svg>
        <span class="text-[10px] font-extrabold uppercase tracking-wide">Home</span>
    </a>

    <a href="{{ route('shop') }}" class="flex flex-col items-center gap-1 {{ request()->routeIs('shop') ? 'text-primary' : 'text-slate-700' }} hover:text-primary transition-colors">
        <svg class="w-5 h-5" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
            <path stroke-linecap="round" stroke-linejoin="round" d="M16 11V7a4 4 0 00-8 0v4M5 9h14l1 12H4L5 9z"></path>
        </svg>
        <span class="text-[10px] font-extrabold uppercase tracking-wide">Shop</span>
    </a>

    <button @click="$store.cartDrawer ? $store.cartDrawer.open() : window.dispatchEvent(new CustomEvent('open-cart-drawer'))" class="flex flex-col items-center gap-1 {{ request()->routeIs('cart') ? 'text-primary' : 'text-slate-700' }} hover:text-primary transition-colors relative">
        <svg class="w-5 h-5" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
            <path stroke-linecap="round" stroke-linejoin="round" d="M3 3h2l.4 2M7 13h10l4-8H5.4M7 13L5.4 5M7 13l-2.293 2.293c-.63.63-.184 1.707.707 1.707H17m0 0a2 2 0 100 4 2 2 0 000-4zm-8 2a2 2 0 11-4 0 2 2 0 000-4z"></path>
        </svg>
        <span class="text-[10px] font-extrabold uppercase tracking-wide">Cart</span>
        <span x-show="cartCount > 0" x-text="cartCount" x-cloak class="absolute -top-1.5 -right-1.5 bg-accent text-white text-[9px] font-bold w-4.5 h-4.5 rounded-full flex items-center justify-center border border-white shadow-xs"></span>
    </button>
</div>
