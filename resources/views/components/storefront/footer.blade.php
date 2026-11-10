@php
    $footerNav = config('demo.navigation.footer', []);
    $site = config('demo.site');
@endphp

<footer class="mt-auto" role="contentinfo">
    <!-- Top Newsletter Band: Separated on Warm Beige with Forest Green CTA -->
    <section class="bg-beige text-ink border-t border-b border-beige-dark/40 py-14 md:py-20" aria-label="Newsletter Subscription">
        <div class="site-container">
            <div class="max-w-2xl mx-auto text-center space-y-4">
                <span class="text-xs uppercase tracking-[0.25em] text-forest font-semibold block">
                    Botanical Correspondence
                </span>
                <h2 class="font-serif text-3xl sm:text-4xl text-ink font-normal tracking-tight">
                    Join The Everbloom Archive
                </h2>
                <p class="text-sm text-ink/80 leading-relaxed max-w-lg mx-auto">
                    Receive seasonal harvest bulletins, private botanical previews, and thoughtful skincare rituals directly in your inbox.
                </p>
                <div class="mt-6 max-w-md mx-auto pt-2">
                    <x-storefront.newsletter-form :dark="false" />
                </div>
            </div>
        </div>
    </section>

    <!-- Main Footer: Forest Background with High-Contrast Ivory & Sage Accents -->
    <div class="bg-forest text-ivory">
        <div class="site-container py-16 md:py-20">
            <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-5 gap-10 lg:gap-12">
                
                <!-- Brand & Heritage Column -->
                <div class="lg:col-span-2 space-y-4">
                    <a href="/" class="block focus-visible:outline-none focus-visible:ring-2 focus-visible:ring-sage" aria-label="{{ $site['name'] }} Home">
                        <span class="font-serif text-2xl tracking-wide text-ivory font-normal block">
                            THE EVERBLOOM
                        </span>
                        <span class="text-xs uppercase tracking-[0.22em] text-sage-light block mt-1 font-sans">
                            Naturally Beautiful. Always Blooming.
                        </span>
                    </a>

                    <p class="text-[13px] text-ivory/85 leading-relaxed max-w-sm">
                        Thoughtfully formulated for everyday rituals using carefully selected botanical ingredients and plant-derived oils.
                    </p>

                    <div class="pt-3 text-xs text-ivory/80 space-y-1.5 font-sans">
                        <p><span class="text-sage-light font-medium">Apothecary Concierge:</span> {{ $site['contact']['phone'] }}</p>
                        <p><span class="text-sage-light font-medium">Email Dispatch:</span> {{ $site['contact']['email'] }}</p>
                        <p><span class="text-sage-light font-medium">Hours:</span> {{ $site['contact']['hours'] }}</p>
                    </div>

                    <!-- Social Icons (Instruction 8: Social icons instead of text links) -->
                    <div class="flex items-center space-x-3 pt-3">
                        <!-- Instagram -->
                        <a 
                            href="{{ $site['socials'][0]['url'] ?? 'https://instagram.com' }}" 
                            target="_blank" 
                            rel="noopener noreferrer" 
                            class="w-9 h-9 rounded-full border border-sage/40 flex items-center justify-center text-ivory hover:text-forest hover:bg-sage-light transition-colors focus-visible:outline-none focus-visible:ring-2 focus-visible:ring-sage"
                            aria-label="Follow The Everbloom on Instagram"
                        >
                            <svg class="w-4 h-4 fill-current" viewBox="0 0 24 24" aria-hidden="true">
                                <path d="M12 2.163c3.204 0 3.584.012 4.85.07 3.252.148 4.771 1.691 4.919 4.919.058 1.265.069 1.645.069 4.849 0 3.205-.012 3.584-.069 4.849-.149 3.225-1.664 4.771-4.919 4.919-1.266.058-1.644.07-4.85.07-3.204 0-3.584-.012-4.849-.07-3.26-.149-4.771-1.699-4.919-4.92-.058-1.265-.07-1.644-.07-4.849 0-3.204.013-3.583.07-4.849.149-3.227 1.664-4.771 4.919-4.919 1.266-.057 1.645-.069 4.849-.069zm0-2.163c-3.259 0-3.667.014-4.947.072-4.358.2-6.78 2.618-6.98 6.98-.059 1.281-.073 1.689-.073 4.948 0 3.259.014 3.668.072 4.948.2 4.358 2.618 6.78 6.98 6.98 1.281.058 1.689.072 4.948.072 3.259 0 3.668-.014 4.948-.072 4.354-.2 6.782-2.618 6.979-6.98.059-1.28.073-1.689.073-4.948 0-3.259-.014-3.667-.072-4.947-.196-4.354-2.617-6.78-6.979-6.98-1.281-.059-1.69-.073-4.949-.073zm0 5.838c-3.403 0-6.162 2.759-6.162 6.162s2.759 6.163 6.162 6.163 6.162-2.759 6.162-6.163c0-3.403-2.759-6.162-6.162-6.162zm0 10.162c-2.209 0-4-1.79-4-4 0-2.209 1.791-4 4-4s4 1.791 4 4c0 2.21-1.791 4-4 4zm6.406-11.845c-.796 0-1.441.645-1.441 1.44s.645 1.44 1.441 1.44c.795 0 1.439-.645 1.439-1.44s-.644-1.44-1.439-1.44z"/>
                            </svg>
                        </a>

                        <!-- Pinterest -->
                        <a 
                            href="{{ $site['socials'][1]['url'] ?? 'https://pinterest.com' }}" 
                            target="_blank" 
                            rel="noopener noreferrer" 
                            class="w-9 h-9 rounded-full border border-sage/40 flex items-center justify-center text-ivory hover:text-forest hover:bg-sage-light transition-colors focus-visible:outline-none focus-visible:ring-2 focus-visible:ring-sage"
                            aria-label="Follow The Everbloom on Pinterest"
                        >
                            <svg class="w-4 h-4 fill-current" viewBox="0 0 24 24" aria-hidden="true">
                                <path d="M12 0c-6.627 0-12 5.372-12 12 0 5.084 3.163 9.426 7.627 11.174-.105-.949-.2-2.405.042-3.441.218-.937 1.407-5.965 1.407-5.965s-.359-.719-.359-1.782c0-1.668.967-2.914 2.171-2.914 1.023 0 1.518.769 1.518 1.69 0 1.029-.655 2.568-.994 3.995-.283 1.194.599 2.169 1.777 2.169 2.133 0 3.772-2.249 3.772-5.495 0-2.873-2.064-4.882-5.012-4.882-3.414 0-5.418 2.561-5.418 5.207 0 1.031.397 2.138.893 2.738.098.119.112.224.083.345-.09.375-.291 1.199-.334 1.373-.056.23-.186.279-.429.168-1.604-.746-2.607-3.088-2.607-4.972 0-4.048 2.941-7.766 8.484-7.766 4.455 0 7.917 3.175 7.917 7.417 0 4.428-2.791 7.991-6.664 7.991-1.301 0-2.525-.676-2.944-1.474l-.801 3.052c-.29 1.109-1.076 2.499-1.603 3.355 1.236.381 2.553.589 3.916.589 6.627 0 12-5.373 12-12 0-6.628-5.373-12-12-12z"/>
                            </svg>
                        </a>

                        <!-- Facebook -->
                        <a 
                            href="{{ $site['socials'][2]['url'] ?? 'https://facebook.com' }}" 
                            target="_blank" 
                            rel="noopener noreferrer" 
                            class="w-9 h-9 rounded-full border border-sage/40 flex items-center justify-center text-ivory hover:text-forest hover:bg-sage-light transition-colors focus-visible:outline-none focus-visible:ring-2 focus-visible:ring-sage"
                            aria-label="Follow The Everbloom on Facebook"
                        >
                            <svg class="w-4 h-4 fill-current" viewBox="0 0 24 24" aria-hidden="true">
                                <path d="M24 12.073c0-6.627-5.373-12-12-12s-12 5.373-12 12c0 5.99 4.388 10.954 10.125 11.854v-8.385H7.078v-3.47h3.047V9.43c0-3.007 1.792-4.669 4.533-4.669 1.312 0 2.686.235 2.686.235v2.953H15.83c-1.491 0-1.956.925-1.956 1.874v2.25h3.328l-.532 3.47h-2.796v8.385C19.612 23.027 24 18.062 24 12.073z"/>
                            </svg>
                        </a>
                    </div>
                </div>

                <!-- Shop Column (Min 13px links) -->
                <div>
                    <h3 class="font-serif text-base text-ivory font-medium tracking-wide mb-4">
                        {{ $footerNav['shop']['title'] ?? 'Botanical Offerings' }}
                    </h3>
                    <ul class="space-y-3 text-[13px] text-ivory/85">
                        @foreach($footerNav['shop']['links'] ?? [] as $link)
                            <li>
                                <a href="{{ $link['url'] }}" class="hover:text-sage-light transition-colors">
                                    {{ $link['label'] }}
                                </a>
                            </li>
                        @endforeach
                    </ul>
                </div>

                <!-- House Column (Min 13px links) -->
                <div>
                    <h3 class="font-serif text-base text-ivory font-medium tracking-wide mb-4">
                        {{ $footerNav['about']['title'] ?? 'The House' }}
                    </h3>
                    <ul class="space-y-3 text-[13px] text-ivory/85">
                        @foreach($footerNav['about']['links'] ?? [] as $link)
                            <li>
                                <a href="{{ $link['url'] }}" class="hover:text-sage-light transition-colors">
                                    {{ $link['label'] }}
                                </a>
                            </li>
                        @endforeach
                    </ul>
                </div>

                <!-- Concierge Column (Min 13px links) -->
                <div>
                    <h3 class="font-serif text-base text-ivory font-medium tracking-wide mb-4">
                        {{ $footerNav['help']['title'] ?? 'Concierge & Care' }}
                    </h3>
                    <ul class="space-y-3 text-[13px] text-ivory/85">
                        @foreach($footerNav['help']['links'] ?? [] as $link)
                            <li>
                                <a href="{{ $link['url'] }}" class="hover:text-sage-light transition-colors">
                                    {{ $link['label'] }}
                                </a>
                            </li>
                        @endforeach
                    </ul>
                </div>

            </div>
        </div>

        <!-- Bottom Bar: Generic Payment Icons & Legal Notice -->
        <div class="border-t border-white/10 py-8 bg-[#253B2F] text-xs text-ivory/80">
            <div class="site-container flex flex-col md:flex-row items-center justify-between gap-6">
                <!-- Copyright & Currency -->
                <div class="flex flex-wrap items-center gap-4 text-xs uppercase tracking-wider text-ivory/75">
                    <span>© {{ date('Y') }} THE EVERBLOOM. ALL RIGHTS RESERVED.</span>
                    <span class="hidden sm:inline">·</span>
                    <span>CURRENCY: {{ $site['currency']['code'] }} ({{ $site['currency']['symbol'] }})</span>
                </div>

                <!-- Generic Payment Icons (Instruction 8: No brand logos required) -->
                <div class="flex items-center gap-2" aria-label="Supported Payment Methods">
                    <!-- Generic Card 1 -->
                    <div class="px-2.5 py-1.5 rounded-none bg-ivory/10 border border-white/20 text-ivory flex items-center justify-center" title="Credit / Debit Card">
                        <svg class="w-6 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24" aria-hidden="true">
                            <rect x="2" y="5" width="20" height="14" rx="2" stroke-width="1.5" />
                            <line x1="2" y1="10" x2="22" y2="10" stroke-width="1.5" />
                        </svg>
                    </div>

                    <!-- Generic Chip Card -->
                    <div class="px-2.5 py-1.5 rounded-none bg-ivory/10 border border-white/20 text-ivory flex items-center justify-center" title="EMV Chip Card">
                        <svg class="w-6 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24" aria-hidden="true">
                            <rect x="2" y="5" width="20" height="14" rx="2" stroke-width="1.5" />
                            <rect x="5" y="9" width="4" height="4" stroke-width="1.25" />
                        </svg>
                    </div>

                    <!-- Generic Contactless Payment -->
                    <div class="px-2.5 py-1.5 rounded-none bg-ivory/10 border border-white/20 text-ivory flex items-center justify-center" title="Contactless Payment">
                        <svg class="w-6 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24" aria-hidden="true">
                            <path stroke-linecap="round" stroke-width="1.5" d="M8.5 8.5a5 5 0 017 7M6 6a8.5 8.5 0 0112 12" />
                        </svg>
                    </div>

                    <!-- Generic Encrypted Shield Checkout -->
                    <div class="px-2.5 py-1.5 rounded-none bg-ivory/10 border border-white/20 text-ivory flex items-center justify-center" title="256-Bit Encrypted Checkout">
                        <svg class="w-6 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24" aria-hidden="true">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M12 15v2m-6 4h12a2 2 0 002-2v-6a2 2 0 00-2-2H6a2 2 0 00-2 2v6a2 2 0 002 2zm10-10V7a4 4 0 00-8 0v4h8z" />
                        </svg>
                    </div>
                </div>

                <!-- Legal Policy Links (Min 13px) -->
                <div class="flex flex-wrap items-center gap-5 text-[13px] text-ivory/85">
                    @foreach($footerNav['legal']['links'] ?? [] as $link)
                        <a href="{{ $link['url'] }}" class="hover:text-sage-light transition-colors">
                            {{ $link['label'] }}
                        </a>
                    @endforeach
                </div>
            </div>
        </div>
    </div>
</footer>
