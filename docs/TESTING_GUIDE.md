# Testing Guide

## 1. Existing Test Suite `[VERIFIED EXISTING]`
Runner: PHPUnit 11 (`phpunit.xml`), `RefreshDatabase`, seeded via `$this->seed()` (full `DatabaseSeeder`; relies on demo data). Run: `composer test` or `php artisan test`. Default test DB = SQLite (check `phpunit.xml`); **row locking is a no-op on SQLite**.

| File | Covers |
|---|---|
| `AccountAreaTest` | account overview/orders/addresses/profile/login/register pages render |
| `AdminAuditFixesTest` | admin logout form, order cancel restores stock, coupon delete, product pagination, inventory adjust, variant stock sum, variant image delete |
| `AdminConsoleEndToEndTest` | all admin pages 200; settings → product w/ WebP S3 → search → coupon → checkout → order visible |
| `BangladeshLocationTest` | 64 districts seed, cascading API, admin rename/disable |
| `CategoryHierarchyAndFinancialMenuTest` | 3-level cap, inherited attributes, financial page + filters |
| `CheckoutOrderPlacementTest` | validate-coupon API; checkout creates order/items/invoice, decrements stock & coupon |
| `CustomerAccountAndFormsTest` | address CRUD, profile/password, contact/newsletter/review persistence |
| `DualAuthSessionIsolationTest` | guard isolation, cookies, mobile login/register |
| `DynamicCurrencyTest` | currency setting reflected everywhere |
| `MultiAttributeProductVariantTest` | attributes & variants with images, order snapshot |
| `StorefrontRoutesTest` | all public & account routes 200 |
| `WholesalePurchaseAndOrderProfitTest` | weighted average cost, order profit |
| `ExampleTest` (Feature/Unit) | scaffolding |

Gaps: no tests for coupon edge cases, overselling, order transitions, shop filters, wishlist, reviews moderation, customer status, authorization (roles), throttling, emails, XSS, or Flash Sale. The tests exercise backend routes directly; **none execute the Alpine UI**, which is why the demo-stubbed checkout/contact/newsletter/review forms were never caught.
Note: tests assert some hard-coded currency strings (`'+$66.00'`) that depend on the `site` setting default; keep fixtures deterministic when changing defaults.
> ⚠️ D-10 (currency) and D-11 (region) will change defaults to BDT/৳. When P18-4 lands, all hard-coded currency assertions (`'+$66.00'`, etc.) must be updated in the same PR. Add a task to Phase 18.

## 2. Unit Tests (to add; `tests/Unit`)
| Area | Cases | Expected |
|---|---|---|
| Model relationships | Product→variants/images/reviews/category; Category parent/children/descendantAndSelfIds (3 levels); Order→items/invoice/coupon; FlashSale→products; `Product::activeFlashSale` | correct counts & types |
| Price calculation (`PriceResolver`) | variant price vs product price; compare-at ignored; rounding of `x.xx5`; flash price when active; expired/not started/disabled sale → regular; sale_stock exhausted → regular | exact decimals, no regular price mutation |
| Coupon | percentage 10% of 1999.99 → 200.00 (2dp); fixed > subtotal → capped; percentage 150 rejected; `max_discount` cap; `min_order_amount`; expired; `usage_limit` reached; per-customer limit; shipping coupon zeroes shipping; scope-only-eligible-lines | per spec |
| Flash sale | `sale_price >= price` invalid; discount % = round((reg − sale)/reg × 100); status resolution at boundary instants (`starts_at` inclusive, `ends_at` exclusive); `remaining = sale_stock − sold_qty`; per-customer limit calc | per spec |
| Inventory | weighted avg: (20×25 + 30×35)/50 = 31.00 (exists); FIFO deduction across 2 batches; stock status derivation at `== threshold` and `0`; restore after cancel | per spec |
| Totals | order of operations (BUSINESS_LOGIC §2), shipping free threshold on discounted subtotal | total never < 0 |
| Currency helper | `Currency::format` decimals/position | e.g. `৳1,900` |

## 3. Feature Tests (`tests/Feature`)
| Suite | Scenario → Expected |
|---|---|
| Admin auth | valid login → redirect `/admin`, `last_login_at` set; inactive admin → error; 6th failed attempt → 429; customer session cannot open `/admin`; logout is POST |
| RBAC | role without `manage-products` → 403 on `/admin/products`; super_admin ok; deactivated admin with live session → redirected |
| Product CRUD | create with all fields incl. brand/min_threshold; slug collision suffix; SKU unique; `compare_at_price <= price` → 422; update keeps images when none uploaded; delete removes images & unsearchable; inactive product 404 on storefront |
| Category CRUD | level 4 rejected (exists); cannot delete with products; moving subtree beyond depth rejected; image replace deletes old |
| Order management | valid transitions only; invalid → 422; cancel restores stock once; history row created; pagination; filter by status (case-insens.) |
| Customer management | search by name/phone/email; toggle Inactive → cannot log in and live session ejected |
| Coupon management | create/edit/toggle/delete; percentage>100 → 422; duplicate code (case-insens.) → 422; usage history list |
| Flash sale management | create draft; add products w/ sale price < regular else 422; sale stock > available stock → 422; overlapping sale on same product → 422 (per D-08); activate/deactivate; delete blocked if orders reference (or soft delete); schedule sets status `scheduled`; non-admin 403 |
| Checkout | guest/customer; invalid variant → 422; insufficient stock → 422 with item message; inactive product → 422; payment_status `Pending` for COD; response JSON shape; email queued (`Mail::fake`) |
| Payment verification | webhook with bad signature → 400 & no change; valid → order `Paid`, transaction row; replay same txn → 200 idempotent, one transaction; failed payment → `Failed` & stock released |
| Shop | filters `in_stock`, `on_sale`, `brand`, `goal[]`, `sort` combos; pagination |
| Reviews | submit → Pending; verified only if purchaser; approve recalculates rating; reject hidden |
| Wishlist | add/remove; merge on login; scoped to user |

## 4. Integration Tests
1. **Cart → checkout:** `POST /cart/quote` totals == `POST /checkout` totals for same lines/coupon.
2. **Checkout → order:** order, items (price snapshot), invoice, coupon redemption, batch consumption, `OrderPlaced` event.
3. **Order → inventory:** place (stock −n) → cancel (stock +n, batch restored, flash `sold_qty` restored per D-04, coupon redemption freed) → re-open blocked.
4. **Payment → order status:** webhook paid → `payment_status=Paid`, order `Processing` (per rule), email.
5. **Flash sale → cart:** quote shows flash price within window, regular price just after `ends_at` (use `Carbon::setTestNow`); sale stock 1, two quotes both show flash price but only first checkout gets it, second gets regular price or 422 (per D-03/D-08 decision). Also test: sale in-window but `status='scheduled'` (scheduler not run) → flash price applies.
6. **Flash sale → order items:** `order_items.price` = flash price, `original_price` = regular, `flash_sale_id` set; later price change of product does not alter history.
7. **Concurrency (MySQL only, `@group mysql`):** 20 parallel checkouts of stock 5 → exactly 5 units sold; flash `sold_qty` never exceeds `sale_stock`; per-customer limit holds under parallel requests.
8. **Scheduler:** `flashsales:sync` activates/expires; idempotent when run twice.

## 5. Security Tests
| Test | Expected |
|---|---|
| Unauthorized: unauthenticated `/admin/*` | 302 `/admin/login` (each route in `routes/admin.php`) |
| Customer opening another customer's order/address | 404/403 (`account/orders/{foreign}`; `PUT /account/addresses/{foreign}` → 403) |
| Invalid input fuzz: negative qty, qty 11, huge strings, array instead of string | 422 |
| CSRF | `POST` without token → 419 on every web POST incl. admin; webhook is the single exemption and verifies signature |
| XSS | product name/description with `<script>`, review/body, contact message, address; assert Blade output escaped; assert `</script>` in catalog JSON is `\u003C/script\u003E` (S-08) |
| SQL injection | `?sort=';--`, `?search=%27`, `?category[]=`, order id `1 OR 1=1` → no error, no leakage |
| File upload | PHP file renamed `.jpg` → rejected or re-encoded to valid WebP; SVG rejected; >5 MB → 422; 10000×10000 px → rejected (add dimension rule) |
| Privilege escalation | support role POST `/admin/settings`→403; mass assignment `role=super_admin` via any request field ignored; `status=Inactive` bypass attempts via profile update ignored |
| Rate limiting | login/contact/newsletter/review/coupon/checkout → 429 after threshold |
| Session | admin & customer cookies distinct, `Secure`/`HttpOnly`; session fixation (`session()->regenerate` on login ✔ tested) |
| Price tampering | POST checkout with extra `price` field → ignored; DB price used |

## 6. Manual / Browser QA Checklist
Mobile 320/375/768/1024; keyboard focus order & Esc on drawers; cart persistence across tabs; currency symbol `৳` font; checkout cascading selects; admin forms with validation errors visible (alert partial). Run Laravel Pint (`vendor/bin/pint`) before commits (dev dependency present).

