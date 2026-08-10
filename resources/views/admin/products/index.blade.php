@extends('admin.layouts.app')

@section('title', 'Products')

@section('content')
    <div class="p-4">
        <!-- Header -->
        <div class="flex justify-between items-center mb-6">
            <h1 class="text-2xl font-bold text-gray-900">Products</h1>
            <a href="{{ route('admin.products.create') }}"
                class="bg-blue-600 hover:bg-blue-700 text-white font-medium py-2 px-4 rounded-lg transition duration-200 flex items-center">
                <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24"
                    xmlns="http://www.w3.org/2000/svg">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4"></path>
                </svg>
                Add Product
            </a>
        </div>

        <!-- Products Table -->
        <div class="bg-white rounded-lg shadow overflow-hidden">
            <div class="overflow-x-auto">
                <table class="min-w-full divide-y divide-gray-200">
                    <thead class="bg-gray-50">
                        <tr>
                            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">ID
                            </th>
                            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Image
                            </th>
                            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Name
                            </th>
                            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Brand
                            </th>
                            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                                Category</th>
                            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                                Status</th>
                            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                                Featured</th>
                            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                                Variants</th>
                            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                                Created</th>
                            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                                Actions</th>
                        </tr>
                    </thead>
                    <tbody class="bg-white divide-y divide-gray-200">
                        @forelse($products as $product)
                            <tr class="hover:bg-gray-50 transition duration-150">
                                <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-900">{{ $product->id }}</td>
                                <td class="px-6 py-4 whitespace-nowrap">
                                    @php $thumb = $product->firstImage ?? $product->anyImage; @endphp
                                    @if ($thumb)
                                        <img src="{{ $thumb->getImageUrl() }}" alt="{{ $product->name }}"
                                            class="w-12 h-12 rounded-lg object-cover">
                                    @else
                                        <div class="w-12 h-12 bg-gray-200 rounded-lg flex items-center justify-center">
                                            <svg class="w-6 h-6 text-gray-400" fill="none" stroke="currentColor"
                                                viewBox="0 0 24 24" xmlns="http://www.w3.org/2000/svg">
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                                    d="M4 16l4.586-4.586a2 2 0 012.828 0L16 16m-2-2l1.586-1.586a2 2 0 012.828 0L20 14m-6-6h.01M6 20h12a2 2 0 002-2V6a2 2 0 00-2-2H6a2 2 0 00-2 2v12a2 2 0 002 2z">
                                                </path>
                                            </svg>
                                        </div>
                                    @endif
                                </td>
                                <td class="px-6 py-4 whitespace-nowrap">
                                    <div class="text-sm font-medium text-gray-900">{{ $product->name }}</div>
                                    <div class="text-sm text-gray-500">{{ $product->slug }}</div>
                                </td>
                                <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-900">
                                    {{ $product->brand?->name ?: 'N/A' }}</td>
                                <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-900">
                                    {{ $product->category?->name ?: 'N/A' }}</td>
                                <td class="px-6 py-4 whitespace-nowrap">
                                    <span
                                        class="inline-flex px-2 py-1 text-xs font-semibold rounded-full 
                                    {{ $product->status === 'active' ? 'bg-green-100 text-green-800' : 'bg-red-100 text-red-800' }}">
                                        {{ ucfirst($product->status) }}
                                    </span>
                                </td>
                                <td class="px-6 py-4 whitespace-nowrap">
                                    <span
                                        class="inline-flex px-2 py-1 text-xs font-semibold rounded-full 
                                    {{ $product->is_featured ? 'bg-yellow-100 text-yellow-800' : 'bg-gray-100 text-gray-800' }}">
                                        {{ $product->is_featured ? 'Yes' : 'No' }}
                                    </span>
                                </td>
                                <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-900">
                                    <span
                                        class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium bg-blue-100 text-blue-800">
                                        {{ $product->variants_count }}
                                    </span>
                                </td>
                                <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500">
                                    {{ $product->created_at->format('M d, Y') }}</td>
                                <td class="px-6 py-4 whitespace-nowrap text-sm font-medium">
                                    <div class="flex items-center space-x-2">
                                        <button type="button" 
                                                data-product="{{ json_encode([
                                                    'id' => $product->id,
                                                    'name' => $product->name,
                                                    'product_type' => $product->product_type ?? 'single',
                                                    'price' => $product->price,
                                                    'status' => $product->status,
                                                    'is_featured' => (int) $product->is_featured,
                                                    'is_free_delivery' => (int) $product->is_free_delivery
                                                ]) }}"
                                                class="quick-edit-btn text-purple-600 hover:text-purple-900 transition duration-150"
                                                title="Quick Edit">
                                            <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24" xmlns="http://www.w3.org/2000/svg">
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 10V3L4 14h7v7l9-11h-7z"></path>
                                            </svg>
                                        </button>
                                         <a href="{{ route('admin.products.show', $product) }}"
                                            class="text-blue-600 hover:text-blue-900 transition duration-150"
                                            title="View">
                                            <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"
                                                xmlns="http://www.w3.org/2000/svg">
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                                    d="M15 12a3 3 0 11-6 0 3 3 0 016 0z"></path>
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                                    d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z">
                                                </path>
                                            </svg>
                                        </a>
                                        <a href="{{ route('admin.products.edit', $product) }}"
                                            class="text-yellow-600 hover:text-yellow-900 transition duration-150"
                                            title="Edit">
                                            <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"
                                                xmlns="http://www.w3.org/2000/svg">
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                                    d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z">
                                                </path>
                                            </svg>
                                        </a>
                                        @if ($product->trashed())
                                            <form action="{{ route('admin.products.restore', $product->id) }}"
                                                method="POST" class="inline">
                                                @csrf
                                                <button type="submit"
                                                    class="text-green-600 hover:text-green-900 transition duration-150"
                                                    title="Restore">
                                                    <svg class="w-5 h-5" fill="none" stroke="currentColor"
                                                        viewBox="0 0 24 24" xmlns="http://www.w3.org/2000/svg">
                                                        <path stroke-linecap="round" stroke-linejoin="round"
                                                            stroke-width="2"
                                                            d="M4 4v5h.582m15.356 2A8.001 8.001 0 004.582 9m0 0H9m11 11v-5h-.581m0 0a8.003 8.003 0 01-15.357-2m15.357 2H15">
                                                        </path>
                                                    </svg>
                                                </button>
                                            </form>
                                            <button
                                                onclick="showDeleteModal('product permanently', '{{ route('admin.products.force-delete', $product->id) }}', '{{ $product->name }}')"
                                                class="text-red-600 hover:text-red-900 transition duration-150"
                                                title="Force Delete">
                                                <svg class="w-5 h-5" fill="none" stroke="currentColor"
                                                    viewBox="0 0 24 24" xmlns="http://www.w3.org/2000/svg">
                                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                                        d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16">
                                                    </path>
                                                </svg>
                                            </button>
                                        @else
                                            <button
                                                onclick="showDeleteModal('product', '{{ route('admin.products.destroy', $product->id) }}', '{{ $product->name }}')"
                                                class="text-red-600 hover:text-red-900 transition duration-150"
                                                title="Delete">
                                                <svg class="w-5 h-5" fill="none" stroke="currentColor"
                                                    viewBox="0 0 24 24" xmlns="http://www.w3.org/2000/svg">
                                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                                        d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16">
                                                    </path>
                                                </svg>
                                            </button>
                                        @endif
                                    </div>
                                </td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="10" class="px-6 py-8 text-center text-gray-500">
                                    <div class="flex flex-col items-center justify-center space-y-3">
                                        <svg class="w-12 h-12 text-gray-400" fill="none" stroke="currentColor"
                                            viewBox="0 0 24 24" xmlns="http://www.w3.org/2000/svg">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                                d="M19 11H5m14 0a2 2 0 012 2v6a2 2 0 01-2 2H5a2 2 0 01-2-2v-6a2 2 0 012-2m14 0V9a2 2 0 00-2-2M5 11V9a2 2 0 012-2m0 0V5a2 2 0 012-2h6a2 2 0 012 2v2M7 7h10">
                                            </path>
                                        </svg>
                                        <p class="text-lg font-medium text-gray-900">No products found</p>
                                        <p class="text-sm text-gray-500">Get started by creating your first product.</p>
                                        <a href="{{ route('admin.products.create') }}"
                                            class="bg-blue-600 hover:bg-blue-700 text-white font-medium py-2 px-4 rounded-lg transition duration-200 flex items-center">
                                            <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor"
                                                viewBox="0 0 24 24" xmlns="http://www.w3.org/2000/svg">
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                                    d="M12 4v16m8-8H4"></path>
                                            </svg>
                                            Create Product
                                        </a>
                                    </div>
                                </td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>

            <!-- Pagination -->
            @if($products->count() > 0)
                <div class="bg-white px-4 py-3 flex items-center justify-between border-t border-gray-200 sm:px-6">
                    <div class="flex-1 flex justify-between sm:hidden">
                        {{ $products->links() }}
                    </div>
                    <div class="hidden sm:flex-1 sm:flex sm:items-center sm:justify-between">
                        <div>
                            <p class="text-sm text-gray-700">
                                Showing <span class="font-medium">{{ $products->firstItem() }}</span> to <span
                                    class="font-medium">{{ $products->lastItem() }}</span> of{' '}
                                <span class="font-medium">{{ $products->total() }}</span> results
                            </p>
                        </div>
                        <div>
                            {{ $products->links() }}
                        </div>
                    </div>
                </div>
            @endif
        </div>
    </div>

    <!-- Quick Edit Modal -->
    <div id="quickEditModal" class="fixed inset-0 z-[100] hidden flex items-center justify-center p-4 sm:p-6" role="dialog" aria-modal="true">
        <!-- Background overlay -->
        <div class="fixed inset-0 bg-gray-900/60 transition-opacity" aria-hidden="true" onclick="closeQuickEditModal()"></div>

        <!-- Modal panel -->
        <div class="relative bg-white rounded-xl shadow-2xl w-full max-w-lg overflow-hidden transform transition-all">
            <form id="quickEditForm" onsubmit="submitQuickEdit(event)">
                @csrf
                <input type="hidden" id="quick_edit_id">
                
                <div class="px-6 py-4 border-b border-gray-100 flex justify-between items-center bg-gray-50/50">
                    <h3 class="text-xl font-bold text-gray-900">Quick Edit Product</h3>
                    <button type="button" onclick="closeQuickEditModal()" class="text-gray-400 hover:text-gray-600 transition-colors p-2 hover:bg-gray-100 rounded-lg">
                        <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"></path></svg>
                    </button>
                </div>
                
                <div class="p-6 space-y-6">
                    <!-- Basic Info -->
                    <div class="space-y-4">
                        <div>
                            <label for="quick_name" class="block text-sm font-bold text-gray-700 mb-1">Product Name</label>
                            <input type="text" id="quick_name" name="name" class="w-full px-4 py-2.5 border border-gray-200 rounded-lg outline-none text-gray-600 bg-gray-100 cursor-not-allowed" readonly>
                        </div>
                    </div>

                    <!-- Toggles Section -->
                    <div class="grid grid-cols-1 sm:grid-cols-2 gap-4 pt-6 border-t border-gray-100">
                        <div class="flex items-center space-x-3 p-3 bg-gray-50/50 rounded-lg">
                            <input type="checkbox" id="quick_is_featured" name="is_featured" class="w-5 h-5 text-blue-600 border-gray-300 rounded focus:ring-blue-500 cursor-pointer">
                            <label for="quick_is_featured" class="text-sm font-bold text-gray-700 cursor-pointer">Featured Product</label>
                        </div>
                        
                        <div id="quick_free_delivery_wrapper" class="flex items-center space-x-3 p-3 bg-gray-50/50 rounded-lg">
                            <input type="checkbox" id="quick_is_free_delivery" name="is_free_delivery" class="w-5 h-5 text-blue-600 border-gray-300 rounded focus:ring-blue-500 cursor-pointer">
                            <label for="quick_is_free_delivery" class="text-sm font-bold text-gray-700 cursor-pointer">Free Delivery</label>
                        </div>
                    </div>
                </div>
                
                <div class="px-6 py-4 bg-gray-50 flex flex-col-reverse sm:flex-row sm:justify-end gap-3 border-t border-gray-100">
                    <button type="button" onclick="closeQuickEditModal()" class="w-full sm:w-auto px-5 py-2.5 text-sm font-bold text-gray-700 bg-white border border-gray-300 rounded-lg hover:bg-gray-50 focus:ring-4 focus:ring-gray-100 transition-all">
                        Cancel
                    </button>
                    <button type="submit" class="w-full sm:w-auto px-5 py-2.5 text-sm font-bold text-white bg-blue-600 rounded-lg hover:bg-blue-700 focus:ring-4 focus:ring-blue-300 transition-all shadow-lg shadow-blue-500/30">
                        Save Changes
                    </button>
                </div>
            </form>
        </div>
    </div>

    @section('scripts')
    <script>
        $(document).ready(function() {
            $(document).on('click', '.quick-edit-btn', function(e) {
                e.preventDefault();
                const btn = $(this).closest('.quick-edit-btn');
                const rawData = btn.attr('data-product');
                let product = {};
                try {
                    product = typeof rawData === 'string' ? JSON.parse(rawData) : btn.data('product');
                } catch(err) {
                    product = btn.data('product');
                }
                openQuickEditModal(product);
            });
        });

        function openQuickEditModal(product) {
            $('#quick_edit_id').val(product.id);
            $('#quick_name').val(product.name);
            
            const isFeatured = product.is_featured == 1 || product.is_featured === true || product.is_featured === '1';
            const isFreeDelivery = product.is_free_delivery == 1 || product.is_free_delivery === true || product.is_free_delivery === '1';
            
            $('#quick_is_featured').prop('checked', isFeatured);
            $('#quick_is_free_delivery').prop('checked', isFreeDelivery);
            
            // Show Free Delivery ONLY if single product
            const pType = product.product_type || 'single';
            if (pType === 'single') {
                $('#quick_free_delivery_wrapper').removeClass('hidden').addClass('flex');
            } else {
                $('#quick_free_delivery_wrapper').addClass('hidden').removeClass('flex');
            }

            $('#quickEditModal').removeClass('hidden').addClass('flex');
            $('body').addClass('overflow-hidden');
        }

        function closeQuickEditModal() {
            $('#quickEditModal').addClass('hidden').removeClass('flex');
            $('body').removeClass('overflow-hidden');
        }

        function submitQuickEdit(event) {
            event.preventDefault();
            const id = $('#quick_edit_id').val();
            const name = $('#quick_name').val();
            const is_featured = $('#quick_is_featured').is(':checked');
            const is_free_delivery = $('#quick_is_free_delivery').is(':checked');

            const url = "{{ route('admin.products.quick-update', ':id') }}".replace(':id', id);
            
            $.ajax({
                url: url,
                method: 'POST',
                data: {
                    _token: '{{ csrf_token() }}',
                    name: name,
                    is_featured: is_featured ? 1 : 0,
                    is_free_delivery: is_free_delivery ? 1 : 0
                },
                success: function(response) {
                    if (response.success) {
                        toastr.success(response.message);
                        closeQuickEditModal();
                        location.reload(); 
                    }
                },
                error: function(xhr) {
                    let errors = xhr.responseJSON.errors;
                    if (errors) {
                        Object.values(errors).forEach(err => toastr.error(err[0]));
                    } else {
                        toastr.error('Something went wrong');
                    }
                }
            });
        }
    </script>
    @endsection
@endsection

