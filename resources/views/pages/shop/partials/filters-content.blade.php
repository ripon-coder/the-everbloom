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

<!-- Price Filter (Static visual for now) -->
<div>
    <h3 class="text-sm font-bold text-gray-900 uppercase tracking-widest mb-4 pb-2 border-b border-gray-100">Price Range</h3>
    <div class="space-y-4">
        <input type="range" min="0" max="10000" class="w-full h-1 bg-gray-200 rounded-none appearance-none cursor-pointer accent-primary">
        <div class="flex items-center gap-2">
            <input type="number" placeholder="Min" class="w-full border-gray-200 rounded-none text-sm px-2 py-1 focus:border-primary focus:ring-0">
            <span class="text-gray-400">-</span>
            <input type="number" placeholder="Max" class="w-full border-gray-200 rounded-none text-sm px-2 py-1 focus:border-primary focus:ring-0">
        </div>
        <button class="w-full bg-gray-900 text-white text-xs font-bold uppercase tracking-wider py-2 rounded-none hover:bg-black transition-colors">Apply</button>
    </div>
</div>
