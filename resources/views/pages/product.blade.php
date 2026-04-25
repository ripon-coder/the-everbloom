<x-layouts.app title="Product Detail | E-Shop">
    <div class="p-4 bg-white min-h-screen" x-data="productDetails()">
        <div class="max-w-6xl mx-auto grid grid-cols-1 md:grid-cols-2 gap-6">
            <!-- Images -->
            <div class="w-full">
                <!-- Main Image -->
                <div class="relative w-full h-96 border rounded-lg overflow-hidden cursor-zoom-in"
                     @mousemove="handleMouseMove" 
                     @mouseenter="isZoomed = true" 
                     @mouseleave="isZoomed = false"
                     x-ref="imageContainer">
                    <img :src="mainImage" alt="Product Image" class="w-full h-full object-cover" />
                    
                    <!-- Zoom Lens -->
                    <template x-if="isZoomed">
                        <div style="position: absolute; pointer-events: none; width: 150px; height: 150px; border-radius: 50%; border: 2px solid #FFA500; z-index: 20; background-repeat: no-repeat;"
                             :style="zoomStyle">
                        </div>
                    </template>
                </div>

                <!-- Thumbnails -->
                <div class="flex gap-3 mt-4 flex-wrap">
                    <template x-for="(img, index) in images" :key="index">
                        <div class="relative w-20 h-20 border rounded-md overflow-hidden cursor-pointer hover:ring-2 hover:ring-orange-500"
                             :class="{'ring-2 ring-orange-500': mainImage === img}"
                             @click="mainImage = img">
                            <img :src="img" alt="Thumbnail" class="w-full h-full object-cover" />
                        </div>
                    </template>
                </div>
            </div>

            <!-- Details -->
            <div class="flex flex-col gap-4">
                <h1 class="text-2xl font-semibold">Premium Wireless Headphones</h1>
                <p class="text-gray-700">Experience crystal clear sound with active noise cancellation and 30-hour battery life.</p>
                <p class="text-sm text-gray-600"><span class="font-medium">SKU:</span> WH-1000XM4</p>

                <!-- Price -->
                <div class="text-3xl font-bold text-orange-600 flex items-center gap-3">
                    <span>৳ 4500</span>
                    <span class="text-gray-400 line-through text-xl">৳ 5500</span>
                </div>

                <!-- Attributes Example -->
                <div class="mt-4 space-y-3">
                    <div>
                        <p class="font-medium">Color:</p>
                        <div class="flex gap-2 flex-wrap mt-1">
                            <button class="px-3 py-1 border rounded-md bg-orange-500 text-white">Black</button>
                            <button class="px-3 py-1 border rounded-md bg-gray-100 text-gray-700 hover:bg-orange-100">Silver</button>
                        </div>
                    </div>
                </div>

                <!-- Stock & Delivery -->
                <div class="flex items-center gap-4 mt-2">
                    <p class="text-sm">
                        <span class="font-medium">Stock:</span>
                        <span class="text-green-600">12 available</span>
                    </p>
                    <div class="inline-flex items-center gap-1 px-2 py-1 rounded-full bg-green-100 text-green-800 text-xs font-semibold border border-green-200">
                        <svg class="w-3 h-3" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" d="M3 10h2l3 9h8l3-9h2"></path>
                            <path stroke-linecap="round" stroke-linejoin="round" d="M16 16v4m-8-4v4"></path>
                        </svg>
                        Free Delivery
                    </div>
                </div>

                <!-- Quantity -->
                <div class="flex items-center gap-3 mt-2">
                    <span class="text-sm font-medium">Quantity:</span>
                    <div class="flex items-center border rounded-md">
                        <button @click="if(quantity > 1) quantity--" class="px-3 py-1 hover:bg-gray-200">-</button>
                        <span class="px-4" x-text="quantity"></span>
                        <button @click="if(quantity < 12) quantity++" class="px-3 py-1 hover:bg-gray-200">+</button>
                    </div>
                </div>

                <!-- Buttons -->
                <div class="flex gap-4 mt-4">
                    <button @click="addToCart" class="flex-1 bg-orange-500 text-white hover:bg-orange-600 cursor-pointer py-2 rounded-md font-medium">
                        Add to Cart
                    </button>
                    <button class="flex-1 border border-orange-500 text-orange-500 hover:bg-orange-50 cursor-pointer py-2 rounded-md font-medium">
                        Buy Now
                    </button>
                </div>
            </div>
        </div>
    </div>

    <script>
        document.addEventListener('alpine:init', () => {
            Alpine.data('productDetails', () => ({
                quantity: 1,
                images: [
                    'https://images.unsplash.com/photo-1505740420928-5e560c06d30e?w=800&q=80',
                    'https://images.unsplash.com/photo-1546435770-a3e426bf472b?w=800&q=80',
                    'https://images.unsplash.com/photo-1484704849700-f032a568e944?w=800&q=80'
                ],
                mainImage: 'https://images.unsplash.com/photo-1505740420928-5e560c06d30e?w=800&q=80',
                isZoomed: false,
                lensPos: { x: 0, y: 0 },
                zoomLevel: 2,
                
                handleMouseMove(e) {
                    const rect = this.$refs.imageContainer.getBoundingClientRect();
                    this.lensPos.x = e.clientX - rect.left;
                    this.lensPos.y = e.clientY - rect.top;
                },
                
                get zoomStyle() {
                    if(!this.$refs.imageContainer) return '';
                    const width = this.$refs.imageContainer.offsetWidth * this.zoomLevel;
                    const height = this.$refs.imageContainer.offsetHeight * this.zoomLevel;
                    const bgPosX = -(this.lensPos.x * this.zoomLevel - 75);
                    const bgPosY = -(this.lensPos.y * this.zoomLevel - 75);
                    const top = this.lensPos.y - 75;
                    const left = this.lensPos.x - 75;
                    
                    return `
                        background-image: url('${this.mainImage}');
                        background-size: ${width}px ${height}px;
                        background-position: ${bgPosX}px ${bgPosY}px;
                        top: ${top}px;
                        left: ${left}px;
                    `;
                },
                
                addToCart() {
                    alert(`Added ${this.quantity} item(s) to cart!`);
                }
            }))
        })
    </script>
</x-layouts.app>
