@extends('layouts.app')

@section('title', 'Create Account â€” ' . config('demo.site.name'))
@section('meta_description', 'Create an account with The Everbloom to track orders and save your favorites.')
@section('meta_robots', 'noindex, nofollow')
@section('hide_newsletter', true)

@section('content')
<div class="site-container max-sm:!px-4 py-12 lg:py-16">
    <!-- Breadcrumbs -->
    <div class="mb-6">
        <x-storefront.breadcrumbs :items="[
            ['label' => 'Create Account', 'url' => ''],
        ]" />
    </div>

    <!-- Single Centered Column -->
    <div class="mx-auto w-full max-w-md">
        @php
            $initialRegisterType = old('register_type') === 'email' || $errors->has('email') ? 'email' : 'phone';
        @endphp
        <div 
            x-data="{
                registerType: @js($initialRegisterType),
                firstName: @js(old('first_name', '')),
                lastName: @js(old('last_name', '')),
                phone: @js(old('phone', '')),
                email: @js(old('email', '')),
                password: '',
                confirmPassword: '',
                showPassword: false,
                showConfirmPassword: false,
                confirmTouched: false,
                agreeTerms: false,
                errors: {
                    @error('first_name') firstName: @js($message), @enderror
                    @error('last_name') lastName: @js($message), @enderror
                    @error('phone') phone: @js($message), @enderror
                    @error('email') email: @js($message), @enderror
                    @error('password') password: @js($message), @enderror
                    @error('terms') agreeTerms: @js($message), @enderror
                },
                demoNotice: @js(session('status', '')),
                get hasMinLength() {
                    return this.password.length >= 8;
                },
                get hasLetter() {
                    return /[A-Za-z]/.test(this.password);
                },
                get hasNumber() {
                    return /\d/.test(this.password);
                },
                switchTab(type) {
                    if (this.registerType === type) return;
                    this.registerType = type;
                    delete this.errors.phone;
                    delete this.errors.email;
                    this.$nextTick(() => {
                        const el = document.getElementById('reg-' + type);
                        if (el) el.focus();
                    });
                },
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
                    if (this.registerType === 'phone') {
                        const digits = this.phone.replace(/\D/g, '');
                        if (!this.phone.trim()) {
                            this.errors.phone = 'Please enter your mobile number.';
                        } else if (digits.length < 7 || digits.length > 15) {
                            this.errors.phone = 'Please enter a valid mobile number.';
                        }
                    } else {
                        if (!this.email.trim()) {
                            this.errors.email = 'Please enter your email address.';
                        } else if (!/^[^\s@]+@[^\s@]+\.[^\s@]+$/.test(this.email.trim())) {
                            this.errors.email = 'Please enter a valid email address.';
                        }
                    }
                    if (!this.password) {
                        this.errors.password = 'Please create a password.';
                    } else if (!this.hasMinLength || !this.hasLetter || !this.hasNumber) {
                        this.errors.password = 'Password must be at least 8 characters and include a letter and a number.';
                    }
                    if (!this.confirmPassword) {
                        this.errors.confirmPassword = 'Please confirm your password.';
                    } else if (this.password !== this.confirmPassword) {
                        this.errors.confirmPassword = 'Passwords don\'t match.';
                    }
                    if (!this.agreeTerms) {
                        this.errors.agreeTerms = 'Please accept the Terms and Privacy Policy.';
                    }

                    const fieldMap = {
                        firstName: 'reg-first_name',
                        lastName: 'reg-last_name',
                        phone: 'reg-phone',
                        email: 'reg-email',
                        password: 'reg-password',
                        confirmPassword: 'reg-confirm_password',
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
                    this.$refs.registerForm.submit();
                }
            }" 
            class="bg-white border border-[#E3E6DD] p-6 sm:p-8 space-y-6"
        >
            <!-- Header -->
            <div class="border-b border-[#ECEEE6] pb-5">
                <span class="text-xs uppercase tracking-[0.2em] font-medium text-forest block mb-1">
                    JOIN THE EVERBLOOM
                </span>
                <h1 class="font-serif text-3xl sm:text-4xl text-ink font-normal tracking-tight">
                    Create Account
                </h1>
                <p class="text-sm text-muted mt-1.5 leading-relaxed">
                    Track orders and save your favorites.
                </p>
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
                <button type="button" @click="demoNotice = ''" class="w-11 h-11 -mr-2 hover:text-sage-light text-ivory flex items-center justify-center shrink-0 cursor-pointer" aria-label="Close message"><svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24" aria-hidden="true"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.75" d="M6 18L18 6M6 6l12 12" /></svg></button>
            </div>

            <!-- Social Sign-Up (Demo Only) -->
            <x-storefront.social-login-buttons :providers="config('demo.auth.enabled_providers')" mode="register" divider-label="Or register with" />

            <!-- Registration Method Tabs (Default: Mobile Number) -->
            <div role="tablist" aria-label="Registration method" class="grid grid-cols-2 border border-[#D5DBCF] bg-[#F8F9F6] p-1 gap-1">
                <button
                    type="button"
                    role="tab"
                    id="tab-reg-phone"
                    aria-controls="panel-reg-phone"
                    :aria-selected="registerType === 'phone' ? 'true' : 'false'"
                    @click="switchTab('phone')"
                    class="min-h-[44px] px-3 text-xs uppercase tracking-[0.18em] font-medium transition-colors focus-visible:outline-none focus-visible:ring-2 focus-visible:ring-[#304C3D] focus-visible:ring-offset-1 cursor-pointer"
                    :class="registerType === 'phone' ? 'bg-[#304C3D] text-[#FAF9F5]' : 'text-muted hover:text-ink bg-transparent'"
                >
                    Mobile
                </button>
                <button
                    type="button"
                    role="tab"
                    id="tab-reg-email"
                    aria-controls="panel-reg-email"
                    :aria-selected="registerType === 'email' ? 'true' : 'false'"
                    @click="switchTab('email')"
                    class="min-h-[44px] px-3 text-xs uppercase tracking-[0.18em] font-medium transition-colors focus-visible:outline-none focus-visible:ring-2 focus-visible:ring-[#304C3D] focus-visible:ring-offset-1 cursor-pointer"
                    :class="registerType === 'email' ? 'bg-[#304C3D] text-[#FAF9F5]' : 'text-muted hover:text-ink bg-transparent'"
                >
                    Email
                </button>
            </div>

            <!-- Registration Form -->
            <form x-ref="registerForm" method="POST" action="{{ route('register.submit') }}" @submit.prevent="submit()" class="space-y-5" novalidate>
                @csrf
                <input type="hidden" name="register_type" :value="registerType" />
                <!-- First and Last Name -->
                <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
                    <div>
                        <label for="reg-first_name" class="block text-xs uppercase tracking-[0.2em] text-muted font-medium mb-1.5">
                            First Name <span class="text-forest" aria-hidden="true">*</span>
                        </label>
                        <input 
                            type="text" 
                            id="reg-first_name" 
                            name="first_name"
                            x-model="firstName" 
                            @input="clearError('firstName')"
                            autocomplete="given-name" 
                            required 
                            class="input-field" 
                            :class="errors.firstName ? 'input-error !border-[#B3453A]' : ''"
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
                            name="last_name"
                            x-model="lastName" 
                            @input="clearError('lastName')"
                            autocomplete="family-name" 
                            required 
                            class="input-field" 
                            :class="errors.lastName ? 'input-error !border-[#B3453A]' : ''"
                            :aria-invalid="errors.lastName ? 'true' : 'false'"
                            :aria-describedby="errors.lastName ? 'err-reg-last_name' : null"
                        />
                        <p id="err-reg-last_name" x-show="errors.lastName" x-text="errors.lastName" class="mt-1 text-[13px] text-[#B3453A] font-medium" role="alert" x-cloak></p>
                    </div>
                </div>

                <!-- Mobile -->
                <div id="panel-reg-phone" role="tabpanel" aria-labelledby="tab-reg-phone" x-show="registerType === 'phone'" x-cloak>
                    <label for="reg-phone" class="block text-xs uppercase tracking-[0.2em] text-muted font-medium mb-1.5">
                        Mobile <span class="text-forest" aria-hidden="true">*</span>
                    </label>
                    <input 
                        type="tel" 
                        id="reg-phone" 
                        name="phone"
                        x-model="phone" 
                        @input="clearError('phone')"
                        :disabled="registerType !== 'phone'"
                        autocomplete="tel" 
                        inputmode="tel"
                        required 
                        class="input-field" 
                        :class="errors.phone ? 'input-error !border-[#B3453A]' : ''"
                        :aria-invalid="errors.phone ? 'true' : 'false'"
                        :aria-describedby="errors.phone ? 'err-reg-phone' : null"
                    />
                    <p id="err-reg-phone" x-show="errors.phone" x-text="errors.phone" class="mt-1 text-[13px] text-[#B3453A] font-medium" role="alert" x-cloak></p>
                </div>

                <!-- Email Address -->
                <div id="panel-reg-email" role="tabpanel" aria-labelledby="tab-reg-email" x-show="registerType === 'email'" x-cloak>
                    <label for="reg-email" class="block text-xs uppercase tracking-[0.2em] text-muted font-medium mb-1.5">
                        Email Address <span class="text-forest" aria-hidden="true">*</span>
                    </label>
                    <input 
                        type="email" 
                        id="reg-email" 
                        name="email"
                        x-model="email" 
                        @input="clearError('email')"
                        :disabled="registerType !== 'email'"
                        autocomplete="email" 
                        inputmode="email"
                        required 
                        class="input-field" 
                        :class="errors.email ? 'input-error !border-[#B3453A]' : ''"
                        :aria-invalid="errors.email ? 'true' : 'false'"
                        :aria-describedby="errors.email ? 'err-reg-email' : null"
                    />
                    <p id="err-reg-email" x-show="errors.email" x-text="errors.email" class="mt-1 text-[13px] text-[#B3453A] font-medium" role="alert" x-cloak></p>
                </div>

                <!-- Create Password + Live Checklist -->
                <div>
                    <label for="reg-password" class="block text-xs uppercase tracking-[0.2em] text-muted font-medium mb-1.5">
                        Password <span class="text-forest" aria-hidden="true">*</span>
                    </label>
                    <div class="relative">
                        <input 
                            :type="showPassword ? 'text' : 'password'" 
                            id="reg-password" 
                            name="password"
                            x-model="password" 
                            @input="clearError('password')"
                            autocomplete="new-password" 
                            data-lpignore="true"
                            required 
                            class="input-field pr-12" 
                            :class="errors.password ? 'input-error !border-[#B3453A]' : ''"
                            :aria-invalid="errors.password ? 'true' : 'false'"
                            :aria-describedby="errors.password ? 'err-reg-password helper-reg-password' : 'helper-reg-password'"
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
                    <p id="helper-reg-password" class="mt-1.5 text-[13px] text-muted">
                        Choose a strong password you don't use elsewhere.
                    </p>
                    <p id="err-reg-password" x-show="errors.password" x-text="errors.password" class="mt-1 text-[13px] text-[#B3453A] font-medium" role="alert" x-cloak></p>

                    <!-- Live Password Checklist -->
                    <ul class="mt-2.5 space-y-1.5 text-[13px]" aria-live="polite">
                        <li class="flex items-center gap-2 transition-colors" :class="hasMinLength ? 'text-[#304C3D] font-medium' : 'text-[#777D74]'">
                            <template x-if="hasMinLength">
                                <svg class="w-4 h-4 shrink-0 text-[#304C3D]" fill="none" stroke="currentColor" viewBox="0 0 24 24" aria-hidden="true">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7" />
                                </svg>
                            </template>
                            <template x-if="!hasMinLength">
                                <svg class="w-4 h-4 shrink-0 text-[#A8B7A0]" fill="none" stroke="currentColor" viewBox="0 0 24 24" aria-hidden="true">
                                    <circle cx="12" cy="12" r="8" stroke-width="1.5" />
                                </svg>
                            </template>
                            <span>At least 8 characters</span>
                        </li>
                        <li class="flex items-center gap-2 transition-colors" :class="hasLetter ? 'text-[#304C3D] font-medium' : 'text-[#777D74]'">
                            <template x-if="hasLetter">
                                <svg class="w-4 h-4 shrink-0 text-[#304C3D]" fill="none" stroke="currentColor" viewBox="0 0 24 24" aria-hidden="true">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7" />
                                </svg>
                            </template>
                            <template x-if="!hasLetter">
                                <svg class="w-4 h-4 shrink-0 text-[#A8B7A0]" fill="none" stroke="currentColor" viewBox="0 0 24 24" aria-hidden="true">
                                    <circle cx="12" cy="12" r="8" stroke-width="1.5" />
                                </svg>
                            </template>
                            <span>At least one letter</span>
                        </li>
                        <li class="flex items-center gap-2 transition-colors" :class="hasNumber ? 'text-[#304C3D] font-medium' : 'text-[#777D74]'">
                            <template x-if="hasNumber">
                                <svg class="w-4 h-4 shrink-0 text-[#304C3D]" fill="none" stroke="currentColor" viewBox="0 0 24 24" aria-hidden="true">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7" />
                                </svg>
                            </template>
                            <template x-if="!hasNumber">
                                <svg class="w-4 h-4 shrink-0 text-[#A8B7A0]" fill="none" stroke="currentColor" viewBox="0 0 24 24" aria-hidden="true">
                                    <circle cx="12" cy="12" r="8" stroke-width="1.5" />
                                </svg>
                            </template>
                            <span>At least one number</span>
                        </li>
                    </ul>
                </div>

                <!-- Confirm Password -->
                <div>
                    <label for="reg-confirm_password" class="block text-xs uppercase tracking-[0.2em] text-muted font-medium mb-1.5">
                        Confirm Password <span class="text-forest" aria-hidden="true">*</span>
                    </label>
                    <div class="relative">
                        <input 
                            :type="showConfirmPassword ? 'text' : 'password'" 
                            id="reg-confirm_password" 
                            name="password_confirmation"
                            x-model="confirmPassword" 
                            @input="confirmTouched = true; clearError('confirmPassword')"
                            @blur="confirmTouched = true"
                            autocomplete="new-password" 
                            data-lpignore="true"
                            required 
                            class="input-field pr-12" 
                            :class="errors.confirmPassword ? 'input-error !border-[#B3453A]' : ''"
                            :aria-invalid="errors.confirmPassword ? 'true' : 'false'"
                            :aria-describedby="errors.confirmPassword ? 'err-reg-confirm_password' : null"
                        />
                        <button 
                            type="button" 
                            @click="showConfirmPassword = !showConfirmPassword" 
                            class="absolute right-0.5 top-1/2 -translate-y-1/2 h-11 w-11 min-w-[44px] min-h-[44px] flex items-center justify-center text-muted hover:text-ink focus-visible:outline-none focus-visible:ring-2 focus-visible:ring-[#304C3D] focus-visible:ring-offset-1 cursor-pointer"
                            :aria-label="showConfirmPassword ? 'Hide password' : 'Show password'"
                            :aria-pressed="showConfirmPassword ? 'true' : 'false'"
                        >
                            <template x-if="!showConfirmPassword">
                                <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24" aria-hidden="true">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.75" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z" />
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.75" d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z" />
                                </svg>
                            </template>
                            <template x-if="showConfirmPassword">
                                <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24" aria-hidden="true">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.75" d="M13.875 18.825A10.05 10.05 0 0112 19c-4.478 0-8.268-2.943-9.543-7a9.97 9.97 0 011.563-3.029m5.858.908a3 3 0 114.243 4.243M9.878 9.878l4.242 4.242M9.88 9.88l-3.29-3.29m7.532 7.532l3.29 3.29M3 3l18 18" />
                                </svg>
                            </template>
                        </button>
                    </div>
                    <div x-show="confirmPassword.length > 0 && !errors.confirmPassword" x-cloak class="mt-1.5 text-[13px]" aria-live="polite">
                        <p x-show="password === confirmPassword" class="text-[#304C3D] font-medium flex items-center gap-1.5">
                            <svg class="w-4 h-4 shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24" aria-hidden="true">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7" />
                            </svg>
                            <span>Passwords match</span>
                        </p>
                        <p x-show="confirmTouched && password !== confirmPassword" class="text-[#B3453A] font-medium">
                            Passwords don't match
                        </p>
                    </div>
                    <p id="err-reg-confirm_password" x-show="errors.confirmPassword" x-text="errors.confirmPassword" class="mt-1 text-[13px] text-[#B3453A] font-medium" role="alert" x-cloak></p>
                </div>

                <!-- Terms & Privacy Agreement -->
                <div>
                    <label for="reg-terms" class="min-h-[44px] flex items-center gap-3 text-sm text-muted leading-relaxed select-none cursor-pointer">
                        <input 
                            type="checkbox" 
                            id="reg-terms" 
                            name="terms"
                            value="1"
                            x-model="agreeTerms" 
                            @change="clearError('agreeTerms')"
                            :aria-invalid="errors.agreeTerms ? 'true' : 'false'"
                            :aria-describedby="errors.agreeTerms ? 'err-reg-terms' : null"
                            class="w-5 h-5 shrink-0 rounded-none accent-[#304C3D] border-[#D5DBCF] cursor-pointer"
                        />
                        <span>
                            I agree to The Everbloom's 
                            <a href="{{ route('terms') }}" class="text-[#304C3D] underline underline-offset-4 decoration-[#A8B7A0] hover:decoration-[#304C3D]">Terms</a> and 
                            <a href="{{ route('privacy') }}" class="text-[#304C3D] underline underline-offset-4 decoration-[#A8B7A0] hover:decoration-[#304C3D]">Privacy Policy</a>.
                        </span>
                    </label>
                    <p id="err-reg-terms" x-show="errors.agreeTerms" x-text="errors.agreeTerms" class="mt-1 text-[13px] text-[#B3453A] font-medium" role="alert" x-cloak></p>
                </div>

                <!-- Primary Submit -->
                <div class="pt-1">
                    <button 
                        type="submit" 
                        class="btn-primary w-full min-h-[48px] cursor-pointer"
                    >
                        Create Account
                    </button>
                </div>
            </form>

            <!-- Bottom Cross-link -->
            <div class="text-center pt-6 border-t border-[#ECEEE6] text-[13px] text-muted">
                <p>
                    Already have an account? 
                    <a href="{{ route('login') }}" class="text-[#304C3D] font-medium underline underline-offset-4 decoration-[#A8B7A0] hover:decoration-[#304C3D] focus-visible:outline-none focus-visible:ring-2 focus-visible:ring-[#304C3D] focus-visible:ring-offset-2 ml-1">
                        Sign in
                    </a>
                </p>
            </div>
        </div>
    </div>
</div>
@endsection
