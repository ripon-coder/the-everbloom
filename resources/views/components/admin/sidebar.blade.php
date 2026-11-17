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

            <!-- Financial -->
            <li class="nav-item">
                <a class="nav-link {{ request()->routeIs('admin.financial.*') ? 'active' : '' }}" href="{{ route('admin.financial.index') }}">
                    <span class="nav-icon">
                        <iconify-icon icon="solar:chart-square-bold-duotone"></iconify-icon>
                    </span>
                    <span class="nav-text"> Financial </span>
                </a>
            </li>

            <!-- Products -->
            <li class="nav-item">
                <a class="nav-link menu-arrow {{ request()->routeIs('admin.products.*', 'admin.attributes.*') ? 'active' : '' }}" href="#sidebarProducts" data-bs-toggle="collapse" role="button" aria-expanded="{{ request()->routeIs('admin.products.*', 'admin.attributes.*') ? 'true' : 'false' }}" aria-controls="sidebarProducts">
                    <span class="nav-icon">
                        <iconify-icon icon="solar:box-bold-duotone"></iconify-icon>
                    </span>
                    <span class="nav-text"> Products </span>
                </a>
                <div class="collapse {{ request()->routeIs('admin.products.*', 'admin.attributes.*') ? 'show' : '' }}" id="sidebarProducts">
                    <ul class="nav sub-navbar-nav">
                        <li class="sub-nav-item">
                            <a class="sub-nav-link {{ request()->routeIs('admin.products.index') ? 'active' : '' }}" href="{{ route('admin.products.index') }}">Product List</a>
                        </li>
                        <li class="sub-nav-item">
                            <a class="sub-nav-link {{ request()->routeIs('admin.products.create') ? 'active' : '' }}" href="{{ route('admin.products.create') }}">Create Product</a>
                        </li>
                        <li class="sub-nav-item">
                            <a class="sub-nav-link {{ request()->routeIs('admin.attributes.*') ? 'active' : '' }}" href="{{ route('admin.attributes.index') }}">Attributes</a>
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

            <!-- Brands -->
            <li class="nav-item">
                <a class="nav-link menu-arrow {{ request()->routeIs('admin.brands.*') ? 'active' : '' }}" href="#sidebarBrands" data-bs-toggle="collapse" role="button" aria-expanded="{{ request()->routeIs('admin.brands.*') ? 'true' : 'false' }}" aria-controls="sidebarBrands">
                    <span class="nav-icon">
                        <iconify-icon icon="solar:tag-bold-duotone"></iconify-icon>
                    </span>
                    <span class="nav-text"> Brands </span>
                </a>
                <div class="collapse {{ request()->routeIs('admin.brands.*') ? 'show' : '' }}" id="sidebarBrands">
                    <ul class="nav sub-navbar-nav">
                        <li class="sub-nav-item">
                            <a class="sub-nav-link {{ request()->routeIs('admin.brands.index') ? 'active' : '' }}" href="{{ route('admin.brands.index') }}">Brand List</a>
                        </li>
                        <li class="sub-nav-item">
                            <a class="sub-nav-link {{ request()->routeIs('admin.brands.create') ? 'active' : '' }}" href="{{ route('admin.brands.create') }}">Create Brand</a>
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

            <!-- Wholesale Purchases -->
            <li class="nav-item">
                <a class="nav-link {{ request()->routeIs('admin.purchases.*') ? 'active' : '' }}" href="{{ route('admin.purchases.index') }}">
                    <span class="nav-icon">
                        <iconify-icon icon="solar:cart-large-minimalistic-bold-duotone"></iconify-icon>
                    </span>
                    <span class="nav-text"> Wholesale Purchases </span>
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

            <!-- Bangladesh Locations -->
            <li class="nav-item">
                <a class="nav-link {{ request()->routeIs('admin.locations.*') ? 'active' : '' }}" href="{{ route('admin.locations.index') }}">
                    <span class="nav-icon">
                        <iconify-icon icon="solar:map-point-bold-duotone"></iconify-icon>
                    </span>
                    <span class="nav-text"> Locations (BD) </span>
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

            <!-- Flash Sales -->
            <li class="nav-item">
                <a class="nav-link menu-arrow {{ request()->routeIs('admin.flash-sales.*') ? 'active' : '' }}" href="#sidebarFlashSales" data-bs-toggle="collapse" role="button" aria-expanded="{{ request()->routeIs('admin.flash-sales.*') ? 'true' : 'false' }}" aria-controls="sidebarFlashSales">
                    <span class="nav-icon">
                        <iconify-icon icon="solar:bolt-bold-duotone"></iconify-icon>
                    </span>
                    <span class="nav-text"> Flash Sales </span>
                </a>
                <div class="collapse {{ request()->routeIs('admin.flash-sales.*') ? 'show' : '' }}" id="sidebarFlashSales">
                    <ul class="nav sub-navbar-nav">
                        <li class="sub-nav-item">
                            <a class="sub-nav-link {{ request()->routeIs('admin.flash-sales.index') ? 'active' : '' }}" href="{{ route('admin.flash-sales.index') }}">All Flash Sales</a>
                        </li>
                        <li class="sub-nav-item">
                            <a class="sub-nav-link {{ request()->routeIs('admin.flash-sales.create') ? 'active' : '' }}" href="{{ route('admin.flash-sales.create') }}">Create Flash Sale</a>
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

            <!-- CMS & Content -->
            <li class="nav-item">
                <a class="nav-link menu-arrow {{ request()->routeIs('admin.cms.*') ? 'active' : '' }}" href="#sidebarCms" data-bs-toggle="collapse" role="button" aria-expanded="{{ request()->routeIs('admin.cms.*') ? 'true' : 'false' }}" aria-controls="sidebarCms">
                    <span class="nav-icon">
                        <iconify-icon icon="solar:document-text-bold-duotone"></iconify-icon>
                    </span>
                    <span class="nav-text"> CMS & Content </span>
                </a>
                <div class="collapse {{ request()->routeIs('admin.cms.*') ? 'show' : '' }}" id="sidebarCms">
                    <ul class="nav sub-navbar-nav">
                        <li class="sub-nav-item"><a class="sub-nav-link {{ request()->routeIs('admin.cms.pages*') ? 'active' : '' }}" href="{{ route('admin.cms.pages') }}">Pages</a></li>
                        <li class="sub-nav-item"><a class="sub-nav-link {{ request()->routeIs('admin.cms.banners*') ? 'active' : '' }}" href="{{ route('admin.cms.banners') }}">Banners & Sections</a></li>
                        <li class="sub-nav-item"><a class="sub-nav-link {{ request()->routeIs('admin.cms.faqs*') ? 'active' : '' }}" href="{{ route('admin.cms.faqs') }}">FAQs</a></li>
                        <li class="sub-nav-item"><a class="sub-nav-link {{ request()->routeIs('admin.cms.inquiries*') ? 'active' : '' }}" href="{{ route('admin.cms.inquiries') }}">Contact Inquiries</a></li>
                        <li class="sub-nav-item"><a class="sub-nav-link {{ request()->routeIs('admin.cms.subscribers*') ? 'active' : '' }}" href="{{ route('admin.cms.subscribers') }}">Newsletter Subscribers</a></li>
                    </ul>
                </div>
            </li>

            <!-- SEO Management -->
            <li class="nav-item">
                <a class="nav-link {{ request()->routeIs('admin.seo.*') ? 'active' : '' }}" href="{{ route('admin.seo.index') }}">
                    <span class="nav-icon">
                        <iconify-icon icon="solar:global-bold-duotone"></iconify-icon>
                    </span>
                    <span class="nav-text"> SEO Management </span>
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
