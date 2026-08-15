<!-- Dashboard Section -->
<div class="space-y-6">
    <div class="bg-white border border-gray-200 rounded-none p-5 md:p-6">
        <h2 class="text-base md:text-lg font-semibold text-gray-900 uppercase tracking-wide mb-3">Hello, {{ $user->name }}!</h2>
        <p class="text-xs sm:text-sm text-gray-600 leading-relaxed mb-6">
            From your account dashboard you can view your <a href="{{ route('account', 'orders') }}" class="text-emerald-700 hover:underline font-semibold">recent orders</a>, manage your <a href="{{ route('account', 'addresses') }}" class="text-emerald-700 hover:underline font-semibold">shipping and billing addresses</a>, and <a href="{{ route('account', 'details') }}" class="text-emerald-700 hover:underline font-semibold">edit your password and account details</a>.
        </p>
        
        <div class="grid grid-cols-1 sm:grid-cols-3 gap-4">
            <a href="{{ route('account', 'orders') }}" class="border border-gray-200 rounded-none p-4 text-center hover:border-emerald-600 transition-all bg-gray-50/40">
                <div class="w-10 h-10 bg-emerald-50 text-emerald-600 rounded-full flex items-center justify-center mx-auto mb-2.5">
                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.8" d="M16 11V7a4 4 0 00-8 0v4M5 9h14l1 12H4L5 9z"></path></svg>
                </div>
                <h4 class="font-semibold text-gray-900 text-xs sm:text-sm">{{ count($orders) }} Orders</h4>
            </a>
            <a href="{{ route('account', 'addresses') }}" class="border border-gray-200 rounded-none p-4 text-center hover:border-emerald-600 transition-all bg-gray-50/40">
                <div class="w-10 h-10 bg-emerald-50 text-emerald-600 rounded-full flex items-center justify-center mx-auto mb-2.5">
                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.8" d="M17.657 16.657L13.414 20.9a1.998 1.998 0 01-2.827 0l-4.244-4.243a8 8 0 1111.314 0z"></path><path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.8" d="M15 11a3 3 0 11-6 0 3 3 0 016 0z"></path></svg>
                </div>
                <h4 class="font-semibold text-gray-900 text-xs sm:text-sm">{{ count($addresses) }} Addresses</h4>
            </a>
            <a href="{{ route('account', 'wishlist') }}" class="border border-gray-200 rounded-none p-4 text-center hover:border-emerald-600 transition-all bg-gray-50/40">
                <div class="w-10 h-10 bg-emerald-50 text-emerald-600 rounded-full flex items-center justify-center mx-auto mb-2.5">
                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.8" d="M4.318 6.318a4.5 4.5 0 000 6.364L12 20.364l7.682-7.682a4.5 4.5 0 00-6.364-6.364L12 7.636l-1.318-1.318a4.5 4.5 0 00-6.364 0z"></path></svg>
                </div>
                <h4 class="font-semibold text-gray-900 text-xs sm:text-sm">{{ $wishlist->total() }} Wishlist</h4>
            </a>
        </div>
    </div>
</div>
