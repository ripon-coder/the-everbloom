<section class="py-24 bg-slate-950 relative overflow-hidden">
    <!-- Glow effect -->
    <div class="absolute left-0 top-1/4 w-[600px] h-[600px] bg-indigo-600/10 rounded-full blur-[120px] pointer-events-none"></div>
    <div class="absolute right-0 bottom-1/4 w-[600px] h-[600px] bg-cyan-600/10 rounded-full blur-[120px] pointer-events-none"></div>

    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 relative z-10">
        <div class="flex items-end justify-between mb-12">
            <div>
                <h2 class="text-4xl md:text-5xl font-extrabold text-white mb-4">Trending Tech</h2>
                <p class="text-lg text-slate-400">Discover the most sought-after gadgets of the week.</p>
            </div>
            <a href="#" class="hidden md:flex items-center gap-2 text-cyan-400 hover:text-cyan-300 font-bold tracking-wide group text-lg bg-slate-900 border border-slate-800 px-6 py-3 rounded-full hover:border-cyan-500/50 transition-all">
                View All Collection 
                <svg class="w-5 h-5 group-hover:translate-x-1 transition-transform" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 8l4 4m0 0l-4 4m4-4H3"></path></svg>
            </a>
        </div>
        
        <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-3 xl:grid-cols-4 gap-8">
            @php
                $products = [
                    ['id' => 1, 'img' => 'https://images.unsplash.com/photo-1546435770-a3e426bf472b?w=600&q=80&fm=webp', 'name' => 'Sony WH-1000XM5 Wireless Noise Canceling', 'price' => '348.00', 'old_price' => '399.00', 'brand' => 'Sony', 'badge' => 'Sale -15%'],
                    ['id' => 2, 'img' => 'https://images.unsplash.com/photo-1523275335684-37898b6baf30?w=600&q=80&fm=webp', 'name' => 'Apple Watch Ultra - Titanium Case', 'price' => '799.00', 'brand' => 'Apple', 'badge' => 'New'],
                    ['id' => 3, 'img' => 'https://images.unsplash.com/photo-1517336714731-489689fd1ca8?w=600&q=80&fm=webp', 'name' => 'MacBook Pro 16" M3 Max 32GB RAM', 'price' => '3499.00', 'brand' => 'Apple'],
                    ['id' => 4, 'img' => 'https://images.unsplash.com/photo-1605464315542-bda3e2f4e605?w=600&q=80&fm=webp', 'name' => 'DJI Mavic 3 Pro Drone with Hasselblad', 'price' => '2199.00', 'brand' => 'DJI'],
                    ['id' => 5, 'img' => 'https://images.unsplash.com/photo-1606813907291-d86efa9b94db?w=600&q=80&fm=webp', 'name' => 'Sony PlayStation 5 Console', 'price' => '499.00', 'brand' => 'Sony', 'badge' => 'Hot'],
                    ['id' => 6, 'img' => 'https://images.unsplash.com/photo-1600294037681-c80b4cb5b434?w=600&q=80&fm=webp', 'name' => 'AirPods Pro (2nd generation)', 'price' => '249.00', 'brand' => 'Apple'],
                    ['id' => 7, 'img' => 'https://images.unsplash.com/photo-1533228100845-08145b01de14?w=600&q=80&fm=webp', 'name' => 'iPhone 15 Pro Max 256GB', 'price' => '1199.00', 'brand' => 'Apple'],
                    ['id' => 8, 'img' => 'https://images.unsplash.com/photo-1593642632823-8f785ba67e45?w=600&q=80&fm=webp', 'name' => 'Dell XPS 15 OLED Touchscreen', 'price' => '1899.00', 'old_price' => '2099.00', 'brand' => 'Dell', 'badge' => '-10%'],
                ];
            @endphp

            @foreach($products as $p)
                <x-ui.product-card :product="$p" />
            @endforeach
        </div>
        
        <div class="mt-12 text-center md:hidden">
            <a href="#" class="inline-flex items-center gap-2 text-cyan-400 font-bold group bg-slate-900 border border-slate-800 px-8 py-4 rounded-full">
                View All Collection 
                <svg class="w-5 h-5 group-hover:translate-x-1 transition-transform" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 8l4 4m0 0l-4 4m4-4H3"></path></svg>
            </a>
        </div>
    </div>
</section>
