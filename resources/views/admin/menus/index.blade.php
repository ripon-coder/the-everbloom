@extends('admin.layouts.app')

@section('title', 'Menus')

@section('content')
<div class="p-4">
    <div class="flex justify-between items-center mb-4">
        <h1 class="text-xl font-semibold text-gray-800">Menus</h1>
        <a href="{{ route('admin.menus.create') }}"
            class="bg-blue-600 hover:bg-blue-700 text-white text-sm font-medium py-2 px-4 rounded transition">
            Add New Menu
        </a>
    </div>

    <div class="bg-white border border-gray-200 rounded shadow-sm overflow-hidden">
        <table class="min-w-full divide-y divide-gray-200">
            <thead class="bg-gray-50">
                <tr>
                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Order</th>
                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Name</th>
                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">URL</th>
                    <th class="px-6 py-3 text-center text-xs font-medium text-gray-500 uppercase">Status</th>
                    <th class="px-6 py-3 text-right text-xs font-medium text-gray-500 uppercase">Actions</th>
                </tr>
            </thead>
            <tbody class="bg-white divide-y divide-gray-200">
                @forelse($menus as $menu)
                    <tr>
                        <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-900">{{ $menu->order }}</td>
                        <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-900">{{ $menu->name }}</td>
                        <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500">{{ $menu->url }}</td>
                        <td class="px-6 py-4 whitespace-nowrap text-center">
                            <input type="checkbox" class="toggle-status" data-id="{{ $menu->id }}" {{ $menu->status ? 'checked' : '' }}>
                        </td>
                        <td class="px-6 py-4 whitespace-nowrap text-right text-sm">
                            <a href="{{ route('admin.menus.edit', $menu->id) }}" class="text-yellow-600 hover:underline mr-3">Edit</a>
                            <button onclick="showDeleteModal('menu', '{{ route('admin.menus.destroy', $menu->id) }}', '{{ $menu->name }}')" 
                                    class="text-red-600 hover:underline">Delete</button>
                        </td>
                    </tr>
                @empty
                    <tr>
                        <td colspan="5" class="px-6 py-4 text-center text-gray-500 text-sm">No menus found.</td>
                    </tr>
                @endforelse
            </tbody>
        </table>
    </div>

    @if($menus->hasPages())
        <div class="mt-4">
            {{ $menus->links() }}
        </div>
    @endif
</div>

@section('scripts')
<script>
$(document).ready(function() {
    $('.toggle-status').on('change', function() {
        let id = $(this).data('id');
        let url = "{{ route('admin.menus.toggle-status', ':id') }}".replace(':id', id);
        $.ajax({
            url: url,
            method: 'POST',
            data: { _token: '{{ csrf_token() }}' },
            success: function(response) {
                if(response.success) {
                    toastr.success('Status updated successfully');
                }
            },
            error: function() { 
                toastr.error('Error updating status'); 
            }
        });
    });
});
</script>
@endsection
@endsection
