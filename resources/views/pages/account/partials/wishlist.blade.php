<div class="bg-white border border-gray-200 rounded-none overflow-hidden" x-data="{ 
    removeItem(id, el) {
        if(!confirm('Are you sure you want to remove this item?')) return;
        
        fetch('{{ route('wishlist.toggle') }}', {
            method: 'POST',
            headers: {
                'Content-Type': 'application/json',
                'X-CSRF-TOKEN': '{{ csrf_token() }}'
            },
            body: JSON.stringify({ product_id: id })
        })
        .then(res => res.json())
        .then(data => {
            if (data.success) {
                el.closest('.wishlist-item').style.opacity = '0';
                setTimeout(() => {
                    el.closest('.wishlist-item').remove();
                    window.dispatchEvent(new CustomEvent('wishlist-updated'));
                    window.dispatchEvent(new CustomEvent('notify', { detail: { message: data.message, type: 'info' } }));
                    
                    if (document.querySelectorAll('.wishlist-item').length === 0) {
                        window.location.reload();
                    }
                }, 300);
            }
        });
    }
}">
    <div class="p-4 md:p-6 border-b border-gray-100 flex items-center justify-between">
        <h2 class="text-sm sm:text-base font-semibold !text-gray-900 uppercase tracking-wide">My Wishlist</h2>
        <span class="bg-emerald-50 !text-emerald-700 text-xs font-semibold px-2.5 py-1 rounded-none uppercase tracking-wide">
            {{ $wishlist->total() }} Items
        </span>
    </div>

    <div class="p-4 md:p-6">
        @if($wishlist->isEmpty())
            <div class="py-12 flex flex-col items-center justify-center text-center">
                <div class="w-20 h-20 bg-gray-50 rounded-none flex items-center justify-center mb-4">
                    <svg class="w-10 h-10 text-gray-300" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M4.318 6.318a4.5 4.5 0 000 6.364L12 20.364l7.682-7.682a4.5 4.5 0 00-6.364-6.364L12 7.636l-1.318-1.318a4.5 4.5 0 00-6.364 0z"></path>
                    </svg>
                </div>
                <h3 class="!text-slate-900 font-semibold mb-1">Your wishlist is empty</h3>
                <p class="!text-gray-600 text-xs sm:text-sm mb-6">Looks like you haven't added anything to your wishlist yet.</p>
                <a href="{{ route('shop') }}" class="bg-slate-900 !text-white px-5 py-2 rounded-none font-semibold text-xs uppercase tracking-wide hover:bg-black transition-colors">Start Shopping</a>
            </div>
        @else
            <div class="space-y-4">
                @foreach($wishlist as $item)
                    <div class="wishlist-item flex flex-col sm:flex-row items-center gap-4 p-4 border border-gray-200 rounded-none hover:border-gray-300 transition-all duration-300 bg-white">
                        <!-- Product Image -->
                        <div class="w-20 h-20 md:w-24 md:h-24 flex-shrink-0 bg-gray-50 rounded-none overflow-hidden group border border-gray-100">
                            <img src="{{ $item->product->firstImage ? $item->product->firstImage->getImageUrl() : asset('images/placeholder.jpg') }}" 
                                 alt="{{ $item->product->name }}" 
                                 class="w-full h-full object-cover group-hover:scale-110 transition-transform duration-500">
                        </div>

                        <!-- Product Info -->
                        <div class="flex-1 min-w-0 text-center sm:text-left">
                            <h4 class="text-xs sm:text-sm font-semibold !text-slate-900 truncate mb-1">
                                <a href="{{ route('product.show', $item->product->slug) }}" class="!text-slate-900 hover:!text-emerald-700 transition-colors block font-semibold">
                                    {{ $item->product->name }}
                                </a>
                            </h4>
                            @php
                                $prod = $item->product;
                                $price = $prod ? ($prod->display_price ?? $prod->price) : 0;
                                $oldPrice = $prod ? ($prod->display_old_price ?? $prod->old_price) : 0;
                            @endphp
                            <div class="flex items-center justify-center sm:justify-start gap-2">
                                <span class="text-xs sm:text-sm font-semibold !text-emerald-700">Tk. {{ number_format((float)$price, 2) }}</span>
                                @if($oldPrice && (float)$oldPrice > (float)$price)
                                    <span class="text-xs !text-gray-400 line-through">Tk. {{ number_format((float)$oldPrice, 2) }}</span>
                                @endif
                            </div>
                        </div>

                        <!-- Actions -->
                        <div class="flex items-center gap-3">
                            <a href="{{ route('product.show', $item->product->slug) }}" 
                               class="bg-slate-900 !text-white px-3.5 py-1.5 rounded-none text-xs font-semibold uppercase tracking-wide hover:bg-black transition-colors whitespace-nowrap">
                                View Product
                            </a>
                            <button @click="removeItem({{ $item->product_id }}, $el)" 
                                    class="p-2 text-gray-400 hover:text-red-600 hover:bg-red-50 rounded-none transition-all duration-200"
                                    title="Remove from Wishlist">
                                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"></path>
                                </svg>
                            </button>
                        </div>
                    </div>
                @endforeach
            </div>

            <!-- Pagination -->
            <div class="mt-8">
                {{ $wishlist->links() }}
            </div>
        @endif
    </div>
</div>
