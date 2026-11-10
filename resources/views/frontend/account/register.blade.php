@extends('layouts.app')

@section('title', 'Create an Account — The Everbloom')

@section('content')
<div class="site-container py-16 md:py-24 max-w-md mx-auto">
    <div class="bg-white p-8 border border-beige space-y-6">
        <div class="text-center space-y-2">
            <span class="text-xs uppercase tracking-[0.2em] font-medium text-forest block">Client Membership</span>
            <h1 class="font-serif text-2xl sm:text-3xl text-ink font-normal">Create Your Account</h1>
            <p class="text-xs text-muted">Join our botanical correspondence circle and track orders seamlessly.</p>
        </div>

        <form class="space-y-4" onsubmit="event.preventDefault(); window.location.href='/account';">
            <div class="grid grid-cols-2 gap-3">
                <div>
                    <label class="block text-xs uppercase tracking-widest text-muted mb-1 font-medium">First Name</label>
                    <input type="text" required class="w-full px-3.5 py-2.5 text-sm border border-beige bg-ivory focus:outline-none focus:ring-1 focus:ring-forest">
                </div>
                <div>
                    <label class="block text-xs uppercase tracking-widest text-muted mb-1 font-medium">Last Name</label>
                    <input type="text" required class="w-full px-3.5 py-2.5 text-sm border border-beige bg-ivory focus:outline-none focus:ring-1 focus:ring-forest">
                </div>
            </div>
            <div>
                <label class="block text-xs uppercase tracking-widest text-muted mb-1 font-medium">Email Address</label>
                <input type="email" required placeholder="name@domain.com" class="w-full px-3.5 py-2.5 text-sm border border-beige bg-ivory focus:outline-none focus:ring-1 focus:ring-forest">
            </div>
            <div>
                <label class="block text-xs uppercase tracking-widest text-muted mb-1 font-medium">Create Password</label>
                <input type="password" required class="w-full px-3.5 py-2.5 text-sm border border-beige bg-ivory focus:outline-none focus:ring-1 focus:ring-forest">
            </div>
            <x-storefront.button type="submit" variant="primary" class="w-full">
                Register Account (Demo)
            </x-storefront.button>
        </form>

        <div class="text-center pt-2 border-t border-beige text-xs text-muted">
            <span>Already registered?</span>
            <a href="/account/login" class="text-forest font-medium hover:underline ml-1">Sign in</a>
        </div>
    </div>
</div>
@endsection
