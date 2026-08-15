@if(isset($relatedProducts) && $relatedProducts->count() > 0)
    <div class="bg-white border border-gray-100 rounded-md p-6 sticky top-24">
        <h2 class="text-sm sm:text-base font-bold !text-gray-900 uppercase tracking-widest mb-6 border-b border-gray-100 pb-4">Related Products</h2>
        <div class="space-y-6">
            @foreach($relatedProducts->take(5) as $relProduct)
                <a href="{{ route('product.show', $relProduct->slug) }}" class="flex gap-3 group items-start">
                    <div class="w-16 h-16 sm:w-20 sm:h-20 bg-gray-50 overflow-hidden border border-gray-100 rounded-md flex-shrink-0">
                        <img src="{{ $relProduct->img }}" alt="{{ $relProduct->name }}" class="w-full h-full object-contain p-2 group-hover:scale-105 transition-transform duration-300">
                    </div>
                    <div class="flex flex-col gap-1 flex-1 min-w-0">
                        <h4 class="text-xs sm:text-sm font-semibold text-gray-900 group-hover:text-primary transition-colors leading-snug line-clamp-2 block">{{ $relProduct->name }}</h4>
                        <div class="flex flex-wrap items-center gap-2">
                            <span class="text-xs sm:text-sm font-bold text-primary">Tk. {{ number_format($relProduct->price, 2) }}</span>
                            @if($relProduct->old_price)
                                <span class="text-[10px] sm:text-xs text-gray-400 line-through">Tk. {{ number_format($relProduct->old_price, 2) }}</span>
                            @endif
                        </div>
                    </div>
                </a>
            @endforeach
        </div>
        <a href="{{ route('shop') }}" class="block text-center mt-8 text-xs font-bold text-gray-400 uppercase tracking-widest hover:text-primary transition-colors">
            View Full Shop
        </a>
    </div>
@endif
