@props([
    'message' => config('demo.site.announcement.message', 'Free shipping on orders over $' . (int)config('demo.site.shipping_rates.standard.free_threshold', 75)),
    'link' => config('demo.site.announcement.link', '/shop'),
    'linkText' => config('demo.site.announcement.link_text', 'Shop Now'),
])

@if(config('demo.site.announcement.enabled', true))
    <!-- VERIFY CLAIM before launch -->
    <div 
        x-data="{ showAnnouncement: true }" 
        x-show="showAnnouncement" 
        x-transition:leave="transition ease-in duration-200"
        x-transition:leave-start="opacity-100 max-h-12"
        x-transition:leave-end="opacity-0 max-h-0"
        class="bg-forest text-ivory text-[11px] sm:text-xs tracking-wider uppercase font-medium py-2 px-8 relative z-40 border-b border-forest-600/40"
    >
        <div class="site-container flex items-center justify-center text-center relative">
            <!-- Single-line container preventing wrapping on mobile -->
            <div class="flex items-center justify-center gap-1.5 text-[11px] sm:text-xs max-w-full">
                <span class="md:hidden whitespace-nowrap">Free shipping over $75</span>
                <span class="hidden md:inline whitespace-nowrap">{{ $message }}</span>
                @if($link && $linkText)
                    <span class="whitespace-nowrap ml-1 shrink-0">
                        <a href="{{ $link }}" class="underline underline-offset-4 font-semibold hover:text-beige transition-colors">
                            {{ $linkText }}
                        </a>
                    </span>
                @endif
            </div>

            <!-- Dismiss Button -->
            <button 
                type="button" 
                @click="showAnnouncement = false" 
                class="absolute right-0 top-1/2 -translate-y-1/2 text-ivory/70 hover:text-ivory p-1 focus-visible:outline-none focus-visible:ring-1 focus-visible:ring-ivory cursor-pointer"
                aria-label="Dismiss announcement"
            >
                <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24" aria-hidden="true">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12" />
                </svg>
            </button>
        </div>
    </div>
@endif