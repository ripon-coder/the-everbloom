<!-- Dashboard Tab -->
<div x-show="activeTab === 'dashboard'" x-cloak class="space-y-6">
    <div class="bg-white border border-gray-200 rounded-lg p-6 md:p-8 shadow-sm">
        <h2 class="text-xl font-bold text-gray-900 uppercase tracking-widest mb-4">Hello, John Doe!</h2>
        <p class="text-sm text-gray-600 leading-relaxed mb-6">
            From your account dashboard you can view your <button @click="activeTab = 'orders'" class="text-red-600 hover:underline">recent orders</button>, manage your <button @click="activeTab = 'addresses'" class="text-red-600 hover:underline">shipping and billing addresses</button>, and <button @click="activeTab = 'details'" class="text-red-600 hover:underline">edit your password and account details</button>.
        </p>
        
        <div class="grid grid-cols-1 sm:grid-cols-3 gap-4">
            <div class="border border-gray-200 rounded-lg p-5 text-center hover:border-red-500 hover:shadow-md transition-all cursor-pointer" @click="activeTab = 'orders'">
                <div class="w-12 h-12 bg-red-50 text-red-600 rounded-full flex items-center justify-center mx-auto mb-3">
                    <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M16 11V7a4 4 0 00-8 0v4M5 9h14l1 12H4L5 9z"></path></svg>
                </div>
                <h4 class="font-bold text-gray-900">12 Orders</h4>
            </div>
            <div class="border border-gray-200 rounded-lg p-5 text-center hover:border-red-500 hover:shadow-md transition-all cursor-pointer" @click="activeTab = 'addresses'">
                <div class="w-12 h-12 bg-red-50 text-red-600 rounded-full flex items-center justify-center mx-auto mb-3">
                    <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M17.657 16.657L13.414 20.9a1.998 1.998 0 01-2.827 0l-4.244-4.243a8 8 0 1111.314 0z"></path><path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M15 11a3 3 0 11-6 0 3 3 0 016 0z"></path></svg>
                </div>
                <h4 class="font-bold text-gray-900">2 Addresses</h4>
            </div>
            <div class="border border-gray-200 rounded-lg p-5 text-center hover:border-red-500 hover:shadow-md transition-all cursor-pointer">
                <div class="w-12 h-12 bg-red-50 text-red-600 rounded-full flex items-center justify-center mx-auto mb-3">
                    <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M4.318 6.318a4.5 4.5 0 000 6.364L12 20.364l7.682-7.682a4.5 4.5 0 00-6.364-6.364L12 7.636l-1.318-1.318a4.5 4.5 0 00-6.364 0z"></path></svg>
                </div>
                <h4 class="font-bold text-gray-900">5 Wishlist</h4>
            </div>
        </div>
    </div>
</div>
