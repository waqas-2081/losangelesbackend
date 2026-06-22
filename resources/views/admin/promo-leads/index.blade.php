@extends('layouts.app')

@section('title', 'Promo Leads')

@section('content')
<div class="container-fluid">

    <div class="d-flex justify-content-between align-items-center mb-4">
        <div>
            <h4 class="mb-1">
                <i class="fas fa-bullhorn me-2 text-primary"></i> Promo Leads
            </h4>
            <nav aria-label="breadcrumb">
                <ol class="breadcrumb mb-0">
                    <li class="breadcrumb-item"><a href="{{ route('admin.dashboard') }}">Dashboard</a></li>
                    <li class="breadcrumb-item active">Promo Leads</li>
                </ol>
            </nav>
        </div>
    </div>

    @if(session('success'))
        <div class="alert alert-success alert-dismissible fade show">
            <i class="fas fa-check-circle me-2"></i> {{ session('success') }}
            <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
        </div>
    @endif

    <div class="card shadow-sm border-0 mb-4">
        <div class="card-body py-3">
            <form method="GET" action="{{ route('admin.promo-leads.index') }}">
                <div class="row g-3">
                    <div class="col-md-5">
                        <input type="text" name="search" class="form-control"
                               placeholder="Search by name, email, phone..."
                               value="{{ request('search') }}">
                    </div>
                    <div class="col-md-3">
                        <select name="source" class="form-select">
                            <option value="">All Sources</option>
                            <option value="home_promo_popup" {{ request('source') == 'home_promo_popup' ? 'selected' : '' }}>
                                Home Promo Popup
                            </option>
                        </select>
                    </div>
                    <div class="col-md-2">
                        <div class="d-flex gap-2">
                            <button type="submit" class="btn btn-primary flex-grow-1">
                                <i class="fas fa-filter me-1"></i> Filter
                            </button>
                            <a href="{{ route('admin.promo-leads.index') }}" class="btn btn-outline-secondary">
                                <i class="fas fa-redo"></i>
                            </a>
                        </div>
                    </div>
                </div>
            </form>
        </div>
    </div>

    <div class="card shadow-sm border-0">
        <div class="card-body p-0">
            <div class="table-responsive">
                <table class="table table-hover align-middle mb-0">
                    <thead class="table-light">
                        <tr>
                            <th width="50">#</th>
                            <th>Name</th>
                            <th>Email</th>
                            <th>Phone</th>
                            <th>Project Details</th>
                            <th>Source</th>
                            <th>Date</th>
                            <th width="80">Action</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse($leads as $lead)
                        <tr>
                            <td><strong class="text-primary">{{ $leads->firstItem() + $loop->index }}</strong></td>
                            <td>{{ $lead->customer_name }}</td>
                            <td>
                                <a href="mailto:{{ $lead->customer_email }}">
                                    {{ $lead->customer_email }}
                                </a>
                            </td>
                            <td>{{ $lead->customer_phone }}</td>
                            <td>
                                <span title="{{ $lead->project_details }}">
                                    {{ Str::limit($lead->project_details, 60) }}
                                </span>
                            </td>
                            <td>
                                <span class="badge bg-primary bg-opacity-10 text-primary">
                                    {{ $lead->source }}
                                </span>
                            </td>
                            <td><small>{{ $lead->created_at->format('M d, Y') }}</small></td>
                            <td>
                                <form action="{{ route('admin.promo-leads.destroy', $lead) }}" method="POST">
                                    @csrf
                                    @method('DELETE')
                                    <button type="submit" class="btn btn-sm btn-outline-danger"
                                            onclick="return confirm('Delete this lead?')">
                                        <i class="fas fa-trash"></i>
                                    </button>
                                </form>
                            </td>
                        </tr>
                        @empty
                        <tr>
                            <td colspan="8" class="text-center py-5 text-muted">
                                <i class="fas fa-inbox fa-3x mb-3 d-block"></i>
                                No promo leads found.
                            </td>
                        </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>

            @if($leads->hasPages())
                <div class="p-3 border-top d-flex justify-content-between align-items-center">
                    <div class="text-muted small">
                        Showing {{ $leads->firstItem() }} to {{ $leads->lastItem() }} of {{ $leads->total() }} entries
                    </div>
                    {{ $leads->withQueryString()->links('pagination::bootstrap-4') }}
                </div>
            @endif
        </div>
    </div>

</div>
@endsection