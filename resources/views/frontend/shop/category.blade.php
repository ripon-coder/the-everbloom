@extends('layouts.app')

@section('title', ($category['name'] ?? 'Category') . ' — The Everbloom')
@section('meta_description', $category['description'] ?? 'Botanical skincare formulations.')

@section('content')
<div 
    x-data="{
        filterDrawerOpen: false,
        sortSheetOpen: false,
        sortDropdownOpen: false,
        sortBy: 'featured',
        sortLabel: 'Featured Formulations',
        setSort(val, label) {
            this.sortBy = val;
            this.sortLabel = label;
            this.sortSheetOpen = false;
            this.sortDropdownOpen = false;
        }
    }"
    @keydown.escape.window="filterDrawerOpen = false; sortSheetOpen = false; sortDropdownOpen = false"
    class="pb-16 md:pb-24"
>
    <!-- Breadcrumbs & Category Header -->
    <div class="site-container pt-8 md:pt-10 pb-6 border-b border-beige/60">
        <x-storefront.breadcrumbs :items="[
            ['label' => 'Shop All', 'url' => '/shop'],
            ['label' => $category['name'] ?? 'Category', 'url' => '']
        ]" />

        <div class="mt-4 flex flex-col md:flex-row md:items-end justify-between gap-4">
            <div>
                <span class="text-xs uppercase tracking-[0.2em] font-medium text-forest block mb-1">Botanical Collection</span>
                <h1 class="font-serif text-3xl sm:text-4xl text-ink font-normal">{{ $category['name'] ?? 'Category' }}</h1>
                <!-- VERIFY CLAIM before launch -->
                <p class="text-sm text-muted mt-2 max-w-xl">
                    {{ $category['description'] ?? 'Thoughtfully formulated botanical skincare for your daily ritual.' }}
                </p>
            </div>
            <p class="hidden lg:block text-xs uppercase tracking-widest text-muted">
                Showing {{ count($products ?? []) }} Formulations
            </p>
        </div>
    </div>

    <!-- Mobile Sticky Toolbar Under Header (lg:hidden) -->
    <div class="sticky top-16 z-30 lg:hidden bg-ivory/95 backdrop-blur-md border-b border-beige/80 px-4 py-2.5 flex items-center justify-between">
        <!-- Left: Product Count -->
        <span class="text-xs uppercase tracking-[0.16em] font-medium text-muted">
            {{ count($products ?? []) }} Formulations
        </span>

        <!-- Right: Sort & Filter Buttons -->
        <div class="flex items-center gap-2">
            <!-- Sort Trigger -->
            <button 
                type="button" 
                @click="sortSheetOpen = true"
                class="inline-flex items-center gap-1.5 px-3 py-1.5 bg-ivory border border-beige/90 text-xs uppercase tracking-wider text-ink hover:text-forest hover:border-forest transition-colors rounded-sm cursor-pointer"
                aria-label="Sort formulations"
            >
                <svg class="w-3.5 h-3.5 text-forest" fill="none" stroke="currentColor" viewBox="0 0 24 24" aria-hidden="true">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.75" d="M3 4h18M6 10h12M9 16h6" />
                </svg>
                <span x-text="sortBy === 'featured' ? 'Sort' : 'Sorted'">Sort</span>
            </button>

            <!-- Filter Trigger -->
            <button 
                type="button" 
                @click="filterDrawerOpen = true"
                class="inline-flex items-center gap-1.5 px-3 py-1.5 bg-forest text-ivory text-xs uppercase tracking-wider hover:bg-forest-800 transition-colors rounded-sm cursor-pointer"
                aria-label="Filter formulations"
            >
                <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24" aria-hidden="true">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.75" d="M12 6V4m0 2a2 2 0 100 4m0-4a2 2 0 110 4m-6 8a2 2 0 100-4m0 4a2 2 0 110-4m0 4v2m0-6V4m6 6v10m6-2a2 2 0 100-4m0 4a2 2 0 110-4m0 4v2m0-6V4" />
                </svg>
                <span>Filter</span>
            </button>
        </div>
    </div>

    <!-- Mobile Filter Bottom Drawer with Focus Trap & Esc -->
    <div 
        x-show="filterDrawerOpen" 
        x-cloak
        class="relative z-50 lg:hidden"
        role="dialog"
        aria-modal="true"
        aria-label="Filter formulations"
    >
        <!-- Backdrop -->
        <div 
            x-show="filterDrawerOpen"
            x-transition:enter="transition-opacity ease-linear duration-300"
            x-transition:enter-start="opacity-0"
            x-transition:enter-end="opacity-100"
            x-transition:leave="transition-opacity ease-linear duration-300"
            x-transition:leave-start="opacity-100"
            x-transition:leave-end="opacity-0"
            @click="filterDrawerOpen = false" 
            class="fixed inset-0 bg-ink/60 backdrop-blur-xs"
            aria-hidden="true"
        ></div>

        <!-- Sliding Bottom Sheet Panel -->
        <div 
            x-show="filterDrawerOpen" 
            x-trap.noscroll="filterDrawerOpen"
            x-transition:enter="transition ease-out duration-300 transform"
            x-transition:enter-start="translate-y-full"
            x-transition:enter-end="translate-y-0"
            x-transition:leave="transition ease-in duration-200 transform"
            x-transition:leave-start="translate-y-0"
            x-transition:leave-end="translate-y-full"
            class="fixed inset-x-0 bottom-0 max-h-[85vh] bg-ivory rounded-t-2xl shadow-drawer flex flex-col justify-between overflow-hidden"
        >
            <!-- Drawer Header -->
            <div class="px-5 py-4 border-b border-beige/80 flex items-center justify-between bg-ivory">
                <div>
                    <h2 class="font-serif text-lg text-ink font-medium">Filter {{ $category['name'] ?? 'Formulations' }}</h2>
                    <p class="text-[11px] text-muted uppercase tracking-wider">Refine by botanical category & skin goal</p>
                </div>
                <button 
                    type="button" 
                    @click="filterDrawerOpen = false" 
                    class="p-2 text-ink/70 hover:text-ink focus-visible:outline-none focus-visible:ring-1 focus-visible:ring-forest cursor-pointer rounded"
                    aria-label="Close filters"
                >
                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24" aria-hidden="true">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12" />
                    </svg>
                </button>
            </div>

            <!-- Drawer Body -->
            <div class="px-5 py-5 overflow-y-auto flex-1 space-y-6">
                <!-- Category Filter -->
                <div>
                    <h3 class="text-xs uppercase tracking-[0.2em] font-semibold text-ink mb-3">Botanical Category</h3>
                    <div class="flex flex-wrap gap-2">
                        <a 
                            href="/shop" 
                            class="px-3 py-1.5 text-xs rounded border transition-colors bg-ivory border-beige text-ink hover:border-forest"
                        >
                            All Formulations
                        </a>
                        @foreach(config('demo.categories') as $cat)
                            <a 
                                href="/shop/category/{{ $cat['slug'] }}" 
                                class="px-3 py-1.5 text-xs rounded border transition-colors {{ ($category['slug'] ?? '') === $cat['slug'] ? 'bg-forest text-ivory border-forest' : 'bg-ivory border-beige text-ink hover:border-forest' }}"
                            >
                                {{ $cat['name'] }}
                            </a>
                        @endforeach
                    </div>
                </div>

                <!-- Skin Goals -->
                <div>
                    <h3 class="text-xs uppercase tracking-[0.2em] font-semibold text-ink mb-3">Skin Ritual Goal</h3>
                    <div class="grid grid-cols-2 gap-2 text-xs">
                        <label class="flex items-center gap-2 p-2 rounded bg-sage-light/30 border border-beige/60 cursor-pointer">
                            <input type="checkbox" class="rounded text-forest focus:ring-forest">
                            <span>Deep Hydration</span>
                        </label>
                        <label class="flex items-center gap-2 p-2 rounded bg-sage-light/30 border border-beige/60 cursor-pointer">
                            <input type="checkbox" class="rounded text-forest focus:ring-forest">
                            <span>Luminosity & Glow</span>
                        </label>
                        <label class="flex items-center gap-2 p-2 rounded bg-sage-light/30 border border-beige/60 cursor-pointer">
                            <input type="checkbox" class="rounded text-forest focus:ring-forest">
                            <span>Barrier Renewal</span>
                        </label>
                        <label class="flex items-center gap-2 p-2 rounded bg-sage-light/30 border border-beige/60 cursor-pointer">
                            <input type="checkbox" class="rounded text-forest focus:ring-forest">
                            <span>Daily Shield</span>
                        </label>
                    </div>
                </div>

                <!-- Availability -->
                <div>
                    <h3 class="text-xs uppercase tracking-[0.2em] font-semibold text-ink mb-3">Availability</h3>
                    <div class="space-y-2 text-xs">
                        <label class="flex items-center gap-2 cursor-pointer">
                            <input type="checkbox" checked class="rounded text-forest focus:ring-forest">
                            <span>In Stock Only</span>
                        </label>
                        <label class="flex items-center gap-2 cursor-pointer">
                            <input type="checkbox" class="rounded text-forest focus:ring-forest">
                            <span>Special Offers & Sets</span>
                        </label>
                    </div>
                </div>
            </div>

            <!-- Drawer Footer -->
            <div class="p-4 bg-sage-light/40 border-t border-beige flex items-center justify-between gap-3">
                <a 
                    href="/shop/category/{{ $category['slug'] ?? 'face-care' }}" 
                    class="text-xs text-muted hover:text-forest underline underline-offset-4 py-2"
                >
                    Reset All
                </a>
                <button 
                    type="button" 
                    @click="filterDrawerOpen = false" 
                    class="btn-primary !py-2.5 !px-6 text-xs"
                >
                    View Results ({{ count($products ?? []) }})
                </button>
            </div>
        </div>
    </div>

    <!-- Mobile Sort Bottom Sheet -->
    <div 
        x-show="sortSheetOpen" 
        x-cloak
        class="relative z-50 lg:hidden"
        role="dialog"
        aria-modal="true"
        aria-label="Sort formulations"
    >
        <div 
            x-show="sortSheetOpen"
            x-transition:enter="transition-opacity ease-linear duration-200"
            x-transition:enter-start="opacity-0"
            x-transition:enter-end="opacity-100"
            x-transition:leave="transition-opacity ease-linear duration-200"
            x-transition:leave-start="opacity-100"
            x-transition:leave-end="opacity-0"
            @click="sortSheetOpen = false" 
            class="fixed inset-0 bg-ink/50 backdrop-blur-xs"
            aria-hidden="true"
        ></div>

        <div 
            x-show="sortSheetOpen" 
            x-trap.noscroll="sortSheetOpen"
            x-transition:enter="transition ease-out duration-250 transform"
            x-transition:enter-start="translate-y-full"
            x-transition:enter-end="translate-y-0"
            x-transition:leave="transition ease-in duration-200 transform"
            x-transition:leave-start="translate-y-0"
            x-transition:leave-end="translate-y-full"
            class="fixed inset-x-0 bottom-0 bg-ivory rounded-t-2xl shadow-drawer p-5 z-50"
        >
            <div class="flex items-center justify-between pb-3 border-b border-beige/80 mb-3">
                <h3 class="font-serif text-base text-ink font-medium">Sort By</h3>
                <button type="button" @click="sortSheetOpen = false" class="p-1 text-muted hover:text-ink cursor-pointer">
                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"/></svg>
                </button>
            </div>
            <div class="space-y-1 text-xs">
                <button 
                    type="button" 
                    @click="setSort('featured', 'Featured Formulations')" 
                    class="w-full text-left py-2.5 px-3 rounded flex items-center justify-between transition-colors cursor-pointer"
                    :class="sortBy === 'featured' ? 'bg-sage-light text-forest font-semibold' : 'text-ink hover:bg-beige/30'"
                >
                    <span>Featured Formulations</span>
                    <span x-show="sortBy === 'featured'" class="text-forest">✓</span>
                </button>
                <button 
                    type="button" 
                    @click="setSort('price-asc', 'Price: Low to High')" 
                    class="w-full text-left py-2.5 px-3 rounded flex items-center justify-between transition-colors cursor-pointer"
                    :class="sortBy === 'price-asc' ? 'bg-sage-light text-forest font-semibold' : 'text-ink hover:bg-beige/30'"
                >
                    <span>Price: Low to High</span>
                    <span x-show="sortBy === 'price-asc'" class="text-forest">✓</span>
                </button>
                <button 
                    type="button" 
                    @click="setSort('price-desc', 'Price: High to Low')" 
                    class="w-full text-left py-2.5 px-3 rounded flex items-center justify-between transition-colors cursor-pointer"
                    :class="sortBy === 'price-desc' ? 'bg-sage-light text-forest font-semibold' : 'text-ink hover:bg-beige/30'"
                >
                    <span>Price: High to Low</span>
                    <span x-show="sortBy === 'price-desc'" class="text-forest">✓</span>
                </button>
                <button 
                    type="button" 
                    @click="setSort('rating', 'Highest Rated')" 
                    class="w-full text-left py-2.5 px-3 rounded flex items-center justify-between transition-colors cursor-pointer"
                    :class="sortBy === 'rating' ? 'bg-sage-light text-forest font-semibold' : 'text-ink hover:bg-beige/30'"
                >
                    <span>Highest Rated</span>
                    <span x-show="sortBy === 'rating'" class="text-forest">✓</span>
                </button>
                <button 
                    type="button" 
                    @click="setSort('newest', 'Newest Rituals')" 
                    class="w-full text-left py-2.5 px-3 rounded flex items-center justify-between transition-colors cursor-pointer"
                    :class="sortBy === 'newest' ? 'bg-sage-light text-forest font-semibold' : 'text-ink hover:bg-beige/30'"
                >
                    <span>Newest Rituals</span>
                    <span x-show="sortBy === 'newest'" class="text-forest">✓</span>
                </button>
            </div>
        </div>
    </div>

    <!-- Main Content Container with Desktop Sidebar & Top Sort -->
    <div class="site-container pt-6 sm:pt-8">
        <div class="lg:grid lg:grid-cols-4 lg:gap-8">
            
            <!-- Desktop Sidebar (lg:block) -->
            <aside class="hidden lg:block lg:col-span-1 space-y-8 pr-6 border-r border-beige/60">
                <!-- Categories Navigation -->
                <div>
                    <h3 class="text-xs uppercase tracking-[0.2em] font-semibold text-ink mb-4 pb-2 border-b border-beige/80">
                        Botanical Collections
                    </h3>
                    <ul class="space-y-2 text-xs">
                        <li>
                            <a 
                                href="/shop" 
                                class="flex items-center justify-between py-1 text-muted hover:text-forest transition-colors"
                            >
                                <span>All Botanical Products</span>
                                <span class="text-muted/70">({{ count(config('demo.products')) }})</span>
                            </a>
                        </li>
                        @foreach(config('demo.categories') as $cat)
                            <li>
                                <a 
                                    href="/shop/category/{{ $cat['slug'] }}" 
                                    class="flex items-center justify-between py-1 transition-colors {{ ($category['slug'] ?? '') === $cat['slug'] ? 'font-semibold text-forest' : 'text-muted hover:text-forest' }}"
                                >
                                    <span>{{ $cat['name'] }}</span>
                                    <span class="text-muted/70">({{ $cat['item_count'] ?? '' }})</span>
                                </a>
                            </li>
                        @endforeach
                    </ul>
                </div>

                <!-- Skin Ritual Goals -->
                <div>
                    <h3 class="text-xs uppercase tracking-[0.2em] font-semibold text-ink mb-4 pb-2 border-b border-beige/80">
                        Skin Ritual Goal
                    </h3>
                    <div class="space-y-2 text-xs">
                        <label class="flex items-center gap-2 cursor-pointer hover:text-forest">
                            <input type="checkbox" class="rounded text-forest focus:ring-forest">
                            <span>Deep Hydration & Comfort</span>
                        </label>
                        <label class="flex items-center gap-2 cursor-pointer hover:text-forest">
                            <input type="checkbox" class="rounded text-forest focus:ring-forest">
                            <span>Radiance & Even Tone</span>
                        </label>
                        <label class="flex items-center gap-2 cursor-pointer hover:text-forest">
                            <input type="checkbox" class="rounded text-forest focus:ring-forest">
                            <span>Lipid Barrier Renewal</span>
                        </label>
                        <label class="flex items-center gap-2 cursor-pointer hover:text-forest">
                            <input type="checkbox" class="rounded text-forest focus:ring-forest">
                            <span>Antioxidant Shield</span>
                        </label>
                    </div>
                </div>

                <!-- Availability -->
                <div>
                    <h3 class="text-xs uppercase tracking-[0.2em] font-semibold text-ink mb-4 pb-2 border-b border-beige/80">
                        Availability
                    </h3>
                    <div class="space-y-2 text-xs">
                        <label class="flex items-center gap-2 cursor-pointer hover:text-forest">
                            <input type="checkbox" checked class="rounded text-forest focus:ring-forest">
                            <span>In Stock Only</span>
                        </label>
                        <label class="flex items-center gap-2 cursor-pointer hover:text-forest">
                            <input type="checkbox" class="rounded text-forest focus:ring-forest">
                            <span>On Sale / Special Sets</span>
                        </label>
                    </div>
                </div>

                <!-- Reset Filters -->
                <div class="pt-2">
                    <a href="/shop/category/{{ $category['slug'] ?? 'face-care' }}" class="text-xs text-muted hover:text-forest underline underline-offset-4">
                        Clear All Filters
                    </a>
                </div>
            </aside>

            <!-- Main Product Catalogue Column -->
            <div class="lg:col-span-3">
                
                <!-- Desktop Top Sort & Count Bar -->
                <div class="hidden lg:flex items-center justify-between pb-5 mb-8 border-b border-beige/60">
                    <span class="text-xs uppercase tracking-wider text-muted font-medium">
                        Showing {{ count($products ?? []) }} Formulations
                    </span>

                    <!-- Desktop Sort Dropdown -->
                    <div class="relative" x-data="{ open: false }">
                        <div class="flex items-center gap-2 text-xs">
                            <span class="text-muted">Sort by:</span>
                            <button 
                                type="button" 
                                @click="open = !open" 
                                @click.outside="open = false"
                                class="inline-flex items-center gap-2 py-1.5 px-3 bg-ivory border border-beige rounded text-ink hover:border-forest transition-colors cursor-pointer font-medium"
                                aria-haspopup="true"
                                :aria-expanded="open"
                            >
                                <span x-text="sortLabel">Featured Formulations</span>
                                <svg class="w-3.5 h-3.5 text-muted transition-transform" :class="open ? 'rotate-180' : ''" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7" />
                                </svg>
                            </button>
                        </div>

                        <!-- Dropdown Menu -->
                        <div 
                            x-show="open" 
                            x-cloak
                            x-transition:enter="transition ease-out duration-150"
                            x-transition:enter-start="opacity-0 translate-y-1"
                            x-transition:enter-end="opacity-100 translate-y-0"
                            class="absolute right-0 mt-2 w-52 bg-ivory border border-beige shadow-lg py-1 z-20 text-xs rounded"
                        >
                            <button 
                                type="button" 
                                @click="setSort('featured', 'Featured Formulations'); open = false" 
                                class="w-full text-left px-4 py-2 hover:bg-beige/40 flex items-center justify-between cursor-pointer"
                                :class="sortBy === 'featured' ? 'text-forest font-semibold bg-sage-light/30' : 'text-ink'"
                            >
                                <span>Featured Formulations</span>
                                <span x-show="sortBy === 'featured'" class="text-forest">✓</span>
                            </button>
                            <button 
                                type="button" 
                                @click="setSort('price-asc', 'Price: Low to High'); open = false" 
                                class="w-full text-left px-4 py-2 hover:bg-beige/40 flex items-center justify-between cursor-pointer"
                                :class="sortBy === 'price-asc' ? 'text-forest font-semibold bg-sage-light/30' : 'text-ink'"
                            >
                                <span>Price: Low to High</span>
                                <span x-show="sortBy === 'price-asc'" class="text-forest">✓</span>
                            </button>
                            <button 
                                type="button" 
                                @click="setSort('price-desc', 'Price: High to Low'); open = false" 
                                class="w-full text-left px-4 py-2 hover:bg-beige/40 flex items-center justify-between cursor-pointer"
                                :class="sortBy === 'price-desc' ? 'text-forest font-semibold bg-sage-light/30' : 'text-ink'"
                            >
                                <span>Price: High to Low</span>
                                <span x-show="sortBy === 'price-desc'" class="text-forest">✓</span>
                            </button>
                            <button 
                                type="button" 
                                @click="setSort('rating', 'Highest Rated'); open = false" 
                                class="w-full text-left px-4 py-2 hover:bg-beige/40 flex items-center justify-between cursor-pointer"
                                :class="sortBy === 'rating' ? 'text-forest font-semibold bg-sage-light/30' : 'text-ink'"
                            >
                                <span>Highest Rated</span>
                                <span x-show="sortBy === 'rating'" class="text-forest">✓</span>
                            </button>
                            <button 
                                type="button" 
                                @click="setSort('newest', 'Newest Rituals'); open = false" 
                                class="w-full text-left px-4 py-2 hover:bg-beige/40 flex items-center justify-between cursor-pointer"
                                :class="sortBy === 'newest' ? 'text-forest font-semibold bg-sage-light/30' : 'text-ink'"
                            >
                                <span>Newest Rituals</span>
                                <span x-show="sortBy === 'newest'" class="text-forest">✓</span>
                            </button>
                        </div>
                    </div>
                </div>

                <!-- Product Grid (3 columns on desktop next to sidebar) -->
                <x-storefront.product-grid :products="$products ?? []" columns="3" />
            </div>

        </div>
    </div>
</div>
@endsection
