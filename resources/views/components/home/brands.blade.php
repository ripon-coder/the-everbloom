<section class="max-w-7xl mx-auto px-1.5 sm:px-6 lg:px-8 py-4 sm:py-8">
    <div class="flex items-center justify-between mb-6">
        <h2 class="text-white bg-primary px-6 py-2 rounded-none font-bold text-lg inline-block shadow-md">Shop by Brands</h2>
    </div>

    <div class="grid grid-cols-3 sm:grid-cols-4 md:grid-cols-6 lg:grid-cols-8 gap-4">
        @php
            $brands = [
                'Xiaomi', 'UGREEN', 'Baseus', 'Samsung', 'realme', 'Oppo', 'OnePlus', 'Amazfit',
                'JBL', 'Hoco', 'Awei', 'Edifier', 'Sony', 'Apple', 'Anker', 'Lenovo'
            ];
        @endphp

        @foreach($brands as $brand)
            <a href="#" class="bg-white border border-gray-100 rounded-none h-16 flex items-center justify-center p-2 hover:border-primary hover:shadow-lg transition-all group">
                <span class="font-bold text-slate-800 text-sm group-hover:text-primary transition-colors uppercase tracking-wider">{{ $brand }}</span>
            </a>
        @endforeach
    </div>
    
    <div class="flex justify-end mt-4">
        <a href="#" class="text-sm font-bold text-slate-700 hover:text-primary flex items-center gap-1">
            See all brands <svg class="w-4 h-4 bg-slate-900 text-white rounded-none p-0.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7"></path></svg>
        </a>
    </div>
</section>
