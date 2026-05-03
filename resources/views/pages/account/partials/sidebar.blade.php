<!-- Sidebar / Navigation Menu -->
<div class="w-full md:w-64 flex-shrink-0">
    <!-- Desktop Sidebar Header -->
    <div class="hidden md:block bg-white border border-gray-200 rounded-t-lg overflow-hidden shadow-sm">
        <div class="p-6 bg-slate-900 text-white">
            <div class="flex items-center gap-4">
                <div class="w-12 h-12 bg-red-600 rounded-full flex items-center justify-center flex-shrink-0 text-white font-bold text-xl uppercase shadow-lg">
                    {{ substr($user->name, 0, 1) }}{{ str_contains($user->name, ' ') ? substr(strrchr($user->name, " "), 1, 1) : '' }}
                </div>
                <div class="min-w-0">
                    <h3 class="font-bold tracking-wide truncate">{{ $user->name }}</h3>
                    <p class="text-[10px] text-gray-400 truncate uppercase tracking-widest font-medium">{{ $user->email }}</p>
                </div>
            </div>
        </div>
    </div>

    <!-- Navigation Links -->
    <div class="bg-white border border-gray-200 md:border-t-0 rounded-lg md:rounded-t-none md:rounded-b-lg shadow-sm overflow-hidden">
        <!-- Mobile Header (Simplified) -->
        <div class="md:hidden p-4 border-b border-gray-100 flex items-center gap-3 bg-slate-900 text-white">
            <div class="w-10 h-10 bg-red-600 rounded-full flex items-center justify-center flex-shrink-0 text-white font-bold text-lg uppercase">
                {{ substr($user->name, 0, 1) }}
            </div>
            <div class="min-w-0">
                <h3 class="font-bold text-sm truncate">{{ $user->name }}</h3>
            </div>
        </div>

        <ul class="flex md:flex-col overflow-x-auto md:overflow-x-visible no-scrollbar py-2 md:py-3">
            <li class="flex-shrink-0 md:flex-shrink">
                <a href="{{ route('account', 'dashboard') }}" class="flex items-center gap-3 px-6 py-3.5 text-xs md:text-sm font-bold uppercase tracking-wider transition-all border-b-2 md:border-b-0 md:border-l-4 {{ $section === 'dashboard' ? 'border-red-600 text-red-600 bg-red-50/50' : 'border-transparent text-gray-500 hover:text-red-600 hover:bg-gray-50' }}">
                    <svg class="w-5 h-5 flex-shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M4 6a2 2 0 012-2h2a2 2 0 012 2v2a2 2 0 01-2 2H6a2 2 0 01-2-2V6zM14 6a2 2 0 012-2h2a2 2 0 012 2v2a2 2 0 01-2 2h-2a2 2 0 01-2-2V6zM4 16a2 2 0 012-2h2a2 2 0 012 2v2a2 2 0 01-2 2H6a2 2 0 01-2-2v-2zM14 16a2 2 0 012-2h2a2 2 0 012 2v2a2 2 0 01-2 2h-2a2 2 0 01-2-2v-2z"></path></svg>
                    <span>Dashboard</span>
                </a>
            </li>
            <li class="flex-shrink-0 md:flex-shrink">
                <a href="{{ route('account', 'orders') }}" class="flex items-center gap-3 px-6 py-3.5 text-xs md:text-sm font-bold uppercase tracking-wider transition-all border-b-2 md:border-b-0 md:border-l-4 {{ $section === 'orders' ? 'border-red-600 text-red-600 bg-red-50/50' : 'border-transparent text-gray-500 hover:text-red-600 hover:bg-gray-50' }}">
                    <svg class="w-5 h-5 flex-shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M16 11V7a4 4 0 00-8 0v4M5 9h14l1 12H4L5 9z"></path></svg>
                    <span>Orders</span>
                </a>
            </li>
            <li class="flex-shrink-0 md:flex-shrink">
                <a href="{{ route('account', 'addresses') }}" class="flex items-center gap-3 px-6 py-3.5 text-xs md:text-sm font-bold uppercase tracking-wider transition-all border-b-2 md:border-b-0 md:border-l-4 {{ $section === 'addresses' ? 'border-red-600 text-red-600 bg-red-50/50' : 'border-transparent text-gray-500 hover:text-red-600 hover:bg-gray-50' }}">
                    <svg class="w-5 h-5 flex-shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M17.657 16.657L13.414 20.9a1.998 1.998 0 01-2.827 0l-4.244-4.243a8 8 0 1111.314 0z"></path><path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M15 11a3 3 0 11-6 0 3 3 0 016 0z"></path></svg>
                    <span>Addresses</span>
                </a>
            </li>
            <li class="flex-shrink-0 md:flex-shrink">
                <a href="{{ route('account', 'wishlist') }}" class="flex items-center gap-3 px-6 py-3.5 text-xs md:text-sm font-bold uppercase tracking-wider transition-all border-b-2 md:border-b-0 md:border-l-4 {{ $section === 'wishlist' ? 'border-red-600 text-red-600 bg-red-50/50' : 'border-transparent text-gray-500 hover:text-red-600 hover:bg-gray-50' }}">
                    <svg class="w-5 h-5 flex-shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M4.318 6.318a4.5 4.5 0 000 6.364L12 20.364l7.682-7.682a4.5 4.5 0 00-6.364-6.364L12 7.636l-1.318-1.318a4.5 4.5 0 00-6.364 0z"></path></svg>
                    <span>Wishlist</span>
                </a>
            </li>
            <li class="flex-shrink-0 md:flex-shrink">
                <a href="{{ route('account', 'details') }}" class="flex items-center gap-3 px-6 py-3.5 text-xs md:text-sm font-bold uppercase tracking-wider transition-all border-b-2 md:border-b-0 md:border-l-4 {{ $section === 'details' ? 'border-red-600 text-red-600 bg-red-50/50' : 'border-transparent text-gray-500 hover:text-red-600 hover:bg-gray-50' }}">
                    <svg class="w-5 h-5 flex-shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M10.325 4.317c.426-1.756 2.924-1.756 3.35 0a1.724 1.724 0 002.573 1.066c1.543-.94 3.31.826 2.37 2.37a1.724 1.724 0 001.065 2.572c1.756.426 1.756 2.924 0 3.35a1.724 1.724 0 00-1.066 2.573c.94 1.543-.826 3.31-2.37 2.37a1.724 1.724 0 00-2.572 1.065c-.426 1.756-2.924 1.756-3.35 0a1.724 1.724 0 00-2.573-1.066c-1.543.94-3.31-.826-2.37-2.37a1.724 1.724 0 00-1.065-2.572c-1.756-.426-1.756-2.924 0-3.35a1.724 1.724 0 001.066-2.573c-.94-1.543.826-3.31 2.37-2.37.996.608 2.296.07 2.572-1.065z"></path><path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z"></path></svg>
                    <span>Details</span>
                </a>
            </li>
            <li class="hidden md:block border-t border-gray-100 mt-2 pt-2">
                <form method="POST" action="{{ route('logout') }}">
                    @csrf
                    <button type="submit" class="flex items-center gap-3 w-full text-left px-6 py-3 text-xs md:text-sm font-bold uppercase tracking-wider text-gray-400 hover:text-red-600 transition-all group">
                        <svg class="w-5 h-5 flex-shrink-0 text-gray-300 group-hover:text-red-600" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M17 16l4-4m0 0l-4-4m4 4H7m6 4v1a3 3 0 01-3 3H6a3 3 0 01-3-3V7a3 3 0 013-3h4a3 3 0 013 3v1"></path></svg>
                        <span>Logout</span>
                    </button>
                </form>
            </li>
        </ul>
    </div>
</div>

<style>
    .no-scrollbar::-webkit-scrollbar { display: none; }
    .no-scrollbar { -ms-overflow-style: none; scrollbar-width: none; }
</style>
