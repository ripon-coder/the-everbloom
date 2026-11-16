@extends('layouts.admin')

@section('title', 'Product Variant Attributes')

@section('content')
<div class="row mb-3">
    <div class="col-12">
        <h4 class="fw-bold mb-1">Product Variant Attributes</h4>
        <p class="text-muted mb-0 fs-13">Manage multi-attribute variant options (Brand, Size, Color / Vessel, Skin Type, etc.).</p>
    </div>
</div>

<div class="row">
    <div class="col-lg-4">
        <div class="card">
            <div class="card-header"><h5 class="card-title mb-0">Add Attribute</h5></div>
            <div class="card-body">
                <form action="{{ route('admin.attributes.store') }}" method="POST">
                    @csrf
                    <div class="mb-3">
                        <label class="form-label">Attribute Name *</label>
                        <input type="text" name="name" class="form-control" placeholder="e.g. Brand, Size, Color" required>
                    </div>
                    <div class="mb-3">
                        <label class="form-label">Display Type</label>
                        <select name="display_type" class="form-select">
                            <option value="button">Button Pills</option>
                            <option value="color">Color Swatch</option>
                            <option value="select">Dropdown</option>
                        </select>
                    </div>
                    <div class="mb-3">
                        <label class="form-label">Assign Categories (Optional)</label>
                        <select name="category_ids[]" class="form-select" multiple size="5">
                            @foreach($categories ?? [] as $cat)
                                <option value="{{ $cat->id }}">{{ $cat->hierarchy_path }}</option>
                            @endforeach
                        </select>
                        <small class="text-muted">Leave empty for global availability.</small>
                    </div>
                    <button type="submit" class="btn btn-primary w-100">Create Attribute</button>
                </form>
            </div>
        </div>
    </div>

    <div class="col-lg-8">
        @foreach($attributes as $attr)
            <div class="card mb-3">
                <div class="card-header d-flex flex-wrap justify-content-between align-items-center gap-2">
                    <form action="{{ route('admin.attributes.update', $attr) }}" method="POST" class="d-flex flex-wrap gap-2 align-items-center">
                        @csrf
                        @method('PUT')
                        <input type="hidden" name="sync_categories" value="1">
                        <input type="text" name="name" value="{{ $attr->name }}" class="form-control form-control-sm fw-bold" style="width:180px;">
                        <select name="category_ids[]" class="form-select form-select-sm" multiple size="1" style="min-width:220px;" title="Assigned categories (hold Ctrl to multi-select)">
                            @foreach($categories ?? [] as $cat)
                                <option value="{{ $cat->id }}" {{ $attr->categories->contains('id', $cat->id) ? 'selected' : '' }}>{{ $cat->hierarchy_path }}</option>
                            @endforeach
                        </select>
                        <button type="submit" class="btn btn-sm btn-soft-primary">Save</button>
                    </form>
                    <div class="d-flex align-items-center gap-1 flex-wrap">
                        @forelse($attr->categories as $assignedCat)
                            <span class="badge bg-primary-subtle text-primary fs-11">{{ $assignedCat->hierarchy_path }}</span>
                        @empty
                            <span class="badge bg-secondary-subtle text-secondary fs-11">Global (All Categories)</span>
                        @endforelse
                        <span class="badge bg-light text-muted">{{ $attr->values->count() }} values</span>
                    </div>
                </div>
                <div class="card-body">
                    <div class="d-flex flex-wrap gap-2 mb-3">
                        @foreach($attr->values as $val)
                            <div class="d-inline-flex align-items-center gap-1 border rounded px-2 py-1 bg-light-subtle">
                                <span class="fs-13">{{ $val->value }}</span>
                                <form action="{{ route('admin.attributes.values.destroy', $val) }}" method="POST" class="d-inline">
                                    @csrf
                                    @method('DELETE')
                                    <button type="submit" class="btn btn-link text-danger p-0 ms-1 fs-12" title="Remove">&times;</button>
                                </form>
                            </div>
                        @endforeach
                    </div>
                    <form action="{{ route('admin.attributes.values.store', $attr) }}" method="POST" class="d-flex gap-2">
                        @csrf
                        <input type="text" name="value" class="form-control form-control-sm" placeholder="Add new {{ $attr->name }} value..." required>
                        <button type="submit" class="btn btn-sm btn-outline-primary">Add Value</button>
                    </form>
                </div>
            </div>
        @endforeach
    </div>
</div>
@endsection

