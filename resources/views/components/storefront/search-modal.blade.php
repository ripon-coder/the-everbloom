@php
    $allProducts = config('demo.products', []);
    $currencySymbol = config('demo.site.currency.symbol', '$');
@endphp

<!-- JSON Data payload for search -->
<script id="everbloom-search-data" type="application/json">
{!! json_encode($allProducts, JSON_UNESCAPED_SLASHES | JSON_UNESCAPED_UNICODE) !!}
</script>

<div 
    x-data="searchModal"
    @open-search.window="openModal()"
    @keydown.window.ctrl.k.prevent="openModal()"
    @keydown.window.meta.k.prevent="openModal()"
    @keydown.escape.window="if(open) closeModal()"
    class="relative z-50"
    x-show="open"
    x-cloak
    role="dialog"
    aria-modal="true"
    aria-label="Search Formulations"
>
    <!-- Backdrop Overlay -->
    <div 
        x-show="open"
        x-transition:enter="transition-opacity ease-linear duration-200"
        x-transition:enter-start="opacity-0"
        x-transition:enter-end="opacity-100"
        x-transition:leave="transition-opacity ease-linear duration-200"
        x-transition:leave-start="opacity-100"
        x-transition:leave-end="opacity-0"
        @click="closeModal()"
        class="fixed inset-0 bg-ink/50 backdrop-blur-xs"
        aria-hidden="true"
    ></div>

    <!-- Modal Box -->
    <div class="fixed inset-0 z-10 overflow-y-auto p-4 sm:p-6 md:p-20">
        <div 
            x-show="open"
            x-trap.noscroll="open"
            x-transition:enter="transition ease-out duration-300"
            x-transition:enter-start="opacity-0 scale-95"
            x-transition:enter-end="opacity-100 scale-100"
            x-transition:leave="transition ease-in duration-200"
            x-transition:leave-start="opacity-100 scale-100"
            x-transition:leave-end="opacity-0 scale-95"
            @click.outside="closeModal()"
            class="mx-auto max-w-2xl transform overflow-hidden bg-ivory border border-beige shadow-2xl transition-all"
        >
            <!-- Search Bar Input Header -->
            <div class="relative border-b border-beige flex items-center px-4 sm:px-6">
                <svg class="w-5 h-5 text-forest/70 shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24" aria-hidden="true">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z" />
                </svg>

                <input 
                    type="text" 
                    id="storefront-search-input"
                    x-model="query"
                    placeholder="Search by botanical name, ingredient, or skin goal..."
                    class="h-16 w-full border-0 bg-transparent px-4 text-ink placeholder:text-muted focus:ring-0 focus:outline-none sm:text-base font-serif"
                    aria-label="Search"
                />

                <button 
                    type="button" 
                    @click="closeModal()"
                    class="text-xs uppercase tracking-widest text-muted hover:text-ink px-2 py-1 cursor-pointer"
                >
                    Esc
                </button>
            </div>

            <!-- Results or Suggestions -->
            <div class="p-4 sm:p-6 max-h-96 overflow-y-auto">
                <!-- If query is empty: popular categories -->
                <div x-show="!query.trim()" class="space-y-4">
                    <p class="text-[10px] uppercase tracking-[0.25em] text-muted font-medium">Popular Botanical Categories</p>
                    <div class="flex flex-wrap gap-2">
                        <a href="/shop/category/serums" @click="closeModal()" class="px-3 py-1.5 bg-sage-light/60 hover:bg-forest hover:text-ivory text-xs text-ink transition-colors">
                            Concentrated Serums
                        </a>
                        <a href="/shop/category/moisturizers" @click="closeModal()" class="px-3 py-1.5 bg-sage-light/60 hover:bg-forest hover:text-ivory text-xs text-ink transition-colors">
                            Nourishing Moisturizers
                        </a>
                        <a href="/shop/category/cleansers" @click="closeModal()" class="px-3 py-1.5 bg-sage-light/60 hover:bg-forest hover:text-ivory text-xs text-ink transition-colors">
                            Balms & Cleansers
                        </a>
                        <a href="/shop/category/sunscreens" @click="closeModal()" class="px-3 py-1.5 bg-sage-light/60 hover:bg-forest hover:text-ivory text-xs text-ink transition-colors">
                            Mineral Sun Care
                        </a>
                    </div>
                </div>

                <!-- If query has matching results -->
                <div x-show="query.trim() && results.length > 0" class="space-y-3">
                    <p class="text-[10px] uppercase tracking-[0.25em] text-muted font-medium">
                        Botanical Formulations (<span x-text="results.length"></span>)
                    </p>
                    <div class="divide-y divide-beige/50">
                        <template x-for="item in results" :key="item.id">
                            <a 
                                :href="'/shop/product/' + item.slug" 
                                @click="closeModal()"
                                class="flex items-center gap-4 py-3 hover:bg-sage-light/30 px-2 -mx-2 transition-colors group"
                            >
                                <div class="w-12 h-14 bg-sage-light/50 shrink-0 border border-beige/40 overflow-hidden">
                                    <template x-if="item.images && item.images.length > 0">
                                        <img :src="'/images/' + item.images[0]" :alt="item.name" class="w-full h-full object-cover">
                                    </template>
                                </div>
                                <div class="flex-1 min-w-0">
                                    <span class="text-[10px] uppercase tracking-widest text-muted block" x-text="item.category"></span>
                                    <p class="font-serif text-sm text-ink group-hover:text-forest transition-colors truncate" x-text="item.name"></p>
                                    <p class="text-xs text-muted truncate mt-0.5" x-text="item.short_description"></p>
                                </div>
                                <div class="shrink-0 text-right">
                                    <span class="text-xs font-semibold text-ink" x-text="'{{ $currencySymbol }}' + parseFloat(item.price).toFixed(2)"></span>
                                </div>
                            </a>
                        </template>
                    </div>
                </div>

                <!-- If query has no matches -->
                <div x-show="query.trim() && results.length === 0" class="py-8 text-center text-muted">
                    <p class="font-serif text-base text-ink">No botanical formulas found for "<span x-text="query"></span>"</p>
                    <p class="text-xs mt-1">Try searching for keywords like "rosehip", "serum", "cleanse", or "sunscreen".</p>
                </div>
            </div>

            <!-- Footer note -->
            <div class="px-6 py-3 bg-sage-light/40 border-t border-beige text-[11px] text-muted flex items-center justify-between">
                <span>Press <kbd class="px-1.5 py-0.5 bg-ivory border border-beige text-[10px] rounded font-mono">ESC</kbd> to exit</span>
                <a href="/shop" @click="closeModal()" class="hover:text-forest underline underline-offset-2">Browse All Products →</a>
            </div>
        </div>
    </div>
</div>
