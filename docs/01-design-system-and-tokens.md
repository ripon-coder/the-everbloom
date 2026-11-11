# 01 — Admin Dashboard Design System, Tokens & Theming Specification

> **AI Agent Context Header**
> - **Source CSS**: `docs/admin-dashboard/assets/css/app.css` (Bootstrap 5.3.3 + Larkon Custom Theme)
> - **Source Config JS**: `docs/admin-dashboard/assets/js/config.js`
> - **Purpose of This Document**: Complete reference of HTML root attributes, CSS custom properties, typography, color tokens, custom utility classes (`.bg-soft-*`, `.btn-soft-*`, `.badge-soft-*`, `.avatar-*`), and iconography rules.

---

## 1. Root HTML Theme Attributes & State Persistence

The Larkon admin template controls layout and theming entirely via **4 data attributes** on the `<html>` element. These attributes are initialized synchronously in `<head>` by `assets/js/config.js` using `sessionStorage.getItem("__LARKON_CONFIG__")`.

```html
<html lang="en"
      data-bs-theme="light"
      data-topbar-color="light"
      data-menu-color="dark"
      data-menu-size="sm-hover-active">
```

### 1.1 Attribute Matrix

| HTML Attribute | Allowed Values | Default (`config.js`) | Visual Effect |
| :--- | :--- | :--- | :--- |
| `data-bs-theme` | `light` \| `dark` | `light` | Switches global page background, cards, borders, and text between light and dark mode. |
| `data-topbar-color` | `light` \| `dark` | `light` | Controls `.topbar` background (`#f9f7f7` in light vs. `#22282e` in dark). |
| `data-menu-color` | `light` \| `dark` | `dark` | Controls `.main-nav` sidebar background (`#262d34` in dark vs. `#f8f5f1` in light). |
| `data-menu-size` | `default` \| `sm-hover-active` \| `sm-hover` \| `condensed` \| `hidden` | `sm-hover-active` (or `hidden` when `window.innerWidth <= 1140`) | Controls sidebar width (`280px` vs. `75px` collapsed vs. off-screen mobile drawer). |

---

## 2. Layout & Dimensional CSS Variables (`:root`)

Defined at the top of `app.css`:

| CSS Variable | Value | Usage |
| :--- | :--- | :--- |
| `--bs-main-nav-width` | `280px` | Expanded left sidebar width (`default`, `sm-hover-active`). |
| `--bs-main-nav-width-sm` | `75px` | Collapsed icon-only sidebar width (`condensed`, `sm-hover`). |
| `--bs-topbar-height` | `100px` | Fixed height of `.topbar` and `.logo-box` line-height. |
| `--bs-footer-height` | `60px` | Fixed height of `.footer` and bottom padding of `.page-content`. |
| `--bs-logo-lg-height` | `24px` | Height of full brand logo (`.logo-lg`). |
| `--bs-logo-sm-height` | `26px` | Height of compact icon logo (`.logo-sm`). |
| `--bs-main-nav-item-icon-size` | `22px` | Sidebar `<iconify-icon>` font size. |
| `--bs-main-nav-item-font-size` | `15px` | Sidebar link label font size. |
| `--bs-main-nav-item-padding-x` | `25px` | Horizontal padding of sidebar links. |
| `--bs-main-nav-item-padding-y` | `10px` | Vertical padding of sidebar links. |

---

## 3. Color System & Theme Tokens

### 3.1 Core Semantic Palette (`[data-bs-theme=light]`)

| Semantic Token | CSS Variable | Hex | RGB Variable | Subtle Background (`-bg-subtle`) | Border Subtle (`-border-subtle`) |
| :--- | :--- | :--- | :--- | :--- | :--- |
| **Primary (Larkon Orange)** | `--bs-primary` | `#ff6c2f` | `255, 108, 47` | `#ffe2d5` | `#ffc4ac` |
| **Secondary (Slate)** | `--bs-secondary` | `#5d7186` | `93, 113, 134` | `#dfe3e7` | `#bec6cf` |
| **Success (Emerald)** | `--bs-success` | `#22c55e` | `34, 197, 94` | `#d3f3df` | `#a7e8bf` |
| **Warning (Amber)** | `--bs-warning` | `#f9b931` | `249, 185, 49` | `#fef1d6` | `#fde3ad` |
| **Info (Teal/Cyan)** | `--bs-info` | `#4ecac2` | `78, 202, 194` | `#dcf4f3` | `#b8eae7` |
| **Danger (Coral Red)** | `--bs-danger` | `#ef5f5f` | `239, 95, 95` | `#fcdfdf` | `#f9bfbf` |
| **Purple** | `--bs-purple` | `#7f56da` | `127, 86, 218` | — | — |
| **Blue** | `--bs-blue` | `#1c84ee` | `28, 132, 238` | — | — |
| **Light** | `--bs-light` | `#eef2f7` | `238, 242, 247` | `#fcfcfd` | `#eef2f7` |
| **Dark** | `--bs-dark` | `#323a46` | `50, 58, 70` | `#b0b0bb` | `#8486a7` |

### 3.2 Surface, Text & Border Tokens (Light vs. Dark Mode)

| Surface / Element | CSS Variable | Light Mode (`[data-bs-theme=light]`) | Dark Mode (`[data-bs-theme=dark]`) |
| :--- | :--- | :--- | :--- |
| **Page Body Background** | `--bs-body-bg` | `#f9f7f7` (Warm Ivory Gray) | `#22282e` |
| **Card / Secondary Surface** | `--bs-secondary-bg` | `#ffffff` | `#282f36` |
| **Tertiary / Subtle Surface** | `--bs-tertiary-bg` | `#f8f9fa` | `#2f3943` |
| **Body Text Color** | `--bs-body-color` | `#5d7186` | `#aab8c5` |
| **Headings Color** | `--bs-headings-color` | `#313b5e` | `#aab8c5` |
| **Border Color** | `--bs-border-color` | `#eaedf1` | `#2f3944` |
| **Input Border Color** | `--bs-input-border-color` | `#d8dfe7` | `#3a4551` |
| **Sidebar Nav Background** | `--bs-main-nav-bg` | `#f8f5f1` (`menu-color=light`) | `#262d34` (`menu-color=dark`) |
| **Sidebar Nav Item Text** | `--bs-main-nav-item-color` | `#797b97` (`menu-color=light`) | `#9097a7` (`menu-color=dark`) |
| **Sidebar Nav Hover Text** | `--bs-main-nav-item-hover-color` | `#ff6c2f` (`menu-color=light`) | `#ffffff` (`menu-color=dark`) |
| **Topbar Background** | `--bs-topbar-bg` | `#f9f7f7` (`topbar-color=light`) | `#22282e` (`topbar-color=dark`) |
| **Topbar Search Input Bg** | `--bs-topbar-search-bg` | `#eae8e8` (`topbar-color=light`) | `#2c3238` (`topbar-color=dark`) |

### 3.3 Optional: Everbloom Botanical Brand Theme Override
By default, `docs/admin-dashboard` uses Larkon's signature orange (`#ff6c2f`) as `--bs-primary` with warm ivory-beige surfaces (`#f9f7f7` body, `#f8f5f1` light menu).
- **Option A (Zero-Friction Default)**: Keep Larkon's default `#ff6c2f` primary accent so all precompiled `.btn-primary`, `.btn-soft-primary`, and ApexCharts colors work out-of-the-box without CSS overrides.
- **Option B (Everbloom Botanical Accent Override)**: If the user requests matching the storefront's Forest Green (`#304C3D`), load a small `public/admin/assets/css/everbloom-admin-theme.css` after `app.min.css` that overrides `--bs-primary: #304C3D; --bs-primary-rgb: 48, 76, 61; --bs-primary-bg-subtle: #E1E8E3;`.

---

## 4. Typography System

Loaded automatically from Google Fonts at the top of `app.css`:
- **Headings (`h1`–`h6`, `.h1`–`.h6`)**: `"Hanken Grotesk", sans-serif` — `font-weight: 600`, `line-height: 1.1`, `margin-bottom: 10px`, `color: var(--bs-heading-color)`.
- **Body Text (`body`)**: `"Play", sans-serif` — `font-size: 0.875rem` (`14px`), `font-weight: 400`, `line-height: 1.5`, `color: var(--bs-body-color)`.
- **Monospace**: `SFMono-Regular, Menlo, Monaco, Consolas, monospace`.

### Font Size Utility Classes (`.fs-*`)
Larkon extends Bootstrap's font-size utilities with pixel-exact classes used heavily across all templates:
- `.fs-10` (`10px`), `.fs-11` (`11px`), `.fs-12` (`12px`), `.fs-13` (`13px`), `.fs-14` (`14px`), `.fs-15` (`15px`), `.fs-16` (`16px`), `.fs-18` (`18px`), `.fs-20` (`20px`), `.fs-22` (`22px`), `.fs-24` (`24px`), `.fs-32` (`32px`), `.fs-35` (`35px`), `.fs-48` (`48px`).

---

## 5. Border Radius & Box Shadows

Unlike the storefront (which uses sharp `2px` editorial corners), the Admin Dashboard uses soft, modern rounded corners:
- `--bs-border-radius`: `0.75rem` (`12px`) — default `.card` and `.rounded` radius
- `--bs-border-radius-sm`: `0.5rem` (`8px`)
- `--bs-border-radius-lg`: `1rem` (`16px`)
- `--bs-border-radius-xl`: `1.25rem` (`20px`)
- `--bs-border-radius-pill`: `50rem`
- `--bs-box-shadow`: `0px 3px 4px 0px rgba(0, 0, 0, 0.03)` (applied to all `.card` elements)
- `--bs-box-shadow-lg`: `0 5px 10px rgba(30, 32, 37, 0.12)` (dropdowns, popovers, condensed flyout menus)

---

## 6. Custom Larkon Utility Classes Reference

### 6.1 Avatar Sizing & Containers
Used for user profile pictures, product thumbnails in tables, and KPI card icon containers:

| Class | Dimensions (`height` × `width`) | Common Usage |
| :--- | :--- | :--- |
| `.avatar-xs` | `1.5rem × 1.5rem` (`24×24px`) | Compact inline status/assignee icons |
| `.avatar-sm` | `2.25rem × 2.25rem` (`36×36px`) | Topbar notifications, customer row avatars, size/color toggle buttons |
| `.avatar` | `3rem × 3rem` (`48×48px`) | Customer detail cards, payment method cards |
| `.avatar-md` | `3.5rem × 3.5rem` (`56×56px`) | **Standard KPI card icon box** & **Product table thumbnail box** |
| `.avatar-lg` | `4.5rem × 4.5rem` (`72×72px`) | Review cards, seller/user profile headers |
| `.avatar-xl` | `6rem × 6rem` (`96×96px`) | Category highlight cards (`category-list.html`) |
| `.avatar-xxl` | `7.5rem × 7.5rem` (`120×120px`) | Full profile hero |
| `.avatar-title` | `100% × 100%` flex centered | Inner `<span>` or `<iconify-icon>` inside `.avatar-*` |

### 6.2 Soft Buttons (`.btn-soft-*`)
Translucent `10%` opacity background with colored text that fills to solid color with white text on `:hover`:
- `.btn-soft-primary` (used for **Edit** action buttons in tables and primary CTAs)
- `.btn-soft-danger` (used for **Delete** action buttons in tables)
- `.btn-soft-success`, `.btn-soft-warning`, `.btn-soft-info`, `.btn-soft-secondary`, `.btn-soft-dark`

### 6.3 Soft & Subtle Backgrounds
- **`.bg-soft-{color}`**: `25%` opacity background (`rgba(var(--bs-{color}-rgb), 0.25)`), e.g., `.bg-soft-primary`, `.bg-soft-info`, `.bg-soft-warning`.
- **`.bg-{color}-subtle`**: Bootstrap 5.3 subtle background variables (`--bs-{color}-bg-subtle`), e.g., `.bg-light-subtle` (standard table `<thead>` and `.card-footer` background), `.bg-success-subtle`, `.bg-danger-subtle`, `.bg-primary-subtle`.
- **`.bg-primary.bg-opacity-10`**: `10%` opacity primary background (used on KPI icon boxes in `orders-list.html` and `customer-list.html`).

### 6.4 Badges (`.badge-soft-*`, `.badge-outline-*`, `.bg-*-subtle`)
Three badge patterns are used across tables and cards:
1. **Subtle Fill Badge**: `<span class="badge bg-success-subtle text-success px-2 py-1 fs-13">Paid</span>`
2. **Soft Badge**: `<span class="badge badge-soft-success">4.4%</span>` (`18%` opacity background)
3. **Outline Border Badge**: `<span class="badge border border-warning text-warning px-2 py-1 fs-13">Packaging</span>` or `.badge-outline-primary`

---

## 7. Iconography System

The Admin Dashboard uses **two icon systems simultaneously**:

### 7.1 Iconify Web Component (`<iconify-icon>`)
Rendered dynamically via `vendor.js` (Iconify runtime). Used for all sidebar navigation icons, topbar buttons, KPI cards, and table action buttons (`eye`, `pen`, `trash`).
- **Solar Icon Set (`solar:*`)**:
  - Sidebar & KPIs (`bold-duotone`): `solar:widget-5-bold-duotone`, `solar:t-shirt-bold-duotone`, `solar:clipboard-list-bold-duotone`, `solar:box-bold-duotone`, `solar:bag-smile-bold-duotone`, `solar:users-group-two-rounded-bold-duotone`, `solar:leaf-bold-duotone`, `solar:chat-square-like-bold-duotone`, `solar:settings-bold-duotone`, `solar:bill-list-bold-duotone`
  - Topbar & UI (`broken` / `linear` / `bold-duotone`): `solar:hamburger-menu-broken`, `solar:moon-bold-duotone`, `solar:bell-bing-bold-duotone`, `solar:clock-circle-bold-duotone`, `solar:magnifer-linear`
  - Table Row Actions (`broken`):
    - View: `<iconify-icon icon="solar:eye-broken" class="align-middle fs-18"></iconify-icon>`
    - Edit: `<iconify-icon icon="solar:pen-2-broken" class="align-middle fs-18"></iconify-icon>`
    - Delete: `<iconify-icon icon="solar:trash-bin-minimalistic-2-broken" class="align-middle fs-18"></iconify-icon>`
- **Iconamoon Set (`iconamoon:*`)**: Used in activity stream and footer (`iconamoon:heart-duotone`, `iconamoon:comment-dots-duotone`).

### 7.2 Boxicons Font (`<i class="bx ..."></i>`)
Loaded via `assets/css/icons.min.css`. Used for:
- Star ratings: `<i class="bx bxs-star text-warning"></i>`, `<i class="bx bxs-star-half text-warning"></i>`
- Trend arrows: `<i class="bx bxs-up-arrow fs-12"></i>`, `<i class="bx bxs-down-arrow fs-12"></i>`, `<i class="bx bx-up-arrow-alt"></i>`
- Status dots: `<i class="bx bxs-circle text-success me-1"></i>`
- Pagination arrows: `<i class="bx bx-left-arrow-alt"></i>`, `<i class="bx bx-right-arrow-alt"></i>`
- Form input group icons: `<i class="bx bx-dollar"></i>`, `<i class="bx bxs-discount"></i>`, `<i class="bx bx-cloud-upload"></i>`
- Sidebar collapse chevron: Automatically injected via CSS `.menu-arrow::after { content: "\ea4a"; font-family: boxicons; }`.
