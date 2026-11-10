@php
    $footerNav = config('demo.navigation.footer', []);
    $site = config('demo.site');
@endphp

<footer class="bg-forest text-ivory border-t border-forest-600/50 mt-auto" role="contentinfo">
    <!-- Top Editorial Newsletter Band -->
    <div class="border-b border-white/10 py-12 md:py-16">
        <div class="site-container">
            <div class="max-w-3xl mx-auto text-center">
                <span class="text-xs uppercase tracking-[0.25em] text-sage font-medium block mb-2">
                    Botanical Correspondence
                </span>
                <h3 class="font-serif text-2xl sm:text-3xl md:text-4xl text-ivory font-normal tracking-tight">
                    Join The Everbloom Apothecary
                </h3>
                <p class="mt-3 text-sm text-ivory/80 max-w-lg mx-auto leading-relaxed">
                    Receive seasonal harvest bulletins, private botanical archive previews, and mindful skincare rituals directly in your inbox.
                </p>
                <div class="mt-8 max-w-md mx-auto">
                    <x-storefront.newsletter-form dark />
                </div>
            </div>
        </div>
    </div>

    <!-- Main Navigation Columns -->
    <div class="site-container py-16 md:py-20">
        <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-5 gap-10 lg:gap-12">
            
            <!-- Brand Column -->
            <div class="lg:col-span-2 space-y-4">
                <a href="/" class="block">
                    <span class="font-serif text-2xl tracking-wide text-ivory font-normal block">
                        THE EVERBLOOM
                    </span>
                    <span class="text-[10px] uppercase tracking-[0.25em] text-sage block mt-0.5">
                        Naturally Beautiful. Always Blooming.
                    </span>
                </a>

                <p class="text-sm text-ivory/70 leading-relaxed max-w-sm">
                    Pure botanical skincare formulated with cold-pressed plant extracts, nourishing oils, and gentle active botanicals. Crafted with reverence for skin and earth.
                </p>

                <div class="pt-2 text-xs text-ivory/60 space-y-1">
                    <p><span class="text-sage">Apothecary Concierge:</span> {{ $site['contact']['phone'] }}</p>
                    <p><span class="text-sage">Email:</span> {{ $site['contact']['email'] }}</p>
                    <p><span class="text-sage">Hours:</span> {{ $site['contact']['hours'] }}</p>
                </div>

                <!-- Social Handles -->
                <div class="flex items-center space-x-4 pt-2">
                    @foreach($site['socials'] as $social)
                        <a 
                            href="{{ $social['url'] }}" 
                            target="_blank" 
                            rel="noopener noreferrer" 
                            class="text-ivory/70 hover:text-white text-xs uppercase tracking-widest transition-colors"
                        >
                            {{ $social['platform'] }}
                        </a>
                    @endforeach
                </div>
            </div>

            <!-- Shop Column -->
            <div>
                <h4 class="font-serif text-base text-ivory font-medium tracking-wide mb-4">
                    {{ $footerNav['shop']['title'] ?? 'Shop' }}
                </h4>
                <ul class="space-y-2.5 text-xs text-ivory/70">
                    @foreach($footerNav['shop']['links'] ?? [] as $link)
                        <li>
                            <a href="{{ $link['url'] }}" class="hover:text-sage transition-colors">
                                {{ $link['label'] }}
                            </a>
                        </li>
                    @endforeach
                </ul>
            </div>

            <!-- House Column -->
            <div>
                <h4 class="font-serif text-base text-ivory font-medium tracking-wide mb-4">
                    {{ $footerNav['about']['title'] ?? 'The House' }}
                </h4>
                <ul class="space-y-2.5 text-xs text-ivory/70">
                    @foreach($footerNav['about']['links'] ?? [] as $link)
                        <li>
                            <a href="{{ $link['url'] }}" class="hover:text-sage transition-colors">
                                {{ $link['label'] }}
                            </a>
                        </li>
                    @endforeach
                </ul>
            </div>

            <!-- Concierge Column -->
            <div>
                <h4 class="font-serif text-base text-ivory font-medium tracking-wide mb-4">
                    {{ $footerNav['help']['title'] ?? 'Concierge' }}
                </h4>
                <ul class="space-y-2.5 text-xs text-ivory/70">
                    @foreach($footerNav['help']['links'] ?? [] as $link)
                        <li>
                            <a href="{{ $link['url'] }}" class="hover:text-sage transition-colors">
                                {{ $link['label'] }}
                            </a>
                        </li>
                    @endforeach
                </ul>
            </div>

        </div>
    </div>

    <!-- Bottom Legal Bar -->
    <div class="border-t border-white/10 py-6 text-xs text-ivory/60 bg-[#253B2F]">
        <div class="site-container flex flex-col md:flex-row items-center justify-between gap-4">
            <div class="flex flex-wrap items-center gap-6 text-[11px] uppercase tracking-wider">
                <span>© {{ date('Y') }} THE EVERBLOOM. ALL RIGHTS RESERVED.</span>
                <span>CURRENCY: {{ $site['currency']['code'] }} ({{ $site['currency']['symbol'] }})</span>
            </div>

            <div class="flex flex-wrap items-center gap-6 text-[11px] uppercase tracking-wider">
                @foreach($footerNav['legal']['links'] ?? [] as $link)
                    <a href="{{ $link['url'] }}" class="hover:text-sage transition-colors">
                        {{ $link['label'] }}
                    </a>
                @endforeach
            </div>
        </div>
    </div>
</footer>
