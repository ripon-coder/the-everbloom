<section class="max-w-[1400px] mx-auto px-4 sm:px-6 lg:px-8 py-8">
    <div class="flex items-center justify-between mb-6">
        <h2 class="text-white bg-red-600 px-6 py-2 rounded-full font-bold text-lg inline-block shadow-md">Featured Products</h2>
        <a href="#" class="text-sm font-bold text-slate-700 hover:text-red-600 flex items-center gap-1 hidden md:flex">
            See all products <svg class="w-4 h-4 bg-slate-900 text-white rounded-full p-0.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7"></path></svg>
        </a>
    </div>

    <!-- Subcategories Tabs -->
    <div class="flex flex-wrap items-center gap-3 mb-6">
        <button class="px-4 py-1.5 rounded-full border border-red-500 text-red-600 font-bold text-sm bg-red-50">Smartwatch</button>
        <button class="px-4 py-1.5 rounded-full border border-gray-200 text-slate-600 font-bold text-sm hover:border-red-500 hover:text-red-600 transition-colors">Power Bank</button>
        <button class="px-4 py-1.5 rounded-full border border-gray-200 text-slate-600 font-bold text-sm hover:border-red-500 hover:text-red-600 transition-colors">Cable & Adapter</button>
        <button class="px-4 py-1.5 rounded-full border border-gray-200 text-slate-600 font-bold text-sm hover:border-red-500 hover:text-red-600 transition-colors">Router</button>
        <button class="px-4 py-1.5 rounded-full border border-gray-200 text-slate-600 font-bold text-sm hover:border-red-500 hover:text-red-600 transition-colors">Mobile Accessory</button>
    </div>

    <div class="relative group">
        <div class="grid grid-cols-2 sm:grid-cols-3 md:grid-cols-4 lg:grid-cols-6 gap-4">
            @php
                $products = [
                    ['name' => 'TP-Link UB500 Bluetooth 5.0 Nano USB Adapter', 'price' => '650', 'old_price' => '750', 'badge' => '-13%', 'img' => 'https://images.unsplash.com/photo-1544244015-0df4b3ffc6b0?w=400&q=80'],
                    ['name' => 'UGREEN 100W USB-C Cable', 'price' => '450', 'old_price' => '600', 'badge' => '-25%', 'img' => 'https://images.unsplash.com/photo-1583863788434-e58a36330cf0?w=400&q=80'],
                    ['name' => 'Baseus 65W GaN Charger', 'price' => '2,200', 'old_price' => '2,800', 'badge' => '-21%', 'img' => 'https://images.unsplash.com/photo-1583863788434-e58a36330cf0?w=400&q=80'],
                    ['name' => 'Belkin BoostCharge Power Bank 10K', 'price' => '1,800', 'old_price' => '2,500', 'badge' => '-28%', 'img' => 'https://images.unsplash.com/photo-1609091839311-d5365f9ff1c5?w=400&q=80'],
                    ['name' => 'Mi Wi-Fi Range Extender Pro', 'price' => '1,050', 'old_price' => '1,200', 'badge' => '-12%', 'img' => 'https://images.unsplash.com/photo-1544244015-0df4b3ffc6b0?w=400&q=80'],
                    ['name' => 'Anker PowerLine III', 'price' => '1,200', 'old_price' => '1,500', 'badge' => '-20%', 'img' => 'https://images.unsplash.com/photo-1583863788434-e58a36330cf0?w=400&q=80'],
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
</section>
