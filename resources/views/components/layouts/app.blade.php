<!DOCTYPE html>
<html lang="en" class="scroll-smooth">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>{{ $title ?? 'Everbloom Electronics | Premium Tech Store' }}</title>
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
    <script defer src="https://cdn.jsdelivr.net/npm/alpinejs@3.x.x/dist/cdn.min.js"></script>
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Outfit:wght@300;400;500;600;700;800&display=swap" rel="stylesheet">
    <style>
        body { font-family: 'Outfit', sans-serif; }
    </style>
</head>
<body class="antialiased bg-gray-50 text-gray-900">
    <x-layout.nav />
    
    <main class="min-h-screen">
        {{ $slot }}
    </main>

    <x-layout.footer />

    <!-- Global Notification/Toast System -->
    <div x-data="{ show: false, message: '', type: 'success' }"
         x-init="
            @if(session('success'))
                message = '{{ addslashes(session('success')) }}';
                type = 'success';
                show = true;
                setTimeout(() => show = false, 3000);
            @endif
            @if(session('error'))
                message = '{{ addslashes(session('error')) }}';
                type = 'error';
                show = true;
                setTimeout(() => show = false, 3000);
            @endif
         "
         @notify.window="message = $event.detail.message; type = $event.detail.type || 'success'; show = true; setTimeout(() => show = false, 3000)"
         class="fixed bottom-4 right-4 z-[100] flex flex-col gap-2 pointer-events-none"
    >
        <div x-show="show" x-cloak
             x-transition:enter="transition ease-out duration-300"
             x-transition:enter-start="opacity-0 translate-y-4"
             x-transition:enter-end="opacity-100 translate-y-0"
             x-transition:leave="transition ease-in duration-200"
             x-transition:leave-start="opacity-100 translate-y-0"
             x-transition:leave-end="opacity-0 translate-y-4"
             class="px-4 py-3 rounded-md shadow-2xl flex items-center gap-3 max-w-sm pointer-events-auto border"
             :class="{
                 'bg-white border-l-4 border-l-red-500 border-gray-100 text-slate-800': type === 'error',
                 'bg-white border-l-4 border-l-green-500 border-gray-100 text-slate-800': type === 'success'
             }"
        >
            <div x-show="type === 'error'" class="text-red-500">
                <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4m0 4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"></path></svg>
            </div>
            <div x-show="type === 'success'" class="text-green-500">
                <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"></path></svg>
            </div>
            <p class="text-[13px] font-bold" x-text="message"></p>
        </div>
    </div>

</body>
</html>
