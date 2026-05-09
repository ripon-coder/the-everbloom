<x-layouts.app title="Feriwalarhat | Home">
    <div class="bg-white">
        <x-home.hero :sliders="$sliders" />
        
        <div class="py-4 space-y-6">
            <x-home.categories :categories="$featuredCategories" />
            @if($flashSale)
                <x-home.campaign :flashSale="$flashSale" :products="$campaignProducts" />
            @endif
            <x-home.featured-products :products="$featuredProducts" />
        </div>
    </div>
</x-layouts.app>
