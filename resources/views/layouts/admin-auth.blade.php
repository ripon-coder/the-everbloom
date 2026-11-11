<!DOCTYPE html>
<html lang="en" class="h-100">
<head>
    <meta charset="utf-8" />
    <title>@yield('title', 'Sign In') | The Everbloom Admin</title>
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta content="The Everbloom Botanical Skincare Management Console" name="description" />
    <meta content="The Everbloom" name="author" />
    <meta http-equiv="X-UA-Compatible" content="IE=edge" />

    <!-- App favicon -->
    <link rel="shortcut icon" href="{{ asset('admin/assets/images/favicon.ico') }}">

    <!-- Vendor css -->
    <link href="{{ asset('admin/assets/css/vendor.min.css') }}" rel="stylesheet" type="text/css" />

    <!-- Icons css -->
    <link href="{{ asset('admin/assets/css/icons.min.css') }}" rel="stylesheet" type="text/css" />

    <!-- App css -->
    <link href="{{ asset('admin/assets/css/app.min.css') }}" rel="stylesheet" type="text/css" />

    @stack('styles')

    <!-- Theme Config js -->
    <script src="{{ asset('admin/assets/js/config.js') }}"></script>
</head>

<body class="h-100">
    <div class="d-flex flex-column h-100 p-3">
        <div class="d-flex flex-column flex-grow-1">
            <div class="row h-100">
                <!-- Left Form Column -->
                <div class="col-xxl-7">
                    <div class="row justify-content-center h-100">
                        <div class="col-lg-7 col-xl-6 py-lg-5">
                            <div class="d-flex flex-column h-100 justify-content-center">
                                <div class="auth-logo mb-4">
                                    <a href="{{ route('home') }}" class="logo-dark">
                                        <img src="{{ asset('admin/assets/images/logo-dark.png') }}" height="28" alt="The Everbloom">
                                    </a>
                                    <a href="{{ route('home') }}" class="logo-light">
                                        <img src="{{ asset('admin/assets/images/logo-light.png') }}" height="28" alt="The Everbloom">
                                    </a>
                                </div>

                                @yield('content')
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Right Brand Artwork Column -->
                <div class="col-xxl-5 d-none d-xxl-flex">
                    <div class="card h-100 mb-0 overflow-hidden border-0">
                        <div class="d-flex flex-column h-100 position-relative">
                            <img src="{{ asset('admin/assets/images/small/img-10.jpg') }}" alt="Everbloom Botanical Skincare" class="w-100 h-100 object-fit-cover">
                            <div class="position-absolute bottom-0 start-0 end-0 p-4 bg-gradient-dark text-white text-center" style="background: linear-gradient(to top, rgba(0,0,0,0.75), transparent);">
                                <h4 class="text-white fw-bold">The Everbloom Botanical Lab</h4>
                                <p class="text-white-50 mb-0 fs-13">Artisanal Skincare Management Console</p>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Vendor Javascript -->
    <script src="{{ asset('admin/assets/js/vendor.js') }}"></script>
    <script src="{{ asset('admin/assets/js/app.js') }}"></script>

    @stack('scripts')
</body>
</html>
