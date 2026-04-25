<x-layouts.app :title="$product->name . ' | Everbloom'">
    <div class="bg-white pb-12" x-data="productDetails({{ $product->toJson() }})">
        <!-- Breadcrumbs -->
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-4 border-b border-gray-100">
            <nav class="flex text-xs font-medium text-gray-500 uppercase tracking-wider">
                <a href="{{ route('home') }}" class="hover:text-red-600">Home</a>
                <span class="mx-2">/</span>
                <a href="#" class="hover:text-red-600">Products</a>
                <span class="mx-2">/</span>
                <span class="text-gray-900 truncate">{{ $product->name }}</span>
            </nav>
        </div>

        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 mt-4 md:mt-8">
            <div class="grid grid-cols-1 lg:grid-cols-2 gap-6 md:gap-12">
                <div class="space-y-3 md:space-y-4">
                    <div class="border border-gray-200 rounded-md overflow-hidden bg-white h-[280px] sm:h-[350px] md:h-[400px] relative cursor-crosshair group"
                         @mousemove="handleMouseMove($event)"
                         @mouseleave="resetZoom()">
                        <img :src="mainImage" 
                             :alt="product.name" 
                             class="w-full h-full object-contain p-4 transition-transform duration-200"
                             :style="zoomStyle" />
                        
                        @if($product->old_price)
                            <div class="absolute top-3 left-3 bg-red-600 text-white text-[9px] font-bold px-2 py-1 rounded z-10">
                                {{ round((($product->old_price - $product->price) / $product->old_price) * 100) }}% OFF
                            </div>
                        @endif
                    </div>

                    <!-- Thumbnails -->
                    <div class="flex flex-wrap gap-2 justify-center pb-1 scrollbar-hide">
                        <template x-for="(img, index) in allImages" :key="index">
                            <button @click="mainImage = img" 
                                    class="flex-shrink-0 w-16 h-16 md:w-20 md:h-20 border rounded transition-all overflow-hidden"
                                    :class="mainImage === img ? 'border-red-600 ring-1 ring-red-600' : 'border-gray-200 hover:border-gray-400'">
                                <img :src="img" class="w-full h-full object-contain p-1" />
                            </button>
                        </template>
                    </div>
                </div>

                <!-- Right: Product Info -->
                <div class="flex flex-col">
                    <div class="mb-2 md:mb-4">
                        <h1 class="text-xl md:text-3xl font-bold text-gray-900 mb-1 md:mb-2">{{ $product->name }}</h1>
                        <div class="flex items-center gap-3 text-xs md:text-sm">
                            <div class="flex text-yellow-400">
                                @for($i=0; $i<5; $i++)
                                    <svg class="w-3 h-3 md:w-4 md:h-4 fill-current" viewBox="0 0 20 20"><path d="M9.049 2.927c.3-.921 1.603-.921 1.902 0l1.07 3.292a1 1 0 00.95.69h3.462c.969 0 1.371 1.24.588 1.81l-2.8 2.034a1 1 0 00-.364 1.118l1.07 3.292c.3.921-.755 1.688-1.54 1.118l-2.8-2.034a1 1 0 00-1.175 0l-2.8 2.034c-.784.57-1.838-.197-1.539-1.118l1.07-3.292a1 1 0 00-.364-1.118L2.98 8.72c-.783-.57-.38-1.81.588-1.81h3.461a1 1 0 00.951-.69l1.07-3.292z"></path></svg>
                                @endfor
                            </div>
                            <span class="text-gray-500">(128)</span>
                            <span class="text-green-600 font-bold">In Stock</span>
                        </div>
                    </div>

                    <div class="flex flex-col gap-1 py-3 md:py-4 border-y border-gray-100 mb-3 md:mb-4">
                        <div class="flex items-baseline gap-3 md:gap-4">
                            @if($product->old_price)
                                <span class="text-base md:text-lg text-gray-400 line-through font-medium">৳ {{ number_format($product->old_price, 2) }}</span>
                            @endif
                            <span class="text-2xl md:text-3xl font-bold text-red-600">৳ {{ number_format($product->price, 2) }}</span>
                        </div>
                        @if($product->old_price)
                            <div class="text-[10px] md:text-xs font-bold text-green-600">
                                You Save: ৳ {{ number_format($product->old_price - $product->price, 2) }} 
                                ({{ round((($product->old_price - $product->price) / $product->old_price) * 100) }}% Off)
                            </div>
                        @endif
                    </div>

                    <div class="text-sm text-gray-600 mb-4 leading-relaxed line-clamp-2 md:line-clamp-3">
                        {!! $product->description ?? 'No description available.' !!}
                    </div>

                    <div class="space-y-4">
                        <!-- Attributes Selection -->
                        @php
                            $groupedAttributes = [];
                            if($product->variants) {
                                foreach($product->variants as $variant) {
                                    foreach($variant->variantAttributes as $va) {
                                        $attrName = $va->attribute->name;
                                        $attrVal = $va->attributeValue->value;
                                        $groupedAttributes[$attrName][$va->attribute_value_id] = $attrVal;
                                    }
                                }
                            }
                        @endphp

                        @foreach($groupedAttributes as $name => $values)
                            <div>
                                <h3 class="text-[10px] md:text-xs font-bold text-gray-900 uppercase mb-2">Select {{ $name }}</h3>
                                <div class="flex flex-wrap gap-2">
                                    @foreach($values as $id => $val)
                                        <button @click="selectedAttributes['{{ $name }}'] = {{ $id }}"
                                                class="px-3 py-1.5 rounded border text-xs md:text-sm font-medium transition-all"
                                                :class="selectedAttributes['{{ $name }}'] === {{ $id }} ? 'border-red-600 bg-red-50 text-red-600' : 'border-gray-200 text-gray-600 hover:border-gray-400'">
                                            {{ $val }}
                                        </button>
                                    @endforeach
                                </div>
                            </div>
                        @endforeach

                        <!-- Quantity -->
                        <div class="flex items-center gap-4 pt-1">
                            <span class="text-[10px] md:text-xs font-bold text-gray-900 uppercase">Qty</span>
                            <div class="flex items-center border border-gray-300 rounded overflow-hidden h-9 md:h-10">
                                <button @click="if(quantity > 1) quantity--" class="px-3 py-1 hover:bg-gray-100 border-r border-gray-300">-</button>
                                <input type="number" x-model="quantity" class="w-10 text-center border-none focus:ring-0 font-medium text-sm p-0" readonly>
                                <button @click="quantity++" class="px-3 py-1 hover:bg-gray-100 border-l border-gray-300">+</button>
                            </div>
                        </div>

                        <!-- Action Buttons (Desktop Only) -->
                        <div class="hidden md:flex flex-col sm:flex-row gap-2 pt-2">
                            <button class="flex-1 bg-red-600 hover:bg-red-700 text-white font-bold py-3 px-4 rounded text-xs uppercase transition-colors">
                                Add to Cart
                            </button>
                            <button class="flex-1 bg-gray-900 hover:bg-black text-white font-bold py-3 px-4 rounded text-xs uppercase transition-colors">
                                Buy It Now
                            </button>
                        </div>
                    </div>

                    <!-- Meta -->
                    <div class="mt-4 md:mt-10 pt-4 md:pt-6 border-t border-gray-100 space-y-1 md:space-y-2 pb-6 md:pb-0">
                        <p class="text-[10px] md:text-xs text-gray-500"><span class="font-bold text-gray-900 uppercase w-24 inline-block">SKU:</span> {{ $product->sku ?? 'N/A' }}</p>
                        <p class="text-[10px] md:text-xs text-gray-500"><span class="font-bold text-gray-900 uppercase w-24 inline-block">Category:</span> {{ $product->category->name ?? 'Uncategorized' }}</p>
                    </div>
                </div>
            </div>

            <!-- Tabs -->
            <div class="mt-4 md:mt-16 border-t border-gray-200 pt-4 md:pt-12" x-data="{ activeTab: 'description' }">
                <div class="flex justify-center gap-6 md:gap-8 border-b border-gray-100 mb-4 md:mb-8">
                    <button @click="activeTab = 'description'" 
                            class="pb-3 md:pb-4 text-[10px] md:text-xs font-bold uppercase tracking-widest border-b-2"
                            :class="activeTab === 'description' ? 'border-red-600 text-gray-900' : 'border-transparent text-gray-400'">
                        Description
                    </button>
                    <button @click="activeTab = 'specifications'" 
                            class="pb-4 text-xs font-bold uppercase tracking-widest border-b-2"
                            :class="activeTab === 'specifications' ? 'border-red-600 text-gray-900' : 'border-transparent text-gray-400'">
                        Specifications
                    </button>
                </div>
                <div class="max-w-4xl mx-auto">
                    <div x-show="activeTab === 'description'" class="prose prose-sm max-w-none text-gray-600">
                        {!! $product->description !!}
                    </div>
                    <div x-show="activeTab === 'specifications'" x-cloak>
                        <table class="w-full text-xs">
                            <tbody class="divide-y divide-gray-100">
                                <tr><td class="py-3 font-bold w-1/3">SKU</td><td class="py-3">{{ $product->sku }}</td></tr>
                                <tr><td class="py-3 font-bold">Stock</td><td class="py-3 text-green-600 font-bold">In Stock</td></tr>
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>

            <!-- Reviews Section -->
            <div class="mt-4 md:mt-16 border-t border-gray-100 pt-4 md:pt-12">
                <div class="flex flex-col md:flex-row justify-between items-start gap-4 md:gap-12">
                    <!-- Left: Review Stats & List -->
                    <div class="flex-1 w-full">
                        <h2 class="text-xl font-bold text-gray-900 mb-8 uppercase tracking-widest">Customer Reviews</h2>
                        
                        <div class="space-y-8">
                            <!-- Sample Review 1 -->
                            <div class="border-b border-gray-100 pb-6">
                                <div class="flex items-center gap-4 mb-2">
                                    <div class="flex text-yellow-400">
                                        @for($i=0; $i<5; $i++)
                                            <svg class="w-3 h-3 fill-current" viewBox="0 0 20 20"><path d="M9.049 2.927c.3-.921 1.603-.921 1.902 0l1.07 3.292a1 1 0 00.95.69h3.462c.969 0 1.371 1.24.588 1.81l-2.8 2.034a1 1 0 00-.364 1.118l1.07 3.292c.3.921-.755 1.688-1.54 1.118l-2.8-2.034a1 1 0 00-1.175 0l-2.8 2.034c-.784.57-1.838-.197-1.539-1.118l1.07-3.292a1 1 0 00-.364-1.118L2.98 8.72c-.783-.57-.38-1.81.588-1.81h3.461a1 1 0 00.951-.69l1.07-3.292z"></path></svg>
                                        @endfor
                                    </div>
                                    <span class="text-xs font-bold text-gray-900">Rahat Ahmed</span>
                                    <span class="text-xs text-gray-400">2 days ago</span>
                                </div>
                                <p class="text-sm text-gray-600 leading-relaxed">The build quality is exceptional. Exactly as described and the delivery was very fast. Highly recommended!</p>
                            </div>

                            <!-- Sample Review 2 -->
                            <div class="border-b border-gray-100 pb-6">
                                <div class="flex items-center gap-4 mb-2">
                                    <div class="flex text-yellow-400">
                                        @for($i=0; $i<4; $i++)
                                            <svg class="w-3 h-3 fill-current" viewBox="0 0 20 20"><path d="M9.049 2.927c.3-.921 1.603-.921 1.902 0l1.07 3.292a1 1 0 00.95.69h3.462c.969 0 1.371 1.24.588 1.81l-2.8 2.034a1 1 0 00-.364 1.118l1.07 3.292c.3.921-.755 1.688-1.54 1.118l-2.8-2.034a1 1 0 00-1.175 0l-2.8 2.034c-.784.57-1.838-.197-1.539-1.118l1.07-3.292a1 1 0 00-.364-1.118L2.98 8.72c-.783-.57-.38-1.81.588-1.81h3.461a1 1 0 00.951-.69l1.07-3.292z"></path></svg>
                                        @endfor
                                        <svg class="w-3 h-3 text-gray-300 fill-current" viewBox="0 0 20 20"><path d="M9.049 2.927c.3-.921 1.603-.921 1.902 0l1.07 3.292a1 1 0 00.95.69h3.462c.969 0 1.371 1.24.588 1.81l-2.8 2.034a1 1 0 00-.364 1.118l1.07 3.292c.3.921-.755 1.688-1.54 1.118l-2.8-2.034a1 1 0 00-1.175 0l-2.8 2.034c-.784.57-1.838-.197-1.539-1.118l1.07-3.292a1 1 0 00-.364-1.118L2.98 8.72c-.783-.57-.38-1.81.588-1.81h3.461a1 1 0 00.951-.69l1.07-3.292z"></path></svg>
                                    </div>
                                    <span class="text-xs font-bold text-gray-900">Tanvir H.</span>
                                    <span class="text-xs text-gray-400">1 week ago</span>
                                </div>
                                <p class="text-sm text-gray-600 leading-relaxed">Great product, but the packaging could be better. Overall satisfied with the purchase.</p>
                            </div>
                        </div>
                    </div>

                    <!-- Right: Add Review Form -->
                    <div class="w-full md:w-[400px] bg-gray-50 p-6 md:p-8 rounded-md border border-gray-100">
                        <h3 class="text-sm font-bold text-gray-900 uppercase tracking-widest mb-6">Write a Review</h3>
                        <form class="space-y-4">
                            <div>
                                <label class="text-[10px] font-bold text-gray-400 uppercase block mb-1">Your Rating</label>
                                <div class="flex gap-1 text-gray-300">
                                    @for($i=1; $i<=5; $i++)
                                        <button type="button" class="hover:text-yellow-400 transition-colors">
                                            <svg class="w-5 h-5 fill-current" viewBox="0 0 20 20"><path d="M9.049 2.927c.3-.921 1.603-.921 1.902 0l1.07 3.292a1 1 0 00.95.69h3.462c.969 0 1.371 1.24.588 1.81l-2.8 2.034a1 1 0 00-.364 1.118l1.07 3.292c.3.921-.755 1.688-1.54 1.118l-2.8-2.034a1 1 0 00-1.175 0l-2.8 2.034c-.784.57-1.838-.197-1.539-1.118l1.07-3.292a1 1 0 00-.364-1.118L2.98 8.72c-.783-.57-.38-1.81.588-1.81h3.461a1 1 0 00.951-.69l1.07-3.292z"></path></svg>
                                        </button>
                                    @endfor
                                </div>
                            </div>
                            <div>
                                <label class="text-[10px] font-bold text-gray-400 uppercase block mb-1">Your Name</label>
                                <input type="text" class="w-full border-gray-200 rounded text-sm focus:border-red-600 focus:ring-0" placeholder="Enter your name">
                            </div>
                            <div>
                                <label class="text-[10px] font-bold text-gray-400 uppercase block mb-1">Your Review</label>
                                <textarea rows="4" class="w-full border-gray-200 rounded text-sm focus:border-red-600 focus:ring-0" placeholder="What did you think?"></textarea>
                            </div>
                            <button type="submit" class="w-full bg-gray-900 hover:bg-black text-white font-bold py-3 rounded text-xs uppercase transition-colors">
                                Submit Review
                            </button>
                        </form>
                    </div>
                </div>
            </div>

            <!-- Similar Products -->
            @if(isset($relatedProducts) && $relatedProducts->count() > 0)
                <div class="mt-4 md:mt-8 pt-4 md:pt-8 border-t border-gray-100 pb-16">
                    <div class="flex items-center justify-between mb-8">
                        <h2 class="text-xl font-bold text-gray-900 uppercase tracking-widest">Similar Products</h2>
                        <a href="#" class="text-xs font-bold text-red-600 hover:underline uppercase tracking-wider">View All</a>
                    </div>
                    <div class="grid grid-cols-2 sm:grid-cols-3 md:grid-cols-4 lg:grid-cols-6 gap-4 md:gap-6">
                        @foreach($relatedProducts as $relProduct)
                            <x-product-card :product="$relProduct" />
                        @endforeach
                    </div>
                </div>
            @endif
        </div>

        <!-- Fixed Bottom Mobile Bar -->
        <div class="fixed bottom-[57px] left-0 right-0 bg-white border-t border-gray-200 p-3 flex gap-2 z-50 md:hidden shadow-[0_-4px_12px_rgba(0,0,0,0.05)]">
            <button class="flex-1 bg-red-600 hover:bg-red-700 text-white font-bold py-3 px-4 rounded text-xs uppercase transition-colors">
                Add to Cart
            </button>
            <button class="flex-1 bg-gray-900 hover:bg-black text-white font-bold py-3 px-4 rounded text-xs uppercase transition-colors">
                Buy Now
            </button>
        </div>
    </div>

    <script>
        function productDetails(productData) {
            return {
                product: productData,
                quantity: 1,
                mainImage: '{{ $product->firstImage ? $product->firstImage->getImageUrl() : asset("images/image1.jpg") }}',
                allImages: [
                    '{{ $product->firstImage ? $product->firstImage->getImageUrl() : asset("images/image1.jpg") }}',
                    @foreach($product->images as $image)
                        '{{ $image->getImageUrl() }}',
                    @endforeach
                ].filter((v, i, a) => a.indexOf(v) === i),
                selectedAttributes: {},
                zoomStyle: 'transform: scale(1)',
                
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
</x-layouts.app>

