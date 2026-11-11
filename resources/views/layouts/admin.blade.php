<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="utf-8" />
    <title>@yield('title', 'Dashboard') | The Everbloom Admin</title>
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta content="The Everbloom Botanical Skincare Management Console" name="description" />
    <meta content="The Everbloom" name="author" />
    <meta http-equiv="X-UA-Compatible" content="IE=edge" />

    <!-- App favicon -->
    <link rel="shortcut icon" href="{{ asset('admin/assets/images/favicon.ico') }}">

    <!-- Vendor css (Bootstrap 5.3.3 + plugins) -->
    <link href="{{ asset('admin/assets/css/vendor.min.css') }}" rel="stylesheet" type="text/css" />

    <!-- Icons css (Solar + Boxicons + Iconamoon) -->
    <link href="{{ asset('admin/assets/css/icons.min.css') }}" rel="stylesheet" type="text/css" />

    <!-- App css (Larkon theme) -->
    <link href="{{ asset('admin/assets/css/app.min.css') }}" rel="stylesheet" type="text/css" />

    @stack('styles')

    <!-- Theme Config Js (MUST run in <head> to set layout attributes before first paint) -->
    <script src="{{ asset('admin/assets/js/config.js') }}"></script>
</head>

<body>
    <!-- START Wrapper -->
    <div class="wrapper">

        <!-- Topbar Header -->
        <x-admin.topbar :title="$pageTitle ?? ($title ?? 'Dashboard')" />

        <!-- Offcanvas Drawers -->
        <x-admin.offcanvas-drawers />

        <!-- Left Sidebar Navigation -->
        <x-admin.sidebar />

        <!-- Right Page Content Area -->
        <div class="page-content">
            <div class="@yield('container_class', 'container-xxl')">
                @yield('content')
            </div>

            <!-- Footer -->
            <x-admin.footer />
        </div>

    </div>
    <!-- END Wrapper -->

    <!-- Core Vendor & App JS -->
    <script src="{{ asset('admin/assets/js/vendor.js') }}"></script>
    <script src="{{ asset('admin/assets/js/app.js') }}"></script>

    @stack('scripts')
</body>
</html>
