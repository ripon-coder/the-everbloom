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
        <div class="p-4 bg-amber-50 border border-amber-200 text-xs text-amber-900 rounded-sm">
            <strong>Notice to Website Purchaser:</strong> The sections below provide a compliant legal template structure. [Replace with your legal text] where indicated prior to commercial publication.
        </div>

        <section class="space-y-3">
            <h2 class="font-serif text-xl text-ink">1. Data We Collect</h2>
            <p>[Replace with your legal text: Detail your collection of personal details including shipping address, email contact, order logs, and cookie device identifiers.]</p>
        </section>

        <section class="space-y-3">
            <h2 class="font-serif text-xl text-ink">2. How Information is Utilized</h2>
            <p>[Replace with your legal text: Explain order fulfillment, concierge client support, and marketing newsletter communications if opted in.]</p>
        </section>

        <section class="space-y-3" id="cookies">
            <h2 class="font-serif text-xl text-ink">3. Cookie Practices</h2>
            <p>[Replace with your legal text: Clarify necessary session cookies, cart state persistence, and anonymized traffic metrics.]</p>
        </section>

        <section class="space-y-3">
            <h2 class="font-serif text-xl text-ink">4. Your Individual Rights</h2>
            <p>[Replace with your legal text: Disclose customer rights to access, export, modify, or erase personal data upon request to {{ config('demo.site.contact.email') }} under GDPR / CCPA.]</p>
        </section>
    </div>
</div>
@endsection
