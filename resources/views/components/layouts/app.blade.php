<!DOCTYPE html>
<html lang="en" class="scroll-smooth">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
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
    <x-nav />
    
    <main class="min-h-screen">
        {{ $slot }}
    </main>

    <x-footer />

</body>
</html>
