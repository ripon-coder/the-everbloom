import './bootstrap';
import Alpine from 'alpinejs';
import collapse from '@alpinejs/collapse';
import focus from '@alpinejs/focus';
import intersect from '@alpinejs/intersect';

Alpine.plugin(collapse);
Alpine.plugin(focus);
Alpine.plugin(intersect);

// Global Cart Store
Alpine.store('cart', {
    items: [],
    drawerOpen: false,

    init() {
        try {
            const saved = localStorage.getItem('everbloom_cart');
            if (saved) {
                this.items = JSON.parse(saved);
            }
        } catch (e) {
            console.warn('Unable to load cart from localStorage:', e);
            this.items = [];
        }
    },

    save() {
        try {
            localStorage.setItem('everbloom_cart', JSON.stringify(this.items));
        } catch (e) {
            console.warn('Unable to save cart to localStorage:', e);
        }
    },

    openDrawer() {
        this.drawerOpen = true;
        document.body.classList.add('overflow-hidden');
    },

    closeDrawer() {
        this.drawerOpen = false;
        document.body.classList.remove('overflow-hidden');
    },

    toggleDrawer() {
        if (this.drawerOpen) {
            this.closeDrawer();
        } else {
            this.openDrawer();
        }
    },

    add(product, quantity = 1, size = null, open = true) {
        const qty = parseInt(quantity, 10) || 1;
        const selectedSize = size || (product.sizes && product.sizes.length > 0 ? product.sizes[0] : null);
        const cartKey = selectedSize ? `${product.id}-${selectedSize}` : `${product.id}`;

        const existing = this.items.find(item => item.key === cartKey);
        if (existing) {
            existing.quantity += qty;
        } else {
            this.items.push({
                key: cartKey,
                id: product.id,
                slug: product.slug,
                name: product.name,
                category: product.category,
                price: parseFloat(product.price),
                compare_at_price: product.compare_at_price ? parseFloat(product.compare_at_price) : null,
                quantity: qty,
                size: selectedSize,
                image: (product.images && product.images.length > 0) ? product.images[0] : null,
            });
        }

        this.save();
        if (open) {
            this.openDrawer();
        }
    },

    remove(key) {
        this.items = this.items.filter(item => item.key !== key);
        this.save();
    },

    updateQty(key, quantity) {
        const qty = parseInt(quantity, 10);
        if (qty <= 0) {
            this.remove(key);
            return;
        }
        const item = this.items.find(item => item.key === key);
        if (item) {
            item.quantity = qty;
            this.save();
        }
    },

    clear() {
        this.items = [];
        this.save();
    },

    get count() {
        return this.items.reduce((total, item) => total + (item.quantity || 0), 0);
    },

    get subtotal() {
        return this.items.reduce((sum, item) => sum + (item.price * item.quantity), 0);
    },
});

// Global Wishlist Store
Alpine.store('wishlist', {
    items: [],

    init() {
        try {
            const saved = localStorage.getItem('everbloom_wishlist');
            if (saved) {
                this.items = JSON.parse(saved);
            }
        } catch (e) {
            console.warn('Unable to load wishlist from localStorage:', e);
            this.items = [];
        }
    },

    save() {
        try {
            localStorage.setItem('everbloom_wishlist', JSON.stringify(this.items));
        } catch (e) {
            console.warn('Unable to save wishlist to localStorage:', e);
        }
    },

    has(id) {
        const targetId = String(id);
        return this.items.some(item => String(item.id || item) === targetId);
    },

    toggle(product) {
        const targetId = typeof product === 'object' ? product.id : product;
        if (this.has(targetId)) {
            this.items = this.items.filter(item => String(item.id || item) !== String(targetId));
        } else {
            if (typeof product === 'object') {
                this.items.push({
                    id: product.id,
                    slug: product.slug,
                    name: product.name,
                    price: product.price,
                    category: product.category,
                    image: (product.images && product.images.length > 0) ? product.images[0] : null,
                });
            } else {
                this.items.push({ id: targetId });
            }
        }
        this.save();
    },

    get count() {
        return this.items.length;
    },
});

// Alpine Mobile Navigation Component
Alpine.data('mobileNavigation', () => ({
    open: false,
    toggle() {
        this.open = !this.open;
        if (this.open) {
            document.body.classList.add('overflow-hidden');
        } else {
            document.body.classList.remove('overflow-hidden');
        }
    },
    close() {
        this.open = false;
        document.body.classList.remove('overflow-hidden');
    }
}));

// Alpine Search Modal Component
Alpine.data('searchModal', () => ({
    open: false,
    query: '',
    products: [],

    init() {
        const el = document.getElementById('everbloom-search-data');
        if (el) {
            try {
                this.products = JSON.parse(el.textContent);
            } catch (e) {
                console.warn('Failed to parse search products JSON:', e);
            }
        }
    },

    openModal() {
        this.open = true;
        this.$nextTick(() => {
            const input = document.getElementById('storefront-search-input');
            if (input) input.focus();
        });
    },

    closeModal() {
        this.open = false;
        this.query = '';
    },

    get results() {
        if (!this.query.trim()) return [];
        const q = this.query.toLowerCase().trim();
        return this.products.filter(p => 
            (p.name && p.name.toLowerCase().includes(q)) || 
            (p.category && p.category.toLowerCase().includes(q)) || 
            (p.short_description && p.short_description.toLowerCase().includes(q))
        ).slice(0, 6);
    }
}));

window.Alpine = Alpine;
Alpine.start();
