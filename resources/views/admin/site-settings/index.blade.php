@extends('admin.layouts.app')

@section('title', 'Site Settings')

@section('content')
    <div class="space-y-6">
        <!-- Single Unified Settings Card (Sharp / Non-rounded) -->
        <div class="bg-white border border-gray-200 shadow-sm" x-data="{
            activeTab: 'general',
            previewImage(input, previewId) {
                if (input.files && input.files[0]) {
                    const reader = new FileReader();
                    reader.onload = (e) => {
                        document.getElementById(previewId).src = e.target.result;
                    };
                    reader.readAsDataURL(input.files[0]);
                }
            }
        }">
            
            <!-- Card Top Header -->
            <div class="p-5 sm:p-6 bg-gradient-to-r from-gray-50/80 via-white to-gray-50/50 border-b border-gray-200">
                <div class="flex flex-col sm:flex-row sm:items-center justify-between gap-4">
                    <div>
                        <!-- Breadcrumb -->
                        <nav class="flex items-center space-x-2 text-xs text-gray-500 mb-1.5">
                            <span class="text-gray-700 font-semibold">Settings</span>
                            <span class="text-gray-300">/</span>
                            <span>Website Configuration</span>
                        </nav>

                        <div class="flex flex-wrap items-center gap-3">
                            <h1 class="text-2xl font-bold text-gray-900 tracking-tight">Site Settings</h1>
                            <span class="inline-flex items-center px-2 py-0.5 text-xs font-mono font-semibold bg-gray-100 text-gray-700 border border-gray-200">
                                Global Configuration
                            </span>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Form -->
            <form action="{{ route('admin.site-settings.update') }}" method="POST" enctype="multipart/form-data">
                @csrf

                <!-- Tab Navigation Bar -->
                <div class="border-b border-gray-200 bg-gray-50/70 px-5 sm:px-6">
                    <div class="flex space-x-1 sm:space-x-4">
                        <button type="button" 
                                @click="activeTab = 'general'"
                                :class="activeTab === 'general' ? 'border-blue-600 text-blue-600 bg-white' : 'border-transparent text-gray-600 hover:text-gray-900'"
                                class="py-3 px-4 text-xs font-bold uppercase tracking-wider border-b-2 transition-all flex items-center gap-2">
                            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 21V5a2 2 0 00-2-2H7a2 2 0 00-2 2v16m14 0h2m-2 0h-5m-9 0H3m2 0h5M9 7h1m-1 4h1m4-4h1m-1 4h1m-5 10v-5a1 1 0 011-1h2a1 1 0 011 1v5m-4 0h4"></path>
                            </svg>
                            General & Branding
                        </button>

                        <button type="button" 
                                @click="activeTab = 'social'"
                                :class="activeTab === 'social' ? 'border-blue-600 text-blue-600 bg-white' : 'border-transparent text-gray-600 hover:text-gray-900'"
                                class="py-3 px-4 text-xs font-bold uppercase tracking-wider border-b-2 transition-all flex items-center gap-2">
                            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13.828 10.172a4 4 0 00-5.656 0l-4 4a4 4 0 105.656 5.656l1.102-1.101m-.758-4.899a4 4 0 005.656 0l4-4a4 4 0 00-5.656-5.656l-1.1 1.1"></path>
                            </svg>
                            Social Channels
                        </button>

                        <button type="button" 
                                @click="activeTab = 'seo'"
                                :class="activeTab === 'seo' ? 'border-blue-600 text-blue-600 bg-white' : 'border-transparent text-gray-600 hover:text-gray-900'"
                                class="py-3 px-4 text-xs font-bold uppercase tracking-wider border-b-2 transition-all flex items-center gap-2">
                            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z"></path>
                            </svg>
                            SEO & Footer
                        </button>
                    </div>
                </div>

                <div class="p-5 sm:p-6">
                    
                    <!-- TAB 1: General & Branding -->
                    <div x-show="activeTab === 'general'" class="space-y-6">
                        
                        <!-- Basic Info -->
                        <div>
                            <h2 class="text-xs font-bold uppercase tracking-wider text-gray-700 pb-2 mb-4 border-b border-gray-200">
                                Store Information
                            </h2>

                            <div class="grid grid-cols-1 md:grid-cols-3 gap-5">
                                <div>
                                    <label for="site_name" class="block text-xs font-semibold text-gray-700 uppercase tracking-wider mb-1.5">
                                        Website Name
                                    </label>
                                    <input type="text" name="site_name" id="site_name" 
                                           value="{{ old('site_name', $setting->site_name ?? '') }}"
                                           placeholder="e.g., The Everbloom"
                                           class="w-full px-3 py-2 text-xs border border-gray-300 bg-white focus:ring-1 focus:ring-blue-600 focus:border-blue-600">
                                </div>

                                <div>
                                    <label for="site_email" class="block text-xs font-semibold text-gray-700 uppercase tracking-wider mb-1.5">
                                        Support / Contact Email
                                    </label>
                                    <input type="email" name="site_email" id="site_email" 
                                           value="{{ old('site_email', $setting->site_email ?? '') }}"
                                           placeholder="support@example.com"
                                           class="w-full px-3 py-2 text-xs border border-gray-300 bg-white focus:ring-1 focus:ring-blue-600 focus:border-blue-600">
                                </div>

                                <div>
                                    <label for="site_phone" class="block text-xs font-semibold text-gray-700 uppercase tracking-wider mb-1.5">
                                        Contact Phone Number
                                    </label>
                                    <input type="text" name="site_phone" id="site_phone" 
                                           value="{{ old('site_phone', $setting->site_phone ?? '') }}"
                                           placeholder="+1 (555) 000-0000"
                                           class="w-full px-3 py-2 text-xs border border-gray-300 bg-white focus:ring-1 focus:ring-blue-600 focus:border-blue-600">
                                </div>
                            </div>

                            <div class="mt-5">
                                <label for="site_address" class="block text-xs font-semibold text-gray-700 uppercase tracking-wider mb-1.5">
                                    Physical Address / Headquarters
                                </label>
                                <textarea name="site_address" id="site_address" rows="2"
                                          placeholder="Full business or warehouse address..."
                                          class="w-full px-3 py-2 text-xs border border-gray-300 bg-white focus:ring-1 focus:ring-blue-600 focus:border-blue-600">{{ old('site_address', $setting->site_address ?? '') }}</textarea>
                            </div>
                        </div>

                        <!-- Branding & Assets -->
                        <div class="pt-4 border-t border-gray-200">
                            <h2 class="text-xs font-bold uppercase tracking-wider text-gray-700 pb-2 mb-4 border-b border-gray-200">
                                Visual Branding & Assets
                            </h2>

                            <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                                <!-- Logo -->
                                <div class="p-4 border border-gray-200 bg-gray-50/50 space-y-3">
                                    <div class="flex items-center justify-between">
                                        <span class="text-xs font-bold text-gray-800 uppercase tracking-wider">Website Header Logo</span>
                                        <span class="text-[10px] text-gray-500">Rec: 200x50 px, Max: 2MB</span>
                                    </div>

                                    <div class="flex items-center space-x-4">
                                        <div class="w-36 h-16 bg-white border border-gray-300 flex items-center justify-center p-2 overflow-hidden flex-shrink-0">
                                            <img id="logo-preview" 
                                                 src="{{ $setting ? $setting->getLogoUrl() : asset('images/default-logo.png') }}" 
                                                 alt="Logo" class="max-w-full max-h-full object-contain">
                                        </div>

                                        <div class="flex-1">
                                            <input type="file" name="site_logo" id="site_logo" accept="image/*" 
                                                   @change="previewImage($event.target, 'logo-preview')"
                                                   class="block w-full text-xs text-gray-500 file:mr-2 file:py-1 file:px-2.5 file:border-0 file:text-xs file:font-semibold file:bg-gray-800 file:text-white hover:file:bg-black cursor-pointer">
                                            @error('site_logo')
                                                <p class="mt-1 text-[11px] text-rose-600 font-medium">{{ $message }}</p>
                                            @enderror
                                        </div>
                                    </div>
                                </div>

                                <!-- Favicon -->
                                <div class="p-4 border border-gray-200 bg-gray-50/50 space-y-3">
                                    <div class="flex items-center justify-between">
                                        <span class="text-xs font-bold text-gray-800 uppercase tracking-wider">Browser Favicon</span>
                                        <span class="text-[10px] text-gray-500">ICO / PNG / SVG, Max: 1MB</span>
                                    </div>

                                    <div class="flex items-center space-x-4">
                                        <div class="w-16 h-16 bg-white border border-gray-300 flex items-center justify-center p-2 overflow-hidden flex-shrink-0">
                                            <img id="favicon-preview" 
                                                 src="{{ $setting ? $setting->getFaviconUrl() : asset('images/default-logo.png') }}" 
                                                 alt="Favicon" class="w-8 h-8 object-contain">
                                        </div>

                                        <div class="flex-1">
                                            <input type="file" name="site_favicon" id="site_favicon" accept="image/*,.ico" 
                                                   @change="previewImage($event.target, 'favicon-preview')"
                                                   class="block w-full text-xs text-gray-500 file:mr-2 file:py-1 file:px-2.5 file:border-0 file:text-xs file:font-semibold file:bg-gray-800 file:text-white hover:file:bg-black cursor-pointer">
                                            @error('site_favicon')
                                                <p class="mt-1 text-[11px] text-rose-600 font-medium">{{ $message }}</p>
                                            @enderror
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>

                    </div>

                    <!-- TAB 2: Social Media -->
                    <div x-show="activeTab === 'social'" class="space-y-6">
                        <div>
                            <h2 class="text-xs font-bold uppercase tracking-wider text-gray-700 pb-2 mb-4 border-b border-gray-200">
                                Social Media Profiles & Channels
                            </h2>

                            <div class="grid grid-cols-1 md:grid-cols-2 gap-5">
                                <!-- Facebook -->
                                <div>
                                    <label for="facebook_url" class="block text-xs font-semibold text-gray-700 uppercase tracking-wider mb-1.5">
                                        Facebook Page URL
                                    </label>
                                    <div class="relative">
                                        <div class="absolute inset-y-0 left-0 pl-3 flex items-center pointer-events-none text-blue-600">
                                            <svg class="w-4 h-4" fill="currentColor" viewBox="0 0 24 24"><path d="M24 12.073c0-6.627-5.373-12-12-12s-12 5.373-12 12c0 5.99 4.388 10.954 10.125 11.854v-8.385H7.078v-3.47h3.047V9.43c0-3.007 1.792-4.669 4.533-4.669 1.312 0 2.686.235 2.686.235v2.953H15.83c-1.491 0-1.956.925-1.956 1.874v2.25h3.328l-.532 3.47h-2.796v8.385C19.612 23.027 24 18.062 24 12.073z"/></svg>
                                        </div>
                                        <input type="url" name="facebook_url" id="facebook_url" 
                                               value="{{ old('facebook_url', $setting->facebook_url ?? '') }}"
                                               placeholder="https://facebook.com/yourpage"
                                               class="w-full pl-9 pr-3 py-2 text-xs font-mono border border-gray-300 bg-white focus:ring-1 focus:ring-blue-600 focus:border-blue-600">
                                    </div>
                                </div>

                                <!-- Twitter / X -->
                                <div>
                                    <label for="twitter_url" class="block text-xs font-semibold text-gray-700 uppercase tracking-wider mb-1.5">
                                        Twitter / X Profile URL
                                    </label>
                                    <div class="relative">
                                        <div class="absolute inset-y-0 left-0 pl-3 flex items-center pointer-events-none text-gray-800">
                                            <svg class="w-4 h-4" fill="currentColor" viewBox="0 0 24 24"><path d="M18.244 2.25h3.308l-7.227 8.26 8.502 11.24H16.17l-5.214-6.817L4.99 24.75H1.68l7.73-8.835L1.254 2.25H8.08l4.713 6.231zm-1.161 17.52h1.833L7.084 4.126H5.117z"/></svg>
                                        </div>
                                        <input type="url" name="twitter_url" id="twitter_url" 
                                               value="{{ old('twitter_url', $setting->twitter_url ?? '') }}"
                                               placeholder="https://x.com/yourhandle"
                                               class="w-full pl-9 pr-3 py-2 text-xs font-mono border border-gray-300 bg-white focus:ring-1 focus:ring-blue-600 focus:border-blue-600">
                                    </div>
                                </div>

                                <!-- Instagram -->
                                <div>
                                    <label for="instagram_url" class="block text-xs font-semibold text-gray-700 uppercase tracking-wider mb-1.5">
                                        Instagram Profile URL
                                    </label>
                                    <div class="relative">
                                        <div class="absolute inset-y-0 left-0 pl-3 flex items-center pointer-events-none text-pink-600">
                                            <svg class="w-4 h-4" fill="currentColor" viewBox="0 0 24 24"><path d="M12 2.163c3.204 0 3.584.012 4.85.07 3.252.148 4.771 1.691 4.919 4.919.058 1.265.069 1.645.069 4.849 0 3.205-.012 3.584-.069 4.849-.149 3.225-1.664 4.771-4.919 4.919-1.266.058-1.644.07-4.85.07-3.204 0-3.584-.012-4.849-.07-3.26-.149-4.771-1.699-4.919-4.92-.058-1.265-.07-1.644-.07-4.849 0-3.204.013-3.583.07-4.849.149-3.227 1.664-4.771 4.919-4.919 1.266-.057 1.645-.069 4.849-.069zm0-2.163c-3.259 0-3.667.014-4.947.072-4.358.2-6.78 2.618-6.98 6.98-.059 1.281-.073 1.689-.073 4.948 0 3.259.014 3.668.072 4.948.2 4.358 2.618 6.78 6.98 6.98 1.281.058 1.689.072 4.948.072 3.259 0 3.668-.014 4.948-.072 4.354-.2 6.782-2.618 6.979-6.98.059-1.28.073-1.689.073-4.948 0-3.259-.014-3.667-.072-4.947-.196-4.354-2.617-6.78-6.979-6.98-1.281-.059-1.69-.073-4.949-.073zm0 5.838c-3.403 0-6.162 2.759-6.162 6.162s2.759 6.163 6.162 6.163 6.162-2.759 6.162-6.163c0-3.403-2.759-6.162-6.162-6.162zm0 10.162c-2.209 0-4-1.79-4-4 0-2.209 1.791-4 4-4s4 1.791 4 4c0 2.21-1.791 4-4 4zm6.406-11.845c-.796 0-1.441.645-1.441 1.44s.645 1.44 1.441 1.44c.795 0 1.439-.645 1.439-1.44s-.644-1.44-1.439-1.44z"/></svg>
                                        </div>
                                        <input type="url" name="instagram_url" id="instagram_url" 
                                               value="{{ old('instagram_url', $setting->instagram_url ?? '') }}"
                                               placeholder="https://instagram.com/yourhandle"
                                               class="w-full pl-9 pr-3 py-2 text-xs font-mono border border-gray-300 bg-white focus:ring-1 focus:ring-blue-600 focus:border-blue-600">
                                    </div>
                                </div>

                                <!-- LinkedIn -->
                                <div>
                                    <label for="linkedin_url" class="block text-xs font-semibold text-gray-700 uppercase tracking-wider mb-1.5">
                                        LinkedIn Page URL
                                    </label>
                                    <div class="relative">
                                        <div class="absolute inset-y-0 left-0 pl-3 flex items-center pointer-events-none text-sky-700">
                                            <svg class="w-4 h-4" fill="currentColor" viewBox="0 0 24 24"><path d="M19 0h-14c-2.761 0-5 2.239-5 5v14c0 2.761 2.239 5 5 5h14c2.762 0 5-2.239 5-5v-14c0-2.761-2.238-5-5-5zm-11 19h-3v-11h3v11zm-1.5-12.268c-.966 0-1.75-.79-1.75-1.764s.784-1.764 1.75-1.764 1.75.79 1.75 1.764-.783 1.764-1.75 1.764zm13.5 12.268h-3v-5.604c0-3.368-4-3.113-4 0v5.604h-3v-11h3v1.765c1.396-2.586 7-2.777 7 2.476v6.759z"/></svg>
                                        </div>
                                        <input type="url" name="linkedin_url" id="linkedin_url" 
                                               value="{{ old('linkedin_url', $setting->linkedin_url ?? '') }}"
                                               placeholder="https://linkedin.com/company/yourpage"
                                               class="w-full pl-9 pr-3 py-2 text-xs font-mono border border-gray-300 bg-white focus:ring-1 focus:ring-blue-600 focus:border-blue-600">
                                    </div>
                                </div>

                                <!-- YouTube -->
                                <div>
                                    <label for="youtube_url" class="block text-xs font-semibold text-gray-700 uppercase tracking-wider mb-1.5">
                                        YouTube Channel URL
                                    </label>
                                    <div class="relative">
                                        <div class="absolute inset-y-0 left-0 pl-3 flex items-center pointer-events-none text-red-600">
                                            <svg class="w-4 h-4" fill="currentColor" viewBox="0 0 24 24"><path d="M23.498 6.186a3.016 3.016 0 0 0-2.122-2.136C19.505 3.545 12 3.545 12 3.545s-7.505 0-9.377.505A3.017 3.017 0 0 0 .502 6.186C0 8.07 0 12 0 12s0 3.93.502 5.814a3.016 3.016 0 0 0 2.122 2.136c1.871.505 9.376.505 9.376.505s7.505 0 9.377-.505a3.015 3.015 0 0 0 2.122-2.136C24 15.93 24 12 24 12s0-3.93-.502-5.814zM9.545 15.568V8.432L15.818 12l-6.273 3.568z"/></svg>
                                        </div>
                                        <input type="url" name="youtube_url" id="youtube_url" 
                                               value="{{ old('youtube_url', $setting->youtube_url ?? '') }}"
                                               placeholder="https://youtube.com/@yourchannel"
                                               class="w-full pl-9 pr-3 py-2 text-xs font-mono border border-gray-300 bg-white focus:ring-1 focus:ring-blue-600 focus:border-blue-600">
                                    </div>
                                </div>

                                <!-- TikTok -->
                                <div>
                                    <label for="tiktok_url" class="block text-xs font-semibold text-gray-700 uppercase tracking-wider mb-1.5">
                                        TikTok Profile URL
                                    </label>
                                    <div class="relative">
                                        <div class="absolute inset-y-0 left-0 pl-3 flex items-center pointer-events-none text-slate-900">
                                            <svg class="w-4 h-4" fill="currentColor" viewBox="0 0 24 24"><path d="M12.525.02c1.31-.02 2.61-.01 3.91-.02.08 1.53.63 3.09 1.75 4.17 1.12 1.11 2.7 1.62 4.24 1.79v4.03c-1.44-.05-2.89-.35-4.2-.97-.57-.26-1.1-.59-1.62-.93-.01 2.92.01 5.84-.02 8.75-.08 1.4-.54 2.79-1.35 3.94-1.31 1.92-3.58 3.17-5.91 3.21-1.43.08-2.86-.31-4.08-1.03-2.02-1.19-3.44-3.37-3.65-5.71-.02-.5-.03-1-.01-1.49.18-1.9 1.12-3.72 2.58-4.96 1.66-1.44 3.98-2.13 6.15-1.72.02 1.48-.04 2.96-.04 4.44-.99-.32-2.15-.23-3.02.37-.82.56-1.34 1.52-1.4 2.52-.07.96.32 1.94 1.05 2.56.76.64 1.82.91 2.8.76 1.2-.18 2.24-.99 2.67-2.11.23-.62.33-1.28.33-1.94.01-4.48 0-8.96.01-13.44z"/></svg>
                                        </div>
                                        <input type="url" name="tiktok_url" id="tiktok_url" 
                                               value="{{ old('tiktok_url', $setting->tiktok_url ?? '') }}"
                                               placeholder="https://tiktok.com/@yourhandle"
                                               class="w-full pl-9 pr-3 py-2 text-xs font-mono border border-gray-300 bg-white focus:ring-1 focus:ring-blue-600 focus:border-blue-600">
                                    </div>
                                </div>

                                <!-- WhatsApp -->
                                <div>
                                    <label for="whatsapp_url" class="block text-xs font-semibold text-gray-700 uppercase tracking-wider mb-1.5">
                                        WhatsApp Link or Phone Number
                                    </label>
                                    <div class="relative">
                                        <div class="absolute inset-y-0 left-0 pl-3 flex items-center pointer-events-none text-emerald-600">
                                            <svg class="w-4 h-4" fill="currentColor" viewBox="0 0 24 24"><path d="M.057 24l1.687-6.163c-1.041-1.804-1.588-3.849-1.587-5.946.003-6.556 5.338-11.891 11.893-11.891 3.181.001 6.167 1.24 8.413 3.488 2.245 2.248 3.481 5.236 3.48 8.414-.003 6.557-5.338 11.892-11.893 11.892-1.99-.001-3.951-.5-5.688-1.448l-6.305 1.654zm6.597-3.807c1.676.995 3.276 1.591 5.392 1.592 5.448 0 9.886-4.434 9.889-9.885.002-5.462-4.415-9.89-9.881-9.892-5.452 0-9.887 4.434-9.889 9.884-.001 2.225.651 3.891 1.746 5.634l.399.638-1.06 3.874 3.968-1.041.636.395z"/></svg>
                                        </div>
                                        <input type="text" name="whatsapp_url" id="whatsapp_url" 
                                               value="{{ old('whatsapp_url', $setting->whatsapp_url ?? '') }}"
                                               placeholder="+8801700000000 or https://wa.me/8801700000000"
                                               class="w-full pl-9 pr-3 py-2 text-xs font-mono border border-gray-300 bg-white focus:ring-1 focus:ring-blue-600 focus:border-blue-600">
                                    </div>
                                </div>

                                <!-- Pinterest -->
                                <div>
                                    <label for="pinterest_url" class="block text-xs font-semibold text-gray-700 uppercase tracking-wider mb-1.5">
                                        Pinterest Profile URL
                                    </label>
                                    <div class="relative">
                                        <div class="absolute inset-y-0 left-0 pl-3 flex items-center pointer-events-none text-red-700">
                                            <svg class="w-4 h-4" fill="currentColor" viewBox="0 0 24 24"><path d="M12.017 0C5.396 0 .029 5.367.029 11.987c0 5.079 3.158 9.417 7.618 11.162-.105-.949-.199-2.403.041-3.439.219-.937 1.406-5.957 1.406-5.957s-.359-.72-.359-1.781c0-1.663.967-2.911 2.168-2.911 1.024 0 1.518.769 1.518 1.688 0 1.029-.653 2.567-.992 3.992-.285 1.193.6 2.165 1.775 2.165 2.128 0 3.768-2.245 3.768-5.487 0-2.861-2.063-4.869-5.008-4.869-3.41 0-5.409 2.562-5.409 5.199 0 1.033.394 2.143.889 2.741.099.12.112.225.085.345-.09.375-.293 1.199-.334 1.363-.053.225-.172.271-.401.165-1.495-.69-2.433-2.878-2.433-4.646 0-3.776 2.748-7.252 7.92-7.252 4.158 0 7.392 2.967 7.392 6.923 0 4.135-2.607 7.462-6.233 7.462-1.214 0-2.354-.629-2.758-1.379l-.749 2.848c-.269 1.045-1.004 2.352-1.498 3.146 1.123.345 2.306.535 3.55.535 6.607 0 11.985-5.365 11.985-11.987C24.007 5.367 18.624 0 12.017 0z"/></svg>
                                        </div>
                                        <input type="url" name="pinterest_url" id="pinterest_url" 
                                               value="{{ old('pinterest_url', $setting->pinterest_url ?? '') }}"
                                               placeholder="https://pinterest.com/yourhandle"
                                               class="w-full pl-9 pr-3 py-2 text-xs font-mono border border-gray-300 bg-white focus:ring-1 focus:ring-blue-600 focus:border-blue-600">
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>

                    <!-- TAB 3: SEO & Footer -->
                    <div x-show="activeTab === 'seo'" class="space-y-6">
                        <div>
                            <h2 class="text-xs font-bold uppercase tracking-wider text-gray-700 pb-2 mb-4 border-b border-gray-200">
                                Global Search Engine Optimization (SEO)
                            </h2>

                            <div class="grid grid-cols-1 md:grid-cols-2 gap-5">
                                <div>
                                    <label for="meta_title" class="block text-xs font-semibold text-gray-700 uppercase tracking-wider mb-1.5">
                                        Default SEO Meta Title
                                    </label>
                                    <input type="text" name="meta_title" id="meta_title" 
                                           value="{{ old('meta_title', $setting->meta_title ?? '') }}"
                                           placeholder="e.g., The Everbloom — Premium Flora & Gifts"
                                           class="w-full px-3 py-2 text-xs border border-gray-300 bg-white focus:ring-1 focus:ring-blue-600 focus:border-blue-600">
                                </div>

                                <div>
                                    <label for="meta_keywords" class="block text-xs font-semibold text-gray-700 uppercase tracking-wider mb-1.5">
                                        SEO Meta Keywords
                                    </label>
                                    <input type="text" name="meta_keywords" id="meta_keywords" 
                                           value="{{ old('meta_keywords', $setting->meta_keywords ?? '') }}"
                                           placeholder="flora, gifts, online shop, delivery"
                                           class="w-full px-3 py-2 text-xs border border-gray-300 bg-white focus:ring-1 focus:ring-blue-600 focus:border-blue-600">
                                </div>
                            </div>

                            <div class="mt-5">
                                <label for="meta_description" class="block text-xs font-semibold text-gray-700 uppercase tracking-wider mb-1.5">
                                    Default SEO Meta Description
                                </label>
                                <textarea name="meta_description" id="meta_description" rows="3"
                                          placeholder="Search engine summary text describing the business..."
                                          class="w-full px-3 py-2 text-xs border border-gray-300 bg-white focus:ring-1 focus:ring-blue-600 focus:border-blue-600">{{ old('meta_description', $setting->meta_description ?? '') }}</textarea>
                            </div>
                        </div>

                        <div class="pt-4 border-t border-gray-200">
                            <h2 class="text-xs font-bold uppercase tracking-wider text-gray-700 pb-2 mb-4 border-b border-gray-200">
                                Footer Content
                            </h2>

                            <div>
                                <label for="footer_text" class="block text-xs font-semibold text-gray-700 uppercase tracking-wider mb-1.5">
                                    Footer Copyright & Note
                                </label>
                                <textarea name="footer_text" id="footer_text" rows="2"
                                          placeholder="e.g., &copy; {{ date('Y') }} The Everbloom. All rights reserved."
                                          class="w-full px-3 py-2 text-xs border border-gray-300 bg-white focus:ring-1 focus:ring-blue-600 focus:border-blue-600">{{ old('footer_text', $setting->footer_text ?? '') }}</textarea>
                            </div>
                        </div>
                    </div>

                </div>

                <!-- Form Footer Actions -->
                <div class="px-5 sm:px-6 py-4 bg-gray-50 border-t border-gray-200 flex items-center justify-end">
                    <button type="submit"
                            class="px-6 py-2.5 text-xs font-semibold text-white bg-blue-600 hover:bg-blue-700 shadow-sm transition">
                        Save Site Settings
                    </button>
                </div>
            </form>

        </div>
    </div>
@endsection
