@extends('layouts.admin')

@php
    $isEdit = $isEdit ?? false;
    $product = $product ?? [
        'id' => null,
        'name' => '',
        'slug' => '',
        'category_slug' => 'serums',
        'badge' => '',
        'price' => 64.00,
        'compare_at_price' => '',
        'sku' => 'EB-BOT-001',
        'stock_count' => 50,
        'stock_status' => 'in_stock',
        'is_featured' => false,
        'sizes' => ['30ml / 1.0 fl oz', '50ml / 1.7 fl oz'],
        'short_description' => 'Targeted botanical elixir formulated with organic rosehip seed oil and cold-pressed botanical extracts.',
        'description' => 'A concentrated daily serum designed to restore barrier vitality, even tone, and infuse cells with natural antioxidants.',
        'ingredients' => 'Simmondsia Chinensis (Jojoba) Seed Oil*, Rosa Canina (Rosehip) Fruit Oil*, Squalane (Olive-derived), Helianthus Annuus (Sunflower) Seed Oil, Tocopherol (Vitamin E), Pelargonium Graveolens (Geranium) Flower Oil*. *Certified Organic',
        'how_to_use' => 'Warm 3–4 drops between palms. Gently press into cleansed face, neck, and décolletage every morning and evening before rich creams.',
        'images' => ['products/botanical-radiance-serum-01.jpg'],
    ];
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
        <!-- Left Column: Live Preview Card -->
        <div class="col-xl-3 col-lg-4">
            <div class="card position-sticky" style="top: 110px;">
                <div class="card-header bg-light-subtle">
                    <h5 class="card-title mb-0">Live Catalogue Preview</h5>
                </div>
                <div class="card-body">
                    <!-- Preview Image -->
                    <div class="bg-light text-center rounded p-2 mb-3">
                        <img id="preview-image" src="{{ app(\App\Services\ImageStorageService::class)->resolveUrl($product['images'][0] ?? 'products/botanical-radiance-serum-01.jpg') }}" alt="" class="img-fluid rounded" style="max-height: 220px; object-fit: cover;">
                    </div>

                    <!-- Title & Category -->
                    <span id="preview-category" class="badge bg-secondary-subtle text-secondary mb-1 text-uppercase fs-11">
                        {{ $product['category_slug'] ?? 'Serums' }}
                    </span>
                    <h5 id="preview-title" class="fw-bold text-dark mb-1">
                        {{ $product['name'] ?: 'Botanical Radiance Serum' }}
                    </h5>

                    <!-- Price -->
                    <div class="d-flex align-items-baseline gap-2 my-2">
                        <h4 id="preview-price" class="text-primary fw-bold mb-0">
                            {{ $currencySymbol }}{{ number_format((float)($product['price'] ?: 64), 2) }}
                        </h4>
                        @if(!empty($product['compare_at_price']))
                            <span class="text-muted text-decoration-line-through fs-13">
                                {{ $currencySymbol }}{{ number_format((float)$product['compare_at_price'], 2) }}
                            </span>
                        @endif
                    </div>

                    <!-- Sizes -->
                    <div class="mt-3">
                        <p class="text-muted mb-1 fs-12 fw-semibold">Available Sizes:</p>
                        <div class="d-flex flex-wrap gap-1">
                            @foreach(['30ml / 1.0 fl oz', '50ml / 1.7 fl oz', '100ml / 3.4 oz'] as $size)
                                <span class="badge bg-light text-dark border">{{ $size }}</span>
                            @endforeach
                        </div>
                    </div>

                    <!-- Badge -->
                    <div class="mt-3">
                        <span class="badge bg-success-subtle text-success fs-12">
                            <i class="bx bx-check-circle me-1"></i> 100% Vegan & Botanical
                        </span>
                    </div>
                </div>
                <div class="card-footer bg-light-subtle">
                    <div class="d-grid gap-2">
                        <button type="submit" class="btn btn-primary">
                            {{ $isEdit ? 'Update Formulation' : 'Publish Product' }}
                        </button>
                        <a href="{{ route('admin.products.index') }}" class="btn btn-outline-secondary">Discard Changes</a>
                    </div>
                </div>
            </div>
        </div>

        <!-- Right Column: Form Inputs -->
        <div class="col-xl-9 col-lg-8">

            <!-- Card 1: Add Product Photos -->
            <div class="card">
                <div class="card-header">
                    <h4 class="card-title mb-0">Product Imagery</h4>
                </div>
                <div class="card-body">
                    <div class="dropzone border-dashed border-2 rounded p-4 text-center bg-light-subtle" id="productDropzone" style="cursor: pointer;">
                        <div class="fallback">
                            <input name="file" type="file" multiple />
                        </div>
                        <div class="dz-message needsclick my-3">
                            <i class="bx bx-cloud-upload fs-48 text-primary"></i>
                            <h4 class="mt-2 mb-1">Drop botanical images here or click to upload</h4>
                            <p class="text-muted fs-13 mb-0">Recommended size: 1000×1000px high-resolution JPG or WebP with neutral studio backdrop.</p>
                        </div>
                    </div>

                    <!-- Existing Image Thumbs -->
                    <div class="d-flex gap-2 mt-3">
                        @foreach($product['images'] ?? [] as $img)
                            <div class="position-relative rounded border p-1" style="width: 70px; height: 70px;">
                                <img src="{{ app(\App\Services\ImageStorageService::class)->resolveUrl($img) }}" class="w-100 h-100 object-fit-cover rounded" alt="">
                            </div>
                        @endforeach
                    </div>
                </div>
            </div>

            <!-- Card 2: Botanical Product Information -->
            <div class="card">
                <div class="card-header">
                    <h4 class="card-title mb-0">Formulation Information</h4>
                </div>
                <div class="card-body">
                    <div class="row g-3">
                        <!-- Product Name -->
                        <div class="col-lg-6">
                            <label class="form-label" for="product-name">Product Name <span class="text-danger">*</span></label>
                            <input type="text" id="product-name" name="name" class="form-control" placeholder="e.g. Botanical Radiance Serum" value="{{ old('name', $product['name']) }}" required>
                        </div>

                        <!-- Category -->
                        <div class="col-lg-6">
                            <label class="form-label" for="category-slug">Category <span class="text-danger">*</span></label>
                            <select id="category-slug" name="category_slug" class="form-select" onchange="filterAttributesByCategory()" required>
                                @foreach($categories as $cat)
                                    <option value="{{ $cat['slug'] }}" data-attribute-ids='@json($cat['effective_attribute_ids'] ?? [])' {{ old('category_slug', $product['category_slug'] ?? '') === $cat['slug'] ? 'selected' : '' }}>
                                        {{ $cat['hierarchy_path'] ?? $cat['name'] }}
                                    </option>
                                @endforeach
                            </select>
                        </div>

                        <!-- URL Slug -->
                        <div class="col-lg-4">
                            <label class="form-label" for="product-slug">URL Slug</label>
                            <input type="text" id="product-slug" name="slug" class="form-control" placeholder="e.g. botanical-radiance-serum" value="{{ old('slug', $product['slug']) }}">
                        </div>

                        <!-- Badge -->
                        <div class="col-lg-4">
                            <label class="form-label" for="product-badge">Product Badge</label>
                            <select id="product-badge" name="badge" class="form-select">
                                <option value="">None</option>
                                <option value="Bestseller" {{ ($product['badge'] ?? '') === 'Bestseller' ? 'selected' : '' }}>Bestseller</option>
                                <option value="Signature" {{ ($product['badge'] ?? '') === 'Signature' ? 'selected' : '' }}>Signature</option>
                                <option value="New" {{ ($product['badge'] ?? '') === 'New' ? 'selected' : '' }}>New</option>
                                <option value="Award Winner" {{ ($product['badge'] ?? '') === 'Award Winner' ? 'selected' : '' }}>Award Winner</option>
                            </select>
                        </div>

                        <!-- Stock Status -->
                        <div class="col-lg-4">
                            <label class="form-label" for="stock-status">Stock Status</label>
                            <select id="stock-status" name="stock_status" class="form-select">
                                <option value="in_stock" {{ ($product['stock_status'] ?? '') === 'in_stock' ? 'selected' : '' }}>In Stock</option>
                                <option value="low_stock" {{ ($product['stock_status'] ?? '') === 'low_stock' ? 'selected' : '' }}>Low Stock</option>
                                <option value="out_of_stock" {{ ($product['stock_status'] ?? '') === 'out_of_stock' ? 'selected' : '' }}>Out of Stock</option>
                            </select>
                        </div>

                        <!-- Dynamic Multi-Attribute Variants & Optional Variant Image Builder -->
                        <div class="col-12">
                            <div class="d-flex justify-content-between align-items-center mb-2">
                                <label class="form-label mb-0 fw-semibold">Product Variants (Brand, Size, Color, Optional Image)</label>
                                <button type="button" class="btn btn-sm btn-soft-primary" onclick="addVariantRow()">+ Add Variant</button>
                            </div>
                            <div id="variants-builder" class="d-flex flex-column gap-3">
                                @php
                                    $existingVariants = $product['variants'] ?? [
                                        ['id' => null, 'title' => '30ml / 1.0 fl oz', 'attributes' => [], 'sku' => '', 'price' => $product['price'] ?? 64.00, 'compare_at_price' => $product['compare_at_price'] ?? '', 'stock_count' => 25, 'stock_status' => 'in_stock', 'image' => null],
                                    ];
                                    $attrList = $attributes ?? collect();
                                @endphp
                                @foreach($existingVariants as $vIdx => $vRow)
                                    <div class="border rounded p-3 bg-light-subtle variant-row">
                                        @if(!empty($vRow['id']))
                                            <input type="hidden" name="variants[{{ $vIdx }}][id]" value="{{ $vRow['id'] }}">
                                        @endif
                                        <div class="row g-2 mb-2">
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
                                                <input type="text" name="variants[{{ $vIdx }}][title]" class="form-control form-control-sm" value="{{ $vRow['title'] ?? ($vRow['size'] ?? '') }}" placeholder="Auto or custom">
                                            </div>
                                            <div class="col-md-2">
                                                <label class="form-label fs-12 mb-1">SKU</label>
                                                <input type="text" name="variants[{{ $vIdx }}][sku]" class="form-control form-control-sm" value="{{ $vRow['sku'] ?? '' }}">
                                            </div>
                                            <div class="col-md-2">
                                                <label class="form-label fs-12 mb-1">Price ({{ $currencySymbol }})</label>
                                                <input type="number" step="0.01" name="variants[{{ $vIdx }}][price]" class="form-control form-control-sm" value="{{ $vRow['price'] ?? ($product['price'] ?? 0) }}">
                                            </div>
                                            <div class="col-md-2">
                                                <label class="form-label fs-12 mb-1">Stock</label>
                                                <input type="number" name="variants[{{ $vIdx }}][stock_count]" class="form-control form-control-sm" value="{{ $vRow['stock_count'] ?? 25 }}">
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
                                    if (el.type === 'file') el.value = '';
                                    if (el.type === 'text') el.value = '';
                                });
                                container.appendChild(clone);
                                variantRowIndex++;
                                filterAttributesByCategory();
                            }
                            function removeVariantRow(btn) {
                                const rows = document.querySelectorAll('#variants-builder .variant-row');
                                if (rows.length <= 1) return;
                                btn.closest('.variant-row').remove();
                            }
                            function filterAttributesByCategory() {
                                const catSelect = document.getElementById('category-slug');
                                if (!catSelect) return;
                                const opt = catSelect.options[catSelect.selectedIndex];
                                const allowedIds = JSON.parse(opt?.dataset?.attributeIds || '[]').map(Number);
                                document.querySelectorAll('.variant-attr-col').forEach(col => {
                                    const attrId = Number(col.dataset.attributeId);
                                    const visible = allowedIds.length === 0 || allowedIds.includes(attrId);
                                    col.style.display = visible ? '' : 'none';
                                    const sel = col.querySelector('select');
                                    if (sel) sel.disabled = !visible;
                                });
                            }
                            document.addEventListener('DOMContentLoaded', filterAttributesByCategory);
                        </script>

                        <!-- Featured Toggle -->
                        <div class="col-12">
                            <div class="form-check form-switch">
                                <input class="form-check-input" type="checkbox" role="switch" id="featuredSwitch" name="is_featured" value="1" {{ !empty($product['is_featured']) ? 'checked' : '' }}>
                                <label class="form-check-label fw-semibold" for="featuredSwitch">Feature on Storefront Homepage</label>
                            </div>
                        </div>

                        <!-- Short Description -->
                        <div class="col-12">
                            <label class="form-label" for="short-desc">Short Summary (Catalogue subtitle)</label>
                            <textarea id="short-desc" name="short_description" class="form-control" rows="2">{{ old('short_description', $product['short_description'] ?? '') }}</textarea>
                        </div>

                        <!-- Full Description -->
                        <div class="col-12">
                            <label class="form-label" for="full-desc">Full Editorial Description</label>
                            <textarea id="full-desc" name="description" class="form-control" rows="4">{{ old('description', $product['description'] ?? '') }}</textarea>
                        </div>

                        <!-- INCI Ingredients -->
                        <div class="col-12">
                            <label class="form-label" for="ingredients">Botanical Ingredients (INCI List)</label>
                            <textarea id="ingredients" name="ingredients" class="form-control font-monospace fs-13" rows="3">{{ old('ingredients', $product['ingredients'] ?? '') }}</textarea>
                            <small class="text-muted">List all organic and wild-harvested botanical actives.</small>
                        </div>

                        <!-- How to Use -->
                        <div class="col-12">
                            <label class="form-label" for="how-to-use">Daily Ritual (How to Use)</label>
                            <textarea id="how-to-use" name="how_to_use" class="form-control" rows="3">{{ old('how_to_use', $product['how_to_use'] ?? '') }}</textarea>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Card 3: Pricing & Inventory Details -->
            <div class="card">
                <div class="card-header">
                    <h4 class="card-title mb-0">Pricing & Inventory</h4>
                </div>
                <div class="card-body">
                    <div class="row g-3">
                        <!-- Price -->
                        <div class="col-lg-4">
                            <label class="form-label" for="product-price">Retail Price ({{ $currencySymbol }} {{ $currencyCode }}) <span class="text-danger">*</span></label>
                            <div class="input-group">
                                <span class="input-group-text">{{ $currencySymbol }}</span>
                                <input type="number" step="0.01" id="product-price" name="price" class="form-control" value="{{ old('price', $product['price']) }}" required>
                            </div>
                        </div>

                        <!-- Compare-at Price -->
                        <div class="col-lg-4">
                            <label class="form-label" for="compare-price">Original / Compare Price ({{ $currencySymbol }} {{ $currencyCode }})</label>
                            <div class="input-group">
                                <span class="input-group-text">{{ $currencySymbol }}</span>
                                <input type="number" step="0.01" id="compare-price" name="compare_at_price" class="form-control" value="{{ old('compare_at_price', $product['compare_at_price'] ?? '') }}">
                            </div>
                        </div>

                        <!-- Stock / SKU -->
                        <div class="col-lg-4">
                            <label class="form-label" for="product-sku">Batch SKU</label>
                            <input type="text" id="product-sku" name="sku" class="form-control font-monospace" value="{{ old('sku', $product['sku'] ?? ('EB-' . str_pad($product['id'] ?? 1, 4, '0', STR_PAD_LEFT))) }}">
                        </div>
                    </div>
                </div>
            </div>

            <!-- Bottom Actions -->
            <div class="p-3 bg-light mb-4 rounded d-flex justify-content-end gap-2">
                <a href="{{ route('admin.products.index') }}" class="btn btn-outline-secondary">Cancel</a>
                <button type="submit" class="btn btn-primary">
                    <i class="bx bx-check-double me-1"></i> {{ $isEdit ? 'Save Changes' : 'Publish Product' }}
                </button>
            </div>

        </div>
    </div>
</form>
@endsection
