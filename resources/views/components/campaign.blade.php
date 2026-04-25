@props(['products' => [], 'title' => 'Flash Campaign'])

<section class="max-w-[1400px] mx-auto px-4 sm:px-6 lg:px-8">
    <div class="bg-slate-900 rounded-2xl p-6 md:p-10 shadow-xl overflow-hidden relative">
        <!-- Background Decorative Elements -->
        <div class="absolute top-0 right-0 -mt-10 -mr-10 w-40 h-40 bg-white/10 rounded-full blur-3xl"></div>
        <div class="absolute bottom-0 left-0 -mb-10 -ml-10 w-40 h-40 bg-black/10 rounded-full blur-3xl"></div>

        <div class="flex flex-col md:flex-row items-center justify-between gap-6 relative z-10">
            <div class="text-center md:text-left">
                <div class="inline-block px-4 py-1 bg-white/20 backdrop-blur-md rounded-full text-white text-xs font-bold uppercase tracking-widest mb-4 border border-white/30">
                    Limited Time Offer
                </div>
                <h2 class="text-3xl md:text-5xl font-black text-white mb-2 leading-tight">
                    {{ $title }}
                </h2>
                <p class="text-red-100 text-lg font-medium max-w-md">
                    Don't miss out on our exclusive campaign deals. Grab yours before they're gone!
                </p>
                
                <!-- Countdown Timer (Dynamic) -->
                <div class="flex items-center justify-center md:justify-start gap-4 mt-8" 
                     x-data="{ 
                        hours: 12, 
                        minutes: 45, 
                        seconds: 30,
                        init() {
                            setInterval(() => {
                                if (this.seconds > 0) {
                                    this.seconds--;
                                } else {
                                    if (this.minutes > 0) {
                                        this.minutes--;
                                        this.seconds = 59;
                                    } else {
                                        if (this.hours > 0) {
                                            this.hours--;
                                            this.minutes = 59;
                                            this.seconds = 59;
                                        }
                                    }
                                }
                            }, 1000);
                        }
                     }">
                    <div class="flex flex-col items-center">
                        <span class="w-12 h-12 flex items-center justify-center bg-white rounded-lg text-slate-900 font-bold text-xl shadow-lg" x-text="hours.toString().padStart(2, '0')"></span>
                        <span class="text-white text-[10px] mt-1 font-bold uppercase">Hrs</span>
                    </div>
                    <div class="text-white font-bold text-xl">:</div>
                    <div class="flex flex-col items-center">
                        <span class="w-12 h-12 flex items-center justify-center bg-white rounded-lg text-slate-900 font-bold text-xl shadow-lg" x-text="minutes.toString().padStart(2, '0')"></span>
                        <span class="text-white text-[10px] mt-1 font-bold uppercase">Min</span>
                    </div>
                    <div class="text-white font-bold text-xl">:</div>
                    <div class="flex flex-col items-center">
                        <span class="w-12 h-12 flex items-center justify-center bg-white rounded-lg text-slate-900 font-bold text-xl shadow-lg" x-text="seconds.toString().padStart(2, '0')"></span>
                        <span class="text-white text-[10px] mt-1 font-bold uppercase">Sec</span>
                    </div>
                </div>
            </div>

            <div class="w-full md:w-3/5">
                <div class="grid grid-cols-2 lg:grid-cols-3 gap-4">
                    @foreach($products as $product)
                        <div class="bg-white/10 backdrop-blur-md border border-white/20 rounded-xl p-3 hover:bg-white/20 transition-all group">
                            <div class="relative pt-[100%] rounded-lg overflow-hidden mb-3">
                                @php
                                    $img = $product->firstImage ? $product->firstImage->getImageUrl() : asset('images/image1.jpg');
                                @endphp
                                <img src="{{ $img }}" alt="{{ $product->name }}" class="absolute top-0 left-0 w-full h-full object-cover group-hover:scale-110 transition-transform duration-500">
                                <div class="absolute top-2 right-2 bg-red-600 text-white text-[10px] font-bold px-2 py-0.5 rounded">
                                    SAVE 20%
                                </div>
                            </div>
                            <h3 class="text-white font-bold text-sm line-clamp-1 group-hover:text-yellow-300 transition-colors">
                                <a href="{{ route('product.show', $product->slug) }}">{{ $product->name }}</a>
                            </h3>
                            <div class="flex items-center gap-2 mt-1">
                                <span class="text-white font-black text-base">৳ {{ $product->price }}</span>
                                <span class="text-red-200 text-xs line-through opacity-70">৳ {{ $product->price * 1.2 }}</span>
                            </div>
                        </div>
                    @endforeach
                </div>
            </div>
        </div>
    </div>
</section>
