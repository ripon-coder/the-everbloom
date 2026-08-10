<script>
    function productDetails(productData) {
        return {
            product: productData,
            quantity: 1,
            isInWishlist: false,
            mainImage: '{{ $product->firstImage ? $product->firstImage->getImageUrl() : asset("images/image1.jpg") }}',
            get allImages() {
                const productImages = [
                    '{{ $product->firstImage ? $product->firstImage->getImageUrl() : asset("images/image1.jpg") }}',
                    @foreach($product->images as $image)
                        '{{ $image->getImageUrl() }}',
                    @endforeach
                ];

                const variant = this.getActiveVariant();
                if (variant && variant.images && variant.images.length > 0) {
                    const variantImages = variant.images.map(img => img.image_url).filter(Boolean);
                    return [...variantImages, ...productImages].filter((v, i, a) => a.indexOf(v) === i);
                }

                return productImages.filter((v, i, a) => a.indexOf(v) === i);
            },
            selectedAttributes: {},
            zoomStyle: 'transform: scale(1)',

            init() {
                if (this.product.variants && this.product.variants.length > 0) {
                    const activeVariants = this.product.variants.filter(v => !v.status || v.status === 'active');
                    const firstVariant = activeVariants.find(v => {
                        const attrs = v.variant_attributes || v.variantAttributes;
                        return attrs && attrs.length > 0;
                    }) || activeVariants[0] || this.product.variants[0];

                    if (firstVariant) {
                        const attrs = firstVariant.variant_attributes || firstVariant.variantAttributes;
                        if (attrs) {
                            attrs.forEach(attr => {
                                const attrObj = attr.attribute;
                                if (attrObj && attrObj.name) {
                                    this.selectAttribute(attrObj.name, attr.attribute_value_id);
                                }
                            });
                        }
                    }
                }

                this.checkWishlist();
                window.addEventListener('wishlist-updated', () => {
                    this.checkWishlist();
                });
            },

            checkWishlist() {
                fetch('{{ route('wishlist.ids') }}')
                    .then(res => res.json())
                    .then(data => {
                        this.isInWishlist = (data.wishlist_ids || []).includes(this.product.id);
                    })
                    .catch(err => console.error('Wishlist check error:', err));
            },

            toggleWishlist() {
                fetch('{{ route('wishlist.toggle') }}', {
                    method: 'POST',
                    headers: {
                        'Content-Type': 'application/json',
                        'Accept': 'application/json',
                        'X-CSRF-TOKEN': '{{ csrf_token() }}'
                    },
                    body: JSON.stringify({
                        product_id: this.product.id
                    })
                })
                .then(res => {
                    if (res.status === 401) {
                        window.dispatchEvent(new CustomEvent('notify', {
                            detail: {
                                message: 'Please login first to manage your wishlist.',
                                type: 'error'
                            }
                        }));
                        return;
                    }
                    return res.json();
                })
                .then(data => {
                    if (data && data.success) {
                        window.dispatchEvent(new CustomEvent('wishlist-updated'));
                        window.dispatchEvent(new CustomEvent('notify', {
                            detail: {
                                message: data.message,
                                type: data.in_wishlist ? 'success' : 'info'
                            }
                        }));
                    }
                })
                .catch(err => console.error('Wishlist toggle error:', err));
            },

            get currentPrice() {
                const variant = this.getActiveVariant();
                // Base price should consider if variant already has a normal discount
                let basePriceForCalculation = variant ? (parseFloat(variant.discount_price) > 0 ? parseFloat(variant.discount_price) : parseFloat(variant.sell_price)) : parseFloat(this.product.price);
                let priceToUse = basePriceForCalculation;
                
                if (this.product.flash_sales && this.product.flash_sales.length > 0) {
                    const pivot = this.product.flash_sales[0].pivot;
                    if (pivot) {
                        let discountAmount = parseFloat(pivot.discount_price || 0);
                        let discountPercentage = parseFloat(pivot.discount_percentage || 0);
                        
                        let computedPercentage = 0;
                        if (discountPercentage > 0) {
                            computedPercentage = discountPercentage;
                        } else if (discountAmount > 0 && parseFloat(this.product.price) > 0) {
                            // Convert the flat discount amount into a percentage based on the base product price
                            // This ensures that more expensive variants get a proportional flash sale discount
                            computedPercentage = (discountAmount / parseFloat(this.product.price)) * 100;
                        }
                        
                        if (computedPercentage > 0) {
                            return Math.max(0, basePriceForCalculation - (basePriceForCalculation * (computedPercentage / 100)));
                        }
                    }
                }
                return priceToUse;
            },

            get currentOldPrice() {
                const variant = this.getActiveVariant();
                let basePrice = variant ? parseFloat(variant.sell_price) : parseFloat(this.product.price);
                
                if (this.product.flash_sales && this.product.flash_sales.length > 0) {
                    return basePrice;
                }

                if (variant) {
                    return (parseFloat(variant.discount_price) > 0) ? parseFloat(variant.sell_price) : null;
                }
                return parseFloat(this.product.old_price) > 0 ? parseFloat(this.product.old_price) : null;
            },

            getActiveVariant() {
                if (!this.product.variants || Object.keys(this.selectedAttributes).length === 0) return null;

                const activeVariants = this.product.variants.filter(v => !v.status || v.status === 'active');

                return activeVariants.find(variant => {
                    const attrs = variant.variant_attributes || variant.variantAttributes;
                    if (!attrs || attrs.length === 0) return false;

                    const allAttrsMatch = attrs.every(attr => {
                        if (!attr.attribute) return false;
                        const attrName = attr.attribute.name;
                        return this.selectedAttributes[attrName] == attr.attribute_value_id;
                    });

                    const selectedCount = Object.keys(this.selectedAttributes).length;
                    return allAttrsMatch && (attrs.length === selectedCount);
                });
            },

            get currentStock() {
                const variant = this.getActiveVariant();
                if (variant) {
                    return parseInt(variant.stock || 0);
                }
                return parseInt(this.product.stock || 0);
            },

            get currentSku() {
                const variant = this.getActiveVariant();
                if (variant && variant.sku) {
                    return variant.sku;
                }
                if (this.product.first_active_variant && this.product.first_active_variant.sku) {
                    return this.product.first_active_variant.sku;
                }
                if (this.product.variants && this.product.variants.length > 0) {
                    const activeV = this.product.variants.find(v => (v.status === 'active' || !v.status) && v.sku);
                    if (activeV) return activeV.sku;
                }
                return this.product.sku || 'N/A';
            },

            formatPrice(price) {
                return parseFloat(price).toLocaleString(undefined, { minimumFractionDigits: 2, maximumFractionDigits: 2 });
            },

            isOptionAvailable(attrName, optionId) {
                if (!this.product.variants) return true;
                return this.product.variants.some(variant => {
                    if (variant.status && variant.status !== 'active') return false;
                    const attrs = variant.variant_attributes || variant.variantAttributes;
                    return attrs && attrs.some(a => a.attribute && a.attribute.name === attrName && a.attribute_value_id == optionId);
                });
            },

            isOptionCompatible(attrName, optionId) {
                if (!this.product.variants) return true;

                return this.product.variants.some(variant => {
                    if (variant.status && variant.status !== 'active') return false;
                    const attrs = variant.variant_attributes || variant.variantAttributes;
                    if (!attrs) return false;

                    return Object.entries(this.selectedAttributes).every(([name, id]) => {
                        if (name === attrName) return true; // Ignore self
                        return attrs.some(a => a.attribute && a.attribute.name === name && a.attribute_value_id == id);
                    }) && attrs.some(a => a.attribute && a.attribute.name === attrName && a.attribute_value_id == optionId);
                });
            },

            selectAttribute(name, id) {
                this.selectedAttributes[name] = id;

                Object.keys(this.selectedAttributes).forEach(key => {
                    if (key === name) return;

                    const currentVal = this.selectedAttributes[key];
                    if (!this.isOptionCompatible(key, currentVal)) {
                        delete this.selectedAttributes[key];
                    }
                });

                if (this.product.variants) {
                    const activeVariants = this.product.variants.filter(v => !v.status || v.status === 'active');
                    const matchingVariant = activeVariants.find(v => {
                        const attrs = v.variant_attributes || v.variantAttributes;
                        if (!attrs) return false;
                        return Object.entries(this.selectedAttributes).every(([attrName, valId]) => {
                            return attrs.some(a => a.attribute && a.attribute.name === attrName && a.attribute_value_id == valId);
                        });
                    });

                    if (matchingVariant) {
                        const attrs = matchingVariant.variant_attributes || matchingVariant.variantAttributes;
                        if (attrs) {
                            attrs.forEach(attr => {
                                if (attr.attribute && attr.attribute.name && !this.selectedAttributes[attr.attribute.name]) {
                                    this.selectedAttributes[attr.attribute.name] = attr.attribute_value_id;
                                }
                            });
                        }
                    }
                }

                const variant = this.getActiveVariant();
                if (variant && variant.images && variant.images.length > 0) {
                    const img = variant.images[0];
                    const imgUrl = img.image_url || img.url || (img.media && img.media.length > 0 ? img.media[0].original_url : null);
                    if (imgUrl) {
                        this.mainImage = imgUrl;
                    }
                }
            },

            addToCart(event) {
                if (this.product.variants && this.product.variants.length > 0) {
                    const variant = this.getActiveVariant();
                    if (!variant) {
                        window.dispatchEvent(new CustomEvent('notify', { detail: { message: 'Please select all options before adding to cart.', type: 'error' } }));
                        return;
                    }
                }

                let cart = JSON.parse(localStorage.getItem('cart')) || [];

                let currentTotalQty = cart.reduce((total, item) => total + item.quantity, 0);
                if (currentTotalQty + this.quantity > 30) {
                    window.dispatchEvent(new CustomEvent('notify', { detail: { message: 'You cannot add more than 30 products to your cart.', type: 'error' } }));
                    return;
                }

                const variant = this.getActiveVariant();

                let attributeLabels = {};
                if (variant) {
                    const attrs = variant.variant_attributes || variant.variantAttributes;
                    if (attrs) {
                        attrs.forEach(attr => {
                            const valObj = attr.attribute_value || attr.attributeValue;
                            if (valObj) {
                                attributeLabels[attr.attribute.name] = valObj.value;
                            }
                        });
                    }
                }

                const unitBasePrice = variant ? parseFloat(variant.sell_price || 0) : parseFloat(this.product.price || 0);
                const unitFinalPrice = parseFloat(this.currentPrice || 0);

                const isFlashSale = this.product.flash_sales && this.product.flash_sales.length > 0;

                const cartItem = {
                    variant_id: variant ? variant.id : null,
                    product_id: this.product.id,
                    name: this.product.name,
                    attributes: attributeLabels,
                    image: this.mainImage,
                    unit_base_price: unitBasePrice,
                    unit_final_price: unitFinalPrice,
                    quantity: this.quantity,
                    line_total: unitFinalPrice * this.quantity,
                    meta: {
                        is_flash_sale: isFlashSale,
                        discount_applied: unitFinalPrice < unitBasePrice,
                        free_delivery: this.product.is_free_delivery ?? false,
                    }
                };

                const existingItemIndex = cart.findIndex(item =>
                    item.product_id === cartItem.product_id &&
                    item.variant_id === cartItem.variant_id
                );

                if (existingItemIndex > -1) {
                    cart[existingItemIndex].quantity += cartItem.quantity;
                    cart[existingItemIndex].line_total = cart[existingItemIndex].quantity * cart[existingItemIndex].unit_final_price;
                } else {
                    cart.push(cartItem);
                }

                localStorage.setItem('cart', JSON.stringify(cart));

                fetch('/cart/sync', {
                    method: 'POST',
                    headers: {
                        'Content-Type': 'application/json',
                        'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content')
                    },
                    body: JSON.stringify({ cart: cart })
                }).catch(err => console.error('Cart sync error:', err));

                window.dispatchEvent(new CustomEvent('cart-updated'));

                // Optional UI feedback
                const btn = event.currentTarget;
                if (btn) {
                    const originalText = btn.innerHTML;
                    btn.innerHTML = `<svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"/></svg> Added!`;
                    btn.classList.add('bg-green-600', 'hover:bg-green-700');
                    btn.classList.remove('bg-red-600', 'hover:bg-red-700');

                    setTimeout(() => {
                        btn.innerHTML = originalText;
                        btn.classList.remove('bg-green-600', 'hover:bg-green-700');
                        btn.classList.add('bg-red-600', 'hover:bg-red-700');
                    }, 2000);
                }
            },

            handleMouseMove(e) {
                const { left, top, width, height } = e.currentTarget.getBoundingClientRect();
                const x = ((e.clientX - left) / width) * 100;
                const y = ((e.clientY - top) / height) * 100;
                this.zoomStyle = `transform: scale(2); transform-origin: ${x}% ${y}%`;
            },

            resetZoom() {
                this.zoomStyle = 'transform: scale(1)';
            }
        }
    }
</script>
