@extends('layouts.account')

@section('title', 'Saved Wishlist — ' . config('demo.site.name'))
@section('meta_description', 'View and manage your saved wishlist of botanical skincare formulations at The Everbloom.')

@section('breadcrumbs')
    <x-storefront.breadcrumbs :items="[
        ['label' => 'My Account', 'url' => route('account')],
        ['label' => 'Wishlist', 'url' => ''],
    ]" />
@endsection

@section('page_title', 'Saved Botanical Rituals')

@section('page_subtitle')
    <p class="text-sm text-muted">
        Formulations you have saved to your personal curation list.
    </p>
@endsection

@section('header_actions')
    <div x-data class="text-xs uppercase tracking-[0.2em] text-muted font-medium" aria-live="polite">
        <span class="price" x-text="$store.wishlist ? $store.wishlist.count : 0">0</span> Saved
    </div>
@endsection

@section('account_content')
<div x-data class="space-y-8">
    <!-- Empty State -->
    <div x-show="!$store.wishlist || $store.wishlist.count === 0" class="py-16 text-center border border-[#E3E6DD] bg-white p-8 max-w-lg mx-auto space-y-4">
        <div class="w-14 h-14 mx-auto rounded-full bg-sage-light/50 flex items-center justify-center text-forest/70">
            <svg class="w-7 h-7" fill="none" stroke="currentColor" viewBox="0 0 24 24" aria-hidden="true">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M4.318 6.318a4.5 4.5 0 000 6.364L12 20.364l7.682-7.682a4.5 4.5 0 00-6.364-6.364L12 7.636l-1.318-1.318a4.5 4.5 0 00-6.364 0z" />
            </svg>
        </div>
        <div class="space-y-1">
            <h2 class="font-serif text-2xl text-ink font-normal">No Saved Formulations Yet</h2>
            <p class="text-sm text-muted max-w-xs mx-auto leading-relaxed">
                Select the heart icon on any product to curate your personal botanical ritual.
            </p>
        </div>
        <div class="pt-2">
            <a href="{{ route('shop.index') }}" class="btn-primary w-full sm:w-auto">
                Explore Formulations
            </a>
        </div>
    </div>

    <!-- Wishlist Items Grid -->
    <div x-show="$store.wishlist && $store.wishlist.count > 0" x-cloak class="grid grid-cols-2 md:grid-cols-3 gap-3 sm:gap-6">
        <template x-for="item in $store.wishlist.items" :key="item.id || item">
            <article class="bg-white border border-[#E3E6DD] p-3 sm:p-4 flex flex-col justify-between">
                <div>
                    <a :href="'/shop/product/' + item.slug" class="block aspect-[4/5] bg-sage-light/40 overflow-hidden mb-3 relative">
                        <x-storefront.image 
                            ::src="item.image ? '/images/' + item.image : ''" 
                            ::alt="item.name || 'Botanical formulation'" 
                            aspect-ratio="aspect-[4/5]" 
                            class="w-full h-full object-cover" 
                        />
                    </a>
                    <span class="text-xs uppercase tracking-[0.2em] text-muted block truncate" x-text="item.category"></span>
                    <a :href="'/shop/product/' + item.slug" class="font-serif text-base text-ink hover:text-forest transition-colors line-clamp-2 mt-0.5 min-h-[2.5rem]" x-text="item.name"></a>
                    <p class="price text-sm font-medium text-ink mt-1.5" x-text="'$' + parseFloat(item.price || 0).toFixed(2)"></p>
                </div>
                <div class="mt-4 pt-3 border-t border-[#ECEEE6] space-y-2">
                    <button 
                        type="button" 
                        @click="$store.cart.add(item.id, 1)" 
                        class="btn-primary w-full !px-3 !py-2.5 !text-xs cursor-pointer"
                    >
                        Add to Bag
                    </button>
                    <div class="flex items-center justify-between">
                        <button 
                            type="button" 
                            @click="$store.wishlist.toggle(item)" 
                            class="min-h-[44px] inline-flex items-center text-[13px] text-muted hover:text-ink underline underline-offset-4 cursor-pointer"
                            :aria-label="'Remove ' + item.name + ' from wishlist'"
                        >
                            Remove
                        </button>
                        <a :href="'/shop/product/' + item.slug" class="min-h-[44px] inline-flex items-center text-[13px] text-forest font-medium hover:underline underline-offset-4">
                            Details →
                        </a>
                    </div>
                </div>
            </article>
        </template>
    </div>
</div>
@endsection
