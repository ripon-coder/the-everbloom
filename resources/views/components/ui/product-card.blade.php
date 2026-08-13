@props(['product'])

@php
    $productObj = is_array($product) ? (object) $product : $product;

    if ($productObj instanceof \App\Models\Product) {
        $basePrice = $productObj->display_price;
        $oldPrice = $productObj->display_old_price;

        if ($productObj->relationLoaded('flashSales') && $productObj->flashSales->isNotEmpty()) {
            $flashSale = $productObj->flashSales->first();
            $discountPrice = $flashSale->pivot->discount_price;
            $discountPercentage = $flashSale->pivot->discount_percentage;

            $oldPrice = $basePrice;
            $price = $discountPrice 
                ? ($basePrice - $discountPrice) 
                : ($basePrice - ($basePrice * ($discountPercentage / 100)));
            $price = max(0, $price);
        } else {
            $price = $basePrice;
        }

        $img = $productObj->firstImage ? $productObj->firstImage->getImageUrl() : asset('images/image1.jpg');
        $name = $productObj->name;
        $slug = $productObj->slug;
        $badge = $productObj->badge ?? null;
    } else {
        $price = $productObj->price ?? 0;
        $oldPrice = $productObj->old_price ?? null;
        $img = $productObj->img ?? asset('images/image1.jpg');
        $name = $productObj->name ?? '';
        $slug = $productObj->slug ?? '';
        $badge = $productObj->badge ?? null;
    }

    $imgUrl = is_string($img) ? $img : asset('images/image1.jpg');
    if (!str_starts_with($imgUrl, 'http://') && !str_starts_with($imgUrl, 'https://')) {
        $imgUrl = $imgUrl . (str_contains($imgUrl, '?') ? '&' : '?') . 'v=' . time();
    }
@endphp

<div class="bg-white rounded-md border border-gray-100 transition-all duration-300 flex flex-col group relative overflow-hidden h-full">
    <!-- Image Area -->
    <a href="{{ $slug ? route('product.show', $slug) : '#' }}" class="relative pt-[100%] bg-gray-50 overflow-hidden">
        <!-- Badges -->
        @if($badge)
            <span class="absolute top-3 left-3 z-20 px-2.5 py-1 text-[10px] font-black tracking-wider uppercase text-white bg-accent rounded-sm shadow-sm">
                {{ $badge }}
            </span>
        @endif
        
        <img src="{{ $imgUrl }}" alt="{{ $name }}" class="absolute top-0 left-0 w-full h-full object-cover transition-transform duration-500 group-hover:scale-110" />
    </a>

    <!-- Details Area -->
    <div class="p-3 flex-1 flex flex-col relative bg-white">
        <!-- Stars -->
        <div class="flex items-center gap-0.5 mb-2">
            @for($i=0; $i<5; $i++)
                <svg class="w-3.5 h-3.5 text-amber-400" fill="currentColor" viewBox="0 0 20 20"><path d="M9.049 2.927c.3-.921 1.603-.921 1.902 0l1.07 3.292a1 1 0 00.95.69h3.462c.969 0 1.371 1.24.588 1.81l-2.8 2.034a1 1 0 00-.364 1.118l1.07 3.292c.3.921-.755 1.688-1.54 1.118l-2.8-2.034a1 1 0 00-1.175 0l-2.8 2.034c-.784.57-1.838-.197-1.539-1.118l1.07-3.292a1 1 0 00-.364-1.118L2.98 8.72c-.783-.57-.38-1.81.588-1.81h3.461a1 1 0 00.951-.69l1.07-3.292z"></path></svg>
            @endfor
            <span class="text-[10px] text-gray-400 ml-1 font-medium">(4.9)</span>
        </div>

        <h3 class="text-[12px] font-bold text-slate-800 mb-1 line-clamp-2 leading-snug group-hover:text-primary transition-colors cursor-pointer">
            <a href="{{ $slug ? route('product.show', $slug) : '#' }}">{{ $name }}</a>
        </h3>
        
        <div class="mt-auto pt-3 flex items-center justify-between gap-2">
            <div class="flex flex-col">
                @if(!empty($oldPrice) && (float)$oldPrice > (float)$price)
                    <span class="text-[11px] font-medium text-slate-400 line-through mb-0.5">Tk. {{ is_numeric($oldPrice) ? number_format((float)$oldPrice, 2) : $oldPrice }}</span>
                @endif
                <span class="text-[14px] sm:text-[16px] font-black text-primary leading-none">Tk. {{ is_numeric($price) ? number_format((float)$price, 2) : $price }}</span>
            </div>

            @if(isset($productObj->id))
                <button type="button" @click.prevent.stop="window.addQuickToCart({
                    id: {{ $productObj->id }},
                    name: '{{ addslashes($name) }}',
                    price: {{ (float)$price }},
                    image: '{{ addslashes($imgUrl) }}',
                    slug: '{{ addslashes($slug) }}'
                })" class="w-8 h-8 rounded-full bg-primary hover:bg-primary-dark text-white flex items-center justify-center transition-colors shadow-xs shrink-0" title="Add to Cart">
                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 3h2l.4 2M7 13h10l4-8H5.4M7 13L5.4 5M7 13l-2.293 2.293c-.63.63-.184 1.707.707 1.707H17m0 0a2 2 0 100 4 2 2 0 000-4zm-8 2a2 2 0 11-4 0 2 2 0 000-4z"></path>
                    </svg>
                </button>
            @endif
        </div>
    </div>
</div>
