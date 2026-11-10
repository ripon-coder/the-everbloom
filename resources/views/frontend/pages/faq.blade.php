@extends('layouts.app')

@section('title', 'Frequently Asked Questions — The Everbloom')
@section('meta_description', 'Answers to common questions regarding our botanical sourcing, shipping, orders, and skin compatibility.')

@section('content')
<div class="site-container py-12 md:py-16 space-y-12 max-w-4xl">
    <x-storefront.breadcrumbs :items="[['label' => 'Apothecary FAQs', 'url' => '']]" />

    <div class="text-center space-y-3">
        <span class="text-xs uppercase tracking-[0.25em] font-medium text-forest block">Support & Inquiries</span>
        <h1 class="font-serif text-3xl sm:text-4xl text-ink font-normal">Frequently Asked Questions</h1>
        <p class="text-sm text-muted max-w-lg mx-auto">
            Everything you need to know about our formulations, packaging, shipping policies, and botanical care rituals.
        </p>
    </div>

    <div class="space-y-10">
        @foreach(config('demo.faq') as $group)
            <div class="bg-white p-6 sm:p-8 border border-beige space-y-4">
                <h2 class="font-serif text-xl text-ink font-medium border-b border-beige pb-3">
                    {{ $group['category'] }}
                </h2>
                <x-storefront.accordion :items="$group['questions']" />
            </div>
        @endforeach
    </div>
</div>
@endsection
