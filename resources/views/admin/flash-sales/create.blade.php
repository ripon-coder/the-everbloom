@extends('layouts.admin')

@section('title', $isEdit ? 'Edit Flash Sale: ' . $flashSale->title : 'Create Flash Sale')
@section('container_class', 'container-xxl')

@section('content')
<form method="POST" action="{{ $isEdit ? route('admin.flash-sales.update', $flashSale) : route('admin.flash-sales.store') }}" enctype="multipart/form-data">
    @csrf
    @if($isEdit)
        @method('PUT')
    @endif

    <div class="row">
        <!-- Left Sidebar: Banner Preview & Status -->
        <div class="col-xl-4 col-lg-5">
            <!-- Banner Image Card -->
            <div class="card">
                <div class="card-header">
                    <h4 class="card-title mb-0">Campaign Banner</h4>
                </div>
                <div class="card-body">
                    <div class="rounded bg-light-subtle border border-dashed p-3 text-center mb-3">
                        <img
                            id="flash-banner-preview"
                            src="{{ $flashSale->banner_image ? $flashSale->resolvedBannerImage() : '' }}"
                            alt="{{ $flashSale->title ?: 'Flash Sale Banner' }}"
                            class="img-fluid rounded object-fit-cover {{ $flashSale->banner_image ? '' : 'd-none' }}"
                            style="max-height: 170px;"
                        >
                        <div id="flash-banner-placeholder" class="{{ $flashSale->banner_image ? 'd-none' : '' }}">
                            <iconify-icon icon="solar:gallery-add-bold-duotone" class="fs-48 text-primary"></iconify-icon>
                            <p class="text-muted fs-13 mt-2 mb-0">Upload a campaign banner</p>
                        </div>
                    </div>
                    <label class="form-label" for="flash-banner-input">Banner Image (WebP)</label>
                    <input type="file" id="flash-banner-input" name="banner_image" class="form-control" accept="image/*">
                    <small class="text-muted d-block mt-1">Wide landscape image recommended for storefront hero strip.</small>
                </div>
            </div>

            <!-- Status & Homepage Visibility Card -->
            <div class="card">
                <div class="card-header">
                    <h4 class="card-title mb-0">Status &amp; Visibility</h4>
                </div>
                <div class="card-body">
                    <div class="mb-3">
                        <label class="form-label" for="flash-status">Campaign Status</label>
                        <select id="flash-status" name="status" class="form-select">
                            @foreach(['draft' => 'Draft', 'scheduled' => 'Scheduled', 'active' => 'Active', 'disabled' => 'Disabled'] as $val => $label)
                                <option value="{{ $val }}" {{ old('status', $flashSale->status) === $val ? 'selected' : '' }}>{{ $label }}</option>
                            @endforeach
                        </select>
                    </div>

                    <div>
                        <div class="form-check form-switch">
                            <input type="hidden" name="show_on_homepage" value="0">
                            <input class="form-check-input" type="checkbox" role="switch" id="show_on_homepage" name="show_on_homepage" value="1" {{ old('show_on_homepage', $flashSale->show_on_homepage) ? 'checked' : '' }}>
                            <label class="form-check-label fw-semibold" for="show_on_homepage">Show on Homepage</label>
                        </div>
                        <small class="text-muted d-block mt-1">Feature this campaign in the homepage Flash Sale section.</small>
                    </div>
                </div>
            </div>
        </div>

        <!-- Right Main Column: Campaign Info & Schedule -->
        <div class="col-xl-8 col-lg-7">
            <!-- General Information Card -->
            <div class="card">
                <div class="card-header d-flex justify-content-between align-items-center">
                    <h4 class="card-title mb-0">{{ $isEdit ? 'Campaign Information' : 'General Information' }}</h4>
                    <div class="d-flex gap-2">
                        @if($isEdit)
                            <a href="{{ route('admin.flash-sales.products', $flashSale) }}" class="btn btn-sm btn-soft-info">Manage Products</a>
                        @endif
                        <a href="{{ route('admin.flash-sales.index') }}" class="btn btn-sm btn-soft-secondary">
                            <i class="bx bx-arrow-back me-1"></i>Back to List
                        </a>
                    </div>
                </div>
                <div class="card-body">
                    <div class="row g-3">
                        <div class="col-md-6">
                            <label class="form-label" for="flash-title">Campaign Title <span class="text-danger">*</span></label>
                            <input type="text" id="flash-title" name="title" class="form-control" value="{{ old('title', $flashSale->title) }}" required>
                        </div>

                        <div class="col-md-6">
                            <label class="form-label" for="flash-slug">URL Slug (optional)</label>
                            <input type="text" id="flash-slug" name="slug" class="form-control" value="{{ old('slug', $flashSale->slug) }}">
                        </div>

                        <div class="col-12">
                            <label class="form-label" for="flash-description">Description</label>
                            <textarea id="flash-description" name="description" class="form-control" rows="4">{{ old('description', $flashSale->description) }}</textarea>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Schedule Window Card -->
            <div class="card">
                <div class="card-header">
                    <h4 class="card-title mb-0">Schedule Window</h4>
                </div>
                <div class="card-body">
                    <div class="row g-3">
                        <div class="col-md-6">
                            <label class="form-label" for="flash-starts-at">Starts At <span class="text-danger">*</span></label>
                            <input type="datetime-local" id="flash-starts-at" name="starts_at" class="form-control" value="{{ old('starts_at', $flashSale->starts_at?->format('Y-m-d\TH:i')) }}" required>
                        </div>

                        <div class="col-md-6">
                            <label class="form-label" for="flash-ends-at">Ends At <span class="text-danger">*</span></label>
                            <input type="datetime-local" id="flash-ends-at" name="ends_at" class="form-control" value="{{ old('ends_at', $flashSale->ends_at?->format('Y-m-d\TH:i')) }}" required>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Action Bar -->
            <div class="p-3 bg-light rounded d-flex justify-content-end gap-2">
                <a href="{{ route('admin.flash-sales.index') }}" class="btn btn-outline-secondary">Cancel</a>
                @if($isEdit)
                    <a href="{{ route('admin.flash-sales.products', $flashSale) }}" class="btn btn-soft-info">Manage Products</a>
                @endif
                <button type="submit" class="btn btn-primary">
                    <iconify-icon icon="solar:check-circle-bold" class="align-middle me-1"></iconify-icon>
                    {{ $isEdit ? 'Update Flash Sale' : 'Create & Assign Products' }}
                </button>
            </div>
        </div>
    </div>
</form>
@endsection

@push('scripts')
<script>
document.getElementById('flash-banner-input')?.addEventListener('change', function (e) {
    const file = e.target.files?.[0];
    if (!file) return;
    const preview = document.getElementById('flash-banner-preview');
    const placeholder = document.getElementById('flash-banner-placeholder');
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
