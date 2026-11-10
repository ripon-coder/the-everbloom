@extends('layouts.app')

@section('title', ($category['name'] ?? 'Category') . ' — The Everbloom')
@section('meta_description', $category['description'] ?? 'Botanical skincare formulations.')

@section('content')
<div class="site-container py-8 md:py-12 space-y-8">
    <x-storefront.breadcrumbs :items="[
        ['label' => 'Shop All', 'url' => '/shop'],
        ['label' => $category['name'] ?? 'Category', 'url' => '']
    ]" />

    <div class="border-b border-beige pb-6">
        <span class="text-xs uppercase tracking-[0.2em] font-medium text-forest block mb-1">Botanical Collection</span>
        <h1 class="font-serif text-3xl sm:text-4xl text-ink font-normal">{{ $category['name'] ?? 'Category' }}</h1>
        <p class="text-sm text-muted mt-2 max-w-xl">
            {{ $category['description'] ?? 'Explore our targeted formulas.' }}
        </p>
    </div>

    <x-storefront.product-grid :products="$products ?? []" columns="4" />
</div>
@endsection
