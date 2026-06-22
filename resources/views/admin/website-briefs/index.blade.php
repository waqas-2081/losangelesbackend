{{-- resources/views/admin/website-briefs/index.blade.php --}}
@extends('layouts.app')

@section('title', 'Website Briefs')

@section('content')

<style>
    .status-badge {
        padding: 5px 12px;
        border-radius: 20px;
        font-size: 12px;
        font-weight: 600;
        display: inline-block;
    }
    .status-pending { background: #fef3c7; color: #92400e; }
    .status-in_progress { background: #dbeafe; color: #1e40af; }
    .status-completed { background: #dcfce7; color: #166534; }
    .status-rejected { background: #fee2e2; color: #991b1b; }
    
    
    .action-btn {
        padding: 5px 10px;
        font-size: 13px;
        border-radius: 6px;
        transition: all 0.2s;
    }
    .action-btn:hover {
        transform: scale(1.05);
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
    
    {{-- Page Header --}}
    <div class="d-flex justify-content-between align-items-center mb-4">
        <div>
            <h4 class="mb-1">
                <i class="fas fa-globe me-2 text-primary"></i>
                Website Briefs
            </h4>
            <nav aria-label="breadcrumb">
                <ol class="breadcrumb mb-0">
                    <li class="breadcrumb-item"><a href="{{ route('admin.dashboard') }}">Dashboard</a></li>
                    <li class="breadcrumb-item active">Website Briefs</li>
                </ol>
            </nav>
        </div>
    </div>

    {{-- Session Messages --}}
    @if(session('success'))
        <div class="alert alert-success alert-dismissible fade show" role="alert">
            <i class="fas fa-check-circle me-2"></i> {{ session('success') }}
            <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
        </div>
    @endif

    @if(session('error'))
        <div class="alert alert-danger alert-dismissible fade show" role="alert">
            <i class="fas fa-exclamation-circle me-2"></i> {{ session('error') }}
            <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
        </div>
    @endif

    {{-- Filters - Only Search --}}
    <div class="filter-card">
        <form method="GET" action="{{ route('admin.website-briefs.index') }}" id="filterForm">
            <div class="row g-3">
                {{-- Search --}}
                <div class="col-md-8">
                    <label class="form-label fw-bold small text-uppercase text-muted">
                        <i class="fas fa-search me-1"></i> Search
                    </label>
                    <input type="text" 
                           name="search" 
                           class="form-control" 
                           placeholder="Search by name, email, business name..."
                           value="{{ request('search') }}">
                </div>

                {{-- Actions --}}
                <div class="col-md-4">
                    <label class="form-label d-block">&nbsp;</label>
                    <div class="d-flex gap-2">
                        <button type="submit" class="btn btn-primary flex-grow-1">
                            <i class="fas fa-filter me-1"></i> Apply
                        </button>
                        <a href="{{ route('admin.website-briefs.index') }}" class="btn btn-outline-secondary">
                            <i class="fas fa-redo"></i>
                        </a>
                    </div>
                </div>
            </div>
            
            {{-- Active Filter Display --}}
            @if(request('search'))
                <div class="mt-3">
                    <small class="text-muted">Active Filter:</small>
                    <div class="mt-1">
                        <span class="badge bg-light text-dark border px-3 py-2">
                            <i class="fas fa-search me-1"></i>
                            Search: "{{ request('search') }}"
                            <a href="{{ route('admin.website-briefs.index') }}" class="text-danger ms-2 text-decoration-none">
                                <i class="fas fa-times"></i>
                            </a>
                        </span>
                    </div>
                </div>
            @endif
        </form>
    </div>


    {{-- Website Briefs Table --}}
    <div class="card shadow-sm border-0">
        <div class="card-body p-0">
            <div class="table-responsive">
                <table class="table table-hover align-middle mb-0">
                    <thead class="table-light">
                        <tr>
                            <th width="50">#</th>
                            <th>Contact Info</th>
                            <th>Business</th>
                            <th>Status</th>
                            <th>Date</th>
                            <th width="120">Actions</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse($briefs as $brief)
                        <tr>
                            <td>
                                <strong class="text-primary">{{ ($briefs->firstItem() ?? 0) + $loop->index }}</strong>
                            </td>
                       
                            <td>
                                <div class="d-flex flex-column">
                                    <span class="fw-bold">{{ $brief->name }}</span>
                                    <small class="text-muted">
                                        <i class="far fa-envelope me-1"></i>{{ $brief->email }}
                                    </small>
                                    @if($brief->phone)
                                        <small class="text-muted">
                                            <i class="fas fa-phone me-1"></i>{{ $brief->phone }}
                                        </small>
                                    @endif
                                </div>
                            </td>
                            
                            <td>
                                <div class="d-flex flex-column">
                                    <span class="fw-bold">{{ $brief->business_name ?: '—' }}</span>
                                    @if($brief->business_type)
                                        <small class="text-muted">{{ $brief->business_type }}</small>
                                    @endif
                                </div>
                            </td>
                            
                        
                            
                            <td>
                                @php
                                    $status = $brief->status ?? 'pending';
                                    $statusLabels = [
                                        'pending' => 'Pending',
                                        'in_progress' => 'In Progress',
                                        'completed' => 'Completed',
                                        'rejected' => 'Rejected'
                                    ];
                                @endphp
                                <span class="status-badge status-{{ $status }}">
                                    {{ $statusLabels[$status] ?? ucfirst($status) }}
                                </span>
                            </td>
                            
                            <td>
                                <div class="d-flex flex-column">
                                    <small>{{ $brief->created_at->format('M d, Y') }}</small>
                                </div>
                            </td>
                            
                            <td>
                                <div class="d-flex gap-1">
                                    <a href="{{ route('admin.website-briefs.show', $brief) }}" 
                                       class="btn btn-sm btn-outline-primary d-flex align-items-center justify-content-center"
                                       data-bs-toggle="tooltip" 
                                       title="View Details"
                                       style="width: 34px; height: 34px; padding: 0;">
                                        <i class="fas fa-eye"></i>
                                    </a>
                                    
                                    <form action="{{ route('admin.website-briefs.destroy', $brief->id) }}" 
                                          method="POST" 
                                          class="d-inline delete-form">
                                        @csrf
                                        @method('DELETE')
                                        <button type="submit"
                                                class="btn btn-sm btn-outline-danger d-flex align-items-center justify-content-center delete-btn"
                                                data-id="{{ $brief->id }}"
                                                data-name="{{ $brief->name }}"
                                                data-bs-toggle="tooltip"
                                                title="Delete"
                                                style="width: 34px; height: 34px; padding: 0;"
                                                onclick="return confirm('Are you sure you want to delete this website brief?');">
                                            <i class="fas fa-trash"></i>
                                        </button>
                                    </form>
                                </div>
                            </td>
                        </tr>
                        @empty
                        <tr>
                            <td colspan="7" class="text-center py-5">
                                <div class="text-muted">
                                    <i class="fas fa-globe fa-3x mb-3"></i>
                                    <h5>No website briefs found</h5>
                                    <p class="mb-0">
                                        @if(request('search'))
                                            No briefs match your search criteria. 
                                            <a href="{{ route('admin.website-briefs.index') }}" class="text-primary">Clear search</a>
                                        @else
                                            New website briefs will appear here.
                                        @endif
                                    </p>
                                </div>
                            </td>
                        </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>

            {{-- Pagination --}}
            @if($briefs->hasPages())
                <div class="p-3 border-top">
                    <div class="d-flex justify-content-between align-items-center">
                        <div class="text-muted small">
                            Showing {{ $briefs->firstItem() ?? 0 }} to {{ $briefs->lastItem() ?? 0 }} 
                            of {{ $briefs->total() }} entries
                        </div>
                        <div>
                            {{ $briefs->withQueryString()->links('pagination::bootstrap-4') }}
                        </div>
                    </div>
                </div>
            @endif
        </div>
    </div>
</div>

@endsection

@section('scripts')
<script>
$(document).ready(function() {
    // Initialize tooltips
    $('[data-bs-toggle="tooltip"]').tooltip();
});
</script>
@endsection