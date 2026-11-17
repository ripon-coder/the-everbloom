@extends('layouts.admin')

@php
    $isEdit = $isEdit ?? false;
    $brand = $brand ?? null;
@endphp

@section('title', $isEdit ? 'Edit Brand: ' . $brand->name : 'Create Brand')
@section('container_class', 'container-xxl')

@section('content')
<div class="row justify-content-center">
    <div class="col-lg-8">
        <form action="{{ $isEdit ? route('admin.brands.update', $brand->id) : route('admin.brands.store') }}" method="POST" enctype="multipart/form-data">
            @csrf
            @if($isEdit)
                @method('PUT')
            @endif

            <div class="card">
                <div class="card-header">
                    <h4 class="card-title mb-0">{{ $isEdit ? 'Update Brand' : 'Create Brand' }}</h4>
                </div>
                <div class="card-body">
                    <div class="row g-3">
                        <div class="col-md-6">
                            <label class="form-label" for="brand-name">Brand Name <span class="text-danger">*</span></label>
                            <input type="text" id="brand-name" name="name" class="form-control" value="{{ old('name', $brand?->name ?? '') }}" required>
                        </div>

                        <div class="col-md-6">
                            <label class="form-label" for="brand-slug">URL Slug</label>
                            <input type="text" id="brand-slug" name="slug" class="form-control" value="{{ old('slug', $brand?->slug ?? '') }}" placeholder="auto-generated-from-name">
                        </div>

                        <div class="col-md-6">
                            <label class="form-label" for="brand-sort">Sort Order</label>
                            <input type="number" min="0" id="brand-sort" name="sort_order" class="form-control" value="{{ old('sort_order', $brand?->sort_order ?? 0) }}">
                        </div>

                        <div class="col-md-6 d-flex align-items-end pb-2">
                            <div class="form-check form-switch">
                                <input type="hidden" name="is_active" value="0">
                                <input class="form-check-input" type="checkbox" role="switch" id="brandActiveSwitch" name="is_active" value="1" {{ old('is_active', $brand?->is_active ?? true) ? 'checked' : '' }}>
                                <label class="form-check-label fw-semibold" for="brandActiveSwitch">Active</label>
                            </div>
                        </div>

                        <div class="col-12">
                            <label class="form-label" for="brand-description">Description</label>
                            <textarea id="brand-description" name="description" class="form-control" rows="3">{{ old('description', $brand?->description ?? '') }}</textarea>
                        </div>

                        <div class="col-12">
                            <label class="form-label" for="brand-logo">Brand Logo</label>
                            <div class="d-flex align-items-center gap-3">
                                @if($brand?->logo)
                                    <img src="{{ $brand->resolvedLogo() }}" alt="{{ $brand->name }}" class="avatar-xl rounded object-fit-cover border">
                                @endif
                                <input type="file" id="brand-logo" name="logo" class="form-control" accept="image/*">
                            </div>
                        </div>

                        <div class="col-md-6">
                            <label class="form-label" for="brand-meta-title">Meta Title</label>
                            <input type="text" id="brand-meta-title" name="meta_title" class="form-control" value="{{ old('meta_title', $brand?->meta_title ?? '') }}">
                        </div>

                        <div class="col-md-6">
                            <label class="form-label" for="brand-meta-description">Meta Description</label>
                            <input type="text" id="brand-meta-description" name="meta_description" class="form-control" value="{{ old('meta_description', $brand?->meta_description ?? '') }}">
                        </div>
                    </div>
                </div>
                <div class="card-footer bg-light-subtle d-flex justify-content-end gap-2">
                    <a href="{{ route('admin.brands.index') }}" class="btn btn-outline-secondary">Cancel</a>
                    <button type="submit" class="btn btn-primary">{{ $isEdit ? 'Save Changes' : 'Create Brand' }}</button>
                </div>
            </div>
        </form>
    </div>
</div>
@endsection

