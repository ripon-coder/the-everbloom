@props([
    'quantity' => 1,
    'min' => 1,
    'max' => 99,
    'name' => 'quantity',
])

<div 
    x-data="{ qty: {{ $quantity }}, min: {{ $min }}, max: {{ $max }} }" 
    {{ $attributes->merge(['class' => 'inline-flex items-center border border-[#D5DBCF] hover:border-[#A8B7A0] bg-ivory rounded-none select-none transition-colors']) }}
>
    <button 
        type="button" 
        @click="if (qty > min) { qty--; $dispatch('qty-change', qty); }" 
        :disabled="qty <= min"
        class="w-11 h-11 min-w-[44px] min-h-[44px] flex items-center justify-center text-ink/80 hover:text-forest disabled:opacity-30 disabled:cursor-not-allowed transition-colors focus-visible:outline-none cursor-pointer"
        aria-label="Decrease quantity"
    >
        <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24" aria-hidden="true">
            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M20 12H4" />
        </svg>
    </button>

    <input 
        type="number" 
        name="{{ $name }}" 
        x-model.number="qty" 
        @change="
            if (qty < min || isNaN(qty)) qty = min;
            if (qty > max) qty = max;
            $dispatch('qty-change', qty);
        "
        min="{{ $min }}" 
        max="{{ $max }}" 
        class="w-11 h-11 text-center bg-transparent border-0 font-sans text-base font-medium text-ink focus:ring-0 focus:outline-none p-0 [appearance:textfield] [&::-webkit-outer-spin-button]:appearance-none [&::-webkit-inner-spin-button]:appearance-none"
        aria-label="Product quantity"
    />

    <button 
        type="button" 
        @click="if (qty < max) { qty++; $dispatch('qty-change', qty); }" 
        :disabled="qty >= max"
        class="w-11 h-11 min-w-[44px] min-h-[44px] flex items-center justify-center text-ink/80 hover:text-forest disabled:opacity-30 disabled:cursor-not-allowed transition-colors focus-visible:outline-none cursor-pointer"
        aria-label="Increase quantity"
    >
        <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24" aria-hidden="true">
            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4" />
        </svg>
    </button>
</div>
