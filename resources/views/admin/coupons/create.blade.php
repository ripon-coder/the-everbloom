@extends('layouts.admin')

@php
    $isEdit = $isEdit ?? false;
    $coupon = $coupon ?? null;
    $selectedProductIds = old('product_ids', $coupon ? $coupon->products->pluck('id')->all() : []);
    $selectedCategoryIds = old('category_ids', $coupon ? $coupon->categories->pluck('id')->all() : []);
@endphp

@section('title', $isEdit ? 'Edit Coupon: ' . $coupon->code : 'Create Coupon')
@section('container_class', 'container-xxl')

@section('content')
<div class="row justify-content-center">
    <div class="col-lg-8">
        <form action="{{ $isEdit ? route('admin.coupons.update', $coupon->id) : route('admin.coupons.store') }}" method="POST">
            @csrf
            @if($isEdit)
                @method('PUT')
            @endif

            <div class="card">
                <div class="card-header">
                    <h4 class="card-title mb-0">{{ $isEdit ? 'Edit Promotional Coupon' : 'Create Promotional Coupon' }}</h4>
                </div>
                <div class="card-body">
                    <div class="row g-3">
                        <div class="col-md-6">
                            <label class="form-label" for="coupon-code">Coupon Code <span class="text-danger">*</span></label>
                            <input type="text" id="coupon-code" name="code" class="form-control text-uppercase font-monospace" placeholder="e.g. SUMMERBLOOM20" value="{{ old('code', $coupon?->code ?? '') }}" required>
                        </div>

                        <div class="col-md-6">
                            <label class="form-label" for="coupon-label">Campaign Title <span class="text-danger">*</span></label>
                            <input type="text" id="coupon-label" name="label" class="form-control" placeholder="e.g. 20% Summer Harvest Special" value="{{ old('label', $coupon?->label ?? '') }}" required>
                        </div>

                        <div class="col-md-4">
                            <label class="form-label" for="coupon-type">Discount Type</label>
                            <select id="coupon-type" name="type" class="form-select">
                                <option value="percentage" {{ old('type', $coupon?->type ?? 'percentage') === 'percentage' ? 'selected' : '' }}>Percentage Discount (% OFF)</option>
                                <option value="fixed" {{ old('type', $coupon?->type ?? '') === 'fixed' ? 'selected' : '' }}>Fixed Cart Discount ({{ $currencySymbol }} {{ $currencyCode }})</option>
                                <option value="shipping" {{ old('type', $coupon?->type ?? '') === 'shipping' ? 'selected' : '' }}>Complimentary Standard Shipping</option>
                            </select>
                        </div>

                        <div class="col-md-4">
                            <label class="form-label" for="coupon-value">Discount Value</label>
                            <input type="number" step="0.01" min="0" id="coupon-value" name="value" class="form-control" value="{{ old('value', $coupon?->value ?? 10) }}">
                        </div>

                        <div class="col-md-4">
                            <label class="form-label" for="coupon-max-discount">Max Discount Cap ({{ $currencySymbol }})</label>
                            <input type="number" step="0.01" min="0" id="coupon-max-discount" name="max_discount" class="form-control" placeholder="Optional" value="{{ old('max_discount', $coupon?->max_discount ?? '') }}">
                        </div>

                        <div class="col-md-4">
                            <label class="form-label" for="usage-limit">Total Usage Limit</label>
                            <input type="number" min="1" id="usage-limit" name="usage_limit" class="form-control" placeholder="Unlimited" value="{{ old('usage_limit', $coupon?->usage_limit ?? '') }}">
                        </div>

                        <div class="col-md-4">
                            <label class="form-label" for="per-customer-limit">Per-Customer Limit</label>
                            <input type="number" min="1" id="per-customer-limit" name="per_customer_limit" class="form-control" placeholder="Unlimited" value="{{ old('per_customer_limit', $coupon?->per_customer_limit ?? '') }}">
                        </div>

                        <div class="col-md-4">
                            <label class="form-label" for="min-order">Minimum Order Amount ({{ $currencySymbol }})</label>
                            <input type="number" step="0.01" min="0" id="min-order" name="min_order" class="form-control" value="{{ old('min_order', $coupon?->min_order_amount ?? 0) }}">
                        </div>

                        <div class="col-md-6">
                            <label class="form-label" for="coupon-starts">Starts At</label>
                            <input type="date" id="coupon-starts" name="starts_at" class="form-control" value="{{ old('starts_at', $coupon?->starts_at?->format('Y-m-d') ?? '') }}">
                        </div>

                        <div class="col-md-6">
                            <label class="form-label" for="coupon-expiry">Expiration Date</label>
                            <input type="date" id="coupon-expiry" name="expires_at" class="form-control" value="{{ old('expires_at', $coupon?->expires_at?->format('Y-m-d') ?? '') }}">
                        </div>

                        <div class="col-md-6">
                            <label class="form-label" for="coupon-products">Restrict to Products (Optional)</label>
                            <select id="coupon-products" name="product_ids[]" class="form-select" multiple size="4">
                                @foreach($products ?? [] as $prod)
                                    <option value="{{ $prod->id }}" {{ in_array($prod->id, $selectedProductIds) ? 'selected' : '' }}>
                                        {{ $prod->name }} ({{ $prod->sku }})
                                    </option>
                                @endforeach
                            </select>
                            <small class="text-muted">Leave empty to apply to all products.</small>
                        </div>

                        <div class="col-md-6">
                            <label class="form-label" for="coupon-categories">Restrict to Categories (Optional)</label>
                            <select id="coupon-categories" name="category_ids[]" class="form-select" multiple size="4">
                                @foreach($categories ?? [] as $cat)
                                    <option value="{{ $cat->id }}" {{ in_array($cat->id, $selectedCategoryIds) ? 'selected' : '' }}>
                                        {{ $cat->name }}
                                    </option>
                                @endforeach
                            </select>
                            <small class="text-muted">Leave empty to apply to all categories.</small>
                        </div>

                        <div class="col-12">
                            <div class="form-check form-switch">
                                <input type="hidden" name="is_active" value="0">
                                <input class="form-check-input" type="checkbox" id="couponActive" name="is_active" value="1" {{ old('is_active', $coupon?->is_active ?? true) ? 'checked' : '' }}>
                                <label class="form-check-label fw-semibold" for="couponActive">Active</label>
                            </div>
                        </div>
                    </div>
                </div>
                <div class="card-footer bg-light-subtle d-flex justify-content-end gap-2">
                    <a href="{{ route('admin.coupons.index') }}" class="btn btn-outline-secondary">Cancel</a>
                    <button type="submit" class="btn btn-primary">
                        <i class="bx bx-check me-1"></i> {{ $isEdit ? 'Save Changes' : 'Save & Publish Coupon' }}
                    </button>
                </div>
            </div>
        </form>
    </div>
</div>
@endsection
