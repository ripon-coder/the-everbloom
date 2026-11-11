@extends('layouts.app')

@section('title', 'Sign In — ' . config('demo.site.name'))
@section('meta_robots', 'noindex, nofollow')
@section('hide_newsletter', true)

@section('content')
<div class="site-container py-10 md:py-16">
    <div class="max-w-md mx-auto">
        <!-- Breadcrumbs -->
        <div class="mb-6">
            <x-storefront.breadcrumbs :items="[
                ['label' => 'My Account', 'url' => route('account')],
                ['label' => 'Sign In', 'url' => ''],
            ]" />
        </div>

        <div 
            x-data="{
                email: 'eleanor.vance@example.com',
                password: '••••••••',
                showPassword: false,
                rememberMe: true,
                demoNotice: '',
                socialNotice: '',
                forgotNotice: '',
                submit() {
                    this.demoNotice = 'Demo only. Authentication is simulated for sample exploration.';
                },
                socialLogin(provider) {
                    this.socialNotice = 'Demo only. ' + provider + ' authentication is simulated in demo mode.';
                    setTimeout(() => { this.socialNotice = ''; }, 6000);
                },
                forgot() {
                    this.forgotNotice = 'Demo only. Password recovery is unavailable in demo mode.';
                    setTimeout(() => { this.forgotNotice = ''; }, 5000);
                }
            }" 
            class="space-y-6"
        >
            <!-- Header -->
            <div class="border-b border-[#ECEEE6] pb-5">
                <span class="text-xs uppercase tracking-[0.2em] font-medium text-forest block mb-1">
                    MY ACCOUNT
                </span>
                <h1 class="font-serif text-3xl sm:text-4xl text-ink font-normal tracking-tight">
                    Sign In
                </h1>
                <p class="text-sm text-muted mt-1 leading-relaxed">
                    Sign in to access your previous orders, saved botanical formulations, and customer details.
                </p>
                <p class="text-xs text-muted/80 mt-1 font-normal">
                    Demo account. Sample data.
                </p>
            </div>

            <!-- Demo Notification Banners -->
            <div 
                x-show="demoNotice" 
                x-cloak 
                x-transition 
                class="p-4 bg-forest text-ivory text-xs space-y-2 border-l-2 border-sage"
                role="status"
            >
                <div class="flex items-center justify-between">
                    <span class="uppercase tracking-wider font-medium" x-text="demoNotice"></span>
                    <button type="button" @click="demoNotice = ''" class="p-1 hover:text-sage text-ivory min-h-[44px] min-w-[44px] flex items-center justify-center" aria-label="Close message">✕</button>
                </div>
                <div class="pt-1">
                    <a href="{{ route('account') }}" class="underline font-medium hover:text-sage text-xs uppercase tracking-wider block">
                        → Access Demo Account Dashboard Directly
                    </a>
                </div>
            </div>

            <div 
                x-show="socialNotice" 
                x-cloak 
                x-transition 
                class="p-4 bg-forest text-ivory text-xs space-y-2 border-l-2 border-sage"
                role="status"
            >
                <div class="flex items-center justify-between">
                    <span class="uppercase tracking-wider font-medium" x-text="socialNotice"></span>
                    <button type="button" @click="socialNotice = ''" class="p-1 hover:text-sage text-ivory min-h-[44px] min-w-[44px] flex items-center justify-center" aria-label="Close message">✕</button>
                </div>
                <div class="pt-1">
                    <a href="{{ route('account') }}" class="underline font-medium hover:text-sage text-xs uppercase tracking-wider block">
                        → Access Demo Account Dashboard Directly
                    </a>
                </div>
            </div>

            <div 
                x-show="forgotNotice" 
                x-cloak 
                x-transition 
                class="p-4 bg-white border border-[#E3E6DD] text-ink text-xs uppercase tracking-wider flex items-center justify-between"
                role="status"
            >
                <span x-text="forgotNotice"></span>
                <button type="button" @click="forgotNotice = ''" class="p-1 text-muted hover:text-ink min-h-[44px] min-w-[44px] flex items-center justify-center" aria-label="Close message">✕</button>
            </div>

            <!-- Social Authentication Section -->
            <div class="space-y-3">
                <!-- Google Button -->
                <button 
                    type="button" 
                    @click="socialLogin('Google')" 
                    class="w-full h-12 min-h-[48px] px-4 bg-white border border-[#D5DBCF] hover:border-[#A8B7A0] hover:bg-[#F2F5F3] text-ink text-sm font-medium flex items-center justify-center gap-3 transition-colors cursor-pointer rounded-none focus-visible:outline-none focus-visible:ring-2 focus-visible:ring-forest"
                    aria-label="Continue with Google"
                >
                    <svg class="w-5 h-5 shrink-0" viewBox="0 0 24 24" aria-hidden="true">
                        <path fill="#4285F4" d="M22.56 12.25c0-.78-.07-1.53-.2-2.25H12v4.26h5.92c-.26 1.37-1.04 2.53-2.21 3.31v2.77h3.57c2.08-1.92 3.28-4.74 3.28-8.09z" />
                        <path fill="#34A853" d="M12 23c2.97 0 5.46-.98 7.28-2.66l-3.57-2.77c-.98.66-2.23 1.06-3.71 1.06-2.86 0-5.29-1.93-6.16-4.53H2.18v2.84C3.99 20.53 7.7 23 12 23z" />
                        <path fill="#FBBC05" d="M5.84 14.09c-.22-.66-.35-1.36-.35-2.09s.13-1.43.35-2.09V7.06H2.18C1.43 8.55 1 10.22 1 12s.43 3.45 1.18 4.94l2.85-2.22.81-.63z" />
                        <path fill="#EA4335" d="M12 5.38c1.62 0 3.06.56 4.21 1.64l3.15-3.15C17.45 2.09 14.97 1 12 1 7.7 1 3.99 3.47 2.18 7.06l3.66 2.84c.87-2.6 3.3-4.52 6.16-4.52z" />
                    </svg>
                    <span>Continue with Google</span>
                </button>

                <!-- Apple Button -->
                <button 
                    type="button" 
                    @click="socialLogin('Apple')" 
                    class="w-full h-12 min-h-[48px] px-4 bg-white border border-[#D5DBCF] hover:border-[#A8B7A0] hover:bg-[#F2F5F3] text-ink text-sm font-medium flex items-center justify-center gap-3 transition-colors cursor-pointer rounded-none focus-visible:outline-none focus-visible:ring-2 focus-visible:ring-forest"
                    aria-label="Continue with Apple"
                >
                    <svg class="w-5 h-5 shrink-0 fill-current text-ink" viewBox="0 0 24 24" aria-hidden="true">
                        <path d="M18.71 19.5c-.83 1.24-1.71 2.45-3.05 2.47-1.34.03-1.77-.79-3.29-.79-1.53 0-2 .77-3.27.82-1.31.05-2.3-1.32-3.14-2.53C4.25 17 2.94 12.45 4.7 9.39c.87-1.52 2.43-2.48 4.12-2.51 1.28-.02 2.5.87 3.29.87.78 0 2.26-1.07 3.81-.91.65.03 2.47.26 3.64 1.98-.09.06-2.17 1.28-2.15 3.81.03 3.02 2.65 4.03 2.68 4.04-.03.07-.42 1.44-1.38 2.83M15.97 6.37c.61-.75 1.04-1.8 0.92-2.85-.9.04-2 .6-2.64 1.35-.56.65-.99 1.7-0.86 2.72.99.08 2-.48 2.58-1.22z" />
                    </svg>
                    <span>Continue with Apple</span>
                </button>
            </div>

            <!-- Divider -->
            <div class="relative flex items-center justify-center my-6">
                <div class="absolute inset-0 flex items-center" aria-hidden="true">
                    <div class="w-full border-t border-[#ECEEE6]"></div>
                </div>
                <div class="relative flex justify-center text-xs uppercase">
                    <span class="bg-[#F8F6F0] px-4 text-muted tracking-widest text-[11px] font-medium">
                        Or continue with email
                    </span>
                </div>
            </div>

            <!-- Email & Password Form -->
            <form @submit.prevent="submit()" class="space-y-5" novalidate>
                <!-- Email Address -->
                <div>
                    <label for="login-email" class="block text-xs uppercase tracking-[0.16em] text-muted font-medium mb-1.5">
                        Email Address
                    </label>
                    <input 
                        type="email" 
                        id="login-email" 
                        x-model="email" 
                        autocomplete="email" 
                        required 
                        placeholder="name@domain.com" 
                        class="input-field" 
                    />
                </div>

                <!-- Password -->
                <div>
                    <div class="flex items-center justify-between mb-1.5">
                        <label for="login-password" class="block text-xs uppercase tracking-[0.16em] text-muted font-medium">
                            Password
                        </label>
                        <button 
                            type="button" 
                            @click="forgot()" 
                            class="text-xs text-muted hover:text-forest underline underline-offset-2 min-h-[44px] flex items-center cursor-pointer"
                        >
                            Forgot password?
                        </button>
                    </div>
                    <div class="relative">
                        <input 
                            :type="showPassword ? 'text' : 'password'" 
                            id="login-password" 
                            x-model="password" 
                            autocomplete="current-password" 
                            required 
                            class="input-field pr-12" 
                        />
                        <button 
                            type="button" 
                            @click="showPassword = !showPassword" 
                            class="absolute right-0 top-0 h-12 w-12 flex items-center justify-center text-muted hover:text-ink cursor-pointer min-h-[44px] min-w-[44px]"
                            :aria-label="showPassword ? 'Hide password' : 'Show password'"
                        >
                            <template x-if="!showPassword">
                                <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24" aria-hidden="true">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.75" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z" />
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.75" d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z" />
                                </svg>
                            </template>
                            <template x-if="showPassword">
                                <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24" aria-hidden="true">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.75" d="M13.875 18.825A10.05 10.05 0 0112 19c-4.478 0-8.268-2.943-9.543-7a9.97 9.97 0 011.563-3.029m5.858.908a3 3 0 114.243 4.243M9.878 9.878l4.242 4.242M9.88 9.88l-3.29-3.29m7.532 7.532l3.29 3.29M3 3l18 18" />
                                </svg>
                            </template>
                        </button>
                    </div>
                </div>

                <!-- Remember Me Checkbox -->
                <div class="flex items-center gap-3">
                    <input 
                        type="checkbox" 
                        id="remember-me" 
                        x-model="rememberMe"
                        class="w-4 h-4 rounded-none accent-[#304C3D] text-[#304C3D] border-[#D5DBCF] focus:ring-forest focus:ring-offset-0"
                    />
                    <label for="remember-me" class="text-xs text-ink select-none cursor-pointer">
                        Keep me signed in on this device
                    </label>
                </div>

                <!-- Primary Submit -->
                <div class="pt-2">
                    <button 
                        type="submit" 
                        class="btn-primary w-full min-h-[48px] cursor-pointer"
                    >
                        Sign in
                    </button>
                </div>
            </form>

            <!-- Bottom Cross-links -->
            <div class="text-center pt-6 border-t border-[#ECEEE6] text-xs text-muted space-y-3">
                <p>
                    Don't have an account? 
                    <a href="{{ route('register') }}" class="text-forest font-medium hover:underline ml-1">
                        Create an account
                    </a>
                </p>
                <div>
                    <a href="{{ route('account') }}" class="inline-flex items-center text-xs uppercase tracking-wider text-muted hover:text-forest underline underline-offset-4">
                        Explore Demo Account directly →
                    </a>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection
