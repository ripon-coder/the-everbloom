<x-layouts.app title="Login | Everbloom">
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
                            <label class="text-[11px] font-bold text-gray-500 uppercase tracking-wide block mb-1">Email Address</label>
                            <input type="email" name="email" value="{{ old('email') }}" class="w-full border-gray-300 rounded-md shadow-sm text-sm focus:ring-red-500 focus:border-red-500 py-3" placeholder="Enter your email" required>
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
                    
                    <div class="mt-8 pt-8 border-t border-gray-100">
                        <p class="text-center text-sm text-gray-500 font-medium">Or sign in with</p>
                        <div class="mt-4 flex gap-4">
                            <button class="flex-1 flex items-center justify-center gap-2 border border-gray-200 rounded-md py-2.5 hover:bg-gray-50 transition-colors">
                                <svg class="w-5 h-5 text-blue-600" fill="currentColor" viewBox="0 0 24 24"><path d="M24 12.073c0-6.627-5.373-12-12-12s-12 5.373-12 12c0 5.99 4.388 10.954 10.125 11.854v-8.385H7.078v-3.469h3.047V9.43c0-3.007 1.792-4.669 4.533-4.669 1.312 0 2.686.235 2.686.235v2.953H15.83c-1.491 0-1.956.925-1.956 1.874v2.25h3.328l-.532 3.469h-2.796v8.385C19.612 23.027 24 18.062 24 12.073z"/></svg>
                                <span class="text-xs font-bold text-slate-700">Facebook</span>
                            </button>
                            <button class="flex-1 flex items-center justify-center gap-2 border border-gray-200 rounded-md py-2.5 hover:bg-gray-50 transition-colors">
                                <svg class="w-5 h-5" viewBox="0 0 24 24"><path fill="#4285F4" d="M22.56 12.25c0-.78-.07-1.53-.2-2.25H12v4.26h5.92c-.26 1.37-1.04 2.53-2.21 3.31v2.77h3.57c2.08-1.92 3.28-4.74 3.28-8.09z"/><path fill="#34A853" d="M12 23c2.97 0 5.46-.98 7.28-2.66l-3.57-2.77c-.98.66-2.23 1.06-3.71 1.06-2.86 0-5.29-1.93-6.16-4.53H2.18v2.84C3.99 20.53 7.7 23 12 23z"/><path fill="#FBBC05" d="M5.84 14.09c-.22-.66-.35-1.36-.35-2.09s.13-1.43.35-2.09V7.07H2.18C1.43 8.55 1 10.22 1 12s.43 3.45 1.18 4.93l2.85-2.22.81-.62z"/><path fill="#EA4335" d="M12 5.38c1.62 0 3.06.56 4.21 1.64l3.15-3.15C17.45 2.09 14.97 1 12 1 7.7 1 3.99 3.47 2.18 7.07l3.66 2.84c.87-2.6 3.3-4.53 6.16-4.53z"/><path fill="none" d="M1 1h22v22H1z"/></svg>
                                <span class="text-xs font-bold text-slate-700">Google</span>
                            </button>
                        </div>
                    </div>
                </div>
                
                <!-- Registration Call to Action (Right Side) -->
                <div class="flex-1 p-8 md:p-12 bg-slate-50 flex flex-col justify-center">
                    <h2 class="text-xl font-black text-slate-900 uppercase tracking-tight mb-4">New to Everbloom?</h2>
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
