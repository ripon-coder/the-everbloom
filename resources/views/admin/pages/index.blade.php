@extends('admin.layouts.app')

@section('title', 'Pages')

@section('content')
<div class="p-4">
    <div class="flex justify-between items-center mb-4">
        <h1 class="text-xl font-semibold text-gray-800">Pages</h1>
        <a href="{{ route('admin.pages.create') }}"
            class="bg-blue-600 hover:bg-blue-700 text-white text-sm font-medium py-2 px-4 rounded transition">
            Add New Page
        </a>
    </div>

    <div class="bg-white border border-gray-200 rounded shadow-sm overflow-hidden">
        <table class="min-w-full divide-y divide-gray-200">
            <thead class="bg-gray-50">
                <tr>
                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Title</th>
                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Slug</th>
                    <th class="px-6 py-3 text-center text-xs font-medium text-gray-500 uppercase">Status</th>
                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Date</th>
                    <th class="px-6 py-3 text-right text-xs font-medium text-gray-500 uppercase">Actions</th>
                </tr>
            </thead>
            <tbody class="bg-white divide-y divide-gray-200">
                @forelse($pages as $page)
                    <tr>
                        <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-900">{{ $page->title }}</td>
                        <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500">{{ $page->slug }}</td>
                        <td class="px-6 py-4 whitespace-nowrap text-center">
                            <input type="checkbox" class="toggle-status" data-id="{{ $page->id }}" {{ $page->is_active ? 'checked' : '' }}>
                        </td>
                        <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500">
                            {{ $page->updated_at->format('Y-m-d') }}
                        </td>
                        <td class="px-6 py-4 whitespace-nowrap text-right text-sm">
                            <a href="{{ url($page->slug) }}" target="_blank" class="text-blue-600 hover:underline mr-3">View</a>
                            <a href="{{ route('admin.pages.edit', $page->id) }}" class="text-yellow-600 hover:underline mr-3">Edit</a>
                            <button onclick="showDeleteModal('page', '{{ route('admin.pages.destroy', $page->id) }}', '{{ $page->title }}')" 
                                    class="text-red-600 hover:underline">Delete</button>
                        </td>
                    </tr>
                @empty
                    <tr>
                        <td colspan="5" class="px-6 py-4 text-center text-gray-500 text-sm">No pages found.</td>
                    </tr>
                @endforelse
            </tbody>
        </table>
    </div>

    @if($pages->hasPages())
        <div class="mt-4">
            {{ $pages->links() }}
        </div>
    @endif
</div>

@section('scripts')
<script>
$(document).ready(function() {
    $('.toggle-status').on('change', function() {
        let id = $(this).data('id');
        let url = "{{ route('admin.pages.toggle-status', ':id') }}".replace(':id', id);
        $.ajax({
            url: url,
            method: 'POST',
            data: { _token: '{{ csrf_token() }}' },
            error: function() { alert('Error updating status'); }
        });
    });
});
</script>
@endsection
@endsection
