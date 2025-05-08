<!DOCTYPE html>
<html lang="en">

<head>
    <title>
        @if (isset($title))
            {{ $title }}
        @else
            Portal
        @endif
    </title>

    <!-- Meta -->
    <meta charset="utf-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">

    <meta name="description" content="Portal - Bootstrap 5 Admin Dashboard Template For Developers">
    <meta name="author" content="Xiaoying Riley at 3rd Wave Media">
    <link rel="shortcut icon" href="favicon.ico">

    <!-- FontAwesome JS-->
    <script defer src="{{ asset('assets/admin/assets/plugins/fontawesome/js/all.min.js') }}"></script>

    <!-- App CSS -->
    <link id="theme-style" rel="stylesheet" href="{{ asset('assets/admin/assets/css/portal.css') }}">

    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/toastr.js/latest/css/toastr.min.css">
    @yield('stylesheet')
</head>

<body class="app">
    @include('admin.partials.header')

    <div class="app-wrapper">
        @yield('content')

        @include('admin.partials.footer')

    </div><!--//app-wrapper-->


    @include('admin.partials.script')

</body>

</html>
