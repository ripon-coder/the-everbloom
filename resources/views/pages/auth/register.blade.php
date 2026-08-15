<x-layouts.app title="Register | Feriwalarhat">
    <div class="auth-page-container bg-gray-50 py-10 md:py-20">
        <div class="max-w-[1000px] mx-auto px-1.5 sm:px-6 lg:px-8">
            
            <div class="bg-white rounded-lg shadow-sm border border-gray-200 overflow-hidden flex flex-col md:flex-row-reverse">
                
                <!-- Registration Form (Right Side on Desktop) -->
                <div class="flex-[1.2] p-6 sm:p-8 md:p-12 border-b md:border-b-0 md:border-l border-gray-100">
                    <h2 class="text-xl md:text-2xl font-bold text-gray-900 mb-1.5">Create Account</h2>
                    <p class="text-xs sm:text-sm text-gray-500 mb-6">Please fill in the information below to create an account.</p>
                    
                    <form class="space-y-5" method="POST" action="{{ route('register.post') }}">
                        @csrf
                        @if($errors->any())
                            <div class="bg-red-50 border-l-4 border-red-500 p-3.5 mb-4">
                                <div class="flex">
                                    <div class="flex-shrink-0">
                                        <svg class="h-5 w-5 text-red-500" viewBox="0 0 20 20" fill="currentColor">
                                            <path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zM8.707 7.293a1 1 0 00-1.414 1.414L8.586 10l-1.293 1.293a1 1 0 101.414 1.414L10 11.414l1.293 1.293a1 1 0 001.414-1.414L11.414 10l1.293-1.293a1 1 0 00-1.414-1.414L10 8.586 8.707 7.293z" clip-rule="evenodd" />
                                        </svg>
                                    </div>
                                    <div class="ml-3">
                                        <ul class="list-disc list-inside text-xs sm:text-sm text-red-600 font-medium">
                                            @foreach($errors->all() as $error)
                                                <li>{{ $error }}</li>
                                            @endforeach
                                        </ul>
                                    </div>
                                </div>
                            </div>
                        @endif

                        <div class="grid grid-cols-1 sm:grid-cols-2 gap-5">
                            <div>
                                <label class="text-xs sm:text-sm font-semibold text-gray-700 block mb-1.5">First Name *</label>
                                <input type="text" name="first_name" value="{{ old('first_name') }}" class="w-full border-gray-300 text-sm text-gray-900 focus:ring-primary focus:border-primary py-3 px-3.5" placeholder="First Name" required>
                            </div>
                            <div>
                                <label class="text-xs sm:text-sm font-semibold text-gray-700 block mb-1.5">Last Name *</label>
                                <input type="text" name="last_name" value="{{ old('last_name') }}" class="w-full border-gray-300 text-sm text-gray-900 focus:ring-primary focus:border-primary py-3 px-3.5" placeholder="Last Name" required>
                            </div>
                        </div>

                        <div class="grid grid-cols-1 sm:grid-cols-2 gap-5">
                            <div>
                                <label class="text-xs sm:text-sm font-semibold text-gray-700 block mb-1.5">Email Address</label>
                                <input type="email" 
                                       name="email" 
                                       id="email"
                                       value="{{ old('email') }}" 
                                       class="w-full border-gray-300 text-sm text-gray-900 focus:ring-primary focus:border-primary py-3 px-3.5" 
                                       placeholder="email@example.com">
                            </div>
                            <div>
                                <label class="text-xs sm:text-sm font-semibold text-gray-700 block mb-1.5">Mobile Number</label>
                                <input type="text" 
                                       name="phone" 
                                       id="phone"
                                       value="{{ old('phone') }}" 
                                       class="w-full border-gray-300 text-sm text-gray-900 focus:ring-primary focus:border-primary py-3 px-3.5" 
                                       placeholder="01XXXXXXXXX" 
                                       maxlength="11"
                                       oninput="this.value = this.value.replace(/[^0-9]/g, '').substring(0, 11)">
                            </div>
                        </div>
                        
                        <div>
                            <label class="text-xs sm:text-sm font-semibold text-gray-700 block mb-1.5">Password *</label>
                            <input type="password" name="password" class="w-full border-gray-300 text-sm text-gray-900 focus:ring-primary focus:border-primary py-3 px-3.5" placeholder="Create a password" required>
                        </div>

                        <div>
                            <label class="text-xs sm:text-sm font-semibold text-gray-700 block mb-1.5">Confirm Password *</label>
                            <input type="password" name="password_confirmation" class="w-full border-gray-300 text-sm text-gray-900 focus:ring-primary focus:border-primary py-3 px-3.5" placeholder="Confirm your password" required>
                        </div>
                        
                        <div class="flex items-start mt-2">
                            <input type="checkbox" name="terms" id="terms" class="border-gray-300 text-primary focus:ring-0 h-4 w-4 mt-0.5" required>
                            <label for="terms" class="ml-2 text-xs sm:text-sm text-gray-600 font-medium">I agree to the <a href="#" class="text-primary hover:underline font-semibold">Terms of Service</a> and <a href="#" class="text-primary hover:underline font-semibold">Privacy Policy</a>.</label>
                        </div>

                        <div class="pt-2">
                            <button type="submit" class="w-full bg-primary hover:bg-primary-dark text-white px-8 py-3 text-xs font-bold uppercase tracking-wide transition-colors">
                                Register
                            </button>
                        </div>
                    </form>
                </div>
                
                <!-- Login Call to Action (Left Side on Desktop) -->
                <div class="flex-1 p-6 sm:p-8 md:p-12 bg-slate-50 flex flex-col justify-center">
                    <h2 class="text-lg md:text-xl font-bold text-gray-900 mb-3">Already a Feriwalarhat member?</h2>
                    <p class="text-xs sm:text-sm text-gray-600 leading-relaxed mb-6">
                        Welcome back! Sign in to access your saved addresses, track your current orders, and view your purchase history.
                    </p>
                    
                    <div class="mb-8">
                        <div class="flex items-center gap-4 mb-4">
                            <div class="w-10 h-10 rounded-full bg-primary-50 flex items-center justify-center text-primary flex-shrink-0">
                                <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 15v2m-6 4h12a2 2 0 002-2v-6a2 2 0 00-2-2H6a2 2 0 00-2 2v6a2 2 0 002 2zm10-10V7a4 4 0 00-8 0v4h8z"></path></svg>
                            </div>
                            <div>
                                <h4 class="font-bold text-gray-900 text-xs sm:text-sm">Secure Shopping</h4>
                                <p class="text-xs text-gray-500">Your data is always protected.</p>
                            </div>
                        </div>
                        <div class="flex items-center gap-4">
                            <div class="w-10 h-10 rounded-full bg-accent-50 flex items-center justify-center text-accent flex-shrink-0">
                                <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m5.618-4.016A11.955 11.955 0 0112 2.944a11.955 11.955 0 01-8.618 3.04A12.02 12.02 0 003 9c0 5.591 3.824 10.29 9 11.622 5.176-1.332 9-6.03 9-11.622 0-1.042-.133-2.052-.382-3.016z"></path></svg>
                            </div>
                            <div>
                                <h4 class="font-bold text-gray-900 text-xs sm:text-sm">Exclusive Offers</h4>
                                <p class="text-xs text-gray-500">Members get access to special sales.</p>
                            </div>
                        </div>
                    </div>
                    
                    <div>
                        <a href="{{ route('login') }}" class="inline-block w-full text-center bg-slate-900 hover:bg-black text-white px-8 py-3 text-xs font-bold uppercase tracking-wide transition-colors">
                            Sign In Instead
                        </a>
                    </div>
                </div>
                
            </div>
            
        </div>
    </div>
</x-layouts.app>
