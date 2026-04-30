<aside id="sidebar"
    class="fixed top-0 left-0 z-40 w-64 h-screen bg-white dark:bg-gray-900 shadow-xl transform transition-transform duration-300">
    <div class="h-full flex flex-col justify-between px-4 py-6 overflow-y-auto">
        <!-- Sidebar Header -->
        <div class="mb-8">
            <div class="flex items-center space-x-3 px-3">
                <div
                    class="w-10 h-10 bg-gradient-to-br from-blue-500 to-purple-600 rounded-xl flex items-center justify-center shadow-lg">
                    <svg class="w-6 h-6 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                            d="M9 19v-6a2 2 0 00-2-2H5a2 2 0 00-2 2v6a2 2 0 002 2h2a2 2 0 002-2zm0 0V9a2 2 0 012-2h2a2 2 0 012 2v10m-6 0a2 2 0 002 2h2a2 2 0 002-2m0 0V5a2 2 0 012-2h2a2 2 0 012 2v14a2 2 0 01-2 2h-2a2 2 0 01-2-2z">
                        </path>
                    </svg>
                </div>
                <div>
                    <h2 class="text-lg font-bold text-gray-800 dark:text-gray-200">AnalyticsPro</h2>
                    <p class="text-xs text-gray-500 dark:text-gray-400">v2.0.1</p>
                </div>
            </div>
        </div>

        <!-- Main Navigation -->
        <nav class="flex-1">
            <ul class="space-y-1">
                <!-- Dashboard -->
                <li>
                    <a href="{{ route('admin.dashboard') }}"
                        class="flex items-center p-3 {{ request()->is('admin/dashboard') ? 'text-blue-600 bg-blue-50 dark:text-blue-400 dark:bg-blue-900/20' : 'text-gray-700 dark:text-gray-300 hover:bg-gray-100 dark:hover:bg-gray-800' }} rounded-lg group transition-all duration-200">
                        <svg class="w-5 h-5" fill="currentColor" viewBox="0 0 20 20">
                            <path
                                d="M10.707 2.293a1 1 0 00-1.414 0l-7 7a1 1 0 001.414 1.414L4 10.414V17a1 1 0 001 1h2a1 1 0 001-1v-2a1 1 0 011-1h2a1 1 0 011 1v2a1 1 0 001 1h2a1 1 0 001-1v-6.586l.293.293a1 1 0 001.414-1.414l-7-7z">
                            </path>
                        </svg>
                        <span class="ml-3 font-medium">Dashboard</span>
                        <span
                            class="ml-auto {{ request()->is('admin/dashboard') ? 'bg-blue-600 text-white' : 'bg-gray-200 text-gray-700 dark:bg-gray-700 dark:text-gray-300' }} text-xs px-2 py-1 rounded-full">Home</span>
                    </a>
                </li>

                <!-- Analytics Dropdown -->
                <li>
                    <button type="button" onclick="toggleDropdown('dropdown-analytics')"
                        class="flex items-center w-full p-3 text-gray-600 dark:text-gray-300 rounded-lg hover:bg-gray-100 dark:hover:bg-gray-800 hover:text-gray-700 dark:hover:text-gray-300 group transition-all duration-200">
                        <svg class="w-5 h-5" fill="currentColor" viewBox="0 0 20 20">
                            <path
                                d="M2 11a1 1 0 011-1h2a1 1 0 011 1v5a1 1 0 01-1 1H3a1 1 0 01-1-1v-5zM8 7a1 1 0 011-1h2a1 1 0 011 1v9a1 1 0 01-1 1H9a1 1 0 01-1-1V7zM14 4a1 1 0 011-1h2a1 1 0 011 1v12a1 1 0 01-1 1h-2a1 1 0 01-1-1V4z">
                            </path>
                        </svg>
                        <span class="ml-3 font-medium flex-1 text-left">Analytics</span>
                        <svg class="w-4 h-4 transition-transform duration-300" fill="none" viewBox="0 0 10 6"
                            id="arrow-dropdown-analytics">
                            <path stroke="currentColor" stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                d="m1 1 4 4 4-4" />
                        </svg>
                    </button>
                    <ul id="dropdown-analytics" class="hidden py-2 space-y-1 ml-4">
                        <li>
                            <a href="#"
                                class="flex items-center p-2 text-gray-500 dark:text-gray-400 rounded-lg hover:bg-gray-100 dark:hover:bg-gray-800 hover:text-gray-700 dark:hover:text-gray-300 transition-all duration-200">
                                <svg class="w-4 h-4 mr-3" fill="currentColor" viewBox="0 0 20 20">
                                    <path d="M10 12a2 2 0 100-4 2 2 0 000 4z"></path>
                                    <path fill-rule="evenodd"
                                        d="M.458 10C1.732 5.943 5.522 3 10 3s8.268 2.943 9.542 7c-1.274 4.057-5.064 7-9.542 7S1.732 14.057.458 10zM14 10a4 4 0 11-8 0 4 4 0 018 0z"
                                        clip-rule="evenodd"></path>
                                </svg>
                                Overview
                            </a>
                        </li>
                        <li>
                            <a href="#"
                                class="flex items-center p-2 text-gray-500 dark:text-gray-400 rounded-lg hover:bg-gray-100 dark:hover:bg-gray-800 hover:text-gray-700 dark:hover:text-gray-300 transition-all duration-200">
                                <svg class="w-4 h-4 mr-3" fill="currentColor" viewBox="0 0 20 20">
                                    <path fill-rule="evenodd"
                                        d="M10 18a8 8 0 100-16 8 8 0 000 16zm1-12a1 1 0 10-2 0v4a1 1 0 00.293.707l2.828 2.829a1 1 0 101.415-1.415L11 9.586V6z"
                                        clip-rule="evenodd"></path>
                                </svg>
                                Real-time
                            </a>
                        </li>
                        <li>
                            <a href="#"
                                class="flex items-center p-2 text-gray-500 dark:text-gray-400 rounded-lg hover:bg-gray-100 dark:hover:bg-gray-800 hover:text-gray-700 dark:hover:text-gray-300 transition-all duration-200">
                                <svg class="w-4 h-4 mr-3" fill="currentColor" viewBox="0 0 20 20">
                                    <path fill-rule="evenodd"
                                        d="M6 2a2 2 0 00-2 2v12a2 2 0 002 2h8a2 2 0 002-2V4a2 2 0 00-2-2H6zm1 2a1 1 0 000 2h6a1 1 0 100-2H7zm6 7a1 1 0 011 1v3a1 1 0 11-2 0v-3a1 1 0 011-1zm-3 3a1 1 0 100 2h.01a1 1 0 100-2H10zm-4 1a1 1 0 011-1h.01a1 1 0 110 2H7a1 1 0 01-1-1zm1-4a1 1 0 100 2h.01a1 1 0 100-2H7zm2 1a1 1 0 011-1h.01a1 1 0 110 2H10a1 1 0 01-1-1zm1-4a1 1 0 100 2h.01a1 1 0 100-2H10z"
                                        clip-rule="evenodd"></path>
                                </svg>
                                Reports
                            </a>
                        </li>
                    </ul>
                </li>



                <!-- Marketing Dropdown -->
                <li>
                    <button type="button" onclick="toggleDropdown('dropdown-marketing')"
                        class="flex items-center w-full p-3 {{ request()->is('admin/coupons*') || request()->is('admin/flash-sales*') ? 'text-blue-600 bg-blue-50 dark:text-blue-400 dark:bg-blue-900/20' : 'text-gray-600 dark:text-gray-300 hover:bg-gray-100 dark:hover:bg-gray-800' }} rounded-lg group transition-all duration-200"
                        id="marketing-button">
                        <svg class="w-5 h-5" fill="currentColor" viewBox="0 0 20 20">
                            <path fill-rule="evenodd"
                                d="M3 3a1 1 0 000 2v8a2 2 0 002 2h2.586l-1.293 1.293a1 1 0 101.414 1.414L10 15.414l2.293 2.293a1 1 0 001.414-1.414L12.414 15H15a2 2 0 002-2V5a1 1 0 100-2H3zm11.707 4.707a1 1 0 00-1.414-1.414L10 9.586 8.707 8.293a1 1 0 00-1.414 0l-2 2a1 1 0 101.414 1.414L8 10.414l1.293 1.293a1 1 0 001.414 0l4-4z"
                                clip-rule="evenodd"></path>
                        </svg>
                        <span class="ml-3 font-medium flex-1 text-left">Marketing</span>
                        <svg class="w-4 h-4 transition-transform duration-300 {{ request()->is('admin/coupons*') || request()->is('admin/flash-sales*') ? 'rotate-180' : '' }}"
                            fill="none" viewBox="0 0 10 6" id="arrow-dropdown-marketing">
                            <path stroke="currentColor" stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                d="m1 1 4 4 4-4" />
                        </svg>
                    </button>
                    <ul id="dropdown-marketing"
                        class="{{ request()->is('admin/coupons*') || request()->is('admin/flash-sales*') ? '' : 'hidden' }} py-2 space-y-1 ml-4"
                        data-initial-state="{{ request()->is('admin/coupons*') || request()->is('admin/flash-sales*') ? 'expanded' : 'collapsed' }}">
                        <li>
                            <a href="{{ route('admin.coupons.index') }}"
                                class="flex items-center p-2 {{ request()->is('admin/coupons*') ? 'text-blue-600 bg-blue-50 dark:text-blue-400 dark:bg-blue-900/20' : 'text-gray-500 dark:text-gray-400 hover:bg-gray-100 dark:hover:bg-gray-800 hover:text-gray-700 dark:hover:text-gray-300' }} rounded-lg transition-all duration-200">
                                <svg class="w-4 h-4 mr-3" fill="currentColor" viewBox="0 0 20 20">
                                    <path fill-rule="evenodd"
                                        d="M5 5a3 3 0 00-3 3v6a3 3 0 003 3h10a3 3 0 003-3V8a3 3 0 00-3-3H5zm-1 9V8a1 1 0 011-1h2a1 1 0 011 1v6a1 1 0 01-1 1H5a1 1 0 01-1-1zm6 0V8a1 1 0 011-1h2a1 1 0 011 1v6a1 1 0 01-1 1h-2a1 1 0 01-1-1zm6 0V8a1 1 0 011-1h2a1 1 0 011 1v6a1 1 0 01-1 1h-2a1 1 0 01-1-1z"
                                        clip-rule="evenodd"></path>
                                </svg>
                                Coupons
                            </a>
                        </li>
                        <li>
                            <a href="{{ route('admin.flash-sales.index') }}"
                                class="flex items-center p-2 {{ request()->is('admin/flash-sales*') ? 'text-blue-600 bg-blue-50 dark:text-blue-400 dark:bg-blue-900/20' : 'text-gray-500 dark:text-gray-400 hover:bg-gray-100 dark:hover:bg-gray-800 hover:text-gray-700 dark:hover:text-gray-300' }} rounded-lg transition-all duration-200">
                                <svg class="w-4 h-4 mr-3" fill="currentColor" viewBox="0 0 20 20">
                                    <path fill-rule="evenodd"
                                        d="M6 2a1 1 0 00-1 1v1H4a2 2 0 00-2 2v10a2 2 0 002 2h12a2 2 0 002-2V6a2 2 0 00-2-2h-1V3a1 1 0 10-2 0v1H7V3a1 1 0 00-1-1zm0 5a1 1 0 000 2h8a1 1 0 100-2H6z"
                                        clip-rule="evenodd"></path>
                                </svg>
                                Flash Sales
                            </a>
                        </li>
                        <!-- Items removed: Campaigns, Promotions, Discounts -->
                    </ul>
                </li>

                <!-- Products Dropdown -->
                <li>
                    <button type="button" onclick="toggleDropdown('products-dropdown')"
                        class="flex items-center w-full p-3 {{ request()->is('admin/products*') || request()->is('admin/brands*') || request()->is('admin/attributes*') || request()->is('admin/categories*') || request()->is('admin/attribute-values*') ? 'text-blue-600 bg-blue-50 dark:text-blue-400 dark:bg-blue-900/20' : 'text-gray-600 dark:text-gray-300 hover:bg-gray-100 dark:hover:bg-gray-800' }} rounded-lg group transition-all duration-200"
                        id="products-button">
                        <svg class="w-5 h-5" fill="currentColor" viewBox="0 0 20 20">
                            <path fill-rule="evenodd"
                                d="M10 2a4 4 0 00-4 4v1H5a1 1 0 00-.994.89l-1 9A1 1 0 004 18h12a1 1 0 00.994-1.11l-1-9A1 1 0 0015 7h-1V6a4 4 0 00-4-4zm2 5V6a2 2 0 10-4 0v1h4zm-6 3a1 1 0 112 0 1 1 0 01-2 0zm7-1a1 1 0 100 2 1 1 0 000-2z"
                                clip-rule="evenodd"></path>
                        </svg>
                        <span class="ml-3 font-medium flex-1 text-left">Products</span>
                        <svg class="w-4 h-4 transition-transform duration-300 {{ request()->is('admin/products*') || request()->is('admin/brands*') || request()->is('admin/attributes*') || request()->is('admin/categories*') || request()->is('admin/attribute-values*') ? 'rotate-180' : '' }}"
                            fill="none" viewBox="0 0 10 6" id="arrow-products-dropdown">
                            <path stroke="currentColor" stroke-linecap="round" stroke-linejoin="round"
                                stroke-width="2" d="m1 1 4 4 4-4" />
                        </svg>
                    </button>
                    <ul id="products-dropdown"
                        class="{{ request()->is('admin/products*') || request()->is('admin/brands*') || request()->is('admin/attributes*') || request()->is('admin/categories*') || request()->is('admin/attribute-values*') ? '' : 'hidden' }} py-2 space-y-1 ml-4"
                        data-initial-state="{{ request()->is('admin/products*') || request()->is('admin/brands*') || request()->is('admin/attributes*') || request()->is('admin/categories*') || request()->is('admin/attribute-values*') ? 'expanded' : 'collapsed' }}">
                        <li>
                            <a href="{{ route('admin.products.index') }}"
                                class="flex items-center p-2 {{ request()->is('admin/products*') ? 'text-blue-600 bg-blue-50 dark:text-blue-400 dark:bg-blue-900/20' : 'text-gray-500 dark:text-gray-400 hover:bg-gray-100 dark:hover:bg-gray-800 hover:text-gray-700 dark:hover:text-gray-300' }} rounded-lg transition-all duration-200">
                                <svg class="w-4 h-4 mr-3" fill="currentColor" viewBox="0 0 20 20">
                                    <path fill-rule="evenodd"
                                        d="M10 2a4 4 0 00-4 4v1H5a1 1 0 00-.994.89l-1 9A1 1 0 004 18h12a1 1 0 00.994-1.11l-1-9A1 1 0 0015 7h-1V6a4 4 0 00-4-4zm2 5V6a2 2 0 10-4 0v1h4zm-6 3a1 1 0 112 0 1 1 0 01-2 0zm7-1a1 1 0 100 2 1 1 0 000-2z"
                                        clip-rule="evenodd"></path>
                                </svg>
                                Products
                            </a>
                        </li>
                        <li>
                            <a href="{{ route('admin.categories.index') }}"
                                class="flex items-center p-2 {{ request()->is('admin/categories*') ? 'text-blue-600 bg-blue-50 dark:text-blue-400 dark:bg-blue-900/20' : 'text-gray-500 dark:text-gray-400 hover:bg-gray-100 dark:hover:bg-gray-800 hover:text-gray-700 dark:hover:text-gray-300' }} rounded-lg transition-all duration-200">
                                <svg class="w-4 h-4 mr-3" fill="currentColor" viewBox="0 0 20 20">
                                    <path fill-rule="evenodd"
                                        d="M3 4a1 1 0 011-1h12a1 1 0 110 2H4a1 1 0 01-1-1zm0 4a1 1 0 011-1h12a1 1 0 110 2H4a1 1 0 01-1-1zm0 4a1 1 0 011-1h12a1 1 0 110 2H4a1 1 0 01-1-1zm0 4a1 1 0 011-1h12a1 1 0 110 2H4a1 1 0 01-1-1z"
                                        clip-rule="evenodd"></path>
                                </svg>
                                Categories
                            </a>
                        </li>
                        <li>
                            <a href="{{ route('admin.brands.index') }}"
                                class="flex items-center p-2 {{ request()->is('admin/brands*') ? 'text-blue-600 bg-blue-50 dark:text-blue-400 dark:bg-blue-900/20' : 'text-gray-500 dark:text-gray-400 hover:bg-gray-100 dark:hover:bg-gray-800 hover:text-gray-700 dark:hover:text-gray-300' }} rounded-lg transition-all duration-200">
                                <svg class="w-4 h-4 mr-3" fill="currentColor" viewBox="0 0 20 20">
                                    <path fill-rule="evenodd"
                                        d="M18 10a8 8 0 11-16 0 8 8 0 0116 0zm-7-4a1 1 0 11-2 0 1 1 0 012 0zM9 9a1 1 0 000 2v3a1 1 0 001 1h1a1 1 0 100-2v-3a1 1 0 00-1-1H9z"
                                        clip-rule="evenodd"></path>
                                </svg>
                                Brands
                            </a>
                        </li>

                        <li>
                            <a href="{{ route('admin.attributes.index') }}"
                                class="flex items-center p-2 {{ request()->is('admin/attributes*') ? 'text-blue-600 bg-blue-50 dark:text-blue-400 dark:bg-blue-900/20' : 'text-gray-500 dark:text-gray-400 hover:bg-gray-100 dark:hover:bg-gray-800 hover:text-gray-700 dark:hover:text-gray-300' }} rounded-lg transition-all duration-200">
                                <svg class="w-4 h-4 mr-3" fill="currentColor" viewBox="0 0 20 20">
                                    <path fill-rule="evenodd"
                                        d="M11.49 3.17c-.38-1.56-2.6-1.56-2.98 0a1.532 1.532 0 01-2.286.948c-1.372-.836-2.942.734-2.106 2.106.54.886.061 2.042-.947 2.287-1.561.379-1.561 2.6 0 2.978a1.532 1.532 0 01.947 2.287c-.836 1.372.734 2.942 2.106 2.106a1.532 1.532 0 012.287.947c.379 1.561 2.6 1.561 2.978 0a1.533 1.533 0 012.287-.947c1.372.836 2.942-.734 2.106-2.106a1.533 1.533 0 01.947-2.287c1.561-.379 1.561-2.6 0-2.978a1.532 1.532 0 01-.947-2.287c.836-1.372-.734-2.942-2.106-2.106a1.532 1.532 0 01-2.287-.947zM10 13a3 3 0 100-6 3 3 0 000 6z"
                                        clip-rule="evenodd"></path>
                                </svg>
                                Attribute
                            </a>
                        </li>

                        <li>
                            <a href="{{ route('admin.attribute-values.index') }}"
                                class="flex items-center p-2 {{ request()->is('admin/attribute-values*') ? 'text-blue-600 bg-blue-50 dark:text-blue-400 dark:bg-blue-900/20' : 'text-gray-500 dark:text-gray-400 hover:bg-gray-100 dark:hover:bg-gray-800 hover:text-gray-700 dark:hover:text-gray-300' }} rounded-lg transition-all duration-200">
                                <svg class="w-4 h-4 mr-3" fill="currentColor" viewBox="0 0 20 20">
                                    <path fill-rule="evenodd"
                                        d="M10 18a8 8 0 100-16 8 8 0 000 16zm1-11a1 1 0 10-2 0v2H7a1 1 0 100 2h2v2a1 1 0 102 0v-2h2a1 1 0 100-2h-2V7z"
                                        clip-rule="evenodd"></path>
                                </svg>
                                Attribute Values
                            </a>
                        </li>
                        <li>
                            <a href="#"
                                class="flex items-center p-2 text-gray-500 dark:text-gray-400 rounded-lg hover:bg-gray-100 dark:hover:bg-gray-800 hover:text-gray-700 dark:hover:text-gray-300 transition-all duration-200">
                                <svg class="w-4 h-4 mr-3" fill="currentColor" viewBox="0 0 20 20">
                                    <path
                                        d="M9 6a3 3 0 11-6 0 3 3 0 016 0zM17 6a3 3 0 11-6 0 3 3 0 016 0zM12.93 17c.046-.327.07-.66.07-1a6.97 6.97 0 00-1.5-4.33A5 5 0 0119 16v1h-6.07zM6 11a5 5 0 015 5v1H1v-1a5 5 0 015-5z">
                                    </path>
                                </svg>
                                Customers
                            </a>
                        </li>
                    </ul>
                </li>

                <!-- CRM -->
                {{-- <li>
                    <a href="#"
                        class="flex items-center p-3 text-gray-600 dark:text-gray-300 rounded-lg hover:bg-gray-100 dark:hover:bg-gray-800 hover:text-gray-700 dark:hover:text-gray-300 group transition-all duration-200">
                        <svg class="w-5 h-5" fill="currentColor" viewBox="0 0 20 20">
                            <path d="M9 6a3 3 0 11-6 0 3 3 0 016 0zM17 6a3 3 0 11-6 0 3 3 0 016 0zM12.93 17c.046-.327.07-.66.07-1a6.97 6.97 0 00-1.5-4.33A5 5 0 0119 16v1h-6.07zM6 11a5 5 0 015 5v1H1v-1a5 5 0 015-5z"></path>
                        </svg>
                        <span class="ml-3 font-medium">CRM</span>
                    </a>
                </li> --}}



                <!-- Divider -->
                <li class="border-t border-gray-200 dark:border-gray-700 my-4"></li>





                <!-- Settings Dropdown -->
                <li>
                    <button type="button" onclick="toggleDropdown('dropdown-settings')"
                        class="flex items-center w-full p-3 {{ request()->is('admin/district*') ? 'text-blue-600 bg-blue-50 dark:text-blue-400 dark:bg-blue-900/20' : 'text-gray-600 dark:text-gray-300 hover:bg-gray-100 dark:hover:bg-gray-800' }} rounded-lg group transition-all duration-200"
                        id="settings-button">
                        <svg class="w-5 h-5" fill="currentColor" viewBox="0 0 20 20">
                            <path fill-rule="evenodd"
                                d="M11.49 3.17c-.38-1.56-2.6-1.56-2.98 0a1.532 1.532 0 01-2.286.948c-1.372-.836-2.942.734-2.106 2.106.54.886.061 2.042-.947 2.287-1.561.379-1.561 2.6 0 2.978a1.532 1.532 0 01.947 2.287c-.836 1.372.734 2.942 2.106 2.106a1.532 1.532 0 012.287.947c.379 1.561 2.6 1.561 2.978 0a1.533 1.533 0 012.287-.947c1.372.836 2.942-.734 2.106-2.106a1.533 1.533 0 01.947-2.287c1.561-.379 1.561-2.6 0-2.978a1.532 1.532 0 01-.947-2.287c.836-1.372-.734-2.942-2.106-2.106a1.532 1.532 0 01-2.287-.947zM10 13a3 3 0 100-6 3 3 0 000 6z"
                                clip-rule="evenodd"></path>
                        </svg>
                        <span class="ml-3 font-medium flex-1 text-left">Settings</span>
                        <svg class="w-4 h-4 transition-transform duration-300 {{ request()->is('admin/district*') ? 'rotate-180' : '' }}"
                            fill="none" viewBox="0 0 10 6" id="arrow-dropdown-settings">
                            <path stroke="currentColor" stroke-linecap="round" stroke-linejoin="round"
                                stroke-width="2" d="m1 1 4 4 4-4" />
                        </svg>
                    </button>
                    <ul id="dropdown-settings"
                        class="{{ request()->is('admin/district*') ? '' : 'hidden' }} py-2 space-y-1 ml-4"
                        data-initial-state="{{ request()->is('admin/district*') || request()->is('admin/district*') ? 'expanded' : 'collapsed' }}">
                        <li>
                            <a href="{{ route('admin.district.index') }}"
                                class="flex items-center p-2 {{ request()->is('admin/district*') ? 'text-blue-600 bg-blue-50 dark:text-blue-400 dark:bg-blue-900/20' : 'text-gray-500 dark:text-gray-400 hover:bg-gray-100 dark:hover:bg-gray-800 hover:text-gray-700 dark:hover:text-gray-300' }} rounded-lg transition-all duration-200">
                                <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24"
                                    stroke-width="1.5" stroke="currentColor" class="w-5 h-5 mr-3">
                                    <path stroke-linecap="round" stroke-linejoin="round"
                                        d="M12 11c1.657 0 3-1.343 3-3S13.657 5 12 5s-3 1.343-3 3 1.343 3 3 3z" />
                                    <path stroke-linecap="round" stroke-linejoin="round"
                                        d="M12 22s8-4.5 8-11a8 8 0 10-16 0c0 6.5 8 11 8 11z" />
                                </svg>
                                District
                            </a>
                        </li>
                    </ul>
                </li>
            </ul>
        </nav>

        <!-- Bottom Section -->
        <div class="mt-auto">
            <!-- Sidebar Toggle Button (hidden on mobile, visible on PC) -->
            <button id="pc-collapse-button" onclick="toggleSidebar()"
                class="w-full items-center justify-center p-3 text-gray-500 dark:text-black rounded-lg hover:bg-gray-100 dark:hover:bg-gray-800 hover:text-gray-700 dark:hover:text-black transition-all duration-200">
                <svg class="w-5 h-5" fill="currentColor" viewBox="0 0 20 20">
                    <path
                        d="M2 4.75A.75.75 0 012.75 4h14.5a.75.75 0 010 1.5H2.75A.75.75 0 012 4.75zm0 10.5a.75.75 0 01.75-.75h14.5a.75.75 0 010 1.5H2.75A.75.75 0 01-.75-.75zM2 10a.75.75 0 01.75-.75h14.5a.75.75 0 010 1.5H2.75A.75.75 0 012 10z" />
                </svg>
                <span class="ml-2 text-sm">Collapse</span>
            </button>
        </div>
    </div>
</aside>

<!-- Professional Expand Button (shows when sidebar is hidden) -->
<button id="expand-sidebar-btn" onclick="toggleSidebar()"
    class="fixed top-1/2 left-4 transform -translate-y-1/2 z-[60] w-12 h-12 bg-gradient-to-br from-blue-500 to-purple-600 text-white rounded-full shadow-2xl hover:shadow-3xl hover:from-blue-600 hover:to-purple-700 transition-all duration-300 flex items-center justify-center group hidden">
    <svg class="w-6 h-6 transition-transform duration-300 group-hover:scale-110" fill="none" stroke="currentColor"
        viewBox="0 0 24 24">
        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 6h16M4 12h16M4 18h16" />
    </svg>
</button>
