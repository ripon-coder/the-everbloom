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
