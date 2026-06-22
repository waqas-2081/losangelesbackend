@extends('layouts.app')

@section('title', 'Add Multiple Portfolio Items')

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

    .upload-area {
        border: 2px dashed var(--border);
        border-radius: 8px;
        padding: 30px;
        text-align: center;
        cursor: pointer;
        transition: all 0.2s;
        background: #fafafa;
        margin-bottom: 10px;
    }

    .upload-area:hover {
        border-color: var(--primary);
        background: #f8f7ff;
    }

    .upload-icon {
        font-size: 40px;
        margin-bottom: 10px;
    }

    .upload-area p {
        margin: 5px 0;
        color: var(--text);
        font-weight: 500;
    }

    .upload-area small {
        color: var(--muted);
    }

    .image-preview-container {
        display: flex;
        flex-wrap: wrap;
        gap: 12px;
        margin-top: 20px;
    }

    .image-preview-item {
        position: relative;
        width: 120px;
        height: 120px;
        border-radius: 8px;
        overflow: hidden;
        border: 2px solid var(--border);
        box-shadow: 0 2px 4px rgba(0,0,0,0.05);
    }

    .image-preview-item img {
        width: 100%;
        height: 100%;
        object-fit: cover;
    }

    .remove-btn {
        position: absolute;
        top: 5px;
        right: 5px;
        background: rgba(220, 38, 38, 0.9);
        color: white;
        border: none;
        border-radius: 50%;
        width: 24px;
        height: 24px;
        display: flex;
        align-items: center;
        justify-content: center;
        cursor: pointer;
        font-size: 16px;
        font-weight: bold;
        padding: 0;
        transition: background 0.2s;
    }

    .remove-btn:hover {
        background: rgb(220, 38, 38);
    }

    .image-count-badge {
        display: inline-block;
        background: var(--primary);
        color: white;
        padding: 4px 12px;
        border-radius: 20px;
        font-size: 0.8rem;
        font-weight: 500;
        margin-top: 10px;
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
        transition: background 0.2s;
    }

    .btn-save:disabled {
        opacity: 0.5;
        cursor: not-allowed;
    }

    .btn-save:hover:not(:disabled) {
        background: var(--primary-dark);
    }

    .alert-danger {
        background: #fee2e2;
        color: #991b1b;
        border: 1px solid #fca5a5;
        border-radius: 8px;
        padding: 12px 16px;
        margin-bottom: 20px;
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
</style>
@endsection

@section('content')
<div class="container-fluid py-4" style="max-width: 800px; margin: 0 auto;">

    {{-- Validation Errors --}}
    @if($errors->any())
        <div class="alert-danger">
            <strong>Please fix the following errors:</strong><br>
            @foreach($errors->all() as $error)
                • {{ $error }}<br>
            @endforeach
        </div>
    @endif

    {{-- Page Header --}}
    <div class="page-header">
        <div>
            <h2>Add Portfolio Items</h2>
            <div class="breadcrumb-nav">
                <a href="{{ route('admin.dashboard') }}">Dashboard</a> › 
                <a href="{{ route('admin.portfolios.index') }}">Portfolio</a> › 
                <span>Create Multiple</span>
            </div>
        </div>
        <a href="{{ route('admin.portfolios.index') }}" class="btn-back">← Back to Portfolio</a>
    </div>

    {{-- Form --}}
    <form action="{{ route('admin.portfolios.store') }}" method="POST" enctype="multipart/form-data" id="portfolioForm">
        @csrf

        <div class="card-box">
            <h5>📋 Portfolio Information</h5>

            {{-- Category --}}
            <div class="mb-3">
                <label class="form-label">Category <span class="text-danger">*</span></label>
                <select name="category" class="form-select" required>
                    <option value="">-- Select Category --</option>
                    @foreach($categories as $cat)
                        <option value="{{ $cat }}" {{ old('category') == $cat ? 'selected' : '' }}>
                            {{ $cat }}
                        </option>
                    @endforeach
                </select>
                <div class="info-text">
                    💡 This category will be applied to all uploaded images
                </div>
            </div>

            {{-- Multiple Images Upload --}}
            <div class="mb-3">
                <label class="form-label">Upload Images <span class="text-danger">*</span></label>
                
                {{-- Hidden File Input --}}
                <input type="file" 
                       name="images[]" 
                       id="imageInput"
                       accept="image/jpeg,image/png,image/jpg,image/gif,image/webp"
                       multiple
                       style="display: none;">
                
                {{-- Upload Area --}}
                <div class="upload-area" id="uploadArea">
                    <div class="upload-icon">📸</div>
                    <p>Click or Drag & Drop Images Here</p>
                    <small>You can select multiple images at once</small>
                </div>
                
                <small class="text-muted">
                    Supported formats: JPG, PNG, GIF, WEBP • Max size: 2MB per image
                </small>
                
                {{-- Image Count Display --}}
                <div id="imageCountDisplay" style="margin-top: 10px;"></div>
                
                {{-- Image Preview Container --}}
                <div class="image-preview-container" id="imagePreviewContainer"></div>
            </div>

            {{-- Status --}}
            <div class="mb-3">
                <label class="form-label">Status</label>
                <div class="status-row">
                    <label class="toggle-switch">
                        <input type="checkbox" name="is_active" id="isActive" value="1" {{ old('is_active', '1') ? 'checked' : '' }}>
                        <span class="toggle-slider"></span>
                    </label>
                    <span class="toggle-label" id="activeLabel">{{ old('is_active', '1') ? 'Active' : 'Inactive' }}</span>
                </div>
                <div class="info-text">
                    💡 This status will be applied to all uploaded images
                </div>
            </div>

            {{-- Base Sort Order --}}
            <div class="mb-3">
                <label class="form-label">Base Sort Order</label>
                <input type="number" 
                       name="sort_order" 
                       class="form-control" 
                       value="{{ old('sort_order', 0) }}" 
                       min="0"
                       placeholder="0">
                <div class="info-text">
                    💡 Images will be assigned sequential sort orders (0, 1, 2, ...)
                </div>
            </div>
        </div>

        {{-- Footer --}}
        <div class="footer-bar">
            <div></div>
            <div style="display: flex; gap: 10px;">
                <a href="{{ route('admin.portfolios.index') }}" class="btn-discard">Cancel</a>
                <button type="submit" class="btn-save" id="submitBtn" disabled>Save All Items</button>
            </div>
        </div>
    </form>
</div>
@endsection

@section('scripts')
<script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
<script>
$(document).ready(function() {
    let selectedFiles = [];
    let dataTransfer = new DataTransfer();

    // Toggle label update
    $('#isActive').change(function() {
        $('#activeLabel').text(this.checked ? 'Active' : 'Inactive');
    });

    // Click on upload area
    $('#uploadArea').click(function() {
        $('#imageInput').click();
    });

    // Handle file selection
    $('#imageInput').on('change', function(e) {
        handleFiles(this.files);
    });

    // Drag and drop handlers
    $('#uploadArea').on('dragover', function(e) {
        e.preventDefault();
        e.stopPropagation();
        $(this).css({
            'border-color': '#6c5ce7',
            'background': '#f8f7ff'
        });
    });

    $('#uploadArea').on('dragleave', function(e) {
        e.preventDefault();
        e.stopPropagation();
        $(this).css({
            'border-color': '#e5e7eb',
            'background': '#fafafa'
        });
    });

    $('#uploadArea').on('drop', function(e) {
        e.preventDefault();
        e.stopPropagation();
        $(this).css({
            'border-color': '#e5e7eb',
            'background': '#fafafa'
        });
        
        let files = e.originalEvent.dataTransfer.files;
        handleFiles(files);
        
        // Update file input with all selected files
        updateFileInput();
    });

    function handleFiles(files) {
        let newFilesAdded = false;
        
        for (let i = 0; i < files.length; i++) {
            let file = files[i];
            
            // Check if it's an image
            if (!file.type.startsWith('image/')) {
                continue;
            }
            
            // Check if file already exists
            let exists = selectedFiles.some(f => 
                f.name === file.name && 
                f.size === file.size && 
                f.lastModified === file.lastModified
            );
            
            if (!exists) {
                selectedFiles.push(file);
                dataTransfer.items.add(file);
                addImagePreview(file);
                newFilesAdded = true;
            }
        }
        
        if (newFilesAdded) {
            updateFileInput();
        }
        
        updateUI();
    }

    function addImagePreview(file) {
        let reader = new FileReader();
        reader.onload = function(e) {
            let fileId = file.name + '-' + file.size + '-' + file.lastModified;
            let previewHtml = `
                <div class="image-preview-item" data-file-id="${fileId}">
                    <img src="${e.target.result}" alt="${file.name}">
                    <button type="button" class="remove-btn" data-file-id="${fileId}">×</button>
                </div>
            `;
            $('#imagePreviewContainer').append(previewHtml);
        };
        reader.readAsDataURL(file);
    }

    // Remove image
    $(document).on('click', '.remove-btn', function() {
        let fileId = $(this).data('file-id');
        
        // Find and remove file from selectedFiles
        selectedFiles = selectedFiles.filter(file => {
            let currentFileId = file.name + '-' + file.size + '-' + file.lastModified;
            return currentFileId !== fileId;
        });
        
        // Remove preview
        $(`.image-preview-item[data-file-id="${fileId}"]`).remove();
        
        // Update file input
        updateFileInput();
        updateUI();
    });

    function updateFileInput() {
        // Create new DataTransfer and add all selected files
        let dt = new DataTransfer();
        selectedFiles.forEach(file => {
            dt.items.add(file);
        });
        
        // Update file input
        $('#imageInput')[0].files = dt.files;
    }

    function updateUI() {
        let count = selectedFiles.length;
        
        if (count > 0) {
            $('#imageCountDisplay').html(`
                <span class="image-count-badge">
                    📸 ${count} image${count > 1 ? 's' : ''} selected
                </span>
            `);
            $('#submitBtn').prop('disabled', false);
        } else {
            $('#imageCountDisplay').html('');
            $('#submitBtn').prop('disabled', true);
        }
    }

    // Form submission validation
    $('#portfolioForm').on('submit', function(e) {
        if (selectedFiles.length === 0) {
            e.preventDefault();
            alert('Please select at least one image to upload.');
            return false;
        }
        
        // Show loading state
        $('#submitBtn').prop('disabled', true).text('Uploading...');
    });
});
</script>
@endsection