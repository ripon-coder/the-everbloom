<div class="space-y-3 md:space-y-4">
    <div class="border border-gray-100 rounded-md overflow-hidden bg-gray-50/50 h-[280px] sm:h-[340px] md:h-[420px] relative cursor-crosshair group"
         @mousemove="handleMouseMove($event)"
         @mouseleave="resetZoom()">
        <img :src="mainImage" 
             :alt="product.name" 
             class="w-full h-full object-contain p-4 transition-transform duration-200"
             :style="zoomStyle" />
        
        @if($product->old_price > 0)
            <div class="absolute top-3 left-3 bg-accent text-white text-[9px] font-bold px-2 py-1 rounded-md z-10 shadow-sm">
                {{ round((($product->old_price - $product->price) / $product->old_price) * 100) }}% OFF
            </div>
        @endif
    </div>

    <!-- Thumbnails -->
    <div class="flex flex-wrap gap-2 justify-center pb-1 scrollbar-hide">
        <template x-for="(img, index) in allImages" :key="index">
            <button @click="mainImage = img" 
                    class="flex-shrink-0 w-16 h-16 md:w-20 md:h-20 border rounded-md transition-all overflow-hidden"
                    :class="mainImage === img ? 'border-primary ring-1 ring-primary' : 'border-gray-200 hover:border-gray-400'">
                <img :src="img" class="w-full h-full object-contain p-1" />
            </button>
        </template>
    </div>
</div>
