{{-- resources/views/admin/blogs/index.blade.php --}}
@extends('layouts.app')

@section('title', 'Blog Posts')

@section('content')

<style>
    .status-badge {
        padding: 5px 12px;
        border-radius: 20px;
        font-size: 12px;
        font-weight: 600;
        display: inline-block;
    }
    .status-published { background: #dcfce7; color: #166534; }
    .status-draft { background: #fef3c7; color: #92400e; }
    
    .featured-star {
        color: #fbbf24;
        font-size: 16px;
    }
    
    .filter-card {
        background: white;
        border-radius: 12px;
        padding: 20px;
        margin-bottom: 24px;
        box-shadow: 0 1px 3px rgba(0,0,0,0.1);
    }
</style>

<div class="container-fluid">
    
    <div class="d-flex justify-content-between align-items-center mb-4">
        <div>
            <h4 class="mb-1">
                <i class="fas fa-blog me-2 text-primary"></i>
                Blog Posts
            </h4>
            <nav aria-label="breadcrumb">
                <ol class="breadcrumb mb-0">
                    <li class="breadcrumb-item"><a href="{{ route('admin.dashboard') }}">Dashboard</a></li>
                    <li class="breadcrumb-item active">Blogs</li>
                </ol>
            </nav>
        </div>
        <a href="{{ route('admin.blogs.create') }}" class="btn btn-primary">
            <i class="fas fa-plus me-1"></i> New Blog Post
        </a>
    </div>

    @if(session('success'))
        <div class="alert alert-success alert-dismissible fade show" role="alert">
            <i class="fas fa-check-circle me-2"></i> {{ session('success') }}
            <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
        </div>
    @endif

    <div class="filter-card">
        <form method="GET" action="{{ route('admin.blogs.index') }}">
            <div class="row g-3">
                <div class="col-md-5">
                    <label class="form-label fw-bold small text-uppercase text-muted">
                        <i class="fas fa-search me-1"></i> Search
                    </label>
                    <input type="text" name="search" class="form-control" 
                           placeholder="Search by title, author, description..."
                           value="{{ request('search') }}">
                </div>

                <div class="col-md-3">
                    <label class="form-label fw-bold small text-uppercase text-muted">
                        <i class="fas fa-flag me-1"></i> Status
                    </label>
                    <select name="status" class="form-select">
                        <option value="">All Status</option>
                        @foreach($statuses as $value => $label)
                            <option value="{{ $value }}" {{ request('status') == $value ? 'selected' : '' }}>
                                {{ $label }}
                            </option>
                        @endforeach
                    </select>
                </div>

                <div class="col-md-2">
                    <label class="form-label fw-bold small text-uppercase text-muted">
                        <i class="fas fa-star me-1"></i> Featured
                    </label>
                    <select name="featured" class="form-select">
                        <option value="0" {{ request('featured') == '0' ? 'selected' : '' }}>All Posts</option>
                        <option value="1" {{ request('featured') == '1' ? 'selected' : '' }}>Featured Only</option>
                    </select>
                </div>

                <div class="col-md-2">
                    <label class="form-label d-block">&nbsp;</label>
                    <div class="d-flex gap-2">
                        <button type="submit" class="btn btn-primary flex-grow-1">
                            <i class="fas fa-filter me-1"></i> Apply
                        </button>
                        <a href="{{ route('admin.blogs.index') }}" class="btn btn-outline-secondary">
                            <i class="fas fa-redo"></i>
                        </a>
                    </div>
                </div>
            </div>
        </form>
    </div>

    <div class="card shadow-sm border-0">
        <div class="card-body p-0">
            <div class="table-responsive">
                <table class="table table-hover align-middle mb-0">
                    <thead class="table-light">
                        <tr>
                            <th width="50">#</th>
                            <th>Title</th>
                            <th>Author</th>
                            <th>Status</th>
                            <th>Date</th>
                            <th width="150">Actions</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse($blogs as $blog)
                        <tr>
                            <td>
                                <strong class="text-primary">{{ ($blogs->firstItem() ?? 0) + $loop->index }}</strong>
                                @if($blog->is_featured)
                                    <i class="fas fa-star featured-star ms-1" title="Featured"></i>
                                @endif
                            </td>
                            <td>
                                <div class="d-flex align-items-center">
                                    @if($blog->thumbnail_image)
                                        <img src="{{ asset('storage/' . $blog->thumbnail_image) }}" 
                                             alt="Thumbnail" 
                                             style="width: 40px; height: 40px; object-fit: cover; border-radius: 6px; margin-right: 12px;">
                                    @else
                                        <div style="width: 40px; height: 40px; background: #e5e7eb; border-radius: 6px; margin-right: 12px; display: flex; align-items: center; justify-content: center;">
                                            <i class="fas fa-image text-muted"></i>
                                        </div>
                                    @endif
                                    <div>
                                        <span class="fw-bold">{{ Str::limit($blog->title, 50) }}</span>
                                        <small class="d-block text-muted">{{ Str::limit($blog->short_description, 60) }}</small>
                                    </div>
                                </div>
                            </td>
                            <td>{{ $blog->author_name }}</td>
                            <td>
                                <span class="status-badge status-{{ $blog->status }}">
                                    {{ $statuses[$blog->status] ?? ucfirst($blog->status) }}
                                </span>
                            </td>
                            <td>
                                <small>{{ $blog->created_at->format('M d, Y') }}</small>
                            </td>
                            <td>
                                <div class="d-flex gap-1">
                                    <a href="{{ route('admin.blogs.edit', $blog) }}" 
                                       class="btn btn-sm btn-outline-primary"
                                       data-bs-toggle="tooltip" 
                                       title="Edit">
                                        <i class="fas fa-edit"></i>
                                    </a>
                                    <form action="{{ route('admin.blogs.destroy', $blog) }}" method="POST" style="display:inline;">
                                        @csrf
                                        @method('DELETE')
                                        <button type="submit" class="btn btn-sm btn-outline-danger"
                                                data-bs-toggle="tooltip"
                                                title="Delete"
                                                onclick="return confirm('Are you sure you want to delete this blog post?')">
                                            <i class="fas fa-trash"></i>
                                        </button>
                                    </form>
                               
                                </div>
                            </td>
                        </tr>
                        @empty
                        <tr>
                            <td colspan="6" class="text-center py-5">
                                <div class="text-muted">
                                    <i class="fas fa-newspaper fa-3x mb-3"></i>
                                    <h5>No blog posts found</h5>
                                    <p class="mb-0">Create your first blog post to get started.</p>
                                </div>
                            </td>
                        </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>

            @if($blogs->hasPages())
                <div class="p-3 border-top">
                    <div class="d-flex justify-content-between align-items-center">
                        <div class="text-muted small">
                            Showing {{ $blogs->firstItem() ?? 0 }} to {{ $blogs->lastItem() ?? 0 }} 
                            of {{ $blogs->total() }} entries
                        </div>
                        <div>
                            {{ $blogs->withQueryString()->links('pagination::bootstrap-4') }}
                        </div>
                    </div>
                </div>
            @endif
        </div>
    </div>
</div>

@endsection

