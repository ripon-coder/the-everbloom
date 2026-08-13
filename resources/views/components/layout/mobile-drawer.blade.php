<div x-data="{ 
        isOpen: false, 
        activeTab: 'menu',
        open(tab = 'menu') { 
            this.isOpen = true; 
            if (tab) this.activeTab = tab;
        },
        close() { 
            this.isOpen = false; 
        },
        toggle(tab = 'menu') { 
            this.isOpen = !this.isOpen; 
            if (tab && this.isOpen) this.activeTab = tab;
        }
    }"
    x-init="
        window.addEventListener('toggle-mobile-menu', (e) => {
            toggle(e.detail && e.detail.tab ? e.detail.tab : 'menu');
        });
        window.addEventListener('open-mobile-menu', (e) => {
            open(e.detail && e.detail.tab ? e.detail.tab : 'menu');
        });
        window.addEventListener('close-mobile-menu', () => {
            close();
        });
    ">

    <!-- Mobile Drawer Overlay -->
    <div x-show="isOpen" x-cloak style="display: none;" 
        class="fixed inset-0 bg-black/60 z-[999999] md:hidden"
        @click="close()" 
        x-transition:enter="transition ease-out duration-300"
        x-transition:enter-start="opacity-0"
        x-transition:enter-end="opacity-100"
        x-transition:leave="transition ease-in duration-200"
        x-transition:leave-start="opacity-100"
        x-transition:leave-end="opacity-0"></div>

    <!-- Mobile Drawer Panel -->
    <div x-show="isOpen" x-cloak style="display: none;" 
        x-transition:enter="transform transition ease-in-out duration-300"
        x-transition:enter-start="-translate-x-full" 
        x-transition:enter-end="translate-x-0"
        x-transition:leave="transform transition ease-in-out duration-300" 
        x-transition:leave-start="translate-x-0"
        x-transition:leave-end="-translate-x-full"
        class="fixed top-0 bottom-0 left-0 h-full w-[85%] max-w-sm bg-white z-[1000000] md:hidden flex flex-col shadow-2xl">

        <!-- Mobile Drawer Header -->
        <div class="flex items-center justify-between px-5 py-4 border-b border-gray-200 bg-slate-900 text-white shrink-0">
            <span class="text-xs font-bold uppercase tracking-wider">Menu</span>
            <button @click="close()" type="button" class="p-1 text-gray-300 hover:text-white transition-colors" title="Close Menu">
                <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"/></svg>
            </button>
        </div>

        <!-- Tabs -->
        <div class="flex border-b border-gray-200 shrink-0">
            <button @click="activeTab = 'menu'" type="button" class="flex-1 py-4 text-[13px] font-bold text-center tracking-wide cursor-pointer"
                :class="activeTab === 'menu' ? 'text-slate-800 border-b-[3px] border-black bg-white' : 'text-gray-500 bg-gray-100'">MENU</button>
            <button @click="activeTab = 'categories'" type="button" class="flex-1 py-4 text-[13px] font-bold text-center tracking-wide cursor-pointer"
                :class="activeTab === 'categories' ? 'text-slate-800 border-b-[3px] border-black bg-white' : 'text-gray-500 bg-gray-100'">CATEGORIES</button>
        </div>

        <!-- Menu Tab Content -->
        <div x-show="activeTab === 'menu'" style="display: none;" class="flex-1 overflow-y-auto pb-20">
            <ul class="flex flex-col">
                <li class="border-b border-gray-100"><a href="{{ route('home') }}"
                        class="block px-6 py-4 text-[14px] font-bold tracking-wide transition-colors {{ request()->routeIs('home') ? 'text-primary' : 'text-slate-800 hover:text-primary' }}">Home</a>
                </li>
                @if(isset($header_menus))
                    @foreach($header_menus as $menu)
                        <li class="border-b border-gray-100"><a href="{{ $menu->url }}"
                                class="block px-6 py-4 text-[14px] font-bold tracking-wide transition-colors {{ request()->url() == url($menu->url) ? 'text-primary' : 'text-slate-800 hover:text-primary' }}">{{ $menu->name }}</a>
                        </li>
                    @endforeach
                @endif
            </ul>
            <div class="px-6 py-6 mt-4 border-t border-gray-200">
                <div class="text-[11px] text-gray-400 font-bold uppercase tracking-wider mb-4">Contact & Support</div>
                <a href="tel:{{ $site_setting->site_phone ?? '+8801720000000' }}"
                    class="flex items-center gap-3 text-slate-700 hover:text-primary font-medium mb-4 text-xs">
                    <svg class="w-5 h-5 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5"
                            d="M3 5a2 2 0 012-2h3.28a1 1 0 01.948.684l1.498 4.493a1 1 0 01-.502 1.21l-2.257 1.13a11.042 11.042 0 005.516 5.516l1.13-2.257a1 1 0 011.21-.502l4.493 1.498a1 1 0 01.684.949V19a2 2 0 01-2 2h-1C9.716 21 3 14.284 3 6V5z">
                        </path>
                    </svg>
                    {{ $site_setting->site_phone ?? '+88 01720 000000' }}
                </a>
                <a href="mailto:{{ $site_setting->site_email ?? 'support@feriwalarhat.com' }}"
                    class="flex items-center gap-3 text-slate-700 hover:text-primary font-medium text-xs">
                    <svg class="w-5 h-5 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5"
                            d="M3 8l7.89 5.26a2 2 0 002.22 0L21 8M5 19h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v10a2 2 0 002 2z">
                        </path>
                    </svg>
                    {{ $site_setting->site_email ?? 'support@feriwalarhat.com' }}
                </a>
            </div>

            <!-- Customer Account & Logout Section -->
            @auth
                <div class="px-6 py-4 border-t border-gray-200 bg-gray-50">
                    <div class="flex items-center gap-3 mb-3">
                        <div class="w-8 h-8 rounded-none bg-primary text-white flex items-center justify-center font-bold text-xs">
                            {{ substr(Auth::user()->name, 0, 1) }}
                        </div>
                        <div class="min-w-0">
                            <p class="text-xs font-bold text-slate-900 truncate">{{ Auth::user()->name }}</p>
                            <p class="text-[10px] text-gray-500 truncate">{{ Auth::user()->email ?? Auth::user()->phone }}</p>
                        </div>
                    </div>
                    <div class="space-y-1">
                        <a href="{{ route('account') }}" class="block text-xs font-bold text-slate-800 hover:text-primary py-1.5 uppercase tracking-wider">My Account</a>
                        <a href="{{ route('account', 'orders') }}" class="block text-xs font-bold text-slate-800 hover:text-primary py-1.5 uppercase tracking-wider">My Orders</a>
                        <form method="POST" action="{{ route('logout') }}" class="mt-2 pt-2 border-t border-gray-200">
                            @csrf
                            <button type="submit" class="flex items-center gap-2 text-xs font-bold text-red-600 hover:text-red-700 uppercase tracking-wider">
                                <svg class="w-4 h-4 text-red-500" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.8" d="M17 16l4-4m0 0l-4-4m4 4H7m6 4v1a3 3 0 01-3 3H6a3 3 0 01-3-3V7a3 3 0 013-3h4a3 3 0 013 3v1"/></svg>
                                <span>Logout</span>
                            </button>
                        </form>
                    </div>
                </div>
            @else
                <div class="px-6 py-4 border-t border-gray-200 bg-gray-50">
                    <a href="{{ route('login') }}" class="block w-full text-center bg-slate-900 hover:bg-black text-white text-xs font-bold py-3 uppercase tracking-wider">Sign In / Register</a>
                </div>
            @endauth
        </div>

        <!-- Categories Tab Content -->
        <div x-show="activeTab === 'categories'" style="display: none;" class="flex-1 overflow-y-auto pb-20">
            <ul class="flex flex-col">
                @if(isset($header_categories) && $header_categories->count() > 0)
                    @foreach($header_categories as $category)
                        @if($category->children && $category->children->count() > 0)
                            <li class="border-b border-gray-200" x-data="{ open: false }">
                                <div class="flex items-stretch justify-between text-[14px] text-slate-800 font-medium transition-colors duration-200"
                                    :class="open ? 'bg-slate-50' : ''">
                                    <a href="{{ route('shop', ['category' => $category->slug]) }}" class="flex items-center gap-3 px-5 py-3.5 flex-1 hover:text-primary">
                                        {{ $category->name }}
                                    </a>
                                    <button @click="open = !open" type="button"
                                        class="w-[54px] flex-shrink-0 flex items-center justify-center border-l border-gray-200 transition-colors duration-200 cursor-pointer"
                                        :class="open ? 'bg-black text-white border-black' : 'text-gray-400 bg-white hover:bg-gray-50'">
                                        <svg class="w-4 h-4 transition-transform duration-200" :class="open ? '' : '-rotate-90'"
                                            fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                                d="M19 9l-7 7-7-7"></path>
                                        </svg>
                                    </button>
                                </div>
                                <ul x-show="open" style="display: none;" class="bg-white border-t border-gray-200">
                                    @foreach($category->children as $child)
                                        <li class="border-b border-gray-100 last:border-0">
                                            <a href="{{ route('shop', ['category' => $child->slug]) }}"
                                                class="block pl-12 pr-5 py-3 text-[13px] text-slate-600 hover:text-primary">
                                                {{ $child->name }}
                                            </a>
                                        </li>
                                    @endforeach
                                </ul>
                            </li>
                        @else
                            <li class="border-b border-gray-200">
                                <a href="{{ route('shop', ['category' => $category->slug]) }}"
                                    class="flex items-center justify-between px-5 py-3.5 text-[14px] text-slate-800 font-medium hover:text-primary">
                                    <div class="flex items-center gap-3">{{ $category->name }}</div>
                                </a>
                            </li>
                        @endif
                    @endforeach
                @else
                    <li class="p-5 text-center text-xs text-gray-500">No categories found</li>
                @endif
            </ul>
        </div>
    </div>
</div>
