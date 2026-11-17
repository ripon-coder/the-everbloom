# Laravel Architecture

## 1. Current Structure `[VERIFIED EXISTING]`
```
app/
  Http/Controllers/            Storefront controllers (Account, Checkout, Contact, Home, Location, Newsletter, Review, Search, Shop)
  Http/Controllers/Admin/      15 Admin* controllers
  Http/Middleware/IsolateAdminSession.php
  Models/                      27 models (flat)
  Providers/AppServiceProvider.php   settings hydration + view composer (queries, caching)
  Services/ImageStorageService.php, PurchaseBatchService.php
  Support/Currency.php, helpers.php (autoloaded `format_price`)
config/demo/*.php              demo content used as fallback & seed source
routes/web.php, routes/admin.php (loaded in bootstrap/app.php with `web` middleware)
```
> `IsolateAdminSession` overrides session config per request — review whether this is the correct approach vs. a dedicated admin cookie/domain before launch.
Absent: Form Requests, Policies/Gates, Events/Listeners, Jobs (queue tables exist), Notifications, Mailables, custom exceptions, scheduler entries (`routes/console.php` only has `inspire`), API routes, observers.

The project is a **conventional Laravel monolith with controllers containing business logic**; "modular monolith" is a goal not yet reflected in folders.

## 2. Conventions to Follow (observed)
- Controllers named `Admin<Module>Controller`; admin routes under `routes/admin.php`, prefix `admin`, names `admin.*`, guard `auth:admin`.
- Validation inline via `$request->validate([...])` with array rules.
- Images always through `ImageStorageService::uploadAsWebp(file, dir, name)` and stored as (`path`, `image_source`); render with `resolveUrl()`.
- After catalog/coupon mutations call `Cache::forget('storefront_catalog_json')` / `'storefront_discounts_json'`; after settings `Cache::forget('site_settings')`.
- Models expose `toStorefrontArray()` / `toViewArray()` DTO arrays consumed by Blade.
- Money: floats cast on models; DB `decimal(10,2)`.

## 3. Recommended Target Structure `[RECOMMENDED IMPROVEMENT]`
Keep it lean — no extra packages. Grow by adding folders, not rewriting:
```
app/
  Actions|Services/
    Checkout/PlaceOrderService.php, PriceResolver.php, CouponService.php, ShippingCalculator.php
    Inventory/StockService.php (reserve/commit/restore), existing PurchaseBatchService.php
    FlashSale/FlashSaleService.php, FlashSalePriceResolver.php
    Orders/OrderStatusService.php (transition map + history)
    Payments/PaymentGateway.php (interface), CodGateway.php, <ProviderGateway>.php
  Http/Requests/Admin/*  (StoreProductRequest, UpdateProductRequest, StoreCouponRequest, StoreFlashSaleRequest, …)
  Http/Requests/Storefront/*  (CheckoutRequest, ReviewRequest, ContactRequest)
  Policies/ (only if multi-role RBAC is chosen) ; Gates in AuthServiceProvider
  Http/Middleware/EnsureAdminPermission.php
  Events/OrderPlaced, OrderStatusChanged, StockLow ; Listeners/SendOrderConfirmation, LogStockMovement
  Jobs/ActivateFlashSales (or console command), ReleaseExpiredReservations
  Notifications|Mail/OrderConfirmationMail, OrderShippedMail, ContactReceivedNotification
  Models/FlashSale.php, FlashSaleProduct.php, Brand.php, PaymentTransaction.php, StockMovement.php, …
```
Namespaces for modules can stay flat (`App\Models`) until size warrants `app/Modules/*`.

## 4. Models & Relationships
Existing relationships are in the models (Product→category/variants/images/reviews; Category parent/children/products/assignedAttributes; Order→user/coupon/items/invoice; …). Missing and to add:
- `Product::orderItems()`, `Product::batchItems()`, `Product::flashSaleProducts()`, `Product::activeFlashSale()` (scoped by time/status), `Product::brandRelation()`.
- `User::wishlist()`, `Order::statusHistories()`, `Order::payments()`, `Order::refunds()`.
- `OrderItem::productVariant()` (FK exists, relation missing), `Coupon::orders()`, `Coupon::redemptions()`.
- `Review` status constants (`Pending|Approved|Rejected`), `Order` status constants/enum (`App\Enums\OrderStatus` backed enum recommended).
- Add `$casts` for `Setting` — exists. Replace dynamic `$appends` that run queries (`Product::$appends = ['category','category_slug','variants']`) because every serialization triggers relation loads (N+1 risk).

## 5. Controllers
- Slim controllers: parse request → call Service → return view/redirect/JSON.
- Remove `Route::resource` actions that don't exist (`show`) for products and categories.
- `CheckoutController@store` should delegate to `PlaceOrderService`; `CheckoutController@index` contains ~90 lines of mapping – move to a `CheckoutPrefill` view-model.
- Pagination: `ShopController`, `AdminOrderController`, `AdminCustomerController`, `AdminCouponController`, `AdminReviewController`, `AdminInvoiceController`, `AdminInventoryController` all call `->get()` over entire tables; use `paginate()`.

## 6. Form Requests
Create one per write action; reuse rule sets between store/update. Examples of rules that must exist: `price` `numeric|min:0.01|max:99999999`, `compare_at_price` `gt:price`, `images.*` `image|mimes:jpg,jpeg,png,webp|max:5120|dimensions:max_width=6000`, coupon `value` ≤ 100 when `percentage`, `expires_at` `after:starts_at`, `usage_limit` ≥ 1. Authorization method returns `$this->user('admin')?->can(...)`.

## 7. Policies, Gates, Middleware
- Admin access today = `auth:admin` only; `admins.role` is **never read**. Define role→ability map in `App\Support\AdminAbilities` (config-driven), register `Gate::define('manage-products', fn(Admin $a) => in_array(...))`, and apply `->middleware('can:manage-products')` on route groups. Ensure `is_active` re-check per request (`Auth::guard('admin')->user()->is_active`) so deactivating an admin kills live sessions.
- Add `throttle:5,1` (login), `throttle:20,1` (contact/newsletter/reviews/coupon validate/checkout).
- Add security headers middleware (CSP, X-Frame-Options, Referrer-Policy) and `TrustProxies` config for production.

## 8. Events, Jobs, Notifications, Mail
- Queue driver default `database` (`.env.example`); tables exist; run `php artisan queue:work` (composer `dev` script already starts a listener).
- `OrderPlaced` → queued `SendOrderConfirmation` (mail), `NotifyAdminNewOrder`.
- Scheduler (`routes/console.php`): `Schedule::command('flashsales:sync')->everyMinute()->withoutOverlapping();` and `Schedule::job(new ReleaseExpiredReservations)->everyFiveMinutes();` Production cron: `* * * * * php artisan schedule:run`.
- Mail from-address env currently `hello@example.com`; set real SMTP; `MAIL_MAILER=log` means no mail leaves the system.

## 9. Transactions & Locking
- Wrap order placement, cancellation, refund, purchase batch, flash-sale counters in `DB::transaction(..., 3)` (deadlock retry).
- Always lock in a consistent order: flash_sale_products → products → variants → purchase_batch_items → coupons.
- Use atomic conditional updates for counters (`where('sold_qty','+',$q ...)` pattern with `DB::table()->whereRaw('sold_qty + ? <= sale_stock')->increment`). SQLite (tests) ignores `lockForUpdate` — also test on MySQL.

## 10. Exception Handling
`bootstrap/app.php::withExceptions` is empty. Add: JSON rendering for `ValidationException`/`ModelNotFoundException` on `expectsJson()`; domain exceptions (`InsufficientStockException`, `CouponInvalidException`, `FlashSaleLimitExceededException`) rendered as 422 with message; `report()` payment-gateway failures to the log channel with order id context; `APP_DEBUG=false` in production.

## 11. Performance / Caching Notes
- `AppServiceProvider::boot()` runs `Schema::hasTable` on **every request** (3 queries). Remove after migration is stable; guard with `app()->runningInConsole()` instead.
- `storefront_catalog_json` embeds the **entire catalog** (descriptions, ingredients) in every page's HTML (cart drawer + search modal each emit it → duplicated twice). Slim the payload (id, slug, name, price, variants, image, stock) and emit once.
- Cache driver `database`; prefer Redis in production.
- `HomeController` issues ~10 queries; wrap in `Cache::remember` with explicit invalidation.
- Scout config: Production MUST set `SCOUT_DRIVER` explicitly in `.env`. Config default is `collection` (in-memory, scans all rows). Local `.env` currently uses `tntsearch` (see SECURITY_AUDIT method note). Document the chosen production driver in the release checklist (Phase 21/22).

