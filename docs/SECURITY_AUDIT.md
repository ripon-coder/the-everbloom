# Security Audit

Method: static review of routes, controllers, models, middleware, config, Blade templates, migrations, seeders. No code was modified and no dynamic/penetration testing was performed. Each finding cites evidence; items I could not prove are in §4. `.env` is git-ignored (verified in `.gitignore`, and `git ls-files .env` returns nothing); local `.env` has `APP_ENV=local`, `APP_DEBUG=true`, `DB_CONNECTION=mysql`, `SCOUT_DRIVER=tntsearch`.

Severity summary: **CRITICAL 2 · HIGH 5 · MEDIUM 8 · LOW 6**.

---
## CRITICAL

### S-01 Orders are always created as "Paid"; no payment is ever taken
- **Files:** `app/Http/Controllers/CheckoutController.php` (`'payment_status' => 'Paid'` ~L340; invoice `'status' => 'Paid'` ~L373); `database/migrations/…000008…php` (`payment_status default 'Paid'`); accepted methods `*_demo` (~L181).
- **Impact:** Once the checkout page is wired to `POST /checkout`, anyone can place unpaid orders marked Paid, invoiced, and counted as revenue in `AdminDashboardController`/`AdminFinancialController`. Admin order page also hard-codes "Captured".
- **Fix:** Default `payment_status = Pending`; only a verified gateway webhook or admin action sets `Paid`; remove `*_demo` methods; stop hard-coding the badge/"Captured" in `admin/orders/show.blade.php`.
- **Test:** Place COD and online orders → assert `Pending`; forge webhook without valid signature → 400, status unchanged.

### S-02 Known default admin credentials seeded
- **Files:** `database/seeders/DatabaseSeeder.php` L39-49 (`admin@the-everbloom.com` / `password`, role `super_admin`); customers also `password`. `resources/views/admin/auth/login.blade.php` L24 pre-fills the email when `app()->isLocal()`.
- **Impact:** If seeding runs in production (`php artisan db:seed`) or `APP_ENV` is left `local`, the admin console is open to anyone who knows the default.
- **Fix:** Production seeder reads admin email/password from env or prompts via artisan command; never seed demo customers/orders in production; force password change on first login; guard seeder with `app()->isProduction()` abort.
- **Test:** After production seed, `POST /admin/login` with `password` → 401/422.

---
## HIGH

### S-03 No rate limiting on any authentication or public write endpoint
- **Files:** `routes/admin.php` L28-30, `routes/web.php` L55-68, L91-98. No `throttle` middleware anywhere (grep of routes; `bootstrap/app.php` registers none).
- **Impact:** Brute force of admin and customer passwords (`/admin/login`, `/account/login` incl. phone login), spam of `/contact`, `/newsletter`, `/shop/product/*/reviews`, coupon-code guessing via `/checkout/validate-coupon`, order spam via `/checkout`.
- **Fix:** `throttle:5,1` on login routes keyed by email+IP (`RateLimiter::for('login', …)`), `throttle:10,1` on coupon validate, `throttle:5,10` on contact/newsletter/review, `throttle:10,1` on checkout; lock accounts after N failures; add CAPTCHA on public forms if abuse appears.
- **Test:** 6 rapid failed logins → 429.

### S-04 Overselling & stock race in checkout
- **File:** `CheckoutController.php` L220-237: out-of-stock only rejected when `stock_count < qty` **and** `stock_status === 'out_of_stock'`; subtraction clamped with `max(0, …)`; variant row not locked.
- **Impact:** Orders can be placed beyond available stock (integrity/financial), concurrent checkouts can double-sell variants; profit/COGS wrongly computed for units that never existed.
- **Fix:** Lock variant (`lockForUpdate`), reject if `available < qty`, decrement with conditional atomic update, throw `ValidationException`.
- **Test:** stock=1, two parallel requests qty=1 → exactly one succeeds (MySQL).

### S-05 Customer-controlled variant fallback & unvalidated variant selection
- **File:** `CheckoutController.php` L206-210: unknown `variantId` silently falls back to the **first** variant; inactive variants (`is_active=false`) and inactive products are not rejected (`Product::findOrFail` has no `is_active` filter, line 201).
- **Impact:** Inactive/hidden/draft products can be purchased by id; price differences between intended and fallback variant.
- **Fix:** Require variant when product has variants; match by id only; filter `is_active`; reject if product inactive.

### S-06 Missing CSRF token meta tag breaks all AJAX protection flows (and encourages bypass)
- **Files:** `resources/views/layouts/app.blade.php`, `layouts/checkout.blade.php` have no `<meta name="csrf-token">`; JS in `account/addresses.blade.php`, `profile.blade.php` reads `meta[name="csrf-token"]` and sends an empty token.
- **Impact:** Not a vulnerability by itself (Laravel rejects with 419), but when developers hit 419 the usual "fix" is adding routes to `VerifyCsrfToken::$except`. All JSON mutations (address, profile, password, delete account, checkout) currently fail in a real browser. **Never** exclude them from CSRF.
- **Fix:** Add the meta tag; keep CSRF enabled.
- **Test:** Browser E2E: update profile succeeds with 200.

### S-07 Admin RBAC not enforced
- **Files:** `app/Models/Admin.php` (`role` fillable, never checked); `routes/admin.php` single `auth:admin` group; no Policies/Gates directory.
- **Impact:** Any active admin has full control of settings, coupons, inventory, financials, order status. No admin user management exists, so there is no path to demote — and any future "support" role would be super-admin by default.
- **Fix:** Define roles/abilities (decision D-14) and enforce per route group; re-check `is_active` per request.
- **Test:** `support` role GET `/admin/settings` → 403.

---
## MEDIUM

| ID | Finding | Evidence | Fix / Test |
|---|---|---|---|
| S-08 | **Script-context JSON injection / stored XSS**. Catalog JSON is printed with `{!! json_encode(..., JSON_UNESCAPED_SLASHES\|JSON_UNESCAPED_UNICODE) !!}` inside `<script>`; no `JSON_HEX_TAG`. A product name/description/ingredients containing `</script><script>…` (admin-entered) executes for every visitor. | `components/storefront/cart-drawer.blade.php` L12-19, `search-modal.blade.php` L8, `layouts/checkout.blade.php` L20-22, `layouts/app.blade.php` L28, L35-50 | Add `JSON_HEX_TAG\|JSON_HEX_AMP\|JSON_HEX_APOS\|JSON_HEX_QUOT` (or `@json`). Test: product name `</script><img src=x onerror=alert(1)>` → page source has `\u003C`. |
| S-09 | **Unauthenticated order creation with arbitrary email/phone** (guest checkout) and no verification; order confirmation emails (once added) could be abused to spam third parties. | `CheckoutController@store` L170-176 | Decide D-02; throttle; email only to verified/own email. |
| S-10 | **Reviews can be spoofed as "verified"**: `verified = Auth::guard('web')->check()` — any logged-in user is "Verified buyer"; guests can post with any author name/email; duplicate reviews unlimited. | `ReviewController` L36 | Verify purchase via `order_items`; one review per user/product; throttle. |
| S-11 | **Coupon abuse**: no per-customer limit, `used_count` incremented even if later order fails? (it is inside the transaction ✔) but never decremented on cancel; client-side validation ignores min order/limits. Coupon codes enumerable via validate endpoint (no throttle). | `Coupon.php`, `CheckoutController` L267-281, `resources/js/app.js` `applyDiscount` | Per-user redemptions table, throttle, restore on cancel. |
| S-12 | **Order status machine absent**: Admin can set any status from any status (e.g. `Cancelled → Delivered`) re-using stock logic only on first cancellation; re-opening a cancelled order does not re-deduct stock → phantom inventory. | `AdminOrderController@updateStatus` | Transition map + history; test invalid transition → 422. |
| S-13 | **Session hardening**: `session.secure` unset (env `SESSION_SECURE_COOKIE` empty), `SESSION_ENCRYPT=false`, `same_site=lax`. `IsolateAdminSession` overrides config at runtime per request. Admin `remember` checkbox is **checked by default** on the admin login form (long-lived admin cookie). | `config/session.php` L172, L50; `admin/auth/login.blade.php` L33 | Production: `SESSION_SECURE_COOKIE=true`, `SESSION_ENCRYPT=true`, `same_site=strict` for admin; uncheck remember by default; add idle timeout for admin (lifetime 30–60 min). |
| S-14 | **Hard-coded fake/PII-looking data rendered to admins** (e.g. notification dropdown names, "Everbloom Client #428") misleads staff into trusting stale UI; not a leak but integrity risk. | `components/admin/topbar.blade.php`, `offcanvas-drawers.blade.php`, `admin/orders/show.blade.php` | Replace with real data or remove. |
| S-15 | **Phone-based login loads every user into memory** and compares digits in PHP → DoS amplification + timing/enumeration differences between "unknown phone" and "wrong password" (both return same message but different latency). | `AccountController::findUserByPhone` | Store `phone_normalized` indexed column. |

## LOW

| ID | Finding | Evidence | Fix |
|---|---|---|---|
| S-16 | **Logout via GET link** — **FIXED (Phase 2)**: replaced with POST `<form action="{{ route('logout') }}">` in `layouts/account.blade.php` and `account/index.blade.php`. | `layouts/account.blade.php`, `account/index.blade.php` | Resolved. |
| S-17 | **Password reset is a stub** — **PARTIALLY FIXED (Phase 2)**: customer `users` broker wired (`forgotPasswordSubmit`, `resetPassword`, `resetPasswordSubmit`); admin broker + production SMTP pending D-13. | `AccountController` | Resolved for customer flow; D-13 pending for mail config. |
| S-18 | **Server-side password policy weaker than UI** — **FIXED (Phase 2)**: `Password::defaults()` (`min(8)->letters()->numbers()`) enforced on registration, password update, and reset. | `AppServiceProvider`, `AccountController` | Resolved. |
| S-19 | **Public `/styleguide` and demo content** exposed. | `routes/web.php` L30 | Remove/gate by env. |
| S-20 | **Information disclosure when `APP_DEBUG=true`** (current local `.env`); `.env.example` also ships `APP_DEBUG=true`. | `.env.example` L4 | Production checklist: `APP_DEBUG=false`, `APP_ENV=production`. |
| S-21 | **Account deletion has no password confirmation** (single DELETE with CSRF) and hard-deletes. | `AccountController::destroyAccount` | Require current password; soft delete. |

---
## Category checklist (requested areas)

| Area | Result |
|---|---|
| Authentication | Dual guard works; login & password-reset rate limited (`throttle:login`, `throttle:password-reset`), customer password reset wired (`users` broker), `Password::defaults()` enforced, and active-status (`status=Active` / `is_active`) enforced per request via `EnsureAccountIsActive`. See S-02, S-15, and admin reset (D-13). |
| Authorization / IDOR | Customer orders & addresses correctly scoped (`$user->orders()`, `abort_unless($address->user_id === $user->id, 403)`) ✔. Admin has no roles (S-07). `Order::getRouteKeyName = order_number` but controllers query manually. |
| CSRF | Enabled globally on `web`; forms include `@csrf`; AJAX token missing (S-06). |
| XSS | Blade `{{ }}` escaping used consistently; `{!! nl2br(e()) !!}` in accordion is safe; **script-context JSON is not** (S-08). Review/contact text rendered via `x-text` (safe). |
| SQL injection | Eloquent bindings everywhere; `whereRaw('LOWER(status) = ?', [..])` parameterised ✔; `LIKE '%'.$request->search.'%'` is bound but wildcard characters unescaped (LOW, DoS-ish). `orWhere('id', …)` precedence in `AdminOrderController::show` (`where order_number = ? OR id = ?`) is safe but ambiguous. |
| Mass assignment | `$fillable` used (no `$guarded=[]`). `Admin::$fillable` includes `role`, `is_active` — safe today (no request-fed `create/update($request->all())` found); `User::$fillable` includes `status`, `provider_*` – `updateProfile` passes validated array only ✔. Keep it that way. |
| File uploads | `image|max:5120` validated; re-encoded to WebP by Intervention (neutralises polyglots) ✔; random file names ✔; no `mimes` whitelist, no dimension limit (decompression-bomb memory risk, LOW); old images deleted on replace; product delete not transactional. Public ACL `put(..., 'public')`. |
| Session security | S-13. |
| Password security | Bcrypt via `hashed` cast + (double) `Hash::make` before `create` in `registerSubmit`/`updatePassword` — **double hashing is harmless** because the `hashed` cast skips values already hashed. |
| Admin access restrictions | `auth:admin` on all `/admin/*` except login/logout. Login/logout routes are registered outside the group (OK). No IP allow-list/2FA. |
| Sensitive config exposure | `.env` ignored ✔. `config/demo/site.php` contains placeholder contact data. `database/database.sqlite` exists in repo directory (check it is not tracked: `git ls-files database/database.sqlite` returned nothing ✔). |
| Payment security | Nothing implemented (S-01). When adding: PCI SAQ-A (hosted redirect), never store card data, verify webhook signatures, HTTPS only. |
| Rate limiting | S-03. |
| Input validation | Present on all writes; gaps: coupon `value` unbounded (percentage > 100 yields discount > subtotal but is clamped only for `fixed`; **`percentage` > 100 produces negative total, clamped by `max(0, …)` — free order**), `price` no max, `usage_limit` accepts 0 on create? (min:1 ✔). |
| Data exposure | `User::toArray()` (appended `name`, etc.; `password` hidden ✔) passed to views and JSON. `Address::toArray` returns phone; scoped to owner ✔. `/search` and storefront catalog JSON include `avg_buy_price`? **No** — `toStorefrontArray` omits buy prices ✔. `Order::toViewArray` includes `total_cost`/`total_profit` — used on customer pages (`AccountController@showOrder`, `index`, `orders`) and these keys are available to Blade (not rendered in customer templates reviewed) but **would leak margins if ever dumped as JSON** — remove from the customer-facing array `[RECOMMENDED]`. |

### Additional: percentage coupon > 100
`AdminCouponController@store` validates `value numeric|min:0` for every type. A `percentage` coupon of 150 gives `discountAmount = subtotal × 1.5`; `$total = max(0, subtotal − discount + shipping)` → free order with negative profit recorded. Add `max:100` when `type=percentage` (MEDIUM, classified with S-11).

## 4. Not verified / needs runtime checking
- Behaviour of TNTSearch index files under `storage/search` permissions. See LARAVEL_ARCHITECTURE §11 for the production-driver requirement.
- S3 bucket policy (public-read objects via `put(..., 'public')`).
- Real mail/queue deployment and HTTPS/HSTS at the web server.
- Whether `public/admin/assets/vendor.js` bundles have known vulnerable library versions (run `npm audit`/retire.js).

## 5. Remediation priority
1. S-02, S-01, S-03, S-04, S-08 (before any public launch).
2. S-05, S-06, S-07, S-10–S-13.
3. Remaining LOW items with normal feature work (see IMPLEMENTATION_ROADMAP Phase 21).

