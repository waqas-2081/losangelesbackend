{{-- resources/views/admin/blogs/create.blade.php --}}
@extends('layouts.app')

@section('title', 'Create Blog Post')

@section('styles')
<link href="https://cdn.jsdelivr.net/npm/select2@4.1.0-rc.0/dist/css/select2.min.css" rel="stylesheet" />
<link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/codemirror/5.65.16/codemirror.min.css">
<link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/codemirror/5.65.16/theme/dracula.min.css">
<link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/codemirror/5.65.16/addon/lint/lint.min.css">
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
        text-decoration: none;
        display: inline-block;
    }

    .btn-discard:hover {
        background: var(--bg);
        color: var(--text);
        text-decoration: none;
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

    .text-danger { color: #dc2626 !important; }

    .image-preview {
        margin-top: 10px;
        max-width: 200px;
    }

    .image-preview img {
        width: 100%;
        border-radius: 8px;
        border: 1px solid var(--border);
    }

    .select2-container--default .select2-selection--multiple {
        border: 1.5px solid var(--border) !important;
        border-radius: 7px !important;
        min-height: 42px;
    }

    /* ===== SCHEMA EDITOR SCROLLBAR STYLES ===== */
    .schema-editor-wrapper {
        position: relative;
        border: 1.5px solid var(--border);
        border-radius: 7px;
        overflow: hidden;
        background: #282a36;
        width: 663px;
        box-sizing: border-box;
    }

    .schema-editor-wrapper .CodeMirror {
        height: 400px !important;
        width: 663px !important;
        border: none !important;
        border-radius: 0 !important;
        font-size: 0.85rem;
        line-height: 1.6;
        box-sizing: border-box;
    }

    .schema-editor-wrapper .CodeMirror-scroll {
        overflow: auto !important;
        max-height: 400px;
        min-height: 400px;
        width: 663px !important;
        box-sizing: border-box;
    }

    .schema-editor-wrapper .CodeMirror-sizer {
        min-width: 663px !important;
    }

    .schema-editor-wrapper .CodeMirror pre.CodeMirror-line,
    .schema-editor-wrapper .CodeMirror pre.CodeMirror-line-like {
        white-space: pre !important;
        word-break: normal !important;
        word-wrap: normal !important;
    }

    .schema-editor-wrapper .CodeMirror-scroll::-webkit-scrollbar {
        width: 10px;
        height: 10px;
    }

    .schema-editor-wrapper .CodeMirror-scroll::-webkit-scrollbar-track {
        background: #1e1f29;
        border-radius: 0;
    }

    .schema-editor-wrapper .CodeMirror-scroll::-webkit-scrollbar-thumb {
        background: #6c5ce7;
        border-radius: 5px;
        border: 2px solid #1e1f29;
    }

    .schema-editor-wrapper .CodeMirror-scroll::-webkit-scrollbar-thumb:hover {
        background: #5a4bd1;
    }

    .schema-editor-wrapper .CodeMirror-scroll {
        scrollbar-width: thin;
        scrollbar-color: #6c5ce7 #1e1f29;
    }

    .schema-editor-wrapper .CodeMirror-gutters {
        background: #1e1f29;
        border-right: 1px solid #44475a;
    }

    .schema-editor-wrapper .CodeMirror-linenumber {
        color: #6272a4;
    }

    #schemaJsonError {
        margin-top: 8px;
        font-size: 0.82rem;
        padding: 6px 12px;
        background: #fee2e2;
        border-radius: 4px;
        border-left: 3px solid #dc2626;
        display: none;
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
            <h2>Create New Blog Post</h2>
            <div class="breadcrumb-nav">
                <a href="{{ route('admin.dashboard') }}">Dashboard</a><span> › </span>
                <a href="{{ route('admin.blogs.index') }}">Blogs</a><span> › </span>
                <span>Create</span>
            </div>
        </div>
        <a href="{{ route('admin.blogs.index') }}" class="btn-back">
            <i class="fas fa-arrow-left me-1"></i> Back to Blogs
        </a>
    </div>

    <form action="{{ route('admin.blogs.store') }}" method="POST" enctype="multipart/form-data" id="blogForm">
        @csrf

        <div class="layout-grid">
            <!-- Main Content Area -->
            <div>
                <div class="card-box">
                    <h5>Blog Content</h5>
                    
                    <div class="mb-3">
                        <label class="form-label">Title <span class="text-danger">*</span></label>
                        <input type="text" name="title" id="title" class="form-control" 
                               value="{{ old('title') }}" required>
                    </div>

                    <div class="mb-3">
                        <label class="form-label">Slug</label>
                        <input type="text" name="slug" id="slug" class="form-control" 
                               value="{{ old('slug') }}" placeholder="auto-generated-if-left-empty">
                        <small class="text-muted">Leave empty to auto-generate from title</small>
                    </div>

                    <div class="mb-3">
                        <label class="form-label">Short Description</label>
                        <textarea name="short_description" id="short_description" class="form-control" 
                                  rows="3" maxlength="500">{{ old('short_description') }}</textarea>
                        <small class="text-muted">
                            <span id="charCount">0</span>/500 characters
                        </small>
                    </div>

                    <div class="mb-3">
                        <label class="form-label">Content <span class="text-danger">*</span></label>
                        <textarea name="content" id="content" class="form-control" rows="20">{{ old('content') }}</textarea>
                    </div>
                </div>

                <!-- SEO Section -->
                <div class="card-box mt-3">
                    <h5>
                        <i class="fas fa-search me-2"></i>
                        SEO Settings
                    </h5>
                    
                    <div class="mb-3">
                        <label class="form-label">Meta Title</label>
                        <input type="text" name="meta_title" class="form-control" 
                               value="{{ old('meta_title') }}" maxlength="255">
                        <small class="text-muted">Recommended: 50-60 characters</small>
                    </div>

                    <div class="mb-3">
                        <label class="form-label">Meta Description</label>
                        <textarea name="meta_description" class="form-control" 
                                  rows="3" maxlength="500">{{ old('meta_description') }}</textarea>
                        <small class="text-muted">Recommended: 150-160 characters</small>
                    </div>

                    <div class="mb-3">
                        <label class="form-label">Meta Keywords</label>
                        <input type="text" name="meta_keywords" class="form-control" 
                               value="{{ old('meta_keywords') }}" placeholder="keyword1, keyword2, keyword3">
                    </div>
                </div>

                <!-- Schema Markup Section with Scrollbars -->
                <div class="card-box mt-3">
                    <h5>
                        <i class="fas fa-code me-2"></i>
                        Schema Markup (JSON-LD)
                    </h5>

                    <div class="mb-3">
                        <label class="form-label">Schema JSON</label>
                        <div class="schema-editor-wrapper">
                            <textarea name="schema" id="schema" rows="10">{{ old('schema') }}</textarea>
                        </div>
                        <small class="text-muted">Must be valid JSON (e.g. Article / BlogPosting structured data). Leave empty if not needed.</small>
                        <div id="schemaJsonError"></div>
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
                            <option value="draft" {{ old('status') == 'draft' ? 'selected' : '' }}>Draft</option>
                            <option value="published" {{ old('status') == 'published' ? 'selected' : '' }}>Published</option>
                        </select>
                    </div>

                    <div class="mb-3" id="publishDateWrapper">
                        <label class="form-label">Publish Date</label>
                        <input type="date" name="published_at" class="form-control"
                            value="{{ old('published_at') }}">
                        <small class="text-muted">Leave empty to use current date/time when published</small>
                    </div>

                    <div class="mb-3">
                        <label class="form-label d-block">Featured Post</label>
                        <div style="display: flex; align-items: center;">
                            <label class="toggle-switch">
                                <input type="hidden" name="is_featured" value="0">
                                <input type="checkbox" name="is_featured" id="isFeatured" value="1" 
                                    {{ old('is_featured') ? 'checked' : '' }}>
                                <span class="toggle-slider"></span>
                            </label>
                            <span class="toggle-label">Show in featured section</span>
                        </div>
                    </div>
                </div>

                <div class="card-box mt-3">
                    <h5>Author Information</h5>
                    
                    <div class="mb-3">
                        <label class="form-label">Author Name <span class="text-danger">*</span></label>
                        <input type="text" name="author_name" class="form-control" 
                               value="{{ old('author_name', Auth::user()->name ?? '') }}" required>
                    </div>
                </div>

                <div class="card-box mt-3">
                    <h5>Featured Image</h5>
                    
                    <div class="mb-3">
                        <label class="form-label">Thumbnail Image</label>
                        <input type="file" name="thumbnail_image" id="thumbnail_image" 
                               class="form-control" accept="image/*">
                        <small class="text-muted">Recommended size: 1200x630px, Max: 2MB</small>
                        
                        <div class="image-preview" id="imagePreview" style="display: none;">
                            <img id="previewImg" src="" alt="Preview">
                        </div>
                    </div>
                </div>

                <div class="card-box mt-3">
                    <button type="submit" class="btn-save w-100 mb-2">
                        <i class="fas fa-save me-1"></i> Save Post
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
<script src="https://cdn.tiny.cloud/1/{{ env('TINYMCE_API_KEY') ?: '' }}/tinymce/6/tinymce.min.js" referrerpolicy="origin"></script>
<script src="https://cdnjs.cloudflare.com/ajax/libs/codemirror/5.65.16/codemirror.min.js"></script>
<script src="https://cdnjs.cloudflare.com/ajax/libs/codemirror/5.65.16/mode/javascript/javascript.min.js"></script>
<script src="https://cdnjs.cloudflare.com/ajax/libs/codemirror/5.65.16/addon/lint/lint.min.js"></script>
<script src="https://cdnjs.cloudflare.com/ajax/libs/jsonlint/1.6.2/jsonlint.min.js"></script>
<script src="https://cdnjs.cloudflare.com/ajax/libs/codemirror/5.65.16/addon/lint/json-lint.min.js"></script>
<script>
$(document).ready(function() {
    // Initialize TinyMCE
    if (typeof tinymce !== 'undefined') {
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
    }

    // Auto-generate slug from title
    var slugUserModified = false;
    
    $('#title').on('input', function() {
        if (!slugUserModified) {
            let slug = $(this).val()
                .toLowerCase()
                .replace(/[^\w\s-]/g, '')
                .replace(/\s+/g, '-')
                .replace(/-+/g, '-');
            $('#slug').val(slug);
        }
    });

    $('#slug').on('input', function() {
        slugUserModified = true;
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
    togglePublishDate();

    function sanitizeJsonNewlines(str) {
    let result = '';
    let inString = false;
    let escaped = false;

    for (let i = 0; i < str.length; i++) {
        let ch = str[i];

        if (escaped) {
            result += ch;
            escaped = false;
            continue;
        }

        if (ch === '\\') {
            result += ch;
            escaped = true;
            continue;
        }

        if (ch === '"') {
            inString = !inString;
            result += ch;
            continue;
        }

        if (inString && (ch === '\n' || ch === '\r')) {
            result += '\\n'; // escape raw newline inside a string
            continue;
        }

        result += ch;
    }

    return result;
}
    // ===== SCHEMA JSON CODE EDITOR WITH SCROLLBARS =====
    var schemaTextarea = document.getElementById('schema');
    if (schemaTextarea) {
        var schemaEditor = CodeMirror.fromTextArea(schemaTextarea, {
            mode: { name: 'javascript', json: true },
            theme: 'dracula',
            lineNumbers: true,
            matchBrackets: true,
            autoCloseBrackets: true,
            gutters: ['CodeMirror-lint-markers'],
            lint: true,
            tabSize: 2,
            scrollbarStyle: 'native',
            lineWrapping: false,
            viewportMargin: Infinity,
            workDelay: 300,
            workTime: 100,
            indentUnit: 2,
            electricChars: true,
            smartIndent: true
        });

        setTimeout(function() {
            schemaEditor.refresh();
        }, 600);

        $(window).on('resize', function() {
            schemaEditor.refresh();
        });

        $('#blogForm').on('submit', function(e) {
            schemaEditor.save();

            var raw = $('#schema').val().trim();
            $('#schemaJsonError').hide().text('');

            if (raw !== '') {
                var sanitized = sanitizeJsonNewlines(raw);
                try {
                    JSON.parse(sanitized);
                    $('#schema').val(sanitized); // save the cleaned version
                } catch (err) {
                    e.preventDefault();
                    $('#schemaJsonError')
                        .text('Schema field must contain valid JSON: ' + err.message)
                        .show();
                    schemaEditor.focus();
                    return false;
                }
            }
        });

        schemaEditor.on('change', function() {
            var content = schemaEditor.getValue().trim();
            if (content !== '') {
                try {
                    JSON.parse(content);
                    $('#schemaJsonError').hide();
                } catch (err) {
                    // Silent validation during typing
                }
            }
        });
    }
});
</script>
@endsection