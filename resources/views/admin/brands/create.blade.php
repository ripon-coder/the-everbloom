@extends('layouts.admin')

@php
    $isEdit = $isEdit ?? false;
    $brand = $brand ?? null;
@endphp

@section('title', $isEdit ? 'Edit Brand: ' . $brand->name : 'Create Brand')
@section('container_class', 'container-xxl')

@section('content')
<form action="{{ $isEdit ? route('admin.brands.update', $brand->id) : route('admin.brands.store') }}" method="POST" enctype="multipart/form-data">
    @csrf
    @if($isEdit)
        @method('PUT')
    @endif

    <div class="row">
        <!-- Left Sidebar: Logo Preview & Visibility -->
        <div class="col-xl-4 col-lg-5">
            <!-- Brand Logo Card -->
            <div class="card">
                <div class="card-header">
                    <h4 class="card-title mb-0">Brand Logo</h4>
                </div>
                <div class="card-body">
                    <div class="rounded bg-light-subtle border border-dashed p-4 text-center mb-3">
                        <img
                            id="brand-logo-preview"
                            src="{{ $brand?->logo ? $brand->resolvedLogo() : '' }}"
                            alt="{{ $brand?->name ?? 'Brand Logo' }}"
                            class="img-fluid rounded object-fit-contain {{ $brand?->logo ? '' : 'd-none' }}"
                            style="max-height: 150px;"
                        >
                        <div id="brand-logo-placeholder" class="{{ $brand?->logo ? 'd-none' : '' }}">
                            <iconify-icon icon="solar:gallery-add-bold-duotone" class="fs-48 text-primary"></iconify-icon>
                            <p class="text-muted fs-13 mt-2 mb-0">Upload a brand logo</p>
                        </div>
                    </div>
                    <label class="form-label" for="brand-logo">Select Logo Image</label>
                    <input type="file" id="brand-logo" name="logo" class="form-control" accept="image/*">
                    <small class="text-muted d-block mt-1">Recommended: PNG or WebP with transparent background.</small>
                </div>
            </div>

            <!-- Visibility & Ordering Card -->
            <div class="card">
                <div class="card-header">
                    <h4 class="card-title mb-0">Visibility &amp; Ordering</h4>
                </div>
                <div class="card-body">
                    <div class="mb-3">
                        <div class="form-check form-switch">
                            <input type="hidden" name="is_active" value="0">
                            <input class="form-check-input" type="checkbox" role="switch" id="brandActiveSwitch" name="is_active" value="1" {{ old('is_active', $brand?->is_active ?? true) ? 'checked' : '' }}>
                            <label class="form-check-label fw-semibold" for="brandActiveSwitch">Active on Storefront</label>
                        </div>
                        <small class="text-muted d-block mt-1">Inactive brands are hidden from storefront filters.</small>
                    </div>

                    <div>
                        <label class="form-label" for="brand-sort">Sort Order</label>
                        <input type="number" min="0" id="brand-sort" name="sort_order" class="form-control" value="{{ old('sort_order', $brand?->sort_order ?? 0) }}">
                        <small class="text-muted d-block mt-1">Lower numbers appear first in brand listings.</small>
                    </div>
                </div>
            </div>
        </div>

        <!-- Right Main Column: General Info & SEO -->
        <div class="col-xl-8 col-lg-7">
            <!-- General Information Card -->
            <div class="card">
                <div class="card-header d-flex justify-content-between align-items-center">
                    <h4 class="card-title mb-0">{{ $isEdit ? 'Brand Information' : 'General Information' }}</h4>
                    <a href="{{ route('admin.brands.index') }}" class="btn btn-sm btn-soft-secondary">
                        <i class="bx bx-arrow-back me-1"></i>Back to Brands
                    </a>
                </div>
                <div class="card-body">
                    <div class="row g-3">
                        <div class="col-md-6">
                            <label class="form-label" for="brand-name">Brand Name <span class="text-danger">*</span></label>
                            <input type="text" id="brand-name" name="name" class="form-control" value="{{ old('name', $brand?->name ?? '') }}" required>
                        </div>

                        <div class="col-md-6">
                            <label class="form-label" for="brand-slug">URL Slug</label>
                            <input type="text" id="brand-slug" name="slug" class="form-control" value="{{ old('slug', $brand?->slug ?? '') }}">
                        </div>

                        <div class="col-12">
                            <label class="form-label" for="brand-description">Description</label>
                            <textarea id="brand-description" name="description" class="form-control" rows="4">{{ old('description', $brand?->description ?? '') }}</textarea>
                        </div>
                    </div>
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
                            <label class="form-label" for="brand-meta-title">Meta Title</label>
                            <input type="text" id="brand-meta-title" name="meta_title" class="form-control" value="{{ old('meta_title', $brand?->meta_title ?? '') }}">
                        </div>

                        <div class="col-12">
                            <label class="form-label" for="brand-meta-description">Meta Description</label>
                            <textarea id="brand-meta-description" name="meta_description" class="form-control" rows="2">{{ old('meta_description', $brand?->meta_description ?? '') }}</textarea>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Action Bar -->
            <div class="p-3 bg-light rounded d-flex justify-content-end gap-2">
                <a href="{{ route('admin.brands.index') }}" class="btn btn-outline-secondary">Cancel</a>
                <button type="submit" class="btn btn-primary">
                    <iconify-icon icon="solar:check-circle-bold" class="align-middle me-1"></iconify-icon>
                    {{ $isEdit ? 'Save Changes' : 'Create Brand' }}
                </button>
            </div>
        </div>
    </div>
</form>
@endsection

@push('scripts')
<script>
document.getElementById('brand-logo')?.addEventListener('change', function (e) {
    const file = e.target.files?.[0];
    if (!file) return;
    const preview = document.getElementById('brand-logo-preview');
    const placeholder = document.getElementById('brand-logo-placeholder');
    if (preview) {
        preview.src = URL.createObjectURL(file);
        preview.classList.remove('d-none');
    }
    if (placeholder) {
        placeholder.classList.add('d-none');
    }
});
</script>
@endpush
