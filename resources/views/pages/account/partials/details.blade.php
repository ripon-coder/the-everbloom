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
