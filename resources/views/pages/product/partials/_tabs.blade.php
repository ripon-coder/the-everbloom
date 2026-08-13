<div x-data="{ activeTab: 'description' }">
    <div class="flex gap-8 border-b border-gray-100 mb-8">
        <button @click="activeTab = 'description'" 
                class="pb-4 text-xs font-bold uppercase tracking-widest border-b-2 transition-all"
                :class="activeTab === 'description' ? 'border-primary text-gray-900' : 'border-transparent text-gray-400 hover:text-gray-600'">
            Description
        </button>
        <button @click="activeTab = 'specifications'" 
                class="pb-4 text-xs font-bold uppercase tracking-widest border-b-2 transition-all"
                :class="activeTab === 'specifications' ? 'border-primary text-gray-900' : 'border-transparent text-gray-400 hover:text-gray-600'">
            Specifications
        </button>
    </div>
    <div class="prose prose-sm max-w-none text-gray-600 overflow-x-auto break-words [&_img]:max-w-full [&_img]:h-auto [&_iframe]:max-w-full [&_h1]:text-base [&_h1]:sm:text-lg [&_h1]:font-bold [&_h1]:text-gray-900 [&_h2]:text-sm [&_h2]:sm:text-base [&_h2]:font-bold [&_h2]:text-gray-900 [&_h3]:text-xs [&_h3]:sm:text-sm [&_h3]:font-bold [&_h3]:text-gray-900 [&_p]:text-xs [&_p]:sm:text-sm [&_p]:leading-relaxed [&_table]:w-full [&_table]:max-w-full [&_span]:!text-xs [&_span]:sm:!text-sm">
        <div x-show="activeTab === 'description'" class="leading-relaxed space-y-3">
            {!! $product->description !!}
        </div>
        <div x-show="activeTab === 'specifications'" x-cloak>
            <table class="w-full text-xs">
                <tbody class="divide-y divide-gray-100">
                    <tr><td class="py-3 font-bold w-1/3 text-gray-900">SKU</td><td class="py-3 text-gray-600">{{ $product->sku }}</td></tr>
                    <tr><td class="py-3 font-bold text-gray-900">Availability</td><td class="py-3 text-green-600 font-bold">In Stock</td></tr>
                    <tr><td class="py-3 font-bold text-gray-900">Category</td><td class="py-3 text-gray-600">{{ $product->category->name ?? 'N/A' }}</td></tr>
                </tbody>
            </table>
        </div>
    </div>
</div>
