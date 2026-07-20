@extends('layouts.app')

@section('title', 'Logo Creator Briefs')

@section('content')

<style>
    .filter-card { background: white; border-radius: 12px; padding: 20px; margin-bottom: 24px; box-shadow: 0 1px 3px rgba(0,0,0,0.1); }
</style>

<div class="container-fluid">

    {{-- Page Header --}}
    <div class="d-flex justify-content-between align-items-center mb-4">
        <div>
            <h4 class="mb-1">
                <i class="fas fa-paint-brush me-2 text-primary"></i>
                Logo Creator
            </h4>
            <nav aria-label="breadcrumb">
                <ol class="breadcrumb mb-0">
                    <li class="breadcrumb-item"><a href="{{ route('admin.dashboard') }}">Dashboard</a></li>
                    <li class="breadcrumb-item active">Logo Creator</li>
                </ol>
            </nav>
        </div>
    </div>

    {{-- Alerts --}}
    @if(session('success'))
        <div class="alert alert-success alert-dismissible fade show">
            <i class="fas fa-check-circle me-2"></i> {{ session('success') }}
            <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
        </div>
    @endif
    @if(session('error'))
        <div class="alert alert-danger alert-dismissible fade show">
            <i class="fas fa-exclamation-circle me-2"></i> {{ session('error') }}
            <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
        </div>
    @endif

    {{-- Search Filter --}}
    <div class="filter-card">
        <form method="GET" action="{{ route('admin.logo-creator.index') }}">
            <div class="row g-3">
                <div class="col-md-8">
                    <label class="form-label fw-bold small text-uppercase text-muted">
                        <i class="fas fa-search me-1"></i> Search
                    </label>
                    <input type="text" name="search" class="form-control"
                           placeholder="Search by business name, email, phone, industry..."
                           value="{{ request('search') }}">
                </div>
                <div class="col-md-4">
                    <label class="form-label d-block">&nbsp;</label>
                    <div class="d-flex gap-2">
                        <button type="submit" class="btn btn-primary flex-grow-1">
                            <i class="fas fa-filter me-1"></i> Apply
                        </button>
                        <a href="{{ route('admin.logo-creator.index') }}" class="btn btn-outline-secondary">
                            <i class="fas fa-redo"></i>
                        </a>
                    </div>
                </div>
            </div>
            @if(request('search'))
                <div class="mt-3">
                    <small class="text-muted">Active Filter:</small>
                    <div class="mt-1">
                        <span class="badge bg-light text-dark border px-3 py-2">
                            <i class="fas fa-search me-1"></i> Search: "{{ request('search') }}"
                            <a href="{{ route('admin.logo-creator.index') }}" class="text-danger ms-2 text-decoration-none">
                                <i class="fas fa-times"></i>
                            </a>
                        </span>
                    </div>
                </div>
            @endif
        </form>
    </div>

    {{-- Table --}}
    <div class="card shadow-sm border-0">
        <div class="card-body p-0">
            <div class="table-responsive">
                <table class="table table-hover align-middle mb-0">
                    <thead class="table-light">
                        <tr>
                            <th width="50">#</th>
                            <th>Business</th>
                            <th>Contact</th>
                            <th>Industry</th>
                            <th>Date</th>
                            <th width="100">Actions</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse($briefs as $brief)
                        <tr>
                            <td><strong class="text-primary">{{ ($briefs->firstItem() ?? 0) + $loop->index }}</strong></td>

                            <td>
                                <div class="fw-bold">{{ $brief->business_name }}</div>
                                @if($brief->slogan)
                                    <small class="text-muted fst-italic">"{{ $brief->slogan }}"</small>
                                @endif
                            </td>

                            <td>
                                @if($brief->email)
                                    <div><i class="far fa-envelope me-1 text-muted"></i>{{ $brief->email }}</div>
                                @endif
                                @if($brief->phone)
                                    <small class="text-muted"><i class="fas fa-phone me-1"></i>{{ $brief->phone }}</small>
                                @endif
                                @if(!$brief->email && !$brief->phone)
                                    <span class="text-muted">—</span>
                                @endif
                            </td>

                            <td>{{ $brief->industry ?? '—' }}</td>

                            <td><small>{{ $brief->created_at->format('M d, Y') }}</small></td>

                            <td>
                                <div class="d-flex gap-1">
                                    <a href="{{ route('admin.logo-creator.show', $brief) }}"
                                       class="btn btn-sm btn-outline-primary d-flex align-items-center justify-content-center"
                                       data-bs-toggle="tooltip" title="View Details"
                                       style="width:34px;height:34px;padding:0;">
                                        <i class="fas fa-eye"></i>
                                    </a>
                                    <form action="{{ route('admin.logo-creator.destroy', $brief->id) }}"
                                          method="POST" class="d-inline">
                                        @csrf @method('DELETE')
                                        <button type="submit"
                                                class="btn btn-sm btn-outline-danger d-flex align-items-center justify-content-center"
                                                style="width:34px;height:34px;padding:0;"
                                                onclick="return confirm('Delete this logo creator brief?');">
                                            <i class="fas fa-trash"></i>
                                        </button>
                                    </form>
                                </div>
                            </td>
                        </tr>
                        @empty
                        <tr>
                            <td colspan="8" class="text-center py-5">
                                <div class="text-muted">
                                    <i class="fas fa-paint-brush fa-3x mb-3"></i>
                                    <h5>No logo creator briefs found</h5>
                                    <p class="mb-0">
                                        @if(request('search'))
                                            No briefs match your search.
                                            <a href="{{ route('admin.logo-creator.index') }}" class="text-primary">Clear search</a>
                                        @else
                                            New submissions will appear here.
                                        @endif
                                    </p>
                                </div>
                            </td>
                        </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>

            @if($briefs->hasPages())
                <div class="p-3 border-top">
                    <div class="d-flex justify-content-between align-items-center">
                        <div class="text-muted small">
                            Showing {{ $briefs->firstItem() ?? 0 }} to {{ $briefs->lastItem() ?? 0 }}
                            of {{ $briefs->total() }} entries
                        </div>
                        {{ $briefs->withQueryString()->links('pagination::bootstrap-4') }}
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
    $('[data-bs-toggle="tooltip"]').tooltip();
});
</script>
@endsection