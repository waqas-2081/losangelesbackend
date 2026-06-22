{{-- resources/views/admin/portfolios/edit.blade.php --}}
@extends('layouts.app')

@section('title', 'Edit Portfolio Item')

@section('styles')
<style>
    :root {
        --primary: #6c5ce7;
        --primary-dark: #5a4bd1;
        --border: #e5e7eb;
        --bg: #f8f9fa;
        --card-bg: #ffffff;
        --text: #1a1a2e;
        --muted: #6b7280;
        --radius: 10px;
    }

    * {
        box-sizing: border-box;
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
        display: inline-block;
    }

    .btn-back:hover {
        background: var(--primary-dark);
        color: #fff;
    }

    .card-box {
        background: var(--card-bg);
        border-radius: var(--radius);
        border: 1px solid var(--border);
        padding: 28px;
        margin-bottom: 20px;
        box-shadow: 0 1px 3px rgba(0,0,0,0.05);
    }

    .card-box h5 {
        font-size: 1rem;
        font-weight: 600;
        margin-bottom: 20px;
        color: var(--text);
        display: flex;
        align-items: center;
        gap: 8px;
    }

    .form-label {
        font-size: 0.875rem;
        font-weight: 500;
        color: var(--text);
        margin-bottom: 5px;
        display: block;
    }

    .form-control,
    .form-select {
        border: 1.5px solid var(--border);
        border-radius: 7px;
        font-size: 0.9rem;
        padding: 9px 12px;
        color: var(--text);
        width: 100%;
        background: #fff;
    }

    .form-control:focus,
    .form-select:focus {
        border-color: var(--primary);
        box-shadow: 0 0 0 3px rgba(108, 92, 231, 0.1);
        outline: none;
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

    .toggle-switch input:checked + .toggle-slider {
        background: var(--primary);
    }

    .toggle-switch input:checked + .toggle-slider:before {
        transform: translateX(18px);
    }

    .toggle-label {
        font-size: 0.88rem;
        color: var(--muted);
        font-weight: 500;
    }

    .current-image-container {
        margin-bottom: 15px;
        padding: 15px;
        background: #f8f9fa;
        border-radius: 8px;
        border: 1px solid var(--border);
    }

    .current-image-label {
        font-size: 0.8rem;
        color: var(--muted);
        margin-bottom: 8px;
        text-transform: uppercase;
        letter-spacing: 0.5px;
    }

    .current-image {
        position: relative;
        display: inline-block;
    }

    .current-image img {
        max-width: 200px;
        max-height: 200px;
        border-radius: 8px;
        border: 2px solid var(--border);
        box-shadow: 0 2px 4px rgba(0,0,0,0.05);
        object-fit: cover;
    }

    .no-image {
        padding: 20px;
        background: #fff;
        border-radius: 8px;
        border: 1px dashed var(--border);
        text-align: center;
        color: var(--muted);
    }

    .image-preview {
        margin-top: 15px;
        max-width: 200px;
    }

    .image-preview img {
        width: 100%;
        border-radius: 8px;
        border: 2px solid var(--primary);
        box-shadow: 0 2px 4px rgba(108, 92, 231, 0.1);
    }

    .preview-label {
        font-size: 0.75rem;
        color: var(--primary);
        margin-top: 5px;
        text-align: center;
    }

    .footer-bar {
        display: flex;
        align-items: center;
        justify-content: space-between;
        padding: 14px 28px;
        background: var(--card-bg);
        border: 1px solid var(--border);
        border-radius: var(--radius);
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
        text-decoration: none;
        display: inline-block;
        transition: all 0.2s;
    }

    .btn-discard:hover {
        background: var(--bg);
        border-color: var(--muted);
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
        transition: background 0.2s;
    }

    .btn-save:hover {
        background: var(--primary-dark);
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
        color: #d63031 !important;
    }

    .text-muted {
        color: var(--muted) !important;
        font-size: 0.8rem;
    }

    .mb-3 {
        margin-bottom: 20px;
    }

    .info-text {
        background: #f0f9ff;
        border-left: 3px solid var(--primary);
        padding: 8px 12px;
        border-radius: 5px;
        margin-top: 8px;
        font-size: 0.85rem;
        color: var(--muted);
    }

    .file-input-wrapper {
        position: relative;
    }

    .file-input-wrapper input[type="file"] {
        padding: 8px 12px;
    }

    .badge {
        display: inline-block;
        padding: 2px 8px;
        border-radius: 4px;
        font-size: 0.7rem;
        font-weight: 600;
        text-transform: uppercase;
        letter-spacing: 0.3px;
    }

    .badge-info {
        background: #e0e7ff;
        color: var(--primary);
    }
</style>
@endsection

@section('content')
<div class="container-fluid py-4" style="max-width: 800px; margin: 0 auto;">

    {{-- Validation Errors --}}
    @if($errors->any())
        <div class="alert-danger">
            <strong>⚠️ Please fix the following errors:</strong><br>
            @foreach($errors->all() as $error)
                • {{ $error }}<br>
            @endforeach
        </div>
    @endif

    {{-- Page Header --}}
    <div class="page-header">
        <div>
            <h2>✏️ Edit Portfolio Item</h2>
            <div class="breadcrumb-nav">
                <a href="{{ route('admin.dashboard') }}">Dashboard</a> › 
                <a href="{{ route('admin.portfolios.index') }}">Portfolio</a> › 
                <span>Edit #{{ $portfolio->id }}</span>
            </div>
        </div>
        <a href="{{ route('admin.portfolios.index') }}" class="btn-back">← Back to Portfolio</a>
    </div>

    {{-- Form --}}
    <form action="{{ route('admin.portfolios.update', $portfolio) }}" method="POST" enctype="multipart/form-data">
        @csrf
        @method('PUT')

        <div class="card-box">
            <h5>
                <span>📋 Portfolio Information</span>
                <span class="badge badge-info">ID: {{ $portfolio->id }}</span>
            </h5>

            {{-- Category --}}
            <div class="mb-3">
                <label class="form-label">Category <span class="text-danger">*</span></label>
                <select name="category" class="form-select" required>
                    <option value="">-- Select Category --</option>
                    @foreach($categories as $cat)
                        <option value="{{ $cat }}" {{ old('category', $portfolio->category) == $cat ? 'selected' : '' }}>
                            {{ $cat }}
                        </option>
                    @endforeach
                </select>
            </div>

            {{-- Current Image --}}
            <div class="mb-3">
                <label class="form-label">Current Image</label>
                <div class="current-image-container">
                    <div class="current-image-label">📸 CURRENT IMAGE</div>
                    @if($portfolio->image)
                        <div class="current-image">
                            <img src="{{ asset('storage/' . $portfolio->image) }}" alt="Current Portfolio Image">
                        </div>
                        <div class="text-muted" style="margin-top: 8px;">
                            Path: {{ $portfolio->image }}
                        </div>
                    @else
                        <div class="no-image">
                            <div style="font-size: 32px; margin-bottom: 8px;">🖼️</div>
                            <p class="text-muted" style="margin: 0;">No image uploaded yet</p>
                        </div>
                    @endif
                </div>
            </div>

            {{-- New Image Upload --}}
            <div class="mb-3">
                <label class="form-label">Change Image (Optional)</label>
                <div class="file-input-wrapper">
                    <input type="file" 
                           name="image" 
                           id="imageInput"
                           class="form-control" 
                           accept="image/jpeg,image/png,image/jpg,image/gif,image/webp">
                </div>
                <small class="text-muted">
                    💡 Leave empty to keep current image • Max size: 2MB
                </small>
                
                {{-- Image Preview --}}
                <div class="image-preview" id="imagePreview" style="display: none;">
                    <img src="" alt="New Image Preview">
                    <div class="preview-label">✨ NEW IMAGE PREVIEW</div>
                </div>
            </div>

            {{-- Status --}}
            <div class="mb-3">
                <label class="form-label">Status</label>
                <div class="status-row">
                    <label class="toggle-switch">
                        <input type="checkbox" name="is_active" id="isActive" value="1" {{ old('is_active', $portfolio->is_active) ? 'checked' : '' }}>
                        <span class="toggle-slider"></span>
                    </label>
                    <span class="toggle-label" id="activeLabel">{{ old('is_active', $portfolio->is_active) ? '🟢 Active' : '🔴 Inactive' }}</span>
                </div>
                <div class="info-text">
                    💡 Active items will be visible on the frontend
                </div>
            </div>

            {{-- Sort Order --}}
            <div class="mb-3">
                <label class="form-label">Sort Order</label>
                <input type="number" 
                       name="sort_order" 
                       class="form-control" 
                       value="{{ old('sort_order', $portfolio->sort_order) }}" 
                       min="0"
                       placeholder="0">
                <div class="info-text">
                    💡 Lower numbers appear first in the listing
                </div>
            </div>

            {{-- Metadata Info --}}
            <div style="margin-top: 20px; padding-top: 20px; border-top: 1px solid var(--border);">
                <div style="display: flex; gap: 20px; font-size: 0.8rem; color: var(--muted);">
                    <div>
                        <strong>Created:</strong> {{ $portfolio->created_at->format('M d, Y H:i') }}
                    </div>
                    <div>
                        <strong>Last Updated:</strong> {{ $portfolio->updated_at->format('M d, Y H:i') }}
                    </div>
                </div>
            </div>
        </div>

        {{-- Footer --}}
        <div class="footer-bar">
            <div>
                <a href="{{ route('admin.portfolios.index') }}" class="btn-discard">← Cancel</a>
            </div>
            <div style="display: flex; gap: 10px;">
                <button type="submit" class="btn-save">💾 Update Portfolio Item</button>
            </div>
        </div>
    </form>
</div>
@endsection

@section('scripts')
<script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
<script>
$(document).ready(function() {
    // Toggle label update
    $('#isActive').change(function() {
        $('#activeLabel').text(this.checked ? '🟢 Active' : '🔴 Inactive');
    });

    // Image preview
    $('#imageInput').change(function() {
        const file = this.files[0];
        if (file) {
            // Check file size
            if (file.size > 2 * 1024 * 1024) {
                alert('File size should be less than 2MB');
                this.value = '';
                $('#imagePreview').hide();
                return;
            }
            
            // Check file type
            const validTypes = ['image/jpeg', 'image/png', 'image/jpg', 'image/gif', 'image/webp'];
            if (!validTypes.includes(file.type)) {
                alert('Please select a valid image file (JPEG, PNG, JPG, GIF, WEBP)');
                this.value = '';
                $('#imagePreview').hide();
                return;
            }
            
            const reader = new FileReader();
            reader.onload = function(e) {
                $('#imagePreview img').attr('src', e.target.result);
                $('#imagePreview').show();
            }
            reader.readAsDataURL(file);
        } else {
            $('#imagePreview').hide();
        }
    });

    // Form submission confirmation
    $('form').on('submit', function(e) {
        const newImageSelected = $('#imageInput').val() !== '';
        const currentImageExists = {{ $portfolio->image ? 'true' : 'false' }};
        
        if (newImageSelected && currentImageExists) {
            const confirmReplace = confirm('Are you sure you want to replace the current image?');
            if (!confirmReplace) {
                e.preventDefault();
                return false;
            }
        }
        
        // Show loading state
        $('.btn-save').html('⏳ Updating...').prop('disabled', true);
    });
});
</script>
@endsection