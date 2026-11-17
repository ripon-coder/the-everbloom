@extends('layouts.admin')

@section('title', 'Store Settings')
@section('container_class', 'container-xxl')

@section('content')
<form action="{{ route('admin.settings.update') }}" method="POST" enctype="multipart/form-data">
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
                            <label class="form-label" for="store-logo">Store Logo (WebP)</label>
                            <input type="file" id="store-logo" name="logo" class="form-control" accept="image/*">
                        </div>
                        <div class="col-md-6">
                            <label class="form-label" for="store-favicon">Favicon</label>
                            <input type="file" id="store-favicon" name="favicon" class="form-control" accept="image/*">
                        </div>
                        <div class="col-12">
                            <label class="form-label" for="currency-preset">Currency Preset</label>
                            @php
                                $curCode = old('currency_code', $site['currency']['code'] ?? 'BDT');
                                $curSym = old('currency_symbol', $site['currency']['symbol'] ?? '৳');
                                $curDec = old('currency_decimals', $site['currency']['decimals'] ?? 0);
                                $curPos = old('currency_position', $site['currency']['position'] ?? 'before');
                                $curTz = old('timezone', $site['timezone'] ?? 'Asia/Dhaka');
                            @endphp
                            <select id="currency-preset" class="form-select" onchange="if(this.value){const [c,s]=this.value.split('|');document.getElementById('curr-code').value=c;document.getElementById('curr-symbol').value=s;}">
                                <option value="">Custom Currency</option>
                                <option value="BDT|৳" {{ $curCode === 'BDT' ? 'selected' : '' }}>Bangladeshi Taka — BDT (৳)</option>
                                <option value="USD|$" {{ $curCode === 'USD' ? 'selected' : '' }}>US Dollar — USD ($)</option>
                                <option value="GBP|£" {{ $curCode === 'GBP' ? 'selected' : '' }}>British Pound — GBP (£)</option>
                                <option value="EUR|€" {{ $curCode === 'EUR' ? 'selected' : '' }}>Euro — EUR (€)</option>
                                <option value="INR|₹" {{ $curCode === 'INR' ? 'selected' : '' }}>Indian Rupee — INR (₹)</option>
                            </select>
                        </div>
                        <div class="col-md-3">
                            <label class="form-label" for="curr-code">Code</label>
                            <input type="text" id="curr-code" name="currency_code" class="form-control text-uppercase" value="{{ $curCode }}">
                        </div>
                        <div class="col-md-3">
                            <label class="form-label" for="curr-symbol">Symbol</label>
                            <input type="text" id="curr-symbol" name="currency_symbol" class="form-control" value="{{ $curSym }}">
                        </div>
                        <div class="col-md-3">
                            <label class="form-label" for="curr-decimals">Decimals</label>
                            <input type="number" id="curr-decimals" name="currency_decimals" min="0" max="4" class="form-control" value="{{ $curDec }}">
                        </div>
                        <div class="col-md-3">
                            <label class="form-label" for="curr-position">Position</label>
                            <select id="curr-position" name="currency_position" class="form-select">
                                <option value="before" {{ $curPos === 'before' ? 'selected' : '' }}>Before (৳100)</option>
                                <option value="after" {{ $curPos === 'after' ? 'selected' : '' }}>After (100৳)</option>
                            </select>
                        </div>
                        <div class="col-12">
                            <label class="form-label" for="store-timezone">Store Timezone</label>
                            <select id="store-timezone" name="timezone" class="form-select">
                                @foreach(['Asia/Dhaka', 'UTC', 'Asia/Kolkata', 'Europe/London', 'America/New_York'] as $tz)
                                    <option value="{{ $tz }}" {{ $curTz === $tz ? 'selected' : '' }}>{{ $tz }}</option>
                                @endforeach
                            </select>
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
                            <input type="text" id="announcement-msg" name="announcement_message" class="form-control" value="{{ old('announcement_message', $site['announcement']['message'] ?? 'Free shipping on orders over ৳2,000') }}">
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

        <!-- Right Column: Shipping, Tax, Contact & Social -->
        <div class="col-lg-6">
            <!-- Card 3: Shipping & Returns & Tax -->
            <div class="card">
                <div class="card-header">
                    <h4 class="card-title mb-0">Shipping, Return & Tax Rules</h4>
                </div>
                <div class="card-body">
                    <div class="row g-3">
                        <div class="col-md-6">
                            <label class="form-label" for="free-ship-thresh">Free Shipping Threshold ({{ $currencySymbol }} {{ $currencyCode }})</label>
                            <div class="input-group">
                                <span class="input-group-text">{{ $currencySymbol }}</span>
                                <input type="number" step="0.01" id="free-ship-thresh" name="free_shipping_threshold" class="form-control" value="{{ old('free_shipping_threshold', $site['shipping']['free_shipping_threshold'] ?? 2000.00) }}">
                            </div>
                        </div>
                        <div class="col-md-6">
                            <label class="form-label" for="std-rate">Standard Rate ({{ $currencySymbol }} {{ $currencyCode }})</label>
                            <div class="input-group">
                                <span class="input-group-text">{{ $currencySymbol }}</span>
                                <input type="number" step="0.01" id="std-rate" name="standard_rate" class="form-control" value="{{ old('standard_rate', $site['shipping']['standard_rate'] ?? 60.00) }}">
                            </div>
                        </div>
                        <div class="col-md-6">
                            <label class="form-label" for="exp-rate">Express Rate ({{ $currencySymbol }} {{ $currencyCode }})</label>
                            <div class="input-group">
                                <span class="input-group-text">{{ $currencySymbol }}</span>
                                <input type="number" step="0.01" id="exp-rate" name="express_rate" class="form-control" value="{{ old('express_rate', $site['shipping']['express_rate'] ?? 120.00) }}">
                            </div>
                        </div>
                        <div class="col-md-6">
                            <label class="form-label" for="return-window">Return Policy Window (Days)</label>
                            <input type="number" id="return-window" name="returns_window_days" class="form-control" value="{{ old('returns_window_days', $site['returns']['window_days'] ?? 30) }}">
                        </div>
                        <div class="col-md-4">
                            <div class="form-check form-switch mt-4">
                                <input type="hidden" name="tax_enabled" value="0">
                                <input class="form-check-input" type="checkbox" id="taxEnabled" name="tax_enabled" value="1" {{ !empty($site['tax']['enabled']) ? 'checked' : '' }}>
                                <label class="form-check-label" for="taxEnabled">Enable VAT/Tax</label>
                            </div>
                        </div>
                        <div class="col-md-4">
                            <label class="form-label" for="tax-rate">Tax Rate (%)</label>
                            <input type="number" step="0.01" min="0" max="100" id="tax-rate" name="tax_rate" class="form-control" value="{{ old('tax_rate', $site['tax']['rate'] ?? 0) }}">
                        </div>
                        <div class="col-md-4">
                            <div class="form-check form-switch mt-4">
                                <input type="hidden" name="tax_inclusive" value="0">
                                <input class="form-check-input" type="checkbox" id="taxInclusive" name="tax_inclusive" value="1" {{ !empty($site['tax']['inclusive']) ? 'checked' : '' }}>
                                <label class="form-check-label" for="taxInclusive">Tax Inclusive</label>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Card 4: Concierge, Studio Contact & Social -->
            <div class="card">
                <div class="card-header">
                    <h4 class="card-title mb-0">Client Concierge & Social Links</h4>
                </div>
                <div class="card-body">
                    <div class="row g-3">
                        <div class="col-md-6">
                            <label class="form-label" for="contact-email">Concierge Email</label>
                            <input type="email" id="contact-email" name="contact_email" class="form-control" value="{{ old('contact_email', $site['contact']['email'] ?? 'concierge@the-everbloom.com') }}">
                        </div>
                        <div class="col-md-6">
                            <label class="form-label" for="contact-phone">Concierge Phone</label>
                            <input type="text" id="contact-phone" name="contact_phone" class="form-control" value="{{ old('contact_phone', $site['contact']['phone'] ?? '+880 1700-000000') }}">
                        </div>
                        <div class="col-12">
                            <label class="form-label" for="contact-hours">Studio Hours</label>
                            <input type="text" id="contact-hours" name="contact_hours" class="form-control" value="{{ old('contact_hours', $site['contact']['hours'] ?? 'Sat – Thu: 10:00 AM – 7:00 PM BST') }}">
                        </div>
                        <div class="col-12">
                            <label class="form-label" for="contact-address">Studio Physical Address</label>
                            <textarea id="contact-address" name="contact_address" class="form-control" rows="2">{{ old('contact_address', $site['contact']['address'] ?? 'Banani, Dhaka 1213, Bangladesh') }}</textarea>
                        </div>
                        <div class="col-md-4">
                            <label class="form-label" for="social-instagram">Instagram URL</label>
                            <input type="text" id="social-instagram" name="social_instagram" class="form-control" value="{{ old('social_instagram', $site['social']['instagram'] ?? '') }}" placeholder="https://instagram.com/...">
                        </div>
                        <div class="col-md-4">
                            <label class="form-label" for="social-facebook">Facebook URL</label>
                            <input type="text" id="social-facebook" name="social_facebook" class="form-control" value="{{ old('social_facebook', $site['social']['facebook'] ?? '') }}" placeholder="https://facebook.com/...">
                        </div>
                        <div class="col-md-4">
                            <label class="form-label" for="social-tiktok">TikTok URL</label>
                            <input type="text" id="social-tiktok" name="social_tiktok" class="form-control" value="{{ old('social_tiktok', $site['social']['tiktok'] ?? '') }}" placeholder="https://tiktok.com/@...">
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
