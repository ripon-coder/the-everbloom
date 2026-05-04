@extends('admin.layouts.app')

@section('title', 'Manage Categories')

@section('content')
    <div class="bg-white dark:bg-gray-800 shadow-md rounded-lg">
        <div class="px-6 py-4 border-b border-gray-200 dark:border-gray-700 flex justify-between items-center">
            <h2 class="text-xl font-semibold text-gray-800 dark:text-white">Category List</h2>
            <a href="{{ route('admin.categories.create') }}"
                class="text-white bg-blue-700 hover:bg-blue-800 focus:ring-4 focus:ring-blue-300 font-medium rounded-lg text-sm px-5 py-2.5 dark:bg-blue-600 dark:hover:bg-blue-700 focus:outline-none dark:focus:ring-blue-800">
                Add New Category
            </a>
        </div>
        <div class="overflow-x-auto">
            <table class="w-full text-sm text-left text-gray-500 dark:text-gray-400">
                <thead class="text-xs text-gray-700 uppercase bg-gray-50 dark:bg-gray-700 dark:text-gray-400">
                    <tr>
                        <th scope="col" class="px-6 py-3">ID</th>
                        <th scope="col" class="px-6 py-3">Image</th>
                        <th scope="col" class="px-6 py-3">Name</th>
                        <th scope="col" class="px-6 py-3">Slug</th>
                        <th scope="col" class="px-6 py-3">Parent</th>
                        <th scope="col" class="px-6 py-3 text-center">Featured</th>
                        <th scope="col" class="px-6 py-3">Status</th>
                        <th scope="col" class="px-6 py-3">Created At</th>
                        <th scope="col" class="px-6 py-3 text-right">Actions</th>
                    </tr>
                </thead>

                <tbody>
                    @forelse($categories as $category)
                        <tr
                            class="bg-white border-b dark:bg-gray-800 dark:border-gray-700 hover:bg-gray-50 dark:hover:bg-gray-600">
                            <th scope="row"
                                class="px-6 py-4 font-medium text-gray-900 whitespace-nowrap dark:text-white">
                                {{ $category->id }}
                            </th>
                            <td class="px-6 py-4">
                                <img src="{{ $category->getImageUrl('category_image') }}" alt="{{ $category->name }}"
                                    class="w-10 h-10 rounded-full object-cover">
                            </td>
                            <td class="px-6 py-4 font-medium text-gray-900 dark:text-white">
                                {{ $category->name }}
                            </td>
                            <td class="px-6 py-4">
                                <span
                                    class="font-mono text-xs bg-gray-100 dark:bg-gray-700 px-2 py-1 rounded">{{ $category->slug }}</span>
                            </td>
                            <td class="px-6 py-4">
                                {{ $category->parent ? $category->parent->name : 'Root' }}
                            </td>
                            <td class="px-6 py-4 text-center">
                                <button onclick="toggleFeatured({{ $category->id }}, this)" class="focus:outline-none transition-transform duration-200 hover:scale-110">
                                    @if ($category->is_featured)
                                        <span class="text-yellow-400 featured-icon" title="Featured Category">
                                            <svg class="w-5 h-5 mx-auto" fill="currentColor" viewBox="0 0 20 20">
                                                <path d="M9.049 2.927c.3-.921 1.603-.921 1.902 0l1.07 3.292a1 1 0 00.95.69h3.462c.969 0 1.371 1.24.588 1.81l-2.8 2.034a1 1 0 00-.364 1.118l1.07 3.292c.3.921-.755 1.688-1.54 1.118l-2.8-2.034a1 1 0 00-1.175 0l-2.8 2.034c-.784.57-1.838-.197-1.539-1.118l1.07-3.292a1 1 0 00-.364-1.118L2.98 8.72c-.783-.57-.38-1.81.588-1.81h3.461a1 1 0 00.951-.69l1.07-3.292z" />
                                            </svg>
                                        </span>
                                    @else
                                        <span class="text-gray-300 dark:text-gray-600 featured-icon" title="Not Featured">
                                            <svg class="w-5 h-5 mx-auto" fill="currentColor" viewBox="0 0 20 20">
                                                <path d="M9.049 2.927c.3-.921 1.603-.921 1.902 0l1.07 3.292a1 1 0 00.95.69h3.462c.969 0 1.371 1.24.588 1.81l-2.8 2.034a1 1 0 00-.364 1.118l1.07 3.292c.3.921-.755 1.688-1.54 1.118l-2.8-2.034a1 1 0 00-1.175 0l-2.8 2.034c-.784.57-1.838-.197-1.539-1.118l1.07-3.292a1 1 0 00-.364-1.118L2.98 8.72c-.783-.57-.38-1.81.588-1.81h3.461a1 1 0 00.951-.69l1.07-3.292z" />
                                            </svg>
                                        </span>
                                    @endif
                                </button>
                            </td>
                            <td class="px-6 py-4">
                                @if ($category->status == \App\Constants\CategoryStatus::ACTIVE)
                                    <span
                                        class="bg-green-100 text-green-800 text-xs font-medium me-2 px-2.5 py-0.5 rounded dark:bg-green-900 dark:text-green-300">Active</span>
                                @else
                                    <span
                                        class="bg-gray-100 text-gray-800 text-xs font-medium me-2 px-2.5 py-0.5 rounded dark:bg-gray-700 dark:text-gray-300">Inactive</span>
                                @endif
                            </td>
                            <td class="px-6 py-4">
                                {{ $category->created_at->format('Y-m-d H:i') }}
                            </td>
                            <td class="px-6 py-4 text-right">
                                <div class="flex justify-end space-x-2">
                                    <a href="{{ route('admin.categories.edit', $category->id) }}"
                                        class="font-medium text-blue-600 dark:text-blue-500 hover:underline">Edit</a>
                                    <button
                                        onclick="showDeleteModal('category', '{{ route('admin.categories.destroy', $category->id) }}', '{{ $category->name }}')"
                                        class="font-medium text-red-600 dark:text-red-500 hover:underline">Delete</button>
                                </div>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="9" class="px-6 py-8 text-center text-gray-500 dark:text-gray-400">
                                <div class="flex flex-col items-center justify-center space-y-3">
                                    <svg class="w-12 h-12 text-gray-400" fill="none" stroke="currentColor"
                                        viewBox="0 0 24 24" xmlns="http://www.w3.org/2000/svg">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                            d="M19 11H5m14 0a2 2 0 012 2v6a2 2 0 01-2 2H5a2 2 0 01-2-2v-6a2 2 0 012-2m14 0V9a2 2 0 00-2-2M5 11V9a2 2 0 012-2m0 0V5a2 2 0 012-2h6a2 2 0 012 2v2M7 7h10">
                                        </path>
                                    </svg>
                                    <p class="text-lg font-medium">No categories found</p>
                                    <p class="text-sm">Get started by creating your first category.</p>
                                    <a href="{{ route('admin.categories.create') }}"
                                        class="text-white bg-blue-700 hover:bg-blue-800 focus:ring-4 focus:ring-blue-300 font-medium rounded-lg text-sm px-5 py-2.5 dark:bg-blue-600 dark:hover:bg-blue-700 focus:outline-none dark:focus:ring-blue-800">
                                        Create Category
                                    </a>
                                </div>
                            </td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
        @if ($categories->count() > 0)
            <div class="px-6 py-4 border-t border-gray-200 dark:border-gray-700 flex justify-center items-center">
                {{ $categories->links() }}
            </div>
        @endif
    </div>

    @push('scripts')
    <script>
        function toggleFeatured(categoryId, button) {
            const iconContainer = button.querySelector('.featured-icon');
            
            fetch(`{{ url('admin/categories') }}/${categoryId}/toggle-featured`, {
                method: 'POST',
                headers: {
                    'X-CSRF-TOKEN': '{{ csrf_token() }}',
                    'Content-Type': 'application/json',
                    'Accept': 'application/json'
                }
            })
            .then(response => response.json())
            .then(data => {
                if (data.success) {
                    if (data.is_featured) {
                        iconContainer.classList.remove('text-gray-300', 'dark:text-gray-600');
                        iconContainer.classList.add('text-yellow-400');
                        iconContainer.title = "Featured Category";
                    } else {
                        iconContainer.classList.remove('text-yellow-400');
                        iconContainer.classList.add('text-gray-300', 'dark:text-gray-600');
                        iconContainer.title = "Not Featured";
                    }
                    toastr.success(data.message);
                }
            })
            .catch(error => {
                console.error('Error:', error);
                toastr.error('Something went wrong!');
            });
        }
    </script>
    @endpush
@endsection
