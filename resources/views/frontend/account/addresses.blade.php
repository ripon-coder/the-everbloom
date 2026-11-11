@extends('layouts.account')

@section('title', 'Saved Addresses — ' . config('demo.site.name'))

@section('breadcrumbs')
    <x-storefront.breadcrumbs :items="[
        ['label' => 'My Account', 'url' => route('account')],
        ['label' => 'Addresses', 'url' => ''],
    ]" />
@endsection

@section('page_title', 'Addresses')

@section('header_actions')
    <div class="hidden lg:block">
        <button 
            type="button" 
            @click="resetForm(); showForm = true; $nextTick(() => document.getElementById('field-first_name')?.focus())"
            class="btn-primary min-h-[44px] cursor-pointer"
        >
            Add new address
        </button>
    </div>
@endsection

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
        validate() {
            this.errors = {};
            if (!this.form.first_name.trim()) this.errors.first_name = 'First name is required';
            if (!this.form.last_name.trim()) this.errors.last_name = 'Last name is required';
            if (!this.form.country.trim()) this.errors.country = 'Country is required';
            if (!this.form.address_line1.trim()) this.errors.address_line1 = 'Street address is required';
            if (!this.form.city.trim()) this.errors.city = 'City is required';
            if (!this.form.state.trim()) this.errors.state = 'State or province is required';
            if (!this.form.postal_code.trim()) this.errors.postal_code = 'Postal code is required';

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
                if (el) el.focus();
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
        x-transition:enter="transition ease-out duration-300"
        x-transition:enter-start="opacity-0 -translate-y-2"
        x-transition:enter-end="opacity-100 translate-y-0"
        x-transition:leave="transition ease-in duration-200"
        x-transition:leave-start="opacity-100 translate-y-0"
        x-transition:leave-end="opacity-0 -translate-y-2"
        class="p-4 bg-forest text-ivory text-xs uppercase tracking-wider flex items-center justify-between"
        role="status"
    >
        <span x-text="demoFeedback"></span>
        <button type="button" @click="demoFeedback = ''" class="p-1 hover:text-sage text-ivory min-h-[44px] min-w-[44px] flex items-center justify-center" aria-label="Close message">✕</button>
    </div>

    <!-- Mobile "Add New Address" Trigger (visible below lg) -->
    <div class="lg:hidden">
        <button 
            type="button" 
            @click="resetForm(); showForm = true; $nextTick(() => document.getElementById('field-first_name')?.focus())"
            class="btn-primary w-full min-h-[44px] flex items-center justify-center cursor-pointer"
        >
            + Add new address
        </button>
    </div>

    <!-- Address Cards / List -->
    <section aria-labelledby="saved-addresses-heading">
        <h2 id="saved-addresses-heading" class="sr-only">Saved Botanical Delivery Addresses</h2>

        <!-- Desktop 2-column grid, Mobile single column (thin soft border #E3E6DD, whitespace, not boxed cards) -->
        <div class="grid grid-cols-1 md:grid-cols-2 gap-6 lg:gap-8">
            @forelse($addresses as $addr)
                <div class="border border-[#E3E6DD] bg-white p-6 sm:p-7 flex flex-col justify-between space-y-4">
                    <div class="space-y-3">
                        <div class="flex items-center justify-between gap-2">
                            <h3 class="font-serif text-lg text-ink font-medium">
                                {{ $addr['first_name'] }} {{ $addr['last_name'] }}
                            </h3>
                            @if(!empty($addr['is_default']))
                                <span class="inline-flex items-center px-2 py-0.5 text-[10px] font-medium uppercase tracking-wider border border-forest text-forest">
                                    Default
                                </span>
                            @endif
                        </div>

                        <div class="text-sm text-ink/85 leading-relaxed break-words space-y-0.5">
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
                    <div class="pt-4 border-t border-[#ECEEE6] flex items-center gap-4 text-xs uppercase tracking-wider">
                        <button 
                            type="button" 
                            @click="editAddress({{ Js::from($addr) }})"
                            class="min-h-[44px] inline-flex items-center text-forest hover:text-forest-700 font-medium underline underline-offset-4 cursor-pointer"
                        >
                            Edit
                        </button>
                        <span class="text-[#D5DBCF]" aria-hidden="true">|</span>
                        <button 
                            type="button" 
                            @click="removeAddress('{{ $addr['id'] }}')"
                            class="min-h-[44px] inline-flex items-center text-muted hover:text-ink font-medium underline underline-offset-4 cursor-pointer"
                        >
                            Remove
                        </button>
                    </div>
                </div>
            @empty
                <div class="col-span-full py-8 text-center text-muted text-sm">
                    <p>No addresses recorded yet. Add your primary shipping details below.</p>
                </div>
            @endforelse
        </div>
    </section>

    <!-- Inline Address Form (Alpine showForm, focus trap, labeled with autocomplete) -->
    <div 
        x-show="showForm" 
        x-cloak 
        x-transition:enter="transition ease-out duration-300"
        x-transition:enter-start="opacity-0 -translate-y-4"
        x-transition:enter-end="opacity-100 translate-y-0"
        x-transition:leave="transition ease-in duration-200"
        x-transition:leave-start="opacity-100 translate-y-0"
        x-transition:leave-end="opacity-0 -translate-y-4"
        class="border border-[#E3E6DD] bg-white p-6 sm:p-8 space-y-6 pt-6"
    >
        <div class="flex items-baseline justify-between border-b border-[#ECEEE6] pb-4">
            <div>
                <h2 class="font-serif text-xl sm:text-2xl text-ink font-normal">
                    Address Details
                </h2>
                <p class="text-xs text-muted mt-0.5">
                    Enter your botanical shipping and delivery destination.
                </p>
            </div>
            <button 
                type="button" 
                @click="showForm = false"
                class="min-h-[44px] min-w-[44px] flex items-center justify-center text-muted hover:text-ink"
                aria-label="Close address form"
            >
                ✕
            </button>
        </div>

        <form @submit.prevent="submit()" novalidate class="space-y-5">
            <!-- First Name & Last Name -->
            <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
                <div>
                    <label for="field-first_name" class="block text-xs uppercase tracking-[0.16em] text-muted font-medium mb-1.5">
                        First Name <span class="text-forest" aria-hidden="true">*</span>
                    </label>
                    <input 
                        type="text" 
                        id="field-first_name" 
                        x-model="form.first_name" 
                        autocomplete="given-name" 
                        class="input-field" 
                        :class="errors.first_name ? 'input-error' : ''"
                        :aria-invalid="errors.first_name ? 'true' : 'false'"
                        aria-describedby="err-first_name"
                    />
                    <p id="err-first_name" x-show="errors.first_name" x-text="errors.first_name" class="mt-1 text-xs text-[#B3453A]" x-cloak></p>
                </div>

                <div>
                    <label for="field-last_name" class="block text-xs uppercase tracking-[0.16em] text-muted font-medium mb-1.5">
                        Last Name <span class="text-forest" aria-hidden="true">*</span>
                    </label>
                    <input 
                        type="text" 
                        id="field-last_name" 
                        x-model="form.last_name" 
                        autocomplete="family-name" 
                        class="input-field" 
                        :class="errors.last_name ? 'input-error' : ''"
                        :aria-invalid="errors.last_name ? 'true' : 'false'"
                        aria-describedby="err-last_name"
                    />
                    <p id="err-last_name" x-show="errors.last_name" x-text="errors.last_name" class="mt-1 text-xs text-[#B3453A]" x-cloak></p>
                </div>
            </div>

            <!-- Country Selection -->
            <div>
                <label for="field-country" class="block text-xs uppercase tracking-[0.16em] text-muted font-medium mb-1.5">
                    Country / Region <span class="text-forest" aria-hidden="true">*</span>
                </label>
                <select 
                    id="field-country" 
                    x-model="form.country" 
                    autocomplete="country-name" 
                    class="input-field bg-white" 
                    :class="errors.country ? 'input-error' : ''"
                    :aria-invalid="errors.country ? 'true' : 'false'"
                    aria-describedby="err-country"
                >
                    <option value="United States">United States</option>
                    <option value="Canada">Canada</option>
                    <option value="United Kingdom">United Kingdom</option>
                    <option value="Australia">Australia</option>
                    <option value="France">France</option>
                    <option value="Germany">Germany</option>
                </select>
                <p id="err-country" x-show="errors.country" x-text="errors.country" class="mt-1 text-xs text-[#B3453A]" x-cloak></p>
            </div>

            <!-- Address Line 1 -->
            <div>
                <label for="field-address_line1" class="block text-xs uppercase tracking-[0.16em] text-muted font-medium mb-1.5">
                    Street Address <span class="text-forest" aria-hidden="true">*</span>
                </label>
                <input 
                    type="text" 
                    id="field-address_line1" 
                    x-model="form.address_line1" 
                    autocomplete="address-line1" 
                    placeholder="House number and street name"
                    class="input-field" 
                    :class="errors.address_line1 ? 'input-error' : ''"
                    :aria-invalid="errors.address_line1 ? 'true' : 'false'"
                    aria-describedby="err-address_line1"
                />
                <p id="err-address_line1" x-show="errors.address_line1" x-text="errors.address_line1" class="mt-1 text-xs text-[#B3453A]" x-cloak></p>
            </div>

            <!-- Address Line 2 (Optional) -->
            <div>
                <label for="field-address_line2" class="block text-xs uppercase tracking-[0.16em] text-muted font-medium mb-1.5">
                    Apartment, Suite, Unit, etc. <span class="text-muted/60 font-normal lowercase">(optional)</span>
                </label>
                <input 
                    type="text" 
                    id="field-address_line2" 
                    x-model="form.address_line2" 
                    autocomplete="address-line2" 
                    class="input-field" 
                />
            </div>

            <!-- City, State, Postal Code -->
            <div class="grid grid-cols-1 sm:grid-cols-3 gap-4">
                <div>
                    <label for="field-city" class="block text-xs uppercase tracking-[0.16em] text-muted font-medium mb-1.5">
                        City <span class="text-forest" aria-hidden="true">*</span>
                    </label>
                    <input 
                        type="text" 
                        id="field-city" 
                        x-model="form.city" 
                        autocomplete="address-level2" 
                        class="input-field" 
                        :class="errors.city ? 'input-error' : ''"
                        :aria-invalid="errors.city ? 'true' : 'false'"
                        aria-describedby="err-city"
                    />
                    <p id="err-city" x-show="errors.city" x-text="errors.city" class="mt-1 text-xs text-[#B3453A]" x-cloak></p>
                </div>

                <div>
                    <label for="field-state" class="block text-xs uppercase tracking-[0.16em] text-muted font-medium mb-1.5">
                        State / Province <span class="text-forest" aria-hidden="true">*</span>
                    </label>
                    <input 
                        type="text" 
                        id="field-state" 
                        x-model="form.state" 
                        autocomplete="address-level1" 
                        class="input-field" 
                        :class="errors.state ? 'input-error' : ''"
                        :aria-invalid="errors.state ? 'true' : 'false'"
                        aria-describedby="err-state"
                    />
                    <p id="err-state" x-show="errors.state" x-text="errors.state" class="mt-1 text-xs text-[#B3453A]" x-cloak></p>
                </div>

                <div>
                    <label for="field-postal_code" class="block text-xs uppercase tracking-[0.16em] text-muted font-medium mb-1.5">
                        Postal Code <span class="text-forest" aria-hidden="true">*</span>
                    </label>
                    <input 
                        type="text" 
                        id="field-postal_code" 
                        x-model="form.postal_code" 
                        autocomplete="postal-code" 
                        class="input-field" 
                        :class="errors.postal_code ? 'input-error' : ''"
                        :aria-invalid="errors.postal_code ? 'true' : 'false'"
                        aria-describedby="err-postal_code"
                    />
                    <p id="err-postal_code" x-show="errors.postal_code" x-text="errors.postal_code" class="mt-1 text-xs text-[#B3453A]" x-cloak></p>
                </div>
            </div>

            <!-- Phone (Optional) -->
            <div>
                <label for="field-phone" class="block text-xs uppercase tracking-[0.16em] text-muted font-medium mb-1.5">
                    Phone Number <span class="text-muted/60 font-normal lowercase">(optional, for courier dispatch)</span>
                </label>
                <input 
                    type="tel" 
                    id="field-phone" 
                    x-model="form.phone" 
                    autocomplete="tel" 
                    placeholder="+1 (555) 000-0000"
                    class="input-field" 
                />
            </div>

            <!-- Set as Default Checkbox -->
            <div class="pt-2 flex items-center gap-3">
                <input 
                    type="checkbox" 
                    id="field-is_default" 
                    x-model="form.is_default"
                    class="w-4 h-4 rounded-none accent-[#304C3D] text-[#304C3D] border-[#D5DBCF] focus:ring-forest focus:ring-offset-0"
                />
                <label for="field-is_default" class="text-xs text-ink select-none cursor-pointer">
                    Set as default delivery address
                </label>
            </div>

            <!-- Form Actions -->
            <div class="pt-4 border-t border-[#ECEEE6] flex flex-col sm:flex-row items-center gap-3">
                <button 
                    type="submit" 
                    class="btn-primary w-full sm:w-auto min-h-[44px] cursor-pointer"
                >
                    Save address
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
