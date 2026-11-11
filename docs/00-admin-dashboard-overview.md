# 00 — Admin Dashboard Overview & Frontend Architecture Blueprint

> **AI Agent Context Header**
> - **Project**: The Everbloom (Botanical Skincare E-Commerce — Laravel 11)
> - **Reference Template Source**: `docs/admin-dashboard/` (Larkon Bootstrap 5.3.3 Responsive Admin Dashboard Template)
> - **Purpose of This Document**: Master index, scope triage (what to extract vs. ignore from `docs/admin-dashboard/`), dual-stack CSS/JS coexistence strategy, and target Laravel Blade/Route architecture.

---

## 1. Documentation Suite Map

When developing or modifying the Admin Dashboard for **The Everbloom**, read these specification files in `/docs` instead of scanning the 123 raw HTML files inside `docs/admin-dashboard/`:

| Doc File | Scope & Contents |
| :--- | :--- |
| [`00-admin-dashboard-overview.md`](./00-admin-dashboard-overview.md) | **Master Index**: Template triage matrix, Laravel integration strategy, asset pipeline, and route/view architecture. |
| [`01-design-system-and-tokens.md`](./01-design-system-and-tokens.md) | **Design Tokens**: CSS custom properties (`:root`, dark mode, menu/topbar themes), typography (`Hanken Grotesk` + `Play`), color palette, utility classes (`.bg-soft-*`, `.btn-soft-*`, `.badge-soft-*`, `.avatar-*`), and Iconify/Boxicons reference. |
| [`02-layout-and-navigation-spec.md`](./02-layout-and-navigation-spec.md) | **Master Shell & Navigation**: Complete DOM hierarchy for `.wrapper`, `.topbar`, `.main-nav` sidebar, offcanvas drawers, `config.js` state persistence, and curated Everbloom navigation tree. |
| [`03-ui-components-and-patterns.md`](./03-ui-components-and-patterns.md) | **Reusable UI Component Contracts**: Blade-ready HTML blueprints for KPI stat cards, data tables, status badges, 5-step order progress bars, Dropzone uploaders, Choices.js selects, timelines, and modals. |
| [`04-page-by-page-specifications.md`](./04-page-by-page-specifications.md) | **Screen Specifications & Domain Mapping**: Page-by-page breakdown of every required admin view mapped directly to The Everbloom's data structures (`products`, `categories`, `orders`, `customers`, `coupons`, `settings`). |

---

## 2. Dual-Stack Frontend Coexistence Strategy (Storefront vs. Admin)

The Everbloom project uses two completely separate frontend styling paradigms that **must never collide**:

| Layer | CSS Framework | Layout Entry Point | Asset Location | Route Prefix |
| :--- | :--- | :--- | :--- | :--- |
| **Storefront** (Public & Customer Account) | **Tailwind CSS v3** + Alpine.js (via Vite) | `resources/views/layouts/app.blade.php`<br>`resources/views/layouts/account.blade.php`<br>`resources/views/layouts/checkout.blade.php` | `resources/css/app.css`<br>`resources/js/app.js` | `/`, `/shop/*`, `/cart`, `/checkout`, `/account/*` |
| **Admin Dashboard** (Back-Office) | **Bootstrap 5.3.3** (Larkon Precompiled CSS/JS) | `resources/views/layouts/admin.blade.php`<br>`resources/views/layouts/admin-auth.blade.php` | `public/admin/assets/css/*`<br>`public/admin/assets/js/*`<br>`public/admin/assets/vendor/*`<br>`public/admin/assets/images/*` | `/admin`, `/admin/*` |

### Critical Isolation Rules for AI Developers
1. **Never load `@vite(['resources/css/app.css'])` inside `layouts/admin.blade.php`**, because Tailwind's Preflight reset conflicts with Bootstrap 5.3.3 base styles and Larkon's `.wrapper`, `.topbar`, and `.main-nav` rules.
2. **Copy Static Assets from `docs/admin-dashboard/assets/` to `public/admin/assets/`** when scaffolding the admin panel, and reference them via `{{ asset('admin/assets/...') }}`.
3. **Keep Admin Blade Components Isolated** inside `resources/views/components/admin/` and views inside `resources/views/admin/`.

---

## 3. Template Triage: What We Need vs. What We Ignore from `docs/admin-dashboard/`

`docs/admin-dashboard/` contains **123 HTML files**. For **The Everbloom** (a single-brand botanical skincare store), we only extract **26 core templates** and exclude multi-vendor seller pages, generic SaaS apps, and UI kitchen-sink demos.

### 3.1 Core Templates to Extract & Adapt (Priority 1 — MVP Admin)

| Admin Module | Source File in `docs/admin-dashboard/` | Target Blade View | Purpose in The Everbloom |
| :--- | :--- | :--- | :--- |
| **Layout Shell** | `index.html` (lines 1–1245, 1760–1798) | `layouts/admin.blade.php` | Master wrapper, Topbar, Sidebar, Theme Settings Offcanvas, Activity Offcanvas, Footer |
| **Auth Shell** | `auth-signin.html`, `auth-password.html`, `auth-lock-screen.html` | `layouts/admin-auth.blade.php`<br>`admin/auth/login.blade.php`<br>`admin/auth/forgot-password.blade.php` | Split-screen 2-column admin sign-in & password reset |
| **Dashboard** | `index.html` (lines 1245–1760) | `admin/dashboard/index.blade.php` | Store KPIs, Performance ApexChart, Conversions RadialBar, Recent Orders |
| **Products** | `product-list.html` | `admin/products/index.blade.php` | Skincare catalogue table with thumbnail, sizes, price, stock, rating, actions |
| **Products** | `product-grid.html` | `admin/products/grid.blade.php` | Visual card grid with sidebar filter for botanical products |
| **Products** | `product-details.html` | `admin/products/show.blade.php` | Admin product preview, variant stock breakdown, review summary |
| **Products** | `product-add.html`, `product-edit.html` | `admin/products/create.blade.php`<br>`admin/products/edit.blade.php` | Live preview card + Dropzone image uploader + product/ingredients/variant/pricing form |
| **Categories** | `category-list.html`, `category-add.html`, `category-edit.html` | `admin/categories/index.blade.php`<br>`admin/categories/create.blade.php`<br>`admin/categories/edit.blade.php` | Face Care, Body Care, Serums, Moisturizers, Cleansers, Sunscreens management |
| **Orders** | `orders-list.html` | `admin/orders/index.blade.php` | 8-card order status summary + filterable order list (`#EVB-*`) |
| **Orders** | `order-detail.html` | `admin/orders/show.blade.php` | 5-step progress tracker, line items, order timeline, customer/shipping/payment sidebar |
| **Inventory** | `inventory-warehouse.html`, `inventory-received-orders.html` | `admin/inventory/index.blade.php`<br>`admin/inventory/received.blade.php` | Variant SKU stock levels (`in_stock`, `low_stock`, `out_of_stock`) & restock batches |
| **Customers** | `customer-list.html`, `customer-detail.html` | `admin/customers/index.blade.php`<br>`admin/customers/show.blade.php` | Customer KPI cards, directory table, customer profile, saved addresses & order history |
| **Coupons** | `coupons-list.html`, `coupons-add.html` | `admin/coupons/index.blade.php`<br>`admin/coupons/create.blade.php` | Promo code management (`WELCOME10`, `BLOOM10`, `FREESHIP`, `BOTANICAL20`) |
| **Reviews** | `pages-review.html` | `admin/reviews/index.blade.php` | Customer product review moderation & star ratings |
| **Invoices** | `invoice-list.html`, `invoice-details.html`, `invoice-add.html` | `admin/invoices/index.blade.php`<br>`admin/invoices/show.blade.php` | Printable order invoices & billing records |
| **Settings** | `settings.html`, `pages-profile.html` | `admin/settings/index.blade.php`<br>`admin/profile/index.blade.php` | Store metadata, announcement bar, shipping thresholds (`$75`), tax & review toggles |

### 3.2 Phase 2 / Optional Templates

| Module | Source Files | When to Use |
| :--- | :--- | :--- |
| **Attributes** | `attributes-list.html`, `attributes-add.html`, `attributes-edit.html` | When dynamic product attributes (e.g., Skin Type, Bottle Volume, Key Botanical Active) are stored in DB tables. |
| **Roles & Permissions** | `role-list.html`, `role-add.html`, `role-edit.html`, `pages-permissions.html` | When multi-staff RBAC (Admin, Fulfillment Manager, Support Concierge) is implemented. |
| **Purchases** | `purchase-list.html`, `purchase-order.html`, `purchase-returns.html` | When raw botanical ingredient / packaging supplier purchase orders are tracked. |

### 3.3 Excluded Demo Bloat (Do Not Copy into Production Views)
- **Duplicate/Legacy E-Commerce Demos**: `apps-ecommerce-*.html` (Larkon includes two sets of e-commerce pages; we standardize on the newer top-level `product-*.html`, `category-*.html`, `orders-list.html`, `customer-*.html` series).
- **Multi-Vendor Seller Pages**: `seller-list.html`, `seller-details.html`, `seller-add.html`, `seller-edit.html` (The Everbloom is a single-brand D2C store, not a multi-vendor marketplace).
- **Admin Cart/Checkout**: `order-cart.html`, `order-checkout.html` (Handled by the storefront).
- **Generic Workspace Apps**: `apps-chat.html`, `apps-email.html`, `apps-calendar.html`, `apps-todo.html`.
- **UI Kitchen Sink & Documentation Demos**: `ui-*.html` (22 files), `forms-*.html` (11 files), `charts-apex-*.html` (16 files), `extended-*.html` (5 files), `tables-*.html` (2 files), `icons-*.html` (2 files), `maps-*.html` (2 files). *Note: Use these only as component snippet references, already distilled in `03-ui-components-and-patterns.md`.*

---

## 4. Required Asset Copy Manifest

When initializing the Admin Dashboard frontend, copy only these directories from `docs/admin-dashboard/assets/` into `public/admin/assets/`:

```text
public/admin/assets/
├── css/
│   ├── vendor.min.css          # Required on ALL admin pages (Simplebar, Choices, Flatpickr, Dropzone, etc.)
│   ├── icons.min.css           # Required on ALL admin pages (Boxicons font definitions)
│   └── app.min.css             # Required on ALL admin pages (Bootstrap 5.3.3 + Larkon custom styles)
├── fonts/                      # Required by icons.min.css (Boxicons woff/woff2/ttf)
├── images/                     # Logos, avatars, card icons, placeholder images
├── js/
│   ├── config.js               # Required in <head> on ALL admin pages (sets html[data-*] attributes before paint)
│   ├── vendor.js               # Required before </body> on ALL admin pages (Bootstrap bundle, Simplebar, Iconify, Choices, Flatpickr, ApexCharts, Prism, Toastify, etc.)
│   ├── app.js                  # Required before </body> on ALL admin pages (Components, FormValidation, FormAdvanced, ThemeLayout)
│   └── pages/                  # Page-specific scripts (dashboard.js, ecommerce-product-details.js, coupons-add.js, etc.)
└── vendor/
    ├── jsvectormap/            # Only if world map widget is used on dashboard
    ├── gridjs/                 # Only if Grid.js client-side data tables are used
    └── quill/                  # Only if rich-text editors are used
```

---

## 5. Target Laravel Route Architecture (`routes/admin.php`)

```php
Route::prefix('admin')->name('admin.')->group(function () {
    // Auth
    Route::get('/login', [AdminAuthController::class, 'login'])->name('login');
    Route::get('/forgot-password', [AdminAuthController::class, 'forgotPassword'])->name('password.request');

    // Dashboard
    Route::get('/', [AdminDashboardController::class, 'index'])->name('dashboard');

    // Catalogue: Products & Categories
    Route::resource('products', AdminProductController::class);
    Route::get('products-grid', [AdminProductController::class, 'grid'])->name('products.grid');
    Route::resource('categories', AdminCategoryController::class);

    // Fulfillment: Orders, Inventory & Invoices
    Route::resource('orders', AdminOrderController::class)->only(['index', 'show', 'update']);
    Route::get('inventory', [AdminInventoryController::class, 'index'])->name('inventory.index');
    Route::resource('invoices', AdminInvoiceController::class)->only(['index', 'show', 'create']);

    // Customers, Reviews & Marketing
    Route::resource('customers', AdminCustomerController::class)->only(['index', 'show']);
    Route::resource('coupons', AdminCouponController::class)->only(['index', 'create', 'store', 'destroy']);
    Route::get('reviews', [AdminReviewController::class, 'index'])->name('reviews.index');

    // Configuration & Access
    Route::get('settings', [AdminSettingController::class, 'index'])->name('settings.index');
    Route::get('profile', [AdminProfileController::class, 'index'])->name('profile.index');
    Route::resource('roles', AdminRoleController::class)->only(['index', 'create', 'edit']);
});
```
