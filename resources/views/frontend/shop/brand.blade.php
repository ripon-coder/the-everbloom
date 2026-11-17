@extends('layouts.app')

@section('title', !empty($brand['meta_title']) ? $brand['meta_title'] : (($brand['name'] ?? 'Brand') . ' — The Everbloom'))
@section('meta_description', !empty($brand['meta_description']) ? $brand['meta_description'] : ($brand['description'] ?? 'Botanical skincare formulations.'))

@section('content')
<div class="pb-16 md:pb-24">
    <div class="site-container pt-8 md:pt-10 pb-6 border-b border-beige/60">
        <x-storefront.breadcrumbs :items="[
            ['label' => 'Shop All', 'url' => '/shop'],
            ['label' => $brand['name'] ?? 'Brand', 'url' => '']
        ]" />

        <div class="mt-4 flex flex-col md:flex-row md:items-end justify-between gap-4">
            <div class="flex items-start gap-4 sm:gap-6">
                @if(!empty($brand['resolved_logo']))
                    <img
                        src="{{ $brand['resolved_logo'] }}"
                        alt="{{ $brand['name'] ?? 'Brand' }}"
                        class="w-20 h-20 sm:w-24 sm:h-24 object-cover rounded border border-beige/80 shrink-0"
                    >
                @endif
                <div>
                    <span class="text-xs uppercase tracking-[0.2em] font-medium text-forest block mb-1">Botanical Brand</span>
                    <h1 class="font-serif text-3xl sm:text-4xl text-ink font-normal">{{ $brand['name'] ?? 'Brand' }}</h1>
                    @if(!empty($brand['description']))
                        <p class="text-sm text-muted mt-2 max-w-xl">{{ $brand['description'] }}</p>
                    @endif
                </div>
            </div>
            <p class="text-xs uppercase tracking-widest text-muted">
                Showing {{ count($products ?? []) }} Formulations
            </p>
        </div>
    </div>

    <div class="site-container pt-8 md:pt-12">
        @if(count($products ?? []) > 0)
            <x-storefront.product-grid :products="$products" columns="4" />
        @else
            <div class="text-center py-16 border border-beige/60 bg-white/50">
                <p class="font-serif text-xl text-ink">No formulations currently available for this brand.</p>
                <a href="{{ route('shop.index') }}" class="btn-secondary mt-4 inline-block">Browse All Formulations</a>
            </div>
        @endif
    </div>
</div>
@endsection

