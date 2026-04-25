@props(['product'])

<div class="bg-white rounded-xl border border-gray-100 hover:border-transparent hover:shadow-[0_8px_30px_rgb(0,0,0,0.08)] transition-all duration-300 flex flex-col group relative overflow-hidden h-full">
    <!-- Image Area -->
    <div class="relative pt-[100%] bg-gray-50 flex items-center justify-center p-6 overflow-hidden">
        <!-- Badges -->
        @if(isset($product['badge']))
            <span class="absolute top-3 left-3 z-20 px-2.5 py-1 text-[10px] font-black tracking-wider uppercase text-white bg-[#E60000] rounded-sm shadow-sm">
                {{ $product['badge'] }}
            </span>
        @endif
        
        <img src="{{ $product['img'] }}" alt="{{ $product['name'] }}" class="absolute top-0 left-0 w-full h-full object-contain p-6 mix-blend-multiply group-hover:scale-110 transition-transform duration-500" />
    </div>

    <!-- Details Area -->
    <div class="p-5 flex-1 flex flex-col relative bg-white">
        <!-- Stars -->
        <div class="flex items-center gap-0.5 mb-2">
            @for($i=0; $i<5; $i++)
                <svg class="w-3.5 h-3.5 text-amber-400" fill="currentColor" viewBox="0 0 20 20"><path d="M9.049 2.927c.3-.921 1.603-.921 1.902 0l1.07 3.292a1 1 0 00.95.69h3.462c.969 0 1.371 1.24.588 1.81l-2.8 2.034a1 1 0 00-.364 1.118l1.07 3.292c.3.921-.755 1.688-1.54 1.118l-2.8-2.034a1 1 0 00-1.175 0l-2.8 2.034c-.784.57-1.838-.197-1.539-1.118l1.07-3.292a1 1 0 00-.364-1.118L2.98 8.72c-.783-.57-.38-1.81.588-1.81h3.461a1 1 0 00.951-.69l1.07-3.292z"></path></svg>
            @endfor
            <span class="text-[10px] text-gray-400 ml-1 font-medium">(4.9)</span>
        </div>

        <h3 class="text-[14px] font-bold text-slate-800 mb-1 line-clamp-2 leading-snug group-hover:text-[#E60000] transition-colors cursor-pointer">{{ $product['name'] }}</h3>
        
        <div class="mt-auto pt-3 flex items-end justify-between">
            <div class="flex flex-col">
                @if(isset($product['old_price']))
                    <span class="text-[12px] font-medium text-slate-400 line-through mb-0.5">৳ {{ $product['old_price'] }}</span>
                @endif
                <span class="text-[18px] font-black text-[#E60000] leading-none">৳ {{ $product['price'] }}</span>
            </div>
        </div>

        <!-- Add to Cart Hover Button -->
        <div class="absolute inset-x-0 bottom-0 p-4 bg-white translate-y-full opacity-0 group-hover:translate-y-0 group-hover:opacity-100 transition-all duration-300 z-10 border-t border-gray-100">
            <button class="w-full bg-[#E60000] text-white py-2.5 rounded text-[13px] font-bold tracking-wide uppercase hover:bg-red-700 transition-colors flex items-center justify-center gap-2">
                <svg class="w-4 h-4" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M3 3h2l.4 2M7 13h10l4-8H5.4M7 13L5.4 5M7 13l-2.293 2.293c-.63.63-.184 1.707.707 1.707H17m0 0a2 2 0 100 4 2 2 0 000-4zm-8 2a2 2 0 11-4 0 2 2 0 014 0z"></path></svg>
                Add to Cart
            </button>
        </div>
    </div>
</div>
