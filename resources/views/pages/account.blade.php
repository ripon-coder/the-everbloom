<x-layouts.app title="My Account | Everbloom">
    <div class="bg-gray-50 py-6 md:py-10" x-data="{ activeTab: 'dashboard' }">
        <div class="max-w-[1200px] mx-auto px-4 sm:px-6 lg:px-8">
            
            <!-- Breadcrumbs -->
            <nav class="flex text-xs font-medium text-gray-500 uppercase tracking-wider mb-6 md:mb-8">
                <a href="{{ route('home') }}" class="hover:text-red-600 transition-colors">Home</a>
                <span class="mx-2">/</span>
                <span class="text-gray-900">My Account</span>
            </nav>

            <div class="flex flex-col md:flex-row gap-8">
                
                <!-- Sidebar Menu -->
                <div class="w-full md:w-64 flex-shrink-0">
                    <div class="bg-white border border-gray-200 rounded-lg overflow-hidden shadow-sm sticky top-24">
                        <div class="p-6 border-b border-gray-100 bg-slate-900 text-white">
                            <div class="flex items-center gap-4">
                                <div class="w-12 h-12 bg-gray-200 rounded-full flex items-center justify-center flex-shrink-0 text-slate-800 font-bold text-xl">
                                    JD
                                </div>
                                <div>
                                    <h3 class="font-bold tracking-wide">John Doe</h3>
                                    <p class="text-[11px] text-gray-400">john.doe@example.com</p>
                                </div>
                            </div>
                        </div>
                        <ul class="flex flex-col py-2">
                            <li>
                                <button @click="activeTab = 'dashboard'" class="w-full text-left px-6 py-3.5 text-sm font-bold uppercase tracking-wider transition-colors border-l-4" :class="activeTab === 'dashboard' ? 'border-red-600 text-red-600 bg-red-50' : 'border-transparent text-gray-600 hover:text-red-600 hover:bg-gray-50'">
                                    Dashboard
                                </button>
                            </li>
                            <li>
                                <button @click="activeTab = 'orders'" class="w-full text-left px-6 py-3.5 text-sm font-bold uppercase tracking-wider transition-colors border-l-4" :class="activeTab === 'orders' ? 'border-red-600 text-red-600 bg-red-50' : 'border-transparent text-gray-600 hover:text-red-600 hover:bg-gray-50'">
                                    Orders
                                </button>
                            </li>
                            <li>
                                <button @click="activeTab = 'addresses'" class="w-full text-left px-6 py-3.5 text-sm font-bold uppercase tracking-wider transition-colors border-l-4" :class="activeTab === 'addresses' ? 'border-red-600 text-red-600 bg-red-50' : 'border-transparent text-gray-600 hover:text-red-600 hover:bg-gray-50'">
                                    Addresses
                                </button>
                            </li>
                            <li>
                                <button @click="activeTab = 'details'" class="w-full text-left px-6 py-3.5 text-sm font-bold uppercase tracking-wider transition-colors border-l-4" :class="activeTab === 'details' ? 'border-red-600 text-red-600 bg-red-50' : 'border-transparent text-gray-600 hover:text-red-600 hover:bg-gray-50'">
                                    Account Details
                                </button>
                            </li>
                            <li class="border-t border-gray-100 mt-2 pt-2">
                                <a href="#" class="block px-6 py-3 text-sm font-bold uppercase tracking-wider text-gray-500 hover:text-gray-900 transition-colors">
                                    Logout
                                </a>
                            </li>
                        </ul>
                    </div>
                </div>

                <!-- Main Content -->
                <div class="flex-1">
                    
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

                    <!-- Orders Tab -->
                    <div x-show="activeTab === 'orders'" x-cloak class="space-y-6">
                        <div class="bg-white border border-gray-200 rounded-lg shadow-sm overflow-hidden">
                            <div class="p-5 md:p-6 border-b border-gray-100 flex items-center justify-between">
                                <h2 class="text-lg font-bold text-gray-900 uppercase tracking-widest">Order History</h2>
                            </div>
                            
                            <div class="overflow-x-auto">
                                <table class="w-full text-left text-sm text-gray-600 whitespace-nowrap">
                                    <thead class="bg-gray-50 text-gray-500 text-[11px] uppercase tracking-wider font-bold">
                                        <tr>
                                            <th class="px-6 py-4">Order ID</th>
                                            <th class="px-6 py-4">Date</th>
                                            <th class="px-6 py-4">Status</th>
                                            <th class="px-6 py-4">Total</th>
                                            <th class="px-6 py-4 text-right">Action</th>
                                        </tr>
                                    </thead>
                                    <tbody class="divide-y divide-gray-100">
                                        <tr class="hover:bg-gray-50 transition-colors">
                                            <td class="px-6 py-4 font-bold text-gray-900">#EVB-89012</td>
                                            <td class="px-6 py-4">Oct 24, 2023</td>
                                            <td class="px-6 py-4"><span class="px-2 py-1 bg-green-100 text-green-700 text-[10px] font-bold rounded-full uppercase tracking-wider">Completed</span></td>
                                            <td class="px-6 py-4 font-bold text-gray-900">৳ 4,310.00</td>
                                            <td class="px-6 py-4 text-right">
                                                <button class="text-red-600 hover:text-red-800 font-bold text-xs uppercase tracking-wider">View</button>
                                            </td>
                                        </tr>
                                        <tr class="hover:bg-gray-50 transition-colors">
                                            <td class="px-6 py-4 font-bold text-gray-900">#EVB-88743</td>
                                            <td class="px-6 py-4">Sep 12, 2023</td>
                                            <td class="px-6 py-4"><span class="px-2 py-1 bg-blue-100 text-blue-700 text-[10px] font-bold rounded-full uppercase tracking-wider">Processing</span></td>
                                            <td class="px-6 py-4 font-bold text-gray-900">৳ 1,250.00</td>
                                            <td class="px-6 py-4 text-right">
                                                <button class="text-red-600 hover:text-red-800 font-bold text-xs uppercase tracking-wider">View</button>
                                            </td>
                                        </tr>
                                        <tr class="hover:bg-gray-50 transition-colors">
                                            <td class="px-6 py-4 font-bold text-gray-900">#EVB-85210</td>
                                            <td class="px-6 py-4">Jul 05, 2023</td>
                                            <td class="px-6 py-4"><span class="px-2 py-1 bg-gray-200 text-gray-700 text-[10px] font-bold rounded-full uppercase tracking-wider">Cancelled</span></td>
                                            <td class="px-6 py-4 font-bold text-gray-900">৳ 3,800.00</td>
                                            <td class="px-6 py-4 text-right">
                                                <button class="text-red-600 hover:text-red-800 font-bold text-xs uppercase tracking-wider">View</button>
                                            </td>
                                        </tr>
                                    </tbody>
                                </table>
                            </div>
                        </div>
                    </div>

                    <!-- Addresses Tab -->
                    <div x-show="activeTab === 'addresses'" x-cloak class="space-y-6">
                        <div class="bg-white border border-gray-200 rounded-lg p-6 md:p-8 shadow-sm">
                            <div class="flex items-center justify-between mb-6">
                                <h2 class="text-lg font-bold text-gray-900 uppercase tracking-widest">Addresses</h2>
                                <button class="bg-slate-900 hover:bg-black text-white px-4 py-2 rounded text-xs font-bold uppercase tracking-wider transition-colors">Add New</button>
                            </div>
                            
                            <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                                <div class="border border-gray-200 rounded-lg p-5 relative">
                                    <span class="absolute top-0 right-0 bg-red-600 text-white text-[10px] font-bold px-2 py-1 rounded-bl-lg rounded-tr-lg uppercase tracking-wider">Default</span>
                                    <h4 class="font-bold text-gray-900 mb-2">John Doe</h4>
                                    <address class="text-sm text-gray-600 not-italic leading-relaxed mb-4">
                                        House 45, Road 12, Block E<br>
                                        Banani, Dhaka - 1213<br>
                                        Bangladesh<br>
                                        Phone: 01712345678
                                    </address>
                                    <div class="flex gap-3">
                                        <button class="text-xs font-bold text-slate-800 hover:text-red-600 uppercase tracking-wider">Edit</button>
                                        <button class="text-xs font-bold text-gray-400 hover:text-red-600 uppercase tracking-wider">Delete</button>
                                    </div>
                                </div>
                                
                                <div class="border border-gray-200 rounded-lg p-5">
                                    <h4 class="font-bold text-gray-900 mb-2">John Doe (Office)</h4>
                                    <address class="text-sm text-gray-600 not-italic leading-relaxed mb-4">
                                        Shei Tech Limited<br>
                                        Level 5, Rahman Tower<br>
                                        Gulshan 1, Dhaka - 1212<br>
                                        Phone: 01812345678
                                    </address>
                                    <div class="flex gap-3">
                                        <button class="text-xs font-bold text-slate-800 hover:text-red-600 uppercase tracking-wider">Edit</button>
                                        <button class="text-xs font-bold text-gray-400 hover:text-red-600 uppercase tracking-wider">Delete</button>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>

                    <!-- Account Details Tab -->
                    <div x-show="activeTab === 'details'" x-cloak class="space-y-6">
                        <div class="bg-white border border-gray-200 rounded-lg p-6 md:p-8 shadow-sm">
                            <h2 class="text-lg font-bold text-gray-900 uppercase tracking-widest mb-6">Account Details</h2>
                            
                            <form class="space-y-5">
                                <div class="grid grid-cols-1 md:grid-cols-2 gap-5">
                                    <div>
                                        <label class="text-[11px] font-bold text-gray-500 uppercase tracking-wide block mb-1">First Name *</label>
                                        <input type="text" value="John" class="w-full border-gray-300 rounded-md shadow-sm text-sm focus:ring-red-500 focus:border-red-500 py-2.5">
                                    </div>
                                    <div>
                                        <label class="text-[11px] font-bold text-gray-500 uppercase tracking-wide block mb-1">Last Name *</label>
                                        <input type="text" value="Doe" class="w-full border-gray-300 rounded-md shadow-sm text-sm focus:ring-red-500 focus:border-red-500 py-2.5">
                                    </div>
                                </div>
                                
                                <div>
                                    <label class="text-[11px] font-bold text-gray-500 uppercase tracking-wide block mb-1">Display Name *</label>
                                    <input type="text" value="John Doe" class="w-full border-gray-300 rounded-md shadow-sm text-sm focus:ring-red-500 focus:border-red-500 py-2.5">
                                    <p class="text-[10px] text-gray-400 mt-1 italic">This will be how your name will be displayed in the account section and in reviews.</p>
                                </div>

                                <div>
                                    <label class="text-[11px] font-bold text-gray-500 uppercase tracking-wide block mb-1">Email Address *</label>
                                    <input type="email" value="john.doe@example.com" class="w-full border-gray-300 rounded-md shadow-sm text-sm focus:ring-red-500 focus:border-red-500 py-2.5">
                                </div>

                                <div class="pt-4 mt-4 border-t border-gray-100">
                                    <h3 class="text-sm font-bold text-gray-900 uppercase tracking-widest mb-4">Password Change</h3>
                                    
                                    <div class="space-y-4">
                                        <div>
                                            <label class="text-[11px] font-bold text-gray-500 uppercase tracking-wide block mb-1">Current Password</label>
                                            <input type="password" class="w-full border-gray-300 rounded-md shadow-sm text-sm focus:ring-red-500 focus:border-red-500 py-2.5">
                                        </div>
                                        <div>
                                            <label class="text-[11px] font-bold text-gray-500 uppercase tracking-wide block mb-1">New Password</label>
                                            <input type="password" class="w-full border-gray-300 rounded-md shadow-sm text-sm focus:ring-red-500 focus:border-red-500 py-2.5">
                                        </div>
                                        <div>
                                            <label class="text-[11px] font-bold text-gray-500 uppercase tracking-wide block mb-1">Confirm New Password</label>
                                            <input type="password" class="w-full border-gray-300 rounded-md shadow-sm text-sm focus:ring-red-500 focus:border-red-500 py-2.5">
                                        </div>
                                    </div>
                                </div>

                                <div class="pt-2">
                                    <button type="button" class="bg-red-600 hover:bg-red-700 text-white px-8 py-3 rounded text-sm font-bold uppercase tracking-widest transition-colors shadow-md">
                                        Save Changes
                                    </button>
                                </div>
                            </form>
                        </div>
                    </div>

                </div>
            </div>
        </div>
    </div>
</x-layouts.app>
