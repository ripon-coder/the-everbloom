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
        <form action="{{ $isEdit ? route('admin.categories.update', $category['slug']) : route('admin.categories.store') }}" method="POST">
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

                        <!-- Tagline -->
                        <div class="col-md-6">
                            <label class="form-label" for="category-tagline">Header Tagline</label>
                            <input type="text" id="category-tagline" name="tagline" class="form-control" placeholder="e.g. Gentle botanical purifying rituals" value="{{ old('tagline', $category['tagline'] ?? '') }}">
                        </div>

                        <!-- Editorial Description -->
                        <div class="col-12">
                            <label class="form-label" for="category-description">Category Editorial Description</label>
                            <textarea id="category-description" name="description" class="form-control" rows="3">{{ old('description', $category['description'] ?? '') }}</textarea>
                        </div>

                        <!-- Image path / upload -->
                        <div class="col-12">
                            <label class="form-label">Category Hero Artwork</label>
                            <div class="d-flex align-items-center gap-3">
                                <div class="avatar-xl rounded bg-light border overflow-hidden">
                                    <img src="{{ asset('images/' . ($category['image'] ?? 'categories/face-care.jpg')) }}" alt="" class="avatar-xl object-fit-cover">
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
