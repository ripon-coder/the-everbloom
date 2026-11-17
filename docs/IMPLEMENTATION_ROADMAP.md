# Implementation Roadmap

Status values: `DONE` (verified), `PARTIAL`, `TODO`. Checklists: `[x]` done, `[ ]` open. Task IDs are stable references (`P<phase>-<n>`). Tests named here are specified in `TESTING_GUIDE.md`. Decisions blocking a task are `D-xx` from `OPEN_DECISIONS.md`.

Dependency order was adjusted from the suggested list: **security/AJAX prerequisites and checkout wiring come before payment**, and **Flash Sale comes after Inventory + Checkout + Coupons** because it hooks into price resolution and order items.

```mermaid
flowchart LR
 P1[1 Schema] --> P2[2 Auth/RBAC] --> P3[3 Products] --> P6[6 Inventory]
 P3 --> P4[4 Categories] & P5[5 Brands]
 P6 --> P8[8 Cart quote] --> P10[10 Checkout] --> P11[11 Payments] --> P12[12 Orders]
 P8 --> P13[13 Coupons] --> P14[14 Flash Sale]
 P10 --> P14
 P12 --> P20[20 Dashboard]
 P14 --> P20
```

## Phase 0 — Prerequisites (do first)
- [ ] **P0-1** Add `<meta name="csrf-token">` to `layouts/app.blade.php` & `layouts/checkout.blade.php`. *Files:* those two. *Accept:* profile/address AJAX returns 200 in browser.
- [ ] **P0-2** Admin flash/validation alert partial in `layouts/admin.blade.php`. *Accept:* `session('success')` and `$errors` visible after any admin action.
- [ ] **P0-3** Restrict `Route::resource` for products/categories with `->except('show')` (`routes/admin.php`). *Accept:* `GET /admin/products/1` → 404, not 500.
- [ ] **P0-4** Replace `config('demo.products')` in `layouts/checkout.blade.php` with `$storefrontProductsJson` (via view composer: add `layouts.checkout` to the composer list in `AppServiceProvider`).

## Phase 1 — Database & schema preparation
| ID | Description | Status | Depends | Create | Update | DB | Acceptance |
|---|---|---|---|---|---|---|---|
| P1-1 | Indexes from DATABASE_SCHEMA §5 | TODO | – | migration `add_indexes_for_catalog_orders` | – | indexes | `EXPLAIN` uses indexes on shop/orders queries |
| P1-2 | Fix category→products FK to restrict | TODO | – | migration | `AdminCategoryController@destroy` (block if products/children) | FK change | Deleting category with products → validation error |
| P1-3 | Core new tables: `flash_sales`, `flash_sale_products`, `brands`, `payment_transactions`, `order_status_histories`, `stock_movements`, `coupon_redemptions`, `wishlists` | TODO | – | 8 migrations + models | – | per schema doc | `migrate:fresh --seed` passes on MySQL & SQLite |
| P1-4 | Coupon columns: `starts_at`, `max_discount`, `per_customer_limit`, pivot tables | TODO | P1-3 | migration | `Coupon` | | |
| P1-5 | `orders` adds `payment_status` default `Pending`, `placed_via`, `coupon…`; `order_items` adds `flash_sale_id`, `flash_sale_product_id`, `original_price` | TODO | P1-3 | migration | `Order`, `OrderItem` fillable | | existing rows backfilled `original_price = price` |
| P1-6 | `users.phone_normalized` + index | TODO | D-26 | migration + backfill | `AccountController` | | phone login uses indexed lookup. *Blocked: D-26 (duplicate-phone handling) must be resolved before the unique index is added.* |
| P1-7 | Production seeder split (`ProductionSeeder` for admin only) | TODO | – | seeder | `DatabaseSeeder` guard | | no demo data/credentials in production |

## Phase 2 — Authentication & authorization
- [ ] **P2-1** Login throttling (`RateLimiter::for('login')`) for admin & customer. *Test:* `AuthThrottleTest`.
- [ ] **P2-2** Real password reset (customer broker `users`, admin broker `admins`): routes `password.reset`, `password.update`, Blade views reusing `forgot-password` styling; Mailable. *Depends:* mail config (D-13).
- [ ] **P2-3** POST logout forms (storefront account layout/overview).
- [ ] **P2-4** Admin RBAC: `config/admin_permissions.php`, `Gate::define`, `EnsureAdminPermission`, per-request `is_active` check. *Blocked:* D-14.
- [ ] **P2-5** Admin Users module (list/create/edit/deactivate/reset password; cannot delete last super_admin). UI new, follow admin conventions.
- [ ] **P2-6** `admin_activity_logs` + log middleware on write routes.
- [ ] **P2-7** Server password policy (`Password::defaults()`).
- [ ] **P2-8** Session hardening env + remember-me default off (admin).
- [ ] **P2-9** Social login (Socialite google/apple) — only if D-15 = yes; else remove buttons.
- [ ] **P2-10** Review `IsolateAdminSession` middleware; confirm it is the right mechanism (vs. separate admin session cookie/domain) and document the rationale. *Ref:* SECURITY_AUDIT S-13, LARAVEL_ARCHITECTURE §1.

## Phase 3 — Product management (`PARTIAL`)
- [ ] **P3-1** Add form inputs: `brand` (select from brands once P5, free text until), `subtitle`, `min_threshold`, `best_seller`, `is_active`, `badge` free text, `skin_types`, `benefits`, `key_ingredients` (tag inputs), `stock_count` for non-variant product. *Files:* `admin/products/create.blade.php`, `AdminProductController@store/update`, new `StoreProductRequest`.
- [ ] **P3-2** Validation: `compare_at_price gt:price`, variant `price min:0.01`, variant SKU unique, max images (matches BUSINESS_LOGIC.md §1).
- [ ] **P3-3** Product gallery management (reorder/delete individual images; stop wiping all on upload). Use `product_images` as source; keep `products.images` in sync or drop it.
- [ ] **P3-4** Pagination/search/sort/status filter in listing; bulk delete/activate (checkboxes exist but do nothing).
- [ ] **P3-5** Soft deletes for products (preserve order history links) — decision D-18; delete in a transaction (DB first, then images).
- [ ] **P3-6** SEO fields (Phase 17).
*Acceptance:* creating/editing every column in DATABASE_SCHEMA `products` from the UI; `ProductCrudTest` passes.

## Phase 4 — Categories (`DONE` core)
- [ ] **P4-1** `is_active` & `sort_order` inputs; **P4-2** block delete when products/children exist; **P4-3** storefront category nav from DB (replace `config('demo.navigation')` and `config('demo.categories')` in header/footer/shop sidebar); **P4-4** image rendering on category page; **P4-5** SEO fields.

## Phase 5 — Brands (`PARTIAL`)
- [ ] **P5-1** `brands` table + `Brand` model + `AdminBrandController` (resource except show) + views `admin/brands/index|create` + sidebar entry. **P5-2** migrate distinct `products.brand` strings → brands, add `products.brand_id`; **P5-3** `/shop?brand=slug` & brand pages (`/brand/{slug}` optional); **P5-4** homepage brands section reads `Brand::active()` (retire `settings.brands`).

## Phase 6 — Inventory
- [ ] **P6-1** `StockService` (atomic decrement/increment with locks) and use it in checkout, cancel, admin adjust. **P6-2** `stock_movements` log on every change (+ history table in inventory page). **P6-3** Variant-level adjust UI. **P6-4** Low-stock alerts list/widget + optional email. **P6-5** Fix cancel restore to use original FIFO rows (store consumed batch rows per order item: `order_item_batches`). **P6-6** Reservation for online payments (D-05).
*Test:* `InventoryConcurrencyTest`, `StockMovementTest`.

## Phase 7 — Customer management
- [ ] **P7-1** Search/filter/paginate, **P7-2** status toggle (`Active/Inactive`) w/ per-request enforcement, **P7-3** remove fabricated labels, **P7-4** address & order stats real, **P7-5** export CSV (optional).

## Phase 8 — Shopping cart (server authority)
- [ ] **P8-1** `POST /cart/quote` (`CartController@quote`) returning lines/prices/stock/coupon/shipping using `PriceResolver`. **P8-2** Alpine cart store calls quote on open/change (keep localStorage lines). **P8-3** Show stock limits & errors; qty max from stock.
## Phase 9 — Wishlist
- [ ] **P9-1** `wishlists` table, `WishlistController` (`GET/POST/DELETE /account/wishlist`), merge localStorage on login, guests keep local. Keep UI.

## Phase 10 — Checkout
- [ ] **P10-1** Wire checkout form to `POST /checkout` (see integration doc). **P10-2** Extract `PlaceOrderService` & `CheckoutRequest`; fix stock (S-04), variant (S-05), `payment_status`. **P10-3** District delivery charge/zones in `ShippingCalculator` (D-09). **P10-4** Save address option. **P10-5** Order-confirmation page + email (queued). **P10-6** Atomic order/invoice numbers (sequence table or `Str::ulid` suffix). **P10-7** Guest policy — implement per D-02 (provisional; gate on owner sign-off).
*Acceptance:* E2E place order as guest/customer, stock reduced, email logged, `CheckoutOrderPlacementTest` passes unchanged.

## Phase 11 — Payments (blocked by D-01)
- [ ] **P11-1** `PaymentGateway` interface + COD gateway (Pending → Paid by admin). **P11-2** Chosen provider(s): initiate, return URL, webhook route (excluded from CSRF **only** this one, signature-verified, idempotent). **P11-3** `payment_transactions`, failure/timeout handling job. **P11-4** Admin Payment Settings UI (enable methods, credentials via encrypted settings or env). **P11-5** Refund API integration.

## Phase 12 — Order management
- [ ] **P12-1** Transition map + history + `OrderStatusService`. **P12-2** Pagination, date/search/payment filters. **P12-3** Internal notes (`orders.notes`) UI. **P12-4** Return/refund workflow (D-07). **P12-5** Remove hard-coded labels. **P12-6** Real invoice number on invoice page + PDF (optional). **P12-7** Customer emails on status change.
- [ ] **P12-8** Remove `total_cost` / `total_profit` from `Order::toViewArray()` used on customer-facing pages; keep them only in admin/financial arrays. *Files:* `app/Models/Order.php`, `AccountController`, admin financial controller. *Accept:* customer order JSON contains no cost/profit keys. *Ref:* SECURITY_AUDIT §4 (Data exposure).

## Phase 13 — Coupons
- [ ] **P13-1** Edit/update/toggle. **P13-2** `max_discount`, `starts_at`, percentage ≤ 100, per-customer limit via redemptions. **P13-3** Product/category scope. **P13-4** Usage history screen. **P13-5** Restore usage on cancel. **P13-6** Cart/checkout use server quote.

## Phase 14 — Flash Sale (see `admin-dashboard/FLASH_SALE_MANAGEMENT.md`)
- [ ] **P14-1** Models/migrations. **P14-2** `FlashSaleService` + `PriceResolver` integration (resolves price at cart quote & checkout). `PriceResolver` must ignore stale `status` when timestamps place the sale in-window (see BUSINESS_LOGIC §8). **P14-3** Scheduler command `flashsales:sync`. **P14-4** Admin list/create/edit/products/show/analytics views & routes. **P14-5** Storefront homepage section + product-card/product-page price & countdown & remaining stock; `/flash-sales` page for upcoming/active. **P14-6** Checkout lock/limit/`sold_qty`/order-item tracking. **P14-7** Cancel/refund adjustments (D-04). **P14-8** Analytics. **P14-9** Retire `settings.flash_sale` + "compare_at_price" heuristic in `HomeController`.
*Blocked decisions:* D-03, D-04, D-08.

## Phase 15 — Reviews
- [ ] **P15-1** Wire storefront form. **P15-2** Status `Rejected`, reject/delete/bulk actions, search/filter/paginate. Include migration to change `reviews.status` default to `Pending`. **P15-3** Verified-buyer detection, one per user/product. **P15-4** Admin reply (optional) & featured testimonial toggle. **P15-5** Show real rating breakdown (remove `config('demo.site.rating_breakdown')`, remove fallback to `config('demo.reviews')`).

## Phase 16 — CMS
- [ ] **P16-1** `pages` table + `AdminPageController` (about/privacy/terms/shipping/faq as DB pages; rich text sanitised with an allow-list, e.g. `strip_tags` + `Purifier`-like logic — avoid raw `{!! !!}` without sanitising). **P16-2** `banners` + `homepage_sections` (hero, promo, trust badges). **P16-3** Contact inquiries inbox (`contact_inquiries` list, mark read, reply by email) and Newsletter subscribers (export, unsubscribe). **P16-4** Wire the storefront forms (Phase 0 follow-up). **P16-5** FAQ CRUD. *Rewrite default copy to match store region (BD, BDT) — D-11.*

## Phase 17 — SEO
- [ ] **P17-1** Meta fields on products/categories/pages + admin inputs; layouts `@yield('meta_description')`, `og_image`, canonical already supported — feed from DB. **P17-2** `GET /sitemap.xml` (cached) and `robots.txt` dynamic or static in `public/`. **P17-3** Product JSON-LD brand/price/availability/reviews from real data (`ShopController`/view). **P17-4** Slug-redirect table when slugs change. **P17-5** Admin SEO overview page.

## Phase 18 — Store settings
- [ ] **P18-1** Logo & favicon upload (admin layout still uses template assets). **P18-2** Social links editor, store email/phone separation, timezone, tax settings fields. **P18-3** Server-side currency decimals/position inputs (`demo.site.currency.decimals` currently only editable via config). **P18-4** Replace USD/US defaults in settings Blade with BDT/BD defaults. **P18-5** Set `config('app.timezone')` from setting (D-10).
- [ ] **P18-6** Update test fixtures that assert USD strings when D-10/D-11 change defaults. *Accept:* `php artisan test` green after currency switch. *Ref:* TESTING_GUIDE §1.

## Phase 19 — Shipping
- [ ] **P19-1** Shipping methods table (name, ETA, cost, free threshold, active) replacing hard-coded `standard`/`express`. **P19-2** Zone pricing (district charge, per-weight optional) used by `ShippingCalculator` (D-09). **P19-3** Courier assignment & tracking number/URL, status events (Steadfast/Pathao appear only as demo strings: D-16). **P19-4** Admin Shipping Settings page grouping methods + locations. **P19-5** Customer order timeline from real `order_status_histories`.

## Phase 20 — Dashboard & analytics
- [ ] **P20-1** Replace in-memory `->get()` with aggregates. **P20-2** Date-range filter, real sales/revenue chart endpoint (JSON) for `dash-performance-chart`; real returning-customer rate; real customer distribution map (district-based). **P20-3** Pending/Processing/Completed counts separately; low-stock list; revenue = paid orders only. **P20-4** Flash Sale analytics cards. **P20-5** Real notifications (new orders, low stock, pending reviews) replacing static dropdown.

## Phase 21 — Security improvements
- [ ] Items S-01 … S-21 from `SECURITY_AUDIT.md`; add CSP/headers middleware; `composer audit`/`npm audit` in CI; remove `/styleguide`; production checklist.
- [ ] Ensure P12-8 (cost/profit leakage) and P22-6 (`Schema::hasTable`) are closed before launch.

## Phase 22 — Final testing & optimisation
- [ ] Full suite on MySQL; concurrency tests; Lighthouse/perf (slim catalog JSON, pagination, caching); backups/queue/scheduler runbook; accessibility regression of storefront; release checklist.
- [ ] **P22-6** Remove per-request `Schema::hasTable` calls in `AppServiceProvider::boot()` (guard with `app()->runningInConsole()` or delete after migrations stabilise). *Accept:* no `Schema::hasTable` queries on storefront requests. *Ref:* LARAVEL_ARCHITECTURE §11.

## Coverage check
Every unfinished module in PROJECT_OVERVIEW §4 maps to a phase: Products P3, Brands P5, Orders P12, Customers P7, Coupons P13, Flash Sale P14, Reviews P15, CMS P16, SEO P17, Settings P18, Payments P11, Shipping P19, Admin users P2, Dashboard P20, Wishlist P9, Checkout P10, Forms (contact/newsletter) P16-4/P0.

