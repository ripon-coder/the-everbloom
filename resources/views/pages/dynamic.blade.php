<x-layouts.app 
    :title="($page->meta_title ?: $page->title) . ' | ' . ($site_setting->site_name ?? 'Feriwalarhat')"
    :description="$page->meta_description"
>
    <!-- Breadcrumb -->
    <div class="bg-gray-50 border-b border-gray-200 py-4">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
            <nav class="text-sm text-gray-500">
                <a href="{{ route('home') }}" class="hover:text-red-600">Home</a>
                <span class="mx-2">/</span>
                <span class="text-gray-900">{{ $page->title }}</span>
            </nav>
        </div>
    </div>

    <!-- Main Content -->
    <div class="py-12">
        <div class="max-w-4xl mx-auto px-4 sm:px-6 lg:px-8">
            <h1 class="text-3xl font-bold text-gray-900 mb-8 border-b pb-4">{{ $page->title }}</h1>
            
            <div class="prose max-w-none text-gray-700 leading-relaxed">
                {!! $page->content !!}
            </div>
        </div>
    </div>

</x-layouts.app>
