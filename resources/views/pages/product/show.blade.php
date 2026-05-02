<x-layouts.app :title="$product->name . ' | Everbloom'">
    <div class="bg-white pb-6" x-data="productDetails({{ $product->toJson() }})" x-init="init()">
        <!-- Breadcrumbs -->
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-3">
            <nav class="flex items-center text-[11px] font-medium text-gray-400 uppercase tracking-wider">
                <a href="{{ route('home') }}" class="hover:text-red-600 transition-colors">Home</a>
                <svg class="w-3 h-3 mx-2 text-gray-300" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7"/></svg>
                <a href="#" class="hover:text-red-600 transition-colors">Products</a>
                <svg class="w-3 h-3 mx-2 text-gray-300" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7"/></svg>
                <span class="text-gray-600 truncate">{{ $product->name }}</span>
            </nav>
        </div>

        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 mt-4 md:mt-6">
            <div class="grid grid-cols-1 lg:grid-cols-2 gap-6 md:gap-12">
                @include('pages.product.partials._gallery')
                @include('pages.product.partials._info')
            </div>

            <!-- Main Content Grid (Description/Reviews vs Sidebar) -->
            <div class="grid grid-cols-1 lg:grid-cols-3 gap-12 mt-4 md:mt-8 border-t border-gray-100 pt-4 md:pt-6">
                <!-- Left Side: Description, Specs, and Reviews (Column 1 & 2) -->
                <div class="lg:col-span-2 space-y-12">
                    @include('pages.product.partials._tabs')
                    @include('pages.product.partials._reviews')
                </div>

                <!-- Right Side: Sidebar (Similar Products) -->
                <div class="lg:col-span-1 space-y-8">
                    @include('pages.product.partials._related')
                </div>
            </div>

            <!-- Full Width Bottom Section: You May Also Like -->
            @include('pages.product.partials._also-like')
        </div>

        <!-- Fixed Bottom Mobile Bar -->
        <div class="fixed bottom-[57px] left-0 right-0 bg-white border-t border-gray-100 p-3 flex gap-2 z-50 md:hidden">
            <button @click="addToCart($event)" class="flex-1 bg-red-600 hover:bg-red-700 text-white font-bold py-3 px-4 rounded-md text-[10px] uppercase tracking-wide transition-colors flex items-center justify-center gap-1.5">
                <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 3h2l.4 2M7 13h10l4-8H5.4M7 13L5.4 5M7 13l-2.293 2.293c-.63.63-.184 1.707.707 1.707H17m0 0a2 2 0 100 4 2 2 0 000-4zm-8 2a2 2 0 100 4 2 2 0 000-4z"/></svg>
                Add to Cart
            </button>
            <button class="flex-1 bg-gray-900 hover:bg-black text-white font-bold py-3 px-4 rounded-md text-[10px] uppercase tracking-wide transition-colors">
                Buy Now
            </button>
        </div>
    </div>

    <!-- Product Logic JS -->
    @include('pages.product.partials._scripts')
</x-layouts.app>