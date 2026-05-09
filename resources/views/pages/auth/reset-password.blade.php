<x-layouts.app title="Reset Password | Feriwalarhat">
    <div class="bg-gray-50 py-12 md:py-24">
        <div class="max-w-[500px] mx-auto px-4 sm:px-6">
            
            <div class="bg-white rounded-lg shadow-sm border border-gray-200 overflow-hidden p-8 md:p-10">
                <div class="text-center mb-8">
                    <div class="w-16 h-16 bg-red-50 rounded-full flex items-center justify-center mx-auto mb-4 text-red-600">
                        <svg class="w-8 h-8" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M12 15v2m-6 4h12a2 2 0 002-2v-6a2 2 0 00-2-2H6a2 2 0 00-2 2v6a2 2 0 002 2zm10-10V7a4 4 0 00-8 0v4h8z"></path></svg>
                    </div>
                    <h2 class="text-2xl font-black text-slate-900 uppercase tracking-tight mb-2">Reset Password</h2>
                    <p class="text-sm text-gray-500 leading-relaxed">
                        Please enter your mobile number and your new password below to reset your account access.
                    </p>
                </div>
                
                <form class="space-y-5">
                    <!-- Token would be hidden here in a real implementation -->
                    <input type="hidden" name="token" value="dummy-token">

                    <div>
                        <label class="text-[11px] font-bold text-gray-500 uppercase tracking-wide block mb-1">Mobile Number *</label>
                        <input type="text" 
                               name="phone"
                               class="w-full border-gray-300 rounded-md shadow-sm text-sm focus:ring-red-500 focus:border-red-500 py-3 bg-gray-50" 
                               placeholder="01XXXXXXXXX" 
                               maxlength="11"
                               oninput="this.value = this.value.replace(/[^0-9]/g, '').substring(0, 11)"
                               required>
                    </div>

                    <div>
                        <label class="text-[11px] font-bold text-gray-500 uppercase tracking-wide block mb-1">New Password *</label>
                        <input type="password" class="w-full border-gray-300 rounded-md shadow-sm text-sm focus:ring-red-500 focus:border-red-500 py-3" placeholder="Enter new password" required autofocus>
                    </div>

                    <div>
                        <label class="text-[11px] font-bold text-gray-500 uppercase tracking-wide block mb-1">Confirm New Password *</label>
                        <input type="password" class="w-full border-gray-300 rounded-md shadow-sm text-sm focus:ring-red-500 focus:border-red-500 py-3" placeholder="Confirm new password" required>
                    </div>

                    <div class="pt-4">
                        <button type="submit" class="w-full bg-red-600 hover:bg-red-700 text-white px-8 py-3.5 rounded text-sm font-bold uppercase tracking-widest transition-colors shadow-md">
                            Reset Password
                        </button>
                    </div>
                </form>
                
            </div>
            
        </div>
    </div>
</x-layouts.app>
