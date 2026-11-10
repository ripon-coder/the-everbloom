PHASE 1: Foundation and design system.

Build:
1. Install/config steps: Tailwind config with all tokens, fonts, Alpine plugins,
   Vite setup, resources/js/app.js with the cart and wishlist stores.
2. resources/css/app.css: base styles, focus styles, typography utilities,
   reduced-motion handling.
3. layouts/app.blade.php and layouts/checkout.blade.php with full SEO stacks.
4. config/demo/*.php with all demo data (16+ products, categories, nav, testimonials, FAQ, site).
5. Components: announcement-bar, header, navigation, mobile-navigation, search-modal,
   cart-drawer, footer, breadcrumbs, button, image, section-heading, rating,
   price-display, quantity-selector, wishlist-button, add-to-cart-button,
   newsletter-form, accordion.
6. routes/web.php with view-only routes for ALL pages (placeholder views are fine for now)
   and a small controller or closures that pass demo data.
7. A temporary /styleguide route showing colors, type scale, buttons, inputs,
   and each component.

Follow the output format from the rules.