@foreach($categories as $cat)
    <option value="{{ $cat->id }}" @selected(old('parent_id', $selectedParentId ?? null) == $cat->id)>
        {{ $prefix }}{{ $cat->name }}
    </option>
    @if($cat->children && $cat->children->count())
        @include('components.category-select', [
            'categories' => $cat->children,
            'prefix' => $prefix . '— ',
            'selectedParentId' => $selectedParentId ?? null
        ])
    @endif
@endforeach
