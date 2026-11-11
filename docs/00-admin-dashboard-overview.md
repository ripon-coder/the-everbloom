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

### 3.1 Core Templates to Extract & Adapt (Priority 1 — MVP Admin — Implemented)

| Admin Module | Source File in `docs/admin-dashboard/` | Implemented Blade View | Controller (`app/Http/Controllers/Admin/`) | Purpose in The Everbloom |
| :--- | :--- | :--- | :--- | :--- |
| **Layout Shell** | `index.html` (lines 1–1245, 1760–1798) | `layouts/admin.blade.php`<br>`components/admin/topbar.blade.php`<br>`components/admin/sidebar.blade.php`<br>`components/admin/offcanvas-drawers.blade.php`<br>`components/admin/footer.blade.php` | — | Master wrapper, Topbar, Sidebar, Theme Settings Offcanvas, Activity Offcanvas, Footer |
| **Auth Shell** | `auth-signin.html`, `auth-password.html` | `layouts/admin-auth.blade.php`<br>`admin/auth/login.blade.php` | `AdminAuthController` | Split-screen 2-column admin sign-in & authentication |
| **Dashboard** | `index.html` (lines 1245–1760) | `admin/dashboard/index.blade.php` | `AdminDashboardController` | Store KPIs, Performance ApexChart, Conversions RadialBar, World Map, Top Formulations, Recent Orders |
| **Products (List)** | `product-list.html` | `admin/products/index.blade.php` | `AdminProductController@index` | Skincare catalogue table with category/search filters, thumbnails, sizes, SKU, price, stock, rating, actions |
| **Products (Create / Edit)** | `product-add.html`, `product-edit.html` | `admin/products/create.blade.php` *(handles both Create & Edit via `$isEdit`)* | `AdminProductController@create`<br>`AdminProductController@edit` | Sticky live preview card + Dropzone image uploader + botanical INCI/ritual/variant/pricing form |
| **Categories** | `category-list.html`, `category-add.html`, `category-edit.html` | `admin/categories/index.blade.php`<br>`admin/categories/create.blade.php` *(Create & Edit via `$isEdit`)* | `AdminCategoryController` | 4 featured category hero cards + full categories table & form |
| **Orders (List)** | `orders-list.html` | `admin/orders/index.blade.php` | `AdminOrderController@index` | 4 status counter cards (`All`, `Processing`, `Dispatched`, `Delivered`) + filterable order list (`#EVB-*`) |
| **Orders (Detail)** | `order-detail.html` | `admin/orders/show.blade.php` | `AdminOrderController@show` | 5-step animated progress tracker, line items, courier tracking, customer/shipping/payment sidebar |
| **Inventory** | `inventory-warehouse.html` | `admin/inventory/index.blade.php` | `AdminInventoryController@index` | Batch stock health KPI cards + SKU/Lot# stock levels (`In Stock`, `Low Stock`, `Out of Stock`) |
| **Customers** | `customer-list.html`, `customer-detail.html` | `admin/customers/index.blade.php`<br>`admin/customers/show.blade.php` | `AdminCustomerController` | Client metric cards, directory table, VIP client profile, saved addresses & order history |
| **Coupons** | `coupons-list.html`, `coupons-add.html` | `admin/coupons/index.blade.php`<br>`admin/coupons/create.blade.php` | `AdminCouponController` | Promo code management (`WELCOME10`, `BLOOM10`, `FREESHIP`, `BOTANICAL20`) & creation form |
| **Reviews** | `pages-review.html` | `admin/reviews/index.blade.php` | `AdminReviewController` | Customer product review moderation, star ratings & approval status toggle |
| **Invoices** | `invoice-list.html`, `invoice-details.html` | `admin/invoices/index.blade.php`<br>`admin/invoices/show.blade.php` | `AdminInvoiceController` | Billing directory & printable branded botanical skincare invoice |
| **Settings** | `settings.html` | `admin/settings/index.blade.php` | `AdminSettingController` | Brand identity, announcement bar toggle, shipping thresholds (`$75`), return window & studio contact |

### 3.2 Phase 2 / Optional Templates

| Module | Source Files | When to Use |
| :--- | :--- | :--- |
| **Products Grid / Detail** | `product-grid.html`, `product-details.html` | Optional visual card grid (`admin/products/grid.blade.php`) or read-only admin inspection screen (`admin/products/show.blade.php`). |
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

## 5. Laravel Route Architecture (`routes/admin.php` & `bootstrap/app.php`)

All admin routes are isolated in `routes/admin.php` (kept completely separate from `routes/web.php`) and registered in `bootstrap/app.php` via Laravel 11's `withRouting(then: ...)` callback:

### 5.1 Route Registration (`bootstrap/app.php`)
```php
return Application::configure(basePath: dirname(__DIR__))
    ->withRouting(
        web: __DIR__.'/../routes/web.php',
        commands: __DIR__.'/../routes/console.php',
        health: '/up',
        then: function () {
            Route::middleware('web')
                ->group(base_path('routes/admin.php'));
        },
    )
```

### 5.2 Active Route Definitions (`routes/admin.php`)
```php
// Admin Authentication
Route::get('/admin/login', [AdminAuthController::class, 'showLogin'])->name('admin.login');
Route::post('/admin/login', [AdminAuthController::class, 'login']);
Route::post('/admin/logout', [AdminAuthController::class, 'logout'])->name('admin.logout');

// Authenticated Admin Console Area
Route::prefix('admin')->name('admin.')->group(function () {
    // BACKEND: Add 'auth:admin' middleware here

    // Dashboard
    Route::get('/', [AdminDashboardController::class, 'index'])->name('dashboard');

    // Products Management (index, create, store, show, edit, update, destroy)
    Route::resource('products', AdminProductController::class);

    // Categories Management (index, create, store, show, edit, update, destroy)
    Route::resource('categories', AdminCategoryController::class);

    // Orders Management
    Route::get('orders', [AdminOrderController::class, 'index'])->name('orders.index');
    Route::get('orders/{id}', [AdminOrderController::class, 'show'])->name('orders.show');
    Route::post('orders/{id}/status', [AdminOrderController::class, 'updateStatus'])->name('orders.updateStatus');

    // Inventory & Batch Tracking
    Route::get('inventory', [AdminInventoryController::class, 'index'])->name('inventory.index');

    // Invoices
    Route::get('invoices', [AdminInvoiceController::class, 'index'])->name('invoices.index');
    Route::get('invoices/{id}', [AdminInvoiceController::class, 'show'])->name('invoices.show');

    // Customers
    Route::get('customers', [AdminCustomerController::class, 'index'])->name('customers.index');
    Route::get('customers/{id}', [AdminCustomerController::class, 'show'])->name('customers.show');

    // Coupons & Promotions
    Route::resource('coupons', AdminCouponController::class)->only(['index', 'create', 'store']);

    // Customer Reviews Moderation
    Route::get('reviews', [AdminReviewController::class, 'index'])->name('reviews.index');
    Route::post('reviews/{id}/toggle', [AdminReviewController::class, 'toggleStatus'])->name('reviews.toggleStatus');

    // Store Settings
    Route::get('settings', [AdminSettingController::class, 'index'])->name('settings.index');
    Route::post('settings', [AdminSettingController::class, 'update'])->name('settings.update');
});
```
