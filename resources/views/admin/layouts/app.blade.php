<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Analytics Dashboard - {{ config('app.name') }}</title>
    <meta name="description" content="Professional analytics dashboard with real-time data visualization">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <link rel="icon" href="{{ asset('favicon.ico') }}" type="image/x-icon">
    @vite(['resources/css/app.css'])
    <link rel="stylesheet" href="{{ asset('assets/custom.css') }}">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/toastr.js/latest/toastr.min.css">
    <script defer src="https://cdn.jsdelivr.net/npm/alpinejs@3.x.x/dist/cdn.min.js"></script>
    <style>
        [x-cloak] { display: none !important; }
        
        /* Pre-render sidebar state to avoid flicker */
        .sidebar-collapsed #sidebar { display: none !important; }
        .sidebar-collapsed #main-content { margin-left: 0 !important; }
        .sidebar-collapsed #expand-sidebar-btn { display: flex !important; }
    </style>
    <script>
        (function() {
            if (localStorage.getItem('sidebar-collapsed') === 'true') {
                document.documentElement.classList.add('sidebar-collapsed');
            }
        })();
    </script>
</head>

<body class="bg-gray-50">
    @include('admin.layouts.nav')
    @include('admin.layouts.sidebar')
    <div id="main-content" class="sm:ml-64 pt-16 min-h-screen content-z relative">
        <main class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-8">
            @include('admin.lib.alert-message')
            @yield('content')
        </main>
    </div>
    
    <!-- Load jQuery first -->
    <script src="https://code.jquery.com/jquery-3.7.1.min.js" integrity="sha256-/JqT3SQfawRcv/BIHPThkBvs0OEvtFFmqPF/lYI/Cxo=" crossorigin="anonymous"></script>
    <script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
    
    <!-- Load Vite assets after jQuery -->
    @vite(['resources/js/app.js'])
    <script src="{{ asset('assets/custom.js') }}"></script>
    <script src="https://cdnjs.cloudflare.com/ajax/libs/toastr.js/latest/toastr.min.js"></script>
    @include('admin.lib.confirm-delete-modal')
    @yield('scripts')
    @stack('scripts')
</body>

</html>
