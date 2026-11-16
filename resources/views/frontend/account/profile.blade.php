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
        deleteFeedback: '',
        confirmingDelete: false,
        profileForm: {
            first_name: @js($customer['first_name'] ?? 'Eleanor'),
            last_name: @js($customer['last_name'] ?? 'Vance'),
            email: @js($customer['email'] ?? 'eleanor.vance@example.com'),
            phone: @js($customer['phone'] ?? '+1 (503) 555-0192'),
        },
        profileErrors: {},
        clearProfileError(field) {
            if (this.profileErrors[field]) delete this.profileErrors[field];
        },
        validateProfile() {
            this.profileErrors = {};
            if (!this.profileForm.first_name.trim()) this.profileErrors.first_name = 'Please enter your first name.';
            if (!this.profileForm.last_name.trim()) this.profileErrors.last_name = 'Please enter your last name.';
            if (!this.profileForm.email.trim()) {
                this.profileErrors.email = 'Please enter your email address.';
            } else if (!/^[^\s@]+@[^\s@]+\.[^\s@]+$/.test(this.profileForm.email.trim())) {
                this.profileErrors.email = 'Please enter a valid email address.';
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
        async submitProfile() {
            if (!this.validateProfile()) return;
            const csrf = document.querySelector('meta[name="csrf-token"]')?.getAttribute('content') || '';
            try {
                const res = await fetch(@js(route('account.profile.update')), {
                    method: 'PUT',
                    headers: {
                        'Content-Type': 'application/json',
                        'Accept': 'application/json',
                        'X-CSRF-TOKEN': csrf,
                    },
                    body: JSON.stringify(this.profileForm),
                });
                const data = await res.json();
                if (!res.ok) {
                    if (data.errors) {
                        Object.entries(data.errors).forEach(([k, v]) => {
                            this.profileErrors[k] = Array.isArray(v) ? v[0] : v;
                        });
                    }
                    return;
                }
                this.profileFeedback = data.message || 'Profile updated successfully.';
                setTimeout(() => { this.profileFeedback = ''; }, 6000);
            } catch (e) {
                this.profileFeedback = 'Unable to update profile.';
            }
        },

        securityForm: {
            current_password: '',
            new_password: '',
            confirm_password: '',
        },
        showCurrent: false,
        showNew: false,
        showConfirm: false,
        confirmTouched: false,
        securityErrors: {},
        get hasMinLength() {
            return this.securityForm.new_password.length >= 8;
        },
        get hasLetter() {
            return /[A-Za-z]/.test(this.securityForm.new_password);
        },
        get hasNumber() {
            return /\d/.test(this.securityForm.new_password);
        },
        clearSecurityError(field) {
            if (this.securityErrors[field]) delete this.securityErrors[field];
        },
        validateSecurity() {
            this.securityErrors = {};
            if (!this.securityForm.current_password) {
                this.securityErrors.current_password = 'Please enter your current password.';
            }
            if (!this.securityForm.new_password) {
                this.securityErrors.new_password = 'Please enter a new password.';
            } else if (!this.hasMinLength || !this.hasLetter || !this.hasNumber) {
                this.securityErrors.new_password = 'Password must be at least 8 characters and include a letter and a number.';
            }
            if (!this.securityForm.confirm_password) {
                this.securityErrors.confirm_password = 'Please confirm your new password.';
            } else if (this.securityForm.new_password !== this.securityForm.confirm_password) {
                this.securityErrors.confirm_password = 'Passwords don\'t match.';
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
        async submitSecurity() {
            if (!this.validateSecurity()) return;
            const csrf = document.querySelector('meta[name="csrf-token"]')?.getAttribute('content') || '';
            try {
                const res = await fetch(@js(route('account.password.update')), {
                    method: 'PUT',
                    headers: {
                        'Content-Type': 'application/json',
                        'Accept': 'application/json',
                        'X-CSRF-TOKEN': csrf,
                    },
                    body: JSON.stringify({
                        current_password: this.securityForm.current_password,
                        password: this.securityForm.new_password,
                        password_confirmation: this.securityForm.confirm_password,
                    }),
                });
                const data = await res.json();
                if (!res.ok) {
                    if (data.errors) {
                        if (data.errors.current_password) this.securityErrors.current_password = data.errors.current_password[0];
                        if (data.errors.password) this.securityErrors.new_password = data.errors.password[0];
                    }
                    return;
                }
                this.securityFeedback = data.message || 'Password updated successfully.';
                this.securityForm = { current_password: '', new_password: '', confirm_password: '' };
                this.confirmTouched = false;
                setTimeout(() => { this.securityFeedback = ''; }, 6000);
            } catch (e) {
                this.securityFeedback = 'Unable to update password.';
            }
        },
        openDeleteConfirm() {
            this.confirmingDelete = true;
            this.$nextTick(() => {
                this.$refs.cancelDeleteBtn?.focus();
            });
        },
        closeDeleteConfirm() {
            this.confirmingDelete = false;
            this.$nextTick(() => {
                this.$refs.deleteTriggerBtn?.focus();
            });
        },
        async confirmDeleteAccount() {
            const csrf = document.querySelector('meta[name="csrf-token"]')?.getAttribute('content') || '';
            try {
                const res = await fetch(@js(route('account.destroy')), {
                    method: 'DELETE',
                    headers: {
                        'Accept': 'application/json',
                        'X-CSRF-TOKEN': csrf,
                    },
                });
                const data = await res.json();
                if (res.ok) {
                    window.location.href = data.redirect_url || '/';
                }
            } catch (e) {
                this.confirmingDelete = false;
            }
        }
    }" 
    @keydown.escape.window="if (confirmingDelete) { closeDeleteConfirm(); }"
    class="pb-16"
>
    <div class="grid grid-cols-1 lg:grid-cols-12 gap-10 lg:gap-12 items-start">
        <!-- Left Column: Profile & Password Forms -->
        <div class="lg:col-span-7 space-y-12 min-w-0">
            <!-- Personal Information Section -->
            <section aria-labelledby="personal-info-heading" class="space-y-6">
                <div class="border-b border-[#ECEEE6] pb-3">
                    <h2 id="personal-info-heading" class="font-serif text-xl sm:text-2xl text-ink font-normal leading-tight">
                        Personal Information
                    </h2>
                    <p class="text-xs text-muted mt-1">
                        Sample customer profile loaded for demonstration. Update fields to preview validation.
                    </p>
                </div>

                <!-- Feedback notice -->
                <div 
                    x-show="profileFeedback" 
                    x-cloak 
                    x-transition 
                    class="p-4 bg-forest text-ivory text-xs uppercase tracking-[0.2em] flex items-center justify-between"
                    role="status"
                    aria-live="polite"
                >
                    <span x-text="profileFeedback"></span>
                    <button type="button" @click="profileFeedback = ''" class="w-11 h-11 -mr-2 hover:text-sage-light text-ivory flex items-center justify-center cursor-pointer" aria-label="Close message">✕</button>
                </div>

                <form @submit.prevent="submitProfile()" novalidate class="space-y-5">
                    <!-- First and Last Name -->
                    <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
                        <div>
                            <label for="profile-first_name" class="block text-xs uppercase tracking-[0.2em] text-muted font-medium mb-1.5">
                                First Name <span class="text-forest" aria-hidden="true">*</span>
                            </label>
                            <input 
                                type="text" 
                                id="profile-first_name" 
                                x-model="profileForm.first_name" 
                                @input="clearProfileError('first_name')"
                                autocomplete="given-name" 
                                placeholder="First name"
                                class="input-field" 
                                :class="profileErrors.first_name ? 'input-error !border-[#B3453A]' : ''"
                                :aria-invalid="profileErrors.first_name ? 'true' : 'false'"
                                :aria-describedby="profileErrors.first_name ? 'err-profile-first_name' : null"
                            />
                            <p id="err-profile-first_name" x-show="profileErrors.first_name" x-text="profileErrors.first_name" class="mt-1 text-[13px] text-[#B3453A] font-medium" role="alert" x-cloak></p>
                        </div>

                        <div>
                            <label for="profile-last_name" class="block text-xs uppercase tracking-[0.2em] text-muted font-medium mb-1.5">
                                Last Name <span class="text-forest" aria-hidden="true">*</span>
                            </label>
                            <input 
                                type="text" 
                                id="profile-last_name" 
                                x-model="profileForm.last_name" 
                                @input="clearProfileError('last_name')"
                                autocomplete="family-name" 
                                placeholder="Last name"
                                class="input-field" 
                                :class="profileErrors.last_name ? 'input-error !border-[#B3453A]' : ''"
                                :aria-invalid="profileErrors.last_name ? 'true' : 'false'"
                                :aria-describedby="profileErrors.last_name ? 'err-profile-last_name' : null"
                            />
                            <p id="err-profile-last_name" x-show="profileErrors.last_name" x-text="profileErrors.last_name" class="mt-1 text-[13px] text-[#B3453A] font-medium" role="alert" x-cloak></p>
                        </div>
                    </div>

                    <!-- Email Address -->
                    <div>
                        <label for="profile-email" class="block text-xs uppercase tracking-[0.2em] text-muted font-medium mb-1.5">
                            Email Address <span class="text-forest" aria-hidden="true">*</span>
                        </label>
                        <input 
                            type="email" 
                            id="profile-email" 
                            x-model="profileForm.email" 
                            @input="clearProfileError('email')"
                            autocomplete="email" 
                            inputmode="email"
                            placeholder="name@example.com"
                            class="input-field" 
                            :class="profileErrors.email ? 'input-error !border-[#B3453A]' : ''"
                            :aria-invalid="profileErrors.email ? 'true' : 'false'"
                            :aria-describedby="profileErrors.email ? 'err-profile-email helper-profile-email' : 'helper-profile-email'"
                        />
                        <p id="helper-profile-email" class="mt-1.5 text-[13px] text-muted">
                            Changing your email sends a confirmation to the new address (demo only).
                        </p>
                        <p id="err-profile-email" x-show="profileErrors.email" x-text="profileErrors.email" class="mt-1 text-[13px] text-[#B3453A] font-medium" role="alert" x-cloak></p>
                    </div>

                    <!-- Phone (Optional) -->
                    <div>
                        <label for="profile-phone" class="block text-xs uppercase tracking-[0.2em] text-muted font-medium mb-1.5">
                            Phone Number <span class="text-muted font-normal lowercase">(optional)</span>
                        </label>
                        <input 
                            type="tel" 
                            id="profile-phone" 
                            x-model="profileForm.phone" 
                            autocomplete="tel" 
                            placeholder="Phone number"
                            class="input-field" 
                        />
                    </div>

                    <div class="pt-2 flex flex-col sm:flex-row sm:items-center gap-3 sm:gap-4">
                        <button 
                            type="submit" 
                            class="btn-primary w-full sm:w-auto min-h-[44px] cursor-pointer"
                        >
                            Update Profile
                        </button>
                        <p class="text-[13px] text-muted">
                            Demo only. Changes are not saved.
                        </p>
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
                        Update your account password to keep your profile secure.
                    </p>
                </div>

                <!-- Feedback notice -->
                <div 
                    x-show="securityFeedback" 
                    x-cloak 
                    x-transition 
                    class="p-4 bg-forest text-ivory text-xs uppercase tracking-[0.2em] flex items-center justify-between"
                    role="status"
                    aria-live="polite"
                >
                    <span x-text="securityFeedback"></span>
                    <button type="button" @click="securityFeedback = ''" class="w-11 h-11 -mr-2 hover:text-sage-light text-ivory flex items-center justify-center cursor-pointer" aria-label="Close message">✕</button>
                </div>

                <form @submit.prevent="submitSecurity()" novalidate class="space-y-5">
                    <!-- Current Password -->
                    <div>
                        <label for="security-current_password" class="block text-xs uppercase tracking-[0.2em] text-muted font-medium mb-1.5">
                            Current Password <span class="text-forest" aria-hidden="true">*</span>
                        </label>
                        <div class="relative">
                            <input 
                                :type="showCurrent ? 'text' : 'password'" 
                                id="security-current_password" 
                                x-model="securityForm.current_password" 
                                @input="clearSecurityError('current_password')"
                                autocomplete="current-password" 
                                class="input-field pr-12" 
                                :class="securityErrors.current_password ? 'input-error !border-[#B3453A]' : ''"
                                :aria-invalid="securityErrors.current_password ? 'true' : 'false'"
                                :aria-describedby="securityErrors.current_password ? 'err-security-current' : null"
                            />
                            <button 
                                type="button" 
                                @click="showCurrent = !showCurrent" 
                                class="absolute right-0.5 top-1/2 -translate-y-1/2 h-11 w-11 min-w-[44px] min-h-[44px] flex items-center justify-center text-muted hover:text-ink focus-visible:outline-none focus-visible:ring-2 focus-visible:ring-[#304C3D] focus-visible:ring-offset-1 cursor-pointer"
                                :aria-label="showCurrent ? 'Hide password' : 'Show password'"
                                :aria-pressed="showCurrent ? 'true' : 'false'"
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
                        <p id="err-security-current" x-show="securityErrors.current_password" x-text="securityErrors.current_password" class="mt-1 text-[13px] text-[#B3453A] font-medium" role="alert" x-cloak></p>
                    </div>

                    <!-- New Password -->
                    <div>
                        <label for="security-new_password" class="block text-xs uppercase tracking-[0.2em] text-muted font-medium mb-1.5">
                            New Password <span class="text-forest" aria-hidden="true">*</span>
                        </label>
                        <div class="relative">
                            <input 
                                :type="showNew ? 'text' : 'password'" 
                                id="security-new_password" 
                                x-model="securityForm.new_password" 
                                @input="clearSecurityError('new_password')"
                                autocomplete="new-password" 
                                data-lpignore="true"
                                class="input-field pr-12" 
                                :class="securityErrors.new_password ? 'input-error !border-[#B3453A]' : ''"
                                :aria-invalid="securityErrors.new_password ? 'true' : 'false'"
                                :aria-describedby="securityErrors.new_password ? 'err-security-new helper-security-new' : 'helper-security-new'"
                            />
                            <button 
                                type="button" 
                                @click="showNew = !showNew" 
                                class="absolute right-0.5 top-1/2 -translate-y-1/2 h-11 w-11 min-w-[44px] min-h-[44px] flex items-center justify-center text-muted hover:text-ink focus-visible:outline-none focus-visible:ring-2 focus-visible:ring-[#304C3D] focus-visible:ring-offset-1 cursor-pointer"
                                :aria-label="showNew ? 'Hide password' : 'Show password'"
                                :aria-pressed="showNew ? 'true' : 'false'"
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
                        <p id="helper-security-new" class="mt-1.5 text-[13px] text-muted">
                            Choose a strong password you don't use elsewhere.
                        </p>
                        <p id="err-security-new" x-show="securityErrors.new_password" x-text="securityErrors.new_password" class="mt-1 text-[13px] text-[#B3453A] font-medium" role="alert" x-cloak></p>

                        <!-- Live Password Checklist -->
                        <ul class="mt-2.5 space-y-1.5 text-[13px]" aria-live="polite">
                            <li class="flex items-center gap-2 transition-colors" :class="hasMinLength ? 'text-[#304C3D] font-medium' : 'text-muted'">
                                <template x-if="hasMinLength">
                                    <svg class="w-4 h-4 shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24" aria-hidden="true">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7" />
                                    </svg>
                                </template>
                                <template x-if="!hasMinLength">
                                    <svg class="w-4 h-4 shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24" aria-hidden="true">
                                        <circle cx="12" cy="12" r="8" stroke-width="1.5" />
                                    </svg>
                                </template>
                                <span>At least 8 characters</span>
                            </li>
                            <li class="flex items-center gap-2 transition-colors" :class="hasLetter ? 'text-[#304C3D] font-medium' : 'text-muted'">
                                <template x-if="hasLetter">
                                    <svg class="w-4 h-4 shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24" aria-hidden="true">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7" />
                                    </svg>
                                </template>
                                <template x-if="!hasLetter">
                                    <svg class="w-4 h-4 shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24" aria-hidden="true">
                                        <circle cx="12" cy="12" r="8" stroke-width="1.5" />
                                    </svg>
                                </template>
                                <span>At least one letter</span>
                            </li>
                            <li class="flex items-center gap-2 transition-colors" :class="hasNumber ? 'text-[#304C3D] font-medium' : 'text-muted'">
                                <template x-if="hasNumber">
                                    <svg class="w-4 h-4 shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24" aria-hidden="true">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7" />
                                    </svg>
                                </template>
                                <template x-if="!hasNumber">
                                    <svg class="w-4 h-4 shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24" aria-hidden="true">
                                        <circle cx="12" cy="12" r="8" stroke-width="1.5" />
                                    </svg>
                                </template>
                                <span>At least one number</span>
                            </li>
                        </ul>
                    </div>

                    <!-- Confirm New Password -->
                    <div>
                        <label for="security-confirm_password" class="block text-xs uppercase tracking-[0.2em] text-muted font-medium mb-1.5">
                            Confirm New Password <span class="text-forest" aria-hidden="true">*</span>
                        </label>
                        <div class="relative">
                            <input 
                                :type="showConfirm ? 'text' : 'password'" 
                                id="security-confirm_password" 
                                x-model="securityForm.confirm_password" 
                                @input="confirmTouched = true; clearSecurityError('confirm_password')"
                                @blur="confirmTouched = true"
                                autocomplete="new-password" 
                                data-lpignore="true"
                                class="input-field pr-12" 
                                :class="securityErrors.confirm_password ? 'input-error !border-[#B3453A]' : ''"
                                :aria-invalid="securityErrors.confirm_password ? 'true' : 'false'"
                                :aria-describedby="securityErrors.confirm_password ? 'err-security-confirm' : null"
                            />
                            <button 
                                type="button" 
                                @click="showConfirm = !showConfirm" 
                                class="absolute right-0.5 top-1/2 -translate-y-1/2 h-11 w-11 min-w-[44px] min-h-[44px] flex items-center justify-center text-muted hover:text-ink focus-visible:outline-none focus-visible:ring-2 focus-visible:ring-[#304C3D] focus-visible:ring-offset-1 cursor-pointer"
                                :aria-label="showConfirm ? 'Hide password' : 'Show password'"
                                :aria-pressed="showConfirm ? 'true' : 'false'"
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
                        <!-- Live match/mismatch indicator -->
                        <div x-show="securityForm.confirm_password.length > 0 && !securityErrors.confirm_password" x-cloak class="mt-1.5 text-[13px]" aria-live="polite">
                            <p x-show="securityForm.new_password === securityForm.confirm_password" class="text-[#304C3D] font-medium flex items-center gap-1.5">
                                <svg class="w-4 h-4 shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24" aria-hidden="true">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7" />
                                </svg>
                                <span>Passwords match</span>
                            </p>
                            <p x-show="confirmTouched && securityForm.new_password !== securityForm.confirm_password" class="text-[#B3453A] font-medium">
                                Passwords don't match
                            </p>
                        </div>
                        <p id="err-security-confirm" x-show="securityErrors.confirm_password" x-text="securityErrors.confirm_password" class="mt-1 text-[13px] text-[#B3453A] font-medium" role="alert" x-cloak></p>
                    </div>

                    <div class="pt-2 flex flex-col sm:flex-row sm:items-center gap-3 sm:gap-4">
                        <button 
                            type="submit" 
                            class="btn-secondary w-full sm:w-auto min-h-[44px] cursor-pointer"
                        >
                            Change Password
                        </button>
                        <p class="text-[13px] text-muted">
                            Demo only. Changes are not saved.
                        </p>
                    </div>
                </form>
            </section>
        </div>

        <!-- Right Column (Desktop) / Below Password Form (Mobile): Quiet Account Info & Delete -->
        <aside class="lg:col-span-5 pt-8 lg:pt-0 border-t lg:border-t-0 lg:border-l border-[#ECEEE6] lg:pl-10" aria-label="Account details">
            <div class="space-y-4">
                <div class="space-y-1">
                    <span class="text-xs uppercase tracking-[0.2em] text-muted font-medium block">
                        Member since
                    </span>
                    <p class="text-sm text-ink">
                        {{ $customer['member_since'] ?? 'March 2024' }}
                    </p>
                </div>

                <div>
                    <a 
                        href="{{ route('account.addresses') }}" 
                        class="min-h-[44px] inline-flex items-center text-sm text-[#304C3D] hover:text-ink font-medium underline underline-offset-4 decoration-[#A8B7A0] hover:decoration-[#304C3D] focus-visible:outline-none focus-visible:ring-2 focus-visible:ring-[#304C3D] focus-visible:ring-offset-2 transition-colors"
                    >
                        Manage addresses
                    </a>
                </div>
            </div>

            <div class="mt-6 space-y-3">
                <div 
                    x-show="deleteFeedback" 
                    x-cloak 
                    class="p-3 bg-forest text-ivory text-xs uppercase tracking-[0.2em]"
                    role="status"
                    aria-live="polite"
                    x-text="deleteFeedback"
                ></div>

                <div x-show="!confirmingDelete">
                    <button 
                        type="button" 
                        x-ref="deleteTriggerBtn"
                        @click="openDeleteConfirm()"
                        class="min-h-[44px] inline-flex items-center text-sm text-muted hover:text-ink underline underline-offset-4 focus-visible:outline-none focus-visible:ring-2 focus-visible:ring-[#304C3D] focus-visible:ring-offset-2 cursor-pointer transition-colors"
                    >
                        Delete account
                    </button>
                </div>

                <div 
                    x-show="confirmingDelete" 
                    x-cloak 
                    class="border border-[#E3E6DD] bg-white p-4 space-y-3"
                    role="alertdialog"
                    aria-label="Confirm account deletion"
                >
                    <p class="text-sm text-ink leading-relaxed">
                        Delete your account? This is a demo and nothing will be deleted.
                    </p>
                    <div class="flex flex-wrap items-center gap-4">
                        <button 
                            type="button" 
                            x-ref="cancelDeleteBtn"
                            @click="closeDeleteConfirm()"
                            class="min-h-[44px] inline-flex items-center text-sm text-[#304C3D] hover:text-ink font-medium underline underline-offset-4 decoration-[#A8B7A0] hover:decoration-[#304C3D] focus-visible:outline-none focus-visible:ring-2 focus-visible:ring-[#304C3D] focus-visible:ring-offset-2 cursor-pointer"
                        >
                            Cancel
                        </button>
                        <button 
                            type="button" 
                            @click="confirmDeleteAccount()"
                            class="min-h-[44px] inline-flex items-center text-sm text-[#B3453A] hover:text-ink font-medium underline underline-offset-4 focus-visible:outline-none focus-visible:ring-2 focus-visible:ring-[#304C3D] focus-visible:ring-offset-2 cursor-pointer"
                        >
                            Confirm
                        </button>
                    </div>
                </div>
            </div>
        </aside>
    </div>
</div>
@endsection
