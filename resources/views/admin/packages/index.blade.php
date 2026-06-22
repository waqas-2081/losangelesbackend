{{-- resources/views/admin/packages/index.blade.php --}}
@extends('layouts.app')

@section('title', 'Manage Packages')

@section('content')

    <style>
        .status-badge {
            padding: 5px 12px;
            border-radius: 20px;
            font-size: 12px;
            font-weight: 600;
            display: inline-block;
        }

        .status-active {
            background: #dcfce7;
            color: #166534;
        }

        .status-inactive {
            background: #f3f4f6;
            color: #4b5563;
        }

        .service-badge {
            padding: 4px 10px;
            border-radius: 15px;
            font-size: 11px;
            font-weight: 600;
            background: #e0f2fe;
            color: #0369a1;
        }

        .price-type-badge {
            padding: 4px 10px;
            border-radius: 15px;
            font-size: 11px;
            font-weight: 600;
            background: #f3e8ff;
            color: #7c3aed;
        }

        .badge-custom {
            background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
            color: white;
            padding: 4px 10px;
            border-radius: 20px;
            font-size: 11px;
            font-weight: 600;
        }

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
            box-shadow: 0 1px 3px rgba(0, 0, 0, 0.1);
        }
    </style>

    <div class="container-fluid">

        {{-- Page Header --}}
        <div class="d-flex justify-content-between align-items-center mb-4">
            <div>
                <h4 class="mb-1">
                    <i class="fas fa-box me-2 text-primary"></i>
                    Package Management
                </h4>
                <nav aria-label="breadcrumb">
                    <ol class="breadcrumb mb-0">
                        <li class="breadcrumb-item"><a href="{{ route('admin.dashboard') }}">Dashboard</a></li>
                        <li class="breadcrumb-item active">Packages</li>
                    </ol>
                </nav>
            </div>
            <a href="{{ route('admin.packages.create') }}" class="btn btn-primary">
                <i class="fas fa-plus me-1"></i> Add New Package
            </a>
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
            <form method="GET" action="{{ route('admin.packages.index') }}" id="filterForm">
                <div class="row g-3">
                    {{-- Search --}}
                    <div class="col-md-4">
                        <label class="form-label fw-bold small text-uppercase text-muted">
                            <i class="fas fa-search me-1"></i> Search
                        </label>
                        <input type="text" name="search" class="form-control" placeholder="Search by name, badge, slug..."
                            value="{{ request('search') }}">
                    </div>
                    @php
                        $serviceLabels = [
                            'logo-design-services' => 'Logo Design',
                            'website-design-development-services' => 'Website Development',
                            'video-animation-services' => 'Video Animation',
                            'mobile-app-development-services' => 'Mobile App Development',
                            'social-media-marketing-services' => 'Social Media Marketing',
                            'search-engine-optimization-services' => 'SEO',
                        ];
                    @endphp
                    {{-- Service Filter --}}
                    <div class="col-md-3">
                        <label class="form-label fw-bold small text-uppercase text-muted">
                            <i class="fas fa-tag me-1"></i> Service Type
                        </label>
                        <select name="service_type" class="form-select">
                            <option value="">All Services</option>
                            @foreach($services as $service)
                                <option value="{{ $service }}" {{ request('service_type') == $service ? 'selected' : '' }}>
                                    {{ $serviceLabels[$service] ?? $service }}
                                </option>
                            @endforeach
                        </select>
                    </div>

                    {{-- Status Filter --}}
                    <div class="col-md-3">
                        <label class="form-label fw-bold small text-uppercase text-muted">
                            <i class="fas fa-flag me-1"></i> Status
                        </label>
                        <select name="status" class="form-select">
                            <option value="">All Status</option>
                            <option value="1" {{ request('status') == '1' ? 'selected' : '' }}>Active</option>
                            <option value="0" {{ request('status') == '0' ? 'selected' : '' }}>Inactive</option>
                        </select>
                    </div>

                    {{-- Actions --}}
                    <div class="col-md-2">
                        <label class="form-label d-block">&nbsp;</label>
                        <div class="d-flex gap-2">
                            <button type="submit" class="btn btn-primary flex-grow-1">
                                <i class="fas fa-filter me-1"></i> Apply
                            </button>
                            <a href="{{ route('admin.packages.index') }}" class="btn btn-outline-secondary">
                                <i class="fas fa-redo"></i>
                            </a>
                        </div>
                    </div>
                </div>

                {{-- Active Filters Display --}}
                @if(request('search') || request('service_type') || request('status') !== null)
                    <div class="mt-3">
                        <small class="text-muted">Active Filters:</small>
                        <div class="d-flex flex-wrap gap-2 mt-1">
                            @if(request('search'))
                                <span class="badge bg-light text-dark border px-3 py-2">
                                    <i class="fas fa-search me-1"></i>
                                    Search: "{{ request('search') }}"
                                    <a href="{{ route('admin.packages.index', array_merge(request()->except('search'), ['service_type' => request('service_type'), 'status' => request('status')])) }}"
                                        class="text-danger ms-2 text-decoration-none">
                                        <i class="fas fa-times"></i>
                                    </a>
                                </span>
                            @endif

                            @if(request('service_type'))
                                <span class="badge bg-light text-dark border px-3 py-2">
                                    <i class="fas fa-tag me-1"></i>
                                    Service: {{ $serviceLabels[request('service_type')] ?? request('service_type') }}
                                    <a href="{{ route('admin.packages.index', array_merge(request()->except('service_type'), ['search' => request('search'), 'status' => request('status')])) }}"
                                        class="text-danger ms-2 text-decoration-none">
                                        <i class="fas fa-times"></i>
                                    </a>
                                </span>
                            @endif

                            @if(request('status') !== null && request('status') !== '')
                                <span class="badge bg-light text-dark border px-3 py-2">
                                    <i class="fas fa-flag me-1"></i>
                                    Status: {{ request('status') == '1' ? 'Active' : 'Inactive' }}
                                    <a href="{{ route('admin.packages.index', array_merge(request()->except('status'), ['search' => request('search'), 'service_type' => request('service_type')])) }}"
                                        class="text-danger ms-2 text-decoration-none">
                                        <i class="fas fa-times"></i>
                                    </a>
                                </span>
                            @endif
                        </div>
                    </div>
                @endif
            </form>
        </div>

        {{-- Packages Table --}}
        <div class="card shadow-sm border-0">
            <div class="card-body p-0">
                <div class="table-responsive">
                    <table class="table table-hover align-middle mb-0">
                        <thead class="table-light">
                            <tr>
                                <th width="50">#</th>
                                <th>Package Details</th>
                                <th>Service</th>
                                <th>Price</th>
                                <th>Features</th>
                                <th>Status</th>
                                <th>Order</th>
                                <th width="120">Actions</th>
                            </tr>
                        </thead>
                        <tbody>
                            @forelse($packages as $package)
                                <tr>
                                    <td>
                                        <strong class="text-primary">{{ ($packages->firstItem() ?? 0) + $loop->index }}</strong>
                                    </td>

                                    <td>
                                        <div class="d-flex flex-column">
                                            <div class="d-flex align-items-center gap-2">
                                                <span class="fw-bold">{{ $package->name }}</span>
                                            </div>
                                            <small class="text-muted">
                                                <i class="fas fa-link me-1"></i>{{ $package->slug }}
                                            </small>
                                        </div>
                                    </td>

                                    <td>
                                        <span class="service-badge">
                                            {{ $serviceLabels[$package->service_type] ?? $package->service_type }}
                                        </span>
                                    </td>

                                    <td>
                                        <div class="d-flex flex-column">
                                            <span class="fw-bold text-primary">${{ number_format($package->price, 2) }}</span>
                                            <span class="price-type-badge mt-1">
                                                {{ $package->price_type === 'one_time' ? 'One Time' : 'Per Project' }}
                                            </span>
                                        </div>
                                    </td>

                                    <td>
                                        <span class="badge bg-light text-dark border px-3 py-2">
                                            <i class="fas fa-list-check me-1"></i>
                                            {{ is_array($package->features) ? count($package->features) : 0 }} features
                                        </span>
                                    </td>

                                    <td>
                                        @if($package->is_active)
                                            <span class="status-badge status-active">
                                                <i class="fas fa-check-circle me-1"></i>Active
                                            </span>
                                        @else
                                            <span class="status-badge status-inactive">
                                                <i class="fas fa-minus-circle me-1"></i>Inactive
                                            </span>
                                        @endif
                                    </td>

                                    <td>
                                        <span class="badge bg-secondary px-3 py-2">{{ $package->sort_order ?? 0 }}</span>
                                    </td>

                                    <td>
                                        <div class="d-flex gap-1">

                                            <a href="{{ route('admin.packages.edit', $package) }}"
                                                class="btn btn-sm btn-outline-primary d-flex align-items-center justify-content-center"
                                                data-bs-toggle="tooltip" title="Edit Package"
                                                style="width: 34px; height: 34px; padding: 0;">
                                                <i class="fas fa-edit"></i>
                                            </a>

                                            <form action="{{ route('admin.packages.destroy', $package->id) }}" method="POST"
                                                class="d-inline delete-form">
                                                @csrf
                                                @method('DELETE')
                                                <button type="submit"
                                                    class="btn btn-sm btn-outline-danger d-flex align-items-center justify-content-center delete-btn"
                                                    data-id="{{ $package->id }}" data-name="{{ $package->name }}"
                                                    data-bs-toggle="tooltip" title="Delete"
                                                    style="width: 34px; height: 34px; padding: 0;"
                                                    onclick="return confirm('Are you sure you want to delete this package?');">
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
                                            <i class="fas fa-box-open fa-3x mb-3"></i>
                                            <h5>No packages found</h5>
                                            <p class="mb-0">
                                                @if(request('search') || request('service_type') || request('status') !== null)
                                                    No packages match your filters.
                                                    <a href="{{ route('admin.packages.index') }}" class="text-primary">Clear
                                                        filters</a>
                                                @else
                                                    Start by creating your first package.
                                                @endif
                                            </p>
                                            @if(!request('search') && !request('service_type') && request('status') === null)
                                                <a href="{{ route('admin.packages.create') }}" class="btn btn-primary mt-3">
                                                    <i class="fas fa-plus me-1"></i> Create First Package
                                                </a>
                                            @endif
                                        </div>
                                    </td>
                                </tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>

                {{-- Pagination --}}
                @if($packages->hasPages())
                    <div class="p-3 border-top">
                        <div class="d-flex justify-content-between align-items-center">
                            <div class="text-muted small">
                                Showing {{ $packages->firstItem() ?? 0 }} to {{ $packages->lastItem() ?? 0 }}
                                of {{ $packages->total() }} entries
                            </div>
                            <div>
                                {{ $packages->withQueryString()->links('pagination::bootstrap-4') }}
                            </div>
                        </div>
                    </div>
                @endif
            </div>
        </div>
    </div>

    {{-- Delete Confirmation Modal --}}
    <div class="modal fade" id="deleteModal" tabindex="-1">
        <div class="modal-dialog modal-dialog-centered">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title text-danger">
                        <i class="fas fa-exclamation-triangle me-2"></i>
                        Confirm Delete
                    </h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                </div>
                <div class="modal-body text-center py-4">
                    <i class="fas fa-trash text-danger fa-3x mb-3"></i>
                    <h6>Delete this package?</h6>
                    <p class="text-muted mb-0" id="deletePackageName"></p>
                    <small class="text-danger">This action cannot be undone.</small>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancel</button>
                    <form id="deleteForm" method="POST">
                        @csrf
                        @method('DELETE')
                        <button type="submit" class="btn btn-danger">
                            <i class="fas fa-trash me-1"></i> Delete Permanently
                        </button>
                    </form>
                </div>
            </div>
        </div>
    </div>

@endsection

@section('scripts')
    <script>
        $(document).ready(function () {
            // Initialize tooltips
            $('[data-bs-toggle="tooltip"]').tooltip();

            // Delete confirmation modal
            $('.delete-btn').click(function (e) {
                e.preventDefault();
                const id = $(this).data('id');
                const name = $(this).data('name');

                $('#deletePackageName').text(`Package: ${name}`);
                $('#deleteForm').attr('action', `/admin/packages/${id}`);
                $('#deleteModal').modal('show');
            });
        });
    </script>
@endsection