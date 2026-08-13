<x-layouts.app title="Login | Feriwalarhat">
    <div class="bg-gray-50 py-6 md:py-16">
        <div class="max-w-[900px] mx-auto px-4 sm:px-6 lg:px-8">
            
            <div class="bg-white border border-gray-200 overflow-hidden flex flex-col md:flex-row items-stretch">
                
                <!-- Login Form (Left Side) -->
                <div class="flex-1 p-6 sm:p-8 md:p-10 border-b md:border-b-0 md:border-r border-gray-100 flex flex-col justify-between">
                    <div>
                        <h2 class="text-lg md:text-xl font-bold text-gray-900 mb-1">Welcome Back</h2>
                        <p class="text-xs text-gray-500 mb-6">Sign in with your mobile number to access your account.</p>
                    </div>
                    
                    <form class="space-y-4" method="POST" action="{{ route('login.post') }}">
                        @csrf
                        @if($errors->any())
                            <div class="bg-red-50 border-l-4 border-red-500 p-3 mb-4">
                                <div class="flex items-center gap-2">
                                    <svg class="h-4 w-4 text-red-500 flex-shrink-0" viewBox="0 0 20 20" fill="currentColor">
                                        <path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zM8.707 7.293a1 1 0 00-1.414 1.414L8.586 10l-1.293 1.293a1 1 0 101.414 1.414L10 11.414l1.293 1.293a1 1 0 001.414-1.414L11.414 10l1.293-1.293a1 1 0 00-1.414-1.414L10 8.586 8.707 7.293z" clip-rule="evenodd" />
                                    </svg>
                                    <p class="text-xs text-red-600 font-medium">
                                        {{ $errors->first() }}
                                    </p>
                                </div>
                            </div>
                        @endif

                        <div>
                            <label class="text-xs font-semibold text-gray-700 block mb-1">Mobile Number</label>
                            <input type="text" 
                                   name="phone" 
                                   id="phone"
                                   value="{{ old('phone') }}" 
                                   class="w-full border-gray-300 text-xs focus:ring-0 focus:border-primary py-2.5 px-3" 
                                   placeholder="01XXXXXXXXX" 
                                   required
                                   maxlength="11"
                                   oninput="this.value = this.value.replace(/[^0-9]/g, '').substring(0, 11)">
                        </div>
                        
                        <div>
                            <div class="flex items-center justify-between mb-1">
                                <label class="text-xs font-semibold text-gray-700">Password</label>
                                <a href="{{ route('password.request') }}" class="text-xs font-medium text-primary hover:underline">Forgot Password?</a>
                            </div>
                            <input type="password" name="password" class="w-full border-gray-300 text-xs focus:ring-0 focus:border-primary py-2.5 px-3" placeholder="Enter your password" required>
                        </div>
                        
                        <div class="flex items-center">
                            <input type="checkbox" name="remember" id="remember" class="border-gray-300 text-primary focus:ring-0 h-4 w-4">
                            <label for="remember" class="ml-2 text-xs text-gray-600 font-medium">Remember me</label>
                        </div>

                        <div class="pt-2">
                            <button type="submit" class="w-full bg-slate-900 hover:bg-black text-white px-6 py-3 text-xs font-bold uppercase tracking-wide transition-colors">
                                Sign In
                            </button>
                        </div>
                    </form>
                </div>
                
                <!-- Registration Call to Action (Right Side) -->
                <div class="flex-1 p-6 sm:p-8 md:p-10 bg-slate-50/70 flex flex-col justify-between border-t md:border-t-0 border-gray-100">
                    <h2 class="text-base md:text-lg font-bold text-gray-900 mb-2">New to Feriwalarhat?</h2>
                    <p class="text-xs text-gray-500 leading-relaxed mb-6">
                        Create an account to track orders, save delivery addresses, and enjoy faster checkout.
                    </p>
                    
                    <ul class="space-y-3 mb-8 text-xs text-gray-600 font-medium">
                        <li class="flex items-center gap-2.5">
                            <svg class="w-4 h-4 text-primary flex-shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"></path></svg>
                            <span>Faster checkout experience</span>
                        </li>
                        <li class="flex items-center gap-2.5">
                            <svg class="w-4 h-4 text-primary flex-shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"></path></svg>
                            <span>Save multiple delivery addresses</span>
                        </li>
                        <li class="flex items-center gap-2.5">
                            <svg class="w-4 h-4 text-primary flex-shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"></path></svg>
                            <span>View & track all your orders</span>
                        </li>
                    </ul>
                    
                    <div>
                        <a href="{{ route('register') }}" class="block w-full text-center bg-primary hover:bg-primary-dark text-white px-6 py-3 text-xs font-bold uppercase tracking-wide transition-colors">
                            Create An Account
                        </a>
                    </div>
                </div>
                
            </div>
            
        </div>
    </div>
</x-layouts.app>
