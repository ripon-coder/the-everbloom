@extends('layouts.app')

@section('title', 'Forgot Password â€” ' . config('demo.site.name'))
@section('meta_description', 'Reset your password for The Everbloom account.')
@section('meta_robots', 'noindex, nofollow')
@section('hide_newsletter', true)

@section('content')
<div class="site-container max-sm:!px-4 py-12 lg:py-16">
    <!-- Breadcrumbs -->
    <div class="mb-6">
        <x-storefront.breadcrumbs :items="[
            ['label' => 'Forgot Password', 'url' => ''],
        ]" />
    </div>

    <!-- Single Centered Column -->
    <div class="mx-auto w-full max-w-md">
        <div 
            x-data="{
                email: @js(old('email', '')),
                errors: {
                    @error('email') email: @js($message), @enderror
                },
                demoNotice: @js(session('status', '')),
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
                    if (Object.keys(this.errors).length > 0) {
                        this.$nextTick(() => {
                            document.getElementById('forgot-email')?.focus();
                        });
                        return false;
                    }
                    return true;
                },
                submit() {
                    if (!this.validate()) return;
                    this.$refs.forgotForm.submit();
                }
            }" 
            class="bg-white border border-[#E3E6DD] p-6 sm:p-8 space-y-6"
        >
            <!-- Header -->
            <div class="border-b border-[#ECEEE6] pb-5">
                <span class="text-xs uppercase tracking-[0.2em] font-medium text-forest block mb-1">
                    ACCOUNT RECOVERY
                </span>
                <h1 class="font-serif text-3xl sm:text-4xl text-ink font-normal tracking-tight">
                    Forgot Password
                </h1>
                <p class="text-sm text-muted mt-1.5 leading-relaxed">
                    Enter your email address and we'll send instructions to reset your password.
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

            <!-- Email Form -->
            <form x-ref="forgotForm" method="POST" action="{{ route('password.email') }}" @submit.prevent="submit()" class="space-y-5" novalidate>
                @csrf
                <div>
                    <label for="forgot-email" class="block text-xs uppercase tracking-[0.2em] text-muted font-medium mb-1.5">
                        Email Address <span class="text-forest" aria-hidden="true">*</span>
                    </label>
                    <input 
                        type="email" 
                        id="forgot-email" 
                        name="email"
                        x-model="email" 
                        @input="clearError('email')"
                        autocomplete="email" 
                        inputmode="email"
                        required 
                        class="input-field" 
                        :class="errors.email ? 'input-error !border-[#B3453A]' : ''"
                        :aria-invalid="errors.email ? 'true' : 'false'"
                        :aria-describedby="errors.email ? 'err-forgot-email' : null"
                    />
                    <p id="err-forgot-email" x-show="errors.email" x-text="errors.email" class="mt-1 text-[13px] text-[#B3453A] font-medium" role="alert" x-cloak></p>
                </div>

                <!-- Primary Submit -->
                <div class="pt-1">
                    <button 
                        type="submit" 
                        class="btn-primary w-full min-h-[48px] cursor-pointer"
                    >
                        Send reset link
                    </button>
                </div>
            </form>

            <!-- Bottom Cross-link -->
            <div class="text-center pt-6 border-t border-[#ECEEE6] text-[13px] text-muted">
                <p>
                    <a href="{{ route('login') }}" class="text-[#304C3D] font-medium underline underline-offset-4 decoration-[#A8B7A0] hover:decoration-[#304C3D] focus-visible:outline-none focus-visible:ring-2 focus-visible:ring-[#304C3D] focus-visible:ring-offset-2">
                        Back to sign in
                    </a>
                </p>
            </div>
        </div>
    </div>
</div>
@endsection
