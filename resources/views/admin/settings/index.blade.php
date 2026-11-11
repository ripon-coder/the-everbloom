@extends('layouts.admin')

@section('title', 'Store Settings')
@section('container_class', 'container-xxl')

@section('content')
<form action="{{ route('admin.settings.update') }}" method="POST">
    @csrf

    <div class="row">
        <!-- Left Column: Identity & Announcements -->
        <div class="col-lg-6">
            <!-- Card 1: Brand Identity -->
            <div class="card">
                <div class="card-header">
                    <h4 class="card-title mb-0">Brand Identity & Currency</h4>
                </div>
                <div class="card-body">
                    <div class="row g-3">
                        <div class="col-12">
                            <label class="form-label" for="store-name">Brand Name</label>
                            <input type="text" id="store-name" name="name" class="form-control" value="{{ old('name', $site['name'] ?? 'The Everbloom') }}" required>
                        </div>
                        <div class="col-12">
                            <label class="form-label" for="store-tagline">Brand Tagline</label>
                            <input type="text" id="store-tagline" name="tagline" class="form-control" value="{{ old('tagline', $site['tagline'] ?? '') }}">
                        </div>
                        <div class="col-md-6">
                            <label class="form-label" for="curr-code">Currency Code</label>
                            <input type="text" id="curr-code" name="currency_code" class="form-control text-uppercase" value="{{ old('currency_code', $site['currency']['code'] ?? 'USD') }}">
                        </div>
                        <div class="col-md-6">
                            <label class="form-label" for="curr-symbol">Currency Symbol</label>
                            <input type="text" id="curr-symbol" name="currency_symbol" class="form-control" value="{{ old('currency_symbol', $site['currency']['symbol'] ?? '$') }}">
                        </div>
                    </div>
                </div>
            </div>

            <!-- Card 2: Top Announcement Bar -->
            <div class="card">
                <div class="card-header">
                    <h4 class="card-title mb-0">Storefront Announcement Bar</h4>
                </div>
                <div class="card-body">
                    <div class="row g-3">
                        <div class="col-12">
                            <div class="form-check form-switch mb-2">
                                <input class="form-check-input" type="checkbox" id="announcementEnabled" name="announcement_enabled" value="1" {{ !empty($site['announcement']['enabled']) ? 'checked' : '' }}>
                                <label class="form-check-label fw-semibold" for="announcementEnabled">Display Announcement on Storefront</label>
                            </div>
                        </div>
                        <div class="col-12">
                            <label class="form-label" for="announcement-msg">Announcement Text</label>
                            <input type="text" id="announcement-msg" name="announcement_message" class="form-control" value="{{ old('announcement_message', $site['announcement']['message'] ?? 'Free shipping on orders over $75') }}">
                        </div>
                        <div class="col-md-6">
                            <label class="form-label" for="announcement-link">Call-to-Action Link</label>
                            <input type="text" id="announcement-link" name="announcement_link" class="form-control" value="{{ old('announcement_link', $site['announcement']['link'] ?? '/shop') }}">
                        </div>
                        <div class="col-md-6">
                            <label class="form-label" for="announcement-text">Button Text</label>
                            <input type="text" id="announcement-text" name="announcement_link_text" class="form-control" value="{{ old('announcement_link_text', $site['announcement']['link_text'] ?? 'Shop Now') }}">
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <!-- Right Column: Shipping & Contact Studio -->
        <div class="col-lg-6">
            <!-- Card 3: Shipping & Returns -->
            <div class="card">
                <div class="card-header">
                    <h4 class="card-title mb-0">Shipping & Return Rules</h4>
                </div>
                <div class="card-body">
                    <div class="row g-3">
                        <div class="col-md-6">
                            <label class="form-label" for="free-ship-thresh">Free Shipping Threshold ($ USD)</label>
                            <div class="input-group">
                                <span class="input-group-text">$</span>
                                <input type="number" step="0.01" id="free-ship-thresh" name="free_shipping_threshold" class="form-control" value="{{ old('free_shipping_threshold', $site['shipping']['free_shipping_threshold'] ?? 75.00) }}">
                            </div>
                        </div>
                        <div class="col-md-6">
                            <label class="form-label" for="std-rate">Standard Ground Rate ($ USD)</label>
                            <div class="input-group">
                                <span class="input-group-text">$</span>
                                <input type="number" step="0.01" id="std-rate" name="standard_rate" class="form-control" value="{{ old('standard_rate', $site['shipping']['standard_rate'] ?? 6.00) }}">
                            </div>
                        </div>
                        <div class="col-md-6">
                            <label class="form-label" for="exp-rate">Express Courier Rate ($ USD)</label>
                            <div class="input-group">
                                <span class="input-group-text">$</span>
                                <input type="number" step="0.01" id="exp-rate" name="express_rate" class="form-control" value="{{ old('express_rate', $site['shipping']['express_rate'] ?? 15.00) }}">
                            </div>
                        </div>
                        <div class="col-md-6">
                            <label class="form-label" for="return-window">Return Policy Window (Days)</label>
                            <input type="number" id="return-window" name="returns_window_days" class="form-control" value="{{ old('returns_window_days', $site['returns']['window_days'] ?? 30) }}">
                        </div>
                    </div>
                </div>
            </div>

            <!-- Card 4: Concierge & Studio Contact -->
            <div class="card">
                <div class="card-header">
                    <h4 class="card-title mb-0">Client Concierge & Studio Lab</h4>
                </div>
                <div class="card-body">
                    <div class="row g-3">
                        <div class="col-md-6">
                            <label class="form-label" for="contact-email">Concierge Email</label>
                            <input type="email" id="contact-email" name="contact_email" class="form-control" value="{{ old('contact_email', $site['contact']['email'] ?? 'concierge@the-everbloom.com') }}">
                        </div>
                        <div class="col-md-6">
                            <label class="form-label" for="contact-phone">Concierge Phone</label>
                            <input type="text" id="contact-phone" name="contact_phone" class="form-control" value="{{ old('contact_phone', $site['contact']['phone'] ?? '+1 (800) 412-8874') }}">
                        </div>
                        <div class="col-12">
                            <label class="form-label" for="contact-hours">Studio Hours</label>
                            <input type="text" id="contact-hours" name="contact_hours" class="form-control" value="{{ old('contact_hours', $site['contact']['hours'] ?? 'Mon – Fri: 9:00 AM – 6:00 PM EST') }}">
                        </div>
                        <div class="col-12">
                            <label class="form-label" for="contact-address">Botanical Studio Physical Address</label>
                            <textarea id="contact-address" name="contact_address" class="form-control" rows="2">{{ old('contact_address', $site['contact']['address'] ?? '740 Botanical Way, Suite 400, Portland, OR 97201') }}</textarea>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <!-- Bottom Actions -->
        <div class="col-12">
            <div class="p-3 bg-light rounded d-flex justify-content-end gap-2 mb-4">
                <button type="submit" class="btn btn-primary">
                    <i class="bx bx-check-double me-1"></i> Save Store Settings
                </button>
            </div>
        </div>
    </div>
</form>
@endsection
