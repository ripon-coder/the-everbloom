@foreach ($categories as $category)
    <tr>
        <td class="cell">{{ $category->id }}</td>
        <td class="cell">{{ $prefix . $category->name }}</td>
        <td class="cell">
            @isset($category->thumbnail)
                <img src="{{ asset('dynamic-assets/category/' . $category->thumbnail) }}" width="50">
            @endisset
        </td>
        <td class="cell">{{ $category->created_at }}</td>
        <td class="cell">
            <span class="badge {{ $category->status->badgeClass() }}">
                {{ $category->status->label() }}
            </span>
        </td>
        <td class="cell">
            <a class="btn-sm app-btn-secondary" href="{{ route('admin.category.edit', $category) }}">Edit</a>
            <a onclick="if(confirm('Are you sure want to delete?')) document.getElementById('delete_{{ $category->id }}').submit()" class="btn-sm app-btn-danger" href="#">Delete</a>
            <form id="delete_{{ $category->id }}" class="d-none" action="{{ route('admin.category.destroy', $category) }}" method="POST">
                @csrf @method('DELETE')
            </form>
        </td>
    </tr>

    @if ($category->children && $category->children->count())
        @include('components.category-row', [
            'categories' => $category->children,
            'prefix' => $prefix . '— ',
        ])
    @endif
@endforeach
