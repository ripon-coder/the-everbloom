@extends('layouts.app')

@section('title', 'Concierge & Contact — The Everbloom')
@section('meta_description', 'Connect with our apothecary concierge team for skincare guidance, order inquiries, and botanical consultations.')

@section('content')
<div class="site-container py-12 md:py-16 space-y-12">
    <x-storefront.breadcrumbs :items="[['label' => 'Concierge & Contact', 'url' => '']]" />

    <div class="grid grid-cols-1 lg:grid-cols-12 gap-12">
        <div class="lg:col-span-5 space-y-6">
            <span class="text-xs uppercase tracking-[0.25em] font-medium text-forest block">Client Concierge</span>
            <h1 class="font-serif text-3xl sm:text-4xl text-ink font-normal">We Are Here for Your Skin Journey</h1>
            <p class="text-sm text-muted leading-relaxed">
                Whether you have questions regarding formulation compatibility, ingredient sourcing, or tracking a parcel, our apothecary specialists are delighted to assist.
            </p>

            <div class="space-y-4 pt-4 border-t border-beige text-xs text-ink">
                <div>
                    <span class="text-muted uppercase tracking-widest block text-[10px]">Direct Phone Concierge</span>
                    <a href="tel:{{ config('demo.site.contact.phone') }}" class="font-medium text-forest hover:underline">{{ config('demo.site.contact.phone') }}</a>
                </div>
                <div>
                    <span class="text-muted uppercase tracking-widest block text-[10px]">Email Dispatch</span>
                    <a href="mailto:{{ config('demo.site.contact.email') }}" class="font-medium text-forest hover:underline">{{ config('demo.site.contact.email') }}</a>
                </div>
                <div>
                    <span class="text-muted uppercase tracking-widest block text-[10px]">Concierge Operating Hours</span>
                    <p class="text-muted">{{ config('demo.site.contact.hours') }}</p>
                </div>
            </div>
        </div>

        <div class="lg:col-span-7 bg-white p-6 sm:p-8 border border-beige" x-data="{ sent: false, name: '', email: '', message: '' }">
            <div x-show="sent" x-cloak class="p-8 text-center space-y-3">
                <div class="w-10 h-10 rounded-full bg-forest text-ivory flex items-center justify-center mx-auto">
                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7" /></svg>
                </div>
                <h3 class="font-serif text-xl text-ink">Message Received</h3>
                <p class="text-xs text-muted max-w-sm mx-auto">Thank you for reaching out. An apothecary concierge will respond to your inquiry within one business day.</p>
            </div>

            <form x-show="!sent" @submit.prevent="sent = true" class="space-y-4">
                <div>
                    <label class="block text-xs uppercase tracking-widest text-muted mb-1 font-medium">Your Name</label>
                    <input type="text" x-model="name" required class="w-full px-3.5 py-2.5 text-sm border border-beige bg-ivory focus:outline-none focus:ring-1 focus:ring-forest">
                </div>
                <div>
                    <label class="block text-xs uppercase tracking-widest text-muted mb-1 font-medium">Email Address</label>
                    <input type="email" x-model="email" required class="w-full px-3.5 py-2.5 text-sm border border-beige bg-ivory focus:outline-none focus:ring-1 focus:ring-forest">
                </div>
                <div>
                    <label class="block text-xs uppercase tracking-widest text-muted mb-1 font-medium">Inquiry / Note</label>
                    <textarea x-model="message" rows="5" required class="w-full px-3.5 py-2.5 text-sm border border-beige bg-ivory focus:outline-none focus:ring-1 focus:ring-forest"></textarea>
                </div>
                <x-storefront.button type="submit" variant="primary" class="w-full">
                    Send Inquiry
                </x-storefront.button>
            </form>
        </div>
    </div>
</div>
@endsection
