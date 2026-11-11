import './bootstrap';
import Alpine from 'alpinejs';
import collapse from '@alpinejs/collapse';
import focus from '@alpinejs/focus';
import intersect from '@alpinejs/intersect';

Alpine.plugin(collapse);
Alpine.plugin(focus);
Alpine.plugin(intersect);

/**
 * Retrieve the products catalog loaded from config/demo/products.php.
 * Checked dynamically to ensure values (names, images, prices) are derived on every render.
 */
function getProductsCatalog() {
    if (window.__everbloomProducts && Array.isArray(window.__everbloomProducts) && window.__everbloomProducts.length > 0) {
        return window.__everbloomProducts;
    }
    if (typeof document !== 'undefined') {
        const el = document.getElementById('everbloom-products-data') || document.getElementById('everbloom-search-data');
        if (el) {
            try {
                const data = JSON.parse(el.textContent);
                if (Array.isArray(data) && data.length > 0) {
                    window.__everbloomProducts = data;
                    return data;
                }
            } catch (e) {
                console.warn('Failed to parse catalog JSON:', e);
            }
        }
    }
    return window.__everbloomProducts || [];
}

/**
 * Retrieve demo discount codes from config/demo/site.php or defaults.
 */
function getDemoDiscounts() {
    const defaults = {
        'WELCOME10': { type: 'percentage', value: 10, label: '10% Welcome Discount' },
        'BLOOM10': { type: 'percentage', value: 10, label: '10% Welcome Discount' },
        'FREESHIP': { type: 'shipping', value: 100, label: 'Complimentary Standard Shipping' },
        'BOTANICAL20': { type: 'percentage', value: 20, label: '20% Seasonal Harvest Discount' },
    };
    if (typeof document !== 'undefined') {
        const el = document.getElementById('everbloom-discounts-data');
        if (el) {
            try {
                const data = JSON.parse(el.textContent);
                return { ...defaults, ...data };
            } catch (e) {}
        }
    }
    return defaults;
}

/**
 * Retrieve shipping configuration from #everbloom-shipping-data or defaults ($75 threshold, $6 cost).
 */
function getShippingConfig() {
    const defaults = {
        free_threshold: 75.00,
        standard_cost: 6.00,
    };
    if (typeof document !== 'undefined') {
        const el = document.getElementById('everbloom-shipping-data');
        if (el) {
            try {
                const data = JSON.parse(el.textContent);
                return {
                    free_threshold: parseFloat(data.free_threshold ?? defaults.free_threshold),
                    standard_cost: parseFloat(data.standard_cost ?? defaults.standard_cost),
                };
            } catch (e) {}
        }
    }
    return defaults;
}

/**
 * Dispatch an event to close any other open overlays so only one overlay is open at a time.
 */
function closeOtherOverlays(exceptOverlay) {
    if (typeof window !== 'undefined') {
        window.dispatchEvent(new CustomEvent('everbloom-overlay-opened', {
            detail: { overlay: exceptOverlay }
        }));
    }
}

/**
 * Creates a cart item storing only { productId, variantId, qty }.
 * Attaches non-enumerable getters to derive name, image, price, and compare-at price
 * from config/demo/products.php on every render.
 * No prices or extra metadata are ever serialized to JSON or stored.
 */
function createDerivedCartItem(itemData) {
    const raw = {
        productId: Number(itemData.productId) || itemData.productId,
        variantId: itemData.variantId !== undefined && itemData.variantId !== null ? String(itemData.variantId) : null,
        qty: Math.min(10, Math.max(1, parseInt(itemData.qty, 10) || 1))
    };

    Object.defineProperties(raw, {
        product: {
            get() {
                const catalog = getProductsCatalog();
                return catalog.find(p => p.id === this.productId || String(p.id) === String(this.productId)) || null;
            },
            enumerable: false
        },
        variant: {
            get() {
                const prod = this.product;
                if (!prod || !Array.isArray(prod.variants) || prod.variants.length === 0) return null;
                if (!this.variantId) return prod.variants[0] || null;
                return prod.variants.find(v => v.size === this.variantId || v.id === this.variantId || String(v.size) === String(this.variantId)) || null;
            },
            enumerable: false
        },
        name: {
            get() {
                return this.product ? this.product.name : '';
            },
            enumerable: false
        },
        slug: {
            get() {
                return this.product ? this.product.slug : '';
            },
            enumerable: false
        },
        category: {
            get() {
                return this.product ? this.product.category : '';
            },
            enumerable: false
        },
        size: {
            get() {
                return this.variantId || (this.variant ? this.variant.size : null);
            },
            enumerable: false
        },
        price: {
            get() {
                const v = this.variant;
                if (v && v.price !== undefined && v.price !== null) {
                    return parseFloat(v.price);
                }
                const p = this.product;
                return p && p.price !== undefined ? parseFloat(p.price) : 0;
            },
            enumerable: false
        },
        compare_at_price: {
            get() {
                const v = this.variant;
                if (v && v.compare_at_price !== undefined) {
                    return v.compare_at_price ? parseFloat(v.compare_at_price) : null;
                }
                const p = this.product;
                return p && p.compare_at_price ? parseFloat(p.compare_at_price) : null;
            },
            enumerable: false
        },
        image: {
            get() {
                const p = this.product;
                return (p && Array.isArray(p.images) && p.images.length > 0) ? p.images[0] : null;
            },
            enumerable: false
        },
        key: {
            get() {
                return this.variantId ? `${this.productId}-${this.variantId}` : `${this.productId}`;
            },
            enumerable: false
        },
        quantity: {
            get() {
                return this.qty;
            },
            set(val) {
                this.qty = Math.min(10, Math.max(1, parseInt(val, 10) || 1));
            },
            enumerable: false
        },
        lineTotal: {
            get() {
                return this.price * this.qty;
            },
            enumerable: false
        }
    });

    return raw;
}

/**
 * Creates a derived wishlist item storing only { id } in localStorage
 * and deriving product metadata from config/demo/products.php.
 */
function createDerivedWishlistItem(productId) {
    const raw = {
        id: Number(productId) || productId
    };

    Object.defineProperties(raw, {
        product: {
            get() {
                const catalog = getProductsCatalog();
                return catalog.find(p => p.id === this.id || String(p.id) === String(this.id)) || null;
            },
            enumerable: false
        },
        name: {
            get() {
                return this.product ? this.product.name : '';
            },
            enumerable: false
        },
        slug: {
            get() {
                return this.product ? this.product.slug : '';
            },
            enumerable: false
        },
        category: {
            get() {
                return this.product ? this.product.category : '';
            },
            enumerable: false
        },
        price: {
            get() {
                return this.product && this.product.price !== undefined ? parseFloat(this.product.price) : 0;
            },
            enumerable: false
        },
        image: {
            get() {
                const p = this.product;
                return (p && Array.isArray(p.images) && p.images.length > 0) ? p.images[0] : null;
            },
            enumerable: false
        }
    });

    return raw;
}

// Global Cart Store
Alpine.store('cart', {
    items: [],
    drawerOpen: false,
    discountCode: null,
    discountError: '',
    discountSuccess: '',

    init() {
        // Load saved promo code
        try {
            const savedDiscount = localStorage.getItem('everbloom_discount');
            if (savedDiscount) {
                const discounts = getDemoDiscounts();
                if (discounts[savedDiscount.toUpperCase()]) {
                    this.discountCode = savedDiscount.toUpperCase();
                }
            }
        } catch (e) {}

        this.cleanAndLoad();

        if (typeof window !== 'undefined') {
            window.addEventListener('everbloom-overlay-opened', (e) => {
                if (e.detail && e.detail.overlay !== 'cart' && this.drawerOpen) {
                    this.closeDrawer();
                }
            });
        }

        // Re-validate against catalog as soon as DOM is fully ready
        if (typeof document !== 'undefined' && document.readyState === 'loading') {
            document.addEventListener('DOMContentLoaded', () => {
                this.cleanAndValidate();
            });
        }
    },

    /**
     * Migration & load step:
     * - Drops any stored prices
     * - Clears price fields if found on old stored items
     * - Silently removes references to unknown products or variants
     * - Limits qty to 1-10
     * - Keeps only { productId, variantId, qty }
     */
    cleanAndLoad() {
        try {
            const saved = localStorage.getItem('everbloom_cart');
            if (!saved) {
                this.items = [];
                return;
            }

            let parsed = JSON.parse(saved);
            if (!Array.isArray(parsed)) {
                this.items = [];
                return;
            }

            // MIGRATION STEP: if old stored items contain price fields, clear them
            parsed.forEach(item => {
                if (item && typeof item === 'object') {
                    if ('price' in item) delete item.price;
                    if ('compare_at_price' in item) delete item.compare_at_price;
                    if ('line_total' in item) delete item.line_total;
                    if ('lineTotal' in item) delete item.lineTotal;
                }
            });

            const catalog = getProductsCatalog();
            const validItems = [];

            for (const rawItem of parsed) {
                if (!rawItem || typeof rawItem !== 'object') continue;

                const productId = rawItem.productId !== undefined ? rawItem.productId : rawItem.id;
                let variantId = rawItem.variantId !== undefined ? rawItem.variantId : (rawItem.size !== undefined ? rawItem.size : null);
                let qty = rawItem.qty !== undefined ? rawItem.qty : (rawItem.quantity !== undefined ? rawItem.quantity : 1);
                qty = parseInt(qty, 10) || 1;
                qty = Math.min(10, Math.max(1, qty));

                if (catalog.length > 0) {
                    const product = catalog.find(p => p.id === productId || String(p.id) === String(productId));
                    if (!product) {
                        continue;
                    }

                    if (Array.isArray(product.variants) && product.variants.length > 0) {
                        if (variantId) {
                            const matchedVariant = product.variants.find(v => v.size === variantId || v.id === variantId || String(v.size) === String(variantId));
                            if (!matchedVariant) {
                                continue;
                            }
                            variantId = matchedVariant.size || matchedVariant.id || variantId;
                        } else {
                            variantId = product.variants[0].size || null;
                        }
                    } else {
                        variantId = null;
                    }

                    validItems.push(createDerivedCartItem({
                        productId: product.id,
                        variantId: variantId,
                        qty: qty
                    }));
                } else {
                    validItems.push(createDerivedCartItem({
                        productId: productId,
                        variantId: variantId,
                        qty: qty
                    }));
                }
            }

            this.items = validItems;
            this.save();
        } catch (e) {
            console.warn('Unable to load cart from localStorage:', e);
            this.items = [];
        }
    },

    cleanAndValidate() {
        const catalog = getProductsCatalog();
        if (catalog.length === 0) return;

        const validated = [];
        let changed = false;

        for (const item of this.items) {
            const product = catalog.find(p => p.id === item.productId || String(p.id) === String(item.productId));
            if (!product) {
                changed = true;
                continue;
            }

            let variantId = item.variantId;
            if (Array.isArray(product.variants) && product.variants.length > 0) {
                if (variantId) {
                    const matchedVariant = product.variants.find(v => v.size === variantId || v.id === variantId || String(v.size) === String(variantId));
                    if (!matchedVariant) {
                        changed = true;
                        continue;
                    }
                    variantId = matchedVariant.size || matchedVariant.id || variantId;
                } else {
                    variantId = product.variants[0].size || null;
                    changed = true;
                }
            } else {
                if (variantId !== null) {
                    variantId = null;
                    changed = true;
                }
            }

            const qty = Math.min(10, Math.max(1, parseInt(item.qty, 10) || 1));
            if (qty !== item.qty) {
                changed = true;
            }

            validated.push(createDerivedCartItem({
                productId: product.id,
                variantId: variantId,
                qty: qty
            }));
        }

        if (changed || validated.length !== this.items.length) {
            this.items = validated;
            this.save();
        }
    },

    /**
     * Persist only { productId, variantId, qty } to localStorage.
     */
    save() {
        try {
            const cleanData = this.items.map(item => ({
                productId: item.productId,
                variantId: item.variantId,
                qty: Math.min(10, Math.max(1, parseInt(item.qty, 10) || 1))
            }));
            localStorage.setItem('everbloom_cart', JSON.stringify(cleanData));
        } catch (e) {
            console.warn('Unable to save cart to localStorage:', e);
        }
    },

    openDrawer() {
        closeOtherOverlays('cart');
        this.drawerOpen = true;
    },

    closeDrawer() {
        this.drawerOpen = false;
    },

    toggleDrawer() {
        if (this.drawerOpen) {
            this.closeDrawer();
        } else {
            this.openDrawer();
        }
    },

    /**
     * Add an item storing strictly { productId, variantId, qty }.
     */
    add(productOrId, quantity = 1, variantOrSize = null, open = true) {
        let productId = null;
        let variantId = variantOrSize;
        let qty = quantity;

        if (typeof productOrId === 'object' && productOrId !== null) {
            productId = productOrId.productId || productOrId.id;
            if (!variantId) {
                variantId = productOrId.variantId || (productOrId.sizes && productOrId.sizes.length > 0 ? productOrId.sizes[0] : null);
            }
            if (productOrId.qty) {
                qty = productOrId.qty;
            }
        } else {
            productId = productOrId;
        }

        productId = Number(productId) || productId;
        qty = Math.min(10, Math.max(1, parseInt(qty, 10) || 1));

        const catalog = getProductsCatalog();
        const product = catalog.find(p => p.id === productId || String(p.id) === String(productId));
        if (!product) {
            return;
        }

        if (Array.isArray(product.variants) && product.variants.length > 0) {
            if (variantId) {
                const matched = product.variants.find(v => v.size === variantId || v.id === variantId || String(v.size) === String(variantId));
                if (!matched) {
                    return;
                }
                variantId = matched.size || matched.id || variantId;
            } else {
                variantId = product.variants[0].size || null;
            }
        } else {
            variantId = null;
        }

        const cartKey = variantId ? `${productId}-${variantId}` : `${productId}`;
        const existing = this.items.find(item => item.key === cartKey);

        if (existing) {
            existing.qty = Math.min(10, existing.qty + qty);
        } else {
            this.items.push(createDerivedCartItem({
                productId: product.id,
                variantId: variantId,
                qty: qty
            }));
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
        if (isNaN(qty) || qty <= 0) {
            this.remove(key);
            return;
        }
        const item = this.items.find(i => i.key === key);
        if (item) {
            item.qty = Math.min(10, Math.max(1, qty));
            this.save();
        }
    },

    clear() {
        this.items = [];
        this.save();
    },

    get count() {
        return this.items.reduce((total, item) => total + (item.qty || 0), 0);
    },

    get subtotal() {
        return this.items.reduce((sum, item) => sum + (item.price * item.qty), 0);
    },

    get discount() {
        if (!this.discountCode) return null;
        const discounts = getDemoDiscounts();
        const code = this.discountCode.toUpperCase().trim();
        return discounts[code] || null;
    },

    get discountAmount() {
        const d = this.discount;
        if (!d || this.items.length === 0) return 0;
        if (d.type === 'percentage') {
            return this.subtotal * (parseFloat(d.value) / 100);
        }
        if (d.type === 'fixed') {
            return Math.min(this.subtotal, parseFloat(d.value) || 0);
        }
        return 0;
    },

    get shippingCost() {
        if (this.items.length === 0) return 0;
        const shippingConfig = getShippingConfig();
        const threshold = shippingConfig.free_threshold;
        const d = this.discount;
        if (this.subtotal >= threshold || (d && d.type === 'shipping')) {
            return 0;
        }
        return shippingConfig.standard_cost;
    },

    get total() {
        if (this.items.length === 0) return 0;
        return Math.max(0, this.subtotal - this.discountAmount + this.shippingCost);
    },

    applyDiscount(rawCode) {
        this.discountError = '';
        this.discountSuccess = '';
        if (!rawCode || !rawCode.trim()) {
            this.discountError = 'Please enter a promotional code.';
            return false;
        }
        const code = rawCode.trim().toUpperCase();
        const discounts = getDemoDiscounts();
        if (discounts[code]) {
            this.discountCode = code;
            this.discountSuccess = `Code ${code} applied (${discounts[code].label})`;
            try {
                localStorage.setItem('everbloom_discount', code);
            } catch (e) {}
            return true;
        } else {
            this.discountError = `The code "${code}" is invalid. Try WELCOME10`;
            return false;
        }
    },

    removeDiscount() {
        this.discountCode = null;
        this.discountError = '';
        this.discountSuccess = '';
        try {
            localStorage.removeItem('everbloom_discount');
        } catch (e) {}
    }
});

// Global Wishlist Store
Alpine.store('wishlist', {
    items: [],

    init() {
        this.cleanAndLoad();

        if (typeof document !== 'undefined' && document.readyState === 'loading') {
            document.addEventListener('DOMContentLoaded', () => {
                this.cleanAndValidate();
            });
        }
    },

    cleanAndLoad() {
        try {
            const saved = localStorage.getItem('everbloom_wishlist');
            if (!saved) {
                this.items = [];
                return;
            }
            const parsed = JSON.parse(saved);
            if (!Array.isArray(parsed)) {
                this.items = [];
                return;
            }
            const catalog = getProductsCatalog();
            const valid = [];
            const seen = new Set();

            for (const entry of parsed) {
                const rawId = (entry && typeof entry === 'object') ? entry.id : entry;
                if (rawId === undefined || rawId === null) continue;
                const key = String(rawId);
                if (seen.has(key)) continue;

                if (catalog.length > 0) {
                    const product = catalog.find(p => p.id === rawId || String(p.id) === key);
                    if (!product) continue;
                    seen.add(String(product.id));
                    valid.push(createDerivedWishlistItem(product.id));
                } else {
                    seen.add(key);
                    valid.push(createDerivedWishlistItem(rawId));
                }
            }
            this.items = valid;
            this.save();
        } catch (e) {
            console.warn('Unable to load wishlist from localStorage:', e);
            this.items = [];
        }
    },

    cleanAndValidate() {
        const catalog = getProductsCatalog();
        if (catalog.length === 0) return;

        const valid = [];
        const seen = new Set();
        let changed = false;

        for (const item of this.items) {
            const rawId = item && typeof item === 'object' ? item.id : item;
            const product = catalog.find(p => p.id === rawId || String(p.id) === String(rawId));
            if (!product || seen.has(String(product.id))) {
                changed = true;
                continue;
            }
            seen.add(String(product.id));
            valid.push(createDerivedWishlistItem(product.id));
        }

        if (changed || valid.length !== this.items.length) {
            this.items = valid;
            this.save();
        } else {
            this.items = valid;
        }
    },

    save() {
        try {
            const cleanIds = this.items.map(item => ({ id: item.id }));
            localStorage.setItem('everbloom_wishlist', JSON.stringify(cleanIds));
        } catch (e) {
            console.warn('Unable to save wishlist to localStorage:', e);
        }
    },

    has(id) {
        const targetId = String(id);
        return this.items.some(item => String(item.id || item) === targetId);
    },

    toggle(product) {
        const targetId = (typeof product === 'object' && product !== null) ? product.id : product;
        if (this.has(targetId)) {
            this.items = this.items.filter(item => String(item.id || item) !== String(targetId));
        } else {
            const catalog = getProductsCatalog();
            const matched = catalog.find(p => p.id === targetId || String(p.id) === String(targetId));
            if (matched) {
                this.items.push(createDerivedWishlistItem(matched.id));
            } else if (targetId !== undefined && targetId !== null) {
                this.items.push(createDerivedWishlistItem(targetId));
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
    init() {
        window.addEventListener('everbloom-overlay-opened', (e) => {
            if (e.detail && e.detail.overlay !== 'mobile-nav' && this.open) {
                this.close();
            }
        });
    },
    toggle() {
        if (this.open) {
            this.close();
        } else {
            closeOtherOverlays('mobile-nav');
            this.open = true;
        }
    },
    close() {
        this.open = false;
    }
}));

// Alpine Search Modal Component
Alpine.data('searchModal', () => ({
    open: false,
    query: '',
    products: [],

    init() {
        const el = document.getElementById('everbloom-search-data') || document.getElementById('everbloom-products-data');
        if (el) {
            try {
                this.products = JSON.parse(el.textContent);
            } catch (e) {
                console.warn('Failed to parse search products JSON:', e);
            }
        }
        window.addEventListener('everbloom-overlay-opened', (e) => {
            if (e.detail && e.detail.overlay !== 'search' && this.open) {
                this.closeModal();
            }
        });
    },

    openModal() {
        closeOtherOverlays('search');
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

window.__closeOtherEverbloomOverlays = closeOtherOverlays;
window.Alpine = Alpine;
Alpine.start();
