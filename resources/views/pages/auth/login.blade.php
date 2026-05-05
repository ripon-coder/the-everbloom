<x-layouts.app title="Login | feriwalarhat">
    <div class="bg-gray-50 py-10 md:py-20">
        <div class="max-w-[1000px] mx-auto px-4 sm:px-6 lg:px-8">
            
            <div class="bg-white rounded-lg shadow-sm border border-gray-200 overflow-hidden flex flex-col md:flex-row">
                
                <!-- Login Form (Left Side) -->
                <div class="flex-1 p-8 md:p-12 border-b md:border-b-0 md:border-r border-gray-100">
                    <h2 class="text-2xl font-black text-slate-900 uppercase tracking-tight mb-2">Welcome Back</h2>
                    <p class="text-sm text-gray-500 mb-8">Sign in to your account to continue.</p>
                    
                    <form class="space-y-5" method="POST" action="{{ route('login.post') }}">
                        @csrf
                        @if($errors->any())
                            <div class="bg-red-50 border-l-4 border-red-500 p-4 mb-4">
                                <div class="flex">
                                    <div class="flex-shrink-0">
                                        <svg class="h-5 w-5 text-red-400" viewBox="0 0 20 20" fill="currentColor">
                                            <path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zM8.707 7.293a1 1 0 00-1.414 1.414L8.586 10l-1.293 1.293a1 1 0 101.414 1.414L10 11.414l1.293 1.293a1 1 0 001.414-1.414L11.414 10l1.293-1.293a1 1 0 00-1.414-1.414L10 8.586 8.707 7.293z" clip-rule="evenodd" />
                                        </svg>
                                    </div>
                                    <div class="ml-3">
                                        <p class="text-sm text-red-700 font-medium">
                                            {{ $errors->first() }}
                                        </p>
                                    </div>
                                </div>
                            </div>
                        @endif

                        <div>
                            <label class="text-[11px] font-bold text-gray-500 uppercase tracking-wide block mb-1">Mobile Number</label>
                            <input type="text" 
                                   name="phone" 
                                   id="phone"
                                   value="{{ old('phone') }}" 
                                   class="w-full border-gray-300 rounded-md shadow-sm text-sm focus:ring-red-500 focus:border-red-500 py-3" 
                                   placeholder="01XXXXXXXXX" 
                                   required
                                   maxlength="11"
                                   oninput="this.value = this.value.replace(/[^0-9]/g, '').substring(0, 11)">
                        </div>
                        
                        <div>
                            <div class="flex items-center justify-between mb-1">
                                <label class="text-[11px] font-bold text-gray-500 uppercase tracking-wide">Password</label>
                                <a href="{{ route('password.request') }}" class="text-[11px] font-bold text-red-600 hover:text-red-700 transition-colors">Forgot Password?</a>
                            </div>
                            <input type="password" name="password" class="w-full border-gray-300 rounded-md shadow-sm text-sm focus:ring-red-500 focus:border-red-500 py-3" placeholder="Enter your password" required>
                        </div>
                        
                        <div class="flex items-center">
                            <input type="checkbox" name="remember" id="remember" class="rounded border-gray-300 text-red-600 focus:ring-red-500 h-4 w-4">
                            <label for="remember" class="ml-2 text-sm text-gray-600 font-medium">Remember me</label>
                        </div>

                        <div class="pt-2">
                            <button type="submit" class="w-full bg-slate-900 hover:bg-black text-white px-8 py-3.5 rounded text-sm font-bold uppercase tracking-widest transition-colors shadow-md">
                                Sign In
                            </button>
                        </div>
                    </form>
                </div>
                
                <!-- Registration Call to Action (Right Side) -->
                <div class="flex-1 p-8 md:p-12 bg-slate-50 flex flex-col justify-center">
                    <h2 class="text-xl font-black text-slate-900 uppercase tracking-tight mb-4">New to feriwalarhat?</h2>
                    <p class="text-sm text-gray-600 leading-relaxed mb-8">
                        Creating an account has many benefits: check out faster, keep more than one address, track orders and more.
                    </p>
                    
                    <ul class="space-y-4 mb-10">
                        <li class="flex items-start gap-3">
                            <svg class="w-5 h-5 text-red-600 mt-0.5 flex-shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"></path></svg>
                            <span class="text-sm text-gray-700 font-medium">Faster checkout process</span>
                        </li>
                        <li class="flex items-start gap-3">
                            <svg class="w-5 h-5 text-red-600 mt-0.5 flex-shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"></path></svg>
                            <span class="text-sm text-gray-700 font-medium">Save multiple shipping addresses</span>
                        </li>
                        <li class="flex items-start gap-3">
                            <svg class="w-5 h-5 text-red-600 mt-0.5 flex-shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"></path></svg>
                            <span class="text-sm text-gray-700 font-medium">View and track your orders</span>
                        </li>
                    </ul>
                    
                    <div>
                        <a href="{{ route('register') }}" class="inline-block w-full text-center bg-red-600 hover:bg-red-700 text-white px-8 py-3.5 rounded text-sm font-bold uppercase tracking-widest transition-colors shadow-md">
                            Create An Account
                        </a>
                    </div>
                </div>
                
            </div>
            
        </div>
    </div>
</x-layouts.app>
