@props(['products' => []])

<section class="max-w-[1400px] mx-auto px-1.5 sm:px-6 lg:px-8">


    <!-- Subcategories Tabs removed -->

    <div class="grid grid-cols-2 sm:grid-cols-3 md:grid-cols-4 lg:grid-cols-6 gap-4">
        @foreach($products as $product)
            <x-ui.product-card :product="$product" />
        @endforeach
    </div>
</section>
