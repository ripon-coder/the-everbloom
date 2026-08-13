@props(['categories'])

<section class="max-w-[1400px] mx-auto px-1.5 sm:px-4 pt-0 pb-6">
    <div class="grid grid-cols-3 sm:grid-cols-4 md:grid-cols-6 lg:grid-cols-8 xl:grid-cols-10 gap-3">
        @foreach($categories as $category)
            <a href="{{ route('shop', ['category' => $category->slug]) }}" class="bg-white rounded-none p-3 flex flex-col items-center justify-center gap-2 border border-gray-200 hover:border-primary hover:shadow-md transition-all group h-28">
                <div class="w-12 h-12 flex items-center justify-center overflow-hidden rounded-none bg-gray-50 group-hover:bg-primary-50 transition-colors">
                    <img src="{{ $category->getImageUrl() }}" alt="{{ $category->name }}" class="w-full h-full object-cover transform group-hover:scale-110 transition-transform duration-300">
                </div>
                <span class="text-[11px] font-bold text-center text-slate-700 group-hover:text-primary line-clamp-2">{{ $category->name }}</span>
            </a>
        @endforeach
    </div>
</section>
