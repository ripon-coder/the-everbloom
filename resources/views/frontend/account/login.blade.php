@extends('layouts.app')

@section('title', 'Sign In — ' . config('demo.site.name'))
@section('meta_description', 'Sign in to your account at The Everbloom to view your orders, saved items, and delivery addresses.')
@section('meta_robots', 'noindex, nofollow')
@section('hide_newsletter', true)

@section('content')
<div class="site-container max-sm:!px-4 py-12 lg:py-16">
    <!-- Breadcrumbs -->
    <div class="mb-6">
        <x-storefront.breadcrumbs :items="[
            ['label' => 'Sign In', 'url' => ''],
        ]" />
    </div>

    <!-- Single Centered Column -->
    <div class="mx-auto w-full max-w-md">
        <div 
            x-data="{
                email: '',
                password: '',
                showPassword: false,
                rememberMe: false,
                errors: {},
                demoNotice: '',
                clearError(field) {
                    if (this.errors[field]) delete this.errors[field];
                },
                validate() {
                    this.errors = {};
                    if (!this.email.trim()) {
                        this.errors.email = 'Please enter your email address.';
                    } else if (!/^[^\s@]+@[^\s@]+\.[^\s@]+$/.test(this.email.trim())) {
                        this.errors.email = 'Please enter a valid email address.';
                    }
                    if (!this.password) {
                        this.errors.password = 'Please enter your password.';
                    }
                    const keys = Object.keys(this.errors);
                    if (keys.length > 0) {
                        const first = keys[0];
                        this.$nextTick(() => {
                            const el = document.getElementById('login-' + first);
                            if (el) el.focus();
                        });
                        return false;
                    }
                    return true;
                },
                submit() {
                    if (!this.validate()) return;
                    this.demoNotice = 'Demo only. No one was signed in.';
                }
            }" 
            class="bg-white border border-[#E3E6DD] p-6 sm:p-8 space-y-6"
        >
            <!-- Header -->
            <div class="border-b border-[#ECEEE6] pb-5">
                <span class="text-xs uppercase tracking-[0.2em] font-medium text-forest block mb-1">
                    WELCOME BACK
                </span>
                <h1 class="font-serif text-3xl sm:text-4xl text-ink font-normal tracking-tight">
                    Sign In
                </h1>
                <p class="text-sm text-muted mt-1.5 leading-relaxed">
                    Sign in to view your orders, saved items and addresses.
                </p>
                <div class="mt-3 flex items-center gap-2">
                    <span class="inline-flex items-center px-2 py-0.5 text-[11px] uppercase tracking-wider font-medium border border-[#D5DBCF] bg-[#F8F6F0] text-muted">
                        Demo
                    </span>
                    <a 
                        href="{{ route('account') }}" 
                        class="text-sm text-[#304C3D] hover:text-ink font-medium underline underline-offset-4 decoration-[#A8B7A0] hover:decoration-[#304C3D] focus-visible:outline-none focus-visible:ring-2 focus-visible:ring-[#304C3D] focus-visible:ring-offset-2 transition-colors"
                    >
                        View the demo account
                    </a>
                </div>
            </div>

            <!-- Demo Notification Banner -->
            <div 
                x-show="demoNotice" 
                x-cloak 
                x-transition 
                class="p-4 bg-forest text-ivory text-xs uppercase tracking-[0.2em] flex items-center justify-between gap-2 border border-forest"
                role="status"
                aria-live="polite"
            >
                <span class="font-medium" x-text="demoNotice"></span>
                <button type="button" @click="demoNotice = ''" class="w-11 h-11 -mr-2 hover:text-sage-light text-ivory flex items-center justify-center shrink-0 cursor-pointer" aria-label="Close message">✕</button>
            </div>

            <!-- Social Sign-In (Demo Only) -->
            <x-storefront.social-login-buttons :providers="config('demo.auth.enabled_providers')" mode="signin" />

            <!-- Email & Password Form -->
            <form @submit.prevent="submit()" class="space-y-5" novalidate>
                <!-- Email Address -->
                <div>
                    <label for="login-email" class="block text-xs uppercase tracking-[0.2em] text-muted font-medium mb-1.5">
                        Email Address <span class="text-forest" aria-hidden="true">*</span>
                    </label>
                    <input 
                        type="email" 
                        id="login-email" 
                        x-model="email" 
                        @input="clearError('email')"
                        autocomplete="username" 
                        inputmode="email"
                        required 
                        placeholder="name@example.com" 
                        class="input-field" 
                        :class="errors.email ? 'input-error !border-[#B3453A]' : ''"
                        :aria-invalid="errors.email ? 'true' : 'false'"
                        :aria-describedby="errors.email ? 'err-login-email' : null"
                    />
                    <p id="err-login-email" x-show="errors.email" x-text="errors.email" class="mt-1 text-[13px] text-[#B3453A] font-medium" role="alert" x-cloak></p>
                </div>

                <!-- Password -->
                <div>
                    <div class="flex items-center justify-between mb-1.5">
                        <label for="login-password" class="block text-xs uppercase tracking-[0.2em] text-muted font-medium">
                            Password <span class="text-forest" aria-hidden="true">*</span>
                        </label>
                        <a 
                            href="{{ route('password.request') }}" 
                            class="text-sm text-[#304C3D] hover:text-ink underline underline-offset-4 decoration-[#A8B7A0] hover:decoration-[#304C3D] focus-visible:outline-none focus-visible:ring-2 focus-visible:ring-[#304C3D] focus-visible:ring-offset-2 min-h-[44px] inline-flex items-center transition-colors"
                        >
                            Forgot password?
                        </a>
                    </div>
                    <div class="relative">
                        <input 
                            :type="showPassword ? 'text' : 'password'" 
                            id="login-password" 
                            x-model="password" 
                            @input="clearError('password')"
                            autocomplete="current-password" 
                            required 
                            placeholder="Enter your password"
                            class="input-field pr-12" 
                            :class="errors.password ? 'input-error !border-[#B3453A]' : ''"
                            :aria-invalid="errors.password ? 'true' : 'false'"
                            :aria-describedby="errors.password ? 'err-login-password' : null"
                        />
                        <button 
                            type="button" 
                            @click="showPassword = !showPassword" 
                            class="absolute right-0.5 top-1/2 -translate-y-1/2 h-11 w-11 min-w-[44px] min-h-[44px] flex items-center justify-center text-muted hover:text-ink focus-visible:outline-none focus-visible:ring-2 focus-visible:ring-[#304C3D] focus-visible:ring-offset-1 cursor-pointer"
                            :aria-label="showPassword ? 'Hide password' : 'Show password'"
                            :aria-pressed="showPassword ? 'true' : 'false'"
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
                    <p id="err-login-password" x-show="errors.password" x-text="errors.password" class="mt-1 text-[13px] text-[#B3453A] font-medium" role="alert" x-cloak></p>
                </div>

                <!-- Remember Me Checkbox (44px row height) -->
                <div class="min-h-[44px] flex items-center gap-3">
                    <input 
                        type="checkbox" 
                        id="remember-me" 
                        x-model="rememberMe"
                        class="w-4 h-4 rounded-none accent-[#304C3D] border-[#D5DBCF] cursor-pointer"
                    />
                    <label for="remember-me" class="text-sm text-ink select-none cursor-pointer">
                        Keep me signed in
                    </label>
                </div>

                <!-- Primary Submit + Demo Note -->
                <div class="pt-1 space-y-2.5">
                    <button 
                        type="submit" 
                        class="btn-primary w-full min-h-[48px] cursor-pointer"
                    >
                        Sign In
                    </button>
                    <p class="text-[13px] text-muted text-center">
                        Demo only. No one will be signed in.
                    </p>
                </div>
            </form>

            <!-- Bottom Cross-link -->
            <div class="text-center pt-6 border-t border-[#ECEEE6] text-[13px] text-muted">
                <p>
                    Don't have an account? 
                    <a href="{{ route('register') }}" class="text-[#304C3D] font-medium underline underline-offset-4 decoration-[#A8B7A0] hover:decoration-[#304C3D] focus-visible:outline-none focus-visible:ring-2 focus-visible:ring-[#304C3D] focus-visible:ring-offset-2 ml-1">
                        Create an account
                    </a>
                </p>
            </div>
        </div>
    </div>
</div>
@endsection
