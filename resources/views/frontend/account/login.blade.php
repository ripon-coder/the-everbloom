@extends('layouts.app')

@section('title', 'Sign In — The Everbloom')

@section('content')
<div class="site-container py-16 md:py-24 max-w-md mx-auto">
    <div class="bg-white p-8 border border-beige space-y-6">
        <div class="text-center space-y-2">
            <span class="text-xs uppercase tracking-[0.2em] font-medium text-forest block">Client Portal</span>
            <h1 class="font-serif text-2xl sm:text-3xl text-ink font-normal">Sign In to Your Account</h1>
            <p class="text-xs text-muted">Access your previous orders and saved botanical formulations.</p>
        </div>

        <form class="space-y-4" onsubmit="event.preventDefault(); window.location.href='/account';">
            <div>
                <label class="block text-xs uppercase tracking-widest text-muted mb-1 font-medium">Email Address</label>
                <input type="email" required placeholder="name@domain.com" class="w-full px-3.5 py-2.5 text-sm border border-beige bg-ivory focus:outline-none focus:ring-1 focus:ring-forest">
            </div>
            <div>
                <div class="flex justify-between items-center mb-1">
                    <label class="block text-xs uppercase tracking-widest text-muted font-medium">Password</label>
                    <a href="#" class="text-xs text-muted hover:text-forest">Forgot?</a>
                </div>
                <input type="password" required class="w-full px-3.5 py-2.5 text-sm border border-beige bg-ivory focus:outline-none focus:ring-1 focus:ring-forest">
            </div>
            <x-storefront.button type="submit" variant="primary" class="w-full">
                Sign In (Demo)
            </x-storefront.button>
        </form>

        <div class="text-center pt-2 border-t border-beige text-xs text-muted">
            <span>New to The Everbloom?</span>
            <a href="/account/register" class="text-forest font-medium hover:underline ml-1">Create an account</a>
        </div>
    </div>
</div>
@endsection
