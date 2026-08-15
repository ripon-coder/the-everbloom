<footer class="bg-slate-950 text-slate-300 pt-12 pb-8 border-t border-slate-800 mt-12">
    <div class="max-w-[1400px] mx-auto px-4 sm:px-6 lg:px-8">

        <!-- Top Feature Assurance Banner -->
        <div class="grid grid-cols-2 md:grid-cols-4 gap-4 pb-10 border-b border-slate-800">
            <div class="flex items-center gap-3 p-3.5 bg-slate-900/60 border border-slate-800">
                <svg class="w-6 h-6 text-primary flex-shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.8" d="M5 8h14M5 8a2 2 0 110-4h14a2 2 0 110 4M5 8v10a2 2 0 002 2h10a2 2 0 002-2V8m-9 4h4" />
                </svg>
                <div>
                    <h4 class="font-bold text-white uppercase text-xs sm:text-sm tracking-wide">Free Delivery</h4>
                    <p class="text-slate-400 text-xs mt-0.5">On eligible orders</p>
                </div>
            </div>
            <div class="flex items-center gap-3 p-3.5 bg-slate-900/60 border border-slate-800">
                <svg class="w-6 h-6 text-primary flex-shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.8" d="M9 12l2 2 4-4m5.618-4.016A11.955 11.955 0 0112 2.944a11.955 11.955 0 01-8.618 3.04A12.02 12.02 0 003 9c0 5.591 3.824 10.29 9 11.622 5.176-1.332 9-6.03 9-11.622 0-1.042-.133-2.052-.382-3.016z" />
                </svg>
                <div>
                    <h4 class="font-bold text-white uppercase text-xs sm:text-sm tracking-wide">100% Genuine</h4>
                    <p class="text-slate-400 text-xs mt-0.5">Authentic products</p>
                </div>
            </div>
            <div class="flex items-center gap-3 p-3.5 bg-slate-900/60 border border-slate-800">
                <svg class="w-6 h-6 text-primary flex-shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.8" d="M12 15v2m-6 4h12a2 2 0 002-2v-6a2 2 0 00-2-2H6a2 2 0 00-2 2v6a2 2 0 002 2zm10-10V7a4 4 0 00-8 0v4h8z" />
                </svg>
                <div>
                    <h4 class="font-bold text-white uppercase text-xs sm:text-sm tracking-wide">Safe Checkout</h4>
                    <p class="text-slate-400 text-xs mt-0.5">Encrypted payment</p>
                </div>
            </div>
            <div class="flex items-center gap-3 p-3.5 bg-slate-900/60 border border-slate-800">
                <svg class="w-6 h-6 text-primary flex-shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.8" d="M18.364 5.636l-3.536 3.536m0 5.656l3.536 3.536M9.172 9.172L5.636 5.636m3.536 9.192l-3.536 3.536M21 12a9 9 0 11-18 0 9 9 0 0118 0zm-5 0a4 4 0 11-8 0 4 4 0 018 0z" />
                </svg>
                <div>
                    <h4 class="font-bold text-white uppercase text-xs sm:text-sm tracking-wide">24/7 Support</h4>
                    <p class="text-slate-400 text-xs mt-0.5">Dedicated customer help</p>
                </div>
            </div>
        </div>

        <!-- Main Footer Links Grid -->
        <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-4 gap-8 py-10">

            <!-- Column 1: Brand & Social -->
            <div class="space-y-4">
                <div class="flex items-center gap-2">
                    <span class="text-base md:text-lg font-bold text-white tracking-wider uppercase">{{ $site_setting->site_name ?? 'Feriwalarhat' }}</span>
                </div>
                <p class="text-xs sm:text-sm text-slate-400 leading-relaxed">
                    {{ $site_setting->footer_text ?? 'Your trusted destination for premium gadgets, accessories, and tech products in Bangladesh.' }}
                </p>

                <!-- Social Icons -->
                <div class="flex items-center gap-2 pt-2">
                    @if($site_setting && $site_setting->facebook_url)
                        <a href="{{ $site_setting->facebook_url }}" target="_blank" class="w-8 h-8 bg-slate-900 border border-slate-800 flex items-center justify-center text-slate-400 hover:text-white hover:bg-primary hover:border-primary transition-colors" title="Facebook">
                            <svg class="w-4 h-4" fill="currentColor" viewBox="0 0 24 24"><path d="M24 12.073c0-6.627-5.373-12-12-12s-12 5.373-12 12c0 5.99 4.388 10.954 10.125 11.854v-8.385H7.078v-3.469h3.047V9.43c0-3.007 1.792-4.669 4.533-4.669 1.312 0 2.686.235 2.686.235v2.953H15.83c-1.491 0-1.956.925-1.956 1.874v2.25h3.328l-.532 3.469h-2.796v8.385C19.612 23.027 24 18.062 24 12.073z"/></svg>
                        </a>
                    @endif
                    @if($site_setting && $site_setting->instagram_url)
                        <a href="{{ $site_setting->instagram_url }}" target="_blank" class="w-8 h-8 bg-slate-900 border border-slate-800 flex items-center justify-center text-slate-400 hover:text-white hover:bg-primary hover:border-primary transition-colors" title="Instagram">
                            <svg class="w-4 h-4" fill="currentColor" viewBox="0 0 24 24"><path d="M12 2.163c3.204 0 3.584.012 4.85.07 3.252.148 4.771 1.691 4.919 4.919.058 1.265.069 1.645.069 4.849 0 3.205-.012 3.584-.069 4.849-.149 3.225-1.664 4.771-4.919 4.919-1.266.058-1.644.07-4.85.07-3.204 0-3.584-.012-4.849-.07-3.26-.149-4.771-1.699-4.919-4.92-.058-1.265-.07-1.644-.07-4.849 0-3.204.013-3.583.07-4.849.149-3.227 1.664-4.771 4.919-4.919 1.266-.057 1.645-.069 4.849-.069zM12 0C8.741 0 8.333.014 7.053.072 2.695.272.273 2.69.073 7.052.014 8.333 0 8.741 0 12c0 3.259.014 3.668.072 4.948.2 4.358 2.618 6.78 6.98 6.98C8.333 23.986 8.741 24 12 24c3.259 0 3.668-.014 4.948-.072 4.354-.2 6.782-2.618 6.979-6.98.059-1.28.073-1.689.073-4.948 0-3.259-.014-3.667-.072-4.947-.196-4.354-2.617-6.78-6.979-6.98C15.668.014 15.259 0 12 0zm0 5.838a6.162 6.162 0 100 12.324 6.162 6.162 0 000-12.324zM12 16a4 4 0 110-8 4 4 0 010 8zm6.406-11.845a1.44 1.44 0 100 2.881 1.44 1.44 0 000-2.881z"/></svg>
                        </a>
                    @endif
                    @if($site_setting && $site_setting->youtube_url)
                        <a href="{{ $site_setting->youtube_url }}" target="_blank" class="w-8 h-8 bg-slate-900 border border-slate-800 flex items-center justify-center text-slate-400 hover:text-white hover:bg-primary hover:border-primary transition-colors" title="YouTube">
                            <svg class="w-4 h-4" fill="currentColor" viewBox="0 0 24 24"><path d="M23.498 6.186a3.016 3.016 0 0 0-2.122-2.136C19.505 3.545 12 3.545 12 3.545s-7.505 0-9.377.505A3.017 3.017 0 0 0 .502 6.186C0 8.07 0 12 0 12s0 3.93.502 5.814a3.016 3.016 0 0 0 2.122 2.136c1.871.505 9.376.505 9.376.505s7.505 0 9.377-.505a3.015 3.015 0 0 0 2.122-2.136C24 15.93 24 12 24 12s0-3.93-.502-5.814zM9.545 15.568V8.432L15.818 12l-6.273 3.568z"/></svg>
                        </a>
                    @endif
                    @if($site_setting && $site_setting->twitter_url)
                        <a href="{{ $site_setting->twitter_url }}" target="_blank" class="w-8 h-8 bg-slate-900 border border-slate-800 flex items-center justify-center text-slate-400 hover:text-white hover:bg-primary hover:border-primary transition-colors" title="Twitter">
                            <svg class="w-4 h-4" fill="currentColor" viewBox="0 0 24 24"><path d="M23.953 4.57a10 10 0 01-2.825.775 4.958 4.958 0 002.163-2.723c-.951.555-2.005.959-3.127 1.184a4.92 4.92 0 00-8.384 4.482C7.69 8.095 4.067 6.13 1.64 3.162a4.822 4.822 0 00-.666 2.475c0 1.71.87 3.213 2.188 4.096a4.904 4.904 0 01-2.228-.616v.06a4.923 4.923 0 003.946 4.827 4.996 4.996 0 01-2.212.085 4.936 4.936 0 004.604 3.417 9.867 9.867 0 01-6.102 2.105c-.39 0-.779-.023-1.17-.067a13.995 13.995 0 007.557 2.209c9.053 0 13.998-7.496 13.998-13.985 0-.21 0-.42-.015-.63A9.935 9.935 0 0024 4.59z"/></svg>
                        </a>
                    @endif
                </div>
            </div>

            <!-- Column 2: Quick Links -->
            <div>
                <h3 class="text-white font-bold uppercase tracking-wider text-xs sm:text-sm mb-4">Quick Links</h3>
                <ul class="space-y-2.5 text-xs sm:text-sm">
                    @php
                        $policySlugs = ['privacy_policy', 'return_policy', 'refund_policy', 'cookie_policy', 'accessibility', 'terms_conditions'];
                        $quickLinks = $active_pages->reject(fn($p) => in_array($p->slug, $policySlugs));
                        $policyLinks = $active_pages->filter(fn($p) => in_array($p->slug, $policySlugs));
                    @endphp

                    @foreach($quickLinks as $page)
                        <li>
                            <a href="{{ route('page.show', $page->slug) }}" class="text-slate-400 hover:text-primary transition-colors flex items-center gap-1.5">
                                <span class="text-slate-600">›</span>
                                <span>{{ $page->title }}</span>
                            </a>
                        </li>
                    @endforeach
                    <li>
                        <a href="{{ route('shop') }}" class="text-slate-400 hover:text-primary transition-colors flex items-center gap-1.5">
                            <span class="text-slate-600">›</span>
                            <span>All Products</span>
                        </a>
                    </li>
                    <li>
                        <a href="{{ route('contact') }}" class="text-slate-400 hover:text-primary transition-colors flex items-center gap-1.5">
                            <span class="text-slate-600">›</span>
                            <span>Contact Us</span>
                        </a>
                    </li>
                </ul>
            </div>

            <!-- Column 3: Customer Policies -->
            <div>
                <h3 class="text-white font-bold uppercase tracking-wider text-xs sm:text-sm mb-4">Customer Care</h3>
                <ul class="space-y-2.5 text-xs sm:text-sm">
                    @foreach($policyLinks as $page)
                        <li>
                            <a href="{{ route('page.show', $page->slug) }}" class="text-slate-400 hover:text-primary transition-colors flex items-center gap-1.5">
                                <span class="text-slate-600">›</span>
                                <span>{{ $page->title }}</span>
                            </a>
                        </li>
                    @endforeach
                    @if($policyLinks->isEmpty())
                        <li>
                            <a href="#" class="text-slate-400 hover:text-primary transition-colors flex items-center gap-1.5">
                                <span class="text-slate-600">›</span>
                                <span>Privacy Policy</span>
                            </a>
                        </li>
                        <li>
                            <a href="#" class="text-slate-400 hover:text-primary transition-colors flex items-center gap-1.5">
                                <span class="text-slate-600">›</span>
                                <span>Return & Refund Policy</span>
                            </a>
                        </li>
                        <li>
                            <a href="#" class="text-slate-400 hover:text-primary transition-colors flex items-center gap-1.5">
                                <span class="text-slate-600">›</span>
                                <span>Terms & Conditions</span>
                            </a>
                        </li>
                    @endif
                </ul>
            </div>

            <!-- Column 4: Contact & Info -->
            <div class="space-y-3">
                <h3 class="text-white font-bold uppercase tracking-wider text-xs sm:text-sm mb-4">Contact Info</h3>
                
                <div class="flex items-start gap-3 text-xs sm:text-sm text-slate-400">
                    <svg class="w-4 h-4 text-primary flex-shrink-0 mt-0.5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.8" d="M17.657 16.657L13.414 20.9a1.998 1.998 0 01-2.827 0l-4.244-4.243a8 8 0 1111.314 0z"/>
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.8" d="M15 11a3 3 0 11-6 0 3 3 0 016 0z"/>
                    </svg>
                    <span>{{ $site_setting->site_address ?? 'Dhaka, Bangladesh' }}</span>
                </div>

                <div class="flex items-center gap-3 text-xs sm:text-sm text-slate-400">
                    <svg class="w-4 h-4 text-primary flex-shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.8" d="M3 5a2 2 0 012-2h3.28a1 1 0 01.948.684l1.498 4.493a1 1 0 01-.502 1.21l-2.257 1.13a11.042 11.042 0 005.516 5.516l1.13-2.257a1 1 0 011.21-.502l4.493 1.498a1 1 0 01.684.949V19a2 2 0 01-2 2h-1C9.716 21 3 14.284 3 6V5z"/>
                    </svg>
                    <a href="tel:{{ $site_setting->site_phone ?? '+8801720000000' }}" class="hover:text-primary transition-colors">{{ $site_setting->site_phone ?? '+880 1720 000000' }}</a>
                </div>

                <div class="flex items-center gap-3 text-xs sm:text-sm text-slate-400">
                    <svg class="w-4 h-4 text-primary flex-shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.8" d="M3 8l7.89 5.26a2 2 0 002.22 0L21 8M5 19h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v10a2 2 0 002 2z"/>
                    </svg>
                    <a href="mailto:{{ $site_setting->site_email ?? 'info@feriwalarhat.com' }}" class="hover:text-primary transition-colors">{{ $site_setting->site_email ?? 'info@feriwalarhat.com' }}</a>
                </div>
            </div>

        </div>

        <!-- Bottom Copyright Bar -->
        <div class="pt-6 border-t border-slate-900 flex flex-col md:flex-row items-center justify-between gap-3 text-xs sm:text-sm text-slate-400">
            <p>© {{ date('Y') }} {{ $site_setting->site_name ?? 'Feriwalarhat' }}. All rights reserved.</p>
            <p>Design & Developed by <a href="#" class="text-slate-300 hover:text-primary font-medium transition-colors">{{ $site_setting->site_name ?? 'Feriwalarhat' }}</a></p>
        </div>

    </div>
</footer>
