<x-layouts.app title="Everbloom | Home">
    <div class="bg-white">
        <x-home.hero />
        
        <div class="py-4 space-y-6">
            <x-home.categories />
            @if($flashSale)
                <x-home.campaign :flashSale="$flashSale" :products="$campaignProducts" />
            @endif
            <x-home.featured-products :products="$featuredProducts" />
        </div>
    </div>
</x-layouts.app>
