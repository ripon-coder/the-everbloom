# 03 — Reusable Admin UI Components & Blade Markup Contracts

> **AI Agent Context Header**
> - **Source Files**: `index.html`, `product-list.html`, `product-add.html`, `orders-list.html`, `order-detail.html`, `customer-list.html`, `category-list.html`, `coupons-add.html`
> - **Purpose of This Document**: Exact, copy-pasteable HTML/Blade markup patterns and JS plugin initialization contracts for all recurring UI components in the Admin Dashboard.

---

## 1. KPI Stat Card Patterns (3 Variants)

The template uses three distinct KPI card layouts depending on the page.

### Variant A: Dashboard KPI Card with Trend Footer (`index.html`)
Used on the main dashboard (`col-md-6`). Features a `.bg-soft-primary` icon box on the left, metric on the right, and a `.card-footer` with trend percentage and "View More" link.

```html
<div class="col-md-6">
    <div class="card overflow-hidden">
        <div class="card-body">
            <div class="row">
                <div class="col-6">
                    <div class="avatar-md bg-soft-primary rounded">
                        <iconify-icon icon="solar:cart-5-bold-duotone" class="avatar-title fs-32 text-primary"></iconify-icon>
                    </div>
                </div>
                <div class="col-6 text-end">
                    <p class="text-muted mb-0 text-truncate">Total Orders</p>
                    <h3 class="text-dark mt-1 mb-0">1,284</h3>
                </div>
            </div>
        </div>
        <div class="card-footer py-2 bg-light bg-opacity-50">
            <div class="d-flex align-items-center justify-content-between">
                <div>
                    <!-- Use text-success + bxs-up-arrow OR text-danger + bxs-down-arrow -->
                    <span class="text-success"><i class="bx bxs-up-arrow fs-12"></i> 8.4%</span>
                    <span class="text-muted ms-1 fs-12">Last Month</span>
                </div>
                <a href="{{ route('admin.orders.index') }}" class="text-reset fw-semibold fs-12">View More</a>
            </div>
        </div>
    </div>
</div>
```

### Variant B: Compact Status Counter Card (`orders-list.html`)
Used in 4-column rows (`col-md-6 col-xl-3`) at the top of `orders-list.html` and `inventory-warehouse.html`. Title and number on the left, `.bg-primary.bg-opacity-10` icon box on the right.

```html
<div class="col-md-6 col-xl-3">
    <div class="card">
        <div class="card-body">
            <div class="d-flex align-items-center justify-content-between">
                <div>
                    <h4 class="card-title mb-2">Order Shipped</h4>
                    <p class="text-muted fw-medium fs-22 mb-0">630</p>
                </div>
                <div>
                    <div class="avatar-md bg-primary bg-opacity-10 rounded">
                        <iconify-icon icon="solar:box-broken" class="fs-32 text-primary avatar-title"></iconify-icon>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
```

### Variant C: Customer Metric Card with Pill Trend Badge (`customer-list.html`)
Used in 4-column rows (`col-md-6 col-xl-3`) on `customer-list.html`. Icon + title on the top row, metric + `.bg-success-subtle` badge on the bottom row.

```html
<div class="col-md-6 col-xl-3">
    <div class="card">
        <div class="card-body">
            <div class="d-flex align-items-center gap-2 mb-3">
                <div class="avatar-md bg-primary bg-opacity-10 rounded">
                    <iconify-icon icon="solar:users-group-two-rounded-bold-duotone" class="fs-32 text-primary avatar-title"></iconify-icon>
                </div>
                <div>
                    <h4 class="mb-0">All Customers</h4>
                </div>
            </div>
            <div class="d-flex align-items-center justify-content-between">
                <p class="text-muted fw-medium fs-22 mb-0">+2,410</p>
                <div>
                    <span class="badge text-success bg-success-subtle fs-12"><i class="bx bx-up-arrow-alt"></i>18.2%</span>
                </div>
            </div>
        </div>
    </div>
</div>
```

---

## 2. Standard Data Table Card Pattern (`product-list.html`, `orders-list.html`, `category-list.html`)

All index tables in the admin panel follow this unified card + responsive table + pagination footer pattern:

```html
<div class="card">
    <!-- 1. Card Header: Title + Primary CTA + Filter/Export Dropdown -->
    <div class="card-header d-flex justify-content-between align-items-center gap-1">
        <h4 class="card-title flex-grow-1">All Product List</h4>

        <a href="{{ route('admin.products.create') }}" class="btn btn-sm btn-primary">
            Add Product
        </a>

        <div class="dropdown">
            <a href="#" class="dropdown-toggle btn btn-sm btn-outline-light" data-bs-toggle="dropdown" aria-expanded="false">
                This Month
            </a>
            <div class="dropdown-menu dropdown-menu-end">
                <a href="#!" class="dropdown-item">Download</a>
                <a href="#!" class="dropdown-item">Export</a>
                <a href="#!" class="dropdown-item">Import</a>
            </div>
        </div>
    </div>

    <!-- 2. Responsive Table Container -->
    <div>
        <div class="table-responsive">
            <table class="table align-middle mb-0 table-hover table-centered">
                <thead class="bg-light-subtle">
                    <tr>
                        <th style="width: 20px;">
                            <div class="form-check ms-1">
                                <input type="checkbox" class="form-check-input" id="checkAll">
                                <label class="form-check-label" for="checkAll"></label>
                            </div>
                        </th>
                        <th>Product Name & Sizes</th>
                        <th>Price</th>
                        <th>Stock</th>
                        <th>Category</th>
                        <th>Rating</th>
                        <th>Action</th>
                    </tr>
                </thead>
                <tbody>
                    <tr>
                        <!-- Bulk Checkbox -->
                        <td>
                            <div class="form-check ms-1">
                                <input type="checkbox" class="form-check-input" id="checkItem1">
                                <label class="form-check-label" for="checkItem1">&nbsp;</label>
                            </div>
                        </td>
                        <!-- Product Thumbnail + Title + Variant Subtitle -->
                        <td>
                            <div class="d-flex align-items-center gap-2">
                                <div class="rounded bg-light avatar-md d-flex align-items-center justify-content-center overflow-hidden">
                                    <img src="{{ asset('images/products/botanical-radiance-serum-01.jpg') }}" alt="" class="avatar-md object-fit-cover">
                                </div>
                                <div>
                                    <a href="#!" class="text-dark fw-medium fs-15">Botanical Radiance Serum</a>
                                    <p class="text-muted mb-0 mt-1 fs-13"><span>Sizes : </span>30ml, 50ml</p>
                                </div>
                            </div>
                        </td>
                        <!-- Price -->
                        <td>$64.00</td>
                        <!-- Stock Info -->
                        <td>
                            <p class="mb-1 text-muted"><span class="text-dark fw-medium">142 Item</span> Left</p>
                            <p class="mb-0 text-muted">89 Sold</p>
                        </td>
                        <!-- Category -->
                        <td>Serums</td>
                        <!-- Rating Badge -->
                        <td>
                            <span class="badge p-1 bg-light text-dark fs-12 me-1">
                                <i class="bx bxs-star align-text-top fs-14 text-warning me-1"></i> 4.9
                            </span> 128 Review
                        </td>
                        <!-- 3-Button Action Group (View, Edit, Delete) -->
                        <td>
                            <div class="d-flex gap-2">
                                <a href="#!" class="btn btn-light btn-sm" title="View">
                                    <iconify-icon icon="solar:eye-broken" class="align-middle fs-18"></iconify-icon>
                                </a>
                                <a href="#!" class="btn btn-soft-primary btn-sm" title="Edit">
                                    <iconify-icon icon="solar:pen-2-broken" class="align-middle fs-18"></iconify-icon>
                                </a>
                                <button type="button" class="btn btn-soft-danger btn-sm" title="Delete">
                                    <iconify-icon icon="solar:trash-bin-minimalistic-2-broken" class="align-middle fs-18"></iconify-icon>
                                </button>
                            </div>
                        </td>
                    </tr>
                </tbody>
            </table>
        </div>
    </div>

    <!-- 3. Card Footer Pagination -->
    <div class="card-footer border-top">
        <nav aria-label="Page navigation">
            <ul class="pagination justify-content-end mb-0">
                <li class="page-item"><a class="page-link" href="javascript:void(0);">Previous</a></li>
                <li class="page-item active"><a class="page-link" href="javascript:void(0);">1</a></li>
                <li class="page-item"><a class="page-link" href="javascript:void(0);">2</a></li>
                <li class="page-item"><a class="page-link" href="javascript:void(0);">3</a></li>
                <li class="page-item"><a class="page-link" href="javascript:void(0);">Next</a></li>
            </ul>
        </nav>
    </div>
</div>
```

---

## 3. Semantic Status Badge Mapping Matrix

To keep status indicators consistent across Orders, Payments, Stock, and Coupons, use these exact Larkon badge class combinations:

| Domain Context | Status Value | Badge HTML Pattern |
| :--- | :--- | :--- |
| **Payment Status** | `Paid` | `<span class="badge bg-success text-light px-2 py-1 fs-13">Paid</span>` *(or `bg-success-subtle text-success`)* |
| **Payment Status** | `Unpaid` / `Pending` | `<span class="badge bg-light text-dark px-2 py-1 fs-13">Unpaid</span>` |
| **Payment Status** | `Refund` / `Refunded` | `<span class="badge bg-light text-dark px-2 py-1 fs-13">Refund</span>` |
| **Order Status** | `Completed` / `Delivered` | `<span class="badge border border-success text-success px-2 py-1 fs-13">Completed</span>` |
| **Order Status** | `Packaging` / `Processing` | `<span class="badge border border-warning text-warning px-2 py-1 fs-13">Processing</span>` |
| **Order Status** | `Shipped` | `<span class="badge border border-info text-info px-2 py-1 fs-13">Shipped</span>` |
| **Order Status** | `Draft` / `Placed` | `<span class="badge border border-secondary text-secondary px-2 py-1 fs-13">Placed</span>` |
| **Order Status** | `Canceled` | `<span class="badge border border-danger text-danger px-2 py-1 fs-13">Canceled</span>` |
| **Stock Status** | `in_stock` (`Ready`) | `<span class="badge bg-success-subtle text-success px-2 py-1 fs-13">In Stock</span>` |
| **Stock Status** | `low_stock` | `<span class="badge bg-warning-subtle text-warning px-2 py-1 fs-13">Low Stock</span>` |
| **Stock Status** | `out_of_stock` | `<span class="badge bg-danger-subtle text-danger px-2 py-1 fs-13">Out of Stock</span>` |

---

## 4. 5-Step Order Fulfillment Progress Bar (`order-detail.html`)

Used in `order-detail.html` (`row row-cols-xxl-5 row-cols-md-2 row-cols-1`) to visualize order progression:
1. **Completed Step**: `.progress-bar.progress-bar-striped.progress-bar-animated.bg-success` at `style="width: 100%"`
2. **Active Step**: `.progress-bar.progress-bar-striped.progress-bar-animated.bg-warning` at `style="width: 60%"` + inline `.spinner-border.spinner-border-sm.text-warning` next to the label
3. **Pending Step**: `.progress-bar.bg-primary` at `style="width: 0%"`

```html
<div class="row row-cols-xxl-5 row-cols-md-2 row-cols-1">
    <div class="col">
        <div class="progress mt-3" style="height: 10px;">
            <div class="progress-bar progress-bar-striped progress-bar-animated bg-success" role="progressbar" style="width: 100%"></div>
        </div>
        <p class="mb-0 mt-2">Order Confirming</p>
    </div>
    <div class="col">
        <div class="progress mt-3" style="height: 10px;">
            <div class="progress-bar progress-bar-striped progress-bar-animated bg-success" role="progressbar" style="width: 100%"></div>
        </div>
        <p class="mb-0 mt-2">Payment Confirmed</p>
    </div>
    <div class="col">
        <div class="progress mt-3" style="height: 10px;">
            <div class="progress-bar progress-bar-striped progress-bar-animated bg-warning" role="progressbar" style="width: 60%"></div>
        </div>
        <div class="d-flex align-items-center gap-2 mt-2">
            <p class="mb-0">Processing</p>
            <div class="spinner-border spinner-border-sm text-warning" role="status">
                <span class="visually-hidden">Loading...</span>
            </div>
        </div>
    </div>
    <div class="col">
        <div class="progress mt-3" style="height: 10px;">
            <div class="progress-bar progress-bar-striped progress-bar-animated bg-primary" role="progressbar" style="width: 0%"></div>
        </div>
        <p class="mb-0 mt-2">Shipping</p>
    </div>
    <div class="col">
        <div class="progress mt-3" style="height: 10px;">
            <div class="progress-bar progress-bar-striped progress-bar-animated bg-primary" role="progressbar" style="width: 0%"></div>
        </div>
        <p class="mb-0 mt-2">Delivered</p>
    </div>
</div>
```

---

## 5. Form Controls & Interactive Plugin Contracts

`app.js` (`FormAdvanced`) and `vendor.js` automatically initialize several form plugins based on HTML data attributes:

### 5.1 Enhanced Select Dropdowns (`Choices.js`)
Any `<select>` with `data-choices` is automatically transformed by `FormAdvanced.initFormChoices()` in `app.js`:
- **Single Select with Groups/Placeholder**:
  ```html
  <label for="product-categories" class="form-label">Product Category</label>
  <select class="form-control" id="product-categories" data-choices data-choices-groups data-placeholder="Select Category" name="category_slug">
      <option value="">Choose a category</option>
      <option value="serums">Serums</option>
      <option value="moisturizers">Moisturizers</option>
      <option value="cleansers">Cleansers</option>
  </select>
  ```
- **Multi-Select Tag Input with Remove Button**:
  ```html
  <select class="form-control" id="product-tags" data-choices data-choices-removeItem name="tags[]" multiple>
      <option value="Bestseller" selected>Bestseller</option>
      <option value="Signature">Signature</option>
      <option value="Vegan">Vegan</option>
  </select>
  ```
- **Supported `data-choices-*` modifiers in `app.js`**:
  - `data-choices-search-false` / `data-choices-search-true`
  - `data-choices-removeItem` / `data-choices-multiple-remove`
  - `data-choices-sorting-false`
  - `data-choices-limit="5"`

### 5.2 Drag-and-Drop Image Upload (`Dropzone.js`)
Initialized in `assets/js/pages/ecommerce-product-details.js` (used on `product-add.html`, `product-edit.html`, `category-add.html`):
- Requires `.dropzone` container and `#dropzone-preview` + `#dropzone-preview-list` template `<li>`:
  ```html
  <div class="dropzone bg-light-subtle py-5">
      <div class="fallback">
          <input name="file" type="file" multiple="multiple">
      </div>
      <div class="dz-message needsclick">
          <i class="bx bx-cloud-upload fs-48 text-primary"></i>
          <h3 class="mt-4">Drop your product images here, or <span class="text-primary">click to browse</span></h3>
          <span class="text-muted fs-13">1600 x 1200 (4:3) recommended. PNG, JPG and WEBP files are allowed</span>
      </div>
  </div>
  <ul class="list-unstyled mb-0" id="dropzone-preview">
      <li class="mt-2" id="dropzone-preview-list">
          <div class="border rounded">
              <div class="d-flex p-2">
                  <div class="flex-shrink-0 me-3">
                      <div class="avatar-sm bg-light rounded">
                          <img data-dz-thumbnail class="img-fluid rounded d-block" src="#" alt="Dropzone-Image" />
                      </div>
                  </div>
                  <div class="flex-grow-1">
                      <div class="pt-1">
                          <h5 class="fs-14 mb-1" data-dz-name>&</h5>
                          <p class="fs-13 text-muted mb-0" data-dz-size></p>
                          <strong class="error text-primary" data-dz-errormessage></strong>
                      </div>
                  </div>
                  <div class="flex-shrink-0 ms-3">
                      <button data-dz-remove class="btn btn-sm btn-primary">Delete</button>
                  </div>
              </div>
          </div>
      </li>
  </ul>
  ```

### 5.3 Input Group Currency / Percentage Fields (`product-add.html`)
```html
<label for="product-price" class="form-label">Price</label>
<div class="input-group mb-3">
    <span class="input-group-text fs-20"><i class="bx bx-dollar"></i></span>
    <input type="number" step="0.01" id="product-price" name="price" class="form-control" placeholder="0.00">
</div>
```

### 5.4 Size / Volume Toggle Pill Checkboxes (`product-add.html`)
Used for selecting product bottle sizes (`30ml`, `50ml`, `100ml`, `120ml`, `200ml`):
```html
<div class="d-flex flex-wrap gap-2" role="group" aria-label="Bottle size toggle group">
    <input type="checkbox" class="btn-check" id="size-30ml" name="sizes[]" value="30ml / 1.0 fl oz" checked>
    <label class="btn btn-light px-3 py-2 rounded d-flex justify-content-center align-items-center" for="size-30ml">30ml</label>

    <input type="checkbox" class="btn-check" id="size-50ml" name="sizes[]" value="50ml / 1.7 fl oz">
    <label class="btn btn-light px-3 py-2 rounded d-flex justify-content-center align-items-center" for="size-50ml">50ml</label>

    <input type="checkbox" class="btn-check" id="size-100ml" name="sizes[]" value="100ml / 3.4 fl oz">
    <label class="btn btn-light px-3 py-2 rounded d-flex justify-content-center align-items-center" for="size-100ml">100ml</label>
</div>
```

### 5.5 Date Pickers (`Flatpickr`)
Used in `coupons-add.html` (`assets/js/pages/coupons-add.js`) for `#start-date` and `#end-date`:
```html
<label for="start-date" class="form-label text-dark">Start Date</label>
<input type="text" id="start-date" name="starts_at" class="form-control flatpickr-input" placeholder="dd-mm-yyyy">
```
Initialized via `flatpickr("#start-date", { dateFormat: "d-m-Y" })`.

### 5.6 Declarative Toast Notifications (`Toastify` in `app.js`)
Any button or element with `data-toast` triggers a Toastify notification via `ToastNotification.initToastNotification()` in `app.js`:
```html
<button type="button" class="btn btn-primary"
        data-toast
        data-toast-text="Product saved successfully!"
        data-toast-gravity="top"
        data-toast-position="right"
        data-toast-className="success"
        data-toast-duration="3000"
        data-toast-close="close">
    Save Changes
</button>
```

---

## 6. Vertical Dashed Timeline Pattern (`order-detail.html`)

Used for order history logs and activity streams:
```html
<div class="position-relative ms-2">
    <!-- Vertical Dashed Line -->
    <span class="position-absolute start-0 top-0 border border-dashed h-100"></span>

    <!-- Timeline Node Item -->
    <div class="position-relative ps-4">
        <div class="mb-4">
            <span class="position-absolute start-0 avatar-sm translate-middle-x bg-light d-inline-flex align-items-center justify-content-center rounded-circle text-success fs-20">
                <i class="bx bx-check-circle"></i>
            </span>
            <div class="ms-2 d-flex flex-wrap gap-2 align-items-center justify-content-between">
                <div>
                    <h5 class="mb-1 text-dark fw-medium fs-15">Order Payment Confirmed</h5>
                    <p class="mb-2">Paid via Apple Pay (•••• 9840)</p>
                    <span class="badge bg-success-subtle text-success px-2 py-1 fs-13">Paid</span>
                </div>
                <p class="mb-0">November 10, 2026, 09:40 AM</p>
            </div>
        </div>
    </div>
</div>
```
