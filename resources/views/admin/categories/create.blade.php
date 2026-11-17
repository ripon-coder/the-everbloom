@extends('layouts.admin')

@php
    $isEdit = $isEdit ?? false;
    $category = $category ?? [
        'name' => '',
        'slug' => '',
        'short_name' => '',
        'tagline' => '',
        'description' => '',
        'image' => 'categories/face-care.jpg',
    ];
    $resolvedImageUrl = app(\App\Services\ImageStorageService::class)->resolveUrl($category['image'] ?? 'categories/face-care.jpg');
@endphp

@section('title', $isEdit ? 'Edit Category: ' . $category['name'] : 'Create Category')
@section('container_class', 'container-xxl')

@section('content')
<form action="{{ $isEdit ? route('admin.categories.update', $category['slug']) : route('admin.categories.store') }}" method="POST" enctype="multipart/form-data">
    @csrf
    @if($isEdit)
        @method('PUT')
    @endif

    <div class="row">
        <!-- Left Sidebar: Artwork Preview & Hierarchy/Visibility -->
        <div class="col-xl-4 col-lg-5">
            <!-- Category Artwork Card -->
            <div class="card">
                <div class="card-header">
                    <h4 class="card-title mb-0">Category Hero Artwork</h4>
                </div>
                <div class="card-body">
                    <div class="rounded bg-light-subtle border border-dashed p-3 text-center mb-3">
                        <img
                            id="category-image-preview"
                            src="{{ $resolvedImageUrl }}"
                            alt="{{ $category['name'] ?: 'Category Artwork' }}"
                            class="img-fluid rounded object-fit-cover"
                            style="max-height: 200px;"
                        >
                    </div>
                    <label class="form-label" for="category-image-input">Select Artwork Image</label>
                    <input type="file" id="category-image-input" name="image" class="form-control" accept="image/*">
                    <small class="text-muted d-block mt-1">High-res vertical lifestyle photo (approx 800&times;1000px).</small>
                </div>
            </div>

            <!-- Hierarchy, Visibility & Ordering Card -->
            <div class="card">
                <div class="card-header">
                    <h4 class="card-title mb-0">Hierarchy &amp; Visibility</h4>
                </div>
                <div class="card-body">
                    <div class="mb-3">
                        <label class="form-label" for="parent-category">Parent Category (Max 3 Steps)</label>
                        <select id="parent-category" name="parent_id" class="form-select">
                            <option value="">None (Step 1: Main Category)</option>
                            @foreach($parentCategories ?? [] as $pCat)
                                <option value="{{ $pCat->id }}" {{ (string) old('parent_id', $category['parent_id'] ?? '') === (string) $pCat->id ? 'selected' : '' }}>
                                    Step {{ $pCat->level }}: {{ $pCat->hierarchy_path }}
                                </option>
                            @endforeach
                        </select>
                        <small class="text-muted d-block mt-1">Level 1 = Main, Level 2 = Subcategory, Level 3 = Child.</small>
                    </div>

                    <div class="mb-3">
                        <div class="form-check form-switch">
                            <input type="hidden" name="is_active" value="0">
                            <input class="form-check-input" type="checkbox" role="switch" id="catActiveSwitch" name="is_active" value="1" {{ old('is_active', $category['is_active'] ?? true) ? 'checked' : '' }}>
                            <label class="form-check-label fw-semibold" for="catActiveSwitch">Active on Storefront</label>
                        </div>
                        <small class="text-muted d-block mt-1">Inactive categories are hidden from storefront navigation.</small>
                    </div>

                    <div>
                        <label class="form-label" for="category-sort-order">Sort Order</label>
                        <input type="number" min="0" id="category-sort-order" name="sort_order" class="form-control" value="{{ old('sort_order', $category['sort_order'] ?? 0) }}">
                        <small class="text-muted d-block mt-1">Lower numbers appear first in category listings.</small>
                    </div>
                </div>
            </div>
        </div>

        <!-- Right Main Column: General Info, Attributes & SEO -->
        <div class="col-xl-8 col-lg-7">
            <!-- General Information Card -->
            <div class="card">
                <div class="card-header d-flex justify-content-between align-items-center">
                    <h4 class="card-title mb-0">{{ $isEdit ? 'Category Information' : 'General Information' }}</h4>
                    <a href="{{ route('admin.categories.index') }}" class="btn btn-sm btn-soft-secondary">
                        <i class="bx bx-arrow-back me-1"></i>Back to Categories
                    </a>
                </div>
                <div class="card-body">
                    <div class="row g-3">
                        <div class="col-md-6">
                            <label class="form-label" for="category-name">Category Name <span class="text-danger">*</span></label>
                            <input type="text" id="category-name" name="name" class="form-control" value="{{ old('name', $category['name']) }}" required>
                        </div>

                        <div class="col-md-6">
                            <label class="form-label" for="category-short-name">Display / Nav Short Name</label>
                            <input type="text" id="category-short-name" name="short_name" class="form-control" value="{{ old('short_name', $category['short_name'] ?? '') }}">
                        </div>

                        <div class="col-md-6">
                            <label class="form-label" for="category-slug">URL Slug</label>
                            <input type="text" id="category-slug" name="slug" class="form-control" value="{{ old('slug', $category['slug']) }}">
                        </div>

                        <div class="col-md-6">
                            <label class="form-label" for="category-tagline">Header Tagline</label>
                            <input type="text" id="category-tagline" name="tagline" class="form-control" value="{{ old('tagline', $category['tagline'] ?? '') }}">
                        </div>

                        <div class="col-12">
                            <label class="form-label" for="category-description">Category Editorial Description</label>
                            <textarea id="category-description" name="description" class="form-control" rows="4">{{ old('description', $category['description'] ?? '') }}</textarea>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Assigned Variant Attributes Card -->
            <div class="card">
                <div class="card-header">
                    <h4 class="card-title mb-0">Assigned Variant Attributes</h4>
                </div>
                <div class="card-body">
                    <div class="d-flex flex-wrap gap-3">
                        @php $assignedAttrIds = old('attribute_ids', $category['attribute_ids'] ?? []); @endphp
                        @forelse($attributes ?? [] as $attr)
                            <div class="form-check">
                                <input class="form-check-input" type="checkbox" name="attribute_ids[]" value="{{ $attr->id }}" id="cat_attr_{{ $attr->id }}" {{ in_array($attr->id, $assignedAttrIds) ? 'checked' : '' }}>
                                <label class="form-check-label" for="cat_attr_{{ $attr->id }}">{{ $attr->name }}</label>
                            </div>
                        @empty
                            <p class="text-muted mb-0 fs-13">No active attributes defined.</p>
                        @endforelse
                    </div>
                    <small class="text-muted d-block mt-2">Only selected attributes (plus parent inherited attributes) appear when creating products in this category.</small>
                </div>
            </div>

            <!-- SEO Metadata Card -->
            <div class="card">
                <div class="card-header">
                    <h4 class="card-title mb-0">SEO &amp; Search Metadata</h4>
                </div>
                <div class="card-body">
                    <div class="row g-3">
                        <div class="col-12">
                            <label class="form-label" for="category-meta-title">Meta Title</label>
                            <input type="text" id="category-meta-title" name="meta_title" class="form-control" value="{{ old('meta_title', $category['meta_title'] ?? '') }}">
                        </div>

                        <div class="col-12">
                            <label class="form-label" for="category-meta-description">Meta Description</label>
                            <textarea id="category-meta-description" name="meta_description" class="form-control" rows="2">{{ old('meta_description', $category['meta_description'] ?? '') }}</textarea>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Action Bar -->
            <div class="p-3 bg-light rounded d-flex justify-content-end gap-2">
                <a href="{{ route('admin.categories.index') }}" class="btn btn-outline-secondary">Cancel</a>
                <button type="submit" class="btn btn-primary">
                    <iconify-icon icon="solar:check-circle-bold" class="align-middle me-1"></iconify-icon>
                    {{ $isEdit ? 'Save Changes' : 'Create Category' }}
                </button>
            </div>
        </div>
    </div>
</form>
@endsection

@push('scripts')
<script>
document.getElementById('category-image-input')?.addEventListener('change', function (e) {
    const file = e.target.files?.[0];
    if (!file) return;
    const preview = document.getElementById('category-image-preview');
    if (preview) {
        preview.src = URL.createObjectURL(file);
    }
});
</script>
@endpush
