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
        class="fixed inset-0 bg-ink/50"
        aria-hidden="true"
    ></div>

    <!-- Modal Box -->
    <div class="fixed inset-0 z-10 overflow-y-auto p-4 sm:p-6 md:p-20">
        <div 
            x-show="open"
            x-trap.noscroll="open"
            x-transition:enter="transition ease-out duration-200"
            x-transition:enter-start="opacity-0 translate-y-2"
            x-transition:enter-end="opacity-100 translate-y-0"
            x-transition:leave="transition ease-in duration-150"
            x-transition:leave-start="opacity-100 translate-y-0"
            x-transition:leave-end="opacity-0 translate-y-2"
            @click.outside="closeModal()"
            class="mx-auto max-w-2xl transform overflow-hidden bg-ivory border border-[#E3E6DD] shadow-drawer transition-all"
        >
            <!-- Search Bar Input Header -->
            <div class="relative border-b border-[#ECEEE6] flex items-center px-4 sm:px-6">
                <svg class="w-5 h-5 text-forest shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24" aria-hidden="true">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.75" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z" />
                </svg>

                <label for="storefront-search-input" class="sr-only">Search formulations</label>
                <input 
                    type="search" 
                    id="storefront-search-input"
                    x-model="query"
                    placeholder="Search by product name, ingredient, or skin goal..."
                    class="h-16 w-full border-0 bg-transparent px-4 text-ink placeholder:text-muted-light focus:outline-none text-base font-sans"
                />

                <button 
                    type="button" 
                    @click="closeModal()"
                    class="min-w-[44px] min-h-[44px] inline-flex items-center justify-center text-xs uppercase tracking-[0.2em] text-muted hover:text-ink px-2 cursor-pointer"
                    aria-label="Close search modal"
                >
                    Esc
                </button>
            </div>

            <!-- Results or Suggestions -->
            <div class="p-4 sm:p-6 max-h-96 overflow-y-auto">
                <!-- If query is empty: popular categories -->
                <div x-show="!query.trim()" class="space-y-4">
                    <p class="text-xs uppercase tracking-[0.2em] text-muted font-medium">Popular Botanical Categories</p>
                    <div class="flex flex-wrap gap-2">
                        <a href="/shop/category/serums" @click="closeModal()" class="min-h-[44px] inline-flex items-center px-3.5 py-2 bg-sage-light/60 hover:bg-forest hover:text-ivory text-xs text-ink transition-colors rounded-sm">
                            Serums
                        </a>
                        <a href="/shop/category/moisturizers" @click="closeModal()" class="min-h-[44px] inline-flex items-center px-3.5 py-2 bg-sage-light/60 hover:bg-forest hover:text-ivory text-xs text-ink transition-colors rounded-sm">
                            Moisturizers
                        </a>
                        <a href="/shop/category/cleansers" @click="closeModal()" class="min-h-[44px] inline-flex items-center px-3.5 py-2 bg-sage-light/60 hover:bg-forest hover:text-ivory text-xs text-ink transition-colors rounded-sm">
                            Cleansers
                        </a>
                        <a href="/shop/category/sunscreens" @click="closeModal()" class="min-h-[44px] inline-flex items-center px-3.5 py-2 bg-sage-light/60 hover:bg-forest hover:text-ivory text-xs text-ink transition-colors rounded-sm">
                            Sunscreens
                        </a>
                    </div>
                </div>

                <!-- If query has matching results -->
                <div x-show="query.trim() && results.length > 0" class="space-y-3" aria-live="polite">
                    <p class="text-xs uppercase tracking-[0.2em] text-muted font-medium">
                        Botanical Formulations (<span class="price" x-text="results.length"></span>)
                    </p>
                    <div class="divide-y divide-[#ECEEE6]">
                        <template x-for="item in results" :key="item.id">
                            <a 
                                :href="'/shop/product/' + item.slug" 
                                @click="closeModal()"
                                class="flex items-center gap-4 py-3 hover:bg-sage-light/30 px-2 -mx-2 transition-colors group"
                            >
                                <div class="w-14 h-14 shrink-0 border border-[#E3E6DD] overflow-hidden">
                                    <x-storefront.image 
                                        ::src="item.images && item.images.length > 0 ? '/images/' + item.images[0] : ''" 
                                        ::alt="item.name" 
                                        aspect-ratio="aspect-square" 
                                        class="w-14 h-14 object-cover" 
                                    />
                                </div>
                                <div class="flex-1 min-w-0">
                                    <span class="text-xs uppercase tracking-[0.2em] text-muted block" x-text="item.category"></span>
                                    <p class="font-serif text-sm text-ink group-hover:text-forest transition-colors truncate" x-text="item.name"></p>
                                    <p class="text-xs text-muted truncate mt-0.5" x-text="item.short_description"></p>
                                </div>
                                <div class="shrink-0 text-right">
                                    <span class="price text-sm font-medium text-ink" x-text="'{{ $currencySymbol }}' + parseFloat(item.price).toFixed(2)"></span>
                                </div>
                            </a>
                        </template>
                    </div>
                </div>

                <!-- If query has no matches -->
                <div x-show="query.trim() && results.length === 0" class="py-8 text-center text-muted" aria-live="polite">
                    <p class="font-serif text-base text-ink">No botanical formulas found for "<span x-text="query"></span>"</p>
                    <p class="text-xs mt-1">Try searching for keywords like "rosehip", "serum", "cleanse", or "sunscreen".</p>
                </div>
            </div>

            <!-- Footer note -->
            <div class="px-6 py-3 bg-sage-light/40 border-t border-[#ECEEE6] text-xs text-muted flex items-center justify-between">
                <span>Press <kbd class="px-1.5 py-0.5 bg-ivory border border-[#E3E6DD] text-xs rounded-sm font-mono">ESC</kbd> to exit</span>
                <a href="/shop" @click="closeModal()" class="text-[13px] text-forest hover:text-ink underline underline-offset-4">Browse All Products →</a>
            </div>
        </div>
    </div>
</div>
