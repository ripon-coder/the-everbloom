@extends('layouts.admin')

@section('title', ($isEdit ? 'Edit Flash Sale' : 'Create Flash Sale') . ' | The Everbloom Admin')

@section('content')
<div class="row">
    <div class="col-12">
        <div class="page-title-box">
            <h4 class="page-title">{{ $isEdit ? 'Edit Flash Sale: ' . $flashSale->title : 'Create Flash Sale' }}</h4>
            <div class="page-title-right">
                <a href="{{ route('admin.flash-sales.index') }}" class="btn btn-soft-secondary btn-sm">Back to List</a>
            </div>
        </div>
    </div>
</div>

<div class="card">
    <div class="card-body">
        <form method="POST" action="{{ $isEdit ? route('admin.flash-sales.update', $flashSale) : route('admin.flash-sales.store') }}" enctype="multipart/form-data">
            @csrf
            @if($isEdit)
                @method('PUT')
            @endif

            <div class="row g-3">
                <div class="col-md-6">
                    <label class="form-label">Campaign Title <span class="text-danger">*</span></label>
                    <input type="text" name="title" class="form-control" value="{{ old('title', $flashSale->title) }}" required>
                </div>

                <div class="col-md-6">
                    <label class="form-label">Slug (optional)</label>
                    <input type="text" name="slug" class="form-control" value="{{ old('slug', $flashSale->slug) }}" placeholder="auto-generated-from-title">
                </div>

                <div class="col-12">
                    <label class="form-label">Description</label>
                    <textarea name="description" class="form-control" rows="3">{{ old('description', $flashSale->description) }}</textarea>
                </div>

                <div class="col-md-4">
                    <label class="form-label">Starts At <span class="text-danger">*</span></label>
                    <input type="datetime-local" name="starts_at" class="form-control" value="{{ old('starts_at', $flashSale->starts_at?->format('Y-m-d\TH:i')) }}" required>
                </div>

                <div class="col-md-4">
                    <label class="form-label">Ends At <span class="text-danger">*</span></label>
                    <input type="datetime-local" name="ends_at" class="form-control" value="{{ old('ends_at', $flashSale->ends_at?->format('Y-m-d\TH:i')) }}" required>
                </div>

                <div class="col-md-4">
                    <label class="form-label">Status</label>
                    <select name="status" class="form-select">
                        @foreach(['draft' => 'Draft', 'scheduled' => 'Scheduled', 'active' => 'Active', 'disabled' => 'Disabled'] as $val => $label)
                            <option value="{{ $val }}" {{ old('status', $flashSale->status) === $val ? 'selected' : '' }}>{{ $label }}</option>
                        @endforeach
                    </select>
                </div>

                <div class="col-md-6">
                    <label class="form-label">Banner Image (WebP)</label>
                    <input type="file" name="banner_image" class="form-control" accept="image/*">
                    @if($flashSale->banner_image)
                        <img src="{{ $flashSale->resolvedBannerImage() }}" alt="Banner" class="mt-2 rounded" style="max-height:90px;">
                    @endif
                </div>

                <div class="col-md-6 d-flex align-items-center">
                    <div class="form-check form-switch mt-3">
                        <input type="hidden" name="show_on_homepage" value="0">
                        <input class="form-check-input" type="checkbox" id="show_on_homepage" name="show_on_homepage" value="1" {{ old('show_on_homepage', $flashSale->show_on_homepage) ? 'checked' : '' }}>
                        <label class="form-check-label" for="show_on_homepage">Show on Homepage Flash Sale Section</label>
                    </div>
                </div>
            </div>

            <div class="mt-4 d-flex gap-2">
                <button type="submit" class="btn btn-primary">{{ $isEdit ? 'Update Flash Sale' : 'Create & Assign Products' }}</button>
                @if($isEdit)
                    <a href="{{ route('admin.flash-sales.products', $flashSale) }}" class="btn btn-soft-info">Manage Products</a>
                @endif
            </div>
        </form>
    </div>
</div>
@endsection

