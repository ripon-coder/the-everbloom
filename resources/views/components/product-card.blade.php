@props(['product'])

@php
    $isModel = $product instanceof \App\Models\Product;
    $name = $isModel ? $product->name : $product['name'];
    $price = $isModel ? $product->price : $product['price'];
    $oldPrice = $isModel ? ($product->old_price ?? null) : ($product['old_price'] ?? null);
    $badge = $isModel ? ($product->badge ?? '10% OFF') : ($product['badge'] ?? '10% OFF');
    $dummyImages = ['image1.jpg', 'image2.jpg'];
    $img = $isModel ? ($product->firstImage ? $product->firstImage->getImageUrl() : asset('images/' . $dummyImages[array_rand($dummyImages)])) : $product['img'];
    $img .= '?v=' . time();
    $slug = $isModel ? $product->slug : ($product['slug'] ?? '#');
@endphp

<div class="bg-white rounded-md border border-gray-100 transition-all duration-300 flex flex-col group relative overflow-hidden h-full">
    <!-- Image Area -->
    <a href="{{ $isModel ? route('product.show', $slug) : '#' }}" class="relative pt-[100%] bg-gray-50 overflow-hidden">
        <!-- Badges -->
        @if($badge)
            <span class="absolute top-3 left-3 z-20 px-2.5 py-1 text-[10px] font-black tracking-wider uppercase text-white bg-[#E60000] rounded-sm shadow-sm">
                {{ $badge }}
            </span>
        @endif
        
        <img src="{{ $img }}" alt="{{ $name }}" class="absolute top-0 left-0 w-full h-full object-cover transition-transform duration-500" />
    </a>

    <!-- Details Area -->
    <div class="p-5 flex-1 flex flex-col relative bg-white">
        <!-- Stars -->
        <div class="flex items-center gap-0.5 mb-2">
            @for($i=0; $i<5; $i++)
                <svg class="w-3.5 h-3.5 text-amber-400" fill="currentColor" viewBox="0 0 20 20"><path d="M9.049 2.927c.3-.921 1.603-.921 1.902 0l1.07 3.292a1 1 0 00.95.69h3.462c.969 0 1.371 1.24.588 1.81l-2.8 2.034a1 1 0 00-.364 1.118l1.07 3.292c.3.921-.755 1.688-1.54 1.118l-2.8-2.034a1 1 0 00-1.175 0l-2.8 2.034c-.784.57-1.838-.197-1.539-1.118l1.07-3.292a1 1 0 00-.364-1.118L2.98 8.72c-.783-.57-.38-1.81.588-1.81h3.461a1 1 0 00.951-.69l1.07-3.292z"></path></svg>
            @endfor
            <span class="text-[10px] text-gray-400 ml-1 font-medium">(4.9)</span>
        </div>

        <h3 class="text-[14px] font-bold text-slate-800 mb-1 line-clamp-2 leading-snug group-hover:text-[#E60000] transition-colors cursor-pointer">
            <a href="{{ $isModel ? route('product.show', $slug) : '#' }}">{{ $name }}</a>
        </h3>
        
        <div class="mt-auto pt-3 flex items-end justify-between">
            <div class="flex flex-col">
                @if($oldPrice)
                    <span class="text-[12px] font-medium text-slate-400 line-through mb-0.5">৳ {{ $oldPrice }}</span>
                @endif
                <span class="text-[18px] font-black text-[#E60000] leading-none">৳ {{ $price }}</span>
            </div>
        </div>


    </div>
</div>
