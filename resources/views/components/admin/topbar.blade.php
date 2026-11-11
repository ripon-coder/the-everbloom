@props(['title' => 'Dashboard'])

<header class="topbar">
    <div class="container-fluid">
        <div class="navbar-header">
            <div class="d-flex align-items-center">
                <!-- Menu Toggle Button -->
                <div class="topbar-item">
                    <button type="button" class="button-toggle-menu me-2" aria-label="Toggle Navigation">
                        <iconify-icon icon="solar:hamburger-menu-broken" class="fs-24 align-middle"></iconify-icon>
                    </button>
                </div>

                <!-- Page Header Title -->
                <div class="topbar-item">
                    <h4 class="fw-bold topbar-button pe-none text-uppercase mb-0">{{ $title }}</h4>
                </div>
            </div>

            <div class="d-flex align-items-center gap-1">
                <!-- Theme Color (Light/Dark) -->
                <div class="topbar-item">
                    <button type="button" class="topbar-button" id="light-dark-mode" aria-label="Toggle Color Theme">
                        <iconify-icon icon="solar:moon-bold-duotone" class="fs-24 align-middle"></iconify-icon>
                    </button>
                </div>

                <!-- Live Storefront Link -->
                <div class="topbar-item d-none d-sm-flex">
                    <a href="{{ route('home') }}" target="_blank" class="btn btn-sm btn-soft-primary d-flex align-items-center gap-1 px-2 py-1">
                        <iconify-icon icon="solar:shop-bold-duotone" class="fs-18"></iconify-icon>
                        <span class="fs-13">View Store</span>
                    </a>
                </div>

                <!-- Notification Dropdown -->
                <div class="dropdown topbar-item">
                    <button type="button" class="topbar-button position-relative" id="page-header-notifications-dropdown" data-bs-toggle="dropdown" aria-haspopup="true" aria-expanded="false">
                        <iconify-icon icon="solar:bell-bing-bold-duotone" class="fs-24 align-middle"></iconify-icon>
                        <span class="position-absolute topbar-badge fs-10 translate-middle badge bg-danger rounded-pill">3<span class="visually-hidden">unread messages</span></span>
                    </button>
                    <div class="dropdown-menu py-0 dropdown-lg dropdown-menu-end" aria-labelledby="page-header-notifications-dropdown">
                        <div class="p-3 border-top-0 border-start-0 border-end-0 border-dashed border">
                            <div class="row align-items-center">
                                <div class="col">
                                    <h6 class="m-0 fs-16 fw-semibold">Notifications</h6>
                                </div>
                                <div class="col-auto">
                                    <a href="javascript:void(0);" class="text-dark text-decoration-underline">
                                        <small>Clear All</small>
                                    </a>
                                </div>
                            </div>
                        </div>
                        <div data-simplebar style="max-height: 280px;">
                            <a href="{{ route('admin.orders.index') }}" class="dropdown-item py-3 border-bottom text-wrap">
                                <div class="d-flex">
                                    <div class="avatar-sm me-2">
                                        <span class="avatar-title bg-soft-success text-success fs-20 rounded-circle">
                                            <iconify-icon icon="solar:bag-smile-bold-duotone"></iconify-icon>
                                        </span>
                                    </div>
                                    <div class="flex-grow-1">
                                        <p class="mb-0 fw-semibold">New Order Received</p>
                                        <p class="mb-0 text-muted fs-12">Eleanor Vance ordered Botanical Radiance Serum (#EVB-9204)</p>
                                    </div>
                                </div>
                            </a>
                            <a href="{{ route('admin.reviews.index') }}" class="dropdown-item py-3 border-bottom text-wrap">
                                <div class="d-flex">
                                    <div class="avatar-sm me-2">
                                        <span class="avatar-title bg-soft-warning text-warning fs-20 rounded-circle">
                                            <iconify-icon icon="solar:chat-square-like-bold-duotone"></iconify-icon>
                                        </span>
                                    </div>
                                    <div class="flex-grow-1">
                                        <p class="mb-0 fw-semibold">5-Star Review Added</p>
                                        <p class="mb-0 text-muted fs-12">Michael B. reviewed Botanical Radiance Serum</p>
                                    </div>
                                </div>
                            </a>
                            <a href="{{ route('admin.inventory.index') }}" class="dropdown-item py-3 border-bottom text-wrap">
                                <div class="d-flex">
                                    <div class="avatar-sm me-2">
                                        <span class="avatar-title bg-soft-danger text-danger fs-20 rounded-circle">
                                            <iconify-icon icon="solar:box-bold-duotone"></iconify-icon>
                                        </span>
                                    </div>
                                    <div class="flex-grow-1">
                                        <p class="mb-0 fw-semibold">Low Stock Alert</p>
                                        <p class="mb-0 text-muted fs-12">Camellia Restorative Moisturizer 100ml (14 units left)</p>
                                    </div>
                                </div>
                            </a>
                        </div>
                        <div class="text-center py-3">
                            <a href="{{ route('admin.orders.index') }}" class="btn btn-primary btn-sm">View All Activity <i class="bx bx-right-arrow-alt ms-1"></i></a>
                        </div>
                    </div>
                </div>

                <!-- Theme Setting Offcanvas Toggle -->
                <div class="topbar-item d-none d-md-flex">
                    <button type="button" class="topbar-button" data-bs-toggle="offcanvas" data-bs-target="#theme-settings-offcanvas" aria-controls="theme-settings-offcanvas" aria-label="Theme Customizer">
                        <iconify-icon icon="solar:settings-bold-duotone" class="fs-24 align-middle"></iconify-icon>
                    </button>
                </div>

                <!-- User Dropdown -->
                <div class="dropdown topbar-item">
                    <a type="button" class="topbar-button" id="page-header-user-dropdown" data-bs-toggle="dropdown" aria-haspopup="true" aria-expanded="false">
                        <span class="d-flex align-items-center">
                            <img class="rounded-circle" width="32" height="32" src="{{ asset('admin/assets/images/users/avatar-1.jpg') }}" alt="Admin Avatar">
                        </span>
                    </a>
                    <div class="dropdown-menu dropdown-menu-end">
                        <h6 class="dropdown-header">Everbloom Concierge</h6>
                        <a class="dropdown-item" href="{{ route('admin.settings.index') }}">
                            <i class="bx bx-cog text-muted fs-18 align-middle me-1"></i><span class="align-middle">Store Settings</span>
                        </a>
                        <a class="dropdown-item" href="{{ route('home') }}" target="_blank">
                            <i class="bx bx-store-alt text-muted fs-18 align-middle me-1"></i><span class="align-middle">Storefront</span>
                        </a>
                        <div class="dropdown-divider my-1"></div>
                        <a class="dropdown-item text-danger" href="{{ route('admin.login') }}">
                            <i class="bx bx-log-out fs-18 align-middle me-1"></i><span class="align-middle">Logout</span>
                        </a>
                    </div>
                </div>

                <!-- App Search -->
                <form class="app-search d-none d-md-block ms-2">
                    <div class="position-relative">
                        <input type="search" class="form-control" placeholder="Search orders, products..." autocomplete="off">
                        <iconify-icon icon="solar:magnifer-linear" class="search-widget-icon"></iconify-icon>
                    </div>
                </form>
            </div>
        </div>
    </div>
</header>
