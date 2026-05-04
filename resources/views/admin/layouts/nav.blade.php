<nav class="bg-white border-b border-gray-200 fixed top-0 left-0 right-0 z-50">
    <div class="px-4 flex items-center justify-between h-16">
        <!-- Brand -->
        <a href="{{ route('admin.dashboard') }}" class="flex items-center space-x-3">
            <div class="w-9 h-9 bg-gradient-to-tr from-indigo-600 to-violet-600 rounded-lg flex items-center justify-center shadow-sm">
                <svg class="w-5 h-5 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M13 10V3L4 14h7v7l9-11h-7z" />
                </svg>
            </div>
            <div class="hidden sm:block">
                <h1 class="text-lg font-bold text-gray-900 tracking-tight leading-none">EverBloom</h1>
                <span class="text-[10px] font-bold text-indigo-600 uppercase tracking-[0.2em]">Management</span>
            </div>
        </a>

        <!-- Right Side -->
        <div class="flex items-center space-x-4">
            <!-- Search -->
            <div class="hidden md:block relative">
                <div class="absolute inset-y-0 left-0 flex items-center pl-3 pointer-events-none text-gray-400">
                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z" />
                    </svg>
                </div>
                <input type="text" placeholder="Search..." class="w-64 pl-10 pr-4 py-2 bg-gray-50 border border-gray-200 rounded-xl text-sm focus:ring-2 focus:ring-indigo-500/10 focus:border-indigo-500 outline-none transition-all">
            </div>

            <!-- Profile Menu -->
            <div class="relative flex items-center space-x-3">
                <button type="button" id="user-menu-button" data-dropdown-toggle="user-dropdown" class="flex items-center space-x-3 p-1 rounded-full hover:bg-gray-50 transition-all focus:ring-4 focus:ring-indigo-50">
                    <img class="w-8 h-8 rounded-full border border-gray-100" 
                         src="https://ui-avatars.com/api/?name={{ urlencode(Auth::guard('admin')->user()->name) }}&background=6366f1&color=fff" 
                         alt="{{ Auth::guard('admin')->user()->name }}">
                </button>

                <!-- Dropdown -->
                <div id="user-dropdown" class="hidden z-50 w-56 bg-white rounded-2xl shadow-xl border border-gray-100 overflow-hidden">
                    <div class="px-5 py-4 bg-gray-50/50 border-b border-gray-100">
                        <p class="text-sm font-bold text-gray-900 truncate">{{ Auth::guard('admin')->user()->name }}</p>
                        <p class="text-[11px] font-medium text-gray-500 truncate mt-0.5">{{ Auth::guard('admin')->user()->email }}</p>
                    </div>
                    <div class="p-2">
                        <a href="{{ route('admin.profile.edit') }}" class="flex items-center px-3 py-2 text-sm font-medium text-gray-700 rounded-xl hover:bg-indigo-50 hover:text-indigo-600 transition-all group">
                            <svg class="w-4 h-4 mr-3 text-gray-400 group-hover:text-indigo-500" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z" />
                            </svg>
                            My Profile
                        </a>
                        <form action="{{ route('admin.logout') }}" method="POST" class="mt-1">
                            @csrf
                            <button type="submit" class="w-full flex items-center px-3 py-2 text-sm font-medium text-rose-600 rounded-xl hover:bg-rose-50 transition-all group">
                                <svg class="w-4 h-4 mr-3 text-rose-400 group-hover:text-rose-500" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 16l4-4m0 0l-4-4m4 4H7m6 4v1a3 3 0 01-3 3H6a3 3 0 01-3-3V7a3 3 0 013-3h4a3 3 0 013 3v1" />
                                </svg>
                                Sign out
                            </button>
                        </form>
                    </div>
                </div>
            </div>

            <!-- Mobile Toggle -->
            <button data-drawer-target="sidebar" data-drawer-toggle="sidebar" class="sm:hidden p-2 text-gray-500 hover:bg-gray-50 rounded-xl transition-all">
                <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 6h16M4 12h16M4 18h16" />
                </svg>
            </button>
        </div>
    </div>
</nav>
