@if(isset($relatedProducts) && $relatedProducts->count() > 0)
    <div class="mt-12 md:mt-20 pt-8 md:pt-12 border-t border-gray-100">
        <div class="flex items-center justify-between mb-8">
            <h2 class="text-lg md:text-xl font-bold text-gray-900 uppercase tracking-widest">You May Also Like</h2>
            <a href="{{ route('shop') }}" class="text-xs font-bold text-red-600 hover:underline uppercase tracking-wider">Explore All</a>
        </div>
        <div class="grid grid-cols-2 sm:grid-cols-3 md:grid-cols-4 lg:grid-cols-6 gap-4 md:gap-6">
            @foreach($relatedProducts->shuffle()->take(6) as $relProduct)
                <x-ui.product-card :product="$relProduct" />
            @endforeach
        </div>
    </div>
@endif