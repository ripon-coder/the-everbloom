<aside id="sidebar"
    class="fixed top-0 left-0 z-40 w-64 h-screen bg-white border-r border-gray-100 shadow-sm transition-all duration-300">
    <div class="h-full flex flex-col px-3 py-6 overflow-y-auto scrollbar-thin scrollbar-thumb-gray-200">
        <!-- Sidebar Header -->
        <div class="mb-8 px-3">
            <div class="flex items-center space-x-3">
                <div class="w-9 h-9 bg-gradient-to-tr from-indigo-600 to-violet-600 rounded-lg flex items-center justify-center shadow-md shadow-indigo-200">
                    <svg class="w-5 h-5 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M13 10V3L4 14h7v7l9-11h-7z" />
                    </svg>
                </div>
                <div>
                    <h2 class="text-base font-bold text-gray-900 tracking-tight">Feriwalarhat</h2>
                    <p class="text-[10px] font-medium text-indigo-600 uppercase tracking-widest">Management</p>
                </div>
            </div>
        </div>

        <!-- Navigation -->
        <nav class="flex-1 space-y-6">
            <!-- GROUP: OVERVIEW -->
            <div>
                <p class="px-4 text-[11px] font-bold text-gray-400 uppercase tracking-widest mb-3">Overview</p>
                <ul class="space-y-1">
                    <li>
                        <a href="{{ route('admin.dashboard') }}"
                            class="flex items-center px-4 py-2.5 text-sm font-medium rounded-xl transition-all duration-200 {{ request()->is('admin/dashboard') ? 'bg-indigo-50 text-indigo-700' : 'text-gray-600 hover:bg-gray-50 hover:text-gray-900' }}">
                            <svg class="w-5 h-5 mr-3 {{ request()->is('admin/dashboard') ? 'text-indigo-600' : 'text-gray-400 group-hover:text-gray-600' }}" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 12l2-2m0 0l7-7 7 7M5 10v10a1 1 0 001 1h3m10-11l2 2m-2-2v10a1 1 0 01-1 1h-3m-6 0a1 1 0 001-1v-4a1 1 0 011-1h2a1 1 0 011 1v4a1 1 0 001 1m-6 0h6" />
                            </svg>
                            Dashboard
                        </a>
                    </li>
                </ul>
            </div>

            <!-- GROUP: E-COMMERCE -->
            <div>
                <p class="px-4 text-[11px] font-bold text-gray-400 uppercase tracking-widest mb-3">E-Commerce</p>
                <ul class="space-y-1">
                    <li>
                        <a href="{{ route('admin.orders.index') }}"
                            class="flex items-center px-4 py-2.5 text-sm font-medium rounded-xl transition-all duration-200 {{ request()->is('admin/orders*') ? 'bg-indigo-50 text-indigo-700' : 'text-gray-600 hover:bg-gray-50 hover:text-gray-900' }}">
                            <svg class="w-5 h-5 mr-3 {{ request()->is('admin/orders*') ? 'text-indigo-600' : 'text-gray-400' }}" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M16 11V7a4 4 0 00-8 0v4M5 9h14l1 12H4L5 9z" />
                            </svg>
                            Orders
                            @php $pendingOrders = \App\Models\Order::where('status', 'pending')->count(); @endphp
                            @if($pendingOrders > 0)
                                <span class="ml-auto bg-rose-500 text-white text-[10px] font-bold px-2 py-0.5 rounded-full shadow-sm shadow-rose-200">{{ $pendingOrders }}</span>
                            @endif
                        </a>
                    </li>
                    <li>
                        <a href="{{ route('admin.products.index') }}"
                            class="flex items-center px-4 py-2.5 text-sm font-medium rounded-xl transition-all duration-200 {{ request()->is('admin/products*') ? 'bg-indigo-50 text-indigo-700' : 'text-gray-600 hover:bg-gray-50 hover:text-gray-900' }}">
                            <svg class="w-5 h-5 mr-3 {{ request()->is('admin/products*') ? 'text-indigo-600' : 'text-gray-400' }}" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M20 7l-8-4-8 4m16 0l-8 4m8-4v10l-8 4m0-10L4 7m8 4v10M4 7v10l8 4" />
                            </svg>
                            Products
                        </a>
                    </li>
                    <li>
                        <a href="{{ route('admin.categories.index') }}"
                            class="flex items-center px-4 py-2.5 text-sm font-medium rounded-xl transition-all duration-200 {{ request()->is('admin/categories*') ? 'bg-indigo-50 text-indigo-700' : 'text-gray-600 hover:bg-gray-50 hover:text-gray-900' }}">
                            <svg class="w-5 h-5 mr-3 {{ request()->is('admin/categories*') ? 'text-indigo-600' : 'text-gray-400' }}" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 6h16M4 12h16M4 18h7" />
                            </svg>
                            Categories
                        </a>
                    </li>
                    <li>
                        <a href="{{ route('admin.brands.index') }}"
                            class="flex items-center px-4 py-2.5 text-sm font-medium rounded-xl transition-all duration-200 {{ request()->is('admin/brands*') ? 'bg-indigo-50 text-indigo-700' : 'text-gray-600 hover:bg-gray-50 hover:text-gray-900' }}">
                            <svg class="w-5 h-5 mr-3 {{ request()->is('admin/brands*') ? 'text-indigo-600' : 'text-gray-400' }}" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M7 7h.01M7 3h5c.512 0 1.024.195 1.414.586l7 7a2 2 0 010 2.828l-7 7a2 2 0 01-2.828 0l-7-7A1.994 1.994 0 013 12V7a4 4 0 014-4z" />
                            </svg>
                            Brands
                        </a>
                    </li>
                    <li>
                        <a href="{{ route('admin.variants.index') }}"
                            class="flex items-center px-4 py-2.5 text-sm font-medium rounded-xl transition-all duration-200 {{ request()->is('admin/variants*') ? 'bg-indigo-50 text-indigo-700' : 'text-gray-600 hover:bg-gray-50 hover:text-gray-900' }}">
                            <svg class="w-5 h-5 mr-3 {{ request()->is('admin/variants*') ? 'text-indigo-600' : 'text-gray-400' }}" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 5a1 1 0 011-1h14a1 1 0 011 1v2a1 1 0 01-1 1H5a1 1 0 01-1-1V5zM4 13a1 1 0 011-1h6a1 1 0 011 1v6a1 1 0 01-1 1H5a1 1 0 01-1-1v-6zM16 13a1 1 0 011-1h2a1 1 0 011 1v6a1 1 0 01-1 1h-2a1 1 0 01-1-1v-6z" />
                            </svg>
                            Variants
                        </a>
                    </li>
                    <li>
                        <a href="{{ route('admin.attributes.index') }}"
                            class="flex items-center px-4 py-2.5 text-sm font-medium rounded-xl transition-all duration-200 {{ request()->is('admin/attributes*') ? 'bg-indigo-50 text-indigo-700' : 'text-gray-600 hover:bg-gray-50 hover:text-gray-900' }}">
                            <svg class="w-5 h-5 mr-3 {{ request()->is('admin/attributes*') ? 'text-indigo-600' : 'text-gray-400' }}" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 6V4m0 2a2 2 0 100 4m0-4a2 2 0 110 4m-6 8a2 2 0 100-4m0 4a2 2 0 110-4m0 4v2m0-6V4m6 6v10m6-2a2 2 0 100-4m0 4a2 2 0 110-4m0 4v2m0-6V4" />
                            </svg>
                            Attributes
                        </a>
                    </li>
                    <li>
                        <a href="{{ route('admin.attribute-values.index') }}"
                            class="flex items-center px-4 py-2.5 text-sm font-medium rounded-xl transition-all duration-200 {{ request()->is('admin/attribute-values*') ? 'bg-indigo-50 text-indigo-700' : 'text-gray-600 hover:bg-gray-50 hover:text-gray-900' }}">
                            <svg class="w-5 h-5 mr-3 {{ request()->is('admin/attribute-values*') ? 'text-indigo-600' : 'text-gray-400' }}" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M7 21h10a2 2 0 002-2V9.414a1 1 0 00-.293-.707l-5.414-5.414A1 1 0 0012.586 3H7a2 2 0 00-2 2v14a2 2 0 002 2z" />
                            </svg>
                            Attribute Values
                        </a>
                    </li>
                </ul>
            </div>

            <!-- GROUP: MARKETING -->
            <div>
                <p class="px-4 text-[11px] font-bold text-gray-400 uppercase tracking-widest mb-3">Marketing</p>
                <ul class="space-y-1">
                    <li>
                        <a href="{{ route('admin.coupons.index') }}"
                            class="flex items-center px-4 py-2.5 text-sm font-medium rounded-xl transition-all duration-200 {{ request()->is('admin/coupons*') ? 'bg-indigo-50 text-indigo-700' : 'text-gray-600 hover:bg-gray-50 hover:text-gray-900' }}">
                            <svg class="w-5 h-5 mr-3 {{ request()->is('admin/coupons*') ? 'text-indigo-600' : 'text-gray-400' }}" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 5v2m0 4v2m0 4v2M5 5a2 2 0 00-2 2v3a2 2 0 110 4v3a2 2 0 002 2h14a2 2 0 002-2v-3a2 2 0 110-4V7a2 2 0 00-2-2H5z" />
                            </svg>
                            Coupons
                        </a>
                    </li>
                    {{-- 
                    <li>
                        <a href="{{ route('admin.flash-sales.index') }}"
                            class="flex items-center px-4 py-2.5 text-sm font-medium rounded-xl transition-all duration-200 {{ request()->is('admin/flash-sales*') ? 'bg-indigo-50 text-indigo-700' : 'text-gray-600 hover:bg-gray-50 hover:text-gray-900' }}">
                            <svg class="w-5 h-5 mr-3 {{ request()->is('admin/flash-sales*') ? 'text-indigo-600' : 'text-gray-400' }}" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 10V3L4 14h7v7l9-11h-7z" />
                            </svg>
                            Flash Sales
                        </a>
                    </li>
                    --}}
                </ul>
            </div>

            <!-- GROUP: CUSTOMERS -->
            <div>
                <p class="px-4 text-[11px] font-bold text-gray-400 uppercase tracking-widest mb-3">Customers</p>
                <ul class="space-y-1">
                    <li>
                        <a href="{{ route('admin.customers.index') }}"
                            class="flex items-center px-4 py-2.5 text-sm font-medium rounded-xl transition-all duration-200 {{ request()->is('admin/customers*') ? 'bg-indigo-50 text-indigo-700' : 'text-gray-600 hover:bg-gray-50 hover:text-gray-900' }}">
                            <svg class="w-5 h-5 mr-3 {{ request()->is('admin/customers*') ? 'text-indigo-600' : 'text-gray-400' }}" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4.354a4 4 0 110 5.292M15 21H3v-1a6 6 0 0112 0v1zm0 0h6v-1a6 6 0 00-9-5.197M13 7a4 4 0 11-8 0 4 4 0 018 0z" />
                            </svg>
                            Customers
                        </a>
                    </li>
                    <li>
                        <a href="{{ route('admin.reviews.index') }}"
                            class="flex items-center px-4 py-2.5 text-sm font-medium rounded-xl transition-all duration-200 {{ request()->is('admin/reviews*') ? 'bg-indigo-50 text-indigo-700' : 'text-gray-600 hover:bg-gray-50 hover:text-gray-900' }}">
                            <svg class="w-5 h-5 mr-3 {{ request()->is('admin/reviews*') ? 'text-indigo-600' : 'text-gray-400' }}" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 10h.01M12 10h.01M16 10h.01M9 16H5a2 2 0 01-2-2V6a2 2 0 012-2h14a2 2 0 012 2v8a2 2 0 01-2 2h-5l-5 5v-5z" />
                            </svg>
                            Reviews
                        </a>
                    </li>
                    <li>
                        <a href="{{ route('admin.contacts.index') }}"
                            class="flex items-center px-4 py-2.5 text-sm font-medium rounded-xl transition-all duration-200 {{ request()->is('admin/contacts*') ? 'bg-indigo-50 text-indigo-700' : 'text-gray-600 hover:bg-gray-50 hover:text-gray-900' }}">
                            <svg class="w-5 h-5 mr-3 {{ request()->is('admin/contacts*') ? 'text-indigo-600' : 'text-gray-400' }}" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 8l7.89 5.26a2 2 0 002.22 0L21 8M5 19h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v10a2 2 0 002 2z" />
                            </svg>
                            Contact Us
                            @php $unreadContacts = \App\Models\ContactMessage::where('is_read', false)->count(); @endphp
                            @if($unreadContacts > 0)
                                <span class="ml-auto bg-indigo-600 text-white text-[10px] font-bold px-2 py-0.5 rounded-full shadow-sm shadow-indigo-200">{{ $unreadContacts }}</span>
                            @endif
                        </a>
                    </li>
                </ul>
            </div>

            <!-- GROUP: CONTENT -->
            <div>
                <p class="px-4 text-[11px] font-bold text-gray-400 uppercase tracking-widest mb-3">Content</p>
                <ul class="space-y-1">
                    <li>
                        <a href="{{ route('admin.pages.index') }}"
                            class="flex items-center px-4 py-2.5 text-sm font-medium rounded-xl transition-all duration-200 {{ request()->is('admin/pages*') ? 'bg-indigo-50 text-indigo-700' : 'text-gray-600 hover:bg-gray-50 hover:text-gray-900' }}">
                            <svg class="w-5 h-5 mr-3 {{ request()->is('admin/pages*') ? 'text-indigo-600' : 'text-gray-400' }}" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z" />
                            </svg>
                            Pages
                        </a>
                    </li>
                    <li>
                        <a href="{{ route('admin.menus.index') }}"
                            class="flex items-center px-4 py-2.5 text-sm font-medium rounded-xl transition-all duration-200 {{ request()->is('admin/menus*') ? 'bg-indigo-50 text-indigo-700' : 'text-gray-600 hover:bg-gray-50 hover:text-gray-900' }}">
                            <svg class="w-5 h-5 mr-3 {{ request()->is('admin/menus*') ? 'text-indigo-600' : 'text-gray-400' }}" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 6h16M4 12h16M4 18h16" />
                            </svg>
                            Menus
                        </a>
                    </li>
                    <li>
                        <a href="{{ route('admin.sliders.index') }}"
                            class="flex items-center px-4 py-2.5 text-sm font-medium rounded-xl transition-all duration-200 {{ request()->is('admin/sliders*') ? 'bg-indigo-50 text-indigo-700' : 'text-gray-600 hover:bg-gray-50 hover:text-gray-900' }}">
                            <svg class="w-5 h-5 mr-3 {{ request()->is('admin/sliders*') ? 'text-indigo-600' : 'text-gray-400' }}" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 16l4.586-4.586a2 2 0 012.828 0L16 16m-2-2l1.586-1.586a2 2 0 012.828 0L20 14m-6-6h.01M6 20h14a2 2 0 002-2V6a2 2 0 00-2-2H6a2 2 0 00-2 2v12a2 2 0 002 2z" />
                            </svg>
                            Hero Sliders
                        </a>
                    </li>
                </ul>
            </div>

            <!-- GROUP: SYSTEM -->
            <div>
                <p class="px-4 text-[11px] font-bold text-gray-400 uppercase tracking-widest mb-3">System</p>
                <ul class="space-y-1">
                    <li>
                        <a href="{{ route('admin.site-settings.index') }}"
                            class="flex items-center px-4 py-2.5 text-sm font-medium rounded-xl transition-all duration-200 {{ request()->is('admin/site-settings*') ? 'bg-indigo-50 text-indigo-700' : 'text-gray-600 hover:bg-gray-50 hover:text-gray-900' }}">
                            <svg class="w-5 h-5 mr-3 {{ request()->is('admin/site-settings*') ? 'text-indigo-600' : 'text-gray-400' }}" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10.325 4.317c.426-1.756 2.924-1.756 3.35 0a1.724 1.724 0 002.573 1.066c1.543-.94 3.31.826 2.37 2.37a1.724 1.724 0 001.065 2.572c1.756.426 1.756 2.924 0 3.35a1.724 1.724 0 00-1.066 2.573c.94 1.543-.826 3.31-2.37 2.37a1.724 1.724 0 00-2.572 1.065c-.426 1.756-2.924 1.756-3.35 0a1.724 1.724 0 00-2.573-1.066c-1.543.94-3.31-.826-2.37-2.37a1.724 1.724 0 00-1.065-2.572c-1.756-.426-1.756-2.924 0-3.35a1.724 1.724 0 001.066-2.573c-.94-1.543.826-3.31 2.37-2.37.996.608 2.296.07 2.572-1.065z" />
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z" />
                            </svg>
                            Settings
                        </a>
                    </li>
                    <li>
                        <a href="{{ route('admin.district.index') }}"
                            class="flex items-center px-4 py-2.5 text-sm font-medium rounded-xl transition-all duration-200 {{ request()->is('admin/district*') ? 'bg-indigo-50 text-indigo-700' : 'text-gray-600 hover:bg-gray-50 hover:text-gray-900' }}">
                            <svg class="w-5 h-5 mr-3 {{ request()->is('admin/district*') ? 'text-indigo-600' : 'text-gray-400' }}" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17.657 16.657L13.414 20.9a1.998 1.998 0 01-2.827 0l-4.244-4.243a8 8 0 1111.314 0z" />
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 11a3 3 0 11-6 0 3 3 0 016 0z" />
                            </svg>
                            Districts
                        </a>
                    </li>
                </ul>
            </div>
        </nav>

        <!-- Bottom Action -->
        <div class="mt-8 pt-6 border-t border-gray-100">
            <button id="pc-collapse-button" onclick="toggleSidebar()"
                class="flex items-center w-full px-4 py-2.5 text-sm font-medium text-gray-500 hover:text-gray-900 rounded-xl hover:bg-gray-50 transition-all duration-200">
                <svg class="w-5 h-5 mr-3" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 19l-7-7 7-7m8 14l-7-7 7-7" />
                </svg>
                Collapse Sidebar
            </button>
        </div>
    </div>
</aside>

<!-- Expand Trigger -->
<button id="expand-sidebar-btn" onclick="toggleSidebar()"
    class="fixed top-1/2 left-4 -translate-y-1/2 z-[60] w-10 h-10 bg-white border border-gray-200 text-gray-500 rounded-full shadow-lg hover:text-indigo-600 transition-all duration-300 flex items-center justify-center group hidden">
    <svg class="w-5 h-5 transition-transform duration-300 group-hover:translate-x-0.5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 5l7 7-7 7M5 5l7 7-7 7" />
    </svg>
</button>
