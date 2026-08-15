import './bootstrap';
import 'flowbite';
import Alpine from 'alpinejs';

window.Alpine = Alpine;

// Register Alpine Cart Drawer Store
Alpine.store('cartDrawer', {
    isOpen: false,
    open() {
        this.isOpen = true;
    },
    close() {
        this.isOpen = false;
    },
    toggle() {
        this.isOpen = !this.isOpen;
    }
});

// Register Alpine Mobile Menu Store
Alpine.store('mobileMenu', {
    isOpen: false,
    open() {
        this.isOpen = true;
    },
    close() {
        this.isOpen = false;
    },
    toggle() {
        this.isOpen = !this.isOpen;
    }
});

// Register Alpine Cart Drawer Component Data
Alpine.data('cartDrawerData', () => ({
    cart: [],
    loadCart() {
        try {
            let localCart = localStorage.getItem('cart');
            let parsed = null;
            if (localCart !== null && localCart !== 'undefined') {
                try {
                    parsed = JSON.parse(localCart);
                } catch(e) {
                    parsed = null;
                }
            }

            if (parsed === null && window.initialCartSession) {
                parsed = window.initialCartSession;
            }

            if (parsed && typeof parsed === 'object' && !Array.isArray(parsed)) {
                parsed = Object.values(parsed);
            }

            if (Array.isArray(parsed)) {
                this.cart = parsed.filter(item => item && typeof item === 'object');
            } else {
                this.cart = [];
            }
        } catch(err) {
            console.error('Error loading cart in drawer:', err);
            this.cart = [];
        }
    },
    get cartCount() {
        try {
            if (!Array.isArray(this.cart)) return 0;
            return this.cart.reduce((total, item) => total + parseInt(item ? (item.quantity || 0) : 0), 0);
        } catch(e) {
            return 0;
        }
    },
    get cartTotal() {
        try {
            if (!Array.isArray(this.cart)) return 0;
            return this.cart.reduce((total, item) => {
                if (!item) return total;
                let price = parseFloat(item.unit_final_price || item.unit_base_price || item.price || 0);
                let qty = parseInt(item.quantity || 0);
                return total + (isNaN(price) ? 0 : price) * (isNaN(qty) ? 0 : qty);
            }, 0);
        } catch(e) {
            return 0;
        }
    },
    formatPrice(price) {
        try {
            let num = parseFloat(price || 0);
            if (isNaN(num)) num = 0;
            return num.toLocaleString(undefined, { minimumFractionDigits: 2, maximumFractionDigits: 2 });
        } catch(e) {
            return '0.00';
        }
    },
    updateQuantity(index, delta) {
        try {
            if (!this.cart || !this.cart[index]) return;
            let currentTotalQty = this.cartCount;
            let newQuantity = parseInt(this.cart[index].quantity || 0) + delta;

            if (delta > 0 && currentTotalQty + delta > 30) {
                window.dispatchEvent(new CustomEvent('notify', { detail: { message: 'You cannot add more than 30 products to your cart.', type: 'error' } }));
                return;
            }

            if (newQuantity > 0) {
                this.cart[index].quantity = newQuantity;
                let unitPrice = parseFloat(this.cart[index].unit_final_price || this.cart[index].unit_base_price || this.cart[index].price || 0);
                this.cart[index].line_total = newQuantity * (isNaN(unitPrice) ? 0 : unitPrice);
                this.saveCart();
            } else {
                this.removeItem(index);
            }
        } catch(e) {
            console.error('Error updating quantity:', e);
        }
    },
    removeItem(index) {
        try {
            if (!this.cart || index < 0 || index >= this.cart.length) return;
            this.cart.splice(index, 1);
            this.saveCart();
        } catch(e) {
            console.error('Error removing item:', e);
        }
    },
    saveCart() {
        try {
            this.cart = (this.cart || []).filter(item => item && typeof item === 'object' && parseInt(item.quantity || 0) > 0);
            localStorage.setItem('cart', JSON.stringify(this.cart));
            const csrfToken = document.querySelector('meta[name="csrf-token"]')?.getAttribute('content') || '';
            fetch('/cart/sync', {
                method: 'POST',
                headers: {
                    'Content-Type': 'application/json',
                    'X-CSRF-TOKEN': csrfToken
                },
                body: JSON.stringify({ cart: this.cart, type: 'cart' })
            }).catch(error => console.error('Error syncing cart:', error));
            window.dispatchEvent(new CustomEvent('cart-updated-internal', { detail: { skipReload: true } }));
        } catch(e) {
            console.error('Error saving cart:', e);
        }
    },
    init() {
        this.loadCart();
        this.$watch('$store.cartDrawer.isOpen', value => { if(value) this.loadCart(); });
        window.addEventListener('cart-updated', (e) => {
            if (e && e.detail && e.detail.cart) {
                let parsed = e.detail.cart;
                if (parsed && typeof parsed === 'object' && !Array.isArray(parsed)) {
                    parsed = Object.values(parsed);
                }
                if (Array.isArray(parsed)) {
                    this.cart = parsed.filter(item => item && typeof item === 'object');
                    return;
                }
            }
            this.loadCart();
        });
        window.addEventListener('cart-updated-internal', (e) => {
            if (e && e.detail && e.detail.skipReload) return;
            this.loadCart();
        });
        window.addEventListener('open-cart-drawer', () => this.loadCart());
        window.addEventListener('storage', () => this.loadCart());
    }
}));

window.addQuickToCart = function(product) {
    if (!product || !product.id) return;
    
    let rawCart = localStorage.getItem('cart');
    let cart = [];
    try {
        cart = rawCart ? JSON.parse(rawCart) : [];
    } catch(e) {
        cart = [];
    }
    if (cart && typeof cart === 'object' && !Array.isArray(cart)) {
        cart = Object.values(cart);
    }
    if (!Array.isArray(cart)) {
        cart = [];
    }
    let existingIndex = cart.findIndex(item => item.product_id === product.id && !item.variant_id);
    
    if (existingIndex > -1) {
        cart[existingIndex].quantity += 1;
        cart[existingIndex].line_total = cart[existingIndex].quantity * cart[existingIndex].unit_final_price;
    } else {
        cart.push({
            variant_id: null,
            product_id: product.id,
            name: product.name,
            slug: product.slug,
            image: product.image,
            unit_base_price: parseFloat(product.price || 0),
            unit_final_price: parseFloat(product.price || 0),
            quantity: 1,
            available_stock: 99,
            line_total: parseFloat(product.price || 0),
            meta: {}
        });
    }
    
    localStorage.setItem('cart', JSON.stringify(cart));
    
    fetch('/cart/sync', {
        method: 'POST',
        headers: {
            'Content-Type': 'application/json',
            'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]')?.getAttribute('content') || ''
        },
        body: JSON.stringify({ cart: cart, type: 'cart' })
    }).catch(err => console.error('Cart sync error:', err));
    
    window.dispatchEvent(new CustomEvent('cart-updated', { detail: { cart: cart } }));
    if (window.Alpine && window.Alpine.store('cartDrawer')) {
        window.Alpine.store('cartDrawer').open();
    }
};

if (!window.AlpineStarted) {
    window.AlpineStarted = true;
    Alpine.start();
}
