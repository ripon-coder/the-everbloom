PHASE 4: Cart and checkout (demo only).

1. Cart drawer: complete behavior (items, qty, remove, subtotal, empty state,
   focus trap, Esc to close).
2. cart/index.blade.php: image, name, variant, unit price, quantity, remove, subtotal,
   discount code field (demo codes in config), shipping estimate, total,
   Continue Shopping, Proceed to Checkout.
3. checkout/index.blade.php using the checkout layout: customer info, email,
   shipping address, country and city (list in config), shipping method,
   payment method selection (UI only), discount code, order summary,
   Place Order.
   - Alpine form validation with accessible error messages
   - Two columns on desktop, single column on mobile, summary collapsible on mobile
   - Place Order shows a clear "Demo checkout: no payment processed" message
     and does NOT show a fake order confirmation

Follow the output format from the rules.