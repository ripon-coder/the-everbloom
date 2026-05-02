@props(['products' => []])

<section class="max-w-[1400px] mx-auto px-4 sm:px-6 lg:px-8">
    <div class="flex items-center justify-between mb-6">
        <h2 class="text-white bg-red-600 px-6 py-2 rounded-full font-bold text-lg inline-block shadow-md">Best Selling Products</h2>
    </div>

    <div class="grid grid-cols-2 sm:grid-cols-3 md:grid-cols-4 lg:grid-cols-6 gap-4 mb-6">
        @foreach($products as $product)
            <x-ui.product-card :product="$product" />
        @endforeach
    </div>

    </div>
</section>
