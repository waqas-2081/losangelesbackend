{{-- resources/views/admin/logo-creator/show.blade.php --}}
@extends('layouts.app')

@section('title', 'Logo Creator Brief #' . $brief->id)

@section('content')

<link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/sweetalert2@11/dist/sweetalert2.min.css">

<style>
    .detail-card { background: white; border-radius: 16px; padding: 24px; box-shadow: 0 1px 3px rgba(0,0,0,0.08); margin-bottom: 24px; border: 1px solid #f0f0f0; }
    .detail-title { font-size: 14px; font-weight: 700; margin-bottom: 20px; padding-bottom: 12px; border-bottom: 1px solid #eee; display: flex; align-items: center; gap: 8px; letter-spacing: 0.5px; text-transform: uppercase; color: #333; }
    .info-row { display: flex; margin-bottom: 12px; font-size: 14px; }
    .info-label { width: 180px; color: #888; flex-shrink: 0; }
    .info-value { flex: 1; font-weight: 500; color: #1a1a1a; word-break: break-word; }
    .badge-pending     { background: #fef3c7; color: #92400e; }
    .badge-in_progress { background: #dbeafe; color: #1e40af; }
    .badge-completed   { background: #dcfce7; color: #166534; }
    .badge-rejected    { background: #fee2e2; color: #991b1b; }
    .step-dot { width: 32px; height: 32px; border-radius: 50%; display: inline-flex; align-items: center; justify-content: center; font-weight: 700; font-size: 13px; }
    .step-done { background: #dcfce7; color: #166534; }
    .step-current { background: #dbeafe; color: #1e40af; }
    .step-pending { background: #f3f4f6; color: #9ca3af; }
</style>

<div class="container-fluid">

    {{-- Header --}}
    <div class="d-flex justify-content-between align-items-center mb-4">
        <div>
            <h4 class="mb-1"><i class="fas fa-paint-brush me-2"></i> Logo Creator Brief #{{ $brief->id }}</h4>
            <nav aria-label="breadcrumb">
                <ol class="breadcrumb mb-0 small">
                    <li class="breadcrumb-item"><a href="{{ route('admin.dashboard') }}">Dashboard</a></li>
                    <li class="breadcrumb-item"><a href="{{ route('admin.logo-creator.index') }}">Logo Creator</a></li>
                    <li class="breadcrumb-item active">Brief #{{ $brief->id }}</li>
                </ol>
            </nav>
        </div>
        <div class="d-flex gap-2">
            <button class="btn btn-outline-primary" onclick="window.print()">
                <i class="fas fa-print"></i> Print
            </button>
            <a href="{{ route('admin.logo-creator.index') }}" class="btn btn-outline-secondary">
                <i class="fas fa-arrow-left"></i> Back
            </a>
        </div>
    </div>

    <div class="row">

        {{-- LEFT COLUMN --}}
        <div class="col-lg-8">

            {{-- Step Progress --}}
            <div class="detail-card">
                <h5 class="detail-title"><i class="fas fa-tasks"></i> Form Progress</h5>
                <div class="d-flex align-items-center gap-3 flex-wrap">
                    @php
                        $steps = [
                            0 => 'Business Name',
                            1 => 'Slogan',
                            2 => 'Industry',
                            3 => 'Contact Info',
                        ];
                    @endphp
                    @foreach($steps as $num => $label)
                        @php
                            $cls = $brief->current_step > $num ? 'step-done'
                                 : ($brief->current_step == $num ? 'step-current' : 'step-pending');
                            $icon = $brief->current_step > $num ? '✓' : ($num + 1);
                        @endphp
                        <div class="d-flex align-items-center gap-2">
                            <span class="step-dot {{ $cls }}">{{ $icon }}</span>
                            <span style="font-size:13px; color:#555;">{{ $label }}</span>
                        </div>
                        @if($num < 3)
                            <div style="height:2px;width:30px;background:#e5e7eb;"></div>
                        @endif
                    @endforeach

                    <div class="ms-auto">
                        @if($brief->is_complete)
                            <span class="badge" style="background:#dcfce7;color:#166534;font-size:13px;padding:6px 14px;border-radius:20px;">
                                <i class="fas fa-check-circle me-1"></i> Fully Complete
                            </span>
                        @else
                            <span class="badge" style="background:#fef3c7;color:#92400e;font-size:13px;padding:6px 14px;border-radius:20px;">
                                <i class="fas fa-clock me-1"></i> Incomplete (Step {{ $brief->current_step }}/3)
                            </span>
                        @endif
                    </div>
                </div>
            </div>

            {{-- Brief Details --}}
            <div class="detail-card">
                <h5 class="detail-title"><i class="fas fa-info-circle"></i> Brief Details</h5>

                <div class="info-row">
                    <div class="info-label">Business Name</div>
                    <div class="info-value"><strong>{{ $brief->business_name }}</strong></div>
                </div>

                <div class="info-row">
                    <div class="info-label">Slogan</div>
                    <div class="info-value">
                        @if($brief->slogan)
                            <em>"{{ $brief->slogan }}"</em>
                        @else
                            <span class="text-muted">—</span>
                        @endif
                    </div>
                </div>

                <div class="info-row">
                    <div class="info-label">Industry</div>
                    <div class="info-value">{{ $brief->industry ?? '—' }}</div>
                </div>
            </div>

            {{-- Contact Info --}}
            <div class="detail-card">
                <h5 class="detail-title"><i class="fas fa-user"></i> Contact Info</h5>

                <div class="info-row">
                    <div class="info-label">Email</div>
                    <div class="info-value">
                        @if($brief->email)
                            <a href="mailto:{{ $brief->email }}">{{ $brief->email }}</a>
                        @else
                            <span class="text-muted">Not provided yet</span>
                        @endif
                    </div>
                </div>

                <div class="info-row">
                    <div class="info-label">Phone</div>
                    <div class="info-value">{{ $brief->phone ?? '—' }}</div>
                </div>
            </div>

        </div>

        {{-- RIGHT COLUMN --}}
        <div class="col-lg-4">

            {{-- Status Card --}}
            <div class="detail-card">
                <h5 class="detail-title"><i class="fas fa-circle-info"></i> Brief Status</h5>
                <div class="info-row"><div class="info-label">Submitted</div><div class="info-value">{{ $brief->created_at->format('M d, Y H:i') }}</div></div>
                <div class="info-row"><div class="info-label">Last Updated</div><div class="info-value">{{ $brief->updated_at->format('M d, Y H:i') }}</div></div>
                <div class="mb-3">
                    <label class="form-label fw-bold">Current Status</label>
                    <select id="statusSelect" class="form-select" data-brief-id="{{ $brief->id }}">
                        @foreach(['pending'=>'Pending','in_progress'=>'In Progress','completed'=>'Completed','rejected'=>'Rejected'] as $val=>$lbl)
                            <option value="{{ $val }}" {{ $brief->status == $val ? 'selected' : '' }}>{{ $lbl }}</option>
                        @endforeach
                    </select>
                </div>
            </div>

            {{-- Admin Notes --}}
            <div class="detail-card">
                <h5 class="detail-title"><i class="fas fa-pen-to-square"></i> Admin Notes</h5>
                <textarea id="adminNotes" class="form-control mb-3" rows="5"
                          placeholder="Internal notes about this brief...">{{ $brief->admin_notes }}</textarea>
                <button class="btn btn-primary w-100" id="saveNotesBtn">
                    <i class="fas fa-floppy-disk"></i> Save Notes
                </button>
            </div>

            {{-- Quick Actions --}}
            <div class="detail-card">
                <h5 class="detail-title"><i class="fas fa-bolt"></i> Quick Actions</h5>
                <div class="d-grid gap-2">
                    @if($brief->email)
                        <a href="mailto:{{ $brief->email }}" class="btn btn-outline-primary">
                            <i class="fas fa-envelope"></i> Email Client
                        </a>
                    @endif
                    <button onclick="window.print()" class="btn btn-outline-secondary">
                        <i class="fas fa-print"></i> Print Brief
                    </button>
                    <form action="{{ route('admin.logo-creator.destroy', $brief) }}" method="POST" id="deleteForm">
                        @csrf @method('DELETE')
                        <button type="button" class="btn btn-outline-danger w-100" id="deleteBtn">
                            <i class="fas fa-trash"></i> Delete Brief
                        </button>
                    </form>
                </div>
            </div>

        </div>
    </div>
</div>

@endsection

@section('scripts')
<script src="https://code.jquery.com/jquery-3.7.1.min.js"></script>
<script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
<script>
$(document).ready(function () {

    // Status update
    $('#statusSelect').change(function () {
        const select  = $(this);
        const briefId = select.data('brief-id');
        select.prop('disabled', true);
        $.ajax({
            url : '/admin/logo-creator/' + briefId + '/status',
            type: 'POST',
            data: { status: select.val(), _token: '{{ csrf_token() }}' },
            success(res) {
                if (res.success) {
                    Swal.fire({ icon:'success', title:'Updated', text: res.status_text,
                        toast:true, position:'top-end', showConfirmButton:false, timer:2500, timerProgressBar:true });
                }
            },
            error() { Swal.fire({ icon:'error', title:'Error', text:'Failed to update status.' }); },
            complete() { select.prop('disabled', false); }
        });
    });

    // Save notes
    $('#saveNotesBtn').click(function () {
        const btn = $(this);
        btn.prop('disabled', true).html('<i class="fas fa-spinner fa-spin"></i> Saving...');
        $.ajax({
            url : '/admin/logo-creator/{{ $brief->id }}/notes',
            type: 'POST',
            data: { admin_notes: $('#adminNotes').val(), _token: '{{ csrf_token() }}' },
            success(res) {
                if (res.success) {
                    Swal.fire({ icon:'success', title:'Saved', text:'Notes saved.',
                        toast:true, position:'top-end', showConfirmButton:false, timer:2000 });
                }
            },
            error() { Swal.fire({ icon:'error', title:'Error', text:'Could not save notes.' }); },
            complete() { btn.prop('disabled', false).html('<i class="fas fa-floppy-disk"></i> Save Notes'); }
        });
    });

    // Delete
    $('#deleteBtn').click(function () {
        Swal.fire({
            icon:'warning', title:'Delete this brief?',
            text:'This cannot be undone.',
            showCancelButton:true, confirmButtonColor:'#ef4444', confirmButtonText:'Yes, Delete',
        }).then(result => { if (result.isConfirmed) $('#deleteForm').submit(); });
    });
});
</script>
@endsection