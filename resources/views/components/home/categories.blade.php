@props(['categories'])

<section class="max-w-[1400px] mx-auto px-1.5 sm:px-4 pt-0 pb-6">
    <div class="grid grid-cols-2 sm:grid-cols-4 md:grid-cols-6 lg:grid-cols-8 xl:grid-cols-10 gap-3 sm:gap-4">
        @foreach($categories as $category)
            <a href="{{ route('shop', ['category' => $category->slug]) }}" class="bg-white rounded-none p-3.5 flex flex-col items-center justify-center gap-2.5 border border-gray-200 hover:border-emerald-600 shadow-none transition-all group min-h-[125px]">
                <div class="w-14 h-14 flex items-center justify-center overflow-hidden rounded-full bg-slate-50 group-hover:bg-emerald-50 transition-colors p-1">
                    <img src="{{ $category->getImageUrl() }}" alt="{{ $category->name }}" class="w-full h-full object-cover rounded-full transform group-hover:scale-110 transition-transform duration-300">
                </div>
                <span class="text-xs sm:text-sm font-semibold text-center text-slate-700 group-hover:text-emerald-600 leading-snug line-clamp-2">{{ $category->name }}</span>
            </a>
        @endforeach
    </div>
</section>


