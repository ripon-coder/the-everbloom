<div class="bg-white border border-gray-200 rounded-md overflow-hidden" x-data="{ 
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
        <h2 class="text-base md:text-lg font-bold text-gray-900 uppercase tracking-tight">My Wishlist</h2>
        <span class="bg-primary-50 text-primary text-[10px] font-bold px-2.5 py-1 rounded-full uppercase tracking-wider">
            {{ $wishlist->total() }} Items
        </span>
    </div>

    <div class="p-4 md:p-6">
        @if($wishlist->isEmpty())
            <div class="py-12 flex flex-col items-center justify-center text-center">
                <div class="w-20 h-20 bg-gray-50 rounded-md flex items-center justify-center mb-4">
                    <svg class="w-10 h-10 text-gray-300" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M4.318 6.318a4.5 4.5 0 000 6.364L12 20.364l7.682-7.682a4.5 4.5 0 00-6.364-6.364L12 7.636l-1.318-1.318a4.5 4.5 0 00-6.364 0z"></path>
                    </svg>
                </div>
                <h3 class="text-gray-900 font-bold mb-1">Your wishlist is empty</h3>
                <p class="text-gray-500 text-sm mb-6">Looks like you haven't added anything to your wishlist yet.</p>
                <a href="{{ route('shop') }}" class="bg-slate-900 text-white px-6 py-2.5 rounded-md font-bold text-xs uppercase tracking-widest hover:bg-primary transition-colors">Start Shopping</a>
            </div>
        @else
            <div class="space-y-4">
                @foreach($wishlist as $item)
                    <div class="wishlist-item flex flex-col sm:flex-row items-center gap-4 p-4 border border-gray-100 rounded-md hover:border-gray-200 transition-all duration-300">
                        <!-- Product Image -->
                        <div class="w-20 h-20 md:w-24 md:h-24 flex-shrink-0 bg-gray-50 rounded-md overflow-hidden group">
                            <img src="{{ $item->product->firstImage ? $item->product->firstImage->getImageUrl() : asset('images/placeholder.jpg') }}" 
                                 alt="{{ $item->product->name }}" 
                                 class="w-full h-full object-cover group-hover:scale-110 transition-transform duration-500">
                        </div>

                        <!-- Product Info -->
                        <div class="flex-1 min-w-0 text-center sm:text-left">
                            <h4 class="text-sm md:text-base font-bold text-gray-900 truncate mb-1">
                                <a href="{{ route('product.show', $item->product->slug) }}" class="hover:text-primary transition-colors">
                                    {{ $item->product->name }}
                                </a>
                            </h4>
                            <div class="flex items-center justify-center sm:justify-start gap-2">
                                <span class="text-sm md:text-base font-black text-primary">Tk. {{ number_format($item->product->price, 2) }}</span>
                                @if($item->product->old_price > 0)
                                    <span class="text-xs text-gray-400 line-through">Tk. {{ number_format($item->product->old_price, 2) }}</span>
                                @endif
                            </div>
                        </div>

                        <!-- Actions -->
                        <div class="flex items-center gap-3">
                            <a href="{{ route('product.show', $item->product->slug) }}" 
                               class="bg-slate-900 text-white px-5 py-2.5 rounded-md text-[10px] font-black uppercase tracking-widest hover:bg-primary transition-colors whitespace-nowrap">
                                View Product
                            </a>
                            <button @click="removeItem({{ $item->product_id }}, $el)" 
                                    class="p-2.5 text-gray-400 hover:text-danger hover:bg-danger/10 rounded-full transition-all duration-200"
                                    title="Remove from Wishlist">
                                <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
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
