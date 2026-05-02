<div x-data="{ activeTab: 'description' }">
    <div class="flex gap-8 border-b border-gray-100 mb-8">
        <button @click="activeTab = 'description'" 
                class="pb-4 text-xs font-bold uppercase tracking-widest border-b-2 transition-all"
                :class="activeTab === 'description' ? 'border-red-600 text-gray-900' : 'border-transparent text-gray-400 hover:text-gray-600'">
            Description
        </button>
        <button @click="activeTab = 'specifications'" 
                class="pb-4 text-xs font-bold uppercase tracking-widest border-b-2 transition-all"
                :class="activeTab === 'specifications' ? 'border-red-600 text-gray-900' : 'border-transparent text-gray-400 hover:text-gray-600'">
            Specifications
        </button>
    </div>
    <div class="prose prose-sm max-w-none text-gray-600">
        <div x-show="activeTab === 'description'" class="leading-relaxed">
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