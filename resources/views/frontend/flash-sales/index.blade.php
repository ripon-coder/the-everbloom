@extends('layouts.app')

@section('title', 'Flash Sales | ' . config('demo.site.name', 'The Everbloom'))

@section('content')
<div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-12">
    <div class="mb-10 text-center">
        <span class="text-xs uppercase tracking-[0.2em] text-rose-700 font-semibold">Limited-Time Offers</span>
        <h1 class="mt-2 font-serif text-3xl sm:text-4xl text-stone-900">Flash Sales</h1>
        <p class="mt-2 text-sm text-stone-600">Curated skincare rituals at limited-stock promotional pricing.</p>
    </div>

    @forelse($activeSales as $sale)
        <section class="mb-14">
            <div class="flex flex-wrap items-center justify-between gap-4 border-b border-stone-200 pb-4 mb-6">
                <div>
                    <h2 class="font-serif text-2xl text-stone-900">{{ $sale->title }}</h2>
                    @if($sale->description)
                        <p class="text-sm text-stone-600 mt-1">{{ $sale->description }}</p>
                    @endif
                </div>
                <div class="text-xs font-medium uppercase tracking-wider text-rose-700 bg-rose-50 px-3 py-1.5 rounded-full">
                    Ends {{ $sale->ends_at->format('M d, H:i') }}
                </div>
            </div>

            @php
                $gridProducts = $sale->items->filter(fn($i) => $i->product && $i->product->is_active)->map(function ($item) {
                    $arr = $item->product->toStorefrontArray();
                    $regularPrice = $item->variant ? (float) $item->variant->price : (float) $item->product->price;
                    $arr['price'] = round((float) $item->sale_price, 2);
                    $arr['compare_at_price'] = round($regularPrice, 2);
                    return $arr;
                })->values()->all();
            @endphp

            <x-storefront.product-grid :products="$gridProducts" columns="4" />
        </section>
    @empty
        <div class="text-center py-12 bg-stone-50 rounded-xl border border-stone-200 mb-12">
            <p class="text-stone-600">No active flash sales right now. Check upcoming drops below.</p>
        </div>
    @endforelse

    @if($upcomingSales->isNotEmpty())
        <section class="mt-12">
            <h2 class="font-serif text-2xl text-stone-900 border-b border-stone-200 pb-3 mb-6">Upcoming Flash Sales</h2>
            <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                @foreach($upcomingSales as $upcoming)
                    <div class="p-6 rounded-xl border border-stone-200 bg-white">
                        <span class="text-xs uppercase tracking-wider text-amber-700 font-semibold">Starts {{ $upcoming->starts_at->format('M d, Y H:i') }}</span>
                        <h3 class="mt-1 font-serif text-xl text-stone-900">{{ $upcoming->title }}</h3>
                        @if($upcoming->description)
                            <p class="mt-2 text-sm text-stone-600">{{ $upcoming->description }}</p>
                        @endif
                    </div>
                @endforeach
            </div>
        </section>
    @endif
</div>
@endsection

