@props([
    'dark' => false,
    'idPrefix' => 'newsletter',
])

<form 
    x-data="{
        email: '',
        status: 'idle', // idle, loading, success, error
        errorMessage: '',
        submit() {
            const re = /^[^\s@]+@[^\s@]+\.[^\s@]+$/;
            if (!this.email || !re.test(this.email.trim())) {
                this.status = 'error';
                this.errorMessage = 'Please enter a valid email address (e.g. name@example.com).';
                this.$nextTick(() => {
                    this.$refs.emailInput?.focus();
                });
                return;
            }
            this.status = 'loading';
            this.errorMessage = '';
            setTimeout(() => {
                this.status = 'success';
                this.email = '';
            }, 500);
        }
    }"
    @submit.prevent="submit"
    {{ $attributes->merge(['class' => 'w-full']) }}
    novalidate
>
    <div class="relative flex flex-col sm:flex-row gap-2.5">
        <label for="{{ $idPrefix }}-email" class="sr-only">Email address</label>
        <input 
            x-ref="emailInput"
            type="email" 
            id="{{ $idPrefix }}-email"
            name="email"
            autocomplete="email"
            x-model="email"
            @input="if (status === 'error') { status = 'idle'; errorMessage = ''; }"
            placeholder="name@example.com" 
            :disabled="status === 'loading' || status === 'success'"
            :aria-invalid="status === 'error' ? 'true' : 'false'"
            :aria-describedby="status === 'error' ? '{{ $idPrefix }}-error' : null"
            class="w-full px-4 py-3 min-h-[48px] text-base font-sans rounded-none border transition-all duration-200 focus:outline-none {{ $dark ? 'bg-white/10 text-ivory placeholder:text-ivory/60 border-white/25 hover:border-white/50 focus:border-sage focus:shadow-[0_0_0_3px_rgba(218,227,214,0.2)]' : 'bg-ivory text-ink placeholder:text-[#8C9389] border-[#D5DBCF] hover:border-[#A8B7A0] focus:border-forest focus:shadow-[0_0_0_3px_rgba(48,76,61,0.12)]' }}"
            required
        />

        <button 
            type="submit" 
            :disabled="status === 'loading' || status === 'success'"
            class="shrink-0 px-6 py-3 font-sans text-xs uppercase tracking-[0.2em] font-medium transition-all duration-200 rounded-none cursor-pointer disabled:opacity-50 min-h-[48px] {{ $dark ? 'bg-sage text-forest hover:bg-ivory hover:text-forest' : 'bg-forest text-ivory hover:bg-[#203329]' }}"
        >
            <span x-show="status === 'idle' || status === 'error'">Subscribe</span>
            <span x-show="status === 'loading'" x-cloak>Submitting...</span>
            <span x-show="status === 'success'" x-cloak>Subscribed</span>
        </button>
    </div>

    <!-- Error message -->
    <p 
        id="{{ $idPrefix }}-error"
        role="alert"
        aria-live="polite"
        x-show="status === 'error'" 
        x-text="errorMessage" 
        class="mt-2 text-xs text-left font-sans font-medium {{ $dark ? 'text-rose-200' : 'text-rose-700' }}" 
        x-cloak
    ></p>

    <!-- Success message -->
    <div 
        role="status"
        aria-live="polite"
        x-show="status === 'success'" 
        class="mt-2 text-xs text-left flex items-center gap-1.5 font-medium {{ $dark ? 'text-sage' : 'text-forest' }}" 
        x-cloak
    >
        <svg class="w-4 h-4 shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24" aria-hidden="true">
            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7" />
        </svg>
        <span>Thank you for subscribing to The Everbloom correspondence.</span>
    </div>
</form>
