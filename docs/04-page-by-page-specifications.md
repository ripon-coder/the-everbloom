# 04 — Page-by-Page Screen Analysis & Everbloom Domain Mapping

> **AI Agent Context Header**
> - **Reference HTML Directory**: `docs/admin-dashboard/`
> - **Domain Data Sources**: `config/demo/products.php`, `config/demo/categories.php`, `config/demo/account.php`, `config/demo/site.php`
> - **Purpose of This Document**: Complete screen-by-screen blueprint mapping the Larkon HTML templates to The Everbloom's botanical skincare e-commerce domain.

---

## 1. Main Admin Dashboard (`admin/dashboard/index.blade.php`)

- **Source File**: `docs/admin-dashboard/index.html` (lines 1245–1760)
- **Topbar Title**: `WELCOME!`
- **Container Class**: `.container-fluid`
- **Page-Specific JS**:
  - `assets/vendor/jsvectormap/js/jsvectormap.min.js`
  - `assets/vendor/jsvectormap/maps/world-merc.js`
  - `assets/vendor/jsvectormap/maps/world.js`
  - `assets/js/pages/dashboard.js`

### Grid & Section Breakdown
1. **Row 1: KPI Cards (`col-xxl-5`) + Performance Chart (`col-xxl-7`)**:
   - **Left (`col-xxl-5`)**:
     - Optional System Alert Banner (`.alert.alert-primary.text-truncate.mb-3`) — can display active storefront promo status (e.g., *"Free shipping over $75 announcement is currently active"*).
     - `2×2` Grid of **Variant A KPI Cards** (`col-md-6`):
       1. **Total Orders** (`solar:cart-5-bold-duotone`)
       2. **Total Customers / Leads** (`bx bx-award`)
       3. **Active Products / SKUs** (`bx bxs-backpack` or `solar:bottle-bold-duotone`)
       4. **Booked Revenue** (`bx bx-dollar-circle`)
   - **Right (`col-xxl-7`)**:
     - **Performance Mixed Chart Card**: Header with time-range filter buttons (`ALL`, `1M`, `6M`, `1Y` `.btn.btn-sm.btn-outline-light`) + `<div id="dash-performance-chart" class="apex-charts"></div>` (Bar + Area chart initialized in `dashboard.js`).
2. **Row 2: Conversions (`col-lg-4`) + Sessions by Country (`col-lg-4`) + Top Pages/Products (`col-lg-4`)**:
   - **Col 1 (`col-lg-4`)**: Returning Customer RadialBar chart (`#conversions`) + This Week vs. Last Week metrics + "View Details" button.
   - **Col 2 (`col-lg-4`)**: World Vector Map (`#world-map-markers` `style="height: 316px"`) + regional session stats.
   - **Col 3 (`col-lg-4`)**: `.card.card-height-100` table of Top Storefront Pages (`/shop`, `/shop/product/botanical-radiance-serum`, `/shop/category/serums`, etc.) with Views and Exit Rate `.badge-soft-success|warning|danger`.
3. **Row 3: Recent Orders Table (`col-12`)**:
   - Full-width `.card` with "Recent Orders" title, `+ Create Order` button, and a 9-column table (`Order ID`, `Date`, `Product` thumbnail, `Customer Name`, `Email ID`, `Phone No.`, `Address`, `Payment Type`, `Status` with `i.bx.bxs-circle.text-success|primary`) + pagination footer.

---

## 2. Products Module (`admin/products/*`)

### 2.1 Product List (`admin/products/index.blade.php`)
- **Source File**: `docs/admin-dashboard/product-list.html`
- **Topbar Title**: `PRODUCT LIST`
- **Container Class**: `.container-fluid`
- **Domain Mapping (`config/demo/products.php`)**:
  - **Columns**:
    1. Checkbox (`20px`)
    2. **Product Name & Sizes**: `.avatar-md` thumbnail (`images[0]`) + `name` (`text-dark fw-medium fs-15`) + subtitle `Sizes: 30ml / 1.0 fl oz, 50ml / 1.7 fl oz` (`implode(', ', $product['sizes'])`) + optional badge (`$product['badge']` e.g. `Bestseller`, `Signature`, `New`).
    3. **Price**: `$64.00` (with muted strikethrough `$78.00` if `compare_at_price` is set).
    4. **Stock**: Variant stock status (`in_stock`, `low_stock`, `out_of_stock`) & item count.
    5. **Category**: `$product['category']` (`Serums`, `Moisturizers`, `Cleansers`, `Face Care`, `Body Care`, `Sunscreens`).
    6. **Rating**: `<span class="badge p-1 bg-light text-dark fs-12 me-1"><i class="bx bxs-star align-text-top fs-14 text-warning me-1"></i> {{ $product['rating'] }}</span> {{ $product['review_count'] }} Review`.
    7. **Action**: View (`solar:eye-broken`), Edit (`solar:pen-2-broken`), Delete (`solar:trash-bin-minimalistic-2-broken`).

### 2.2 Product Create & Edit (`admin/products/create.blade.php`, `admin/products/edit.blade.php`)
- **Source Files**: `docs/admin-dashboard/product-add.html`, `docs/admin-dashboard/product-edit.html`
- **Topbar Title**: `CREATE PRODUCT` / `PRODUCT EDIT`
- **Container Class**: `.container-xxl`
- **Page-Specific JS**: `assets/js/pages/ecommerce-product-details.js` (initializes Dropzone)
- **2-Column Layout (`row`)**:
  - **Left Column (`col-xl-3 col-lg-4`) — Sticky Live Preview Card**:
    - Displays primary product image (`img-fluid rounded bg-light`), Product Name, Category, Price (`$64.00` vs `$78.00`), Bottle Size toggle pills (`30ml`, `50ml`, `100ml`), Badge/Status, and `.card-footer` with `Save Product` / `Cancel` buttons.
  - **Right Column (`col-xl-9 col-lg-8`) — 3 Stacked Form Cards**:
    1. **Card 1: Add Product Photos**: `.dropzone.bg-light-subtle.py-5` + `#dropzone-preview` list.
    2. **Card 2: Botanical Product Information**:
       - `name` (Product Name, `col-lg-6`)
       - `category_slug` (`select[data-choices]` populated with Everbloom categories: Face Care, Body Care, Serums, Moisturizers, Cleansers, Sunscreens, `col-lg-6`)
       - `slug` (URL Slug, `col-lg-4`)
       - `badge` (Badge Select: `None`, `Bestseller`, `Signature`, `New`, `Limited`, `col-lg-4`)
       - `stock_status` (`in_stock`, `low_stock`, `out_of_stock`, `col-lg-4`)
       - `sizes[]` (Bottle Size toggle pills: `30ml / 1.0 fl oz`, `50ml / 1.7 fl oz`, `100ml / 3.4 oz`, `120ml / 4.0 fl oz`, `200ml / 6.7 fl oz`)
       - `is_featured` (Featured on Homepage toggle switch)
       - `short_description` (`textarea` rows="2")
       - `description` (`textarea.bg-light-subtle` rows="4" — Full Editorial Description)
       - `ingredients` (`textarea.bg-light-subtle` rows="3" — Full INCI Botanical Ingredients List)
       - `how_to_use` (`textarea.bg-light-subtle` rows="3" — Daily Ritual Application Instructions)
    3. **Card 3: Pricing & Variant Details**:
       - Base `price` (`.input-group` with `bx-dollar`, `col-lg-4`)
       - `compare_at_price` (Compare-at / Original Price with `bxs-discount`, `col-lg-4`)
       - `sku` / Stock Quantity (`col-lg-4`)
    4. **Bottom Action Bar (`.p-3.bg-light.mb-3.rounded`)**: Right-aligned `Create Product` and `Cancel` buttons.

### 2.3 Product Grid & Product Details (`product-grid.html`, `product-details.html`)
- **`product-grid.html`**: Left filter sidebar (`col-lg-3`: Category checkboxes, Price range slider `nouislider`, Rating filter) + Right product card grid (`col-lg-9`: 3 or 4 columns of visual product cards with wishlist/action buttons).
- **`product-details.html`**: Admin-side product inspection view showing image gallery, specifications, variant pricing, and customer reviews.

---

## 3. Categories Module (`admin/categories/*`)

- **Source Files**: `docs/admin-dashboard/category-list.html`, `category-add.html`, `category-edit.html`
- **Topbar Title**: `CATEGORIES LIST` / `CREATE CATEGORY`
- **Container Class**: `.container-xxl`
- **Domain Mapping (`config/demo/categories.php`)**:
  - **Row 1 (`category-list.html`) — 4 Featured Category Highlight Cards (`col-md-6 col-xl-3`)**:
    - `.card.card-body.text-center` with `.rounded.bg-secondary-subtle` (or `bg-primary-subtle`, `bg-warning-subtle`, `bg-info-subtle`) container holding `img.avatar-xl` + `h4.mt-3.mb-0` (`Face Care`, `Body Care`, `Serums`, `Moisturizers`).
  - **Row 2 (`category-list.html`) — All Categories Table Card (`col-xl-12`)**:
    - Columns: Checkbox, Category Image + `name` (`Face Care`, `Body Care`, `Serums`, `Moisturizers`, `Cleansers`, `Sunscreens`), `short_name` (`Face`, `Body`, `Sun Care`), `slug`, ` tagline`, `item_count` (Product Count), Action (`eye`, `pen`, `trash`).
  - **Category Create/Edit (`category-add.html`, `category-edit.html`)**:
    - Left `col-xl-3 col-lg-4` live preview card + Right `col-xl-9 col-lg-8` Dropzone thumbnail upload & Category Information form (`name`, `short_name`, `slug`, `tagline`, `description`).

---

## 4. Orders Module (`admin/orders/*`)

### 4.1 Orders List (`admin/orders/index.blade.php`)
- **Source File**: `docs/admin-dashboard/orders-list.html`
- **Topbar Title**: `ORDERS LIST`
- **Container Class**: `.container-xxl`
- **Row 1: 8 Order Status Summary Cards (`col-md-6 col-xl-3`, Variant B)**:
  1. `Payment Refund` (`solar:chat-round-money-broken`)
  2. `Order Cancel` (`solar:cart-cross-broken`)
  3. `Order Shipped` (`solar:box-broken`)
  4. `Order Delivering` (`solar:tram-broken`)
  5. `Pending Review` (`solar:clipboard-remove-broken`)
  6. `Pending Payment` (`solar:clock-circle-broken`)
  7. `Delivered` (`solar:clipboard-check-broken`)
  8. `In Progress` (`solar:inbox-line-broken`)
- **Row 2: All Order List Table Card (`col-xl-12`)**:
  - Mapped to `config/demo/account.php` (`orders`):
  - Columns: `Order ID` (`#EVB-9204`), `Created at` (`November 10, 2026`), `Customer` (`Eleanor Vance`), `Delivery Method`, `Total` (`$64.00`), `Payment Status` (`Paid` / `Unpaid` / `Refund`), `Items` (`items_count`), `Tracking Number` (`tracking_number`), `Order Status` (`Placed`, `Processing`, `Shipped`, `Delivered`, `Canceled`), `Action` (`eye`, `pen`, `trash`).

### 4.2 Order Detail (`admin/orders/show.blade.php`)
- **Source File**: `docs/admin-dashboard/order-detail.html`
- **Topbar Title**: `ORDER DETAILS`
- **Container Class**: `.container-xxl`
- **2-Column Layout (`row`)**:
  - **Left Column (`col-xl-9 col-lg-8`)**:
    1. **Order Header & 5-Step Progress Card**:
       - Order ID (`#EVB-9204`) + Payment Badge (`Paid`) + Status Badge (`Processing`) + Action buttons (`Refund`, `Return`, `Edit Order`).
       - 5-Step Progress Bar (`Order Confirming` → `Payment Confirmed` → `Processing` → `Shipping` → `Delivered`).
       - Footer with Estimated Shipping Date & `Mark As Ready To Ship` primary CTA.
    2. **Order Line Items Card (`Product`)**:
       - Table of ordered skincare items with `.avatar-md` thumbnail, `name`, `size` (`30ml / 1.0 fl oz`), `Status` badge (`Ready` / `Packaging`), `Quantity`, `Price`, `Tax`, `Amount`.
    3. **Order Timeline Card**:
       - Vertical dashed timeline showing timestamped fulfillment events, invoice actions (`Download Invoice`, `Resend Invoice`), and payment confirmation.
    4. **Order Metadata Strip Card (`.card.bg-light-subtle`)**:
       - 4-column horizontal strip (`border-end`): Store/Fulfillment Center (`solar:shop-2-bold-duotone`), Order Date (`solar:calendar-date-bold-duotone`), Paid By (`solar:user-circle-bold-duotone`), Reference ID (`solar:clipboard-text-bold-duotone`).
  - **Right Column (`col-xl-3 col-lg-4`)**:
    1. **Order Summary Card**: Table of `Sub Total`, `Discount`, `Delivery Charge`, `Estimated Tax`, and `.card-footer` `Total Amount`.
    2. **Payment Information Card**: Payment icon (`mastercard.svg` / Apple Pay), masked card number, transaction ID, cardholder name.
    3. **Customer Details Card**: Avatar, Customer Name (`Eleanor Vance`), Email (`eleanor.vance@example.com`), Contact Number (`+1 (503) 555-0192`), Shipping Address (`740 Botanical Way, Suite 400, Portland, OR 97201`), Billing Address, with inline edit icons (`bx-edit-alt`).

---

## 5. Customers, Coupons & Reviews Modules

### 5.1 Customers (`customer-list.html`, `customer-detail.html`)
- **`customer-list.html`**:
  - Row 1: 4 **Variant C KPI Cards** (`All Customers`, `Orders`, `Support Inquiries`, `Total Revenue`).
  - Row 2: Customer Table (`Checkbox`, `Customer Name` with `.avatar-sm.rounded-circle`, `Latest Order/Invoice ID`, `Status`, `Total Spent`, `Orders Count`, `Last Order Date`, `Payment Method`, `Action`).
- **`customer-detail.html`**:
  - Left column (`col-lg-4`): Customer profile card, contact info, saved shipping/billing addresses (`config/demo/account.php` `addresses`).
  - Right column (`col-lg-8`): Customer lifetime stats (Total Orders, Total Spend, Reward Points) + Customer Order History table + Transaction history.

### 5.2 Coupons (`coupons-list.html`, `coupons-add.html`)
- **Domain Mapping (`config/demo/site.php` `demo_discounts`)**:
  - Maps directly to Everbloom promo codes:
    - `WELCOME10` (`percentage`, `10%`, *10% Welcome Discount*)
    - `BLOOM10` (`percentage`, `10%`, *10% Welcome Discount*)
    - `FREESHIP` (`shipping`, `100%`, *Complimentary Standard Shipping*)
    - `BOTANICAL20` (`percentage`, `20%`, *20% Seasonal Harvest Discount*)
- **`coupons-add.html`**:
  - Left `col-lg-5`: **Coupon Status Card** (`Active`, `Inactive`, `Future Plan` radios) + **Date Schedule Card** (`#start-date`, `#end-date` Flatpickr inputs).
  - Right `col-lg-7`: **Coupon Information Card** (`coupons-code`, eligible category select `data-choices`, usage limits, Coupon Type radios: `Free Shipping`, `Percentage`, `Fixed Amount`, and `discount-value`).

### 5.3 Product Reviews (`pages-review.html`)
- **Source File**: `docs/admin-dashboard/pages-review.html`
- **Layout**: 4-column responsive grid (`col-xl-3 col-md-6`) of review cards showing review date/country, review quote, 5-star rating (`bx bxs-star text-warning`), quality label, and `.card-footer.bg-primary` with overlapping customer avatar (`avatar-lg border border-light border-3 rounded-circle`), quote watermark (`assets/images/double.png`), reviewer name, and verified buyer / product subtitle.

---

## 6. Store Settings Module (`admin/settings/index.blade.php`)

- **Source File**: `docs/admin-dashboard/settings.html`
- **Topbar Title**: `SETTINGS`
- **Container Class**: `.container-xxl`
- **Domain Mapping (`config/demo/site.php`)**:
  1. **Card 1: General & Announcement Bar Settings (`solar:settings-bold-duotone`)**:
     - Store Name (`The Everbloom`), Tagline (`Naturally Beautiful. Always Blooming.`), Announcement Bar Enabled toggle, Announcement Message (`Free shipping on orders over $75`), Announcement Link (`/shop`), Meta Description.
  2. **Card 2: Store Contact & Fulfillment Origin (`solar:shop-2-bold-duotone`)**:
     - Concierge Email (`concierge@the-everbloom.com`), Support Phone (`+1 (800) 412-8874`), Support Hours (`Mon – Fri: 9:00 AM – 6:00 PM EST`), Botanical Studio Address (`740 Botanical Way, Suite 400, Portland, OR 97201`).
  3. **Card 3: Shipping, Currency & Localization Settings (`solar:compass-bold-duotone`)**:
     - Currency (`USD`, `$`), Free Shipping Threshold (`$75.00`), Standard Shipping Rate (`$6.00`, `3–5 business days`), Express Shipping Rate (`$15.00`, `1–2 business days`), Return Window (`30 days`).
  4. **Row of 4 Compact Policy Cards (`col-lg-3`)**:
     - **Categories Settings** (`solar:box-bold-duotone`): Show Category Product Count (`Yes`/`No`), Default Items Per Page.
     - **Reviews Settings** (`solar:chat-square-check-bold-duotone`): Allow Reviews (`Yes`/`No`), Allow Guest Reviews (`Yes`/`No`).
     - **Coupons / Vouchers Settings** (`solar:ticket-bold-duotone`): Minimum / Maximum Vouchers.
     - **Tax Settings** (`solar:ticket-sale-bold-duotone`): Prices with Tax (`Yes`/`No`), Default Tax Rate.
  5. **Bottom Save Action Bar**: `Save Changes` (`.btn.btn-success`) and `Cancel` (`.btn.btn-danger`).
