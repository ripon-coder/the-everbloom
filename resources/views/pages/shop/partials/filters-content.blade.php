<!-- Categories Filter -->
<div>
    <h3 class="text-sm font-bold text-gray-900 uppercase tracking-widest mb-4 pb-2 border-b border-gray-100">Categories</h3>
    <ul class="space-y-3">
        <li>
            <a href="{{ route('shop') }}" class="flex items-center justify-between text-sm {{ !request('category') ? 'text-primary font-bold' : 'text-gray-600 hover:text-primary' }}">
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
                       class="flex-1 py-1 text-sm {{ $isParentActive ? 'text-primary font-bold' : 'text-gray-600 group-hover:text-primary' }} transition-colors">
                        {{ $category->name }}
                    </a>
                    @if($category->children->count() > 0)
                        <button @click="open = !open" 
                                class="p-1 text-gray-400 hover:text-primary focus:outline-none transition-transform duration-200" 
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
                                   class="block py-1 text-[13px] {{ request('category') === $child->slug ? 'text-primary font-bold' : 'text-gray-500 hover:text-primary' }} transition-colors">
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

<!-- Price Filter -->
<div>
    <h3 class="text-sm font-bold text-gray-900 uppercase tracking-widest mb-4 pb-2 border-b border-gray-100">Price Range</h3>
    <form action="{{ route('shop') }}" method="GET" class="space-y-4">
        @if(request('category'))
            <input type="hidden" name="category" value="{{ request('category') }}">
        @endif
        @if(request('search'))
            <input type="hidden" name="search" value="{{ request('search') }}">
        @endif
        @if(request('sort'))
            <input type="hidden" name="sort" value="{{ request('sort') }}">
        @endif

        <div class="flex items-center gap-2">
            <div class="flex-1">
                <label class="text-[10px] text-gray-400 font-bold uppercase tracking-wider block mb-1">Min (Tk)</label>
                <input type="number" name="min_price" value="{{ request('min_price') }}" placeholder="0" min="0" 
                    class="w-full border border-gray-200 rounded-none text-xs px-2.5 py-1.5 focus:border-primary focus:ring-0">
            </div>
            <span class="text-gray-400 font-bold self-end pb-1.5">-</span>
            <div class="flex-1">
                <label class="text-[10px] text-gray-400 font-bold uppercase tracking-wider block mb-1">Max (Tk)</label>
                <input type="number" name="max_price" value="{{ request('max_price') }}" placeholder="Max" min="0" 
                    class="w-full border border-gray-200 rounded-none text-xs px-2.5 py-1.5 focus:border-primary focus:ring-0">
            </div>
        </div>

        <div class="flex gap-2">
            <button type="submit" class="flex-1 bg-primary hover:bg-primary-dark text-white text-xs font-bold uppercase tracking-wider py-2.5 rounded-none transition-colors shadow-xs">
                Apply Price Filter
            </button>
            @if(request('min_price') || request('max_price'))
                <a href="{{ route('shop', request()->except(['min_price', 'max_price'])) }}" 
                    class="px-3 bg-gray-200 text-gray-700 hover:bg-gray-300 text-xs font-bold uppercase tracking-wider py-2.5 rounded-none transition-colors flex items-center justify-center">
                    Reset
                </a>
            @endif
        </div>
    </form>
</div>
