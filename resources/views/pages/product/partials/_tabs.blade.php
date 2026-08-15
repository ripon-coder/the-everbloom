<div x-data="{ activeTab: 'description' }">
    <div class="flex gap-8 border-b border-gray-100 mb-8">
        <button @click="activeTab = 'description'" 
                class="pb-4 text-xs sm:text-sm font-bold uppercase tracking-widest border-b-2 transition-all"
                :class="activeTab === 'description' ? 'border-primary text-gray-900' : 'border-transparent text-gray-400 hover:text-gray-600'">
            Description
        </button>
        <button @click="activeTab = 'specifications'" 
                class="pb-4 text-xs sm:text-sm font-bold uppercase tracking-widest border-b-2 transition-all"
                :class="activeTab === 'specifications' ? 'border-primary text-gray-900' : 'border-transparent text-gray-400 hover:text-gray-600'">
            Specifications
        </button>
    </div>

    <div class="prose max-w-none text-xs sm:text-sm text-gray-800 break-words leading-relaxed space-y-4">
        <div x-show="activeTab === 'description'">
            {!! $product->description !!}
        </div>
        <div x-show="activeTab === 'specifications'" x-cloak>
            <table class="w-full text-xs sm:text-sm">
                <tbody class="divide-y divide-gray-100">
                    <tr><td class="py-2.5 font-bold w-1/3 text-gray-900">SKU</td><td class="py-2.5 text-gray-700">{{ $product->sku }}</td></tr>
                    <tr><td class="py-2.5 font-bold text-gray-900">Availability</td><td class="py-2.5 text-emerald-600 font-bold">In Stock</td></tr>
                    <tr><td class="py-2.5 font-bold text-gray-900">Category</td><td class="py-2.5 text-gray-700">{{ $product->category->name ?? 'N/A' }}</td></tr>
                </tbody>
            </table>
        </div>
    </div>
</div>
