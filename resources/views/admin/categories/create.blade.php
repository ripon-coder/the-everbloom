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
@endphp

@section('title', $isEdit ? 'Edit Category: ' . $category['name'] : 'Create Category')
@section('container_class', 'container-xxl')

@section('content')
<div class="row justify-content-center">
    <div class="col-lg-8">
        <form action="{{ $isEdit ? route('admin.categories.update', $category['slug']) : route('admin.categories.store') }}" method="POST" enctype="multipart/form-data">
            @csrf
            @if($isEdit)
                @method('PUT')
            @endif

            <div class="card">
                <div class="card-header">
                    <h4 class="card-title mb-0">{{ $isEdit ? 'Update Category' : 'Create Botanical Category' }}</h4>
                </div>
                <div class="card-body">
                    <div class="row g-3">
                        <!-- Category Name -->
                        <div class="col-md-6">
                            <label class="form-label" for="category-name">Category Name <span class="text-danger">*</span></label>
                            <input type="text" id="category-name" name="name" class="form-control" placeholder="e.g. Cleansers" value="{{ old('name', $category['name']) }}" required>
                        </div>

                        <!-- Short / Menu Name -->
                        <div class="col-md-6">
                            <label class="form-label" for="category-short-name">Display / Nav Short Name</label>
                            <input type="text" id="category-short-name" name="short_name" class="form-control" placeholder="e.g. Cleanser" value="{{ old('short_name', $category['short_name'] ?? '') }}">
                        </div>

                        <!-- Slug -->
                        <div class="col-md-6">
                            <label class="form-label" for="category-slug">URL Slug</label>
                            <input type="text" id="category-slug" name="slug" class="form-control" placeholder="e.g. cleansers" value="{{ old('slug', $category['slug']) }}">
                        </div>

                        <!-- Parent Category (3-Step Hierarchy) -->
                        <div class="col-md-6">
                            <label class="form-label" for="parent-category">Parent Category (Max 3 Steps)</label>
                            <select id="parent-category" name="parent_id" class="form-select">
                                <option value="">None (Step 1: Main Category)</option>
                                @foreach($parentCategories ?? [] as $pCat)
                                    <option value="{{ $pCat->id }}" {{ (string) old('parent_id', $category['parent_id'] ?? '') === (string) $pCat->id ? 'selected' : '' }}>
                                        Step {{ $pCat->level }}: {{ $pCat->hierarchy_path }}
                                    </option>
                                @endforeach
                            </select>
                            <small class="text-muted">Level 1 = Main, Level 2 = Subcategory, Level 3 = Child Category.</small>
                        </div>

                        <!-- Tagline -->
                        <div class="col-md-6">
                            <label class="form-label" for="category-tagline">Header Tagline</label>
                            <input type="text" id="category-tagline" name="tagline" class="form-control" placeholder="e.g. Gentle botanical purifying rituals" value="{{ old('tagline', $category['tagline'] ?? '') }}">
                        </div>

                        <!-- Sort Order -->
                        <div class="col-md-3">
                            <label class="form-label" for="category-sort-order">Sort Order</label>
                            <input type="number" min="0" id="category-sort-order" name="sort_order" class="form-control" value="{{ old('sort_order', $category['sort_order'] ?? 0) }}">
                        </div>

                        <!-- Status -->
                        <div class="col-md-3 d-flex align-items-end pb-2">
                            <div class="form-check form-switch">
                                <input type="hidden" name="is_active" value="0">
                                <input class="form-check-input" type="checkbox" role="switch" id="catActiveSwitch" name="is_active" value="1" {{ old('is_active', $category['is_active'] ?? true) ? 'checked' : '' }}>
                                <label class="form-check-label fw-semibold" for="catActiveSwitch">Active</label>
                            </div>
                        </div>

                        <!-- SEO Metadata -->
                        <div class="col-md-6">
                            <label class="form-label" for="category-meta-title">Meta Title</label>
                            <input type="text" id="category-meta-title" name="meta_title" class="form-control" value="{{ old('meta_title', $category['meta_title'] ?? '') }}">
                        </div>
                        <div class="col-md-6">
                            <label class="form-label" for="category-meta-description">Meta Description</label>
                            <input type="text" id="category-meta-description" name="meta_description" class="form-control" value="{{ old('meta_description', $category['meta_description'] ?? '') }}">
                        </div>

                        <!-- Editorial Description -->
                        <div class="col-12">
                            <label class="form-label" for="category-description">Category Editorial Description</label>
                            <textarea id="category-description" name="description" class="form-control" rows="3">{{ old('description', $category['description'] ?? '') }}</textarea>
                        </div>

                        <!-- Assigned Variant Attributes -->
                        <div class="col-12">
                            <label class="form-label d-block">Assigned Variant Attributes</label>
                            <div class="d-flex flex-wrap gap-3">
                                @php $assignedAttrIds = old('attribute_ids', $category['attribute_ids'] ?? []); @endphp
                                @foreach($attributes ?? [] as $attr)
                                    <div class="form-check">
                                        <input class="form-check-input" type="checkbox" name="attribute_ids[]" value="{{ $attr->id }}" id="cat_attr_{{ $attr->id }}" {{ in_array($attr->id, $assignedAttrIds) ? 'checked' : '' }}>
                                        <label class="form-check-label" for="cat_attr_{{ $attr->id }}">{{ $attr->name }}</label>
                                    </div>
                                @endforeach
                            </div>
                            <small class="text-muted">Only selected attributes (plus parent inherited attributes) appear when creating products in this category.</small>
                        </div>

                        <!-- Image path / upload -->
                        <div class="col-12">
                            <label class="form-label">Category Hero Artwork</label>
                            <div class="d-flex align-items-center gap-3">
                                <div class="avatar-xl rounded bg-light border overflow-hidden">
                                    <img src="{{ app(\App\Services\ImageStorageService::class)->resolveUrl($category['image'] ?? 'categories/face-care.jpg') }}" alt="" class="avatar-xl object-fit-cover">
                                </div>
                                <div class="flex-grow-1">
                                    <input type="file" name="image" class="form-control mb-1">
                                    <small class="text-muted">High-res vertical lifestyle photo (approx 800×1000px).</small>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
                <div class="card-footer bg-light-subtle d-flex justify-content-end gap-2">
                    <a href="{{ route('admin.categories.index') }}" class="btn btn-outline-secondary">Cancel</a>
                    <button type="submit" class="btn btn-primary">
                        <i class="bx bx-check me-1"></i> {{ $isEdit ? 'Save Changes' : 'Create Category' }}
                    </button>
                </div>
            </div>
        </form>
    </div>
</div>
@endsection
