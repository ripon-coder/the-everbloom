@props(['products' => []])

<section class="max-w-[1400px] mx-auto px-4 sm:px-6 lg:px-8">
    <div class="flex items-center justify-between mb-8">
        <h2 class="text-xl font-bold text-gray-900 dark:text-white tracking-tight relative inline-block uppercase">
            Best Selling Products
            <span class="absolute -bottom-2 left-0 w-10 h-1 bg-amber-500 rounded-full"></span>
        </h2>
    </div>

    <div class="grid grid-cols-2 sm:grid-cols-3 md:grid-cols-4 lg:grid-cols-6 gap-4 mb-6">
        @foreach($products as $product)
            <x-ui.product-card :product="$product" />
        @endforeach
    </div>

    </div>
</section>
