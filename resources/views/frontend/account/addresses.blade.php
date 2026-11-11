@extends('layouts.account')

@section('title', 'Saved Addresses — ' . config('demo.site.name'))

@section('breadcrumbs')
    <x-storefront.breadcrumbs :items="[
        ['label' => 'My Account', 'url' => route('account')],
        ['label' => 'Addresses', 'url' => ''],
    ]" />
@endsection

@section('page_title', 'Addresses')

@section('account_content')
<div 
    x-data="{
        showForm: false,
        demoFeedback: '',
        form: {
            first_name: '',
            last_name: '',
            country: 'United States',
            address_line1: '',
            address_line2: '',
            city: '',
            state: '',
            postal_code: '',
            phone: '',
            is_default: false,
        },
        errors: {},
        resetForm() {
            this.form = {
                first_name: '',
                last_name: '',
                country: 'United States',
                address_line1: '',
                address_line2: '',
                city: '',
                state: '',
                postal_code: '',
                phone: '',
                is_default: false,
            };
            this.errors = {};
        },
        clearError(field) {
            if (this.errors[field]) {
                delete this.errors[field];
            }
        },
        validate() {
            this.errors = {};
            if (!this.form.first_name.trim()) this.errors.first_name = 'Please enter a first name.';
            if (!this.form.last_name.trim()) this.errors.last_name = 'Please enter a last name.';
            if (!this.form.country.trim()) this.errors.country = 'Please select a country.';
            if (!this.form.address_line1.trim()) this.errors.address_line1 = 'Please enter a street address.';
            if (!this.form.city.trim()) this.errors.city = 'Please enter a city.';
            if (!this.form.state.trim()) this.errors.state = 'Please enter a state or province.';
            if (!this.form.postal_code.trim()) this.errors.postal_code = 'Please enter a postal or ZIP code.';

            const keys = Object.keys(this.errors);
            if (keys.length > 0) {
                const firstKey = keys[0];
                this.$nextTick(() => {
                    const el = document.getElementById('field-' + firstKey);
                    if (el) el.focus();
                });
                return false;
            }
            return true;
        },
        submit() {
            if (!this.validate()) return;
            this.demoFeedback = 'Demo only. Nothing was saved.';
            this.showForm = false;
            window.scrollTo({ top: 0, behavior: 'smooth' });
            setTimeout(() => { this.demoFeedback = ''; }, 6000);
        },
        editAddress(addr) {
            this.form = {
                first_name: addr.first_name || '',
                last_name: addr.last_name || '',
                country: addr.country || 'United States',
                address_line1: addr.address_line1 || '',
                address_line2: addr.address_line2 || '',
                city: addr.city || '',
                state: addr.state || '',
                postal_code: addr.postal_code || '',
                phone: addr.phone || '',
                is_default: Boolean(addr.is_default),
            };
            this.errors = {};
            this.showForm = true;
            this.$nextTick(() => {
                const el = document.getElementById('field-first_name');
                if (el) {
                    el.focus();
                    el.scrollIntoView({ behavior: 'smooth', block: 'center' });
                }
            });
        },
        removeAddress(id) {
            this.demoFeedback = 'Demo only. Address removal was not processed.';
            window.scrollTo({ top: 0, behavior: 'smooth' });
            setTimeout(() => { this.demoFeedback = ''; }, 5000);
        }
    }"
    @keydown.escape.window="if(showForm) { showForm = false; }"
    class="space-y-8"
>
    <!-- Demo Feedback Notification Banner -->
    <div 
        x-show="demoFeedback" 
        x-cloak 
        x-transition:enter="transition ease-out duration-200"
        x-transition:enter-start="opacity-0 -translate-y-1"
        x-transition:enter-end="opacity-100 translate-y-0"
        x-transition:leave="transition ease-in duration-150"
        x-transition:leave-start="opacity-100 translate-y-0"
        x-transition:leave-end="opacity-0 -translate-y-1"
        class="p-4 bg-forest text-ivory text-xs uppercase tracking-[0.2em] flex items-center justify-between"
        role="status"
        aria-live="polite"
    >
        <span x-text="demoFeedback"></span>
        <button type="button" @click="demoFeedback = ''" class="w-11 h-11 -mr-2 hover:text-sage-light text-ivory flex items-center justify-center cursor-pointer" aria-label="Close message">✕</button>
    </div>

    <!-- Top Action Bar -->
    <div class="flex flex-col sm:flex-row sm:items-center sm:justify-between gap-4 pb-4 border-b border-[#ECEEE6]">
        <p class="text-sm text-muted">
            Manage your saved shipping destinations for faster checkout.
        </p>
        <button 
            type="button" 
            @click="resetForm(); showForm = true; $nextTick(() => document.getElementById('field-first_name')?.focus())"
            class="btn-primary w-full sm:w-auto shrink-0 cursor-pointer"
        >
            Add New Address
        </button>
    </div>

    <!-- Address Cards / List -->
    <section aria-labelledby="saved-addresses-heading">
        <h2 id="saved-addresses-heading" class="sr-only">Saved Delivery Addresses</h2>

        <div class="grid grid-cols-1 md:grid-cols-2 gap-6 lg:gap-8">
            @forelse($addresses as $addr)
                <div class="border border-[#E3E6DD] bg-white p-6 flex flex-col justify-between space-y-4">
                    <div class="space-y-3">
                        <div class="flex items-center justify-between gap-2">
                            <h3 class="font-serif text-lg text-ink font-medium">
                                {{ $addr['first_name'] }} {{ $addr['last_name'] }}
                            </h3>
                            @if(!empty($addr['is_default']))
                                <span class="inline-flex items-center px-2.5 py-0.5 text-xs font-medium uppercase tracking-[0.2em] border border-forest text-forest bg-sage-light/40">
                                    Default
                                </span>
                            @endif
                        </div>

                        <div class="text-sm text-ink leading-relaxed break-words space-y-0.5">
                            @if(!empty($addr['company']))
                                <p class="text-muted text-xs">{{ $addr['company'] }}</p>
                            @endif
                            <p>{{ $addr['address_line1'] }}</p>
                            @if(!empty($addr['address_line2']))
                                <p>{{ $addr['address_line2'] }}</p>
                            @endif
                            <p class="lining-nums">{{ $addr['city'] }}, {{ $addr['state'] }} {{ $addr['postal_code'] }}</p>
                            <p>{{ $addr['country'] }}</p>
                            @if(!empty($addr['phone']))
                                <p class="text-xs text-muted pt-1 lining-nums">{{ $addr['phone'] }}</p>
                            @endif
                        </div>
                    </div>

                    <!-- Actions: Edit and Remove (UI only) -->
                    <div class="pt-4 border-t border-[#ECEEE6] flex items-center gap-4 text-[13px]">
                        <button 
                            type="button" 
                            @click="editAddress({{ Js::from($addr) }})"
                            class="min-h-[44px] inline-flex items-center text-forest hover:text-ink font-medium underline underline-offset-4 cursor-pointer"
                            aria-label="Edit address for {{ $addr['first_name'] }} {{ $addr['last_name'] }} ({{ $addr['city'] }})"
                        >
                            Edit
                        </button>
                        <span class="text-[#D5DBCF]" aria-hidden="true">|</span>
                        <button 
                            type="button" 
                            @click="removeAddress('{{ $addr['id'] }}')"
                            class="min-h-[44px] inline-flex items-center text-muted hover:text-ink font-medium underline underline-offset-4 cursor-pointer"
                            aria-label="Remove address for {{ $addr['first_name'] }} {{ $addr['last_name'] }} ({{ $addr['city'] }})"
                        >
                            Remove
                        </button>
                    </div>
                </div>
            @empty
                <div class="col-span-full py-12 text-center border border-[#E3E6DD] bg-white p-8 text-muted text-sm">
                    <p>No addresses recorded yet. Add your primary shipping details above.</p>
                </div>
            @endforelse
        </div>
    </section>

    <!-- Inline Address Form -->
    <div 
        x-show="showForm" 
        x-cloak 
        x-transition:enter="transition ease-out duration-200"
        x-transition:enter-start="opacity-0 -translate-y-2"
        x-transition:enter-end="opacity-100 translate-y-0"
        x-transition:leave="transition ease-in duration-150"
        x-transition:leave-start="opacity-100 translate-y-0"
        x-transition:leave-end="opacity-0 -translate-y-2"
        class="border border-[#E3E6DD] bg-white p-6 sm:p-8 space-y-6"
    >
        <div class="flex items-baseline justify-between border-b border-[#ECEEE6] pb-4">
            <div>
                <h2 class="font-serif text-xl sm:text-2xl text-ink font-normal">
                    Address Details
                </h2>
                <p class="text-xs text-muted mt-0.5">
                    Enter your shipping and delivery destination.
                </p>
            </div>
            <button 
                type="button" 
                @click="showForm = false"
                class="min-h-[44px] min-w-[44px] -mr-2 flex items-center justify-center text-muted hover:text-ink cursor-pointer"
                aria-label="Close address form"
            >
                ✕
            </button>
        </div>

        <form @submit.prevent="submit()" novalidate class="space-y-5">
            <!-- First Name & Last Name -->
            <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
                <div>
                    <label for="field-first_name" class="block text-xs uppercase tracking-[0.2em] text-muted font-medium mb-1.5">
                        First Name <span class="text-forest" aria-hidden="true">*</span>
                    </label>
                    <input 
                        type="text" 
                        id="field-first_name" 
                        x-model="form.first_name" 
                        @input="clearError('first_name')"
                        autocomplete="given-name" 
                        placeholder="First name"
                        class="input-field" 
                        :class="errors.first_name ? 'input-error' : ''"
                        :aria-invalid="errors.first_name ? 'true' : 'false'"
                        :aria-describedby="errors.first_name ? 'err-first_name' : null"
                    />
                    <p id="err-first_name" x-show="errors.first_name" x-text="errors.first_name" class="mt-1 text-[13px] text-[#B3453A] font-medium" role="alert" x-cloak></p>
                </div>

                <div>
                    <label for="field-last_name" class="block text-xs uppercase tracking-[0.2em] text-muted font-medium mb-1.5">
                        Last Name <span class="text-forest" aria-hidden="true">*</span>
                    </label>
                    <input 
                        type="text" 
                        id="field-last_name" 
                        x-model="form.last_name" 
                        @input="clearError('last_name')"
                        autocomplete="family-name" 
                        placeholder="Last name"
                        class="input-field" 
                        :class="errors.last_name ? 'input-error' : ''"
                        :aria-invalid="errors.last_name ? 'true' : 'false'"
                        :aria-describedby="errors.last_name ? 'err-last_name' : null"
                    />
                    <p id="err-last_name" x-show="errors.last_name" x-text="errors.last_name" class="mt-1 text-[13px] text-[#B3453A] font-medium" role="alert" x-cloak></p>
                </div>
            </div>

            <!-- Country Selection -->
            <div>
                <label for="field-country" class="block text-xs uppercase tracking-[0.2em] text-muted font-medium mb-1.5">
                    Country / Region <span class="text-forest" aria-hidden="true">*</span>
                </label>
                <div class="relative">
                    <select 
                        id="field-country" 
                        x-model="form.country" 
                        @change="clearError('country')"
                        autocomplete="country-name" 
                        class="input-field pr-10 appearance-none cursor-pointer bg-white" 
                        :class="errors.country ? 'input-error' : ''"
                        :aria-invalid="errors.country ? 'true' : 'false'"
                        :aria-describedby="errors.country ? 'err-country' : null"
                    >
                        <option value="United States">United States</option>
                        <option value="Canada">Canada</option>
                        <option value="United Kingdom">United Kingdom</option>
                        <option value="Australia">Australia</option>
                        <option value="France">France</option>
                        <option value="Germany">Germany</option>
                    </select>
                    <div class="pointer-events-none absolute inset-y-0 right-0 flex items-center pr-4 text-forest">
                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24" aria-hidden="true">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7" />
                        </svg>
                    </div>
                </div>
                <p id="err-country" x-show="errors.country" x-text="errors.country" class="mt-1 text-[13px] text-[#B3453A] font-medium" role="alert" x-cloak></p>
            </div>

            <!-- Address Line 1 -->
            <div>
                <label for="field-address_line1" class="block text-xs uppercase tracking-[0.2em] text-muted font-medium mb-1.5">
                    Street Address <span class="text-forest" aria-hidden="true">*</span>
                </label>
                <input 
                    type="text" 
                    id="field-address_line1" 
                    x-model="form.address_line1" 
                    @input="clearError('address_line1')"
                    autocomplete="address-line1" 
                    placeholder="Street address"
                    class="input-field" 
                    :class="errors.address_line1 ? 'input-error' : ''"
                    :aria-invalid="errors.address_line1 ? 'true' : 'false'"
                    :aria-describedby="errors.address_line1 ? 'err-address_line1' : null"
                />
                <p id="err-address_line1" x-show="errors.address_line1" x-text="errors.address_line1" class="mt-1 text-[13px] text-[#B3453A] font-medium" role="alert" x-cloak></p>
            </div>

            <!-- Address Line 2 (Optional) -->
            <div>
                <label for="field-address_line2" class="block text-xs uppercase tracking-[0.2em] text-muted font-medium mb-1.5">
                    Apartment, Suite, Unit, etc. <span class="text-muted font-normal lowercase">(optional)</span>
                </label>
                <input 
                    type="text" 
                    id="field-address_line2" 
                    x-model="form.address_line2" 
                    autocomplete="address-line2" 
                    placeholder="Apartment, suite, unit (optional)"
                    class="input-field" 
                />
            </div>

            <!-- City, State, Postal Code -->
            <div class="grid grid-cols-1 sm:grid-cols-3 gap-4">
                <div>
                    <label for="field-city" class="block text-xs uppercase tracking-[0.2em] text-muted font-medium mb-1.5">
                        City <span class="text-forest" aria-hidden="true">*</span>
                    </label>
                    <input 
                        type="text" 
                        id="field-city" 
                        x-model="form.city" 
                        @input="clearError('city')"
                        autocomplete="address-level2" 
                        placeholder="City"
                        class="input-field" 
                        :class="errors.city ? 'input-error' : ''"
                        :aria-invalid="errors.city ? 'true' : 'false'"
                        :aria-describedby="errors.city ? 'err-city' : null"
                    />
                    <p id="err-city" x-show="errors.city" x-text="errors.city" class="mt-1 text-[13px] text-[#B3453A] font-medium" role="alert" x-cloak></p>
                </div>

                <div>
                    <label for="field-state" class="block text-xs uppercase tracking-[0.2em] text-muted font-medium mb-1.5">
                        State / Province <span class="text-forest" aria-hidden="true">*</span>
                    </label>
                    <input 
                        type="text" 
                        id="field-state" 
                        x-model="form.state" 
                        @input="clearError('state')"
                        autocomplete="address-level1" 
                        placeholder="State / Province"
                        class="input-field" 
                        :class="errors.state ? 'input-error' : ''"
                        :aria-invalid="errors.state ? 'true' : 'false'"
                        :aria-describedby="errors.state ? 'err-state' : null"
                    />
                    <p id="err-state" x-show="errors.state" x-text="errors.state" class="mt-1 text-[13px] text-[#B3453A] font-medium" role="alert" x-cloak></p>
                </div>

                <div>
                    <label for="field-postal_code" class="block text-xs uppercase tracking-[0.2em] text-muted font-medium mb-1.5">
                        Postal Code <span class="text-forest" aria-hidden="true">*</span>
                    </label>
                    <input 
                        type="text" 
                        id="field-postal_code" 
                        x-model="form.postal_code" 
                        @input="clearError('postal_code')"
                        autocomplete="postal-code" 
                        placeholder="Postal code"
                        class="input-field" 
                        :class="errors.postal_code ? 'input-error' : ''"
                        :aria-invalid="errors.postal_code ? 'true' : 'false'"
                        :aria-describedby="errors.postal_code ? 'err-postal_code' : null"
                    />
                    <p id="err-postal_code" x-show="errors.postal_code" x-text="errors.postal_code" class="mt-1 text-[13px] text-[#B3453A] font-medium" role="alert" x-cloak></p>
                </div>
            </div>

            <!-- Phone (Optional) -->
            <div>
                <label for="field-phone" class="block text-xs uppercase tracking-[0.2em] text-muted font-medium mb-1.5">
                    Phone Number <span class="text-muted font-normal lowercase">(optional)</span>
                </label>
                <input 
                    type="tel" 
                    id="field-phone" 
                    x-model="form.phone" 
                    autocomplete="tel" 
                    placeholder="Phone number"
                    class="input-field" 
                />
            </div>

            <!-- Set as Default Checkbox -->
            <div class="pt-2 flex items-center gap-3">
                <input 
                    type="checkbox" 
                    id="field-is_default" 
                    x-model="form.is_default"
                    class="w-4 h-4 rounded-none accent-[#304C3D] border-[#D5DBCF] cursor-pointer"
                />
                <label for="field-is_default" class="text-sm text-ink select-none cursor-pointer">
                    Set as default delivery address
                </label>
            </div>

            <!-- Form Actions -->
            <div class="pt-4 border-t border-[#ECEEE6] flex flex-col sm:flex-row items-center gap-3">
                <button 
                    type="submit" 
                    class="btn-primary w-full sm:w-auto min-h-[44px] cursor-pointer"
                >
                    Save Address
                </button>
                <button 
                    type="button" 
                    @click="showForm = false"
                    class="btn-secondary w-full sm:w-auto min-h-[44px] cursor-pointer"
                >
                    Cancel
                </button>
            </div>
        </form>
    </div>
</div>
@endsection
