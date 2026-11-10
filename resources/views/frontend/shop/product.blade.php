@extends('layouts.app')

@section('title', ($product['name'] ?? 'Product') . ' — The Everbloom')
@section('meta_description', $product['short_description'] ?? 'Botanical skincare product.')

@section('content')
<div class="site-container py-8 md:py-12 space-y-12">
    <x-storefront.breadcrumbs :items="[
        ['label' => 'Shop All', 'url' => '/shop'],
        ['label' => $product['category'] ?? 'Category', 'url' => '/shop/category/' . ($product['category_slug'] ?? '')],
        ['label' => $product['name'] ?? 'Product', 'url' => '']
    ]" />

    <div class="grid grid-cols-1 lg:grid-cols-12 gap-10 lg:gap-14">
        <!-- Media Gallery Column -->
        <div class="lg:col-span-7 space-y-4">
            <div class="aspect-[4/5] bg-sage-light/40 border border-beige">
                <x-storefront.image :src="$product['images'][0] ?? null" :alt="$product['name'] ?? ''" aspect-ratio="aspect-[4/5]" />
            </div>
            @if(count($product['images'] ?? []) > 1)
                <div class="grid grid-cols-4 gap-3">
                    @foreach($product['images'] as $img)
                        <div class="aspect-square bg-sage-light/40 border border-beige">
                            <x-storefront.image :src="$img" :alt="$product['name']" aspect-ratio="aspect-square" />
                        </div>
                    @endforeach
                </div>
            @endif
        </div>

        <!-- Product Details Column -->
        <div class="lg:col-span-5 space-y-6">
            <div>
                <span class="text-xs uppercase tracking-[0.2em] font-medium text-forest block mb-1">
                    {{ $product['category'] ?? '' }}
                </span>
                <h1 class="font-serif text-3xl sm:text-4xl text-ink font-normal leading-tight">
                    {{ $product['name'] ?? '' }}
                </h1>
                <div class="mt-2.5 flex items-center gap-3">
                    <x-storefront.rating :rating="$product['rating'] ?? 5" :count="$product['review_count'] ?? 0" />
                    <span class="text-xs text-forest uppercase tracking-widest font-medium">In Stock</span>
                </div>
            </div>

            <div class="border-y border-beige/80 py-4">
                <x-storefront.price-display :price="$product['price'] ?? 0" :compare-at-price="$product['compare_at_price'] ?? null" size="lg" />
            </div>

            <p class="text-sm text-muted leading-relaxed">
                {{ $product['description'] ?? '' }}
            </p>

            <div class="space-y-4 pt-2">
                <x-storefront.add-to-cart-button :product="$product" size="{{ $product['sizes'][0] ?? null }}" />
            </div>

            <div class="pt-6 border-t border-beige">
                <x-storefront.accordion :items="[
                    ['title' => 'Botanical Ingredients', 'content' => $product['ingredients'] ?? ''],
                    ['title' => 'How to Use', 'content' => $product['how_to_use'] ?? ''],
                    ['title' => 'Shipping & Botanical Packaging', 'content' => 'Shipped in recyclable amber glass containers inside recycled botanical fiber cartons. Standard delivery 3–5 business days.']
                ]" />
            </div>
        </div>
    </div>
</div>
@endsection
