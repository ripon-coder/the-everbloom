# 05 — Complete Frontend & Admin System Analysis

> **AI Agent Context Header**
> - **Project**: The Everbloom (Botanical Skincare E-Commerce — Laravel 12, PHP 8.2+)
> - **Current Project State**: 100% UI-complete Prototype (Storefront + `/admin` Console). Both layers currently read from static PHP arrays in `config/demo/*.php` and simulate mutations via flash messages or `localStorage`.
> - **Purpose of This Document**: Provide an exhaustive architectural analysis of the existing Storefront frontend, Alpine.js state contracts, `/admin` panel controllers/views, and every `// BACKEND:` integration point before writing backend code.

---

## 1. Executive Summary & Dual-Stack Architecture

**The Everbloom** is a direct-to-consumer (D2C) botanical skincare e-commerce platform built on **Laravel 12**. It is architected with two strictly isolated presentation layers:

| Layer | Tech Stack | Layouts | Entry Assets | Routes File | Current Data Source |
| :--- | :--- | :--- | :--- | :--- | :--- |
| **Storefront & Customer Portal** | Tailwind CSS v3.4 + Alpine.js v3.14 (`@alpinejs/collapse`, `focus`, `intersect`) via Vite 6 | `layouts/app.blade.php`<br>`layouts/checkout.blade.php`<br>`layouts/account.blade.php` | `resources/css/app.css`<br>`resources/js/app.js` | `routes/web.php` | `config/demo/*.php` + Browser `localStorage` |
| **Admin Console (`/admin`)** | Bootstrap 5.3.3 (Larkon Theme) + ApexCharts + Iconify + Boxicons | `layouts/admin.blade.php`<br>`layouts/admin-auth.blade.php` | `public/admin/assets/css/*`<br>`public/admin/assets/js/*` | `routes/admin.php` | `config/demo/*.php` + Controller demo arrays |

> [!IMPORTANT]
> **CSS/JS Isolation Rule**: Never load `@vite(['resources/css/app.css', 'resources/js/app.js'])` in `layouts/admin*.blade.php`, and never load `public/admin/assets/*` in `layouts/app.blade.php`, `layouts/checkout.blade.php`, or `layouts/account.blade.php`.

---

## 2. Storefront Frontend Analysis (`routes/web.php` & `resources/views/frontend/`)

### 2.1 Storefront Route & View Inventory

All storefront routes currently reside in `routes/web.php`. Closure routes render views directly using `config('demo.*')`, while customer account routes point to `App\Http\Controllers\AccountController`.

| Route URI | Named Route | Handler / Controller | Blade View | Current Behavior & Data Dependencies |
| :--- | :--- | :--- | :--- | :--- |
| `GET /` | `home` | Closure (`routes/web.php`) | `frontend.home` | Filters `config('demo.products')` (`is_featured`, `best_seller`), loads `config('demo.categories')`, `config('demo.testimonials')`, `config('demo.site.trust_badges')`. |
| `GET /styleguide` | `styleguide` | Closure (`routes/web.php`) | `frontend.styleguide` | Living design system reference (colors, typography, buttons, inputs). |
| `GET /shop` | `shop.index` | Closure (`routes/web.php`) | `frontend.shop.index` | Displays all 16 products + 6 categories. Includes Alpine UI state for mobile filter drawer, sort sheet, and desktop sort dropdown (`featured`, `price-asc`, `price-desc`, `rating`, `newest`), plus checkbox UI for Skin Ritual Goals & Availability. |
| `GET /shop/category/{slug}` | `shop.category` | Closure (`routes/web.php`) | `frontend.shop.category` | Looks up category by `slug` in `config('demo.categories')` (404 if missing) and filters `config('demo.products')` where `category_slug === $slug`. |
| `GET /shop/product/{slug}` | `shop.product` | Closure (`routes/web.php`) | `frontend.shop.product` | Looks up product by `slug` in `config('demo.products')` (404 if missing). Computes 4 related products (same category first, fallback to others). Loads reviews from `config('demo.reviews')` and rating breakdown from `config('demo.site.rating_breakdown')`. |
| `GET /cart` | `cart.index` | Closure (`routes/web.php`) | `frontend.cart.index` | Driven by Alpine `$store.cart` (`localStorage['everbloom_cart']`). Shows empty state with 4 bestsellers, or cart table + order summary + free shipping progress bar + 4 "Pairs Thoughtfully With" products. |
| `GET /checkout` | `checkout.index` | Closure (`routes/web.php`) | `frontend.checkout.index` | Uses `layouts.checkout`. Driven by `checkoutComponent()` Alpine component + `$store.cart`. **Must be updated** so Country is fixed to **Bangladesh**, with cascading **District -> Thana -> Area** selects populated from seeded database tables (`districts`, `thanas`, `areas` where `is_active = true`). |
| `GET /wishlist` | `wishlist` | Closure (`routes/web.php`) | `frontend.pages.wishlist` | Uses `layouts.account`. Driven by Alpine `$store.wishlist` (`localStorage['everbloom_wishlist']`). Displays saved product cards with "Add to Bag" and "Remove". |
| `GET /about` | `about` | Closure (`routes/web.php`) | `frontend.pages.about` | Static brand story, sourcing philosophy, and editorial imagery. |
| `GET /contact` | `contact` | Closure (`routes/web.php`) | `frontend.pages.contact` | Displays contact info from `config('demo.site.contact')` and an Alpine-driven inquiry form (`name`, `email`, `message`) that currently toggles `sent = true` client-side. |
| `GET /faq` | `faq` | Closure (`routes/web.php`) | `frontend.pages.faq` | Renders categorized FAQ accordions from `config('demo.faq')`. |
| `GET /privacy-policy` | `privacy` | Closure (`routes/web.php`) | `frontend.pages.privacy-policy` | Static legal page. |
| `GET /terms-and-conditions` | `terms` | Closure (`routes/web.php`) | `frontend.pages.terms-and-conditions` | Static legal page. |
| `GET /shipping-and-returns` | `shipping` | Closure (`routes/web.php`) | `frontend.pages.shipping-and-returns` | Shipping rates and return policy page using `config('demo.site.shipping')` and `returns`. |
| `GET /account/login` | `login` | `AccountController@login` | `frontend.account.login` | Client-side validated email/password sign-in form + social login buttons (`config('demo.auth.enabled_providers')`). Currently displays `"Demo only. No one was signed in."` |
| `GET /account/register` | `register` | `AccountController@register` | `frontend.account.register` | Client-side validated registration form (`first_name`, `last_name`, `email`, `password`) + social login buttons. |
| `GET /account/forgot-password` | `password.request` | `AccountController@forgotPassword` | `frontend.account.forgot-password` | Password reset request form (`email`). |
| `GET /account` | `account` | `AccountController@index` | `frontend.account.index` | Uses `layouts.account`. Displays customer welcome, recent 3 orders (`config('demo.account.orders')`), and default shipping address (`config('demo.account.addresses')`). |
| `GET /account/orders` | `account.orders` | `AccountController@orders` | `frontend.account.orders` | Lists customer orders from `config('demo.account.orders')`. Supports `?empty=1` query param to preview empty state. |
| `GET /account/orders/{id}` | `account.order.details` | `AccountController@showOrder` | `frontend.account.order-details` | Displays single order (`#EVB-*`), 4-step progress tracker (`Placed` -> `Processing` -> `Shipped` -> `Delivered`), line items, shipping address, payment method, and summary. |
| `GET /account/addresses` | `account.addresses` | `AccountController@addresses` | `frontend.account.addresses` | Interactive Alpine CRUD UI for customer addresses (Add, Edit, Set Default, Inline Remove Confirmation). **Must be updated** to use **Bangladesh** as fixed country and cascading **District -> Thana -> Area** selects from active database records. |
| `GET /account/profile` | `account.profile` | `AccountController@profile` | `frontend.account.profile` | Personal Info form (`first_name`, `last_name`, `email`, `phone`), Security & Password form (`current_password`, `new_password`, `confirm_password` with live strength checklist), and Delete Account confirmation dialog. |

---

### 2.2 Storefront Reusable Blade Components (`resources/views/components/storefront/`)

All storefront pages compose UI from 22 modular Blade components in `resources/views/components/storefront/`:

| Component | Tag | Role & Data Props |
| :--- | :--- | :--- |
| `announcement-bar.blade.php` | `<x-storefront.announcement-bar />` | Reads `config('demo.site.announcement')` (`enabled`, `message`, `link`, `link_text`). |
| `header.blade.php` | `<x-storefront.header />` | Mobile menu trigger, desktop search trigger (`⌘K`), centered brand logo, wishlist counter badge (`$store.wishlist.count`), account icon, and bag drawer trigger (`$store.cart.count`). |
| `navigation.blade.php` | `<x-storefront.navigation />` | Desktop horizontal nav bar reading `config('demo.navigation.main')`. |
| `mobile-navigation.blade.php` | `<x-storefront.mobile-navigation />` | Slide-over mobile navigation drawer using Alpine `mobileNavigation` component. |
| `bottom-nav.blade.php` | `<x-storefront.bottom-nav />` | Fixed mobile bottom bar (`lg:hidden`) with Home, Shop, Search, Wishlist (badge), and Bag (badge). Hidden on `/checkout*`. |
| `cart-drawer.blade.php` | `<x-storefront.cart-drawer />` | Slide-over shopping bag drawer + **hosts the global JSON `<script>` payloads** (`#everbloom-products-data`, `#everbloom-discounts-data`, `#everbloom-shipping-data`) used by `resources/js/app.js`. |
| `search-modal.blade.php` | `<x-storefront.search-modal />` | Modal search (`Ctrl+K` / `⌘K`) using Alpine `searchModal` and `#everbloom-search-data` JSON script tag. |
| `footer.blade.php` | `<x-storefront.footer />` | Newsletter signup (`<x-storefront.newsletter-form />`), 4 link columns from `config('demo.navigation.footer')`, social links, and copyright. |
| `product-grid.blade.php` | `<x-storefront.product-grid :products="$products" columns="4" />` | Responsive product grid rendering `<x-storefront.product-card />` items. |
| `product-card.blade.php` | `<x-storefront.product-card :product="$product" />` | Product card with `aspect-[4/5]` image, badge, wishlist heart toggle, category label, title link, star rating, price display, and quick "Add to Bag" button. |
| `price-display.blade.php` | `<x-storefront.price-display :price="..." :compare-at-price="..." />` | Formats currency with `.price` class (lining tabular numerals) and strike-through compare price. |
| `rating.blade.php` | `<x-storefront.rating :rating="4.9" :count="128" />` | SVG star rating display with optional review count. |
| `quantity-selector.blade.php` | `<x-storefront.quantity-selector :quantity="1" />` | Accessible `-` / `+` quantity stepper dispatching `qty-change` window event. |
| `wishlist-button.blade.php` | `<x-storefront.wishlist-button :product-id="$id" />` | Heart toggle button bound to `$store.wishlist.has(id)` and `$store.wishlist.toggle(id)`. |
| `add-to-cart-button.blade.php` | `<x-storefront.add-to-cart-button :product="$product" />` | Button calling `$store.cart.add(...)` with loading feedback. |
| `breadcrumbs.blade.php` | `<x-storefront.breadcrumbs :items="[...]" />` | Accessible breadcrumb nav + automatic `BreadcrumbList` Schema.org JSON-LD injection. |
| `section-heading.blade.php` | `<x-storefront.section-heading ... />` | Eyebrow + Playfair heading + description + optional action link. |
| `accordion.blade.php` | `<x-storefront.accordion ... />` | Collapsible FAQ/content accordion item. |
| `button.blade.php` | `<x-storefront.button variant="primary|secondary|tertiary" />` | Renders `<a>` or `<button>` with `.btn-primary`, `.btn-secondary`, or `.btn-tertiary`. |
| `image.blade.php` | `<x-storefront.image :src="..." :source="..." :alt="..." />` | Resolves image path. Currently checks `public_path('images/' . $src)` locally; **must be updated** to support full third-party storage URLs (`https://...`) and `Storage::disk($source)->url($src)` for `.webp` images stored on third-party cloud storage. |
| `pagination.blade.php` | `<x-storefront.pagination />` | Storefront pagination UI component. |
| `social-login-buttons.blade.php` | `<x-storefront.social-login-buttons :providers="..." />` | Google and Apple OAuth button UI for login/register pages. |
| `status-badge.blade.php` | `<x-storefront.status-badge :status="..." />` | Order status badge (`Placed`, `Processing`, `Shipped`, `Delivered`, `Cancelled`). |

---

### 2.3 Frontend JavaScript & Alpine.js State Architecture (`resources/js/app.js`)

Understanding `resources/js/app.js` is critical when connecting the backend:

1. **Embedded JSON Hydration Bridge**:
   - `components/storefront/cart-drawer.blade.php` outputs three `<script type="application/json">` tags on every storefront page:
     - `#everbloom-products-data`: Array of products (used to derive product name, slug, category, image, and variant prices in the cart and wishlist).
     - `#everbloom-discounts-data`: Map of active promo codes (`code => { type, value, label }`).
     - `#everbloom-shipping-data`: `{ free_threshold: 75.00, standard_cost: 6.00 }`.
   - `components/storefront/search-modal.blade.php` currently outputs `#everbloom-search-data` for client-side search.
   - **Backend Implication (Cart/Wishlist vs. TNTSearch)**:
     - For `$store.cart` and `$store.wishlist`, keep `#everbloom-products-data`, `#everbloom-discounts-data`, and `#everbloom-shipping-data` populated via a **View Composer** (from cached DB queries) so cart/wishlist derivation works without breaking existing UI behavior.
     - For **Search (`searchModal` & `/shop` search)**, replace the static client-side array filter with **Laravel Scout + TNTSearch (`teamtnt/laravel-scout-tntsearch-driver`)** via a fast JSON endpoint (`GET /api/search?q=...` or `GET /search?q=...`) and server-side Scout search on `/shop`.

2. **`Alpine.store('cart')` Contract**:
   - Persists **only** `[{ productId, variantId, qty }]` in `localStorage.getItem('everbloom_cart')`.
   - Never trusts prices from `localStorage`; derives `name`, `slug`, `category`, `size`, `price`, `compare_at_price`, `image`, and `lineTotal` dynamically from `#everbloom-products-data`.
   - Enforces `1 <= qty <= 10`.
   - Manages promo code in `localStorage.getItem('everbloom_discount')` and computes `subtotal`, `discountAmount`, `shippingCost`, and `total`.

3. **`Alpine.store('wishlist')` Contract**:
   - Persists `[{ id }]` in `localStorage.getItem('everbloom_wishlist')`.
   - Derives product card fields (`name`, `slug`, `category`, `price`, `image`) from `#everbloom-products-data`.

---

## 3. Admin Panel Analysis (`routes/admin.php` & `app/Http/Controllers/Admin/`)

The `/admin` console is already built with 10 controllers and 15 Blade views using the Larkon Bootstrap 5.3.3 design system.

### 3.1 Admin Controllers, Routes & Current State

| Controller | Routes (`admin.*`) | Blade Views | Current Status & `// BACKEND:` Gap |
| :--- | :--- | :--- | :--- |
| `AdminAuthController` | `GET /admin/login` (`admin.login`)<br>`POST /admin/login`<br>`POST /admin/logout` (`admin.logout`) | `admin.auth.login` | **Mock Auth**: `login()` immediately redirects to `admin.dashboard` without checking credentials; `routes/admin.php` has no `auth` middleware yet. |
| `AdminDashboardController` | `GET /admin` (`admin.dashboard`) | `admin.dashboard.index` | **Operational Overview Only**: Displays common store statistics (`Total Orders`, `Processing / Delivered Counts`, `Total Customers`, `Active SKUs`, `Low Stock Alerts`, `Recent Orders`, `Top Formulations`). Deep accounting/profit ledgers are moved to the dedicated **Financial** menu (`/admin/financial`). |
| `AdminFinancialController` *(New Requirement)* | `GET /admin/financial` (`admin.financial.index`) | `admin.financial.index` | **Dedicated Financial & Profit Analytics Hub**: Displays Revenue, Wholesale COGS, Net Realized Profit, Profit Margin %, Wholesale Batch Outflow, Available Inventory Cost Valuation, Projected Retail Value, ApexCharts Revenue vs. Cost vs. Profit chart, and Product/Order profit breakdown with **Date Range / Period, Category, Vendor, and Order Status filters** using Larkon components from `docs/admin-dashboard/`. |
| `AdminProductController` | `Route::resource('products')` (`index`, `create`, `store`, `edit`, `update`, `destroy`) | `admin.products.index`<br>`admin.products.create` | **3-Step Category + Category-Driven Dynamic Attribute Variant Builder**: Selecting a Category (Parent -> Subcategory -> Child Category) on `/admin/products/create` dynamically displays only the **Attributes assigned to that Category** (`Brand`, `Size`, `Color`, `SPF`, etc.), where each variant has its own price, SKU, stock, and an optional `.webp` variant image. |
| `AdminAttributeController` *(Category-Scoped)* | `Route::resource('attributes')` (`/admin/attributes`) | `admin.attributes.index` | Creates and manages reusable product attributes (`Brand`, `Size`, `Color`, `Scent`, `Finish`, etc.) **linked to specific Categories** (`category_attribute` pivot) and their option values (`attribute_values`). |
| `AdminCategoryController` *(3-Step Hierarchy)* | `Route::resource('categories')` (`index`, `create`, `store`, `edit`, `update`, `destroy`) | `admin.categories.index`<br>`admin.categories.create` | **3-Level Category Hierarchy (Max 3 Steps)**: Supports **Step 1 (Main Category) -> Step 2 (Subcategory) -> Step 3 (Child Category)** via `parent_id` and `level` (`1`, `2`, `3`), plus assigning category-specific variant attributes. |
| `AdminOrderController` | `GET /admin/orders` (`orders.index`)<br>`GET /admin/orders/{id}` (`orders.show`)<br>`POST /admin/orders/{id}/status` (`orders.updateStatus`) | `admin.orders.index`<br>`admin.orders.show` | Reads `config('demo.account.orders')`. Filters by status (`Processing`, `Shipped`, `Delivered`). `updateStatus` redirects back with flash message only. |
| `AdminInventoryController` | `GET /admin/inventory` (`inventory.index`) | `admin.inventory.index` | Maps `config('demo.products')` into inventory rows with `sku`, `stock`, `min_threshold`, `batch_lot`, `avg_buy_price`, `selling_price`, `unit_profit`, and `last_restocked`. |
| `AdminPurchaseController` *(New Requirement)* | `GET /admin/purchases` (`purchases.index`)<br>`GET /admin/purchases/create` (`purchases.create`)<br>`POST /admin/purchases` (`purchases.store`) | `admin.purchases.index`<br>`admin.purchases.create` | **Wholesale Product Buy & Batch Management**: Tracks wholesale purchases by **Vendor Name** and **Batch Code**, calculates **Weighted Average Buy Price using ONLY currently available stock**, increments product stock, and previews per-unit profit margin. |
| `AdminOrderController` *(Profit Requirement)* | `GET /admin/orders` (`orders.index`)<br>`GET /admin/orders/{id}` (`orders.show`) | `admin.orders.index`<br>`admin.orders.show` | When an order is received, displays each item's **Avg Buy Price**, **Selling Price**, and **Line Profit**, plus the order's **Total Wholesale Cost** and **Net Order Profit**. |
| `AdminInvoiceController` | `GET /admin/invoices` (`invoices.index`)<br>`GET /admin/invoices/{id}` (`invoices.show`) | `admin.invoices.index`<br>`admin.invoices.show` | Generates invoice list (`INV-2026-1001+`) from demo orders and renders printable invoice detail view. |
| `AdminCustomerController` | `GET /admin/customers` (`customers.index`)<br>`GET /admin/customers/{id}` (`customers.show`) | `admin.customers.index`<br>`admin.customers.show` | Uses private `getDemoCustomers()` array + demo orders/addresses. |
| `AdminCouponController` | `GET /admin/coupons` (`coupons.index`)<br>`GET /admin/coupons/create` (`coupons.create`)<br>`POST /admin/coupons` (`coupons.store`) | `admin.coupons.index`<br>`admin.coupons.create` | Maps `config('demo.site.demo_discounts')` into table rows. `store()` redirects with flash message only. |
| `AdminReviewController` | `GET /admin/reviews` (`reviews.index`)<br>`POST /admin/reviews/{id}/toggle` (`reviews.toggleStatus`) | `admin.reviews.index` | Uses hardcoded `$reviews` array in controller. `toggleStatus()` redirects with flash message only. |
| `AdminSettingController` | `GET /admin/settings` (`settings.index`)<br>`POST /admin/settings` (`settings.update`) | `admin.settings.index` | Reads `config('demo.site')`. `update()` redirects with flash message only. |

---

## 4. Complete Data Contract Analysis (`config/demo/*.php`)

Every Blade template in both the Storefront and Admin Console expects specific array/object keys. When migrating to Eloquent models, models **must expose these exact attribute names or accessors** (and `toArray()` / JSON serialization formats) so existing Blade templates and Alpine.js scripts work seamlessly.

### 4.1 Product Data Contract (`config/demo/products.php`)
Currently 16 botanical skincare products.
```php
[
    'id' => 1,                                      // int (Primary Key)
    'slug' => 'botanical-radiance-serum',           // string (Unique URL slug)
    'sku' => 'EB-001',                              // string (Optional in config, auto-generated if null)
    'name' => 'Botanical Radiance Serum',           // string
    'subtitle' => null,                             // string|null (Optional editorial subtitle)
    'category' => 'Serums',                         // string (Category display name)
    'category_slug' => 'serums',                    // string (Foreign key / slug reference)
    'price' => 64.00,                               // float (Base / default variant price)
    'compare_at_price' => 78.00,                    // float|null
    'rating' => 4.9,                                // float (Cached average rating 0.0 - 5.0)
    'review_count' => 128,                          // int (Cached approved review count)
    'short_description' => 'A light, concentrated infusion...', // string (Catalogue & SEO summary)
    'description' => 'Thoughtfully formulated...',  // text (Full description)
    'ingredients' => 'Water (Aqua), Camellia...',   // text (Full INCI list)
    'key_ingredients' => ['Elderberry', 'Marula'],  // array|null (Optional featured actives)
    'benefits' => ['Softens skin texture', ...],    // array|null (Optional bullet points)
    'skin_types' => ['Dry', 'Sensitive'],           // array|null (Optional skin type tags)
    'how_to_use' => 'Apply 3 to 4 drops...',        // text (Daily ritual instructions)
    'sizes' => ['30ml / 1.0 fl oz', '50ml / 1.7 fl oz'], // array of variant labels (kept for backward compatibility)
    'variants' => [                                 // array of multi-attribute variant objects
        [
            'id' => 101,
            'title' => '30ml / 1.0 fl oz — Amber Glass',
            'size' => '30ml / 1.0 fl oz — Amber Glass', // alias of title for storefront compatibility
            'attributes' => [                       // dynamic attribute key-value map (Brand, Size, Color, etc.)
                'Brand' => 'The Everbloom',
                'Size' => '30ml / 1.0 fl oz',
                'Color' => 'Amber Glass',
            ],
            'sku' => 'EB-001-30ML-AMB',
            'price' => 64.00,
            'compare_at_price' => 78.00,
            'stock_count' => 25,
            'stock_status' => 'in_stock',           // 'in_stock' | 'low_stock' | 'out_of_stock'
            'image' => 'products/variants/radiance-30ml-amber.webp', // OPTIONAL per-variant WebP image (null if using main product image)
            'image_source' => 's3',                 // 'local' | 's3' | 'r2' | 'spaces' | 'cloudinary'
        ],
    ],
    'stock_count' => 50,                            // int (Used by Admin Inventory/Product form)
    'stock_status' => 'in_stock',                   // 'in_stock' | 'low_stock' | 'out_of_stock'
    'is_featured' => true,                          // bool (Homepage "Featured Botanicals")
    'best_seller' => true,                          // bool (Homepage "Most Loved Rituals")
    'badge' => 'Bestseller',                        // string|null ('Bestseller', 'Signature', 'New', 'Daily Essential', 'Customer Favorite', 'Limited Stock')
    'images' => [                                   // array of WebP paths or resolved URLs
        'products/botanical-radiance-serum-01.webp',
    ],
    'image_source' => 's3',                         // string ('local' for demo assets, or third-party disk name e.g. 's3', 'r2', 'spaces', 'cloudinary')
]
```

### 4.2 Category Data Contract (`config/demo/categories.php`)
Currently 6 categories (`face-care`, `body-care`, `serums`, `moisturizers`, `cleansers`, `sunscreens`).
```php
[
    'id' => 1,
    'slug' => 'face-care',
    'name' => 'Face Care',
    'short_name' => 'Face',
    'tagline' => 'Gentle botanicals formulated to nurture facial skin balance and natural radiance.',
    'description' => 'Thoughtfully formulated botanical skincare for your daily ritual.',
    'image' => 'categories/category-face-care.webp', // WebP path or resolved third-party URL
    'image_source' => 's3',                          // 'local' | 's3' | 'r2' | 'spaces'
    'item_count' => 8,                               // dynamic products_count
]
```

### 4.3 Order & Customer Data Contract (`config/demo/account.php`)
```php
// Customer Profile
[
    'first_name' => 'Eleanor',
    'last_name' => 'Vance',
    'email' => 'eleanor.vance@example.com',
    'phone' => '+1 (503) 555-0192',
    'member_since' => 'March 2024',
]

// Saved Address (Updated for Bangladesh Location Hierarchy)
[
    'id' => 1,
    'is_default' => true,
    'first_name' => 'Eleanor',
    'last_name' => 'Vance',
    'company' => '',
    'country' => 'Bangladesh',       // Always Bangladesh
    'district_id' => 1,              // FK -> districts.id (e.g. Dhaka)
    'district_name' => 'Dhaka',
    'thana_id' => 12,                // FK -> thanas.id (e.g. Gulshan)
    'thana_name' => 'Gulshan',
    'area_id' => 45,                 // FK -> areas.id (e.g. Gulshan 2)
    'area_name' => 'Gulshan 2',
    'address_line1' => 'House 14, Road 71',
    'address_line2' => 'Apt 4B',
    'postal_code' => '1212',
    'phone' => '+880 1711-000000',
]

// Order Record
[
    'id' => 'EVB-9204', // Order Number string used in URLs (/account/orders/EVB-9204 & /admin/orders/EVB-9204)
    'date' => 'November 10, 2026',
    'iso_date' => '2026-11-10',
    'status' => 'Processing',       // 'Placed' | 'Processing' | 'Shipped' | 'Delivered' | 'Cancelled'
    'current_step' => 'Processing', // Used by 4-step / 5-step progress tracker
    'subtotal' => 64.00,
    'shipping' => 0.00,
    'tax' => 0.00,
    'discount' => 0.00,
    'total' => 64.00,
    'items_count' => 1,
    'delivery_method' => 'Complimentary Standard Ground (3–5 business days)',
    'payment_method' => 'Apple Pay (•••• 9840)',
    'tracking_number' => 'Pending assignment',
    'shipping_address' => [ /* same keys as Saved Address */ ],
    'items' => [
        [
            'product_id' => 1,
            'variant_id' => '30ml / 1.0 fl oz',
            'name' => 'Botanical Radiance Serum',
            'variant' => '30ml / 1.0 fl oz',
            'qty' => 1,
            'price' => 64.00,
            'image' => 'products/botanical-radiance-serum-01.jpg',
            'slug' => 'botanical-radiance-serum',
        ],
    ],
]
```

### 4.4 Site Settings, Shipping & Coupons (`config/demo/site.php`)
- **Brand & Currency**: `name`, `tagline`, `url`, `currency` (`code`, `symbol`, `position`, `decimals`).
- **Announcement Bar**: `announcement` (`enabled`, `message`, `link`, `link_text`).
- **Shipping Rules**: `shipping` (`free_shipping_threshold`, `standard_rate`, `express_rate`, `standard_days`, `express_days`) and `shipping_rates` (`standard`, `express`).
- **Returns**: `returns.window_days` (`30`).
- **Contact**: `contact` (`email`, `phone`, `hours`, `address`).
- **Discounts / Coupons**: `demo_discounts` (`CODE => ['type' => 'percentage|shipping|fixed', 'value' => 10, 'label' => '...']`).

---

## 5. Summary of Missing Backend Functionality (What Needs to Be Built)

1. **Database Migrations & Eloquent Models**: Currently only default Laravel migrations (`users`, `cache`, `jobs`) exist. All domain tables (`admins`, `users`, `districts`, `thanas`, `areas`, `categories`, `products`, `product_images`, `product_variants`, `addresses`, `coupons`, `orders`, `order_items`, `invoices`, `reviews`, `settings`, `contact_inquiries`, `newsletter_subscribers`) must be created.
2. **Database Seeder (`DatabaseSeeder` + `BangladeshLocationSeeder`)**:
   - Seed all **Bangladesh Districts, Thanas, and Areas** into `districts`, `thanas`, and `areas` tables.
   - Import all current `config/demo/*.php` records (including default `Admin` and `User` accounts) and build the initial **TNTSearch** index (`php artisan scout:import "App\Models\Product"`).
3. **Bangladesh Location Hierarchy (`Country = Bangladesh` -> `District` -> `Thana` -> `Area`)**:
   - Storefront Checkout (`/checkout`) and Customer Addresses (`/account/addresses`) always use **Bangladesh** as the country and provide cascading selects: **Select District -> Select Thana -> Select Area** (loading only `is_active = true` locations).
   - Admin Console (`/admin/locations`) provides full management for **Districts, Thanas, and Areas** so the admin can **rename** any location, **enable/disable (`is_active` toggle)** delivery coverage for specific Districts/Thanas/Areas, and add new Thanas/Areas.
3. **Dual-Table & Dual-Session Authentication (`users` vs `admins`)**:
   - **Separate Tables & Models**: Storefront customers use the `users` table (`App\Models\User`) with the `web` guard; administrators use a dedicated `admins` table (`App\Models\Admin`) with the `admin` guard.
   - **Separate Browser & Server Sessions**: Admin routes (`/admin*`) and Storefront routes (`/`) must use completely separate session cookies (`everbloom_admin_session` vs `everbloom_session`) via middleware before `StartSession` so logging in or logging out of `/admin` never affects the customer storefront session (and vice versa).
4. **Full-Text Search via TNTSearch (`laravel/scout` + `teamtnt/laravel-scout-tntsearch-driver`)**:
   - Configure Laravel Scout with the `tntsearch` driver storing index files in `storage/search/`.
   - Power both the global storefront search modal (`GET /search`) and catalogue search (`GET /shop?search=...`) using `Product::search($q)`.
5. **Storefront Dynamic Queries & Form Actions**:
   - Replace closure routes in `routes/web.php` with dedicated Storefront Controllers (`HomeController`, `ShopController`, `SearchController`, `CheckoutController`, `ContactController`, `NewsletterController`, `ReviewController`).
   - Connect `/shop` filtering (TNTSearch query, category, skin goals, availability, sort) and pagination.
   - Connect `/checkout` order placement (`POST /checkout`) to persist `orders`, `order_items`, decrement variant/product stock, record coupon usage, generate an `invoice`, and clear `$store.cart`.
   - Connect Customer Account CRUD (`POST/PUT/DELETE /account/addresses`, `PUT /account/profile`, `PUT /account/password`, `DELETE /account`).
6. **Admin Console Full CRUD & Third-Party WebP Media Pipeline**:
   - Connect all `Admin*Controller` classes to real Eloquent models, FormRequest validation, order status transitions, inventory stock adjustments, review moderation toggles, and persistent store settings.
   - **WebP Conversion & Third-Party Storage**: Convert all uploaded images (`products`, `product_variants`, `categories`, `avatars`) to `.webp` format automatically, upload them to the configured third-party cloud storage disk (`s3`, `r2`, `spaces`, etc.), and record the storage provider name in the companion `*_source` (`image_source` / `avatar_source`) database column.
7. **Wholesale Purchase Batches, Available-Stock Weighted Average Cost & Order Profit Tracking**:
   - **Wholesale Buy List & Batch Creation (`/admin/purchases`)**: Admin records wholesale product purchases with **Vendor Name** (`vendors` table), **Batch Code** (`purchase_batches`), and per-product/variant **Buy Quantity & Unit Buy Price** (`purchase_batch_items`).
   - **Available-Stock-Only Average Price Rule**: When a new batch is purchased for an existing product, the product's `avg_buy_price` is recalculated using **only the currently available (`stock_count > 0`) units** plus the newly purchased batch units (already-sold units from past batches do not affect the average price).
   - **Order Received Profit Display (`/admin/orders` & `/admin/orders/{id}`)**: Every order snapshots each product's `unit_buy_price` (`avg_buy_price` at time of sale) and calculates `unit_profit`, `line_profit`, `total_cost`, and `total_profit` (`(subtotal - discount_amount) - total_cost`), displaying exact profit per product and per order in the Admin panel.
8. **3-Step Category Hierarchy & Category-Driven Product Variant Attributes + Optional Variant Image**:
   - **3-Step Category Tree (`level` 1 -> 2 -> 3)**: Categories support up to 3 levels (**Step 1: Root Category -> Step 2: Subcategory -> Step 3: Child Category**). Level 3 categories cannot have further children.
   - **Category-Scoped Attributes**: Attributes (`Brand`, `Size`, `Color`, `SPF`, `Skin Type`, etc.) are assigned to categories via `category_attribute` (with automatic inheritance from parent categories).
   - **Dynamic Attribute Display on Product Create/Edit**: When the Admin selects a Category on `/admin/products/create`, the Variant Builder dynamically shows only the attribute dropdowns relevant to that Category. Every variant row can also upload an **optional `.webp` Variant Image** (`image` + `image_source`).
9. **Separated Operational Dashboard (`/admin`) & Dedicated Financial Menu (`/admin/financial`)**:
   - `/admin` (`AdminDashboardController`) shows common operational KPIs (order counts, customer counts, active SKUs, low-stock alerts, recent orders, top products).
   - `/admin/financial` (`AdminFinancialController`) is a dedicated sidebar menu displaying all financial metrics (Revenue, Discounts, Wholesale COGS, Net Profit, Margin %, Purchase Batch Outflow, Available Inventory Valuation, Projected Retail Value, ApexCharts financial chart, and item/order profit ledger) with filters for **Date Range, Category, Vendor, and Order Status**, built using Larkon UI components from `docs/admin-dashboard/`.
