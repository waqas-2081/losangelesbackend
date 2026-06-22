@extends('layouts.app')

@section('title', 'Manage Portfolio')

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

    .category-badge {
        padding: 4px 10px;
        border-radius: 15px;
        font-size: 11px;
        font-weight: 600;
        background: #e0f2fe;
        color: #0369a1;
    }

    .filter-card {
        background: white;
        border-radius: 12px;
        padding: 20px;
        margin-bottom: 24px;
        box-shadow: 0 1px 3px rgba(0, 0, 0, 0.1);
    }

    .drag-handle {
        cursor: move;
        color: #9ca3af;
        font-size: 18px;
        padding: 0 10px;
    }

    .drag-handle:hover {
        color: #6b7280;
    }

    .portfolio-image {
        width: 60px;
        height: 60px;
        object-fit: cover;
        border-radius: 8px;
        border: 1px solid #e5e7eb;
    }

    .sortable-ghost {
        opacity: 0.5;
        background: #f3f4f6;
    }

    .sortable-drag {
        box-shadow: 0 4px 12px rgba(0, 0, 0, 0.15);
    }

    .bulk-actions-bar {
        display: none;
        align-items: center;
        justify-content: space-between;
        background: #fff7ed;
        border: 1px solid #fed7aa;
        border-radius: 10px;
        padding: 12px 18px;
        margin-bottom: 16px;
    }

    .bulk-actions-bar.show {
        display: flex;
    }

    .bulk-selected-count {
        font-weight: 600;
        color: #c2410c;
        font-size: 0.9rem;
    }

    .row-checkbox,
    #selectAllCheckbox {
        width: 18px;
        height: 18px;
        cursor: pointer;
    }
</style>

<div class="container-fluid">

    {{-- Page Header --}}
    <div class="d-flex justify-content-between align-items-center mb-4">
        <div>
            <h4 class="mb-1">
                <i class="fas fa-images me-2 text-primary"></i>
                Portfolio Management
            </h4>
            <nav aria-label="breadcrumb">
                <ol class="breadcrumb mb-0">
                    <li class="breadcrumb-item"><a href="{{ route('admin.dashboard') }}">Dashboard</a></li>
                    <li class="breadcrumb-item active">Portfolio</li>
                </ol>
            </nav>
        </div>
        <a href="{{ route('admin.portfolios.create') }}" class="btn btn-primary">
            <i class="fas fa-plus me-1"></i> Add New Portfolio
        </a>
    </div>

    {{-- Session Messages --}}
    @if(session('success'))
        <div class="alert alert-success alert-dismissible fade show" role="alert">
            <i class="fas fa-check-circle me-2"></i> {{ session('success') }}
            <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
        </div>
    @endif

    {{-- Filters --}}
    <div class="filter-card">
        <form method="GET" action="{{ route('admin.portfolios.index') }}">
            <div class="row g-3">
                <div class="col-md-4">
                    <label class="form-label fw-bold small text-uppercase text-muted">
                        <i class="fas fa-folder me-1"></i> Category
                    </label>
                    <select name="category" class="form-select">
                        <option value="">All Categories</option>
                        @foreach($categories as $key => $cat)
                            <option value="{{ $key }}" {{ request('category') == $key ? 'selected' : '' }}>
                                {{ $cat }}
                            </option>
                        @endforeach
                    </select>
                </div>

                <div class="col-md-4">
                    <label class="form-label fw-bold small text-uppercase text-muted">
                        <i class="fas fa-flag me-1"></i> Status
                    </label>
                    <select name="status" class="form-select">
                        <option value="">All Status</option>
                        <option value="1" {{ request('status') == '1' ? 'selected' : '' }}>Active</option>
                        <option value="0" {{ request('status') == '0' ? 'selected' : '' }}>Inactive</option>
                    </select>
                </div>

                <div class="col-md-4">
                    <label class="form-label d-block">&nbsp;</label>
                    <div class="d-flex gap-2">
                        <button type="submit" class="btn btn-primary flex-grow-1">
                            <i class="fas fa-filter me-1"></i> Apply
                        </button>
                        <a href="{{ route('admin.portfolios.index') }}" class="btn btn-outline-secondary">
                            <i class="fas fa-redo"></i>
                        </a>
                    </div>
                </div>
            </div>
        </form>
    </div>

    {{-- Bulk Actions Bar --}}
    <div class="bulk-actions-bar" id="bulkActionsBar">
        <span class="bulk-selected-count"><span id="selectedCount">0</span> item(s) selected</span>
        <div class="d-flex gap-2">
            <button type="button" class="btn btn-sm btn-outline-secondary" id="clearSelectionBtn">
                <i class="fas fa-times me-1"></i> Clear Selection
            </button>
            <button type="button" class="btn btn-sm btn-danger" id="bulkDeleteBtn">
                <i class="fas fa-trash me-1"></i> Delete Selected
            </button>
        </div>
    </div>

    {{-- Portfolio Table --}}
    <div class="card shadow-sm border-0">
        <div class="card-body p-0">
            <div class="table-responsive">
                <table class="table table-hover align-middle mb-0">
                    <thead class="table-light">
                        <tr>
                            <th width="40">
                                <input type="checkbox" id="selectAllCheckbox">
                            </th>
                            <th width="50">#</th>
                            <th width="100">Image</th>
                            <th>Category</th>
                            <th>Status</th>
                            <th width="100">Sort</th>
                            <th width="120">Actions</th>
                        </tr>
                    </thead>
                    <tbody id="sortable-table">
                        @forelse($portfolios as $portfolio)
                            <tr data-id="{{ $portfolio->id }}">
                                <td>
                                    <input type="checkbox" class="row-checkbox" value="{{ $portfolio->id }}">
                                </td>
                                <td>
                                    <span class="text-muted">{{ $loop->iteration }}</span>
                                </td>
                                <td>
                                    @if($portfolio->image)
                                        <img src="{{ asset('storage/' . $portfolio->image) }}" alt="Portfolio"
                                            class="portfolio-image">
                                    @else
                                        <div class="portfolio-image bg-light d-flex align-items-center justify-content-center">
                                            <i class="fas fa-image text-muted"></i>
                                        </div>
                                    @endif
                                </td>
                                <td>
                                    <span class="category-badge">
                                        <i class="fas fa-folder me-1"></i>
                                        {{ $portfolio->category }}
                                    </span>
                                </td>
                                <td>
                                    @if($portfolio->is_active)
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
                                    <span class="badge bg-secondary px-3 py-2 sort-order-display">
                                        {{ $portfolio->sort_order }}
                                    </span>
                                </td>
                                <td>
                                    <div class="d-flex gap-1">
                                        <a href="{{ route('admin.portfolios.edit', $portfolio) }}"
                                            class="btn btn-sm btn-outline-primary d-flex align-items-center justify-content-center"
                                            data-bs-toggle="tooltip" title="Edit"
                                            style="width: 34px; height: 34px; padding: 0;">
                                            <i class="fas fa-edit"></i>
                                        </a>

                                        <form action="{{ route('admin.portfolios.destroy', $portfolio->id) }}" method="POST"
                                            class="d-inline"
                                            onsubmit="return confirm('Are you sure you want to delete this item?');">
                                            @csrf
                                            @method('DELETE')
                                            <button type="submit"
                                                class="btn btn-sm btn-outline-danger d-flex align-items-center justify-content-center"
                                                data-bs-toggle="tooltip" title="Delete"
                                                style="width: 34px; height: 34px; padding: 0;">
                                                <i class="fas fa-trash"></i>
                                            </button>
                                        </form>

                                        <span class="drag-handle" data-bs-toggle="tooltip" title="Drag to reorder">
                                            <i class="fas fa-grip-vertical"></i>
                                        </span>
                                    </div>
                                </td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="7" class="text-center py-5">
                                    <div class="text-muted">
                                        <i class="fas fa-images fa-3x mb-3"></i>
                                        <h5>No portfolio items found</h5>
                                        <p class="mb-3">
                                            @if(request('category') || request('status') !== null)
                                                No items match your filters.
                                                <a href="{{ route('admin.portfolios.index') }}" class="text-primary">Clear filters</a>
                                            @else
                                                Start by adding your first portfolio item.
                                            @endif
                                        </p>
                                        @if(!request('category') && request('status') === null)
                                            <a href="{{ route('admin.portfolios.create') }}" class="btn btn-primary">
                                                <i class="fas fa-plus me-1"></i> Add First Item
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
            @if($portfolios->hasPages())
                <div class="p-3 border-top">
                    <div class="d-flex justify-content-between align-items-center">
                        <div class="text-muted small">
                            Showing {{ $portfolios->firstItem() ?? 0 }} to {{ $portfolios->lastItem() ?? 0 }}
                            of {{ $portfolios->total() }} entries
                        </div>
                        <div>
                            {{ $portfolios->withQueryString()->links('pagination::bootstrap-4') }}
                        </div>
                    </div>
                </div>
            @endif
        </div>
    </div>
</div>

{{-- Bulk Delete Form - FIXED --}}
<form id="bulkDeleteForm" action="{{ route('admin.portfolios.bulk-destroy') }}" method="POST">
    @csrf
    @method('DELETE')
    <div id="bulkIdsContainer"></div>
</form>

@endsection

@section('scripts')
<script src="https://cdn.jsdelivr.net/npm/sortablejs@latest/Sortable.min.js"></script>
<script>
document.addEventListener('DOMContentLoaded', function() {
    // Initialize tooltips
    if (typeof bootstrap !== 'undefined') {
        document.querySelectorAll('[data-bs-toggle="tooltip"]').forEach(el => {
            new bootstrap.Tooltip(el);
        });
    }

    // Drag & Drop
    const tableBody = document.getElementById('sortable-table');
    if (tableBody) {
        new Sortable(tableBody, {
            handle: '.drag-handle',
            animation: 150,
            ghostClass: 'sortable-ghost',
            dragClass: 'sortable-drag',
            onEnd: function() {
                const rows = tableBody.querySelectorAll('tr');
                const orders = [];

                rows.forEach((row, index) => {
                    const id = row.dataset.id;
                    const sortDisplay = row.querySelector('.sort-order-display');
                    if (sortDisplay) {
                        sortDisplay.textContent = index + 1;
                    }
                    orders.push({
                        id: id,
                        sort_order: index + 1
                    });
                });

                fetch('{{ route("admin.portfolios.reorder") }}', {
                    method: 'POST',
                    headers: {
                        'Content-Type': 'application/json',
                        'X-CSRF-TOKEN': '{{ csrf_token() }}'
                    },
                    body: JSON.stringify({ orders: orders })
                })
                .then(response => response.json())
                .then(data => {
                    console.log('Order saved');
                })
                .catch(error => {
                    alert('Error saving order. Please try again.');
                    location.reload();
                });
            }
        });
    }

    // Bulk selection logic
    function updateBulkBar() {
        const checked = document.querySelectorAll('.row-checkbox:checked');
        const count = checked.length;

        document.getElementById('selectedCount').textContent = count;

        const bulkBar = document.getElementById('bulkActionsBar');
        if (count > 0) {
            bulkBar.classList.add('show');
        } else {
            bulkBar.classList.remove('show');
        }

        const totalCheckboxes = document.querySelectorAll('.row-checkbox').length;
        const selectAll = document.getElementById('selectAllCheckbox');
        selectAll.checked = count === totalCheckboxes && totalCheckboxes > 0;
    }

    // Select all
    document.getElementById('selectAllCheckbox').addEventListener('change', function() {
        document.querySelectorAll('.row-checkbox').forEach(cb => {
            cb.checked = this.checked;
        });
        updateBulkBar();
    });

    // Individual checkbox
    document.querySelectorAll('.row-checkbox').forEach(cb => {
        cb.addEventListener('change', updateBulkBar);
    });

    // Clear selection
    document.getElementById('clearSelectionBtn').addEventListener('click', function() {
        document.querySelectorAll('.row-checkbox').forEach(cb => {
            cb.checked = false;
        });
        document.getElementById('selectAllCheckbox').checked = false;
        updateBulkBar();
    });

    // Bulk delete - FIXED
    document.getElementById('bulkDeleteBtn').addEventListener('click', function(e) {
        e.preventDefault();
        
        const checkedBoxes = document.querySelectorAll('.row-checkbox:checked');
        const ids = Array.from(checkedBoxes).map(cb => cb.value);

        if (ids.length === 0) {
            alert('Please select at least one item to delete.');
            return;
        }

        if (!confirm(`Are you sure you want to delete ${ids.length} selected item(s)? This action cannot be undone.`)) {
            return;
        }

        // Clear and add IDs to form
        const container = document.getElementById('bulkIdsContainer');
        container.innerHTML = '';
        ids.forEach(id => {
            const input = document.createElement('input');
            input.type = 'hidden';
            input.name = 'ids[]';
            input.value = id;
            container.appendChild(input);
        });

        // Submit the form
        const form = document.getElementById('bulkDeleteForm');
        console.log('Submitting bulk delete with IDs:', ids);
        form.submit();
    });

    // Initial update
    updateBulkBar();
});
</script>
@endsection