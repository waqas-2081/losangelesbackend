{{-- resources/views/admin/blogs/edit.blade.php --}}
@extends('layouts.app')

@section('title', 'Edit Blog Post')

@section('styles')
<link href="https://cdn.jsdelivr.net/npm/select2@4.1.0-rc.0/dist/css/select2.min.css" rel="stylesheet" />
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

    body { background: var(--bg); }

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

    .breadcrumb-nav a:hover { color: var(--primary); }

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

    .toggle-switch {
        position: relative;
        display: inline-block;
        width: 42px;
        height: 24px;
    }

    .toggle-switch input { display: none; }

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
        margin-left: 10px;
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
        transition: background 0.15s;
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
    }

    .alert-danger {
        background: #fee2e2;
        color: #991b1b;
        border: 1px solid #fca5a5;
        border-radius: 8px;
        padding: 12px 16px;
        margin-bottom: 16px;
    }

    .image-preview {
        margin-top: 10px;
        max-width: 200px;
    }

    .image-preview img {
        width: 100%;
        border-radius: 8px;
        border: 1px solid var(--border);
    }

    .current-image {
        margin-bottom: 10px;
        padding: 10px;
        background: var(--bg);
        border-radius: 8px;
    }

    .current-image img {
        max-width: 100%;
        border-radius: 6px;
    }
</style>
@endsection

@section('content')
<div class="container-fluid py-4" style="max-width: 1200px; margin: 0 auto;">

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

    <div class="page-header">
        <div>
            <h2>Edit Blog Post</h2>
            <div class="breadcrumb-nav">
                <a href="{{ route('admin.dashboard') }}">Dashboard</a><span> › </span>
                <a href="{{ route('admin.blogs.index') }}">Blogs</a><span> › </span>
                <span>Edit: {{ Str::limit($blog->title, 40) }}</span>
            </div>
        </div>
        <a href="{{ route('admin.blogs.index') }}" class="btn-back">
            <i class="fas fa-arrow-left me-1"></i> Back to Blogs
        </a>
    </div>

    <form action="{{ route('admin.blogs.update', $blog) }}" method="POST" enctype="multipart/form-data" id="blogForm">
        @csrf
        @method('PUT')

        <div class="layout-grid">
            <!-- Main Content Area -->
            <div>
                <div class="card-box">
                    <h5>Blog Content</h5>
                    
                    <div class="mb-3">
                        <label class="form-label">Title <span class="text-danger">*</span></label>
                        <input type="text" name="title" id="title" class="form-control" 
                               value="{{ old('title', $blog->title) }}" required>
                    </div>

                    <div class="mb-3">
                        <label class="form-label">Slug</label>
                        <input type="text" name="slug" id="slug" class="form-control" 
                               value="{{ old('slug', $blog->slug) }}" placeholder="auto-generated-if-left-empty">
                        <small class="text-muted">Leave empty to auto-generate from title</small>
                    </div>

                    <div class="mb-3">
                        <label class="form-label">Short Description</label>
                        <textarea name="short_description" id="short_description" class="form-control" 
                                  rows="3" maxlength="500">{{ old('short_description', $blog->short_description) }}</textarea>
                        <small class="text-muted">
                            <span id="charCount">0</span>/500 characters
                        </small>
                    </div>

                    <div class="mb-3">
                        <label class="form-label">Content <span class="text-danger">*</span></label>
                        <textarea name="content" id="content" class="form-control" rows="20">{{ old('content', $blog->content) }}</textarea>
                    </div>
                </div>

                <!-- SEO Section -->
                <div class="card-box">
                    <h5>
                        <i class="fas fa-search me-2"></i>
                        SEO Settings
                    </h5>
                    
                    <div class="mb-3">
                        <label class="form-label">Meta Title</label>
                        <input type="text" name="meta_title" class="form-control" 
                               value="{{ old('meta_title', $blog->meta_title) }}" maxlength="255">
                    </div>

                    <div class="mb-3">
                        <label class="form-label">Meta Description</label>
                        <textarea name="meta_description" class="form-control" 
                                  rows="3" maxlength="500">{{ old('meta_description', $blog->meta_description) }}</textarea>
                    </div>

                    <div class="mb-3">
                        <label class="form-label">Meta Keywords</label>
                        <input type="text" name="meta_keywords" class="form-control" 
                               value="{{ old('meta_keywords', $blog->meta_keywords) }}">
                    </div>
                </div>
            </div>

            <!-- Sidebar -->
            <div>
            <div class="card-box">
                <h5>Publishing</h5>
                
                <div class="mb-3">
                    <label class="form-label">Status <span class="text-danger">*</span></label>
                    <select name="status" class="form-select" id="statusSelect" required>
                        <option value="draft" {{ old('status', $blog->status) == 'draft' ? 'selected' : '' }}>Draft</option>
                        <option value="published" {{ old('status', $blog->status) == 'published' ? 'selected' : '' }}>Published</option>
                    </select>
                </div>

                <div class="mb-3" id="publishDateWrapper">
                    <label class="form-label">Publish Date</label>
                    <input type="date" name="published_at" class="form-control"
                        value="{{ old('published_at', $blog->published_at ? $blog->published_at->format('Y-m-d') : '') }}">
                    <small class="text-muted">Leave empty to use current date/time when published</small>
                </div>

                <div class="mb-3">
                    <label class="form-label d-block">Featured Post</label>
                    <div style="display: flex; align-items: center;">
                        <label class="toggle-switch">
                            <input type="hidden" name="is_featured" value="0">
                            <input type="checkbox" name="is_featured" id="isFeatured" value="1" 
                                {{ old('is_featured', $blog->is_featured) ? 'checked' : '' }}>
                            <span class="toggle-slider"></span>
                        </label>
                        <span class="toggle-label">Show in featured section</span>
                    </div>
                </div>
            </div>

                <div class="card-box">
                    <h5>Author Information</h5>
                    
                    <div class="mb-3">
                        <label class="form-label">Author Name <span class="text-danger">*</span></label>
                        <input type="text" name="author_name" class="form-control" 
                               value="{{ old('author_name', $blog->author_name) }}" required>
                    </div>
                </div>

                <div class="card-box">
                    <h5>Featured Image</h5>
                    
                    @if($blog->thumbnail_image)
                        <div class="current-image">
                            <label class="form-label">Current Image</label>
                            <img src="{{ asset('storage/' . $blog->thumbnail_image) }}" alt="Current thumbnail">
                        </div>
                    @endif
                    
                    <div class="mb-3">
                        <label class="form-label">Change Thumbnail Image</label>
                        <input type="file" name="thumbnail_image" id="thumbnail_image" 
                               class="form-control" accept="image/*">
                        <small class="text-muted">Leave empty to keep current image</small>
                        
                        <div class="image-preview" id="imagePreview" style="display: none;">
                            <img id="previewImg" src="" alt="Preview">
                        </div>
                    </div>
                </div>

                <div class="card-box">
                    <button type="submit" class="btn-save w-100 mb-2">
                        <i class="fas fa-save me-1"></i> Update Post
                    </button>
                    <a href="{{ route('admin.blogs.index') }}" class="btn-discard w-100 d-block text-center">
                        Cancel
                    </a>
                </div>
            </div>
        </div>
    </form>
</div>
@endsection

@section('scripts')
<script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
<script src="https://cdn.jsdelivr.net/npm/select2@4.1.0-rc.0/dist/js/select2.min.js"></script>
<script src="https://cdn.tiny.cloud/1/{{ env('TINYMCE_API_KEY') ? env('TINYMCE_API_KEY') : '' }}/tinymce/6/tinymce.min.js" referrerpolicy="origin"></script>

<script>
$(document).ready(function() {

    // Initialize TinyMCE
    tinymce.init({
        selector: '#content',
        height: 500,
        menubar: true,
        plugins: [
            'advlist', 'autolink', 'lists', 'link', 'image', 'charmap', 'preview',
            'anchor', 'searchreplace', 'visualblocks', 'code', 'fullscreen',
            'insertdatetime', 'media', 'table', 'help', 'wordcount'
        ],
        toolbar: 'undo redo | blocks | ' +
            'bold italic backcolor | alignleft aligncenter ' +
            'alignright alignjustify | bullist numlist outdent indent | ' +
            'removeformat | help | link image media | code preview',
        content_style: 'body { font-family:Helvetica,Arial,sans-serif; font-size:16px }',
        images_upload_url: '{{ route("admin.blogs.upload-image") }}',
        images_upload_credentials: true,
        automatic_uploads: true,
        file_picker_types: 'image',
        setup: function(editor) {
            editor.on('change', function() {
                editor.save();
            });
        }
    });

    // Auto-generate slug from title
    $('#title').on('input', function() {
        if (!$('#slug').data('user-modified')) {
            let slug = $(this).val()
                .toLowerCase()
                .replace(/[^\w\s-]/g, '')
                .replace(/\s+/g, '-')
                .replace(/-+/g, '-');
            $('#slug').val(slug);
        }
    });

    $('#slug').on('input', function() {
        $(this).data('user-modified', true);
    });

    // Character counter for short description
    $('#short_description').on('input', function() {
        let length = $(this).val().length;
        $('#charCount').text(length);
        if (length > 500) {
            $(this).val($(this).val().substring(0, 500));
            $('#charCount').text(500);
        }
    }).trigger('input');

    // Image preview
    $('#thumbnail_image').on('change', function() {
        const file = this.files[0];
        if (file) {
            const reader = new FileReader();
            reader.onload = function(e) {
                $('#previewImg').attr('src', e.target.result);
                $('#imagePreview').show();
            }
            reader.readAsDataURL(file);
        } else {
            $('#imagePreview').hide();
        }
    });

    // Toggle Publish Date field based on status
    function togglePublishDate() {
        if ($('#statusSelect').val() === 'draft') {
            $('#publishDateWrapper').hide();
        } else {
            $('#publishDateWrapper').show();
        }
    }

    $('#statusSelect').on('change', togglePublishDate);
    togglePublishDate(); // run on page load

});
</script>
@endsection