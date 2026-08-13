<div class="space-y-0 md:space-y-3">
    <div class="bg-gray-50 overflow-hidden h-[320px] sm:h-[360px] md:h-[440px] relative cursor-crosshair md:border md:border-gray-100"
         @mousemove="handleMouseMove($event)"
         @mouseleave="resetZoom()">
        <img :src="mainImage" 
             :alt="product.name" 
             class="w-full h-full object-contain p-4 transition-transform duration-200"
             :style="zoomStyle" />
        
        @if($product->old_price > 0)
            <div class="absolute top-0 left-0 bg-accent text-white text-[10px] font-bold px-3 py-1.5 z-10">
                {{ round((($product->old_price - $product->price) / $product->old_price) * 100) }}% OFF
            </div>
        @endif
    </div>

    <!-- Thumbnails -->
    <div class="flex gap-0 md:gap-2 overflow-x-auto px-0 md:px-0 py-2 md:py-0 scrollbar-hide">
        <template x-for="(img, index) in allImages" :key="index">
            <button @click="mainImage = img" 
                    class="flex-shrink-0 w-16 h-16 md:w-[72px] md:h-[72px] border-2 transition-all overflow-hidden"
                    :class="mainImage === img ? 'border-primary opacity-100' : 'border-transparent opacity-60 hover:opacity-100'">
                <img :src="img" class="w-full h-full object-contain" />
            </button>
        </template>
    </div>
</div>
