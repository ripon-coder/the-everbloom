<x-layouts.app :title="(isset($activeCategory) ? $activeCategory->name . ' - Shop' : 'Shop') . ' | Feriwalarhat'" :description="isset($activeCategory) ? $activeCategory->description : 'Browse our premium electronics and gadgets at Feriwalarhat.'">
    <div class="shop-page-container bg-gray-50 py-4 md:py-8" x-data="{ isFilterDrawerOpen: false }">
        <div class="max-w-[1400px] mx-auto px-1.5 sm:px-6 lg:px-8">
            
            <!-- Breadcrumbs -->
            <nav class="flex text-xs font-medium text-gray-500 uppercase tracking-wider mb-6">
                <a href="{{ route('home') }}" class="hover:text-primary transition-colors">Home</a>
                <span class="mx-2">/</span>
                <span class="text-gray-900">Shop</span>
            </nav>

            <div class="flex flex-col lg:flex-row gap-8">
                <!-- Desktop Sidebar Filters -->
                <div class="hidden lg:block w-64 flex-shrink-0">
                    <div class="bg-white border border-gray-200 rounded-none p-5 space-y-8 sticky top-24">
                        @include('pages.shop.partials.filters-content')
                    </div>
                </div>

                <!-- Main Content -->
                <div class="flex-1">
                    @php
                        /** @var \Illuminate\Pagination\LengthAwarePaginator $products */
                        /** @var \Illuminate\Support\Collection $categories */
                    @endphp
                    <!-- Top Bar -->
                    <div class="bg-white border border-gray-200 rounded-none p-3.5 sm:p-4 mb-4 sm:mb-6 flex flex-col md:flex-row md:items-center justify-between gap-3 sm:gap-4">
                        <!-- Title & Count -->
                        <div>
                            <h1 class="text-base sm:text-xl font-bold text-slate-900 leading-tight">
                                @if(request('search'))
                                    Search Results for "{{ request('search') }}"
                                @elseif(isset($activeCategory))
                                    {{ $activeCategory->name }}
                                @else
                                    All Products
                                @endif
                            </h1>
                            <p class="text-[11px] sm:text-xs text-gray-500 mt-0.5">Showing {{ $products->firstItem() ?? 0 }}-{{ $products->lastItem() ?? 0 }} of {{ $products->total() }} results</p>
                        </div>

                        <!-- Controls (Mobile: side-by-side flex row, Desktop: right aligned flex row) -->
                        <div class="flex items-center gap-2.5 w-full md:w-auto">
                            <!-- Mobile Filters Button (visible on mobile < lg) -->
                            <button type="button" @click="isFilterDrawerOpen = true" class="lg:hidden flex-1 md:flex-none inline-flex items-center justify-center gap-1.5 px-3.5 py-2 border border-gray-200 text-xs font-bold uppercase tracking-wider text-slate-800 hover:text-primary hover:border-primary transition-colors bg-gray-50 hover:bg-white shadow-2xs">
                                <svg class="w-4 h-4 text-primary" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 4a1 1 0 011-1h16a1 1 0 011 1v2.586a1 1 0 01-.293.707l-6.414 6.414a1 1 0 00-.293.707V17l-4 4v-6.586a1 1 0 00-.293-.707L3.293 7.293A1 1 0 013 6.586V4z"></path>
                                </svg>
                                <span>Filters</span>
                            </button>

                            <!-- Sort By Dropdown -->
                            <div class="flex-1 md:flex-none flex items-center gap-2">
                                <label class="hidden sm:inline text-xs font-bold text-gray-500 uppercase whitespace-nowrap">Sort By:</label>
                                <select onchange="window.location.href=this.value" class="w-full md:w-auto border-gray-200 rounded-none text-xs text-slate-800 focus:border-primary focus:ring-0 py-2 pl-3 pr-8 bg-gray-50 hover:bg-white cursor-pointer font-semibold">
                                    <option value="{{ route('shop', array_merge(request()->query(), ['sort' => 'latest'])) }}" {{ request('sort') === 'latest' || !request('sort') ? 'selected' : '' }}>Latest Arrivals</option>
                                    <option value="{{ route('shop', array_merge(request()->query(), ['sort' => 'popular'])) }}" {{ request('sort') === 'popular' ? 'selected' : '' }}>Popularity</option>
                                    <option value="{{ route('shop', array_merge(request()->query(), ['sort' => 'price_asc'])) }}" {{ request('sort') === 'price_asc' ? 'selected' : '' }}>Price: Low to High</option>
                                    <option value="{{ route('shop', array_merge(request()->query(), ['sort' => 'price_desc'])) }}" {{ request('sort') === 'price_desc' ? 'selected' : '' }}>Price: High to Low</option>
                                </select>
                            </div>
                        </div>
                    </div>

                    <!-- Active Filter Badges -->
                    @if(request('min_price') || request('max_price') || request('category') || request('search'))
                        <div class="flex flex-wrap items-center gap-2 mb-4 p-3 bg-white border border-gray-200">
                            <span class="text-xs font-bold text-gray-400 uppercase tracking-wider">Active Filters:</span>
                            @if(request('category') && isset($activeCategory))
                                <a href="{{ route('shop', request()->except('category')) }}" class="inline-flex items-center gap-1.5 px-2.5 py-1 bg-emerald-50 text-emerald-800 text-xs font-bold border border-emerald-200 hover:bg-emerald-100 transition-colors">
                                    <span>Category: {{ $activeCategory->name }}</span>
                                    <svg class="w-3.5 h-3.5 text-emerald-600" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"/></svg>
                                </a>
                            @endif
                            @if(request('min_price'))
                                <a href="{{ route('shop', request()->except('min_price')) }}" class="inline-flex items-center gap-1.5 px-2.5 py-1 bg-emerald-50 text-emerald-800 text-xs font-bold border border-emerald-200 hover:bg-emerald-100 transition-colors">
                                    <span>Min: Tk. {{ number_format(request('min_price')) }}</span>
                                    <svg class="w-3.5 h-3.5 text-emerald-600" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"/></svg>
                                </a>
                            @endif
                            @if(request('max_price'))
                                <a href="{{ route('shop', request()->except('max_price')) }}" class="inline-flex items-center gap-1.5 px-2.5 py-1 bg-emerald-50 text-emerald-800 text-xs font-bold border border-emerald-200 hover:bg-emerald-100 transition-colors">
                                    <span>Max: Tk. {{ number_format(request('max_price')) }}</span>
                                    <svg class="w-3.5 h-3.5 text-emerald-600" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"/></svg>
                                </a>
                            @endif
                            <a href="{{ route('shop') }}" class="text-xs font-bold text-red-600 hover:underline uppercase tracking-wider ml-1">Clear All</a>
                        </div>
                    @endif

                    <!-- Products Grid -->
                    @if($products->count() > 0)
                        <div class="grid grid-cols-2 sm:grid-cols-3 lg:grid-cols-4 gap-2 sm:gap-4 md:gap-6">
                            @foreach($products as $product)
                                <div class="bg-white rounded-none shadow-sm hover:shadow-md transition-shadow">
                                    <x-ui.product-card :product="$product" />
                                </div>
                            @endforeach
                        </div>
                        
                        <!-- Pagination -->
                        <div class="mt-8">
                            {{ $products->links() }}
                        </div>
                    @else
                        <div class="bg-white border border-gray-200 rounded-none p-12 text-center">
                            <div class="inline-flex items-center justify-center w-16 h-16 rounded-none bg-gray-100 text-gray-400 mb-4">
                                <svg class="w-8 h-8" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9.172 16.172a4 4 0 015.656 0M9 10h.01M15 10h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                                </svg>
                            </div>
                            <h3 class="text-lg font-bold text-gray-900 mb-2">No Products Found</h3>
                            <p class="text-sm text-gray-500 mb-6">We couldn't find any products matching your current filters.</p>
                            <a href="{{ route('shop') }}" class="inline-flex items-center justify-center px-6 py-2 border border-transparent rounded-none shadow-sm text-sm font-medium text-white bg-primary hover:bg-primary-dark">
                                Clear All Filters
                            </a>
                        </div>
                    @endif
                </div>
            </div>
        </div>

        <!-- Mobile Filter Slide-over Drawer -->
        <div x-show="isFilterDrawerOpen" 
             x-cloak 
             class="fixed inset-0 z-[10000] lg:hidden"
             role="dialog" 
             aria-modal="true">
            <!-- Backdrop -->
            <div x-show="isFilterDrawerOpen" 
                 x-transition:enter="transition-opacity ease-linear duration-300"
                 x-transition:enter-start="opacity-0"
                 x-transition:enter-end="opacity-100"
                 x-transition:leave="transition-opacity ease-linear duration-300"
                 x-transition:leave-start="opacity-100"
                 x-transition:leave-end="opacity-0"
                 @click="isFilterDrawerOpen = false"
                 class="fixed inset-0 bg-black/50 backdrop-blur-xs"></div>

            <!-- Drawer Container -->
            <div class="fixed inset-y-0 left-0 max-w-full flex">
                <div x-show="isFilterDrawerOpen" 
                     x-transition:enter="transition ease-in-out duration-300 transform"
                     x-transition:enter-start="-translate-x-full"
                     x-transition:enter-end="translate-x-0"
                     x-transition:leave="transition ease-in-out duration-300 transform"
                     x-transition:leave-start="translate-x-0"
                     x-transition:leave-end="-translate-x-full"
                     class="w-screen max-w-xs bg-white shadow-2xl flex flex-col h-full">
                    
                    <!-- Drawer Header -->
                    <div class="p-4 bg-slate-900 text-white flex items-center justify-between border-b border-gray-800">
                        <div class="flex items-center gap-2">
                            <svg class="w-5 h-5 text-primary" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 4a1 1 0 011-1h16a1 1 0 011 1v2.586a1 1 0 01-.293.707l-6.414 6.414a1 1 0 00-.293.707V17l-4 4v-6.586a1 1 0 00-.293-.707L3.293 7.293A1 1 0 013 6.586V4z"></path>
                            </svg>
                            <h2 class="text-sm font-bold uppercase tracking-wider">Filters</h2>
                        </div>
                        <button type="button" @click="isFilterDrawerOpen = false" class="text-gray-400 hover:text-white p-1 focus:outline-none">
                            <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"></path>
                            </svg>
                        </button>
                    </div>

                    <!-- Drawer Body -->
                    <div class="p-5 flex-1 overflow-y-auto space-y-8">
                        @include('pages.shop.partials.filters-content')
                    </div>
                </div>
            </div>
        </div>
    </div>
</x-layouts.app>
