@extends('layouts.app')

@section('title', 'Page Not Found — The Everbloom')
@section('meta_description', 'The requested page could not be found. Explore The Everbloom botanical skincare collection.')
@section('meta_robots', 'noindex, follow')

@section('content')
<div class="site-container py-16 md:py-24">
    <div class="max-w-xl mx-auto bg-white border border-[#E3E6DD] p-8 sm:p-12 text-center space-y-6">
        <span class="text-xs uppercase tracking-[0.2em] text-forest font-medium block">
            Error 404
        </span>
        <h1 class="font-serif text-3xl sm:text-4xl text-ink font-normal">
            We Could Not Find That Page
        </h1>
        <p class="text-base text-muted leading-relaxed">
            The botanical formulation or page you are looking for may have moved or no longer exists. Explore our collection or return to the homepage to continue your ritual.
        </p>
        <div class="pt-2 flex flex-col sm:flex-row items-center justify-center gap-4">
            <x-storefront.button href="/shop" variant="primary" class="w-full sm:w-auto">
                Explore Formulations
            </x-storefront.button>
            <x-storefront.button href="/" variant="secondary" class="w-full sm:w-auto">
                Return Home
            </x-storefront.button>
        </div>
    </div>
</div>
@endsection
