@extends('layouts.app')

@section('title', 'Privacy Policy — The Everbloom')
@section('meta_description', 'Privacy practices and data protection disclosures for The Everbloom clients.')

@section('content')
<div class="site-container py-12 md:py-16 space-y-10 max-w-4xl">
    <x-storefront.breadcrumbs :items="[['label' => 'Privacy Policy', 'url' => '']]" />

    <div class="border-b border-beige pb-6">
        <h1 class="font-serif text-3xl sm:text-4xl text-ink font-normal">Privacy Policy</h1>
        <p class="text-xs uppercase tracking-widest text-muted mt-2">Last Updated: November 10, 2026</p>
    </div>

    <div class="prose max-w-none text-sm text-ink/80 space-y-6 leading-relaxed font-sans">
        <section class="space-y-3">
            <h2 class="font-serif text-xl text-ink">1. Data We Collect</h2>
            <p>When you browse our storefront, create an account, or place an order with The Everbloom, we collect the information necessary to fulfill your requests, including your name, email address, phone number, delivery address, and order history, alongside essential technical session identifiers.</p>
        </section>

        <section class="space-y-3">
            <h2 class="font-serif text-xl text-ink">2. How Information is Utilized</h2>
            <p>Your personal details are used solely to process and deliver your botanical formulations, provide concierge client support, maintain your saved preferences, and deliver editorial journal updates when you choose to subscribe.</p>
        </section>

        <section class="space-y-3" id="cookies">
            <h2 class="font-serif text-xl text-ink">3. Cookie Practices</h2>
            <p>We use essential browser storage and session cookies to preserve your shopping bag, saved wishlist, and authenticated account session across visits, ensuring a seamless browsing and checkout experience.</p>
        </section>

        <section class="space-y-3">
            <h2 class="font-serif text-xl text-ink">4. Your Individual Rights</h2>
            <p>You may review, update, export, or request deletion of your personal account details at any time through your account settings or by contacting our client care team at {{ config('demo.site.contact.email') }}.</p>
        </section>
    </div>
</div>
@endsection
