@extends('layouts.admin')

@section('title', 'Edit CMS Page | The Everbloom Admin')

@section('content')
<div class="row">
    <div class="col-12">
        <div class="page-title-box">
            <h4 class="page-title">Edit Page: {{ $page->title }}</h4>
            <div class="page-title-right">
                <a href="{{ route('admin.cms.pages') }}" class="btn btn-soft-secondary btn-sm">Back to Pages</a>
            </div>
        </div>
    </div>
</div>

<div class="card">
    <div class="card-body">
        <form method="POST" action="{{ route('admin.cms.pages.update', $page) }}">
            @csrf @method('PUT')
            <div class="row g-3">
                <div class="col-md-6">
                    <label class="form-label">Title</label>
                    <input type="text" name="title" class="form-control" value="{{ old('title', $page->title) }}" required>
                </div>
                <div class="col-md-6">
                    <label class="form-label">Slug</label>
                    <input type="text" name="slug" class="form-control" value="{{ old('slug', $page->slug) }}" required>
                </div>
                <div class="col-12">
                    <label class="form-label">Content</label>
                    <textarea name="content" class="form-control" rows="10">{{ old('content', $page->content) }}</textarea>
                </div>
                <div class="col-md-6">
                    <label class="form-label">Meta Title</label>
                    <input type="text" name="meta_title" class="form-control" value="{{ old('meta_title', $page->meta_title) }}">
                </div>
                <div class="col-md-6">
                    <label class="form-label">Meta Description</label>
                    <input type="text" name="meta_description" class="form-control" value="{{ old('meta_description', $page->meta_description) }}">
                </div>
                <div class="col-12">
                    <div class="form-check form-switch">
                        <input type="hidden" name="is_active" value="0">
                        <input class="form-check-input" type="checkbox" name="is_active" value="1" {{ old('is_active', $page->is_active) ? 'checked' : '' }} id="edit_active">
                        <label class="form-check-label" for="edit_active">Published</label>
                    </div>
                </div>
            </div>
            <div class="mt-3">
                <button type="submit" class="btn btn-primary">Update Page</button>
            </div>
        </form>
    </div>
</div>
@endsection

