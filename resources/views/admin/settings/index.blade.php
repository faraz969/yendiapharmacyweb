@extends('admin.layouts.app')

@section('title', 'Settings')
@section('page-title', 'Settings')

@section('content')
<div class="card">
    <div class="card-header">
        <h5 class="mb-0"><i class="fas fa-cog me-2"></i>Site Settings</h5>
    </div>
    <div class="card-body">
        <form action="{{ route('admin.settings.update') }}" method="POST" enctype="multipart/form-data">
            @csrf
            @method('POST')

            <!-- Logo Settings -->
            <div class="card mb-3">
                <div class="card-header bg-light">
                    <h6 class="mb-0"><i class="fas fa-image me-2"></i>Logo & Favicon Settings</h6>
                </div>
                <div class="card-body">
                    <div class="row">
                        <div class="col-md-4">
                            <div class="mb-3">
                                <label for="favicon" class="form-label">Favicon</label>
                                @php
                                    $favicon = $settings['favicon']->value ?? null;
                                @endphp
                                @if($favicon)
                                    <div class="mb-2">
                                        <img src="{{ Storage::url($favicon) }}" alt="Favicon" style="max-height: 32px; max-width: 32px; border-radius: 5px;">
                                    </div>
                                    <div class="form-check mb-2">
                                        <input class="form-check-input" type="checkbox" id="remove_favicon" name="remove_favicon">
                                        <label class="form-check-label" for="remove_favicon">
                                            Remove favicon (use default)
                                        </label>
                                    </div>
                                @else
                                    <div class="mb-2">
                                        <i class="fas fa-globe fa-2x text-muted"></i>
                                        <small class="d-block text-muted mt-1">Currently using default favicon</small>
                                    </div>
                                @endif
                                <input type="file" class="form-control @error('favicon') is-invalid @enderror" id="favicon" name="favicon" accept="image/*">
                                @error('favicon')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                                <small class="form-text text-muted">Max size: 512KB. Formats: ICO, PNG. Recommended: 32x32px or 16x16px</small>
                            </div>
                        </div>
                        <div class="col-md-4">
                            <div class="mb-3">
                                <label for="header_logo" class="form-label">Header Logo</label>
                                @php
                                    $headerLogo = $settings['header_logo']->value ?? null;
                                @endphp
                                @if($headerLogo)
                                    <div class="mb-2">
                                        <img src="{{ Storage::url($headerLogo) }}" alt="Header Logo" style="max-height: 80px; border-radius: 5px;">
                                    </div>
                                    <div class="form-check mb-2">
                                        <input class="form-check-input" type="checkbox" id="remove_header_logo" name="remove_header_logo">
                                        <label class="form-check-label" for="remove_header_logo">
                                            Remove header logo (use default)
                                        </label>
                                    </div>
                                @else
                                    <div class="mb-2">
                                        <img src="{{ asset('logo.png') }}" alt="Default Logo" style="max-height: 80px; border-radius: 5px;">
                                        <small class="d-block text-muted mt-1">Currently using default logo</small>
                                    </div>
                                @endif
                                <input type="file" class="form-control @error('header_logo') is-invalid @enderror" id="header_logo" name="header_logo" accept="image/*">
                                @error('header_logo')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                                <small class="form-text text-muted">Max size: 2MB. Formats: JPEG, PNG, JPG, GIF, WEBP</small>
                            </div>
                        </div>
                        <div class="col-md-4">
                            <div class="mb-3">
                                <label for="footer_logo" class="form-label">Footer Logo</label>
                                @php
                                    $footerLogo = $settings['footer_logo']->value ?? null;
                                @endphp
                                @if($footerLogo)
                                    <div class="mb-2">
                                        <img src="{{ Storage::url($footerLogo) }}" alt="Footer Logo" style="max-height: 80px; border-radius: 5px;">
                                    </div>
                                    <div class="form-check mb-2">
                                        <input class="form-check-input" type="checkbox" id="remove_footer_logo" name="remove_footer_logo">
                                        <label class="form-check-label" for="remove_footer_logo">
                                            Remove footer logo (use default)
                                        </label>
                                    </div>
                                @else
                                    <div class="mb-2">
                                        <img src="{{ asset('logo.png') }}" alt="Default Logo" style="max-height: 80px; border-radius: 5px;">
                                        <small class="d-block text-muted mt-1">Currently using default logo</small>
                                    </div>
                                @endif
                                <input type="file" class="form-control @error('footer_logo') is-invalid @enderror" id="footer_logo" name="footer_logo" accept="image/*">
                                @error('footer_logo')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                                <small class="form-text text-muted">Max size: 2MB. Formats: JPEG, PNG, JPG, GIF, WEBP</small>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Footer Settings -->
            <div class="card mb-3">
                <div class="card-header bg-light">
                    <h6 class="mb-0"><i class="fas fa-copyright me-2"></i>Footer Settings</h6>
                </div>
                <div class="card-body">
                    <div class="row">
                        <div class="col-md-6">
                            <div class="mb-3">
                                <label for="copyright_year" class="form-label">Copyright Year <span class="text-danger">*</span></label>
                                <input type="text" class="form-control @error('copyright_year') is-invalid @enderror" id="copyright_year" name="copyright_year" value="{{ old('copyright_year', $settings['copyright_year']->value ?? date('Y')) }}" required>
                                @error('copyright_year')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                                <small class="form-text text-muted">Year displayed in "All rights reserved" footer text</small>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            <!-- App Store Links -->
            <div class="card mb-3">
                <div class="card-header bg-light">
                    <h6 class="mb-0"><i class="fas fa-mobile-alt me-2"></i>App Store Links</h6>
                </div>
                <div class="card-body">
                    <div class="row">
                        <div class="col-md-6">
                            <div class="mb-3">
                                <label for="app_store_url" class="form-label">Apple App Store URL</label>
                                <input type="url" class="form-control @error('app_store_url') is-invalid @enderror" id="app_store_url" name="app_store_url" value="{{ old('app_store_url', $settings['app_store_url']->value ?? '') }}" placeholder="https://apps.apple.com/...">
                                @error('app_store_url')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>
                        </div>
                        <div class="col-md-6">
                            <div class="mb-3">
                                <label for="play_store_url" class="form-label">Google Play Store URL</label>
                                <input type="url" class="form-control @error('play_store_url') is-invalid @enderror" id="play_store_url" name="play_store_url" value="{{ old('play_store_url', $settings['play_store_url']->value ?? '') }}" placeholder="https://play.google.com/...">
                                @error('play_store_url')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Contact Information -->
            <div class="card mb-3">
                <div class="card-header bg-light">
                    <h6 class="mb-0"><i class="fas fa-phone me-2"></i>Contact Information</h6>
                </div>
                <div class="card-body">
                    <div class="row">
                        <div class="col-md-6">
                            <div class="mb-3">
                                <label for="contact_phone" class="form-label">Contact Phone Number</label>
                                <input type="text" class="form-control @error('contact_phone') is-invalid @enderror" id="contact_phone" name="contact_phone" value="{{ old('contact_phone', $settings['contact_phone']->value ?? '+1 800 900') }}" placeholder="+1 800 900">
                                @error('contact_phone')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                                <small class="form-text text-muted">Displayed in the top utility bar</small>
                            </div>
                        </div>
                        <div class="col-md-6">
                            <div class="mb-3">
                                <label for="contact_email" class="form-label">Contact Email</label>
                                <input type="email" class="form-control @error('contact_email') is-invalid @enderror" id="contact_email" name="contact_email" value="{{ old('contact_email', $settings['contact_email']->value ?? 'info@pharmacystore.com') }}" placeholder="info@pharmacystore.com">
                                @error('contact_email')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>
                        </div>
                    </div>
                    <div class="row">
                        <div class="col-md-12">
                            <div class="mb-3">
                                <label for="topbar_tagline" class="form-label">Topbar Tagline</label>
                                <input type="text" class="form-control @error('topbar_tagline') is-invalid @enderror" id="topbar_tagline" name="topbar_tagline" value="{{ old('topbar_tagline', $settings['topbar_tagline']->value ?? 'Super Value Deals - Save more with coupons') }}" placeholder="Super Value Deals - Save more with coupons">
                                @error('topbar_tagline')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                                <small class="form-text text-muted">Tagline displayed in the center of the top utility bar</small>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Currency Settings -->
            <div class="card mb-3">
                <div class="card-header bg-light">
                    <h6 class="mb-0"><i class="fas fa-dollar-sign me-2"></i>Currency Settings</h6>
                </div>
                <div class="card-body">
                    <p class="text-muted small mb-3">Set the currency that will be used throughout the website and mobile app. The currency symbol will replace the $ sign wherever prices are displayed.</p>
                    <div class="row">
                        <div class="col-md-6">
                            <div class="mb-3">
                                <label for="currency" class="form-label">Currency Code <span class="text-danger">*</span></label>
                                <select class="form-select @error('currency') is-invalid @enderror" id="currency" name="currency" required>
                                    <option value="USD" {{ old('currency', $settings['currency']->value ?? 'USD') == 'USD' ? 'selected' : '' }}>USD - US Dollar</option>
                                    <option value="NGN" {{ old('currency', $settings['currency']->value ?? 'USD') == 'NGN' ? 'selected' : '' }}>NGN - Nigerian Naira</option>
                                    <option value="EUR" {{ old('currency', $settings['currency']->value ?? 'USD') == 'EUR' ? 'selected' : '' }}>EUR - Euro</option>
                                    <option value="GBP" {{ old('currency', $settings['currency']->value ?? 'USD') == 'GBP' ? 'selected' : '' }}>GBP - British Pound</option>
                                    <option value="CAD" {{ old('currency', $settings['currency']->value ?? 'USD') == 'CAD' ? 'selected' : '' }}>CAD - Canadian Dollar</option>
                                    <option value="AUD" {{ old('currency', $settings['currency']->value ?? 'USD') == 'AUD' ? 'selected' : '' }}>AUD - Australian Dollar</option>
                                    <option value="GHS" {{ old('currency', $settings['currency']->value ?? 'USD') == 'GHS' ? 'selected' : '' }}>GHS - Ghana Cedi</option>
                                </select>
                                @error('currency')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>
                        </div>
                        <div class="col-md-6">
                            <div class="mb-3">
                                <label for="currency_symbol" class="form-label">Currency Symbol <span class="text-danger">*</span></label>
                                <input type="text" class="form-control @error('currency_symbol') is-invalid @enderror" id="currency_symbol" name="currency_symbol" value="{{ old('currency_symbol', $settings['currency_symbol']->value ?? '$') }}" placeholder="$" required maxlength="10">
                                @error('currency_symbol')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                                <small class="form-text text-muted">Examples: $ (USD), ₦ (NGN), € (EUR), £ (GBP), ₵ (GHS)</small>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Navbar Categories -->
            <div class="card mb-3">
                <div class="card-header bg-light">
                    <h6 class="mb-0"><i class="fas fa-list me-2"></i>Navbar Categories</h6>
                </div>
                <div class="card-body">
                    <p class="text-muted small mb-3">Select which categories should appear in the main navigation bar. These categories will be clickable links that take users to the products page filtered by that category.</p>
                    <div class="mb-3">
                        <label class="form-label">Select Categories</label>
                        <div class="row">
                            @foreach($categories as $category)
                                <div class="col-md-4 mb-2">
                                    <div class="form-check">
                                        <input class="form-check-input" type="checkbox" name="navbar_categories[]" value="{{ $category->id }}" id="category_{{ $category->id }}" 
                                            {{ in_array($category->id, $navbarCategoryIds) ? 'checked' : '' }}>
                                        <label class="form-check-label" for="category_{{ $category->id }}">
                                            {{ $category->name }}
                                        </label>
                                    </div>
                                </div>
                            @endforeach
                        </div>
                        @if($categories->isEmpty())
                            <p class="text-muted">No active categories available. <a href="{{ route('admin.categories.create') }}">Create a category</a> first.</p>
                        @endif
                    </div>
                </div>
            </div>

            <div class="d-flex justify-content-end">
                <button type="submit" class="btn btn-primary">
                    <i class="fas fa-save me-2"></i>Save Settings
                </button>
            </div>
        </form>
    </div>
</div>
@endsection

