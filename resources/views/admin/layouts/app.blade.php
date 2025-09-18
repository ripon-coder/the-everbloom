<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Dashboard - {{ config('app.name') }}</title>

    @vite(['resources/css/app.css', 'resources/js/app.js'])
</head>

<body class="bg-gray-50 dark:bg-gray-900">

    <!-- Navbar -->
    @include('admin.layouts.nav')

    <!-- Sidebar -->
    @include('admin.layouts.sidebar')

    <!-- Main content wrapper -->
    <div class="sm:ml-64"> <!-- pt-16 = navbar height, sm:ml-64 = sidebar width -->
        <h1 class="text-2xl font-bold mb-4 pl-2">Bangladesh</h1>

        <!-- Page Content -->

            @yield('content')

    </div>

</body>

</html>
