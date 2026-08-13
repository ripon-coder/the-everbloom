<section class="max-w-[1400px] mx-auto px-1.5 sm:px-6 lg:px-8 py-4 sm:py-8">
    <div class="flex flex-col md:flex-row gap-0 rounded-none overflow-hidden shadow-lg border border-gray-100">
        <!-- Left Banner -->
        <div class="bg-primary p-8 flex flex-col justify-center items-center text-center w-full md:w-1/4 relative overflow-hidden">
            <div class="relative z-10">
                <h2 class="text-3xl font-black text-white uppercase leading-tight mb-4">
                    This Week's<br/>Must-Have
                </h2>
                <p class="text-primary-100 text-sm mb-6">
                    Trending Products<br/>Carefully Chosen for You
                </p>
                <button class="bg-slate-900 text-white px-6 py-2 rounded-none text-xs font-bold uppercase tracking-wider hover:bg-black transition-colors">
                    View More ->
                </button>
            </div>
            <!-- Decorative circle -->
            <div class="absolute -bottom-10 -right-10 w-32 h-32 bg-primary-500 rounded-none blur-xl"></div>
            <div class="absolute -top-10 -left-10 w-32 h-32 bg-primary-700 rounded-none blur-xl"></div>
        </div>

        <!-- Right Carousel -->
        <div class="bg-white w-full md:w-3/4 p-6 relative flex items-center">
            <div class="grid grid-cols-1 sm:grid-cols-2 md:grid-cols-3 gap-6 w-full">
                @php
                    $mustHaves = [
                        ['name' => 'Baseus 20000mAh Power Bank', 'price' => '1,490', 'old_price' => '2,000', 'badge' => '-25%', 'img' => 'https://images.unsplash.com/photo-1609091839311-d5365f9ff1c5?w=400&q=80'],
                        ['name' => 'Amazfit Bip 3 Smart Watch', 'price' => '3,490', 'old_price' => '4,500', 'badge' => '-22%', 'img' => 'https://images.unsplash.com/photo-1579586337278-3befd40fd17a?w=400&q=80'],
                        ['name' => 'DJI Action 2 Dual-Screen', 'price' => '35,000', 'old_price' => '40,000', 'badge' => '-12%', 'img' => 'https://images.unsplash.com/photo-1564466809058-bf4114d55352?w=400&q=80'],
                    ];
                @endphp
                @foreach($mustHaves as $product)
                    <x-ui.product-card :product="$product" />
                @endforeach
            </div>
            
            <!-- Carousel arrows -->
            <button class="absolute left-2 w-8 h-8 bg-white border border-gray-200 rounded-none flex items-center justify-center shadow-md text-gray-500 hover:text-primary z-10">
                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 19l-7-7 7-7"></path></svg>
            </button>
            <button class="absolute right-2 w-8 h-8 bg-white border border-gray-200 rounded-none flex items-center justify-center shadow-md text-gray-500 hover:text-primary z-10">
                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7"></path></svg>
            </button>
        </div>
    </div>
</section>
