@props([
    'dark' => false,
])

<form 
    x-data="{
        email: '',
        status: 'idle', // idle, loading, success, error
        errorMessage: '',
        submit() {
            const re = /^[^\s@]+@[^\s@]+\.[^\s@]+$/;
            if (!this.email || !re.test(this.email)) {
                this.status = 'error';
                this.errorMessage = 'Please enter a valid email address.';
                return;
            }
            this.status = 'loading';
            // BACKEND: replace with newsletter subscription endpoint
            setTimeout(() => {
                this.status = 'success';
                this.email = '';
            }, 600);
        }
    }"
    @submit.prevent="submit"
    {{ $attributes->merge(['class' => 'w-full']) }}
    novalidate
>
    <div class="relative flex flex-col sm:flex-row gap-2.5">
        <label for="newsletter-email" class="sr-only">Your email address</label>
        <input 
            type="email" 
            id="newsletter-email"
            x-model="email"
            placeholder="Enter your email address" 
            :disabled="status === 'loading' || status === 'success'"
            class="w-full px-4 py-3 text-xs sm:text-sm font-sans rounded-none transition-colors focus-visible:outline-none focus-visible:ring-2 focus-visible:ring-forest {{ $dark ? 'bg-white/10 text-ivory placeholder:text-ivory/60 border border-white/20 focus:border-sage' : 'bg-ivory text-ink placeholder:text-muted border border-beige-dark/50 focus:border-forest' }}"
            required
        />

        <button 
            type="submit" 
            :disabled="status === 'loading' || status === 'success'"
            class="shrink-0 px-6 py-3 font-sans text-xs uppercase tracking-[0.2em] font-medium transition-all duration-200 rounded-none cursor-pointer disabled:opacity-50 min-h-[44px] {{ $dark ? 'bg-sage text-forest hover:bg-white hover:text-forest' : 'bg-forest text-ivory hover:bg-[#203329]' }}"
        >
            <span x-show="status === 'idle'">Subscribe</span>
            <span x-show="status === 'loading'" x-cloak>Submitting...</span>
            <span x-show="status === 'success'" x-cloak>Subscribed</span>
        </button>
    </div>

    <!-- Error message -->
    <p 
        x-show="status === 'error'" 
        x-text="errorMessage" 
        class="mt-2 text-xs text-rose-700 text-left font-sans font-medium" 
        x-cloak
    ></p>

    <!-- Success message -->
    <div 
        x-show="status === 'success'" 
        class="mt-2 text-xs text-forest text-left flex items-center gap-1.5 font-medium" 
        x-cloak
    >
        <svg class="w-4 h-4 shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24">
            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7" />
        </svg>
        <span>Thank you for subscribing to The Everbloom correspondence.</span>
    </div>
</form>
