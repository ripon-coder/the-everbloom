# 02 — Admin Master Layout Shell, Topbar, Sidebar & Navigation Specification

> **AI Agent Context Header**
> - **Source HTML**: `docs/admin-dashboard/index.html` (lines 1–1245, 1760–1798)
> - **Source JS**: `docs/admin-dashboard/assets/js/config.js`, `docs/admin-dashboard/assets/js/app.js`
> - **Target Blade Files**:
>   - `resources/views/layouts/admin.blade.php` (Master Authenticated Shell)
>   - `resources/views/layouts/admin-auth.blade.php` (Standalone Auth Shell)
>   - `resources/views/components/admin/topbar.blade.php`
>   - `resources/views/components/admin/sidebar.blade.php`
>   - `resources/views/components/admin/footer.blade.php`

---

## 1. Master DOM Hierarchy (`layouts/admin.blade.php`)

Every authenticated Admin Dashboard page **must** follow this exact structural skeleton so that `config.js`, `app.js` (`ThemeLayout`), and `app.min.css` position the fixed `280px`/`75px` sidebar, `100px` topbar, and `60px` footer properly:

```html
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="utf-8" />
    <title>@yield('title', 'Dashboard') | The Everbloom Admin</title>
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link rel="shortcut icon" href="{{ asset('admin/assets/images/favicon.ico') }}">

    <!-- 1. Vendor CSS (Required on all pages) -->
    <link href="{{ asset('admin/assets/css/vendor.min.css') }}" rel="stylesheet" type="text/css" />
    <!-- 2. Icons CSS (Required on all pages) -->
    <link href="{{ asset('admin/assets/css/icons.min.css') }}" rel="stylesheet" type="text/css" />
    <!-- 3. App CSS (Required on all pages) -->
    <link href="{{ asset('admin/assets/css/app.min.css') }}" rel="stylesheet" type="text/css" />

    @stack('styles')

    <!-- 4. Theme Config JS (MUST run in <head> before body paint) -->
    <script src="{{ asset('admin/assets/js/config.js') }}"></script>
</head>
<body>
    <!-- START Wrapper -->
    <div class="wrapper">

        <!-- 1. Topbar Header -->
        <x-admin.topbar :title="$pageTitle ?? 'Welcome!'" />

        <!-- 2. Offcanvas Drawers (Activity Stream & Theme Settings) -->
        <x-admin.offcanvas-drawers />

        <!-- 3. Left Sidebar Navigation -->
        <x-admin.sidebar />

        <!-- 4. Right Page Content Area -->
        <div class="page-content">
            <!-- Use .container-fluid on Dashboard/Product-List, or .container-xxl on inner CRUD pages -->
            <div class="@yield('container_class', 'container-xxl')">
                @yield('content')
            </div>

            <!-- 5. Footer -->
            <x-admin.footer />
        </div>

    </div>
    <!-- END Wrapper -->

    <!-- Required Core JS -->
    <script src="{{ asset('admin/assets/js/vendor.js') }}"></script>
    <script src="{{ asset('admin/assets/js/app.js') }}"></script>

    @stack('scripts')
</body>
</html>
```

### Container Width Convention in `docs/admin-dashboard`
- **`.container-fluid`** (100% full width): Used on `index.html` (Main Dashboard) and `product-list.html`.
- **`.container-xxl`** (max-width `1320px` centered inside `.page-content`): Used on `orders-list.html`, `order-detail.html`, `product-add.html`, `category-list.html`, `customer-list.html`, `coupons-list.html`, `coupons-add.html`, `settings.html`, `pages-review.html`.

---

## 2. Topbar Specification (`header.topbar`)

### 2.1 Structural Anatomy
- **Height**: `100px` (`--bs-topbar-height`)
- **Left Cluster (`.d-flex.align-items-center`)**:
  1. **Mobile/Tablet Hamburger Button (`.button-toggle-menu`)**:
     - Visible when `data-menu-size` is `default`, `condensed`, or `hidden` (hidden via CSS when `sm-hover` or `sm-hover-active` is active on desktop).
     - Clicking toggles `condensed` on desktop or `.sidebar-enable` + `.offcanvas-backdrop` on `< 1140px` screens.
  2. **Page Header Title (`h4.fw-bold.topbar-button.pe-none.text-uppercase.mb-0`)**:
     - Displays the current page title in uppercase (e.g., `WELCOME!`, `PRODUCT LIST`, `ORDERS LIST`, `ORDER DETAILS`, `CREATE PRODUCT`, `CATEGORIES LIST`, `SETTINGS`).
     - **Important**: Unlike traditional templates that put a `<div class="page-title-box">` inside `.page-content`, Larkon places the primary page heading **directly inside the Topbar left cluster**!
- **Right Cluster (`.d-flex.align-items-center.gap-1`)**:
  1. **Light/Dark Mode Toggle (`button#light-dark-mode.topbar-button`)**:
     - Icon: `<iconify-icon icon="solar:moon-bold-duotone" class="fs-24 align-middle"></iconify-icon>`
     - Bound by `ThemeLayout.initSwitchListener()` in `app.js` to toggle `data-bs-theme` between `light` and `dark`.
  2. **Notifications Dropdown (`.dropdown.topbar-item`)**:
     - Button ID: `page-header-notifications-dropdown`
     - Icon: `solar:bell-bing-bold-duotone` (`fs-24`)
     - Unread Badge: `<span class="position-absolute topbar-badge fs-10 translate-middle badge bg-danger rounded-pill">3</span>`
     - Menu: `.dropdown-menu.py-0.dropdown-lg.dropdown-menu-end` with `data-simplebar style="max-height: 280px;"`.
  3. **Theme Settings Button (`button#theme-settings-btn.topbar-button`)**:
     - Opens `#theme-settings-offcanvas` (`data-bs-toggle="offcanvas"`).
     - Icon: `solar:settings-bold-duotone` (`fs-24`).
  4. **Activity Stream Button**:
     - Opens `#theme-activity-offcanvas` (`data-bs-toggle="offcanvas"`).
     - Icon: `solar:clock-circle-bold-duotone` (`fs-24`).
  5. **User Profile Dropdown (`#page-header-user-dropdown`)**:
     - Trigger: `<img class="rounded-circle" width="32" src="..." alt="avatar">`
     - Menu items: Welcome header (`h6.dropdown-header`), Profile (`bx-user-circle`), Settings/Help, Lock Screen (`bx-lock`), Divider, Logout (`a.dropdown-item.text-danger` with `bx-log-out`).
  6. **Global Search Form (`form.app-search.d-none.d-md-block.ms-2`)**:
     - `<input type="search" class="form-control" placeholder="Search...">`
     - `<iconify-icon icon="solar:magnifer-linear" class="search-widget-icon"></iconify-icon>`

---

## 3. Sidebar Navigation Specification (`div.main-nav`)

### 3.1 Structural Anatomy
```html
<div class="main-nav">
    <!-- 1. Brand Logo Box -->
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

    <!-- 2. Pin / Unpin Hover Sidebar Toggle -->
    <button type="button" class="button-sm-hover" aria-label="Show Full Sidebar">
        <iconify-icon icon="solar:double-alt-arrow-right-bold-duotone" class="button-sm-hover-icon"></iconify-icon>
    </button>

    <!-- 3. Scrollable Menu Container -->
    <div class="scrollbar" data-simplebar>
        <ul class="navbar-nav" id="navbar-nav">
            <!-- Menu Titles & Nav Items -->
        </ul>
    </div>
</div>
```

### 3.2 Sidebar Menu Item Markup Patterns

#### Section Title
```html
<li class="menu-title">General</li>
<!-- Subsequent section titles use mt-2: -->
<li class="menu-title mt-2">Users</li>
```

#### Single Direct Link Item
```html
<li class="nav-item">
    <a class="nav-link" href="{{ route('admin.dashboard') }}">
        <span class="nav-icon">
            <iconify-icon icon="solar:widget-5-bold-duotone"></iconify-icon>
        </span>
        <span class="nav-text"> Dashboard </span>
    </a>
</li>
```

#### Collapsible Parent Item with Sub-Menu
```html
<li class="nav-item">
    <a class="nav-link menu-arrow" href="#sidebarProducts" data-bs-toggle="collapse" role="button" aria-expanded="false" aria-controls="sidebarProducts">
        <span class="nav-icon">
            <iconify-icon icon="solar:t-shirt-bold-duotone"></iconify-icon>
        </span>
        <span class="nav-text"> Products </span>
    </a>
    <div class="collapse" id="sidebarProducts">
        <ul class="nav sub-navbar-nav">
            <li class="sub-nav-item">
                <a class="sub-nav-link" href="{{ route('admin.products.index') }}">List</a>
            </li>
            <li class="sub-nav-item">
                <a class="sub-nav-link" href="{{ route('admin.products.grid') }}">Grid</a>
            </li>
            <li class="sub-nav-item">
                <a class="sub-nav-link" href="{{ route('admin.products.create') }}">Create</a>
            </li>
        </ul>
    </div>
</li>
```

### 3.3 Automatic Active-Link Highlighting (`ThemeLayout.initVerticalMenu`)
In `app.js` (lines 738–767), `ThemeLayout.initVerticalMenu()` automatically compares every `.navbar-nav a` `href` against `window.location.href.split(/[?#]/)[0]`. When matched:
1. Adds `.active` to the `<a>` and its parent `<li>`.
2. Walks up `.closest('.collapse')`, adds `.show` to the collapse `<div>`, and adds `.active` + `aria-expanded="true"` to the parent `.nav-link.menu-arrow`.
3. Smooth-scrolls `.main-nav .simplebar-content-wrapper` so the active link is centered in the viewport.
- **Blade Enhancement Tip**: For dynamic detail/edit routes (e.g., `/admin/products/1/edit` which may not appear as a literal link in the sidebar), also apply server-side `request()->routeIs('admin.products.*') ? 'active' : ''` and `.show` on `#sidebarProducts`.

---

## 4. Curated Sidebar Navigation Tree for The Everbloom

Replace the 100+ demo links in `index.html` with this clean, domain-aligned sidebar menu for **The Everbloom**:

| Section (`li.menu-title`) | Menu Item | Icon (`iconify-icon`) | Sub-Links / Route Targets |
| :--- | :--- | :--- | :--- |
| **GENERAL** | **Dashboard** | `solar:widget-5-bold-duotone` | Direct link → `admin.dashboard` (`/admin`) |
| | **Products** (`#sidebarProducts`) | `solar:bottle-bold-duotone` *(or `solar:t-shirt-bold-duotone`)* | • Product List (`admin.products.index`)<br>• Product Grid (`admin.products.grid`)<br>• Create Product (`admin.products.create`) |
| | **Categories** (`#sidebarCategory`) | `solar:clipboard-list-bold-duotone` | • Category List (`admin.categories.index`)<br>• Create Category (`admin.categories.create`) |
| | **Attributes** (`#sidebarAttributes`) | `solar:confetti-minimalistic-bold-duotone` | • Botanical Attributes (`admin.attributes.index`)<br>• Add Attribute (`admin.attributes.create`) |
| | **Inventory** (`#sidebarInventory`) | `solar:box-bold-duotone` | • Warehouse Stock (`admin.inventory.index`)<br>• Received Batches (`admin.inventory.received`) |
| | **Orders** (`#sidebarOrders`) | `solar:bag-smile-bold-duotone` | • All Orders (`admin.orders.index`)<br>• Order Details (`admin.orders.show`) |
| | **Invoices** (`#sidebarInvoice`) | `solar:bill-list-bold-duotone` | • Invoice List (`admin.invoices.index`)<br>• Create Invoice (`admin.invoices.create`) |
| | **Settings** | `solar:settings-bold-duotone` | Direct link → `admin.settings.index` (`/admin/settings`) |
| **CUSTOMERS & ACCESS** | **Customers** (`#sidebarCustomers`) | `solar:users-group-two-rounded-bold-duotone` | • Customer List (`admin.customers.index`)<br>• Customer Details (`admin.customers.show`) |
| | **Roles & Staff** (`#sidebarRoles`) | `solar:user-speak-rounded-bold-duotone` | • Roles List (`admin.roles.index`)<br>• Permissions (`admin.permissions.index`) |
| | **Admin Profile** | `solar:user-circle-bold-duotone` | Direct link → `admin.profile.index` (`/admin/profile`) |
| **MARKETING & REVIEWS** | **Coupons** (`#sidebarCoupons`) | `solar:leaf-bold-duotone` | • Coupon List (`admin.coupons.index`)<br>• Create Coupon (`admin.coupons.create`) |
| | **Reviews** | `solar:chat-square-like-bold-duotone` | Direct link → `admin.reviews.index` (`/admin/reviews`) |
| **STOREFRONT** | **View Live Store** | `solar:shop-bold-duotone` | Direct link → `home` (`/`, `target="_blank"`) |

---

## 5. Standalone Authentication Layout (`layouts/admin-auth.blade.php`)

Extracted from `auth-signin.html`, `auth-password.html`, and `auth-lock-screen.html`:
- `<html lang="en" class="h-100">` and `<body class="h-100">` (no `.wrapper`, `.topbar`, or `.main-nav`).
- Full-height flex container: `<div class="d-flex flex-column h-100 p-3"><div class="d-flex flex-column flex-grow-1"><div class="row h-100">`
- **Left Column (`col-xxl-7`)**: Centered form container (`col-lg-6 py-lg-5`) with `.auth-logo.mb-4`, `h2.fw-bold.fs-24`, `p.text-muted`, and `form.authentication-form`.
- **Right Column (`col-xxl-5 d-none d-xxl-flex`)**: Full-height visual card (`card h-100 mb-0 overflow-hidden`) displaying an editorial botanical image (`img.w-100.h-100.object-fit-cover`).
