# 08 — Admin Panel Logic Audit & Identified Issues Report

> **AI Agent Context Header**
> - **Project**: The Everbloom (Laravel 12 Botanical Skincare E-Commerce)
> - **Audit Scope**: Complete line-by-line logic and template audit of `/admin` (`routes/admin.php`, all 12 `Admin*Controller` classes, `PurchaseBatchService`, `ImageStorageService`, and all Blade views under `resources/views/admin/` and `resources/views/components/admin/`).
> - **Purpose of This Document**: Catalog every logic bug, broken form, missing field, calculation edge case, and UI-to-controller mismatch currently present in the Admin Panel, with exact file paths, line numbers, root causes, and recommended fixes.

---

## 1. Executive Summary of Audit Findings (Re-Checked)

We re-audited all **12 Admin Controllers**, **Services**, and **19 Admin Blade Templates** (plus Storefront price bindings):
- **Issues `ISS-01` through `ISS-15`**: **ALL RESOLVED & VERIFIED** in the codebase.

| ID | Status | Severity | Module | File(s) Affected | Issue Summary |
| :--- | :--- | :--- | :--- | :--- | :--- |
| **ISS-01** | **Resolved** | Critical | Orders Detail | `resources/views/admin/orders/show.blade.php` | Added `status` select & `tracking_number` input to status update form. |
| **ISS-02** | **Resolved** | Critical | Locations (BD) | `resources/views/admin/locations/index.blade.php` | Fixed invalid `<form>` nesting across `<td>` cells using HTML5 `form="..."` attributes and added search bars. |
| **ISS-03** | **Resolved** | Critical | Categories | `resources/views/admin/categories/create.blade.php` | Added `enctype="multipart/form-data"` to category form. |
| **ISS-04** | **Resolved** | Critical | Admin Auth / Topbar | `resources/views/components/admin/topbar.blade.php` | Replaced `GET` login link with `POST /admin/logout` form and dynamic admin name. |
| **ISS-05** | **Resolved** | High | Coupons | `resources/views/admin/coupons/index.blade.php` | Wired `DELETE` form to `admin.coupons.destroy` and guarded usage bar against division by zero. |
| **ISS-06** | **Resolved** | High | Cloud Images | Multiple `resources/views/admin/**/*.blade.php` | Replaced `asset('images/' . ...)` with `ImageStorageService::resolveUrl(...)` + `Category::resolvedImage()`. |
| **ISS-07** | **Resolved** | High | Wholesale Purchases | `AdminPurchaseController.php`<br>`PurchaseBatchService.php`<br>`admin/purchases/create.blade.php` | Synced primary variant `stock_count` when `product_variant_id` is omitted; added variant selector, remove-row button, and live avg-price calculator. |
| **ISS-08** | **Resolved** | High | Orders / Inventory | `AdminOrderController.php`<br>`admin/orders/show.blade.php`<br>`admin/orders/index.blade.php` | Added stock & batch rollback when cancelling an order; added discount row to Order Summary & Invoice; rendered `$totalProfit` KPI card and proper `+$`/`-$` formatting. |
| **ISS-09** | **Resolved** | Medium | Products Create/Edit | `AdminProductController.php`<br>`admin/products/create.blade.php` | Added "Remove Variant" button, variant image thumbnail preview, parent `stock_count` sum sync, and variant cloud image cleanup on delete. |
| **ISS-10** | **Resolved** | Medium | Products List | `resources/views/admin/products/index.blade.php` | Replaced dummy pagination with `$products->links('pagination::bootstrap-5')` and `$products->total()`. |
| **ISS-11** | **Resolved** | Medium | Inventory | `resources/views/admin/inventory/index.blade.php` | Computed dynamic stock health KPIs, linked "Receive New Batch" to `purchases.create`, and added inline stock adjustment form. |
| **ISS-12** | **Resolved** | Low | Customers & Sidebar | `resources/views/admin/customers/index.blade.php`<br>`AdminCustomerController.php`<br>`components/admin/sidebar.blade.php` | Computed dynamic customer KPIs, changed default fallback city to `'Dhaka, Bangladesh'`, and removed hardcoded demo order/customer IDs from sidebar. |
| **ISS-13** | **Resolved** | High | **Dynamic Currency (Storefront & Admin)** | `AppServiceProvider.php`<br>`AdminSettingController.php`<br>All Admin & Storefront Price Views | Shared `$currencySymbol` & `$currencyCode` globally, added Currency Preset selector in `/admin/settings`, and replaced all hardcoded `$` and `USD` across Admin & Storefront views and JS templates. |
| **ISS-14** | **Resolved** | High | **3-Step Category Hierarchy & Category-Scoped Attributes** | `categories` & `category_attribute` tables<br>`AdminCategoryController.php`<br>`AdminAttributeController.php`<br>`admin/products/create.blade.php` | Implemented 3-step hierarchy (`Main -> Subcategory -> Child Category`) with depth enforcement, category-to-attribute assignments (`category_attribute`), descendant product filtering, and dynamic `filterAttributesByCategory()` on `/admin/products/create`. |
| **ISS-15** | **Resolved** | High | **Operational Dashboard (`/admin`) vs. Financial Menu (`/admin/financial`)** | `AdminDashboardController.php`<br>`AdminFinancialController.php`<br>`admin/dashboard/index.blade.php`<br>`admin/financial/index.blade.php` | Focused `/admin` on live operational KPIs (`Total Orders`, `Pending/Processing`, `Customers`, `Active Profiles`, `Active SKUs`, `Low Stock Alerts`) and implemented `/admin/financial` with Date Range, Category, Vendor, and Order Status filters, ApexCharts, and profitability ledgers. |

---

## 2. Detailed Issue Breakdown & Exact Fixes

### ISS-01 (Critical): Order Status Update Form Missing Inputs
- **Location**:
  - [`resources/views/admin/orders/show.blade.php`](../resources/views/admin/orders/show.blade.php#L36-L41)
  - [`app/Http/Controllers/Admin/AdminOrderController.php`](../app/Http/Controllers/Admin/AdminOrderController.php#L55-L73)
- **What Is Wrong**:
  In `admin/orders/show.blade.php`, the status update form only contains `@csrf` and a submit button:
  ```blade
  <form action="{{ route('admin.orders.updateStatus', $order['id']) }}" method="POST">
      @csrf
      <button type="submit" class="btn btn-sm btn-primary">Update Status</button>
  </form>
  ```
  However, `AdminOrderController::updateStatus()` validates:
  ```php
  $validated = $request->validate([
      'status' => ['required', 'in:Placed,Processing,Shipped,Delivered,Cancelled'],
      'tracking_number' => ['nullable', 'string', 'max:80'],
  ]);
  ```
  Because `status` is never sent, validation always fails and the order status can never be changed from the UI.
- **Required Fix**:
  Add a `<select name="status" class="form-select form-select-sm">` (with options `Placed`, `Processing`, `Shipped`, `Delivered`, `Cancelled`) and an `<input type="text" name="tracking_number" value="{{ $order['tracking_number'] }}">` inside the `<form>` in `admin/orders/show.blade.php`.

---

### ISS-02 (Critical): Invalid HTML `<form>` Nesting in Bangladesh Locations Table
- **Location**:
  - [`resources/views/admin/locations/index.blade.php`](../resources/views/admin/locations/index.blade.php#L72-L91) (Districts table, lines 72–91; Thanas table, lines 161–177; Areas table, lines 251–267)
- **What Is Wrong**:
  In all three tables (`districts`, `thanas`, `areas`), `<form>` is opened inside the first `<td>` and closed inside the last `<td>`:
  ```blade
  <tr>
      <td>
          <form action="{{ route('admin.locations.districts.update', $district) }}" method="POST">
              @csrf @method('PUT')
              <input type="text" name="name" value="{{ $district->name }}">
      </td>
      <td>
              <input type="text" name="bn_name" value="{{ $district->bn_name }}">
      </td>
      ...
      <td>
              <button type="submit">Save</button>
          </form>
      </td>
  </tr>
  ```
  Per the HTML5 parser specification, `<form>` is not allowed to span across `<td>` cells; browsers immediately close `<form>` before the first `</td>`. As a result, `bn_name`, `delivery_charge`, `postal_code`, and the `Save` submit button are detached from the `<form>`, and clicking **Save** does nothing in the browser.
  Additionally, `AdminLocationController@index` supports `?search=...` and district/thana filter parameters, but `admin/locations/index.blade.php` has no search bar in the card header.
- **Required Fix**:
  Place each `<form id="update-district-{{ $district->id }}" ...>@csrf @method('PUT')</form>` cleanly inside the Actions `<td>` (or outside the table) and bind the row's `<input>` and `<button>` elements using the standard HTML5 `form="update-district-{{ $district->id }}"` attribute, plus add a search filter form to the card header.

---

### ISS-03 (Critical): Missing `enctype="multipart/form-data"` on Category Create/Edit Form
- **Location**:
  - [`resources/views/admin/categories/create.blade.php`](../resources/views/admin/categories/create.blade.php#L21)
- **What Is Wrong**:
  Line 21 defines:
  ```blade
  <form action="{{ $isEdit ? route('admin.categories.update', $category['slug']) : route('admin.categories.store') }}" method="POST">
  ```
  without `enctype="multipart/form-data"`, while line 71 has `<input type="file" name="image">`. When an admin selects a category image in the browser, the file binary is never transmitted, causing Laravel's `'image' => ['nullable', 'image']` rule to reject the request.
- **Required Fix**:
  Add `enctype="multipart/form-data"` to the `<form>` tag in `resources/views/admin/categories/create.blade.php`.

---

### ISS-04 (Critical): Admin Topbar Logout Link Does Not Log Out
- **Location**:
  - [`resources/views/components/admin/topbar.blade.php`](../resources/views/components/admin/topbar.blade.php#L125-L127)
- **What Is Wrong**:
  Line 125 renders:
  ```blade
  <a class="dropdown-item text-danger" href="{{ route('admin.login') }}">
      <i class="bx bx-log-out fs-18 align-middle me-1"></i><span class="align-middle">Logout</span>
  </a>
  ```
  `route('admin.login')` is `GET /admin/login`, which redirects authenticated admins straight back to `/admin`. The actual logout route is `POST /admin/logout` (`route('admin.logout')`).
  Also, the topbar user dropdown hardcodes `"Everbloom Concierge"` and `avatar-1.jpg` instead of displaying `Auth::guard('admin')->user()->name` and `avatar`.
- **Required Fix**:
  Replace the `<a>` tag with a `<form action="{{ route('admin.logout') }}" method="POST">@csrf <button type="submit" class="dropdown-item text-danger">...</button></form>` and display the authenticated admin's name.

---

### ISS-05 (High): Coupon Delete Button Is Unwired & Usage Bar Can Divide by Zero
- **Location**:
  - [`resources/views/admin/coupons/index.blade.php`](../resources/views/admin/coupons/index.blade.php#L51-L67)
- **What Is Wrong**:
  1. Line 64 has a plain `<button type="button" class="btn btn-sm btn-soft-danger">` with no `<form>` submitting `DELETE` to `route('admin.coupons.destroy', $c['id'])`.
  2. Line 52 calculates `{{ ($c['used_count'] / $c['usage_limit']) * 100 }}%` without guarding against `$c['usage_limit'] <= 0`.
  3. Line 61 always renders `bg-success-subtle text-success` even when `$c['status'] === 'Inactive'` (`$c['status_badge']` from the controller is ignored).
  4. Line 79 footer still says `Discounts configured in config/demo/site.php`.
- **Required Fix**:
  Wrap the delete button in `<form action="{{ route('admin.coupons.destroy', $c['id']) }}" method="POST">@csrf @method('DELETE')`, guard `$c['usage_limit'] > 0`, and use `bg-{{ $c['status_badge'] }}-subtle text-{{ $c['status_badge'] }}`.

---

### ISS-06 (High): Third-Party Cloud Image URLs Broken by `asset('images/' . ...)` in Admin Views
- **Location**:
  - [`resources/views/admin/dashboard/index.blade.php`](../resources/views/admin/dashboard/index.blade.php#L211) (lines 211, 266)
  - [`resources/views/admin/products/index.blade.php`](../resources/views/admin/products/index.blade.php#L79) (line 79)
  - [`resources/views/admin/products/create.blade.php`](../resources/views/admin/products/create.blade.php#L46) (lines 46, 121)
  - [`resources/views/admin/categories/index.blade.php`](../resources/views/admin/categories/index.blade.php#L14) (lines 14, 69)
  - [`resources/views/admin/categories/create.blade.php`](../resources/views/admin/categories/create.blade.php#L68) (line 68)
  - [`resources/views/admin/orders/index.blade.php`](../resources/views/admin/orders/index.blade.php#L170) (line 170)
  - [`resources/views/admin/orders/show.blade.php`](../resources/views/admin/orders/show.blade.php#L151) (line 151)
  - [`resources/views/admin/inventory/index.blade.php`](../resources/views/admin/inventory/index.blade.php#L128) (line 128)
- **What Is Wrong**:
  When an image is uploaded and converted to `.webp` on a third-party disk (`s3`, `r2`, etc.), `Product::resolvedImages()` and `OrderItem::resolvedImage()` return a full URL (`https://...`) or root-relative storage URL (`/storage/...`). However, the Admin Blade views wrap the string in `asset('images/' . $img)`, producing invalid URLs such as `http://localhost/images/https://cdn.example.com/products/item.webp`.
  Additionally, `Category` model does not resolve its `image` via `ImageStorageService::resolveUrl($this->image, $this->image_source)`.
- **Required Fix**:
  1. Add `resolvedImage(): string` (or `image_url` accessor) on `Category`, `Product`, and `OrderItem` that always returns a complete URL (whether `local` or third-party cloud).
  2. In all Admin Blade views, check `Str::startsWith($img, ['http://', 'https://', '/']) ? $img : asset('images/' . $img)`.

---

### ISS-07 (High): Wholesale Purchase Batch Variant Stock Sync & Live Calculator Gaps
- **Location**:
  - [`resources/views/admin/purchases/create.blade.php`](../resources/views/admin/purchases/create.blade.php#L54-L76)
  - [`app/Services/PurchaseBatchService.php`](../app/Services/PurchaseBatchService.php#L114-L133)
  - [`app/Http/Controllers/Admin/AdminPurchaseController.php`](../app/Http/Controllers/Admin/AdminPurchaseController.php#L55-L76)
- **What Is Wrong**:
  1. **Variant Stock Not Updated When Variant Is Omitted**: In `PurchaseBatchService::createBatch()`, if `product_variant_id` is not provided, `$product->stock_count` is increased by `$purchasedQty`, and `$product->productVariants()->update(['avg_buy_price' => ..., 'last_buy_price' => ...])` runs, **but `product_variants.stock_count` is never increased**! Because `CheckoutController@store` checks `$variant->stock_count`, wholesale stock added without a `product_variant_id` cannot be sold if the variant was out of stock.
  2. **No Variant Selector or Remove Row Button in `admin/purchases/create.blade.php`**: The batch creation form does not allow selecting a specific variant (optional) or removing an added `.batch-row`, nor does it show a live preview of the new Weighted Average Buy Price as quantities/prices are entered.
- **Required Fix**:
  1. When `product_variant_id` is null in `PurchaseBatchService::createBatch()`, also increment the default/primary variant's `stock_count` (or distribute/sync variant stock) and allow optional `product_variant_id` selection in `AdminPurchaseController@store` and `admin/purchases/create.blade.php`.
  2. Add a remove row button and live JS Weighted Average Price & Unit Profit preview to `admin/purchases/create.blade.php`.

---

### ISS-08 (High): Order Profit, Discount Row & Cancellation Stock Rollback Issues
- **Location**:
  - [`app/Http/Controllers/Admin/AdminOrderController.php`](../app/Http/Controllers/Admin/AdminOrderController.php#L55-L73)
  - [`resources/views/admin/orders/index.blade.php`](../resources/views/admin/orders/index.blade.php#L8-L96)
  - [`resources/views/admin/orders/show.blade.php`](../resources/views/admin/orders/show.blade.php#L165-L241)
  - [`resources/views/admin/invoices/show.blade.php`](../resources/views/admin/invoices/show.blade.php#L83-L105)
- **What Is Wrong**:
  1. **No Stock Rollback on Order Cancellation**: When `AdminOrderController::updateStatus()` changes an order status from `Placed`/`Processing` to `Cancelled`, the items' quantities are **not** returned to `products.stock_count`, `product_variants.stock_count`, or `purchase_batch_items.remaining_qty`.
  2. **Missing Discount Line in Order Summary & Invoice**: Both `admin/orders/show.blade.php` (lines 213–231) and `admin/invoices/show.blade.php` (lines 84–103) omit `$order['discount_amount']` and `$order['discount_code']`. When a coupon is used, `Subtotal - Discount + Shipping = Total` appears visually inconsistent because the discount row is hidden.
  3. **`$totalProfit` Not Rendered on `admin/orders/index.blade.php`**: `AdminOrderController@index` computes `$totalProfit`, but the blade view only shows 4 status cards and never displays `$totalProfit`.
  4. **Negative Profit Formatting**: `admin/orders/index.blade.php` (line 188) and `admin/orders/show.blade.php` (lines 166, 238) prefix `+$` unconditionally (`+${{ number_format($profit, 2) }}`), which renders `+$ -10.00` if an item is sold below cost.

---

### ISS-09 (Medium): Product Multi-Attribute Variant Builder Edge Cases
- **Location**:
  - [`resources/views/admin/products/create.blade.php`](../resources/views/admin/products/create.blade.php#L182-L259)
  - [`app/Http/Controllers/Admin/AdminProductController.php`](../app/Http/Controllers/Admin/AdminProductController.php#L275-L413)
- **What Is Wrong**:
  1. **No "Remove Variant" Button**: In `admin/products/create.blade.php`, admins can click `+ Add Variant`, but there is no button to remove a variant row.
  2. **Cloned Variant Row Retains "Remove existing image" Checkbox & Old Values**: `addVariantRow()` clones `.variant-row` without removing `.form-check` (the "Remove existing image" checkbox from row 0) and without resetting text inputs (`title`, `sku`).
  3. **Variant Image Thumbnail Not Shown**: In `admin/products/create.blade.php` (line 232), when `$vRow['image']` exists, it shows a "Remove existing image" checkbox, but does not render an `<img>` thumbnail preview of the variant's current image.
  4. **Parent `stock_count` Not Synced with Sum of Variants**: In `AdminProductController::syncProductVariants()`, when multiple variants are saved with individual `stock_count` values, `$product->stock_count` is not updated to equal the sum of its variants' `stock_count`.
  5. **Orphaned Variant Cloud Images on Product Delete**: In `AdminProductController::destroy()`, `$product->images` are deleted via `$imageStorage->delete()`, but variant images (`$variant->image`) are not deleted from third-party storage before deleting the product.

---

### ISS-10 (Medium): Admin Product List Pagination Is Static Dummy HTML
- **Location**:
  - [`resources/views/admin/products/index.blade.php`](../resources/views/admin/products/index.blade.php#L12) (line 12 and lines 169–180)
- **What Is Wrong**:
  `AdminProductController@index` paginates products (`paginate(12)`), but `admin/products/index.blade.php`:
  - Uses `count($products)` in the header instead of `$products->total()`.
  - Hardcodes a static disabled `<ul class="pagination">` with page `1` only instead of calling `{{ $products->links('pagination::bootstrap-5') }}`. If more than 12 products exist (16 are seeded by default!), products 13–16 are inaccessible without manually typing `?page=2` in the URL!

---

### ISS-11 (Medium): Warehouse Inventory Page Hardcoded KPI Cards & Missing Stock Adjust UI
- **Location**:
  - [`resources/views/admin/inventory/index.blade.php`](../resources/views/admin/inventory/index.blade.php#L8-L103)
- **What Is Wrong**:
  1. Top 4 KPI cards hardcode `384` Total Units, `9 Formulations` Healthy, `3 Batches` Low Stock, and `0 Batches` Out of Stock instead of computing them dynamically from `$inventory`.
  2. The `"Receive New Batch"` button (line 100) links to `route('admin.products.create')` instead of `route('admin.purchases.create')`.
  3. Route `POST /admin/inventory/{product}/adjust` (`admin.inventory.adjust`) exists in `routes/admin.php` and `AdminInventoryController@updateStock`, but the Inventory table has no inline form or modal to adjust stock or alert threshold.

---

### ISS-12 (Low): Hardcoded Customer KPI Cards & Sidebar Demo Parameters
- **Location**:
  - [`resources/views/admin/customers/index.blade.php`](../resources/views/admin/customers/index.blade.php#L8-L96)
  - [`app/Http/Controllers/Admin/AdminCustomerController.php`](../app/Http/Controllers/Admin/AdminCustomerController.php#L20)
  - [`resources/views/components/admin/sidebar.blade.php`](../resources/views/components/admin/sidebar.blade.php#L111)
- **What Is Wrong**:
  1. `admin/customers/index.blade.php` hardcodes `428` All Clients, `389` Active Profiles, `$186.40` Average LTV, and `65.2%` Repeat Rate instead of dynamic values from `AdminCustomerController@index`.
  2. `AdminCustomerController` falls back to `'Portland, OR'` instead of `'Dhaka, Bangladesh'` when a customer has no saved address.
  3. `components/admin/sidebar.blade.php` (line 111) hardcodes `route('admin.orders.show', 'EVB-9204')` and (line 163) `route('admin.customers.show', 1)`, which will 404 if order `EVB-9204` or user `1` does not exist in a fresh database.

---

### ISS-13 (Resolved — High): Hardcoded `$` & `USD` Currency Across Admin & Storefront Views Instead of Dynamic Website Currency (`Taka ৳`, `Dollar $`, `Pound £`, `Euro €`)
- **Locations**:
  1. **Global View Sharing (`app/Providers/AppServiceProvider.php`)**:
     - Currently merges `Setting::where('key', 'site')` into `config('demo.site')`, but does **not** call `View::share('currencySymbol', ...)` or `View::share('currencyCode', ...)`.
  2. **Admin Settings View (`resources/views/admin/settings/index.blade.php`)**:
     - Lines 28–35 only have plain text inputs for `currency_code` and `currency_symbol` (no quick preset selector for `Bangladeshi Taka (BDT — ৳)`, `US Dollar (USD — $)`, `British Pound (GBP — £)`, `Euro (EUR — €)`).
     - Lines 80, 82, 87, 89, 94, 96 hardcode `($ USD)` and `<span class="input-group-text">$</span>`.
  3. **Admin Panel Views & Controllers Hardcoding `$` / `USD`**:
     - `app/Http/Controllers/Admin/AdminCouponController.php` (line 25): Hardcodes `'$' . number_format((float) $c->value, 2) . ' OFF'`.
     - `resources/views/admin/dashboard/index.blade.php` (lines 12, 111, 219, 278)
     - `resources/views/admin/products/index.blade.php` (lines 100, 102)
     - `resources/views/admin/products/create.blade.php` (lines 60, 64, 222, 318, 320, 327, 329)
     - `resources/views/admin/orders/index.blade.php` (lines 20, 189, 192, 197)
     - `resources/views/admin/orders/show.blade.php` (lines 168–176, 222–252)
     - `resources/views/admin/inventory/index.blade.php` (lines 157–163)
     - `resources/views/admin/purchases/index.blade.php` (lines 48–53, 90) & `create.blade.php` (lines 80, 126)
     - `resources/views/admin/invoices/index.blade.php` (line 49) & `show.blade.php` (lines 71, 73, 87, 92, 97, 100, 101)
     - `resources/views/admin/customers/index.blade.php` (lines 66, 155) & `show.blade.php` (lines 80, 120)
     - `resources/views/admin/coupons/create.blade.php` (lines 35, 54)
  4. **Storefront Views Hardcoding `$` / `USD`**:
     - `resources/views/frontend/shop/product.blade.php` (lines 93, 356, 357, 362, 368, 417, 449, 484, 509)
     - `resources/views/frontend/cart/index.blade.php` (lines 107, 117, 179, 208, 243, 259, 268, 273, 278)
     - `resources/views/frontend/checkout/index.blade.php` (lines 125, 154, 203, 208, 214, 219, 493, 609, 658, 663, 670, 676, and JS `getShippingRatePrice()`)
     - `resources/views/frontend/pages/wishlist.blade.php` (line 64)
     - `resources/views/frontend/account/index.blade.php`, `orders.blade.php`, `order-details.blade.php`
- **Required Fix**:
  Follow **Phase 10** in [`07-step-by-step-backend-development-plan.md`](./07-step-by-step-backend-development-plan.md#phase-10-dynamic-website-currency-taka--dollar--pound--euro--across-storefront--admin):
  1. Share `$currencySymbol` (`config('demo.site.currency.symbol', '$')`) and `$currencyCode` (`config('demo.site.currency.code', 'USD')`) globally in `AppServiceProvider::boot()`.
  2. Add a Currency Preset selector (`BDT / ৳`, `USD / $`, `GBP / £`, `EUR / €`, etc.) to `resources/views/admin/settings/index.blade.php`.
  3. Replace all hardcoded `$` and `USD` occurrences across the Admin and Storefront views listed above with `{{ $currencySymbol }}` and `{{ $currencyCode }}`.

---

### ISS-14 (Resolved — High): Flat Categories Instead of 3-Step Hierarchy (`Main -> Subcategory -> Child`) & Global Attributes Instead of Category-Scoped Attributes on Product Create
- **Locations**:
  - `database/migrations/0001_01_01_000004_create_categories_table.php`
  - `app/Models/Category.php` & `app/Models/Attribute.php`
  - `app/Http/Controllers/Admin/AdminCategoryController.php` & `resources/views/admin/categories/create.blade.php`
  - `app/Http/Controllers/Admin/AdminAttributeController.php` & `resources/views/admin/attributes/index.blade.php`
  - `resources/views/admin/products/create.blade.php`
- **What Is Wrong**:
  1. **No 3-Step Category Hierarchy**: `categories` currently has no `parent_id` or `level` column. The business requirement specifies up to **3 category steps only** (`Step 1: Main Category -> Step 2: Subcategory -> Step 3: Child Category`, where `level` $\in \{1, 2, 3\}$ and a Level 3 category cannot have further child categories).
  2. **Attributes Not Scoped by Category**: In `admin/products/create.blade.php` (lines 200–210), every active `Attribute` is rendered for every product regardless of category. Instead, Attributes should be linked to Categories (`category_attribute` pivot table, with parent-to-child inheritance) so that when an Admin selects a Category on `/admin/products/create`, **only the Attributes assigned to that Category** dynamically appear in the Variant Builder.
- **Required Fix**:
  Implement **Phase 11 (Steps 1–4)** in [`07-step-by-step-backend-development-plan.md`](./07-step-by-step-backend-development-plan.md#phase-11-3-step-category-hierarchy-category-scoped-attributes--dedicated-financial-menu-adminfinancial).

---

### ISS-15 (Resolved — High): Missing Dedicated Financial Menu (`/admin/financial`) with Filters & Operational Dashboard Separation (`/admin`)
- **Locations**:
  - `routes/admin.php`
  - `resources/views/components/admin/sidebar.blade.php`
  - `app/Http/Controllers/Admin/AdminDashboardController.php` & `resources/views/admin/dashboard/index.blade.php`
  - New: `app/Http/Controllers/Admin/AdminFinancialController.php` & `resources/views/admin/financial/index.blade.php`
- **What Is Wrong**:
  1. `/admin` (`AdminDashboardController@index`) currently mixes booked revenue with general operational counts and has no dedicated **Financial** section.
  2. There is no **"Financial"** menu item (`/admin/financial`) where the admin can filter financial performance by **Date Period / Custom Date Range**, **Category (including 3-step child categories)**, **Vendor**, and **Order Status** to inspect Gross Revenue, Discounts, Net Revenue, Wholesale COGS (`total_cost`), Net Realized Profit (`total_profit`), Profit Margin %, Wholesale Batch Outflow, Available Inventory Cost Valuation (`stock_count * avg_buy_price`), and Projected Retail Stock Value.
- **Required Fix**:
  Implement **Phase 11 (Step 5)** in [`07-step-by-step-backend-development-plan.md`](./07-step-by-step-backend-development-plan.md#phase-11-3-step-category-hierarchy-category-scoped-attributes--dedicated-financial-menu-adminfinancial) using the Larkon Bootstrap 5.3.3 KPI cards, filter bars, ApexCharts, and ledger table patterns documented in [`03-ui-components-and-patterns.md`](./03-ui-components-and-patterns.md) and `docs/admin-dashboard/`.



