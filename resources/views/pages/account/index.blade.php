<x-layouts.app title="My Account | Everbloom">
    <div class="bg-gray-50 py-6 md:py-10" x-data="{ activeTab: 'dashboard' }">
        <div class="max-w-[1200px] mx-auto px-4 sm:px-6 lg:px-8">
            
            <!-- Breadcrumbs -->
            <nav class="flex text-xs font-medium text-gray-500 uppercase tracking-wider mb-6 md:mb-8">
                <a href="{{ route('home') }}" class="hover:text-red-600 transition-colors">Home</a>
                <span class="mx-2">/</span>
                <span class="text-gray-900">My Account</span>
            </nav>

            <div class="flex flex-col md:flex-row gap-8">
                
                @include('pages.account.partials.sidebar')

                <!-- Main Content -->
                <div class="flex-1">
                    
                    @include('pages.account.partials.dashboard')

                    @include('pages.account.partials.orders')

                    @include('pages.account.partials.addresses')

                    @include('pages.account.partials.details')

                </div>
            </div>
        </div>
    </div>
</x-layouts.app>

