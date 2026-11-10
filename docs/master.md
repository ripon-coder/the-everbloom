# PROJECT RULES: THE EVERBLOOM (Premium Skincare E-commerce Frontend)

Act as a senior UI/UX designer, creative director, and expert Laravel frontend developer.

Build the FRONTEND ONLY of a premium, conversion-focused skincare e-commerce site
for the brand THE EVERBLOOM (https://the-everbloom.com).
Tagline: "Naturally Beautiful. Always Blooming."
Goal: it must look like a real international premium brand, not a template,
and be presentable to buyers as part of a turnkey online business.

I will give you work in phases. Follow these rules in every phase.

## 1. Versions and stack (strict)
- Laravel 11, Blade, Vite
- Tailwind CSS v3.4 (config-based tokens in tailwind.config.js)
- Alpine.js 3 with plugins: collapse, focus, intersect (installed via npm, bundled by Vite)
- Icons: Lucide, inline SVG inside Blade components (no icon fonts)
- Fonts: Playfair Display (headings) and Inter (body), loaded from Google Fonts
  with preconnect and font-display: swap
- Forbidden: React, Vue, Next.js, Bootstrap, jQuery, any other frontend framework
- JavaScript lives in resources/js/ as small modules; no inline <script> blobs in Blade
  except Alpine x-data attributes

## 2. Backend boundary
- Allowed: web routes, simple view-only controllers (or Route::view), a demo data layer
- Forbidden: models, migrations, auth logic, payment logic, database queries
- Checkout is a DEMO. Never show a fake "payment successful" or fake order confirmation.
  The Place Order button shows a clear "Demo checkout: no payment was processed" notice.
- Mark every integration point with a comment: // BACKEND: replace with real data

## 3. Demo data layer
- All sample content lives in config/demo/ (products.php, categories.php,
  testimonials.php, navigation.php, faq.php, site.php), never hardcoded in views.
- At least 16 products across Face Care, Body Care, Serums, Moisturizers, Cleansers,
  Sunscreens. Each has: id, slug, name, category, price, compare_at_price (nullable),
  rating, review_count, short_description, description, ingredients, how_to_use,
  sizes/variants, stock status, and an images array (3 to 4 distinct images).
- Currency: USD by default, controlled by one value in config/demo/site.php
  (so it can be switched to BDT or another currency later).
- Testimonials are labeled "Sample review" in the UI.
- No unverifiable claims (no "clinically proven", "dermatologist approved",
  "100% organic", etc.). Use soft, factual language like "carefully selected ingredients".

## 4. Images
- Do not invent real photos. Use a placeholder system:
  public/images/{hero,categories,products,editorial,gallery}/ with descriptive file names
  (e.g. serum-vitamin-c-01.jpg).
- Until I add real photos, use <x-storefront.image> that renders a tinted
  sage/ivory placeholder block with the intended filename and aspect ratio.
- At the end of each phase, output an "IMAGE SLOTS" table:
  file path | aspect ratio | what the photo should show (style: botanical, natural light,
  sage and ivory backgrounds, editorial).
- All images need alt text, width/height attributes, aspect-ratio classes
  (prevent layout shift), and loading="lazy" below the fold.

## 5. Design system
Colors (add as Tailwind tokens):
- forest #304C3D | sage #A8B7A0 | sage-light #E8EEE5 | ivory #F8F6F0
- beige #E8DFD0 | ink #292D29 | muted #777D74 | white #FFFFFF

Typography:
- Headings: Playfair Display. Body: Inter.
- Uppercase labels: text-xs, tracking-[0.2em], font-medium.
- Define a clear scale for display, h1 to h4, body, small, label.

Shape and style rules (specific, follow exactly):
- Buttons and inputs: rounded-none or rounded-sm only.
- Product cards: no border radius, no heavy shadows; image on top, text below.
- Allowed radius exceptions: one arch-shaped image in the hero, and circular avatars.
- Not every section is a card. Alternate layouts: full-bleed image sections,
  split layouts, text-only bands, asymmetric grids.
- Section vertical padding: py-16 on mobile, max py-24 on desktop. No oversized empty gaps.
- Max gradient use: none, except a subtle overlay for text legibility on images.
- Animations: only subtle opacity/translate transitions (150 to 300ms) and hover states.
  Respect prefers-reduced-motion.
- Avoid: dashboard-like layouts, floating decorative blobs, generic AI-looking sections.
- Primary button: forest background, ivory text, uppercase label style.
  Secondary: forest outline. Tertiary: underlined text link.

## 6. Alpine state strategy
- Only TWO global stores: Alpine.store('cart') and Alpine.store('wishlist'),
  persisted to localStorage (wrapped in try/catch) for demo purposes.
- Everything else uses local x-data components (gallery, variant picker, accordion,
  filters, drawers, newsletter validation).
- Cart store: items, add, remove, updateQty, count, subtotal, drawer open state.

## 7. Blade architecture
resources/views/
  layouts/app.blade.php (and checkout.blade.php, a minimal distraction-free layout)
  components/storefront/ (announcement-bar, header, navigation, mobile-navigation, footer,
    breadcrumbs, product-card, product-grid, category-card, price-display, rating,
    quantity-selector, wishlist-button, add-to-cart-button, cart-drawer, search-modal,
    newsletter-form, pagination, image, section-heading, button, accordion)
  frontend/ (home, shop/index, shop/category, shop/product, cart/index, checkout/index,
    account/*, pages/*)
Use anonymous Blade components with @props. Use @stack('meta') and @yield/@section for
per-page SEO.

## 8. SEO and accessibility (every page)
- Semantic HTML, one h1 per page, correct heading order
- Unique <title>, meta description, canonical placeholder, Open Graph tags
- JSON-LD integration points (Organization, Product, BreadcrumbList) as @stack('structured-data')
- Keyboard-navigable menus and drawers, focus trap in drawers/modals (Alpine focus plugin),
  Esc to close, visible focus rings, aria-labels, labeled form fields, skip-to-content link

## 9. Responsive and performance
- Breakpoints: mobile 320 to 767, tablet 768 to 1023, laptop 1024 to 1439, desktop 1440+
- Mobile first. Touch targets at least 44px. No horizontal overflow.
- Product grid: 2 columns mobile, 3 tablet, 4 desktop.
- Minimal JS, lazy-load below the fold, explicit image dimensions, efficient CSS.

## 10. Output format for every phase
1. Short plan (max 10 lines)
2. Files created or changed (tree)
3. FULL code for every file. Never abbreviate, never write "same as above" or "etc."
4. IMAGE SLOTS table
5. Manual QA checklist for me to test (breakpoints, keyboard, Alpine interactions)
6. Anything left for the next phase

Reply "Rules understood" and wait for Phase 1.