@extends('layouts.admin')

@php
    $isEdit = $isEdit ?? false;
    $product = $product ?? [
        'id' => null,
        'name' => '',
        'slug' => '',
        'category_slug' => '',
        'brand_id' => '',
        'badge' => '',
        'price' => '',
        'compare_at_price' => '',
        'sku' => '',
        'subtitle' => '',
        'stock_count' => '',
        'min_threshold' => '',
        'stock_status' => 'in_stock',
        'is_featured' => false,
        'best_seller' => false,
        'is_active' => true,
        'meta_title' => '',
        'meta_description' => '',
        'key_ingredients' => [],
        'benefits' => [],
        'skin_types' => [],
        'sizes' => [],
        'short_description' => '',
        'description' => '',
        'ingredients' => '',
        'how_to_use' => '',
        'images' => [],
    ];
    $existingVariants = $product['variants'] ?? [
        [
            'id' => null,
            'title' => '',
            'attributes' => [],
            'sku' => '',
            'price' => '',
            'compare_at_price' => '',
            'stock_count' => '',
            'stock_status' => 'in_stock',
            'image' => null,
        ],
    ];
    $attrList = $attributes ?? collect();
    $previewImg = !empty($product['images'][0])
        ? app(\App\Services\ImageStorageService::class)->resolveUrl($product['images'][0], $product['image_source'] ?? 'local')
        : '';
@endphp

@section('title', $isEdit ? 'Edit Product: ' . $product['name'] : 'Create Product')
@section('container_class', 'container-xxl')

@section('content')
<form action="{{ $isEdit ? route('admin.products.update', $product['id']) : route('admin.products.store') }}" method="POST" enctype="multipart/form-data">
    @csrf
    @if($isEdit)
        @method('PUT')
    @endif

    <div class="row">
        <!-- Left Sticky Sidebar: Live Preview + Publish Controls -->
        <div class="col-xl-3 col-lg-4">
            <div class="position-sticky" style="top: 90px;">
                <!-- Live Catalogue Preview Card -->
                <div class="card">
                    <div class="card-header bg-light-subtle">
                        <h5 class="card-title mb-0">Live Catalogue Preview</h5>
                    </div>
                    <div class="card-body">
                        <div class="bg-light text-center rounded p-3 mb-3 border border-dashed">
                            <img id="preview-image" src="{{ $previewImg }}" alt="" class="img-fluid rounded {{ $previewImg ? '' : 'd-none' }}" style="max-height: 200px; object-fit: cover;">
                            <div id="preview-image-placeholder" class="py-4 {{ $previewImg ? 'd-none' : '' }}">
                                <iconify-icon icon="solar:bottle-bold-duotone" class="fs-48 text-muted"></iconify-icon>
                                <p class="text-muted fs-12 mb-0 mt-1">No image selected</p>
                            </div>
                        </div>

                        <span id="preview-category" class="badge bg-secondary-subtle text-secondary mb-1 text-uppercase fs-11">
                            {{ $product['category_slug'] ?: 'Category' }}
                        </span>
                        <h5 id="preview-title" class="fw-bold text-dark mb-1">
                            {{ $product['name'] ?: 'Untitled Formulation' }}
                        </h5>

                        <div class="d-flex align-items-baseline gap-2 mt-2">
                            <h4 id="preview-price" class="text-primary fw-bold mb-0">
                                {{ $product['price'] !== '' ? ($currencySymbol . number_format((float) $product['price'], 2)) : ($currencySymbol . '0.00') }}
                            </h4>
                            @if(!empty($product['compare_at_price']))
                                <span class="text-muted text-decoration-line-through fs-13">
                                    {{ $currencySymbol }}{{ number_format((float) $product['compare_at_price'], 2) }}
                                </span>
                            @endif
                        </div>
                    </div>
                </div>

                <!-- Visibility & Publish Card -->
                <div class="card">
                    <div class="card-header bg-light-subtle">
                        <h5 class="card-title mb-0">Visibility &amp; Publishing</h5>
                    </div>
                    <div class="card-body">
                        <div class="d-flex flex-column gap-2 mb-3">
                            <div class="form-check form-switch">
                                <input type="hidden" name="is_active" value="0">
                                <input class="form-check-input" type="checkbox" role="switch" id="activeSwitch" name="is_active" value="1" {{ old('is_active', $product['is_active'] ?? true) ? 'checked' : '' }}>
                                <label class="form-check-label fw-semibold" for="activeSwitch">Active on Storefront</label>
                            </div>
                            <div class="form-check form-switch">
                                <input class="form-check-input" type="checkbox" role="switch" id="featuredSwitch" name="is_featured" value="1" {{ old('is_featured', !empty($product['is_featured'])) ? 'checked' : '' }}>
                                <label class="form-check-label fw-semibold" for="featuredSwitch">Featured Product</label>
                            </div>
                            <div class="form-check form-switch">
                                <input class="form-check-input" type="checkbox" role="switch" id="bestSellerSwitch" name="best_seller" value="1" {{ old('best_seller', !empty($product['best_seller'])) ? 'checked' : '' }}>
                                <label class="form-check-label fw-semibold" for="bestSellerSwitch">Best Seller</label>
                            </div>
                        </div>

                        <div class="d-grid gap-2">
                            <button type="submit" class="btn btn-primary">
                                <iconify-icon icon="solar:check-circle-bold" class="align-middle me-1"></iconify-icon>
                                {{ $isEdit ? 'Update Formulation' : 'Publish Product' }}
                            </button>
                            <a href="{{ route('admin.products.index') }}" class="btn btn-outline-secondary">Discard Changes</a>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <!-- Right Main Column -->
        <div class="col-xl-9 col-lg-8">
            <!-- Card 1: General Information & Pricing -->
            <div class="card">
                <div class="card-header d-flex justify-content-between align-items-center">
                    <h4 class="card-title mb-0">Basic Information &amp; Pricing</h4>
                    <a href="{{ route('admin.products.index') }}" class="btn btn-sm btn-soft-secondary">
                        <i class="bx bx-arrow-back me-1"></i>Back to Products
                    </a>
                </div>
                <div class="card-body">
                    <div class="row g-3">
                        <div class="col-lg-6">
                            <label class="form-label" for="product-name">Product Name <span class="text-danger">*</span></label>
                            <input type="text" id="product-name" name="name" class="form-control" value="{{ old('name', $product['name']) }}" required>
                        </div>

                        <div class="col-lg-6">
                            <label class="form-label" for="category-slug">Category <span class="text-danger">*</span></label>
                            <select id="category-slug" name="category_slug" class="form-select" onchange="filterAttributesByCategory()" required>
                                <option value="">— Select Category —</option>
                                @foreach($categories as $cat)
                                    <option value="{{ $cat['slug'] }}" data-attribute-ids='@json($cat['effective_attribute_ids'] ?? [])' {{ old('category_slug', $product['category_slug'] ?? '') === $cat['slug'] ? 'selected' : '' }}>
                                        {{ $cat['hierarchy_path'] ?? $cat['name'] }}
                                    </option>
                                @endforeach
                            </select>
                        </div>

                        <div class="col-lg-4">
                            <label class="form-label" for="product-brand-id">Brand</label>
                            <select id="product-brand-id" name="brand_id" class="form-select">
                                <option value="">— Select Brand —</option>
                                @foreach($brands ?? [] as $b)
                                    <option value="{{ $b->id }}" {{ (string) old('brand_id', $product['brand_id'] ?? '') === (string) $b->id ? 'selected' : '' }}>
                                        {{ $b->name }}
                                    </option>
                                @endforeach
                            </select>
                        </div>

                        <div class="col-lg-4">
                            <label class="form-label" for="product-slug">URL Slug</label>
                            <input type="text" id="product-slug" name="slug" class="form-control" value="{{ old('slug', $product['slug']) }}">
                            <small class="text-muted d-block mt-1">Auto-generated from name if left blank.</small>
                        </div>

                        <div class="col-lg-4">
                            <label class="form-label" for="product-subtitle">Subtitle</label>
                            <input type="text" id="product-subtitle" name="subtitle" class="form-control" value="{{ old('subtitle', $product['subtitle'] ?? '') }}">
                        </div>

                        <div class="col-lg-4">
                            <label class="form-label" for="product-price">Selling Price ({{ $currencySymbol }} {{ $currencyCode }}) <span class="text-danger">*</span></label>
                            <div class="input-group">
                                <span class="input-group-text">{{ $currencySymbol }}</span>
                                <input type="number" step="0.01" min="0" id="product-price" name="price" class="form-control" value="{{ old('price', $product['price']) }}" required>
                            </div>
                            <small class="text-muted d-block mt-1">Current price customers pay.</small>
                        </div>

                        <div class="col-lg-4">
                            <label class="form-label" for="compare-price">Original / Regular Price ({{ $currencySymbol }} {{ $currencyCode }})</label>
                            <div class="input-group">
                                <span class="input-group-text">{{ $currencySymbol }}</span>
                                <input type="number" step="0.01" min="0" id="compare-price" name="compare_at_price" class="form-control" value="{{ old('compare_at_price', $product['compare_at_price'] ?? '') }}">
                            </div>
                            <small class="text-muted d-block mt-1">Optional strikethrough price (higher than selling price).</small>
                        </div>

                        <div class="col-lg-4">
                            <label class="form-label" for="product-sku">Batch SKU</label>
                            <input type="text" id="product-sku" name="sku" class="form-control font-monospace" value="{{ old('sku', $product['sku'] ?? '') }}">
                            <small class="text-muted d-block mt-1">Auto-generated if left blank.</small>
                        </div>

                        <div class="col-lg-3">
                            <label class="form-label" for="product-stock-count">Stock Count</label>
                            <input type="number" min="0" id="product-stock-count" name="stock_count" class="form-control" value="{{ old('stock_count', $product['stock_count'] ?? '') }}">
                            <small class="text-muted d-block mt-1">Auto-sums variant stocks (defaults to 25 if blank).</small>
                        </div>

                        <div class="col-lg-3">
                            <label class="form-label" for="product-min-threshold">Low Stock Threshold</label>
                            <input type="number" min="0" id="product-min-threshold" name="min_threshold" class="form-control" value="{{ old('min_threshold', $product['min_threshold'] ?? '') }}">
                            <small class="text-muted d-block mt-1">Defaults to 10 if left blank.</small>
                        </div>

                        <div class="col-lg-3">
                            <label class="form-label" for="stock-status">Stock Status</label>
                            <select id="stock-status" name="stock_status" class="form-select">
                                <option value="in_stock" {{ old('stock_status', $product['stock_status'] ?? 'in_stock') === 'in_stock' ? 'selected' : '' }}>In Stock</option>
                                <option value="low_stock" {{ old('stock_status', $product['stock_status'] ?? '') === 'low_stock' ? 'selected' : '' }}>Low Stock</option>
                                <option value="out_of_stock" {{ old('stock_status', $product['stock_status'] ?? '') === 'out_of_stock' ? 'selected' : '' }}>Out of Stock</option>
                            </select>
                        </div>

                        <div class="col-lg-3">
                            <label class="form-label" for="product-badge">Product Badge</label>
                            <input type="text" id="product-badge" name="badge" list="badge-presets" class="form-control" value="{{ old('badge', $product['badge'] ?? '') }}">
                            <datalist id="badge-presets">
                                <option value="Bestseller">
                                <option value="Signature">
                                <option value="New">
                                <option value="Award Winner">
                            </datalist>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Card 2: Product Imagery -->
            <div class="card">
                <div class="card-header">
                    <h4 class="card-title mb-0">Product Imagery</h4>
                </div>
                <div class="card-body">
                    <div class="border border-dashed border-2 rounded p-4 text-center bg-light-subtle" id="productUploadBox" style="cursor: pointer;">
                        <input id="product-images-input" name="images[]" type="file" multiple accept="image/*" class="d-none" />
                        <iconify-icon icon="solar:cloud-upload-bold-duotone" class="fs-48 text-primary"></iconify-icon>
                        <h5 class="mt-2 mb-1">Click to upload product images</h5>
                        <p class="text-muted fs-13 mb-0">Recommended: 1000&times;1000px JPG, PNG, or WebP (max 5MB each).</p>
                    </div>

                    <div id="new-images-preview" class="d-flex flex-wrap gap-3 mt-3 d-none"></div>

                    @php
                        $rawGallery = $product['raw_images'] ?? ($product['images'] ?? []);
                    @endphp
                    @if(!empty($rawGallery))
                        <div class="d-flex flex-wrap gap-3 mt-3">
                            @foreach($rawGallery as $gIdx => $img)
                                <div class="position-relative rounded border p-1 text-center" style="width: 82px;">
                                    <img src="{{ app(\App\Services\ImageStorageService::class)->resolveUrl($img, $product['image_source'] ?? 'local') }}" class="w-100 rounded object-fit-cover" style="height: 62px;" alt="">
                                    @if($isEdit)
                                        <div class="form-check mt-1 mb-0 d-flex justify-content-center gap-1">
                                            <input class="form-check-input" type="checkbox" name="remove_images[]" value="{{ $img }}" id="remove_img_{{ $gIdx }}">
                                            <label class="form-check-label fs-11 text-danger" for="remove_img_{{ $gIdx }}">Remove</label>
                                        </div>
                                    @endif
                                </div>
                            @endforeach
                        </div>
                    @endif
                </div>
            </div>

            <!-- Card 3: Product Variants -->
            <div class="card">
                <div class="card-header d-flex justify-content-between align-items-center">
                    <h4 class="card-title mb-0">Product Variants</h4>
                    <button type="button" class="btn btn-sm btn-soft-primary" onclick="addVariantRow()">+ Add Variant</button>
                </div>
                <div class="card-body">
                    <div id="variants-builder" class="d-flex flex-column gap-3">
                        @foreach($existingVariants as $vIdx => $vRow)
                            <div class="border rounded p-3 bg-light-subtle variant-row">
                                @if(!empty($vRow['id']))
                                    <input type="hidden" name="variants[{{ $vIdx }}][id]" value="{{ $vRow['id'] }}">
                                @endif
                                <div class="row g-2 mb-2 variant-attr-row">
                                    @foreach($attrList as $attr)
                                        <div class="col-md-3 variant-attr-col" data-attribute-id="{{ $attr->id }}">
                                            <label class="form-label fs-12 mb-1">{{ $attr->name }}</label>
                                            <select name="variants[{{ $vIdx }}][attributes][{{ $attr->name }}]" class="form-select form-select-sm">
                                                <option value="">— {{ $attr->name }} —</option>
                                                @foreach($attr->values as $val)
                                                    <option value="{{ $val->value }}" {{ (($vRow['attributes'][$attr->name] ?? '') === $val->value) ? 'selected' : '' }}>{{ $val->value }}</option>
                                                @endforeach
                                            </select>
                                        </div>
                                    @endforeach
                                </div>
                                <div class="row g-2 align-items-end">
                                    <div class="col-md-3">
                                        <label class="form-label fs-12 mb-1">Variant Title</label>
                                        <input type="text" name="variants[{{ $vIdx }}][title]" class="form-control form-control-sm" value="{{ $vRow['title'] ?? ($vRow['size'] ?? '') }}">
                                    </div>
                                    <div class="col-md-2">
                                        <label class="form-label fs-12 mb-1">SKU</label>
                                        <input type="text" name="variants[{{ $vIdx }}][sku]" class="form-control form-control-sm" value="{{ $vRow['sku'] ?? '' }}">
                                    </div>
                                    <div class="col-md-2">
                                        <label class="form-label fs-12 mb-1">Price ({{ $currencySymbol }})</label>
                                        <input type="number" step="0.01" name="variants[{{ $vIdx }}][price]" class="form-control form-control-sm" value="{{ $vRow['price'] ?? '' }}">
                                    </div>
                                    <div class="col-md-2">
                                        <label class="form-label fs-12 mb-1">Stock</label>
                                        <input type="number" name="variants[{{ $vIdx }}][stock_count]" class="form-control form-control-sm variant-stock-input" value="{{ $vRow['stock_count'] ?? '' }}">
                                    </div>
                                    <div class="col-md-3">
                                        <label class="form-label fs-12 mb-1">Variant Image (Optional)</label>
                                        <div class="d-flex gap-1 align-items-center">
                                            <input type="file" name="variant_images[{{ $vIdx }}]" accept="image/*" class="form-control form-control-sm">
                                            <button type="button" class="btn btn-sm btn-soft-danger" onclick="removeVariantRow(this)" title="Remove Variant">&times;</button>
                                        </div>
                                        @if(!empty($vRow['image']))
                                            <div class="variant-existing-image-wrap mt-1 d-flex align-items-center gap-2">
                                                <img src="{{ app(\App\Services\ImageStorageService::class)->resolveUrl($vRow['image']) }}" alt="" class="rounded border" style="width:32px;height:32px;object-fit:cover;">
                                                <div class="form-check mb-0">
                                                    <input class="form-check-input" type="checkbox" name="variants[{{ $vIdx }}][remove_image]" value="1">
                                                    <label class="form-check-label fs-11 text-danger">Remove</label>
                                                </div>
                                            </div>
                                        @endif
                                    </div>
                                </div>
                            </div>
                        @endforeach
                    </div>
                </div>
            </div>

            <!-- Card 4: Descriptions, Botanicals & SEO -->
            <div class="card">
                <div class="card-header">
                    <h4 class="card-title mb-0">Descriptions, Botanicals &amp; SEO</h4>
                </div>
                <div class="card-body">
                    <div class="row g-3">
                        <div class="col-lg-4">
                            <label class="form-label" for="skin-types">Skin Types (comma-separated)</label>
                            <input type="text" id="skin-types" name="skin_types" class="form-control" value="{{ old('skin_types', is_array($product['skin_types'] ?? null) ? implode(', ', $product['skin_types']) : ($product['skin_types'] ?? '')) }}">
                        </div>
                        <div class="col-lg-4">
                            <label class="form-label" for="benefits">Benefits (comma-separated)</label>
                            <input type="text" id="benefits" name="benefits" class="form-control" value="{{ old('benefits', is_array($product['benefits'] ?? null) ? implode(', ', $product['benefits']) : ($product['benefits'] ?? '')) }}">
                        </div>
                        <div class="col-lg-4">
                            <label class="form-label" for="key-ingredients">Key Ingredients (comma-separated)</label>
                            <input type="text" id="key-ingredients" name="key_ingredients" class="form-control" value="{{ old('key_ingredients', is_array($product['key_ingredients'] ?? null) ? implode(', ', $product['key_ingredients']) : ($product['key_ingredients'] ?? '')) }}">
                        </div>

                        <div class="col-12">
                            <label class="form-label" for="short-desc">Short Summary</label>
                            <textarea id="short-desc" name="short_description" class="form-control" rows="2">{{ old('short_description', $product['short_description'] ?? '') }}</textarea>
                        </div>

                        <div class="col-12">
                            <label class="form-label" for="full-desc">Full Editorial Description</label>
                            <textarea id="full-desc" name="description" class="form-control" rows="4">{{ old('description', $product['description'] ?? '') }}</textarea>
                        </div>

                        <div class="col-md-6">
                            <label class="form-label" for="ingredients">Botanical Ingredients (INCI List)</label>
                            <textarea id="ingredients" name="ingredients" class="form-control font-monospace fs-13" rows="3">{{ old('ingredients', $product['ingredients'] ?? '') }}</textarea>
                        </div>

                        <div class="col-md-6">
                            <label class="form-label" for="how-to-use">Daily Ritual (How to Use)</label>
                            <textarea id="how-to-use" name="how_to_use" class="form-control" rows="3">{{ old('how_to_use', $product['how_to_use'] ?? '') }}</textarea>
                        </div>

                        <div class="col-12 border-top pt-3 mt-2">
                            <div class="row g-3">
                                <div class="col-lg-6">
                                    <label class="form-label" for="meta-title">Meta Title</label>
                                    <input type="text" id="meta-title" name="meta_title" class="form-control" value="{{ old('meta_title', $product['meta_title'] ?? '') }}">
                                </div>
                                <div class="col-lg-6">
                                    <label class="form-label" for="meta-description">Meta Description</label>
                                    <input type="text" id="meta-description" name="meta_description" class="form-control" value="{{ old('meta_description', $product['meta_description'] ?? '') }}">
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Bottom Actions -->
            <div class="p-3 bg-light mb-4 rounded d-flex justify-content-end gap-2">
                <a href="{{ route('admin.products.index') }}" class="btn btn-outline-secondary">Cancel</a>
                <button type="submit" class="btn btn-primary">
                    <iconify-icon icon="solar:check-circle-bold" class="align-middle me-1"></iconify-icon>
                    {{ $isEdit ? 'Save Changes' : 'Publish Product' }}
                </button>
            </div>
        </div>
    </div>
</form>
@endsection

@push('scripts')
<script>
    let variantRowIndex = {{ count($existingVariants) }};
    function addVariantRow() {
        const container = document.getElementById('variants-builder');
        const first = container.querySelector('.variant-row');
        if (!first) return;
        const clone = first.cloneNode(true);
        clone.querySelectorAll('input[type="hidden"], .variant-existing-image-wrap').forEach(el => el.remove());
        clone.querySelectorAll('select, input').forEach(el => {
            el.name = el.name.replace(/\[\d+\]/, `[${variantRowIndex}]`);
            if (el.type === 'file' || el.type === 'text' || el.type === 'number') el.value = '';
        });
        container.appendChild(clone);
        variantRowIndex++;
        filterAttributesByCategory();
    }
    function removeVariantRow(btn) {
        const rows = document.querySelectorAll('#variants-builder .variant-row');
        if (rows.length <= 1) return;
        btn.closest('.variant-row').remove();
        syncTotalVariantStock();
    }
    function syncTotalVariantStock() {
        const stockInput = document.getElementById('product-stock-count');
        if (!stockInput) return;
        const inputs = Array.from(document.querySelectorAll('.variant-stock-input'));
        const hasAnyValue = inputs.some(el => el.value !== '');
        if (!hasAnyValue) return;
        const total = inputs.reduce((sum, el) => sum + (parseInt(el.value, 10) || 0), 0);
        stockInput.value = total;
    }
    function filterAttributesByCategory() {
        const catSelect = document.getElementById('category-slug');
        if (!catSelect) return;
        const opt = catSelect.options[catSelect.selectedIndex];
        const allowedIds = catSelect.value
            ? JSON.parse(opt?.dataset?.attributeIds || '[]').map(Number)
            : [];
        document.querySelectorAll('.variant-attr-col').forEach(col => {
            const attrId = Number(col.dataset.attributeId);
            const visible = allowedIds.includes(attrId);
            col.style.display = visible ? '' : 'none';
            const sel = col.querySelector('select');
            if (sel) sel.disabled = !visible;
        });
        document.querySelectorAll('.variant-attr-row').forEach(row => {
            const anyVisible = Array.from(row.querySelectorAll('.variant-attr-col'))
                .some(col => col.style.display !== 'none');
            row.style.display = anyVisible ? '' : 'none';
        });
    }
    document.addEventListener('DOMContentLoaded', function () {
        filterAttributesByCategory();

        document.getElementById('variants-builder')?.addEventListener('input', function (e) {
            if (e.target.classList.contains('variant-stock-input')) {
                syncTotalVariantStock();
            }
        });

        const currencySymbol = @json($currencySymbol);
        document.getElementById('product-name')?.addEventListener('input', function () {
            const el = document.getElementById('preview-title');
            if (el) el.textContent = this.value.trim() || 'Untitled Formulation';
        });
        document.getElementById('product-price')?.addEventListener('input', function () {
            const el = document.getElementById('preview-price');
            const val = parseFloat(this.value);
            if (el) el.textContent = currencySymbol + (isNaN(val) ? '0.00' : val.toFixed(2));
        });
        document.getElementById('category-slug')?.addEventListener('change', function () {
            const el = document.getElementById('preview-category');
            if (el) el.textContent = this.value || 'Category';
        });

        const uploadBox = document.getElementById('productUploadBox');
        const imagesInput = document.getElementById('product-images-input');
        const newImagesPreview = document.getElementById('new-images-preview');
        uploadBox?.addEventListener('click', () => imagesInput?.click());
        imagesInput?.addEventListener('change', function () {
            if (!newImagesPreview) return;
            newImagesPreview.innerHTML = '';
            const files = Array.from(this.files || []);
            if (files.length === 0) {
                newImagesPreview.classList.add('d-none');
                return;
            }
            newImagesPreview.classList.remove('d-none');
            files.forEach((file, idx) => {
                const url = URL.createObjectURL(file);
                if (idx === 0) {
                    const prevImg = document.getElementById('preview-image');
                    const prevPh = document.getElementById('preview-image-placeholder');
                    if (prevImg) {
                        prevImg.src = url;
                        prevImg.classList.remove('d-none');
                    }
                    prevPh?.classList.add('d-none');
                }
                const wrap = document.createElement('div');
                wrap.className = 'rounded border p-1';
                wrap.style.width = '82px';
                wrap.innerHTML = `<img src="${url}" class="w-100 rounded object-fit-cover" style="height: 62px;" alt="">`;
                newImagesPreview.appendChild(wrap);
            });
        });
    });
</script>
@endpush
