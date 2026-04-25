<section class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-8">
    <div class="flex items-center justify-between mb-6">
        <h2 class="text-white bg-red-600 px-6 py-2 rounded-full font-bold text-lg inline-block shadow-md">New Arrivals</h2>
    </div>

    <!-- New Arrival Banner Image -->
    <div class="w-full h-32 md:h-48 rounded-2xl overflow-hidden mb-6 relative bg-gradient-to-r from-blue-900 to-indigo-900">
        <img src="https://images.unsplash.com/photo-1519389950473-47ba0277781c?w=1200&q=80" alt="New Arrival Banner" class="w-full h-full object-cover opacity-50 mix-blend-overlay">
        <div class="absolute inset-0 flex items-center justify-center">
            <h3 class="text-3xl md:text-5xl font-black text-white italic transform -skew-x-12 tracking-widest drop-shadow-2xl">
                <span class="text-cyan-400">NEW</span> ARRIVAL
            </h3>
        </div>
    </div>

    <div class="relative group">
        <div class="grid grid-cols-2 sm:grid-cols-3 md:grid-cols-4 lg:grid-cols-6 gap-4 mb-4">
            @php
                $products = [
                    ['name' => 'Yongnuo YN360 III Pro LED', 'price' => '12,500', 'badge' => 'New', 'img' => 'https://images.unsplash.com/photo-1516035069371-29a1b244cc32?w=400&q=80'],
                    ['name' => 'Godox SL60W LED Video Light', 'price' => '14,000', 'badge' => 'New', 'img' => 'https://images.unsplash.com/photo-1516035069371-29a1b244cc32?w=400&q=80'],
                    ['name' => 'Ulanzi VL49 RGB Video Light', 'price' => '1,800', 'badge' => 'New', 'img' => 'https://images.unsplash.com/photo-1516035069371-29a1b244cc32?w=400&q=80'],
                    ['name' => 'Zhiyun Smooth 5 Gimbal', 'price' => '16,500', 'badge' => 'New', 'img' => 'https://images.unsplash.com/photo-1516035069371-29a1b244cc32?w=400&q=80'],
                    ['name' => 'DJI OM 6 Mobile Gimbal', 'price' => '18,500', 'badge' => 'New', 'img' => 'https://images.unsplash.com/photo-1516035069371-29a1b244cc32?w=400&q=80'],
                    ['name' => 'Boya BY-M1 Lavalier Mic', 'price' => '850', 'badge' => 'New', 'img' => 'https://images.unsplash.com/photo-1516035069371-29a1b244cc32?w=400&q=80'],
                ];
            @endphp
            @foreach($products as $product)
                <x-product-card :product="$product" />
            @endforeach
        </div>

        <button class="absolute -left-4 top-1/2 -translate-y-1/2 w-10 h-10 bg-white border border-gray-200 rounded-full flex items-center justify-center shadow-lg text-gray-500 hover:text-red-600 opacity-0 group-hover:opacity-100 transition-opacity z-10 hidden md:flex">
            <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 19l-7-7 7-7"></path></svg>
        </button>
        <button class="absolute -right-4 top-1/2 -translate-y-1/2 w-10 h-10 bg-white border border-gray-200 rounded-full flex items-center justify-center shadow-lg text-gray-500 hover:text-red-600 opacity-0 group-hover:opacity-100 transition-opacity z-10 hidden md:flex">
            <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7"></path></svg>
        </button>
    </div>

    <div class="flex justify-end">
        <a href="#" class="text-sm font-bold text-slate-700 hover:text-red-600 flex items-center gap-1">
            See all products <svg class="w-4 h-4 bg-slate-900 text-white rounded-full p-0.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7"></path></svg>
        </a>
    </div>
</section>
