<x-layouts.app :title="($product->meta_title ? $product->meta_title : $product->name) . ' | Feriwalarhat'" :description="$product->meta_description">
    <div class="product-page-container bg-white pb-32 md:pb-12" x-data="productDetails({{ $product->toJson() }})" x-init="init()">
        <!-- Breadcrumbs (desktop only) -->
        <div class="hidden md:block max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-3">
            <nav class="flex items-center text-xs sm:text-sm font-medium text-gray-500 uppercase tracking-wider">
                <a href="{{ route('home') }}" class="hover:text-primary transition-colors">Home</a>
                <svg class="w-3 h-3 mx-2 text-gray-300" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7"/></svg>
                <a href="{{ route('shop') }}" class="hover:text-primary transition-colors">Products</a>
                <svg class="w-3 h-3 mx-2 text-gray-300" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7"/></svg>
                <span class="text-gray-900 font-semibold truncate">{{ $product->name }}</span>
            </nav>
        </div>

        <div class="max-w-7xl mx-auto px-0 md:px-4 sm:px-6 lg:px-8 mt-0 md:mt-6">
            <div class="grid grid-cols-1 lg:grid-cols-2 gap-0 md:gap-10">
                @include('pages.product.partials._gallery')
                @include('pages.product.partials._info')
            </div>

            <!-- Main Content Grid (Description/Reviews vs Sidebar) -->
            <div class="grid grid-cols-1 lg:grid-cols-3 gap-8 md:gap-12 mt-4 md:mt-8 border-t border-gray-100 pt-4 md:pt-6 px-1.5 md:px-0">
                <!-- Left Side: Description, Specs, and Reviews -->
                <div class="lg:col-span-2 space-y-10">
                    @include('pages.product.partials._tabs')
                    @include('pages.product.partials._reviews')
                </div>

                <!-- Right Side: Sidebar -->
                <div class="lg:col-span-1 space-y-8">
                    @include('pages.product.partials._related')
                </div>
            </div>

            <!-- Full Width Bottom Section -->
            <div class="px-1.5 md:px-0">
                @include('pages.product.partials._also-like')
            </div>
        </div>

        <!-- Fixed Bottom Mobile Bar -->
        <div class="fixed bottom-[57px] left-0 right-0 bg-white border-t border-gray-200 flex z-50 md:hidden">
            <button @click="addToCart($event)" :disabled="currentStock <= 0"
                class="flex-1 bg-primary disabled:bg-gray-400 disabled:cursor-not-allowed text-white font-bold py-4 text-xs sm:text-sm uppercase tracking-wide transition-colors flex items-center justify-center gap-1.5">
                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 3h2l.4 2M7 13h10l4-8H5.4M7 13L5.4 5M7 13l-2.293 2.293c-.63.63-.184 1.707.707 1.707H17m0 0a2 2 0 100 4 2 2 0 000-4zm-8 2a2 2 0 100 4 2 2 0 000-4z"/></svg>
                <span x-text="currentStock > 0 ? 'Add to Cart' : 'Out of Stock'">Add to Cart</span>
            </button>
            <button @click="buyNow($event)" :disabled="currentStock <= 0"
                class="flex-1 bg-gray-900 disabled:bg-gray-700 disabled:cursor-not-allowed text-white font-bold py-4 text-xs sm:text-sm uppercase tracking-wide transition-colors border-l border-gray-700">
                Buy Now
            </button>
        </div>
    </div>

    <!-- Product Logic JS -->
    @include('pages.product.partials._scripts')
</x-layouts.app>
