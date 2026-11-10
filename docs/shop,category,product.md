PHASE 3: Catalogue and product pages.

1. shop/index.blade.php: breadcrumbs, title, description, product count, sidebar filters
   (category, price range, availability), sort dropdown, active filter chips,
   "Clear all", responsive grid (4/3/2 columns), pagination component.
   Mobile: filter drawer + sort control.
   Filtering and sorting work in Alpine on the demo data.
   BACKEND comment: replace with server-side query params.
2. shop/category.blade.php: one reusable template driven by the category slug
   (Face Care, Body Care, Serums, Moisturizers, Cleansers), with banner and description.
3. shop/product.blade.php driven by slug:
   - Left: gallery with thumbnails, image switching, hover/tap zoom
   - Right: breadcrumb, category, name, rating, price, compare-at price, discount %,
     short description, variant/size selector, quantity selector, Add to Cart (opens cart drawer),
     Buy Now (goes to checkout demo), wishlist, stock status
   - Below: accordions for Description, Ingredients, How to Use, Product Details,
     Shipping; reviews section (sample reviews); related products
   - Product JSON-LD and BreadcrumbList

Follow the output format from the rules.