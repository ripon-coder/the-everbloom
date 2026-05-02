<x-layouts.app title="Everbloom | Home">
    <div class="bg-white">
        <x-home.hero />
        
        <div class="py-8 space-y-12">
            <x-home.featured-products :products="$featuredProducts" />
            <x-home.campaign :products="$campaignProducts" />
            <x-home.best-selling :products="$bestSellingProducts" />
            <x-home.new-arrivals :products="$newArrivals" />
        </div>
    </div>
</x-layouts.app>
