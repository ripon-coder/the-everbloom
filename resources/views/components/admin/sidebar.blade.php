<div class="main-nav">
    <!-- Sidebar Logo -->
    <div class="logo-box">
        <a href="{{ route('admin.dashboard') }}" class="logo-dark">
            <img src="{{ asset('admin/assets/images/logo-sm.png') }}" class="logo-sm" alt="Everbloom">
            <img src="{{ asset('admin/assets/images/logo-dark.png') }}" class="logo-lg" alt="The Everbloom">
        </a>
        <a href="{{ route('admin.dashboard') }}" class="logo-light">
            <img src="{{ asset('admin/assets/images/logo-sm.png') }}" class="logo-sm" alt="Everbloom">
            <img src="{{ asset('admin/assets/images/logo-light.png') }}" class="logo-lg" alt="The Everbloom">
        </a>
    </div>

    <!-- Menu Toggle Button (sm-hover) -->
    <button type="button" class="button-sm-hover" aria-label="Show Full Sidebar">
        <iconify-icon icon="solar:double-alt-arrow-right-bold-duotone" class="button-sm-hover-icon"></iconify-icon>
    </button>

    <div class="scrollbar" data-simplebar>
        <ul class="navbar-nav" id="navbar-nav">

            <li class="menu-title">General</li>

            <!-- Dashboard -->
            <li class="nav-item">
                <a class="nav-link {{ request()->routeIs('admin.dashboard') ? 'active' : '' }}" href="{{ route('admin.dashboard') }}">
                    <span class="nav-icon">
                        <iconify-icon icon="solar:widget-5-bold-duotone"></iconify-icon>
                    </span>
                    <span class="nav-text"> Dashboard </span>
                </a>
            </li>

            <!-- Products -->
            <li class="nav-item">
                <a class="nav-link menu-arrow {{ request()->routeIs('admin.products.*') ? 'active' : '' }}" href="#sidebarProducts" data-bs-toggle="collapse" role="button" aria-expanded="{{ request()->routeIs('admin.products.*') ? 'true' : 'false' }}" aria-controls="sidebarProducts">
                    <span class="nav-icon">
                        <iconify-icon icon="solar:box-bold-duotone"></iconify-icon>
                    </span>
                    <span class="nav-text"> Products </span>
                </a>
                <div class="collapse {{ request()->routeIs('admin.products.*') ? 'show' : '' }}" id="sidebarProducts">
                    <ul class="nav sub-navbar-nav">
                        <li class="sub-nav-item">
                            <a class="sub-nav-link {{ request()->routeIs('admin.products.index') ? 'active' : '' }}" href="{{ route('admin.products.index') }}">Product List</a>
                        </li>
                        <li class="sub-nav-item">
                            <a class="sub-nav-link {{ request()->routeIs('admin.products.create') ? 'active' : '' }}" href="{{ route('admin.products.create') }}">Create Product</a>
                        </li>
                    </ul>
                </div>
            </li>

            <!-- Categories -->
            <li class="nav-item">
                <a class="nav-link menu-arrow {{ request()->routeIs('admin.categories.*') ? 'active' : '' }}" href="#sidebarCategory" data-bs-toggle="collapse" role="button" aria-expanded="{{ request()->routeIs('admin.categories.*') ? 'true' : 'false' }}" aria-controls="sidebarCategory">
                    <span class="nav-icon">
                        <iconify-icon icon="solar:clipboard-list-bold-duotone"></iconify-icon>
                    </span>
                    <span class="nav-text"> Categories </span>
                </a>
                <div class="collapse {{ request()->routeIs('admin.categories.*') ? 'show' : '' }}" id="sidebarCategory">
                    <ul class="nav sub-navbar-nav">
                        <li class="sub-nav-item">
                            <a class="sub-nav-link {{ request()->routeIs('admin.categories.index') ? 'active' : '' }}" href="{{ route('admin.categories.index') }}">Category List</a>
                        </li>
                        <li class="sub-nav-item">
                            <a class="sub-nav-link {{ request()->routeIs('admin.categories.create') ? 'active' : '' }}" href="{{ route('admin.categories.create') }}">Create Category</a>
                        </li>
                    </ul>
                </div>
            </li>

            <!-- Inventory -->
            <li class="nav-item">
                <a class="nav-link {{ request()->routeIs('admin.inventory.index') ? 'active' : '' }}" href="{{ route('admin.inventory.index') }}">
                    <span class="nav-icon">
                        <iconify-icon icon="solar:box-broken"></iconify-icon>
                    </span>
                    <span class="nav-text"> Inventory </span>
                </a>
            </li>

            <!-- Orders -->
            <li class="nav-item">
                <a class="nav-link menu-arrow {{ request()->routeIs('admin.orders.*') ? 'active' : '' }}" href="#sidebarOrders" data-bs-toggle="collapse" role="button" aria-expanded="{{ request()->routeIs('admin.orders.*') ? 'true' : 'false' }}" aria-controls="sidebarOrders">
                    <span class="nav-icon">
                        <iconify-icon icon="solar:bag-smile-bold-duotone"></iconify-icon>
                    </span>
                    <span class="nav-text"> Orders </span>
                </a>
                <div class="collapse {{ request()->routeIs('admin.orders.*') ? 'show' : '' }}" id="sidebarOrders">
                    <ul class="nav sub-navbar-nav">
                        <li class="sub-nav-item">
                            <a class="sub-nav-link {{ request()->routeIs('admin.orders.index') ? 'active' : '' }}" href="{{ route('admin.orders.index') }}">Order List</a>
                        </li>
                        <li class="sub-nav-item">
                            <a class="sub-nav-link {{ request()->routeIs('admin.orders.show') ? 'active' : '' }}" href="{{ route('admin.orders.show', 'EVB-9204') }}">Order Details</a>
                        </li>
                    </ul>
                </div>
            </li>

            <!-- Invoices -->
            <li class="nav-item">
                <a class="nav-link {{ request()->routeIs('admin.invoices.*') ? 'active' : '' }}" href="{{ route('admin.invoices.index') }}">
                    <span class="nav-icon">
                        <iconify-icon icon="solar:bill-list-bold-duotone"></iconify-icon>
                    </span>
                    <span class="nav-text"> Invoices </span>
                </a>
            </li>

            <!-- Store Settings -->
            <li class="nav-item">
                <a class="nav-link {{ request()->routeIs('admin.settings.index') ? 'active' : '' }}" href="{{ route('admin.settings.index') }}">
                    <span class="nav-icon">
                        <iconify-icon icon="solar:settings-bold-duotone"></iconify-icon>
                    </span>
                    <span class="nav-text"> Settings </span>
                </a>
            </li>

            <li class="menu-title mt-2">Customers & Marketing</li>

            <!-- Customers -->
            <li class="nav-item">
                <a class="nav-link menu-arrow {{ request()->routeIs('admin.customers.*') ? 'active' : '' }}" href="#sidebarCustomers" data-bs-toggle="collapse" role="button" aria-expanded="{{ request()->routeIs('admin.customers.*') ? 'true' : 'false' }}" aria-controls="sidebarCustomers">
                    <span class="nav-icon">
                        <iconify-icon icon="solar:users-group-two-rounded-bold-duotone"></iconify-icon>
                    </span>
                    <span class="nav-text"> Customers </span>
                </a>
                <div class="collapse {{ request()->routeIs('admin.customers.*') ? 'show' : '' }}" id="sidebarCustomers">
                    <ul class="nav sub-navbar-nav">
                        <li class="sub-nav-item">
                            <a class="sub-nav-link {{ request()->routeIs('admin.customers.index') ? 'active' : '' }}" href="{{ route('admin.customers.index') }}">Customer List</a>
                        </li>
                        <li class="sub-nav-item">
                            <a class="sub-nav-link {{ request()->routeIs('admin.customers.show') ? 'active' : '' }}" href="{{ route('admin.customers.show', 1) }}">Customer Detail</a>
                        </li>
                    </ul>
                </div>
            </li>

            <!-- Coupons -->
            <li class="nav-item">
                <a class="nav-link menu-arrow {{ request()->routeIs('admin.coupons.*') ? 'active' : '' }}" href="#sidebarCoupons" data-bs-toggle="collapse" role="button" aria-expanded="{{ request()->routeIs('admin.coupons.*') ? 'true' : 'false' }}" aria-controls="sidebarCoupons">
                    <span class="nav-icon">
                        <iconify-icon icon="solar:leaf-bold-duotone"></iconify-icon>
                    </span>
                    <span class="nav-text"> Coupons </span>
                </a>
                <div class="collapse {{ request()->routeIs('admin.coupons.*') ? 'show' : '' }}" id="sidebarCoupons">
                    <ul class="nav sub-navbar-nav">
                        <li class="sub-nav-item">
                            <a class="sub-nav-link {{ request()->routeIs('admin.coupons.index') ? 'active' : '' }}" href="{{ route('admin.coupons.index') }}">Coupon List</a>
                        </li>
                        <li class="sub-nav-item">
                            <a class="sub-nav-link {{ request()->routeIs('admin.coupons.create') ? 'active' : '' }}" href="{{ route('admin.coupons.create') }}">Create Coupon</a>
                        </li>
                    </ul>
                </div>
            </li>

            <!-- Reviews -->
            <li class="nav-item">
                <a class="nav-link {{ request()->routeIs('admin.reviews.index') ? 'active' : '' }}" href="{{ route('admin.reviews.index') }}">
                    <span class="nav-icon">
                        <iconify-icon icon="solar:chat-square-like-bold-duotone"></iconify-icon>
                    </span>
                    <span class="nav-text"> Reviews </span>
                </a>
            </li>

            <li class="menu-title mt-2">Storefront</li>

            <li class="nav-item">
                <a class="nav-link" href="{{ route('home') }}" target="_blank">
                    <span class="nav-icon">
                        <iconify-icon icon="solar:shop-2-bold-duotone"></iconify-icon>
                    </span>
                    <span class="nav-text"> View Live Store </span>
                    <span class="badge bg-primary-subtle text-primary badge-pill text-end">Online</span>
                </a>
            </li>

        </ul>
    </div>
</div>
