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
        $reviewsCount = $productObj instanceof \App\Models\Product ? $productObj->reviews_count : (int) ($productObj->reviews_count ?? 0);
        $avgRating = $productObj instanceof \App\Models\Product ? $productObj->average_rating : (float) ($productObj->avg_rating ?? $productObj->rating ?? 0);
    } else {
        $price = $productObj->price ?? 0;
        $oldPrice = $productObj->old_price ?? null;
        $img = $productObj->img ?? asset('images/image1.jpg');
        $name = $productObj->name ?? '';
        $slug = $productObj->slug ?? '';
        $badge = $productObj->badge ?? null;

        $reviewsCount = (int) ($productObj->reviews_count ?? 0);
        $avgRating = (float) ($productObj->avg_rating ?? $productObj->rating ?? 0);
    }

    $imgUrl = is_string($img) ? $img : asset('images/image1.jpg');
@endphp

<div class="bg-white rounded-md border border-gray-100 transition-all duration-300 flex flex-col group relative overflow-hidden h-full">
    <!-- Image Area -->
    <a href="{{ $slug ? route('product.show', $slug) : '#' }}" class="relative pt-[100%] bg-gray-50 overflow-hidden">
        <!-- Badges -->
        @if($badge)
            <span class="absolute top-3 left-3 z-20 px-2 py-0.5 text-[10px] font-bold tracking-wider uppercase text-white bg-accent rounded-xs">
                {{ $badge }}
            </span>
        @endif
        
        <img src="{{ $imgUrl }}" alt="{{ $name }}" class="absolute top-0 left-0 w-full h-full object-cover transition-transform duration-500 group-hover:scale-105" />
    </a>

    <!-- Details Area -->
    <div class="p-3 flex-1 flex flex-col relative bg-white">
        <!-- Dynamic Stars & Ratings -->
        <div class="flex items-center gap-0.5 mb-1.5">
            @for($i = 1; $i <= 5; $i++)
                <svg class="w-3.5 h-3.5 {{ ($reviewsCount > 0 && $i <= round($avgRating)) ? 'text-amber-400 fill-current' : 'text-gray-200 fill-current' }}" viewBox="0 0 20 20">
                    <path d="M9.049 2.927c.3-.921 1.603-.921 1.902 0l1.07 3.292a1 1 0 00.95.69h3.462c.969 0 1.371 1.24.588 1.81l-2.8 2.034a1 1 0 00-.364 1.118l1.07 3.292c.3.921-.755 1.688-1.54 1.118l-2.8-2.034a1 1 0 00-1.175 0l-2.8 2.034c-.784.57-1.838-.197-1.539-1.118l1.07-3.292a1 1 0 00-.364-1.118L2.98 8.72c-.783-.57-.38-1.81.588-1.81h3.461a1 1 0 00.951-.69l1.07-3.292z"></path>
                </svg>
            @endfor
            <span class="text-xs text-gray-400 ml-1 font-medium">
                @if($reviewsCount > 0)
                    ({{ number_format($avgRating, 1) }})
                @else
                    (0)
                @endif
            </span>
        </div>

        <h3 class="text-xs sm:text-sm font-semibold mb-1 leading-snug">
            <a href="{{ $slug ? route('product.show', $slug) : '#' }}" class="block line-clamp-2 text-gray-900 hover:text-primary transition-colors overflow-hidden" title="{{ $name }}" style="display: -webkit-box; -webkit-line-clamp: 2; -webkit-box-orient: vertical; overflow: hidden; word-break: break-word;">
                {{ $name }}
            </a>
        </h3>
        
        <div class="mt-auto pt-1 flex items-center justify-between gap-2">
            <div class="flex flex-col">
                @if(!empty($oldPrice) && (float)$oldPrice > (float)$price)
                    <span class="text-xs sm:text-sm font-normal text-slate-400 line-through mb-0.5">Tk. {{ is_numeric($oldPrice) ? number_format((float)$oldPrice, 2) : $oldPrice }}</span>
                @endif
                <span class="text-base sm:text-lg font-bold text-primary leading-none">Tk. {{ is_numeric($price) ? number_format((float)$price, 2) : $price }}</span>
            </div>
        </div>
    </div>
</div>
