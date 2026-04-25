<x-layouts.app title="Everbloom | Home">
    <div class="bg-white">
        <x-hero />
        
        <div class="py-8 space-y-12">
            <x-featured-products :products="$featuredProducts" />
            <x-campaign :products="$campaignProducts" />
            <x-best-selling :products="$bestSellingProducts" />
            <x-new-arrivals :products="$newArrivals" />
        </div>
    </div>
</x-layouts.app>
