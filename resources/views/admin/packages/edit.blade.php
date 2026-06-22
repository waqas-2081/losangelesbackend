{{-- resources/views/admin/packages/edit.blade.php --}}
@extends('layouts.app')


@section('styles')
    <style>
        :root {
            --primary: #6c5ce7;
            --primary-dark: #5a4bd1;
            --success: #00b894;
            --danger: #d63031;
            --border: #e5e7eb;
            --bg: #f8f9fa;
            --card-bg: #ffffff;
            --text: #1a1a2e;
            --muted: #6b7280;
            --radius: 10px;
        }

        body {
            background: var(--bg);
        }

        .page-header {
            display: flex;
            align-items: flex-start;
            justify-content: space-between;
            margin-bottom: 24px;
        }

        .page-header h2 {
            font-size: 1.5rem;
            font-weight: 700;
            color: var(--text);
            margin: 0;
        }

        .breadcrumb-nav {
            font-size: 0.82rem;
            color: var(--muted);
            margin-top: 4px;
        }

        .breadcrumb-nav a {
            color: var(--muted);
            text-decoration: none;
        }

        .breadcrumb-nav a:hover {
            color: var(--primary);
        }

        .breadcrumb-nav span {
            margin: 0 5px;
        }

        .btn-back {
            background: var(--primary);
            color: #fff;
            border: none;
            padding: 9px 20px;
            border-radius: 8px;
            font-size: 0.875rem;
            font-weight: 500;
            text-decoration: none;
            transition: background 0.18s;
            white-space: nowrap;
        }

        .btn-back:hover {
            background: var(--primary-dark);
            color: #fff;
        }

        .layout-grid {
            display: grid;
            grid-template-columns: 1fr 320px;
            gap: 20px;
            margin-bottom: 20px;
        }

        .card-box {
            background: var(--card-bg);
            border-radius: var(--radius);
            border: 1px solid var(--border);
            padding: 28px;
        }

        .card-box h5 {
            font-size: 1rem;
            font-weight: 600;
            margin-bottom: 20px;
            color: var(--text);
        }

        .form-label {
            font-size: 0.82rem;
            font-weight: 500;
            color: var(--text);
            margin-bottom: 5px;
        }

        .form-control,
        .form-select {
            border: 1.5px solid var(--border);
            border-radius: 7px;
            font-size: 0.9rem;
            padding: 9px 12px;
            color: var(--text);
            transition: border-color 0.15s;
        }

        .form-control:focus,
        .form-select:focus {
            border-color: var(--primary);
            box-shadow: 0 0 0 3px rgba(108, 92, 231, 0.1);
            outline: none;
        }

        .form-row-2 {
            display: grid;
            grid-template-columns: 1fr 1fr;
            gap: 14px;
        }

        .status-row {
            display: flex;
            align-items: center;
            gap: 10px;
            margin-top: 6px;
        }

        .toggle-switch {
            position: relative;
            display: inline-block;
            width: 42px;
            height: 24px;
        }

        .toggle-switch input {
            display: none;
        }

        .toggle-slider {
            position: absolute;
            inset: 0;
            background: #ccc;
            border-radius: 24px;
            cursor: pointer;
            transition: background 0.2s;
        }

        .toggle-slider:before {
            content: '';
            position: absolute;
            width: 18px;
            height: 18px;
            background: #fff;
            border-radius: 50%;
            top: 3px;
            left: 3px;
            transition: transform 0.2s;
        }

        .toggle-switch input:checked+.toggle-slider {
            background: var(--primary);
        }

        .toggle-switch input:checked+.toggle-slider:before {
            transform: translateX(18px);
        }

        .toggle-label {
            font-size: 0.88rem;
            color: var(--muted);
            font-weight: 500;
        }

        .footer-bar {
            display: flex;
            align-items: center;
            justify-content: space-between;
            padding: 14px 28px;
            background: var(--card-bg);
            border: 1px solid var(--border);
            border-radius: var(--radius);
            margin-bottom: 20px;
            font-size: 0.8rem;
            color: var(--muted);
        }

        .footer-actions {
            display: flex;
            gap: 10px;
        }

        .btn-discard {
            padding: 8px 18px;
            border: 1.5px solid var(--border);
            border-radius: 7px;
            background: #fff;
            color: var(--text);
            font-size: 0.875rem;
            font-weight: 500;
            cursor: pointer;
            transition: background 0.15s, border-color 0.15s;
            text-decoration: none;
        }

        .btn-discard:hover {
            background: var(--bg);
        }

        .btn-save {
            padding: 8px 20px;
            border: none;
            border-radius: 7px;
            background: var(--primary);
            color: #fff;
            font-size: 0.875rem;
            font-weight: 600;
            cursor: pointer;
            transition: background 0.15s;
        }

        .btn-save:hover {
            background: var(--primary-dark);
        }

        .alert-success {
            background: #d1fae5;
            color: #065f46;
            border: 1px solid #a7f3d0;
            border-radius: 8px;
            padding: 12px 16px;
            margin-bottom: 16px;
            font-size: 0.875rem;
        }

        .alert-danger {
            background: #fee2e2;
            color: #991b1b;
            border: 1px solid #fca5a5;
            border-radius: 8px;
            padding: 12px 16px;
            margin-bottom: 16px;
            font-size: 0.875rem;
        }

        .text-danger {
            color: var(--danger) !important;
        }

        .feature-flex-row {
            display: flex;
            gap: 10px;
            margin-bottom: 10px;
            align-items: center;
        }

        .feature-flex-row .feature-input {
            flex: 1;
        }

        .remove-feature-btn {
            border: none;
            background: var(--danger);
            color: #fff !important;
            border-radius: 6px;
            padding: 6px 12px;
            cursor: pointer;
            font-size: 1rem;
            transition: background 0.15s;
        }

        .remove-feature-btn:hover {
            background: #b71c1c;
        }

        #addFeatureBtn {
            background: var(--primary);
            color: #fff;
            border: none;
            border-radius: 6px;
            font-size: 0.96rem;
            padding: 7px 18px;
        }

        #addFeatureBtn:hover {
            background: var(--primary-dark);
        }
    </style>
@endsection

@section('content')

    <div class="container-fluid py-4" style="max-width: 1000px; margin: 0 auto;">

        @if(session('success'))
            <div class="alert-success">{{ session('success') }}</div>
        @endif
        @if($errors->any())
            <div class="alert-danger">
                @foreach($errors->all() as $error)
                    {{ $error }}<br>
                @endforeach
            </div>
        @endif

        <!-- Page Header -->
        <div class="page-header">
            <div>
                <h2>Edit Package</h2>
                <div class="breadcrumb-nav">
                    <a href="{{ route('admin.dashboard') }}">Home</a><span>&rsaquo;</span>
                    <a href="{{ route('admin.packages.index') }}">Packages</a><span>&rsaquo;</span>
                    <span>Edit</span>
                </div>
            </div>
            <a href="{{ route('admin.packages.index') }}" class="btn-back">Back to Packages</a>
        </div>

        <!-- Main Package Form -->
        <form action="{{ route('admin.packages.update', $package->id) }}" method="POST" id="packageForm" autocomplete="off">
            @csrf
            @method('PUT')

            <div class="layout-grid">
                <!-- Left: Main Info -->
                <div class="card-box">
                    <h5>Package Information</h5>
                    <div class="mb-3">
                        <label class="form-label">Package Name <span class="text-danger">*</span></label>
                        <input type="text" name="name" id="packageName" class="form-control" value="{{ old('name', $package->name) }}"
                            required>
                    </div>
                    <div class="form-row-2 mb-3">
                        <div>
                            <label class="form-label">Service Type <span class="text-danger">*</span></label>
                            <select name="service_type" id="serviceType" class="form-select required" required>
                                <option value="">Select Service Type</option>
                                <option value="logo-design-services" {{ old('service_type', $package->service_type ?? '') == 'logo-design-services' ? 'selected' : '' }}>Logo Design</option>
                                <option value="website-design-development-services" {{ old('service_type', $package->service_type ?? '') == 'website-design-development-services' ? 'selected' : '' }}>Website Development</option>
                                <option value="video-animation-services" {{ old('service_type', $package->service_type ?? '') == 'video-animation-services' ? 'selected' : '' }}>Video Animation</option>
                                <option value="mobile-app-development-services" {{ old('service_type', $package->service_type ?? '') == 'mobile-app-development-services' ? 'selected' : '' }}>Mobile App Development</option>
                                <option value="social-media-marketing-services" {{ old('service_type', $package->service_type ?? '') == 'social-media-marketing-services' ? 'selected' : '' }}>Social Media Marketing</option>
                                <option value="search-engine-optimization-services" {{ old('service_type', $package->service_type ?? '') == 'search-engine-optimization-services' ? 'selected' : '' }}>SEO</option>
                            </select>
                        </div>
                        <div>
                            <label class="form-label">Price ($) <span class="text-danger">*</span></label>
                            <input type="number" step="0.01" min="0" name="price" id="packagePrice" class="form-control"
                                value="{{ old('price', $package->price) }}" required>
                        </div>
                    </div>
                    <div class="form-row-2 mb-3">
                        <div>
                            <label class="form-label">Price Type <span class="text-danger">*</span></label>
                            <select name="price_type" id="priceType" class="form-select required" required>
                                <option value="one_time" {{ old('price_type', $package->price_type) == 'one_time' ? 'selected' : '' }}>One Time
                                    Payment</option>
                                <option value="project" {{ old('price_type', $package->price_type) == 'project' ? 'selected' : '' }}>Per Project
                                </option>
                            </select>
                        </div>
                        <div>
                            <label class="form-label">Sort Order</label>
                            <input type="number" name="sort_order" class="form-control" placeholder="0"
                                value="{{ old('sort_order', $package->sort_order) }}">
                        </div>
                    </div>
                    <div class="form-row-2 mb-3">
                        <div>
                            <label class="form-label">Badge (Optional)</label>
                            <input type="text" name="badge" id="packageBadge" class="form-control"
                                value="{{ old('badge', $package->badge) }}" placeholder="e.g., MOST POPULAR, BEST SELLER">
                        </div>
                        <div>
                            <label class="form-label">Status</label>
                            <div class="status-row">
                                <label class="toggle-switch">
                                    <input type="hidden" name="is_active" value="0">
                                    <input type="checkbox" name="is_active" id="isActive" value="1" {{ old('is_active', $package->is_active) ? 'checked' : '' }}>
                                    <span class="toggle-slider"></span>
                                </label>
                                <span class="toggle-label"
                                    id="activeLabel">{{ old('is_active', $package->is_active) ? 'Active' : 'Inactive' }}</span>
                            </div>
                        </div>
                    </div>
                    <!-- Features Card -->
                    <div style="margin-bottom: 28px;">
                        <label class="form-label">Package Features <span class="text-danger">*</span></label>
                        <div id="featuresContainer">
                            @php 
                                $oldFeatures = old('features', $package->features ?? ['']);
                                if (!is_array($oldFeatures)) {
                                    $oldFeatures = json_decode($oldFeatures, true) ?: [''];
                                }
                            @endphp
                            @foreach($oldFeatures as $index => $feature)
                                <div class="feature-flex-row">
                                    <input type="text" name="features[]" class="form-control feature-input"
                                        placeholder="Enter a feature" value="{{ $feature }}" required>
                                    <button type="button" class="remove-feature-btn"
                                        style="display: {{ $index === 0 && count($oldFeatures) === 1 ? 'none' : 'inline-block' }};"><i
                                            class="fas fa-trash-alt"></i></button>
                                </div>
                            @endforeach
                        </div>
                        <button type="button" id="addFeatureBtn" style="margin-top: 8px;"><i class="fas fa-plus"></i> Add
                            Feature</button>
                        <div>
                            <small class="text-muted"><i class="fas fa-info-circle me-1"></i>Enter at least one feature. Add
                                asterisk (*) for features with footnotes.</small>
                        </div>
                        @error('features')
                            <div class="text-danger small mt-2">{{ $message }}</div>
                        @enderror
                    </div>
                </div>
                <!-- Right Bar: Tips etc. -->
                <div class="card-box" style="height: fit-content;">
                    <h5>Quick Actions</h5>
                    <button type="submit" class="btn-save mb-3 w-100"><i class="fas fa-save me-2"></i> Update
                        Package</button>
                    <a href="{{ route('admin.packages.index') }}" class="btn-discard w-100">Cancel</a>
                    <hr style="margin: 24px 0;">
                    <h6 class="fw-bold mb-3" style="font-size: 1rem;color:var(--primary);">
                        <i class="fas fa-lightbulb text-warning"></i> Tips
                    </h6>
                    <ul style="font-size:0.93rem; color:var(--muted); padding-left:17px; margin-bottom:0;">
                        <li class="mb-2">Use clear and descriptive package names</li>
                        <li class="mb-2">List features in order of importance</li>
                        <li class="mb-2">Add a badge to highlight popular packages</li>
                        <li class="mb-2">Set sort order for display</li>
                        <li class="mb-2">Preview updates in real-time as you type</li>
                    </ul>
                </div>
            </div>

            <!-- Preview Section -->
            <div class="card-box" style="margin-bottom:25px;">
                <h5>Live Preview</h5>
                <div style="max-width:350px;background:var(--bg);border-radius:10px;margin:0 auto;">
                    <div class="position-relative" style="text-align: center; margin-bottom: -10px;">
                        <div class="preview-badge" style="display: none;">
                            <span class="badge px-3 py-2 preview-badge-text"
                                style="background: #ffeaa7; color: #636363; border-radius: 20px; font-weight: 600; font-size: 1rem"></span>
                        </div>
                    </div>
                    <div style="padding: 20px 18px 0 18px;">
                        <h4 class="card-title text-center mb-3 preview-name" style="font-weight:600">Package Name</h4>
                        <div class="text-center mb-3">
                            <span class="display-6 fw-bold preview-price"
                                style="color:var(--primary); font-size:2rem;">$0.00</span>
                            <span class="text-muted preview-price-type">/project</span>
                        </div>
                        <ul class="list-unstyled preview-features" style="min-height: 34px;">
                            <li class="text-muted text-center">Features will appear here</li>
                        </ul>
                    </div>
                    <div class="card-footer" style="background:transparent; border:none; padding:20px 0 18px 0;">
                        <button class="btn-save w-100" disabled style="opacity:.66;">Get Started</button>
                    </div>
                </div>
            </div>

            <div class="footer-bar">
                <div>&nbsp;</div>
                <div class="footer-actions">
                    <a href="{{ route('admin.packages.index') }}" class="btn-discard">Cancel</a>
                    <button type="submit" class="btn-save">Update Package</button>
                </div>
            </div>
        </form>
    </div>

@endsection

@section('scripts')
    <script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
    <script>
        // Status active label
        document.getElementById('isActive')?.addEventListener('change', function () {
            const label = document.getElementById('activeLabel');
            if (label) label.textContent = this.checked ? 'Active' : 'Inactive';
        });

        // ADD/REMOVE Package Features DYNAMIC
        function handleRemoveBtnsDisplay() {
            let items = $('#featuresContainer .feature-flex-row');
            if (items.length === 1) {
                items.find('.remove-feature-btn').hide();
            } else {
                items.find('.remove-feature-btn').show();
            }
        }

        function updatePreview() {
            // Name
            const packageName = $('#packageName').val() || 'Package Name';
            $('.preview-name').text(packageName);

            // Badge
            const badge = $('#packageBadge').val();
            if (badge && badge.trim() !== '') {
                $('.preview-badge').show();
                $('.preview-badge-text').text(badge);
            } else {
                $('.preview-badge').hide();
            }

            // Price
            const price = $('#packagePrice').val() || '0.00';
            $('.preview-price').text('$' + parseFloat(price).toFixed(2));

            // Price Type
            const priceType = $('#priceType').val();
            const priceTypeText = priceType === 'one_time' ? '/one time' : '/project';
            $('.preview-price-type').text(priceTypeText);

            // Features
            let features = [];
            $('.feature-input').each(function () {
                const val = $(this).val();
                if (val && val.trim() !== '') features.push(val);
            });
            let html = '';
            if (features.length > 0) {
                features.forEach(function (f) {
                    html += `<li class="mb-2"><i class="fas fa-check" style="color:var(--success);margin-right:7px;font-size:1.1em"></i>${f}</li>`;
                });
            } else {
                html = '<li class="text-muted text-center">No features added yet</li>';
            }
            $('.preview-features').html(html);

            handleRemoveBtnsDisplay();
        }

        $(document).ready(function () {
            // Init remove btn view
            handleRemoveBtnsDisplay();
            updatePreview();

            $('#addFeatureBtn').click(function () {
                const row = `
                    <div class="feature-flex-row">
                        <input type="text" name="features[]" class="form-control feature-input" placeholder="Enter a feature" required>
                        <button type="button" class="remove-feature-btn"><i class="fas fa-trash-alt"></i></button>
                    </div>
                `;
                $('#featuresContainer').append(row);
                handleRemoveBtnsDisplay();
                updatePreview();
                // Focus last input
                $('#featuresContainer .feature-input').last().focus();
            });

            // Remove feature row
            $(document).on('click', '.remove-feature-btn', function () {
                if ($('#featuresContainer .feature-flex-row').length > 1) {
                    $(this).closest('.feature-flex-row').remove();
                    handleRemoveBtnsDisplay();
                    updatePreview();
                } else {
                    // prevent remove last
                    $(this).blur();
                }
            });

            // Add preview updates on any input
            $('#packageName, #packageBadge, #packagePrice, #priceType').on('input change', function () {
                updatePreview();
            });
            $(document).on('input', '.feature-input', updatePreview);

            // Enter key in feature adds new field if filled, else ignore
            $(document).on('keypress', '.feature-input', function (e) {
                if (e.which === 13) {
                    e.preventDefault();
                    $('#addFeatureBtn').click();
                }
            });

            // Validation on submit
            $('#packageForm').on('submit', function (e) {
                let filled = $('.feature-input').filter(function () {
                    return $(this).val().trim() !== '';
                }).length;
                if (filled === 0) {
                    e.preventDefault();
                    alert('Please add at least one feature in the package.');
                    return false;
                }
                let allFilled = true;
                $('.feature-input').each(function () {
                    if ($(this).val().trim() === '') {
                        $(this).addClass('is-invalid');
                        allFilled = false;
                    } else {
                        $(this).removeClass('is-invalid');
                    }
                });
                if (!allFilled) {
                    e.preventDefault();
                    alert('Please fill in all feature fields or remove empty ones.');
                    return false;
                }
                return true;
            });
            // Remove red border as soon as correcting
            $(document).on('input', '.feature-input', function () {
                $(this).removeClass('is-invalid');
            });

            // On page load, preview right away
            updatePreview();
        });
    </script>

@endsection