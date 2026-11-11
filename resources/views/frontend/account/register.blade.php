@extends('layouts.app')

@section('title', 'Create Account — ' . config('demo.site.name'))
@section('meta_description', 'Create an account with The Everbloom to track orders, save favorite botanical formulations, and manage delivery addresses.')
@section('meta_robots', 'noindex, nofollow')
@section('hide_newsletter', true)

@section('content')
<div class="site-container py-10 md:py-16">
    <div class="max-w-md mx-auto">
        <!-- Breadcrumbs -->
        <div class="mb-6">
            <x-storefront.breadcrumbs :items="[
                ['label' => 'My Account', 'url' => route('account')],
                ['label' => 'Register', 'url' => ''],
            ]" />
        </div>

        <div 
            x-data="{
                firstName: '',
                lastName: '',
                email: '',
                password: '',
                showPassword: false,
                agreeTerms: false,
                errors: {},
                demoNotice: '',
                socialNotice: '',
                clearError(field) {
                    if (this.errors[field]) delete this.errors[field];
                },
                validate() {
                    this.errors = {};
                    if (!this.firstName.trim()) {
                        this.errors.firstName = 'Please enter your first name.';
                    }
                    if (!this.lastName.trim()) {
                        this.errors.lastName = 'Please enter your last name.';
                    }
                    if (!this.email.trim()) {
                        this.errors.email = 'Please enter your email address.';
                    } else if (!/^[^\s@]+@[^\s@]+\.[^\s@]+$/.test(this.email.trim())) {
                        this.errors.email = 'Please enter a valid email address.';
                    }
                    if (!this.password) {
                        this.errors.password = 'Please create a password.';
                    } else if (this.password.length < 8) {
                        this.errors.password = 'Password must be at least 8 characters.';
                    }
                    if (!this.agreeTerms) {
                        this.errors.agreeTerms = 'Please accept the Terms and Privacy Policy.';
                    }

                    const fieldMap = {
                        firstName: 'reg-first_name',
                        lastName: 'reg-last_name',
                        email: 'reg-email',
                        password: 'reg-password',
                        agreeTerms: 'reg-terms'
                    };
                    const keys = Object.keys(this.errors);
                    if (keys.length > 0) {
                        const targetId = fieldMap[keys[0]];
                        this.$nextTick(() => {
                            const el = document.getElementById(targetId);
                            if (el) el.focus();
                        });
                        return false;
                    }
                    return true;
                },
                submit() {
                    if (!this.validate()) return;
                    this.demoNotice = 'Demo only. No account was created.';
                },
                socialRegister(provider) {
                    this.socialNotice = 'Demo only. Registration via ' + provider + ' is not connected in demo mode.';
                    setTimeout(() => { this.socialNotice = ''; }, 6000);
                }
            }" 
            class="bg-white border border-[#E3E6DD] p-6 sm:p-8 space-y-6"
        >
            <!-- Header -->
            <div class="border-b border-[#ECEEE6] pb-5">
                <span class="text-xs uppercase tracking-[0.2em] font-medium text-forest block mb-1">
                    MY ACCOUNT
                </span>
                <h1 class="font-serif text-3xl sm:text-4xl text-ink font-normal tracking-tight">
                    Create Account
                </h1>
                <p class="text-sm text-muted mt-1 leading-relaxed">
                    Join The Everbloom to track orders, save favorite formulations, and manage your delivery preferences.
                </p>
                <p class="text-xs text-muted mt-1.5 font-normal">
                    Demo account — sample data for preview only.
                </p>
            </div>

            <!-- Demo Notification Banners -->
            <div 
                x-show="demoNotice" 
                x-cloak 
                x-transition 
                class="p-4 bg-forest text-ivory text-xs space-y-2 border border-forest"
                role="status"
                aria-live="polite"
            >
                <div class="flex items-center justify-between gap-2">
                    <span class="uppercase tracking-[0.2em] font-medium" x-text="demoNotice"></span>
                    <button type="button" @click="demoNotice = ''" class="w-11 h-11 -mr-2 hover:text-sage-light text-ivory flex items-center justify-center shrink-0 cursor-pointer" aria-label="Close message">✕</button>
                </div>
                <div class="pt-1">
                    <a href="{{ route('account') }}" class="underline underline-offset-4 font-medium hover:text-sage-light text-xs uppercase tracking-[0.2em] block">
                        → View Sample Account Overview
                    </a>
                </div>
            </div>

            <div 
                x-show="socialNotice" 
                x-cloak 
                x-transition 
                class="p-4 bg-forest text-ivory text-xs space-y-2 border border-forest"
                role="status"
                aria-live="polite"
            >
                <div class="flex items-center justify-between gap-2">
                    <span class="uppercase tracking-[0.2em] font-medium" x-text="socialNotice"></span>
                    <button type="button" @click="socialNotice = ''" class="w-11 h-11 -mr-2 hover:text-sage-light text-ivory flex items-center justify-center shrink-0 cursor-pointer" aria-label="Close message">✕</button>
                </div>
                <div class="pt-1">
                    <a href="{{ route('account') }}" class="underline underline-offset-4 font-medium hover:text-sage-light text-xs uppercase tracking-[0.2em] block">
                        → View Sample Account Overview
                    </a>
                </div>
            </div>

            <!-- Social Authentication Section -->
            <div class="space-y-3">
                <!-- Google Button -->
                <button 
                    type="button" 
                    @click="socialRegister('Google')" 
                    class="w-full h-12 min-h-[48px] px-4 bg-white border border-[#D5DBCF] hover:border-[#A8B7A0] hover:bg-[#F2F5F3] text-ink text-sm font-medium flex items-center justify-center gap-3 transition-colors cursor-pointer rounded-none"
                    aria-label="Sign up with Google"
                >
                    <svg class="w-5 h-5 shrink-0" viewBox="0 0 24 24" aria-hidden="true">
                        <path fill="#4285F4" d="M22.56 12.25c0-.78-.07-1.53-.2-2.25H12v4.26h5.92c-.26 1.37-1.04 2.53-2.21 3.31v2.77h3.57c2.08-1.92 3.28-4.74 3.28-8.09z" />
                        <path fill="#34A853" d="M12 23c2.97 0 5.46-.98 7.28-2.66l-3.57-2.77c-.98.66-2.23 1.06-3.71 1.06-2.86 0-5.29-1.93-6.16-4.53H2.18v2.84C3.99 20.53 7.7 23 12 23z" />
                        <path fill="#FBBC05" d="M5.84 14.09c-.22-.66-.35-1.36-.35-2.09s.13-1.43.35-2.09V7.06H2.18C1.43 8.55 1 10.22 1 12s.43 3.45 1.18 4.94l2.85-2.22.81-.63z" />
                        <path fill="#EA4335" d="M12 5.38c1.62 0 3.06.56 4.21 1.64l3.15-3.15C17.45 2.09 14.97 1 12 1 7.7 1 3.99 3.47 2.18 7.06l3.66 2.84c.87-2.6 3.3-4.52 6.16-4.52z" />
                    </svg>
                    <span>Sign up with Google</span>
                </button>

                <!-- Apple Button -->
                <button 
                    type="button" 
                    @click="socialRegister('Apple')" 
                    class="w-full h-12 min-h-[48px] px-4 bg-white border border-[#D5DBCF] hover:border-[#A8B7A0] hover:bg-[#F2F5F3] text-ink text-sm font-medium flex items-center justify-center gap-3 transition-colors cursor-pointer rounded-none"
                    aria-label="Sign up with Apple"
                >
                    <svg class="w-5 h-5 shrink-0 fill-current text-ink" viewBox="0 0 24 24" aria-hidden="true">
                        <path d="M18.71 19.5c-.83 1.24-1.71 2.45-3.05 2.47-1.34.03-1.77-.79-3.29-.79-1.53 0-2 .77-3.27.82-1.31.05-2.3-1.32-3.14-2.53C4.25 17 2.94 12.45 4.7 9.39c.87-1.52 2.43-2.48 4.12-2.51 1.28-.02 2.5.87 3.29.87.78 0 2.26-1.07 3.81-.91.65.03 2.47.26 3.64 1.98-.09.06-2.17 1.28-2.15 3.81.03 3.02 2.65 4.03 2.68 4.04-.03.07-.42 1.44-1.38 2.83M15.97 6.37c.61-.75 1.04-1.8 0.92-2.85-.9.04-2 .6-2.64 1.35-.56.65-.99 1.7-0.86 2.72.99.08 2-.48 2.58-1.22z" />
                    </svg>
                    <span>Sign up with Apple</span>
                </button>
            </div>

            <!-- Divider -->
            <div class="relative flex items-center justify-center my-6">
                <div class="absolute inset-0 flex items-center" aria-hidden="true">
                    <div class="w-full border-t border-[#ECEEE6]"></div>
                </div>
                <div class="relative flex justify-center">
                    <span class="bg-white px-4 text-muted uppercase tracking-[0.2em] text-xs font-medium">
                        Or register with email
                    </span>
                </div>
            </div>

            <!-- Form (Neutral placeholders: First name, Last name, name@example.com) -->
            <form @submit.prevent="submit()" class="space-y-5" novalidate>
                <!-- First and Last Name -->
                <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
                    <div>
                        <label for="reg-first_name" class="block text-xs uppercase tracking-[0.2em] text-muted font-medium mb-1.5">
                            First Name <span class="text-forest" aria-hidden="true">*</span>
                        </label>
                        <input 
                            type="text" 
                            id="reg-first_name" 
                            x-model="firstName" 
                            @input="clearError('firstName')"
                            autocomplete="given-name" 
                            required 
                            placeholder="First name"
                            class="input-field" 
                            :class="errors.firstName ? 'input-error' : ''"
                            :aria-invalid="errors.firstName ? 'true' : 'false'"
                            :aria-describedby="errors.firstName ? 'err-reg-first_name' : null"
                        />
                        <p id="err-reg-first_name" x-show="errors.firstName" x-text="errors.firstName" class="mt-1 text-[13px] text-[#B3453A] font-medium" role="alert" x-cloak></p>
                    </div>
                    <div>
                        <label for="reg-last_name" class="block text-xs uppercase tracking-[0.2em] text-muted font-medium mb-1.5">
                            Last Name <span class="text-forest" aria-hidden="true">*</span>
                        </label>
                        <input 
                            type="text" 
                            id="reg-last_name" 
                            x-model="lastName" 
                            @input="clearError('lastName')"
                            autocomplete="family-name" 
                            required 
                            placeholder="Last name"
                            class="input-field" 
                            :class="errors.lastName ? 'input-error' : ''"
                            :aria-invalid="errors.lastName ? 'true' : 'false'"
                            :aria-describedby="errors.lastName ? 'err-reg-last_name' : null"
                        />
                        <p id="err-reg-last_name" x-show="errors.lastName" x-text="errors.lastName" class="mt-1 text-[13px] text-[#B3453A] font-medium" role="alert" x-cloak></p>
                    </div>
                </div>

                <!-- Email Address -->
                <div>
                    <label for="reg-email" class="block text-xs uppercase tracking-[0.2em] text-muted font-medium mb-1.5">
                        Email Address <span class="text-forest" aria-hidden="true">*</span>
                    </label>
                    <input 
                        type="email" 
                        id="reg-email" 
                        x-model="email" 
                        @input="clearError('email')"
                        autocomplete="email" 
                        required 
                        placeholder="name@example.com" 
                        class="input-field" 
                        :class="errors.email ? 'input-error' : ''"
                        :aria-invalid="errors.email ? 'true' : 'false'"
                        :aria-describedby="errors.email ? 'err-reg-email' : null"
                    />
                    <p id="err-reg-email" x-show="errors.email" x-text="errors.email" class="mt-1 text-[13px] text-[#B3453A] font-medium" role="alert" x-cloak></p>
                </div>

                <!-- Create Password -->
                <div>
                    <label for="reg-password" class="block text-xs uppercase tracking-[0.2em] text-muted font-medium mb-1.5">
                        Create Password <span class="text-forest" aria-hidden="true">*</span>
                    </label>
                    <div class="relative">
                        <input 
                            :type="showPassword ? 'text' : 'password'" 
                            id="reg-password" 
                            x-model="password" 
                            @input="clearError('password')"
                            autocomplete="new-password" 
                            required 
                            placeholder="At least 8 characters"
                            class="input-field pr-12" 
                            :class="errors.password ? 'input-error' : ''"
                            :aria-invalid="errors.password ? 'true' : 'false'"
                            :aria-describedby="errors.password ? 'err-reg-password' : null"
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
                    <p id="err-reg-password" x-show="errors.password" x-text="errors.password" class="mt-1 text-[13px] text-[#B3453A] font-medium" role="alert" x-cloak></p>
                </div>

                <!-- Terms & Privacy Agreement -->
                <div>
                    <div class="flex items-start gap-3 pt-1">
                        <input 
                            type="checkbox" 
                            id="reg-terms" 
                            x-model="agreeTerms" 
                            @change="clearError('agreeTerms')"
                            :aria-invalid="errors.agreeTerms ? 'true' : 'false'"
                            :aria-describedby="errors.agreeTerms ? 'err-reg-terms' : null"
                            class="w-4 h-4 mt-0.5 rounded-none accent-[#304C3D] border-[#D5DBCF] cursor-pointer"
                        />
                        <label for="reg-terms" class="text-xs text-muted leading-relaxed select-none cursor-pointer">
                            I agree to The Everbloom's 
                            <a href="{{ route('terms') }}" class="text-ink underline underline-offset-4 hover:text-forest">Terms</a> and 
                            <a href="{{ route('privacy') }}" class="text-ink underline underline-offset-4 hover:text-forest">Privacy Policy</a>.
                        </label>
                    </div>
                    <p id="err-reg-terms" x-show="errors.agreeTerms" x-text="errors.agreeTerms" class="mt-1 text-[13px] text-[#B3453A] font-medium" role="alert" x-cloak></p>
                </div>

                <!-- Primary Submit -->
                <div class="pt-2">
                    <button 
                        type="submit" 
                        class="btn-primary w-full min-h-[48px] cursor-pointer"
                    >
                        Create Account
                    </button>
                </div>
            </form>

            <!-- Bottom Cross-links -->
            <div class="text-center pt-6 border-t border-[#ECEEE6] text-[13px] text-muted space-y-3">
                <p>
                    Already have an account? 
                    <a href="{{ route('login') }}" class="text-forest font-medium underline underline-offset-4 hover:text-ink ml-1">
                        Sign in
                    </a>
                </p>
                <div>
                    <a href="{{ route('account') }}" class="min-h-[44px] inline-flex items-center text-xs uppercase tracking-[0.2em] text-muted hover:text-forest underline underline-offset-4">
                        Explore Demo Account Overview →
                    </a>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection
