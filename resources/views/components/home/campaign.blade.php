@props(['products' => [], 'title' => 'Flash Campaign', 'flashSale' => null])

@php
    $endDate = $flashSale ? $flashSale->end_date->format('Y-m-d H:i:s') : now()->addDays(1)->format('Y-m-d H:i:s');
@endphp

<section class="max-w-[1400px] mx-auto px-1.5 sm:px-6 lg:px-8 mb-6 sm:mb-10">
    <div class="bg-white rounded-none border border-gray-200">
        <!-- Header area -->
        <div class="flex flex-col sm:flex-row items-center justify-between p-4 sm:p-5 border-b border-gray-100 gap-4">
            <div class="flex flex-col sm:flex-row sm:items-center gap-3 sm:gap-6">
                <div class="flex items-center gap-2">
                    <svg class="w-6 h-6 text-accent" fill="none" stroke="currentColor" viewBox="0 0 24 24" xmlns="http://www.w3.org/2000/svg"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 10V3L4 14h7v7l9-11h-7z"></path></svg>
                    <h2 class="text-xl sm:text-2xl font-black text-slate-800 uppercase tracking-tight">
                        {{ $flashSale ? $flashSale->name : $title }}
                    </h2>
                </div>
                
                <!-- Clean Countdown Timer -->
                <div class="flex items-center gap-2" 
                     x-data="{ 
                        endDate: new Date('{{ $endDate }}').getTime(),
                        days: 0, hours: 0, minutes: 0, seconds: 0,
                        init() {
                            this.updateTimer();
                            setInterval(() => this.updateTimer(), 1000);
                        },
                        updateTimer() {
                            let now = new Date().getTime();
                            let distance = this.endDate - now;
                            if (distance > 0) {
                                this.days = Math.floor(distance / (1000 * 60 * 60 * 24));
                                this.hours = Math.floor((distance % (1000 * 60 * 60 * 24)) / (1000 * 60 * 60));
                                this.minutes = Math.floor((distance % (1000 * 60 * 60)) / (1000 * 60));
                                this.seconds = Math.floor((distance % (1000 * 60)) / 1000);
                            } else {
                                this.days = 0; this.hours = 0; this.minutes = 0; this.seconds = 0;
                            }
                        }
                     }">
                     <span class="text-sm font-medium text-gray-500">Ending in</span>
                    
                    <div class="flex items-center gap-1">
                        <template x-if="days > 0">
                            <div class="flex items-center gap-1">
                                <span class="w-8 h-8 flex items-center justify-center bg-accent-100 text-accent-800 rounded-none text-sm font-bold" x-text="days.toString().padStart(2, '0')"></span>
                                <span class="text-accent font-bold">:</span>
                            </div>
                        </template>
                        <span class="w-8 h-8 flex items-center justify-center bg-accent-100 text-accent-800 rounded-none text-sm font-bold" x-text="hours.toString().padStart(2, '0')"></span>
                        <span class="text-accent font-bold">:</span>
                        <span class="w-8 h-8 flex items-center justify-center bg-accent-100 text-accent-800 rounded-none text-sm font-bold" x-text="minutes.toString().padStart(2, '0')"></span>
                        <span class="text-accent font-bold">:</span>
                        <span class="w-8 h-8 flex items-center justify-center bg-accent-100 text-accent-800 rounded-none text-sm font-bold" x-text="seconds.toString().padStart(2, '0')"></span>
                    </div>
                </div>
            </div>

            @if(count($products) > 6)
                <a href="{{ route('shop') }}" class="hidden sm:inline-flex items-center gap-1 text-sm font-bold text-slate-500 hover:text-primary transition-colors">
                    View All 
                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7"></path></svg>
                </a>
            @endif
        </div>

        <!-- Products Grid -->
        <div class="p-4 sm:p-5">
            <div class="grid grid-cols-2 md:grid-cols-4 lg:grid-cols-6 gap-4">
                @foreach($products->take(6) as $product)
                    @php
                        if (isset($product->discount_percentage) && $product->discount_percentage > 0) {
                            $product->badge = "SAVE " . $product->discount_percentage . "%";
                        }
                    @endphp
                    <x-ui.product-card :product="$product" />
                @endforeach
            </div>
            
            @if(count($products) > 6)
                <div class="mt-6 text-center sm:hidden">
                    <a href="{{ route('shop') }}" class="inline-block px-6 py-2 bg-gray-50 border border-gray-200 text-slate-700 text-sm font-bold rounded-none hover:bg-gray-100 transition-colors">
                        View All Products
                    </a>
                </div>
            @endif
        </div>
    </div>
</section>
