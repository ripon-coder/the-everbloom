@extends('layouts.admin')

@section('title', 'Create Coupon')
@section('container_class', 'container-xxl')

@section('content')
<div class="row justify-content-center">
    <div class="col-lg-8">
        <form action="{{ route('admin.coupons.store') }}" method="POST">
            @csrf

            <div class="card">
                <div class="card-header">
                    <h4 class="card-title mb-0">Create Promotional Coupon</h4>
                </div>
                <div class="card-body">
                    <div class="row g-3">
                        <!-- Code -->
                        <div class="col-md-6">
                            <label class="form-label" for="coupon-code">Coupon Code <span class="text-danger">*</span></label>
                            <input type="text" id="coupon-code" name="code" class="form-control text-uppercase font-monospace" placeholder="e.g. SUMMERBLOOM20" required>
                        </div>

                        <!-- Campaign Label -->
                        <div class="col-md-6">
                            <label class="form-label" for="coupon-label">Campaign Title <span class="text-danger">*</span></label>
                            <input type="text" id="coupon-label" name="label" class="form-control" placeholder="e.g. 20% Summer Harvest Special" required>
                        </div>

                        <!-- Discount Type -->
                        <div class="col-md-6">
                            <label class="form-label" for="coupon-type">Discount Type</label>
                            <select id="coupon-type" name="type" class="form-select">
                                <option value="percentage">Percentage Discount (% OFF)</option>
                                <option value="fixed">Fixed Cart Discount ($ USD)</option>
                                <option value="shipping">Complimentary Standard Shipping</option>
                            </select>
                        </div>

                        <!-- Discount Value -->
                        <div class="col-md-6">
                            <label class="form-label" for="coupon-value">Discount Value</label>
                            <input type="number" id="coupon-value" name="value" class="form-control" placeholder="e.g. 20" value="10">
                        </div>

                        <!-- Usage Limit -->
                        <div class="col-md-6">
                            <label class="form-label" for="usage-limit">Maximum Usages</label>
                            <input type="number" id="usage-limit" name="usage_limit" class="form-control" value="500">
                        </div>

                        <!-- Minimum Order Value -->
                        <div class="col-md-6">
                            <label class="form-label" for="min-order">Minimum Order Amount ($ USD)</label>
                            <input type="number" id="min-order" name="min_order" class="form-control" value="50.00">
                        </div>

                        <!-- Expiration Date -->
                        <div class="col-md-6">
                            <label class="form-label" for="coupon-expiry">Expiration Date</label>
                            <input type="date" id="coupon-expiry" name="expires_at" class="form-control" value="2026-12-31">
                        </div>

                        <!-- Status Toggle -->
                        <div class="col-md-6 d-flex align-items-center mt-4">
                            <div class="form-check form-switch">
                                <input class="form-check-input" type="checkbox" id="couponActive" name="is_active" value="1" checked>
                                <label class="form-check-label fw-semibold" for="couponActive">Activate coupon immediately</label>
                            </div>
                        </div>
                    </div>
                </div>
                <div class="card-footer bg-light-subtle d-flex justify-content-end gap-2">
                    <a href="{{ route('admin.coupons.index') }}" class="btn btn-outline-secondary">Cancel</a>
                    <button type="submit" class="btn btn-primary">
                        <i class="bx bx-check me-1"></i> Save & Publish Coupon
                    </button>
                </div>
            </div>
        </form>
    </div>
</div>
@endsection
