<script>
    function productDetails(productData) {
        return {
            product: productData,
            quantity: 1,
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
                    const firstVariant = this.product.variants[0];
                    const attrs = firstVariant.variant_attributes || firstVariant.variantAttributes;
                    if (attrs) {
                        attrs.forEach(attr => {
                            this.selectAttribute(attr.attribute.name, attr.attribute_value_id);
                        });
                    }
                }
            },
            
            get currentPrice() {
                const variant = this.getActiveVariant();
                if (variant) {
                    return (variant.discount_price > 0) ? variant.discount_price : variant.sell_price;
                }
                return this.product.price;
            },

            get currentOldPrice() {
                const variant = this.getActiveVariant();
                if (variant) {
                    return (variant.discount_price > 0) ? variant.sell_price : null;
                }
                return this.product.old_price;
            },

            getActiveVariant() {
                if (!this.product.variants || Object.keys(this.selectedAttributes).length === 0) return null;
                
                return this.product.variants.find(variant => {
                    const attrs = variant.variant_attributes || variant.variantAttributes;
                    if (!attrs) return false;
                    
                    return attrs.every(attr => {
                        const attrName = attr.attribute.name;
                        return this.selectedAttributes[attrName] == attr.attribute_value_id;
                    });
                });
            },

            formatPrice(price) {
                return parseFloat(price).toLocaleString(undefined, { minimumFractionDigits: 2, maximumFractionDigits: 2 });
            },

            isOptionAvailable(attrName, optionId) {
                if (!this.product.variants) return true;
                return this.product.variants.some(variant => {
                    const attrs = variant.variant_attributes || variant.variantAttributes;
                    return attrs && attrs.some(a => a.attribute.name === attrName && a.attribute_value_id == optionId);
                });
            },

            isOptionCompatible(attrName, optionId) {
                if (!this.product.variants) return true;
                
                return this.product.variants.some(variant => {
                    const attrs = variant.variant_attributes || variant.variantAttributes;
                    if (!attrs) return false;
                    
                    return Object.entries(this.selectedAttributes).every(([name, id]) => {
                        if (name === attrName) return true; // Ignore self
                        return attrs.some(a => a.attribute.name === name && a.attribute_value_id == id);
                    }) && attrs.some(a => a.attribute.name === attrName && a.attribute_value_id == optionId);
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

                const variant = this.getActiveVariant();
                if (variant && variant.images && variant.images.length > 0) {
                    const img = variant.images[0];
                    if (img.image_url) {
                        this.mainImage = img.image_url;
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
                
                const cartItem = {
                    product_id: this.product.id,
                    name: this.product.name,
                    price: this.currentPrice,
                    image: this.mainImage,
                    quantity: this.quantity,
                    variant_id: variant ? variant.id : null,
                    attributes: attributeLabels
                };

                const existingItemIndex = cart.findIndex(item => 
                    item.product_id === cartItem.product_id && 
                    item.variant_id === cartItem.variant_id
                );

                if (existingItemIndex > -1) {
                    cart[existingItemIndex].quantity += cartItem.quantity;
                } else {
                    cart.push(cartItem);
                }

                localStorage.setItem('cart', JSON.stringify(cart));
                
                window.dispatchEvent(new CustomEvent('cart-updated'));
                
                // Optional UI feedback
                const btn = event.currentTarget;
                if(btn) {
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