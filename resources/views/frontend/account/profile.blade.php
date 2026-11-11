@extends('layouts.account')

@section('title', 'Profile & Security — ' . config('demo.site.name'))

@section('breadcrumbs')
    <x-storefront.breadcrumbs :items="[
        ['label' => 'My Account', 'url' => route('account')],
        ['label' => 'Profile', 'url' => ''],
    ]" />
@endsection

@section('page_title', 'Profile')

@section('account_content')
<div 
    x-data="{
        profileFeedback: '',
        securityFeedback: '',
        profileForm: {
            first_name: '{{ $customer['first_name'] ?? 'Eleanor' }}',
            last_name: '{{ $customer['last_name'] ?? 'Vance' }}',
            email: '{{ $customer['email'] ?? 'eleanor.vance@example.com' }}',
            phone: '{{ $customer['phone'] ?? '+1 (503) 555-0192' }}',
        },
        profileErrors: {},
        validateProfile() {
            this.profileErrors = {};
            if (!this.profileForm.first_name.trim()) this.profileErrors.first_name = 'First name is required';
            if (!this.profileForm.last_name.trim()) this.profileErrors.last_name = 'Last name is required';
            if (!this.profileForm.email.trim()) {
                this.profileErrors.email = 'Email address is required';
            } else if (!/^[^\s@]+@[^\s@]+\.[^\s@]+$/.test(this.profileForm.email)) {
                this.profileErrors.email = 'Please provide a valid email address';
            }

            const keys = Object.keys(this.profileErrors);
            if (keys.length > 0) {
                const first = keys[0];
                this.$nextTick(() => {
                    const el = document.getElementById('profile-' + first);
                    if (el) el.focus();
                });
                return false;
            }
            return true;
        },
        submitProfile() {
            if (!this.validateProfile()) return;
            this.profileFeedback = 'Demo only. Nothing was saved.';
            setTimeout(() => { this.profileFeedback = ''; }, 6000);
        },

        securityForm: {
            current_password: '',
            new_password: '',
            confirm_password: '',
        },
        showCurrent: false,
        showNew: false,
        showConfirm: false,
        securityErrors: {},
        validateSecurity() {
            this.securityErrors = {};
            if (!this.securityForm.current_password) {
                this.securityErrors.current_password = 'Current password is required';
            }
            if (!this.securityForm.new_password) {
                this.securityErrors.new_password = 'New password is required';
            } else if (this.securityForm.new_password.length < 8) {
                this.securityErrors.new_password = 'Password must be at least 8 characters';
            }
            if (this.securityForm.new_password !== this.securityForm.confirm_password) {
                this.securityErrors.confirm_password = 'Passwords do not match';
            }

            const keys = Object.keys(this.securityErrors);
            if (keys.length > 0) {
                const first = keys[0];
                this.$nextTick(() => {
                    const el = document.getElementById('security-' + first);
                    if (el) el.focus();
                });
                return false;
            }
            return true;
        },
        submitSecurity() {
            if (!this.validateSecurity()) return;
            this.securityFeedback = 'Demo only. Nothing was saved.';
            this.securityForm = { current_password: '', new_password: '', confirm_password: '' };
            setTimeout(() => { this.securityFeedback = ''; }, 6000);
        }
    }" 
    class="space-y-12 max-w-xl"
>
    <!-- Personal Information Section -->
    <section aria-labelledby="personal-info-heading" class="space-y-6">
        <div class="border-b border-[#ECEEE6] pb-3">
            <h2 id="personal-info-heading" class="font-serif text-xl sm:text-2xl text-ink font-normal">
                Personal Information
            </h2>
            <p class="text-xs text-muted mt-1">
                Update your contact details for correspondence and dispatch notifications.
            </p>
        </div>

        <!-- Feedback notice -->
        <div 
            x-show="profileFeedback" 
            x-cloak 
            x-transition 
            class="p-4 bg-forest text-ivory text-xs uppercase tracking-wider flex items-center justify-between"
            role="status"
        >
            <span x-text="profileFeedback"></span>
            <button type="button" @click="profileFeedback = ''" class="p-1 hover:text-sage text-ivory min-h-[44px] min-w-[44px] flex items-center justify-center" aria-label="Close message">✕</button>
        </div>

        <form @submit.prevent="submitProfile()" novalidate class="space-y-5">
            <!-- First and Last Name -->
            <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
                <div>
                    <label for="profile-first_name" class="block text-xs uppercase tracking-[0.16em] text-muted font-medium mb-1.5">
                        First Name <span class="text-forest" aria-hidden="true">*</span>
                    </label>
                    <input 
                        type="text" 
                        id="profile-first_name" 
                        x-model="profileForm.first_name" 
                        autocomplete="given-name" 
                        class="input-field" 
                        :class="profileErrors.first_name ? 'input-error' : ''"
                        :aria-invalid="profileErrors.first_name ? 'true' : 'false'"
                        aria-describedby="err-profile-first_name"
                    />
                    <p id="err-profile-first_name" x-show="profileErrors.first_name" x-text="profileErrors.first_name" class="mt-1 text-xs text-[#B3453A]" x-cloak></p>
                </div>

                <div>
                    <label for="profile-last_name" class="block text-xs uppercase tracking-[0.16em] text-muted font-medium mb-1.5">
                        Last Name <span class="text-forest" aria-hidden="true">*</span>
                    </label>
                    <input 
                        type="text" 
                        id="profile-last_name" 
                        x-model="profileForm.last_name" 
                        autocomplete="family-name" 
                        class="input-field" 
                        :class="profileErrors.last_name ? 'input-error' : ''"
                        :aria-invalid="profileErrors.last_name ? 'true' : 'false'"
                        aria-describedby="err-profile-last_name"
                    />
                    <p id="err-profile-last_name" x-show="profileErrors.last_name" x-text="profileErrors.last_name" class="mt-1 text-xs text-[#B3453A]" x-cloak></p>
                </div>
            </div>

            <!-- Email Address -->
            <div>
                <label for="profile-email" class="block text-xs uppercase tracking-[0.16em] text-muted font-medium mb-1.5">
                    Email Address <span class="text-forest" aria-hidden="true">*</span>
                </label>
                <input 
                    type="email" 
                    id="profile-email" 
                    x-model="profileForm.email" 
                    autocomplete="email" 
                    class="input-field" 
                    :class="profileErrors.email ? 'input-error' : ''"
                    :aria-invalid="profileErrors.email ? 'true' : 'false'"
                    aria-describedby="err-profile-email"
                />
                <p id="err-profile-email" x-show="profileErrors.email" x-text="profileErrors.email" class="mt-1 text-xs text-[#B3453A]" x-cloak></p>
            </div>

            <!-- Phone (Optional) -->
            <div>
                <label for="profile-phone" class="block text-xs uppercase tracking-[0.16em] text-muted font-medium mb-1.5">
                    Phone Number <span class="text-muted/60 font-normal lowercase">(optional)</span>
                </label>
                <input 
                    type="tel" 
                    id="profile-phone" 
                    x-model="profileForm.phone" 
                    autocomplete="tel" 
                    class="input-field" 
                />
            </div>

            <div class="pt-2">
                <button 
                    type="submit" 
                    class="btn-primary w-full sm:w-auto min-h-[44px] cursor-pointer"
                >
                    Update profile
                </button>
            </div>
        </form>
    </section>

    <!-- Security & Password Section -->
    <section aria-labelledby="security-heading" class="space-y-6 pt-10 border-t border-[#ECEEE6]">
        <div class="border-b border-[#ECEEE6] pb-3">
            <h2 id="security-heading" class="font-serif text-xl sm:text-2xl text-ink font-normal">
                Security & Password
            </h2>
            <p class="text-xs text-muted mt-1">
                Ensure your client credentials use a strong, unique passkey.
            </p>
        </div>

        <!-- Feedback notice -->
        <div 
            x-show="securityFeedback" 
            x-cloak 
            x-transition 
            class="p-4 bg-forest text-ivory text-xs uppercase tracking-wider flex items-center justify-between"
            role="status"
        >
            <span x-text="securityFeedback"></span>
            <button type="button" @click="securityFeedback = ''" class="p-1 hover:text-sage text-ivory min-h-[44px] min-w-[44px] flex items-center justify-center" aria-label="Close message">✕</button>
        </div>

        <form @submit.prevent="submitSecurity()" novalidate class="space-y-5">
            <!-- Current Password -->
            <div>
                <label for="security-current_password" class="block text-xs uppercase tracking-[0.16em] text-muted font-medium mb-1.5">
                    Current Password <span class="text-forest" aria-hidden="true">*</span>
                </label>
                <div class="relative">
                    <input 
                        :type="showCurrent ? 'text' : 'password'" 
                        id="security-current_password" 
                        x-model="securityForm.current_password" 
                        autocomplete="current-password" 
                        class="input-field pr-12" 
                        :class="securityErrors.current_password ? 'input-error' : ''"
                        :aria-invalid="securityErrors.current_password ? 'true' : 'false'"
                        aria-describedby="err-security-current"
                    />
                    <button 
                        type="button" 
                        @click="showCurrent = !showCurrent" 
                        class="absolute right-0 top-0 h-12 w-12 flex items-center justify-center text-muted hover:text-ink cursor-pointer"
                        :aria-label="showCurrent ? 'Hide current password' : 'Show current password'"
                    >
                        <template x-if="!showCurrent">
                            <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24" aria-hidden="true">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.75" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z" />
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.75" d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z" />
                            </svg>
                        </template>
                        <template x-if="showCurrent">
                            <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24" aria-hidden="true">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.75" d="M13.875 18.825A10.05 10.05 0 0112 19c-4.478 0-8.268-2.943-9.543-7a9.97 9.97 0 011.563-3.029m5.858.908a3 3 0 114.243 4.243M9.878 9.878l4.242 4.242M9.88 9.88l-3.29-3.29m7.532 7.532l3.29 3.29M3 3l18 18" />
                            </svg>
                        </template>
                    </button>
                </div>
                <p id="err-security-current" x-show="securityErrors.current_password" x-text="securityErrors.current_password" class="mt-1 text-xs text-[#B3453A]" x-cloak></p>
            </div>

            <!-- New Password -->
            <div>
                <label for="security-new_password" class="block text-xs uppercase tracking-[0.16em] text-muted font-medium mb-1.5">
                    New Password <span class="text-forest" aria-hidden="true">*</span>
                </label>
                <div class="relative">
                    <input 
                        :type="showNew ? 'text' : 'password'" 
                        id="security-new_password" 
                        x-model="securityForm.new_password" 
                        autocomplete="new-password" 
                        class="input-field pr-12" 
                        :class="securityErrors.new_password ? 'input-error' : ''"
                        :aria-invalid="securityErrors.new_password ? 'true' : 'false'"
                        aria-describedby="err-security-new"
                    />
                    <button 
                        type="button" 
                        @click="showNew = !showNew" 
                        class="absolute right-0 top-0 h-12 w-12 flex items-center justify-center text-muted hover:text-ink cursor-pointer"
                        :aria-label="showNew ? 'Hide new password' : 'Show new password'"
                    >
                        <template x-if="!showNew">
                            <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24" aria-hidden="true">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.75" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z" />
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.75" d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z" />
                            </svg>
                        </template>
                        <template x-if="showNew">
                            <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24" aria-hidden="true">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.75" d="M13.875 18.825A10.05 10.05 0 0112 19c-4.478 0-8.268-2.943-9.543-7a9.97 9.97 0 011.563-3.029m5.858.908a3 3 0 114.243 4.243M9.878 9.878l4.242 4.242M9.88 9.88l-3.29-3.29m7.532 7.532l3.29 3.29M3 3l18 18" />
                            </svg>
                        </template>
                    </button>
                </div>
                <p id="err-security-new" x-show="securityErrors.new_password" x-text="securityErrors.new_password" class="mt-1 text-xs text-[#B3453A]" x-cloak></p>
            </div>

            <!-- Confirm New Password -->
            <div>
                <label for="security-confirm_password" class="block text-xs uppercase tracking-[0.16em] text-muted font-medium mb-1.5">
                    Confirm New Password <span class="text-forest" aria-hidden="true">*</span>
                </label>
                <div class="relative">
                    <input 
                        :type="showConfirm ? 'text' : 'password'" 
                        id="security-confirm_password" 
                        x-model="securityForm.confirm_password" 
                        autocomplete="new-password" 
                        class="input-field pr-12" 
                        :class="securityErrors.confirm_password ? 'input-error' : ''"
                        :aria-invalid="securityErrors.confirm_password ? 'true' : 'false'"
                        aria-describedby="err-security-confirm"
                    />
                    <button 
                        type="button" 
                        @click="showConfirm = !showConfirm" 
                        class="absolute right-0 top-0 h-12 w-12 flex items-center justify-center text-muted hover:text-ink cursor-pointer"
                        :aria-label="showConfirm ? 'Hide confirm password' : 'Show confirm password'"
                    >
                        <template x-if="!showConfirm">
                            <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24" aria-hidden="true">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.75" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z" />
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.75" d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z" />
                            </svg>
                        </template>
                        <template x-if="showConfirm">
                            <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24" aria-hidden="true">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.75" d="M13.875 18.825A10.05 10.05 0 0112 19c-4.478 0-8.268-2.943-9.543-7a9.97 9.97 0 011.563-3.029m5.858.908a3 3 0 114.243 4.243M9.878 9.878l4.242 4.242M9.88 9.88l-3.29-3.29m7.532 7.532l3.29 3.29M3 3l18 18" />
                            </svg>
                        </template>
                    </button>
                </div>
                <p id="err-security-confirm" x-show="securityErrors.confirm_password" x-text="securityErrors.confirm_password" class="mt-1 text-xs text-[#B3453A]" x-cloak></p>
            </div>

            <div class="pt-2">
                <button 
                    type="submit" 
                    class="btn-primary w-full sm:w-auto min-h-[44px] cursor-pointer"
                >
                    Change password
                </button>
            </div>
        </form>
    </section>
</div>
@endsection
