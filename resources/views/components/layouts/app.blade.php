@props(['title' => null, 'description' => null, 'image' => null, 'type' => 'website', 'url' => null])
<!DOCTYPE html>
<html lang="en" class="scroll-smooth">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    @php
        $currentUrl = $url ?? url()->current();
        $pageTitle = $title ?? (($site_setting->site_name ?? 'Feriwalarhat') . ' | ' . ($site_setting->meta_title ?? 'Premium Tech Store'));
        $pageDescription = $description ?? $site_setting->meta_description ?? '';
        $pageImage = $image ?? ($site_setting && $site_setting->site_logo ? Storage::url($site_setting->site_logo) : asset('default-og-image.jpg'));
    @endphp

    <meta name="description" content="{{ $pageDescription }}">
    <meta name="keywords" content="{{ $site_setting->meta_keywords ?? '' }}">
    <title>{{ $pageTitle }}</title>

    <link rel="canonical" href="{{ $currentUrl }}">

    <!-- Open Graph Meta Tags -->
    <meta property="og:title" content="{{ $pageTitle }}">
    <meta property="og:description" content="{{ $pageDescription }}">
    <meta property="og:url" content="{{ $currentUrl }}">
    <meta property="og:type" content="{{ $type ?? 'website' }}">
    <meta property="og:image" content="{{ $pageImage }}">
    <meta property="og:site_name" content="{{ $site_setting->site_name ?? 'Feriwalarhat' }}">
    @if($site_setting && $site_setting->site_favicon)
        <link rel="icon" href="{{ Storage::url($site_setting->site_favicon) }}" type="image/x-icon">
    @endif
    @php
        $manifestPath = public_path('build/manifest.json');
        if (file_exists($manifestPath)) {
            $manifest = json_decode(file_get_contents($manifestPath), true);
            echo '<link rel="stylesheet" href="/build/' . $manifest['resources/css/app.css']['file'] . '?v=' . time() . '">';
            echo '<script type="module" src="/build/' . $manifest['resources/js/app.js']['file'] . '?v=' . time() . '"></script>';
        } else {
            echo '@vite(["resources/css/app.css", "resources/js/app.js"])';
        }
    @endphp
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Outfit:wght@300;400;500;600;700;800&display=swap" rel="stylesheet">
    <style>
        body { font-family: 'Outfit', sans-serif; }
        [x-cloak] { display: none !important; }
    </style>
    @stack('styles')
</head>
<body class="antialiased bg-gray-50 text-gray-900 pb-16 md:pb-0">
    <x-layout.nav />
    
    <main class="min-h-screen bg-white">
        <div class="max-w-8xl mx-auto px-4 sm:px-6 lg:px-8 py-6">
                    {{ $slot }}
        </div>
    </main>

    <x-layout.footer />

    <!-- Global Notification/Toast System -->
    <div x-data="{ 
            show: false, 
            message: '', 
            type: 'success',
            progress: 0,
            interval: null,
            duration: 4000,
            startTimer() {
                this.show = true;
                this.progress = 0;
                if (this.interval) clearInterval(this.interval);
                
                const start = Date.now();
                this.interval = setInterval(() => {
                    const elapsed = Date.now() - start;
                    this.progress = (elapsed / this.duration) * 100;
                    if (elapsed >= this.duration) {
                        this.show = false;
                        clearInterval(this.interval);
                    }
                }, 10);
            }
         }"
         x-init="
            @if(session('success'))
                message = {!! Js::from(session('success')) !!};
                type = 'success';
                startTimer();
            @endif
            @if(session('error'))
                message = {!! Js::from(session('error')) !!};
                type = 'error';
                startTimer();
            @endif
         "
         @notify.window="message = $event.detail.message; type = $event.detail.type || 'success'; startTimer()"
         class="fixed top-6 right-6 z-[100] flex flex-col gap-3 pointer-events-none min-w-[320px] max-w-sm"
    >
        <div x-show="show" x-cloak
             x-transition:enter="transition ease-out duration-500"
             x-transition:enter-start="opacity-0 translate-x-12 scale-95"
             x-transition:enter-end="opacity-100 translate-x-0 scale-100"
             x-transition:leave="transition ease-in duration-300"
             x-transition:leave-start="opacity-100 translate-x-0"
             x-transition:leave-end="opacity-0 translate-x-12"
             class="relative overflow-hidden rounded-2xl shadow-[0_20px_50px_rgba(0,0,0,0.15)] backdrop-blur-xl border pointer-events-auto p-4 flex items-start gap-4 transition-all"
             :class="{
                 'bg-white/90 border-emerald-100': type === 'success',
                 'bg-white/90 border-danger/20': type === 'error',
                 'bg-white/90 border-amber-100': type === 'info' || type === 'warning'
             }"
        >
            <!-- Background Glow -->
            <div class="absolute -right-4 -top-4 w-24 h-24 blur-3xl opacity-20 rounded-full"
                 :class="type === 'success' ? 'bg-emerald-500' : (type === 'info' || type === 'warning' ? 'bg-amber-400' : 'bg-danger')"></div>

            <!-- Icon Section -->
            <div class="flex-shrink-0 w-10 h-10 rounded-xl flex items-center justify-center shadow-sm"
                 :class="type === 'success' ? 'bg-emerald-50 text-emerald-600' : (type === 'info' || type === 'warning' ? 'bg-amber-50 text-amber-600' : 'bg-danger/10 text-danger')">
                <template x-if="type === 'success'">
                    <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M5 13l4 4L19 7"></path></svg>
                </template>
                <template x-if="type === 'error'">
                    <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M6 18L18 6M6 6l12 12"></path></svg>
                </template>
                <template x-if="type === 'info' || type === 'warning'">
                    <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M13 16h-1v-4h-1m1-4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"></path></svg>
                </template>
            </div>

            <!-- Content -->
            <div class="flex-1 pt-0.5 pr-2">
                <h4 class="text-[10px] font-black uppercase tracking-[0.2em] mb-1 opacity-40"
                    :class="type === 'success' ? 'text-emerald-800' : (type === 'info' || type === 'warning' ? 'text-amber-800' : 'text-danger')"
                    x-text="type === 'success' ? 'Success' : (type === 'info' ? 'Notice' : (type === 'warning' ? 'Warning' : 'Attention'))"></h4>
                <p class="text-[13px] font-bold text-gray-800 leading-tight" x-text="message"></p>
            </div>

            <!-- Close Button -->
            <button @click="show = false" class="text-gray-300 hover:text-gray-500 transition-colors">
                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"></path></svg>
            </button>

            <!-- Progress Bar -->
            <div class="absolute bottom-0 left-0 h-1 bg-current opacity-20"
                 :style="`width: ${progress}%; transition: width 10ms linear;`"
                 :class="type === 'success' ? 'text-emerald-500' : (type === 'info' || type === 'warning' ? 'text-amber-400' : 'text-danger')"></div>
            <div class="absolute bottom-0 left-0 h-1 bg-current"
                 :style="`width: ${progress}%; transition: width 10ms linear;`"
                 :class="type === 'success' ? 'text-emerald-500' : (type === 'info' || type === 'warning' ? 'text-amber-400' : 'text-danger')"></div>
        </div>
    </div>

    <!-- Global Right Side Cart Drawer -->
    <x-layout.cart-drawer />

    <!-- Mobile Bottom Navigation Bar -->
    <x-layout.mobile-nav />

    @stack('scripts')
</body>
</html>
