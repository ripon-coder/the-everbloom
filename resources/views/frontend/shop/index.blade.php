@extends('layouts.app')

@section('title', 'Shop All Botanical Formulations — The Everbloom')
@section('meta_description', 'Explore our complete apothecary catalogue of serums, creams, cleansers, and botanical sun shields.')

@section('content')
<div class="site-container py-8 md:py-12 space-y-8">
    <x-storefront.breadcrumbs :items="[
        ['label' => 'Shop All', 'url' => '']
    ]" />

    <div class="border-b border-beige pb-6 flex flex-col md:flex-row md:items-end justify-between gap-4">
        <div>
            <h1 class="font-serif text-3xl sm:text-4xl text-ink font-normal">All Botanical Formulations</h1>
            <p class="text-sm text-muted mt-2 max-w-xl">
                Explore our full ritual collection crafted with organic cold-pressed plant extracts and gentle lipid complexes.
            </p>
        </div>
        <p class="text-xs uppercase tracking-widest text-muted">
            Showing {{ count($products ?? config('demo.products')) }} Formulations
        </p>
    </div>

    <!-- Product Grid -->
    <x-storefront.product-grid :products="$products ?? config('demo.products')" columns="4" />
</div>
@endsection
