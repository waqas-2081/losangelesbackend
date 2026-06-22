{{-- resources/views/admin/contacts/index.blade.php --}}
@extends('layouts.app')

@section('title', 'Contact Inquiries')

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
    .status-archived { background: #f3f4f6; color: #4b5563; }
    
    
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
                <i class="fas fa-envelope-open-text me-2 text-primary"></i>
                Contact Inquiries
            </h4>
            <nav aria-label="breadcrumb">
                <ol class="breadcrumb mb-0">
                    <li class="breadcrumb-item"><a href="{{ route('admin.dashboard') }}">Dashboard</a></li>
                    <li class="breadcrumb-item active">Contacts</li>
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

    {{-- Filters --}}
    <div class="filter-card">
        <form method="GET" action="{{ route('admin.contacts.index') }}" id="filterForm">
            <div class="row g-3">
                {{-- Search --}}
                <div class="col-md-4">
                    <label class="form-label fw-bold small text-uppercase text-muted">
                        <i class="fas fa-search me-1"></i> Search
                    </label>
                    <input type="text" 
                           name="search" 
                           class="form-control" 
                           placeholder="Search by name, email, company..."
                           value="{{ request('search') }}">
                </div>


                {{-- Status Filter --}}
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

                {{-- Actions --}}
                <div class="col-md-2">
                    <label class="form-label d-block">&nbsp;</label>
                    <div class="d-flex gap-2">
                        <button type="submit" class="btn btn-primary flex-grow-1">
                            <i class="fas fa-filter me-1"></i> Apply
                        </button>
                        <a href="{{ route('admin.contacts.index') }}" class="btn btn-outline-secondary">
                            <i class="fas fa-redo"></i>
                        </a>
                    </div>
                </div>
            </div>
        </form>
    </div>

   

    {{-- Contacts Table --}}
    <div class="card shadow-sm border-0">
        <div class="card-body p-0">
            <div class="table-responsive">
                <table class="table table-hover align-middle mb-0">
                    <thead class="table-light">
                        <tr>
                            <th width="50">#</th>
                            <th>Contact Info</th>
                            <th>Company</th>
                            <th>Status</th>
                            <th>Date</th>
                            <th width="120">Actions</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse($contacts as $contact)
                        <tr>
                            <td>
                                <strong class="text-primary">{{ ($contacts->firstItem() ?? 0) + $loop->index }}</strong>
                            </td>
                       
                            <td>
                                <div class="d-flex flex-column">
                                    <span class="fw-bold">{{ $contact->full_name }}</span>
                                    <small class="text-muted">{{ $contact->email }}</small>
                                </div>
                            </td>
                            <td>
                                <span class="text-dark">{{ $contact->company_name ?: '—' }}</span>
                            </td>
                            <td>
                                <span class="status-badge status-{{ $contact->status }}">
                                    {{ $statuses[$contact->status] ?? ucfirst($contact->status) }}
                                </span>
                            </td>
                            <td>
                                <div class="d-flex flex-column">
                                    <small>{{ $contact->created_at->format('M d, Y') }}</small>
                                </div>
                            </td>
                            <td>
                                <div class="d-flex gap-1">
                                    <a href="{{ route('admin.contacts.show', $contact) }}" 
                                       class="btn btn-sm btn-outline-primary d-flex align-items-center justify-content-center"
                                       data-bs-toggle="tooltip" 
                                       title="View Details"
                                       style="width: 34px; height: 30px; padding: 0;">
                                        <i class="fas fa-eye"></i>
                                    </a>
                                    <form action="{{ route('admin.contacts.destroy', $contact->id) }}" method="POST" class="d-inline delete-form" style="margin-left: 4px;">
                                        @csrf
                                        @method('DELETE')
                                        <button type="submit"
                                                class="btn btn-sm btn-outline-danger d-flex align-items-center justify-content-center delete-btn"
                                                data-id="{{ $contact->id }}"
                                                data-name="{{ $contact->full_name }}"
                                                data-bs-toggle="tooltip"
                                                title="Delete"
                                                style="width: 34px; height: 34px; padding: 0;"
                                                onclick="return confirm('Are you sure you want to delete this contact?');">
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
                                    <i class="fas fa-inbox fa-3x mb-3"></i>
                                    <h5>No contact inquiries found</h5>
                                    <p class="mb-0">New contact form submissions will appear here.</p>
                                </div>
                            </td>
                        </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>

            {{-- Pagination --}}
            @if($contacts->hasPages())
                <div class="p-3 border-top">
                    <div class="d-flex justify-content-between align-items-center">
                        <div class="text-muted small">
                            Showing {{ $contacts->firstItem() ?? 0 }} to {{ $contacts->lastItem() ?? 0 }} 
                            of {{ $contacts->total() }} entries
                        </div>
                        <div>
                            {{ $contacts->withQueryString()->links('pagination::bootstrap-4') }}
                        </div>
                    </div>
                </div>
            @endif
        </div>
    </div>
</div>

@endsection
