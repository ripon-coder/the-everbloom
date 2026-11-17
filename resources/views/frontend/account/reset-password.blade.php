@extends('layouts.app')

@section('title', 'Reset Password — ' . config('demo.site.name'))
@section('meta_description', 'Set a new password for your The Everbloom account.')
@section('meta_robots', 'noindex, nofollow')
@section('hide_newsletter', true)

@section('content')
<div class="site-container max-sm:!px-4 py-12 lg:py-16">
    <div class="mb-6">
        <x-storefront.breadcrumbs :items="[
            ['label' => 'Reset Password', 'url' => ''],
        ]" />
    </div>

    <div class="mx-auto w-full max-w-md">
        <div class="bg-white border border-[#E3E6DD] p-6 sm:p-8 space-y-6">
            <div class="border-b border-[#ECEEE6] pb-5">
                <span class="text-xs uppercase tracking-[0.2em] font-medium text-forest block mb-1">
                    ACCOUNT RECOVERY
                </span>
                <h1 class="font-serif text-3xl sm:text-4xl text-ink font-normal tracking-tight">
                    Reset Password
                </h1>
                <p class="text-sm text-muted mt-1.5 leading-relaxed">
                    Choose a new password with at least 8 characters including a letter and a number.
                </p>
            </div>

            @if ($errors->any())
                <div class="p-4 bg-[#FDF4F3] border border-[#B3453A] text-[#B3453A] text-xs font-medium" role="alert">
                    {{ $errors->first() }}
                </div>
            @endif

            <form method="POST" action="{{ route('password.update') }}" class="space-y-5" novalidate>
                @csrf
                <input type="hidden" name="token" value="{{ $token }}">

                <div>
                    <label for="reset-email" class="block text-xs uppercase tracking-[0.2em] font-medium text-ink mb-2">
                        Email Address
                    </label>
                    <input
                        id="reset-email"
                        type="email"
                        name="email"
                        value="{{ old('email', $email) }}"
                        required
                        autocomplete="email"
                        class="w-full min-h-[48px] px-4 py-3 text-base bg-ivory border border-[#D5DBCF] focus:border-forest focus:outline-none rounded-none"
                    >
                </div>

                <div>
                    <label for="reset-password" class="block text-xs uppercase tracking-[0.2em] font-medium text-ink mb-2">
                        New Password
                    </label>
                    <input
                        id="reset-password"
                        type="password"
                        name="password"
                        required
                        autocomplete="new-password"
                        class="w-full min-h-[48px] px-4 py-3 text-base bg-ivory border border-[#D5DBCF] focus:border-forest focus:outline-none rounded-none"
                    >
                </div>

                <div>
                    <label for="reset-password-confirmation" class="block text-xs uppercase tracking-[0.2em] font-medium text-ink mb-2">
                        Confirm Password
                    </label>
                    <input
                        id="reset-password-confirmation"
                        type="password"
                        name="password_confirmation"
                        required
                        autocomplete="new-password"
                        class="w-full min-h-[48px] px-4 py-3 text-base bg-ivory border border-[#D5DBCF] focus:border-forest focus:outline-none rounded-none"
                    >
                </div>

                <button type="submit" class="btn-primary w-full text-center">
                    Reset Password
                </button>
            </form>
        </div>
    </div>
</div>
@endsection

