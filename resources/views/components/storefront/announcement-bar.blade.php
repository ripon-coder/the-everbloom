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
        class="bg-forest text-ivory text-xs tracking-wider uppercase font-medium relative z-40 border-b border-forest-600/40"
    >
        <div class="site-container flex items-center justify-between min-h-[44px] gap-2">
            <!-- Left spacer on desktop to keep announcement text centered -->
            <div class="hidden sm:block w-11 shrink-0" aria-hidden="true"></div>

            <!-- Single-line container preventing wrapping or truncation at 320px -->
            <div class="flex-1 flex items-center justify-center gap-1.5 text-xs whitespace-nowrap">
                <span class="md:hidden">Free shipping over $75</span>
                <span class="hidden md:inline">{{ $message }}</span>
                @if($link && $linkText)
                    <a href="{{ $link }}" class="underline underline-offset-4 font-semibold hover:text-beige transition-colors shrink-0 ml-1">
                        {{ $linkText }}
                    </a>
                @endif
            </div>

            <!-- Dismiss Button (44x44px touch target, never overlaps text at 320px) -->
            <button 
                type="button" 
                @click="showAnnouncement = false" 
                class="w-11 h-11 -mr-2 shrink-0 inline-flex items-center justify-center text-ivory/80 hover:text-ivory transition-colors cursor-pointer"
                aria-label="Dismiss announcement"
            >
                <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24" aria-hidden="true">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12" />
                </svg>
            </button>
        </div>
    </div>
@endif
