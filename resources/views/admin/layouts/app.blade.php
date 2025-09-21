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
    @vite(['resources/js/app.js'])
    <link rel="stylesheet" href="{{ asset('assets/custom.css') }}">
    <script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
</head>

<body class="bg-gray-50 dark:bg-gray-900">
    @include('admin.layouts.nav')
    @include('admin.layouts.sidebar')
    <div id="main-content" class="sm:ml-64 pt-16 min-h-screen content-z relative">
        <main class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-8">
            @include('admin.lib.alert-message')
            @yield('content')
        </main>
    </div>
    <script src="{{ asset('assets/custom.js') }}"></script>
    @include('admin.lib.confirm-delete-modal')
    @yield('scripts')
</body>

</html>
