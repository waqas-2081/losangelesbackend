{{-- resources/views/admin/contacts/show.blade.php --}}
@extends('layouts.app')

@section('title', 'Contact Inquiry #' . $contact->id)

@section('content')

<style>
    .detail-card { background: white; border-radius: 16px; padding: 24px; box-shadow: 0 1px 3px rgba(0,0,0,0.08); margin-bottom: 24px; border: 1px solid #f0f0f0; }
    .detail-title { font-size: 14px; font-weight: 700; margin-bottom: 20px; padding-bottom: 12px; border-bottom: 1px solid #eee; display: flex; align-items: center; gap: 8px; letter-spacing: 0.5px; text-transform: uppercase; color: #333; }
    .info-row { display: flex; margin-bottom: 14px; font-size: 14px; }
    .info-label { width: 160px; color: #888; flex-shrink: 0; font-weight: 500; }
    .info-value { flex: 1; font-weight: 500; color: #1a1a1a; word-break: break-word; }
    .status-badge { padding: 6px 16px; border-radius: 20px; font-size: 13px; font-weight: 600; display: inline-block; }
    .status-pending { background: #fef3c7; color: #92400e; }
    .status-in_progress { background: #dbeafe; color: #1e40af; }
    .status-completed { background: #dcfce7; color: #166534; }
    .status-archived { background: #f3f4f6; color: #4b5563; }
    
    .timeline-item {
        padding-left: 24px;
        border-left: 2px solid #e5e7eb;
        position: relative;
        margin-bottom: 20px;
    }
    
    .timeline-item::before {
        content: '';
        position: absolute;
        left: -6px;
        top: 0;
        width: 10px;
        height: 10px;
        border-radius: 50%;
        background: #3b82f6;
    }
</style>

@php
    $statuses = App\Models\Contact::getStatuses();
@endphp

<div class="container-fluid">
    
    {{-- Header --}}
    <div class="d-flex justify-content-between align-items-center mb-4">
        <div>
            <h4 class="mb-1">
                <i class="fas fa-envelope-open-text me-2 text-primary"></i>
                Contact Inquiry #{{ $contact->id }}
            </h4>
            <nav aria-label="breadcrumb">
                <ol class="breadcrumb mb-0 small">
                    <li class="breadcrumb-item"><a href="{{ route('admin.dashboard') }}">Dashboard</a></li>
                    <li class="breadcrumb-item"><a href="{{ route('admin.contacts.index') }}">Contacts</a></li>
                    <li class="breadcrumb-item active">Inquiry #{{ $contact->id }}</li>
                </ol>
            </nav>
        </div>
        <div class="d-flex gap-2">
            <a href="mailto:{{ $contact->email }}" class="btn btn-outline-primary">
                <i class="fas fa-reply me-1"></i> Reply via Email
            </a>
            <button class="btn btn-outline-secondary" onclick="window.print()">
                <i class="fas fa-print me-1"></i> Print
            </button>
            <a href="{{ route('admin.contacts.index') }}" class="btn btn-outline-secondary">
                <i class="fas fa-arrow-left me-1"></i> Back
            </a>
        </div>
    </div>

    <div class="row">
        
        {{-- Left Column - Main Info --}}
        <div class="col-lg-8">
            
            {{-- Contact Information --}}
            <div class="detail-card">
                <h5 class="detail-title">
                    <i class="fas fa-user-circle text-primary"></i>
                    Contact Information
                </h5>
                
                <div class="info-row">
                    <div class="info-label">Full Name</div>
                    <div class="info-value fw-bold">{{ $contact->full_name }}</div>
                </div>
                
                <div class="info-row">
                    <div class="info-label">Email Address</div>
                    <div class="info-value">
                        <a href="mailto:{{ $contact->email }}" class="text-decoration-none">
                            {{ $contact->email }}
                        </a>
                    </div>
                </div>
                
                <div class="info-row">
                    <div class="info-label">Phone Number</div>
                    <div class="info-value">
                        <a href="tel:{{ $contact->phone_number }}" class="text-decoration-none">
                            {{ $contact->phone_number }}
                        </a>
                    </div>
                </div>
                
                <div class="info-row">
                    <div class="info-label">Company Name</div>
                    <div class="info-value">
                        {{ $contact->company_name ?: '—' }}
                    </div>
                </div>
                
            </div>
            
            {{-- Project Description --}}
            <div class="detail-card">
                <h5 class="detail-title">
                    <i class="fas fa-project-diagram text-primary"></i>
                    Project Description
                </h5>
                
                <div class="p-3 bg-light rounded">
                    <p class="mb-0" style="white-space: pre-wrap; line-height: 1.6;">
                        {{ $contact->project_description }}
                    </p>
                </div>
            </div>
            
            {{-- Timeline --}}
            <div class="detail-card">
                <h5 class="detail-title">
                    <i class="fas fa-history text-primary"></i>
                    Timeline
                </h5>
                
                <div class="timeline-item">
                    <div class="fw-bold">Inquiry Submitted</div>
                    <div class="text-muted small">
                        {{ $contact->created_at->format('F d, Y') }} at {{ $contact->created_at->format('h:i A') }}
                    </div>
                    <div class="text-muted small">
                        {{ $contact->created_at->diffForHumans() }}
                    </div>
                </div>
                
                @if($contact->created_at != $contact->updated_at)
                <div class="timeline-item">
                    <div class="fw-bold">Last Updated</div>
                    <div class="text-muted small">
                        {{ $contact->updated_at->format('F d, Y') }} at {{ $contact->updated_at->format('h:i A') }}
                    </div>
                    <div class="text-muted small">
                        {{ $contact->updated_at->diffForHumans() }}
                    </div>
                </div>
                @endif
            </div>
            
        </div>
        
        {{-- Right Column - Status & Actions --}}
        <div class="col-lg-4">
            
            {{-- Status Card --}}
            <div class="detail-card">
                <h5 class="detail-title">
                    <i class="fas fa-flag text-primary"></i>
                    Current Status
                </h5>
                
                <div class="text-center mb-3">
                    <span class="status-badge status-{{ $contact->status }}" id="currentStatusBadge" style="font-size: 16px;">
                        <i class="fas fa-circle me-1" style="font-size: 10px;"></i>
                        {{ $statuses[$contact->status] ?? ucfirst($contact->status) }}
                    </span>
                </div>
                
                <div class="mt-3">
                    <label class="form-label fw-bold small text-uppercase text-muted mb-2">
                        Update Status
                    </label>
                    <select name="status" class="form-select" id="statusSelect" data-id="{{ $contact->id }}">
                        @foreach($statuses as $value => $label)
                            <option value="{{ $value }}" {{ $contact->status == $value ? 'selected' : '' }}>
                                {{ $label }}
                            </option>
                        @endforeach
                    </select>
                    <div class="mt-2">
                        <small id="statusMessage"></small>
                    </div>
                </div>
            </div>
            
            {{-- Admin Notes --}}
            <div class="detail-card">
                <h5 class="detail-title">
                    <i class="fas fa-sticky-note text-primary"></i>
                    Admin Notes
                </h5>
                
                <div>
                    <textarea 
                        name="admin_notes" 
                        id="adminNotes" 
                        class="form-control" 
                        rows="6"
                        placeholder="Add internal notes about this inquiry..."
                    >{{ $contact->admin_notes }}</textarea>
                    
                    <button type="button" class="btn btn-primary w-100 mt-3" id="saveNotesBtn">
                        <i class="fas fa-save me-1"></i> Save Notes
                    </button>
                </div>
            </div>
            
            {{-- Quick Actions --}}
            <div class="detail-card">
                <h5 class="detail-title">
                    <i class="fas fa-bolt text-primary"></i>
                    Quick Actions
                </h5>
                
                <div class="d-grid gap-2">
                    <a href="mailto:{{ $contact->email }}?subject=RE: Your Project Inquiry #{{ $contact->id }}&body=Dear {{ $contact->full_name }},%0D%0A%0D%0A" 
                       class="btn btn-outline-primary">
                        <i class="fas fa-envelope me-1"></i> Reply to Client
                    </a>
                    
                    <a href="tel:{{ $contact->phone_number }}" class="btn btn-outline-secondary">
                        <i class="fas fa-phone me-1"></i> Call Client
                    </a>
                    
                    <button onclick="window.print()" class="btn btn-outline-secondary">
                        <i class="fas fa-print me-1"></i> Print Details
                    </button>
                    
                    <button class="btn btn-outline-danger" id="deleteContactBtn" data-id="{{ $contact->id }}">
                        <i class="fas fa-trash me-1"></i> Delete Inquiry
                    </button>
                </div>
            </div>
            
            {{-- Quick Info --}}
            <div class="detail-card">
                <h5 class="detail-title">
                    <i class="fas fa-info-circle text-primary"></i>
                    Quick Info
                </h5>
                
                <div class="info-row">
                    <div class="info-label">Inquiry ID</div>
                    <div class="info-value">#{{ $contact->id }}</div>
                </div>
                
                <div class="info-row">
                    <div class="info-label">Created</div>
                    <div class="info-value">{{ $contact->created_at->format('M d, Y') }}</div>
                </div>
                
                <div class="info-row">
                    <div class="info-label">Last Updated</div>
                    <div class="info-value">{{ $contact->updated_at->format('M d, Y') }}</div>
                </div>
            </div>
            
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
                <h6>Delete this inquiry?</h6>
                <p class="text-muted">Inquiry #{{ $contact->id }} from {{ $contact->full_name }}</p>
                <small class="text-danger">This action cannot be undone.</small>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancel</button>
                <form action="{{ route('admin.contacts.destroy', $contact) }}" method="POST">
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
<script src="https://code.jquery.com/jquery-3.7.1.min.js"></script>
<script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
<script>
$(document).ready(function() {
    
    // Status update - SAME PATTERN AS LOGO BRIEF
    $('#statusSelect').change(function() {
        const select       = $(this);
        const contactId    = select.data('id');
        const newStatus    = select.val();
        const originalStatus = select.find('option[selected]').val();
        const statusMessage = $('#statusMessage');
        
        select.prop('disabled', true);
        statusMessage.html('<i class="fas fa-spinner fa-spin"></i> Updating...').removeClass('text-danger text-success');

        // LOGO BRIEF JAISA PATTERN - _token directly data mein
        $.ajax({
            url: '/admin/contacts/' + contactId + '/status',
            type: 'POST',
            data: { 
                status: newStatus, 
                _token: '{{ csrf_token() }}' 
            },
            success: function(response) {
                if (response.success) {
                    statusMessage.html('<i class="fas fa-check-circle"></i> ' + response.message).addClass('text-success');
                    
                    // Update badge
                    const statusText = $('#statusSelect option:selected').text();
                    $('#currentStatusBadge')
                        .removeClass()
                        .addClass('status-badge status-' + newStatus)
                        .html('<i class="fas fa-circle me-1" style="font-size: 10px;"></i> ' + statusText);
                        
                    // Update selected attribute
                    select.find('option').removeAttr('selected');
                    select.find('option[value="' + newStatus + '"]').attr('selected', 'selected');
                    
                    // Show toast
                    Swal.fire({
                        icon: 'success',
                        title: 'Status Updated',
                        text: response.message,
                        toast: true,
                        position: 'top-end',
                        showConfirmButton: false,
                        timer: 2500,
                        timerProgressBar: true
                    });
                    
                    setTimeout(function() {
                        statusMessage.html('');
                    }, 3000);
                } else {
                    statusMessage.html('<i class="fas fa-exclamation-circle"></i> Update failed').addClass('text-danger');
                    select.val(originalStatus);
                }
            },
            error: function(xhr) {
                let errorMsg = 'Failed to update status.';
                if (xhr.responseJSON && xhr.responseJSON.message) {
                    errorMsg = xhr.responseJSON.message;
                }
                statusMessage.html('<i class="fas fa-exclamation-circle"></i> ' + errorMsg).addClass('text-danger');
                select.val(originalStatus);
                
                Swal.fire({
                    icon: 'error',
                    title: 'Error',
                    text: errorMsg
                });
            },
            complete: function() {
                select.prop('disabled', false);
            }
        });
    });
    
    // Save notes - SAME PATTERN AS LOGO BRIEF
    $('#saveNotesBtn').click(function() {
        const saveBtn   = $(this);
        const contactId = {{ $contact->id }};
        const notes     = $('#adminNotes').val();
        
        saveBtn.prop('disabled', true).html('<i class="fas fa-spinner fa-spin me-1"></i> Saving...');

        // LOGO BRIEF JAISA PATTERN - _token directly data mein
        $.ajax({
            url: '/admin/contacts/' + contactId + '/notes',
            type: 'POST',
            data: { 
                admin_notes: notes, 
                _token: '{{ csrf_token() }}' 
            },
            success: function(response) {
                if (response.success) {
                    Swal.fire({
                        icon: 'success',
                        title: 'Saved!',
                        text: response.message || 'Admin notes saved.',
                        toast: true,
                        position: 'top-end',
                        showConfirmButton: false,
                        timer: 2500,
                        timerProgressBar: true
                    });
                }
            },
            error: function(xhr) {
                let errorMsg = 'Could not save notes.';
                if (xhr.responseJSON && xhr.responseJSON.message) {
                    errorMsg = xhr.responseJSON.message;
                }
                Swal.fire({
                    icon: 'error',
                    title: 'Error',
                    text: errorMsg
                });
            },
            complete: function() {
                saveBtn.prop('disabled', false).html('<i class="fas fa-save me-1"></i> Save Notes');
            }
        });
    });
    
    // Delete button
    $('#deleteContactBtn').click(function() {
        Swal.fire({
            icon: 'warning',
            title: 'Delete this inquiry?',
            text: 'Inquiry #{{ $contact->id }} from {{ $contact->full_name }}. This cannot be undone.',
            showCancelButton: true,
            confirmButtonColor: '#ef4444',
            confirmButtonText: 'Yes, Delete',
        }).then((result) => {
            if (result.isConfirmed) {
                $('#deleteModal').modal('show');
            }
        });
    });
    
});
</script>
@endsection