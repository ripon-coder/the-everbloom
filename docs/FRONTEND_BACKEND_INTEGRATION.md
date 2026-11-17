# Frontend ↔ Backend Integration

Design rule: **do not redesign** any existing Blade page. Only replace demo stubs with real requests, keeping markup/classes. Storefront = Tailwind + Alpine (`resources/views/frontend`, `components/storefront`). Admin = Larkon Bootstrap (`resources/views/admin`, `components/admin`).

## 1. Storefront pages

| Page | Blade | Route (name) | Controller | Data passed | Backend status |
|---|---|---|---|---|---|
| Home | `frontend/home.blade.php` | `GET /` (`home`) | `HomeController@index` | flashSaleProducts, flashSaleEndsAt, featuredProducts, bestsellerProducts, newArrivalsProducts, categories(with item_count), testimonials, brands | ✔ live. Falls back to `config('demo.*')` when vars missing. Hero/trust/"Why Everbloom" copy is static. |
| Shop | `frontend/shop/index.blade.php` | `GET /shop` (`shop.index`) | `ShopController@index` | products[] (storefront arrays, **no pagination**: `->get()`), categories | Query params supported: `search, category, brand, in_stock, on_sale, goal[], sort`. **Gap:** view lists categories from `config('demo.categories')` and counts from `config('demo.products')`; filter checkboxes have no `name`/handler; "In Stock Only" is pre-checked but not applied. |
| Category | `frontend/shop/category.blade.php` | `GET /shop/category/{slug}` (`shop.category`) | `ShopController@category` | category(array), products[] | Same gaps; category image not rendered. |
| Product | `frontend/shop/product.blade.php` | `GET /shop/product/{slug}` (`shop.product`) | `ShopController@product` | product, related[], reviews[] | **Gap:** review form `submitReview()` is client-only; variant stock shown from `stock_status` only; `ratingBreakdown` from `config('demo.site.rating_breakdown')` (fake); falls back to `config('demo.reviews')` when no approved reviews → fake reviews shown; JSON-LD `brand` hard-coded `The Everbloom`. |
| Search modal | `components/storefront/search-modal.blade.php` | `GET /search?q=` (`search`) | `SearchController@index` | JSON `{results:[…]}` | ✔ (200 ms debounce, top 6) |
| Cart | `frontend/cart/index.blade.php` | `GET /cart` (`cart.index`) closure | – | `storefrontProductsJson` via view composer | Client-only (see BUSINESS_LOGIC §2). |
| Checkout | `frontend/checkout/index.blade.php` (layout `layouts/checkout`) | `GET /checkout`, `POST /checkout`, `POST /checkout/validate-coupon` | `CheckoutController` | prefillForm, districts, savedAddresses, isGuest | **Gap (critical):** `submitDemoOrder()` never POSTs. Layout preloads `config('demo.products')` into `window.__everbloomProducts` (stale ids/prices); cart-drawer overwrites via JSON tag but order differs → use `$storefrontProductsJson` in the layout. |
| Wishlist | `frontend/pages/wishlist.blade.php` | `GET /wishlist` (`wishlist`) | closure | – | localStorage only. |
| Login / Register / Forgot | `frontend/account/{login,register,forgot-password}.blade.php` | `login`, `login.submit`, `register`, `register.submit`, `password.request`, `password.email` (guest:web) | `AccountController` | – | Login/register ✔. Forgot = stub. Social buttons = demo notice. |
| Account overview | `frontend/account/index.blade.php` | `GET /account` (`account`) | `AccountController@index` | customer, recentOrders, defaultAddress | ✔. "Sign Out" links to login page (bug). |
| Orders / Details | `account/orders.blade.php`, `order-details.blade.php` | `account.orders`, `account.order.details` | `orders`, `showOrder` | orders[] / order,id | ✔ scoped to `$user->orders()`. Status filter tabs list only Processing/Shipped/Delivered (no Placed/Cancelled). Timeline dates are **computed fake offsets** when `timeline` missing; courier hard-coded `Steadfast Courier`; "Track package" demo notice. |
| Addresses | `account/addresses.blade.php` | `account.addresses*` | `AccountController` | addresses[], districts | ✔ AJAX. Client sends `company`, `address_line2`, `postal_code` (ignored server-side). |
| Profile | `account/profile.blade.php` | `account.profile`, `account.profile.update`, `account.password.update`, `account.destroy` | `AccountController` | customer | ✔. Falls back to demo names when absent. |
| About, FAQ, Privacy, Terms, Shipping & Returns | `frontend/pages/*.blade.php` | closures | – | FAQ from `config('demo.faq')` | Static. Contents contradict store region (FAQ says US/Canada shipping & EST; policies written for a US store while the store is BD/BDT). |
| Contact | `pages/contact.blade.php` | `GET /contact`, `POST /contact` (`contact.store`) | `ContactController@store` | – | **Gap:** form `@submit.prevent="sent = true"`. |
| Newsletter | `components/storefront/newsletter-form.blade.php` | `POST /newsletter` (`newsletter.store`) | `NewsletterController@store` | – | **Gap:** `setTimeout` stub. |
| Styleguide | `frontend/styleguide.blade.php` | `GET /styleguide` | closure | – | Public dev page; **remove or gate in production**. |
| 404 | `errors/404.blade.php` | – | – | – | ✔ |

### Required wiring snippets (keep markup, add fetch)
```js
// Newsletter submit(): after client validation
const res = await fetch('{{ route('newsletter.store') }}', {
  method:'POST',
  headers:{'Accept':'application/json','Content-Type':'application/json','X-CSRF-TOKEN':document.querySelector('meta[name=csrf-token]').content},
  body:JSON.stringify({email:this.email})
});
this.status = res.ok ? 'success' : 'error';
```
- **A prerequisite:** `layouts/app.blade.php` and `layouts/checkout.blade.php` contain **no** `<meta name="csrf-token">`. Every existing `fetch` in account pages reads `meta[name=csrf-token]` and silently sends an empty token → 419 errors. Add `<meta name="csrf-token" content="{{ csrf_token() }}">` to both layouts first.
- Contact: POST `{name,email,message}` to `route('contact.store')`, expect 201 `{success,message}`; map 422 `errors` to field errors.
- Review: POST `{author, author_email, rating, headline, body, variant}` to `route('shop.product.reviews.store', slug)`; note the form's headline is optional on screen but `required` server-side → make the field required or relax rule.
- Checkout: replace `submitDemoOrder()` with POST body `{email, phone, firstName, lastName, district_id, thana_id, area_id, address, addressLine2, shippingMethod, paymentMethod, discountCode, items:[{productId, variantId, qty}]}` from `$store.cart.items`; on success clear cart, redirect to `redirect_url`; 422 → map `errors` (`items`, `phone`, …) to the existing `errors` object. Change `paymentMethod` radio values from `*_demo` once real methods are decided (D-01) and update the validation `in:` list.
- Coupon: call `POST /checkout/validate-coupon` instead of local lookup; response `{valid,code,type,value,label}` or 422 `{valid:false,message}`.
- Logout: replace `<a href=route('login')>Sign Out` by a `<form method="POST" action="{{ route('logout') }}">@csrf<button>`.
- Shop filters: make checkboxes real GET inputs (`in_stock=1`, `on_sale=1`, `goal[]=…`) inside a `<form method="GET">`; replace demo config lookups with controller-supplied `$categories` (already passed) and `$products` count.

## 2. Admin pages (layout `layouts.admin`, sidebar `components/admin/sidebar.blade.php`)

| Page | Blade | Routes | Controller | Notes |
|---|---|---|---|---|
| Login | `admin/auth/login` (`layouts.admin-auth`) | `admin.login(.submit)`, `admin.logout` | `AdminAuthController` | prefills admin email in local env; "Forgot password?" is a dead link |
| Dashboard | `admin/dashboard/index` | `admin.dashboard` | `AdminDashboardController` | Loads **all** products and orders into memory (`->get()` + `toViewArray`) for counts — replace by aggregate queries |
| Financial | `admin/financial/index` | `admin.financial.index` | `AdminFinancialController` | loads all matching orders in memory |
| Products | `admin/products/index`, `create` (shared create/edit) | `admin.products.*` (resource) | `AdminProductController` | `show` route registered by `Route::resource` but **no `show()` method** → 500 on `/admin/products/{id}` GET. Restrict with `->except('show')` |
| Attributes | `admin/attributes/index` | `admin.attributes.*` | `AdminAttributeController` | no attribute delete / value edit |
| Categories | `admin/categories/index`, `create` | `admin.categories.*` | `AdminCategoryController` | same: no `show()`; sidebar passes slug for edit/delete |
| Orders | `admin/orders/index`, `show` | `admin.orders.*` | `AdminOrderController` | hard-coded "Laboratory Hub Portland", "Everbloom Client #428", payment "Captured", payment badge always "Paid" |
| Inventory / Purchases | `admin/inventory/index`, `purchases/index|create` | `admin.inventory.*`, `admin.purchases.*` | `AdminInventoryController`, `AdminPurchaseController` | hard-coded warehouse "Portland… OR-97201" and fallback dates |
| Invoices | `admin/invoices/index|show` | `admin.invoices.*` | `AdminInvoiceController` | invoice number on detail page is computed `INV-2026-{last4}` instead of `invoices.invoice_no`; hard-coded Portland address; `{{ $order['id'] }}` used as lookup key |
| Customers | `admin/customers/index|show` | `admin.customers.*` | `AdminCustomerController` | "VIP Botanical Member", "Preferred Ritual", "580 pts", default city "Dhaka, Bangladesh" are fabricated placeholders |
| Coupons | `admin/coupons/index|create` | `admin.coupons.{index,create,store,destroy}` | `AdminCouponController` | create form default `expires_at=2026-12-31`, `min_order 50.00` hard-coded |
| Reviews | `admin/reviews/index` | `admin.reviews.*` | `AdminReviewController` | toggle only |
| Settings | `admin/settings/index` | `admin.settings.*` | `AdminSettingController` | defaults are USD/US phone/Portland address |
| Locations | `admin/locations/index` | `admin.locations.*` | `AdminLocationController` | ✔ |
| Topbar/Offcanvas | `components/admin/topbar|offcanvas-drawers` | – | – | notification dropdown and activity stream are **static fake data**; topbar search is a non-functional input; avatar fixed `avatar-1.jpg` |

### Admin UI consistency rules for new modules
Extend `layouts.admin`; set `@section('title')` and `@section('container_class','container-xxl')`; use `card` → `card-header d-flex justify-content-between` → `table align-middle table-hover table-centered` with `bg-light-subtle` thead; action buttons `btn btn-sm btn-soft-{primary|secondary|danger}` with `iconify-icon` Solar icons; badges `bg-{x}-subtle text-{x}`; forms in `row g-3`; flash `session('success')` is rendered by… **no layout currently renders `session('success')` / `errors`** — add a shared alert partial in `layouts/admin.blade.php` (`@if(session('success'))`) or admins get no feedback for any action. Register sidebar entries in `components/admin/sidebar.blade.php` inside the existing `<li class="nav-item">` structure and highlight with `request()->routeIs(...)`.

## 3. Response & error conventions
- Storefront AJAX: JSON `{success, message, …}`; validation 422 `{message, errors}`; 403 for ownership failures; 419 CSRF.
- Admin: redirect back with `->with('success'|'error', …)`; validation errors via `$errors`.
- Unauthenticated admin → `/admin/login`; web → `/account/login` (`bootstrap/app.php`).
- No custom exception rendering exists (`withExceptions` empty); only `errors/404.blade.php` is present — add 403/419/500/503 views.

