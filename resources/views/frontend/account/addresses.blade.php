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
@php
    $initialAddresses = collect($addresses ?? [])
        ->sortByDesc(fn($a) => !empty($a['is_default']))
        ->values()
        ->all();
@endphp
<div 
    x-data="{
        addresses: {{ Js::from($initialAddresses) }},
        districts: @js($districts ?? []),
        thanas: [],
        areas: [],
        showForm: false,
        editingId: null,
        editingIsDefault: false,
        editingContextLabel: '',
        lastTriggerEl: null,
        confirmingRemoveId: null,
        demoFeedback: '',
        form: {
            first_name: '',
            last_name: '',
            company: '',
            country: 'Bangladesh',
            district_id: '',
            thana_id: '',
            area_id: '',
            address_line1: '',
            address_line2: '',
            postal_code: '',
            phone: '',
            is_default: false,
        },
        errors: {},
        sortedAddresses() {
            return [...this.addresses].sort((a, b) => (b.is_default ? 1 : 0) - (a.is_default ? 1 : 0));
        },
        async loadThanas(districtId) {
            if (!districtId) { this.thanas = []; return; }
            try {
                const res = await fetch(`/locations/districts/${districtId}/thanas`);
                const data = await res.json();
                this.thanas = data.thanas || [];
            } catch (e) { this.thanas = []; }
        },
        async loadAreas(thanaId) {
            if (!thanaId) { this.areas = []; return; }
            try {
                const res = await fetch(`/locations/thanas/${thanaId}/areas`);
                const data = await res.json();
                this.areas = data.areas || [];
            } catch (e) { this.areas = []; }
        },
        async onDistrictChange() {
            this.clearError('district_id');
            this.form.thana_id = '';
            this.form.area_id = '';
            this.areas = [];
            await this.loadThanas(this.form.district_id);
        },
        async onThanaChange() {
            this.clearError('thana_id');
            this.form.area_id = '';
            await this.loadAreas(this.form.thana_id);
        },
        onAreaChange() {
            this.clearError('area_id');
            const selected = this.areas.find(a => String(a.id) === String(this.form.area_id));
            if (selected && selected.postal_code) {
                this.form.postal_code = selected.postal_code;
            }
        },
        resetForm() {
            this.editingId = null;
            this.editingIsDefault = false;
            this.editingContextLabel = '';
            this.thanas = [];
            this.areas = [];
            this.form = {
                first_name: '',
                last_name: '',
                company: '',
                country: 'Bangladesh',
                district_id: '',
                thana_id: '',
                area_id: '',
                address_line1: '',
                address_line2: '',
                postal_code: '',
                phone: '',
                is_default: false,
            };
            this.errors = {};
        },
        scrollAndFocusForm() {
            this.$nextTick(() => {
                const panel = this.$refs.editPanel;
                if (panel && typeof panel.scrollIntoView === 'function') {
                    const prefersReduced = window.matchMedia && window.matchMedia('(prefers-reduced-motion: reduce)').matches;
                    panel.scrollIntoView({ behavior: prefersReduced ? 'auto' : 'smooth', block: 'start' });
                }
                const firstField = document.getElementById('field-first_name');
                if (firstField) {
                    firstField.focus({ preventScroll: true });
                }
            });
        },
        openAddForm(triggerEl = null) {
            this.lastTriggerEl = triggerEl || this.$refs.addAddressBtn;
            this.resetForm();
            this.showForm = true;
            this.scrollAndFocusForm();
        },
        closeForm() {
            const trigger = this.lastTriggerEl;
            this.showForm = false;
            this.editingId = null;
            this.editingIsDefault = false;
            this.editingContextLabel = '';
            this.errors = {};
            this.$nextTick(() => {
                if (trigger && typeof trigger.focus === 'function' && document.contains(trigger)) {
                    trigger.focus();
                } else {
                    this.$refs.addAddressBtn?.focus();
                }
            });
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
            if (!this.form.district_id) this.errors.district_id = 'Please select a district.';
            if (!this.form.thana_id) this.errors.thana_id = 'Please select a thana.';
            if (!this.form.area_id) this.errors.area_id = 'Please select an area.';
            if (!this.form.address_line1.trim()) this.errors.address_line1 = 'Please enter a house, road, or block address.';

            const phone = this.form.phone.trim();
            if (phone) {
                const digits = phone.replace(/\D/g, '');
                if (!/^[+\d\s().-]{7,20}$/.test(phone) || digits.length < 7 || digits.length > 15) {
                    this.errors.phone = 'Please enter a valid phone number.';
                }
            }

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
        async submit() {
            if (!this.validate()) return;
            const csrf = document.querySelector('meta[name="csrf-token"]')?.getAttribute('content') || '';
            const isEdit = Boolean(this.editingId);
            const url = isEdit
                ? @js(url('/account/addresses')) + '/' + this.editingId
                : @js(route('account.addresses.store'));

            try {
                const res = await fetch(url, {
                    method: isEdit ? 'PUT' : 'POST',
                    headers: {
                        'Content-Type': 'application/json',
                        'Accept': 'application/json',
                        'X-CSRF-TOKEN': csrf,
                    },
                    body: JSON.stringify(this.form),
                });
                const data = await res.json();
                if (!res.ok) {
                    if (data.errors) {
                        Object.entries(data.errors).forEach(([k, v]) => {
                            this.errors[k] = Array.isArray(v) ? v[0] : v;
                        });
                    }
                    return;
                }
                if (data.address) {
                    if (data.address.is_default) {
                        this.addresses = this.addresses.map(a => ({ ...a, is_default: false }));
                    }
                    if (isEdit) {
                        this.addresses = this.addresses.map(a => String(a.id) === String(this.editingId) ? data.address : a);
                    } else {
                        this.addresses = [data.address, ...this.addresses];
                    }
                }
                this.demoFeedback = data.message || 'Address saved.';
                this.closeForm();
                setTimeout(() => { this.demoFeedback = ''; }, 6000);
            } catch (e) {
                this.demoFeedback = 'Unable to save address. Please try again.';
            }
        },
        async editAddress(addr, triggerEl = null) {
            this.lastTriggerEl = triggerEl;
            this.editingId = addr.id;
            this.editingIsDefault = Boolean(addr.is_default);
            const labelName = (addr.company && addr.company.trim()) ? addr.company.trim() : (addr.first_name + ' ' + addr.last_name).trim();
            this.editingContextLabel = addr.district_name ? `${labelName}, ${addr.district_name}` : labelName;
            this.form = {
                first_name: addr.first_name || '',
                last_name: addr.last_name || '',
                company: addr.company || '',
                country: 'Bangladesh',
                district_id: addr.district_id ? String(addr.district_id) : '',
                thana_id: addr.thana_id ? String(addr.thana_id) : '',
                area_id: addr.area_id ? String(addr.area_id) : '',
                address_line1: addr.address_line1 || '',
                address_line2: addr.address_line2 || '',
                postal_code: addr.postal_code || '',
                phone: addr.phone || '',
                is_default: Boolean(addr.is_default),
            };
            this.errors = {};
            this.showForm = true;
            if (this.form.district_id) {
                await this.loadThanas(this.form.district_id);
                if (this.form.thana_id) {
                    await this.loadAreas(this.form.thana_id);
                }
            }
            this.scrollAndFocusForm();
        },
        async setAsDefault(id) {
            const csrf = document.querySelector('meta[name="csrf-token"]')?.getAttribute('content') || '';
            try {
                const res = await fetch(@js(url('/account/addresses')) + '/' + id + '/default', {
                    method: 'PATCH',
                    headers: {
                        'Accept': 'application/json',
                        'X-CSRF-TOKEN': csrf,
                    },
                });
                const data = await res.json();
                if (res.ok) {
                    this.addresses = this.addresses.map(a => ({
                        ...a,
                        is_default: String(a.id) === String(id),
                    }));
                    this.demoFeedback = data.message || 'Default address updated.';
                    setTimeout(() => { this.demoFeedback = ''; }, 5000);
                }
            } catch (e) {}
        },
        startRemove(id) {
            this.confirmingRemoveId = id;
            this.$nextTick(() => {
                document.getElementById('confirm-remove-' + id)?.focus();
            });
        },
        cancelRemove() {
            this.confirmingRemoveId = null;
        },
        async confirmRemove(id) {
            const csrf = document.querySelector('meta[name="csrf-token"]')?.getAttribute('content') || '';
            try {
                const res = await fetch(@js(url('/account/addresses')) + '/' + id, {
                    method: 'DELETE',
                    headers: {
                        'Accept': 'application/json',
                        'X-CSRF-TOKEN': csrf,
                    },
                });
                const data = await res.json();
                if (res.ok) {
                    this.addresses = this.addresses.filter(a => String(a.id) !== String(id));
                    this.confirmingRemoveId = null;
                    this.demoFeedback = data.message || 'Address removed.';
                    setTimeout(() => { this.demoFeedback = ''; }, 5000);
                }
            } catch (e) {}
        }
    }"
    @keydown.escape.window="if (confirmingRemoveId !== null) { cancelRemove(); } else if (showForm) { closeForm(); }"
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

    <!-- Top Action Bar (vertically centered with first sidebar item) -->
    <div class="min-h-[44px] flex flex-col sm:flex-row sm:items-center sm:justify-between gap-4 pb-4 border-b border-[#ECEEE6]">
        <p class="text-sm text-muted">
            Manage your saved shipping destinations for faster checkout.
        </p>
        <button 
            type="button" 
            x-ref="addAddressBtn"
            @click="openAddForm($event.currentTarget)"
            class="btn-primary w-full sm:w-auto shrink-0 cursor-pointer"
        >
            Add new address
        </button>
    </div>

    <!-- Inline Add / Edit Address Panel (below list header) -->
    <div 
        x-ref="editPanel"
        x-show="showForm" 
        x-collapse
        x-cloak 
        class="border border-[#E3E6DD] bg-white p-6 sm:p-8 space-y-6"
    >
        <div class="flex items-start justify-between gap-4 border-b border-[#ECEEE6] pb-4">
            <div>
                <h2 class="font-serif text-xl sm:text-2xl text-ink font-normal" x-text="editingId ? 'Edit address' : 'Add new address'">
                    Address Details
                </h2>
                <p 
                    x-show="editingId !== null" 
                    x-cloak 
                    class="text-xs text-muted mt-1" 
                    x-text="'Editing: ' + editingContextLabel"
                ></p>
                <p x-show="editingId === null" class="text-xs text-muted mt-1">
                    Enter your shipping and delivery destination.
                </p>
            </div>
            <button 
                type="button" 
                @click="closeForm()"
                class="w-11 h-11 min-w-[44px] min-h-[44px] -mr-2 -mt-1 flex items-center justify-center text-muted hover:text-ink cursor-pointer focus-visible:outline-none focus-visible:ring-2 focus-visible:ring-[#304C3D] focus-visible:ring-offset-2"
                aria-label="Close edit form"
            >
                ✕
            </button>
        </div>

        <form @submit.prevent="submit()" novalidate class="space-y-5">
            <!-- First Name & Last Name (2-column row) -->
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
                        :class="errors.first_name ? 'input-error !border-[#B3453A]' : ''"
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
                        :class="errors.last_name ? 'input-error !border-[#B3453A]' : ''"
                        :aria-invalid="errors.last_name ? 'true' : 'false'"
                        :aria-describedby="errors.last_name ? 'err-last_name' : null"
                    />
                    <p id="err-last_name" x-show="errors.last_name" x-text="errors.last_name" class="mt-1 text-[13px] text-[#B3453A] font-medium" role="alert" x-cloak></p>
                </div>
            </div>

            <!-- Company (Optional) -->
            <div>
                <label for="field-company" class="block text-xs uppercase tracking-[0.2em] text-muted font-medium mb-1.5">
                    Company <span class="text-muted font-normal lowercase">(optional)</span>
                </label>
                <input 
                    type="text" 
                    id="field-company" 
                    x-model="form.company" 
                    autocomplete="organization" 
                    placeholder="Company name (optional)"
                    class="input-field" 
                />
            </div>

            <!-- Country (Fixed: Bangladesh) -->
            <div>
                <label for="field-country" class="block text-xs uppercase tracking-[0.2em] text-muted font-medium mb-1.5">
                    Country / Region <span class="text-forest" aria-hidden="true">*</span>
                </label>
                <input 
                    type="text" 
                    id="field-country" 
                    x-model="form.country" 
                    readonly 
                    class="input-field bg-ivory/60 cursor-not-allowed"
                />
            </div>

            <!-- District, Thana, Area (Bangladesh Cascading Selects) -->
            <div class="grid grid-cols-1 sm:grid-cols-3 gap-4">
                <div>
                    <label for="field-district_id" class="block text-xs uppercase tracking-[0.2em] text-muted font-medium mb-1.5">
                        District <span class="text-forest" aria-hidden="true">*</span>
                    </label>
                    <div class="relative">
                        <select 
                            id="field-district_id" 
                            x-model="form.district_id" 
                            @change="onDistrictChange()"
                            class="input-field pr-10 appearance-none cursor-pointer bg-white" 
                            :class="errors.district_id ? 'input-error !border-[#B3453A]' : ''"
                        >
                            <option value="">Select District</option>
                            <template x-for="d in districts" :key="d.id">
                                <option :value="String(d.id)" x-text="d.name"></option>
                            </template>
                        </select>
                        <div class="pointer-events-none absolute inset-y-0 right-0 flex items-center pr-4 text-forest">
                            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24" aria-hidden="true">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7" />
                            </svg>
                        </div>
                    </div>
                    <p id="err-district_id" x-show="errors.district_id" x-text="errors.district_id" class="mt-1 text-[13px] text-[#B3453A] font-medium" role="alert" x-cloak></p>
                </div>

                <div>
                    <label for="field-thana_id" class="block text-xs uppercase tracking-[0.2em] text-muted font-medium mb-1.5">
                        Thana / Upazila <span class="text-forest" aria-hidden="true">*</span>
                    </label>
                    <div class="relative">
                        <select 
                            id="field-thana_id" 
                            x-model="form.thana_id" 
                            @change="onThanaChange()"
                            :disabled="!form.district_id"
                            class="input-field pr-10 appearance-none cursor-pointer bg-white disabled:bg-ivory/60 disabled:cursor-not-allowed" 
                            :class="errors.thana_id ? 'input-error !border-[#B3453A]' : ''"
                        >
                            <option value="">Select Thana</option>
                            <template x-for="t in thanas" :key="t.id">
                                <option :value="String(t.id)" x-text="t.name"></option>
                            </template>
                        </select>
                        <div class="pointer-events-none absolute inset-y-0 right-0 flex items-center pr-4 text-forest">
                            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24" aria-hidden="true">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7" />
                            </svg>
                        </div>
                    </div>
                    <p id="err-thana_id" x-show="errors.thana_id" x-text="errors.thana_id" class="mt-1 text-[13px] text-[#B3453A] font-medium" role="alert" x-cloak></p>
                </div>

                <div>
                    <label for="field-area_id" class="block text-xs uppercase tracking-[0.2em] text-muted font-medium mb-1.5">
                        Area / Locality <span class="text-forest" aria-hidden="true">*</span>
                    </label>
                    <div class="relative">
                        <select 
                            id="field-area_id" 
                            x-model="form.area_id" 
                            @change="onAreaChange()"
                            :disabled="!form.thana_id"
                            class="input-field pr-10 appearance-none cursor-pointer bg-white disabled:bg-ivory/60 disabled:cursor-not-allowed" 
                            :class="errors.area_id ? 'input-error !border-[#B3453A]' : ''"
                        >
                            <option value="">Select Area</option>
                            <template x-for="a in areas" :key="a.id">
                                <option :value="String(a.id)" x-text="a.name"></option>
                            </template>
                        </select>
                        <div class="pointer-events-none absolute inset-y-0 right-0 flex items-center pr-4 text-forest">
                            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24" aria-hidden="true">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7" />
                            </svg>
                        </div>
                    </div>
                    <p id="err-area_id" x-show="errors.area_id" x-text="errors.area_id" class="mt-1 text-[13px] text-[#B3453A] font-medium" role="alert" x-cloak></p>
                </div>
            </div>

            <!-- Address Line 1 -->
            <div>
                <label for="field-address_line1" class="block text-xs uppercase tracking-[0.2em] text-muted font-medium mb-1.5">
                    House / Road / Block <span class="text-forest" aria-hidden="true">*</span>
                </label>
                <input 
                    type="text" 
                    id="field-address_line1" 
                    x-model="form.address_line1" 
                    @input="clearError('address_line1')"
                    autocomplete="address-line1" 
                    placeholder="House, road, block"
                    class="input-field" 
                    :class="errors.address_line1 ? 'input-error !border-[#B3453A]' : ''"
                    :aria-invalid="errors.address_line1 ? 'true' : 'false'"
                    :aria-describedby="errors.address_line1 ? 'err-address_line1' : null"
                />
                <p id="err-address_line1" x-show="errors.address_line1" x-text="errors.address_line1" class="mt-1 text-[13px] text-[#B3453A] font-medium" role="alert" x-cloak></p>
            </div>

            <!-- Address Line 2 & Postal Code -->
            <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
                <div>
                    <label for="field-address_line2" class="block text-xs uppercase tracking-[0.2em] text-muted font-medium mb-1.5">
                        Apartment, Suite, Floor <span class="text-muted font-normal lowercase">(optional)</span>
                    </label>
                    <input 
                        type="text" 
                        id="field-address_line2" 
                        x-model="form.address_line2" 
                        autocomplete="address-line2" 
                        placeholder="Apartment, suite, floor (optional)"
                        class="input-field" 
                    />
                </div>

                <div>
                    <label for="field-postal_code" class="block text-xs uppercase tracking-[0.2em] text-muted font-medium mb-1.5">
                        Postal Code <span class="text-muted font-normal lowercase">(optional)</span>
                    </label>
                    <input 
                        type="text" 
                        id="field-postal_code" 
                        x-model="form.postal_code" 
                        @input="clearError('postal_code')"
                        autocomplete="postal-code" 
                        placeholder="Postal code"
                        class="input-field" 
                    />
                </div>
            </div>

            <!-- Phone (Optional, max-w-sm) -->
            <div class="max-w-sm">
                <label for="field-phone" class="block text-xs uppercase tracking-[0.2em] text-muted font-medium mb-1.5">
                    Phone Number <span class="text-muted font-normal lowercase">(optional)</span>
                </label>
                <input 
                    type="tel" 
                    id="field-phone" 
                    x-model="form.phone" 
                    @input="clearError('phone')"
                    autocomplete="tel" 
                    placeholder="Phone number"
                    class="input-field" 
                    :class="errors.phone ? 'input-error !border-[#B3453A]' : ''"
                    :aria-invalid="errors.phone ? 'true' : 'false'"
                    :aria-describedby="errors.phone ? 'err-phone' : null"
                />
                <p id="err-phone" x-show="errors.phone" x-text="errors.phone" class="mt-1 text-[13px] text-[#B3453A] font-medium" role="alert" x-cloak></p>
            </div>

            <!-- Set as Default Checkbox -->
            <div class="pt-2 space-y-1.5">
                <div class="flex items-center gap-3">
                    <input 
                        type="checkbox" 
                        id="field-is_default" 
                        x-model="form.is_default"
                        :disabled="editingIsDefault"
                        :aria-disabled="editingIsDefault ? 'true' : 'false'"
                        class="w-4 h-4 rounded-none accent-[#304C3D] border-[#D5DBCF] disabled:opacity-60 disabled:cursor-not-allowed cursor-pointer"
                    />
                    <label 
                        for="field-is_default" 
                        class="text-sm text-ink select-none"
                        :class="editingIsDefault ? 'text-muted cursor-not-allowed' : 'cursor-pointer'"
                    >
                        Set as default
                    </label>
                </div>
                <p x-show="editingIsDefault" x-cloak class="text-xs text-muted pl-7">
                    This is your default address. Set another address as default to change it.
                </p>
            </div>

            <!-- Form Actions + Demo Note -->
            <div class="pt-4 border-t border-[#ECEEE6] flex flex-col sm:flex-row sm:items-center gap-3 sm:gap-4">
                <button 
                    type="submit" 
                    class="btn-primary w-full sm:w-auto min-h-[44px] cursor-pointer"
                >
                    Save Address
                </button>
                <button 
                    type="button" 
                    @click="closeForm()"
                    class="btn-secondary w-full sm:w-auto min-h-[44px] cursor-pointer"
                >
                    Cancel
                </button>
                <p class="text-[13px] text-muted sm:ml-auto">
                    Demo only. Changes are not saved.
                </p>
            </div>
        </form>
    </div>

    <!-- Address Cards / List -->
    <section aria-labelledby="saved-addresses-heading">
        <h2 id="saved-addresses-heading" class="sr-only">Saved Delivery Addresses</h2>

        <div x-show="addresses.length > 0" class="grid grid-cols-1 md:grid-cols-2 gap-6 lg:gap-8 items-stretch">
            <template x-for="addr in sortedAddresses()" :key="addr.id">
                <article 
                    class="border p-6 flex flex-col gap-6 h-full transition-colors"
                    :style="addr.is_default ? 'border-color: #304C3D; background-color: rgba(232, 238, 229, 0.5);' : ''"
                    :class="addr.is_default ? 'border-[#304C3D]' : 'border-[#E3E6DD] bg-white'"
                >
                    <div class="space-y-3">
                        <div class="flex items-center justify-between gap-2">
                            <h3 class="font-serif text-lg text-ink font-medium" x-text="addr.first_name + ' ' + addr.last_name"></h3>
                            <div class="flex items-center gap-2 shrink-0">
                                <span 
                                    x-show="editingId === addr.id" 
                                    x-cloak
                                    class="inline-flex items-center px-2 py-0.5 text-xs font-medium uppercase tracking-wider border border-[#D5DBCF] text-muted bg-white"
                                >
                                    Editing
                                </span>
                                <span 
                                    x-show="addr.is_default" 
                                    class="inline-flex items-center px-2.5 py-0.5 text-xs font-medium uppercase tracking-wider border border-[#304C3D] text-[#304C3D] bg-[#E8EEE5]"
                                >
                                    Default
                                </span>
                            </div>
                        </div>

                        <div class="text-sm text-ink leading-[1.5] break-words">
                            <p x-show="addr.company" class="text-sm text-muted" x-text="addr.company"></p>
                            <p x-text="addr.address_line1"></p>
                            <p x-show="addr.address_line2" x-text="addr.address_line2"></p>
                            <p class="lining-nums" x-text="addr.city + ', ' + addr.state + ' ' + addr.postal_code"></p>
                            <p x-text="addr.country"></p>
                            <p x-show="addr.phone" class="text-xs text-muted mt-2 lining-nums" x-text="addr.phone"></p>
                        </div>
                    </div>

                    <!-- Actions Footer (pinned to bottom with mt-auto) -->
                    <div class="pt-4 mt-auto border-t border-[#ECEEE6]">
                        <div x-show="confirmingRemoveId !== addr.id" class="flex flex-wrap items-center gap-4">
                            <template x-if="editingId === addr.id">
                                <span class="min-h-[44px] inline-flex items-center text-sm text-muted">Editing</span>
                            </template>
                            <template x-if="editingId !== addr.id">
                                <button 
                                    type="button" 
                                    @click="editAddress(addr, $event.currentTarget)"
                                    class="min-h-[44px] inline-flex items-center text-sm text-[#304C3D] hover:text-ink font-medium underline underline-offset-4 decoration-[#A8B7A0] hover:decoration-[#304C3D] focus-visible:outline-none focus-visible:ring-2 focus-visible:ring-[#304C3D] focus-visible:ring-offset-2 cursor-pointer"
                                    :aria-label="'Edit address for ' + addr.first_name + ' ' + addr.last_name + ' (' + addr.city + ')'"
                                >
                                    Edit
                                </button>
                            </template>

                            <button 
                                x-show="!addr.is_default"
                                type="button" 
                                @click="setAsDefault(addr.id)"
                                class="min-h-[44px] inline-flex items-center text-sm text-[#304C3D] hover:text-ink font-medium underline underline-offset-4 decoration-[#A8B7A0] hover:decoration-[#304C3D] focus-visible:outline-none focus-visible:ring-2 focus-visible:ring-[#304C3D] focus-visible:ring-offset-2 cursor-pointer"
                            >
                                Set as default
                            </button>

                            <button 
                                type="button" 
                                @click="startRemove(addr.id)"
                                class="min-h-[44px] inline-flex items-center text-sm text-[#304C3D] hover:text-ink font-medium underline underline-offset-4 decoration-[#A8B7A0] hover:decoration-[#304C3D] focus-visible:outline-none focus-visible:ring-2 focus-visible:ring-[#304C3D] focus-visible:ring-offset-2 cursor-pointer"
                                :aria-label="'Remove address for ' + addr.first_name + ' ' + addr.last_name + ' (' + addr.city + ')'"
                            >
                                Remove
                            </button>
                        </div>

                        <!-- Inline Remove Confirmation -->
                        <div 
                            x-show="confirmingRemoveId === addr.id" 
                            x-cloak 
                            class="flex flex-wrap items-center justify-between gap-3"
                            role="alertdialog"
                            aria-label="Confirm address removal"
                        >
                            <span class="text-sm text-ink font-medium">Remove this address?</span>
                            <div class="flex items-center gap-4">
                                <button 
                                    type="button" 
                                    :id="'confirm-remove-' + addr.id"
                                    @click="confirmRemove(addr.id)"
                                    class="min-h-[44px] inline-flex items-center text-sm text-[#B3453A] hover:text-ink font-medium underline underline-offset-4 focus-visible:outline-none focus-visible:ring-2 focus-visible:ring-[#304C3D] focus-visible:ring-offset-2 cursor-pointer"
                                >
                                    Yes, remove
                                </button>
                                <button 
                                    type="button" 
                                    @click="cancelRemove()"
                                    class="min-h-[44px] inline-flex items-center text-sm text-[#304C3D] hover:text-ink font-medium underline underline-offset-4 decoration-[#A8B7A0] hover:decoration-[#304C3D] focus-visible:outline-none focus-visible:ring-2 focus-visible:ring-[#304C3D] focus-visible:ring-offset-2 cursor-pointer"
                                >
                                    Cancel
                                </button>
                            </div>
                        </div>
                    </div>
                </article>
            </template>
        </div>

        <!-- Empty State -->
        <div x-show="addresses.length === 0" x-cloak class="py-12 text-center border border-[#E3E6DD] bg-white p-8 space-y-4">
            <p class="font-serif text-xl text-ink font-normal">No saved addresses</p>
            <button 
                type="button" 
                @click="openAddForm($event.currentTarget)"
                class="btn-primary w-full sm:w-auto cursor-pointer"
            >
                Add new address
            </button>
        </div>
    </section>
</div>
@endsection
