@props([
    'providers' => config('demo.auth.enabled_providers', ['google', 'apple']),
    'mode' => 'signin', // 'signin' | 'register'
])

@php
    $normalizedProviders = array_values(array_intersect(
        array_map('strtolower', (array) $providers),
        ['google', 'apple']
    ));
    $isRegister = $mode === 'register';
    $dividerLabel = $isRegister ? 'Or register with email' : 'Or continue with email';
@endphp

@if(count($normalizedProviders) > 0)
    <div x-data="{ socialNotice: '' }" class="space-y-4">
        <div class="space-y-3">
            @foreach($normalizedProviders as $provider)
                @if($provider === 'google')
                    @php
                        $googleLabel = $isRegister ? 'Sign up with Google' : 'Continue with Google';
                    @endphp
                    {{-- BACKEND: replace with Laravel Socialite redirect (google, apple) --}}
                    <button
                        type="button"
                        @click="socialNotice = 'Social sign-in is currently unavailable. Please continue with email.'"
                        class="w-full h-12 min-h-[48px] px-4 bg-white border border-[#D5DBCF] hover:border-[#A8B7A0] hover:bg-[#F4F6F1] text-ink text-sm font-medium flex items-center justify-center gap-3 transition-colors focus-visible:outline focus-visible:outline-2 focus-visible:outline-[#304C3D] focus-visible:outline-offset-2 cursor-pointer rounded-none"
                        aria-label="{{ $googleLabel }}"
                    >
                        <svg class="w-5 h-5 shrink-0" viewBox="0 0 24 24" aria-hidden="true">
                            <path fill="#4285F4" d="M22.56 12.25c0-.78-.07-1.53-.2-2.25H12v4.26h5.92c-.26 1.37-1.04 2.53-2.21 3.31v2.77h3.57c2.08-1.92 3.28-4.74 3.28-8.09z" />
                            <path fill="#34A853" d="M12 23c2.97 0 5.46-.98 7.28-2.66l-3.57-2.77c-.98.66-2.23 1.06-3.71 1.06-2.86 0-5.29-1.93-6.16-4.53H2.18v2.84C3.99 20.53 7.7 23 12 23z" />
                            <path fill="#FBBC05" d="M5.84 14.09c-.22-.66-.35-1.36-.35-2.09s.13-1.43.35-2.09V7.06H2.18C1.43 8.55 1 10.22 1 12s.43 3.45 1.18 4.94l2.85-2.22.81-.63z" />
                            <path fill="#EA4335" d="M12 5.38c1.62 0 3.06.56 4.21 1.64l3.15-3.15C17.45 2.09 14.97 1 12 1 7.7 1 3.99 3.47 2.18 7.06l3.66 2.84c.87-2.6 3.3-4.52 6.16-4.52z" />
                        </svg>
                        <span>{{ $googleLabel }}</span>
                    </button>
                @elseif($provider === 'apple')
                    @php
                        $appleLabel = $isRegister ? 'Sign up with Apple' : 'Continue with Apple';
                    @endphp
                    {{-- BACKEND: replace with Laravel Socialite redirect (google, apple) --}}
                    <button
                        type="button"
                        @click="socialNotice = 'Social sign-in is currently unavailable. Please continue with email.'"
                        class="w-full h-12 min-h-[48px] px-4 bg-white border border-[#D5DBCF] hover:border-[#A8B7A0] hover:bg-[#F4F6F1] text-ink text-sm font-medium flex items-center justify-center gap-3 transition-colors focus-visible:outline focus-visible:outline-2 focus-visible:outline-[#304C3D] focus-visible:outline-offset-2 cursor-pointer rounded-none"
                        aria-label="{{ $appleLabel }}"
                    >
                        <svg class="w-5 h-5 shrink-0 fill-current text-forest" viewBox="0 0 24 24" aria-hidden="true">
                            <path d="M18.71 19.5c-.83 1.24-1.71 2.45-3.05 2.47-1.34.03-1.77-.79-3.29-.79-1.53 0-2 .77-3.27.82-1.31.05-2.3-1.32-3.14-2.53C4.25 17 2.94 12.45 4.7 9.39c.87-1.52 2.43-2.48 4.12-2.51 1.28-.02 2.5.87 3.29.87.78 0 2.26-1.07 3.81-.91.65.03 2.47.26 3.64 1.98-.09.06-2.17 1.28-2.15 3.81.03 3.02 2.65 4.03 2.68 4.04-.03.07-.42 1.44-1.38 2.83M15.97 6.37c.61-.75 1.04-1.8 0.92-2.85-.9.04-2 .6-2.64 1.35-.56.65-.99 1.7-0.86 2.72.99.08 2-.48 2.58-1.22z" />
                        </svg>
                        <span>{{ $appleLabel }}</span>
                    </button>
                @endif
            @endforeach
        </div>

        <!-- Demo Social Sign-In Notice -->
        <div
            x-show="socialNotice"
            x-cloak
            x-transition
            class="p-3.5 bg-[#E8EEE5] border border-[#304C3D] text-[#304C3D] text-xs font-medium flex items-center justify-between gap-2"
            role="status"
            aria-live="polite"
        >
            <span x-text="socialNotice"></span>
            <button type="button" @click="socialNotice = ''" class="w-8 h-8 -mr-1 text-[#304C3D] hover:text-ink flex items-center justify-center shrink-0 cursor-pointer" aria-label="Close message">✕</button>
        </div>

        <!-- Divider -->
        <div class="relative flex items-center justify-center my-6">
            <div class="absolute inset-0 flex items-center" aria-hidden="true">
                <div class="w-full border-t border-[#ECEEE6]"></div>
            </div>
            <div class="relative flex justify-center">
                <span class="bg-white px-4 text-muted uppercase tracking-[0.2em] text-xs font-medium">
                    {{ $dividerLabel }}
                </span>
            </div>
        </div>
    </div>
@endif
