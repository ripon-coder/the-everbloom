<section class="max-w-[1400px] mx-auto px-4 sm:px-6 lg:px-8 py-8">
    <div class="flex items-center justify-between mb-6">
        <h2 class="text-white bg-red-600 px-6 py-2 rounded-full font-bold text-lg inline-block shadow-md">Best Selling Products</h2>
    </div>

    <div class="grid grid-cols-2 sm:grid-cols-3 md:grid-cols-4 lg:grid-cols-6 gap-4 mb-6">
        @php
            $products = [
                ['name' => 'Joyroom JR-T03S Pro', 'price' => '1,490', 'old_price' => '2,500', 'badge' => '-40%', 'img' => 'https://images.unsplash.com/photo-1590658268037-6bf12165a8df?w=400&q=80'],
                ['name' => 'Apple AirPods Pro 2nd Gen', 'price' => '25,000', 'old_price' => '28,000', 'badge' => '-11%', 'img' => 'https://images.unsplash.com/photo-1600294037681-c80b4cb5b434?w=400&q=80'],
                ['name' => 'Realme Buds Q2s', 'price' => '1,890', 'old_price' => '2,200', 'badge' => '-14%', 'img' => 'https://images.unsplash.com/photo-1590658268037-6bf12165a8df?w=400&q=80'],
                ['name' => 'Edifier X3 True Wireless', 'price' => '2,150', 'old_price' => '2,500', 'badge' => '-14%', 'img' => 'https://images.unsplash.com/photo-1590658268037-6bf12165a8df?w=400&q=80'],
                ['name' => 'Lenovo LP40 Pro', 'price' => '850', 'old_price' => '1,200', 'badge' => '-29%', 'img' => 'https://images.unsplash.com/photo-1590658268037-6bf12165a8df?w=400&q=80'],
                ['name' => 'Awei T29 ANC', 'price' => '1,650', 'old_price' => '2,000', 'badge' => '-17%', 'img' => 'https://images.unsplash.com/photo-1590658268037-6bf12165a8df?w=400&q=80'],
                ['name' => 'Samsung Galaxy Buds 2', 'price' => '10,500', 'old_price' => '12,000', 'badge' => '-12%', 'img' => 'https://images.unsplash.com/photo-1590658268037-6bf12165a8df?w=400&q=80'],
                ['name' => 'OnePlus Nord Buds', 'price' => '3,200', 'old_price' => '3,800', 'badge' => '-15%', 'img' => 'https://images.unsplash.com/photo-1590658268037-6bf12165a8df?w=400&q=80'],
                ['name' => 'Boat Airdopes 141', 'price' => '1,350', 'old_price' => '1,800', 'badge' => '-25%', 'img' => 'https://images.unsplash.com/photo-1590658268037-6bf12165a8df?w=400&q=80'],
                ['name' => 'JBL Wave 200TWS', 'price' => '4,500', 'old_price' => '5,000', 'badge' => '-10%', 'img' => 'https://images.unsplash.com/photo-1590658268037-6bf12165a8df?w=400&q=80'],
                ['name' => 'Xiaomi Redmi Buds 3 Lite', 'price' => '1,450', 'old_price' => '1,800', 'badge' => '-19%', 'img' => 'https://images.unsplash.com/photo-1590658268037-6bf12165a8df?w=400&q=80'],
                ['name' => 'Sony WF-1000XM4', 'price' => '22,000', 'old_price' => '25,000', 'badge' => '-12%', 'img' => 'https://images.unsplash.com/photo-1590658268037-6bf12165a8df?w=400&q=80'],
            ];
        @endphp
        @foreach($products as $product)
            <x-product-card :product="$product" />
        @endforeach
    </div>

    <div class="flex justify-end">
        <a href="#" class="text-sm font-bold text-slate-700 hover:text-red-600 flex items-center gap-1">
            See all products <svg class="w-4 h-4 bg-slate-900 text-white rounded-full p-0.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7"></path></svg>
        </a>
    </div>
</section>
