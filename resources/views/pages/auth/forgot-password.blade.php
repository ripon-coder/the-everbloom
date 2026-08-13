<x-layouts.app title="Forgot Password | Feriwalarhat">
    <div class="bg-gray-50 py-12 md:py-24">
        <div class="max-w-[500px] mx-auto px-4 sm:px-6">
            
            <div class="bg-white rounded-lg shadow-sm border border-gray-200 overflow-hidden p-8 md:p-10">
                <div class="text-center mb-8">
                    <div class="w-16 h-16 bg-primary-50 rounded-full flex items-center justify-center mx-auto mb-4 text-primary">
                        <svg class="w-8 h-8" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M15 7a2 2 0 012 2m4 0a6 6 0 01-7.743 5.743L11 17H9v2H7v2H4a1 1 0 01-1-1v-2.586a1 1 0 01.293-.707l5.964-5.964A6 6 0 1121 9z"></path></svg>
                    </div>
                    <h2 class="text-2xl font-black text-slate-900 uppercase tracking-tight mb-2">Forgot Password</h2>
                    <p class="text-sm text-gray-500 leading-relaxed">
                        Forgot your password? No problem. Just let us know your mobile number and we will provide a way to reset your password.
                    </p>
                </div>
                
                <form class="space-y-6">
                    <div>
                        <label class="text-[11px] font-bold text-gray-500 uppercase tracking-wide block mb-1">Mobile Number</label>
                        <input type="text" 
                               name="phone"
                               class="w-full border-gray-300 rounded-md shadow-sm text-sm focus:ring-primary focus:border-primary py-3" 
                               placeholder="01XXXXXXXXX" 
                               required 
                               maxlength="11"
                               oninput="this.value = this.value.replace(/[^0-9]/g, '').substring(0, 11)"
                               autofocus>
                    </div>

                    <div>
                        <button type="submit" class="w-full bg-slate-900 hover:bg-black text-white px-8 py-3.5 rounded text-sm font-bold uppercase tracking-widest transition-colors shadow-md">
                            Send Reset Instructions
                        </button>
                    </div>
                    
                    <div class="text-center pt-4 border-t border-gray-100">
                        <a href="{{ route('login') }}" class="text-sm font-bold text-gray-600 hover:text-primary transition-colors uppercase tracking-wider">
                            Return to Login
                        </a>
                    </div>
                </form>
                
            </div>
            
        </div>
    </div>
</x-layouts.app>
