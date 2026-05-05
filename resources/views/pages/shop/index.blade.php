<x-layouts.app title="Shop | feriwalarhat">
    <div class="bg-gray-50 py-4 md:py-8">
        <div class="max-w-[1400px] mx-auto px-4 sm:px-6 lg:px-8">
            
            <!-- Breadcrumbs -->
            <nav class="flex text-xs font-medium text-gray-500 uppercase tracking-wider mb-6">
                <a href="{{ route('home') }}" class="hover:text-red-600 transition-colors">Home</a>
                <span class="mx-2">/</span>
                <span class="text-gray-900">Shop</span>
            </nav>

            <div class="flex flex-col lg:flex-row gap-8">
                <!-- Sidebar Filters -->
                <div class="w-full lg:w-64 flex-shrink-0">
                    <div class="bg-white border border-gray-200 rounded-lg p-5 space-y-8 sticky top-24">
                        <!-- Categories Filter -->
                        <div>
                            <h3 class="text-sm font-bold text-gray-900 uppercase tracking-widest mb-4 pb-2 border-b border-gray-100">Categories</h3>
                            <ul class="space-y-3">
                                <li>
                                    <a href="{{ route('shop') }}" class="flex items-center justify-between text-sm {{ !request('category') ? 'text-red-600 font-bold' : 'text-gray-600 hover:text-red-600' }}">
                                        <span>All Products</span>
                                    </a>
                                </li>
                                @foreach($categories as $category)
                                    @php
                                        $isChildActive = request('category') && $category->children->pluck('slug')->contains(request('category'));
                                        $isParentActive = request('category') === $category->slug;
                                        $isOpen = $isParentActive || $isChildActive;
                                    @endphp
                                    <li x-data="{ open: {{ $isOpen ? 'true' : 'false' }} }">
                                        <div class="flex items-center justify-between group">
                                            <a href="{{ route('shop', array_merge(request()->query(), ['category' => $category->slug])) }}" 
                                               class="flex-1 py-1 text-sm {{ $isParentActive ? 'text-red-600 font-bold' : 'text-gray-600 group-hover:text-red-600' }} transition-colors">
                                                {{ $category->name }}
                                            </a>
                                            @if($category->children->count() > 0)
                                                <button @click="open = !open" 
                                                        class="p-1 text-gray-400 hover:text-red-600 focus:outline-none transition-transform duration-200" 
                                                        :class="open ? 'rotate-180' : ''">
                                                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7"></path>
                                                    </svg>
                                                </button>
                                            @endif
                                        </div>
                                        
                                        @if($category->children->count() > 0)
                                            <ul x-show="open" 
                                                x-cloak 
                                                x-transition:enter="transition ease-out duration-200"
                                                x-transition:enter-start="opacity-0 -translate-y-2"
                                                x-transition:enter-end="opacity-100 translate-y-0"
                                                class="ml-3 mt-1 space-y-1 border-l-2 border-gray-50 pl-3">
                                                @foreach($category->children as $child)
                                                    <li>
                                                        <a href="{{ route('shop', array_merge(request()->query(), ['category' => $child->slug])) }}" 
                                                           class="block py-1 text-[13px] {{ request('category') === $child->slug ? 'text-red-600 font-bold' : 'text-gray-500 hover:text-red-600' }} transition-colors">
                                                            {{ $child->name }}
                                                        </a>
                                                    </li>
                                                @endforeach
                                            </ul>
                                        @endif
                                    </li>
                                @endforeach
                            </ul>
                        </div>

                        <!-- Price Filter (Static visual for now) -->
                        <div>
                            <h3 class="text-sm font-bold text-gray-900 uppercase tracking-widest mb-4 pb-2 border-b border-gray-100">Price Range</h3>
                            <div class="space-y-4">
                                <input type="range" min="0" max="10000" class="w-full h-1 bg-gray-200 rounded-lg appearance-none cursor-pointer accent-red-600">
                                <div class="flex items-center gap-2">
                                    <input type="number" placeholder="Min" class="w-full border-gray-200 rounded text-sm px-2 py-1 focus:border-red-600 focus:ring-0">
                                    <span class="text-gray-400">-</span>
                                    <input type="number" placeholder="Max" class="w-full border-gray-200 rounded text-sm px-2 py-1 focus:border-red-600 focus:ring-0">
                                </div>
                                <button class="w-full bg-gray-900 text-white text-xs font-bold uppercase tracking-wider py-2 rounded hover:bg-black transition-colors">Apply</button>
                            </div>
                        </div>

                    </div>
                </div>

                <!-- Main Content -->
                <div class="flex-1">
                    @php
                        /** @var \Illuminate\Pagination\LengthAwarePaginator $products */
                        /** @var \Illuminate\Support\Collection $categories */
                    @endphp
                    <!-- Top Bar -->
                    <div class="bg-white border border-gray-200 rounded-lg p-4 mb-6 flex flex-col sm:flex-row items-start sm:items-center justify-between gap-4">
                        <div>
                            <h1 class="text-xl font-bold text-gray-900">
                                @if(request('search'))
                                    Search Results for "{{ request('search') }}"
                                @elseif(isset($activeCategory))
                                    {{ $activeCategory->name }}
                                @else
                                    All Products
                                @endif
                            </h1>
                            <p class="text-xs text-gray-500 mt-1">Showing {{ $products->firstItem() ?? 0 }}-{{ $products->lastItem() ?? 0 }} of {{ $products->total() }} results</p>
                        </div>

                        <div class="flex items-center gap-4">
                            <label class="text-xs font-bold text-gray-500 uppercase">Sort By:</label>
                            <select onchange="window.location.href=this.value" class="border-gray-200 rounded text-sm text-gray-700 focus:border-red-600 focus:ring-0 py-1.5 pl-3 pr-8 cursor-pointer">
                                <option value="{{ route('shop', array_merge(request()->query(), ['sort' => 'latest'])) }}" {{ request('sort') === 'latest' || !request('sort') ? 'selected' : '' }}>Latest Arrivals</option>
                                <option value="{{ route('shop', array_merge(request()->query(), ['sort' => 'popular'])) }}" {{ request('sort') === 'popular' ? 'selected' : '' }}>Popularity</option>
                                <option value="{{ route('shop', array_merge(request()->query(), ['sort' => 'price_asc'])) }}" {{ request('sort') === 'price_asc' ? 'selected' : '' }}>Price: Low to High</option>
                                <option value="{{ route('shop', array_merge(request()->query(), ['sort' => 'price_desc'])) }}" {{ request('sort') === 'price_desc' ? 'selected' : '' }}>Price: High to Low</option>
                            </select>
                        </div>
                    </div>

                    <!-- Products Grid -->
                    @if($products->count() > 0)
                        <div class="grid grid-cols-2 sm:grid-cols-3 lg:grid-cols-4 gap-4 md:gap-6">
                            @foreach($products as $product)
                                <div class="bg-white rounded-lg shadow-sm hover:shadow-md transition-shadow">
                                    <x-ui.product-card :product="$product" />
                                </div>
                            @endforeach
                        </div>
                        
                        <!-- Pagination -->
                        <div class="mt-8">
                            {{ $products->links() }}
                        </div>
                    @else
                        <div class="bg-white border border-gray-200 rounded-lg p-12 text-center">
                            <div class="inline-flex items-center justify-center w-16 h-16 rounded-full bg-gray-100 text-gray-400 mb-4">
                                <svg class="w-8 h-8" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9.172 16.172a4 4 0 015.656 0M9 10h.01M15 10h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                                </svg>
                            </div>
                            <h3 class="text-lg font-bold text-gray-900 mb-2">No Products Found</h3>
                            <p class="text-sm text-gray-500 mb-6">We couldn't find any products matching your current filters.</p>
                            <a href="{{ route('shop') }}" class="inline-flex items-center justify-center px-6 py-2 border border-transparent rounded-md shadow-sm text-sm font-medium text-white bg-red-600 hover:bg-red-700">
                                Clear All Filters
                            </a>
                        </div>
                    @endif
                </div>
            </div>
        </div>
    </div>
</x-layouts.app>
