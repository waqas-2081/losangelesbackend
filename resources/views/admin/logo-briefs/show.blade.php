{{-- backend/resources/views/admin/logo-briefs/show.blade.php --}}

@extends('layouts.app')

@section('content')

    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/sweetalert2@11/dist/sweetalert2.min.css">

    <style>
        .detail-card {
            background: white;
            border-radius: 16px;
            padding: 24px;
            box-shadow: 0 1px 3px rgba(0, 0, 0, 0.08);
            margin-bottom: 24px;
            border: 1px solid #f0f0f0;
        }

        .detail-title {
            font-size: 15px;
            font-weight: 700;
            margin-bottom: 20px;
            padding-bottom: 12px;
            border-bottom: 1px solid #eee;
            display: flex;
            align-items: center;
            gap: 8px;
            letter-spacing: 0.5px;
            text-transform: uppercase;
            color: #333;
        }

        .info-row {
            display: flex;
            margin-bottom: 12px;
            font-size: 14px;
        }

        .info-label {
            width: 160px;
            color: #888;
            flex-shrink: 0;
        }

        .info-value {
            flex: 1;
            font-weight: 500;
            color: #1a1a1a;
            word-break: break-word;
        }

        .tag-pill {
            display: inline-block;
            padding: 3px 12px;
            border-radius: 20px;
            font-size: 12px;
            font-weight: 600;
            background: #f3e8ff;
            color: #7c3aed;
            margin: 2px;
        }

        .color-dot {
            display: inline-block;
            width: 14px;
            height: 14px;
            border-radius: 50%;
            margin-right: 4px;
            vertical-align: middle;
            border: 1px solid rgba(0, 0, 0, 0.1);
        }

        .file-card {
            display: flex;
            align-items: center;
            gap: 12px;
            padding: 12px 16px;
            border: 1px solid #f0f0f0;
            border-radius: 10px;
            margin-bottom: 10px;
        }

        .file-icon {
            width: 40px;
            height: 40px;
            border-radius: 8px;
            background: #f3f4f6;
            display: flex;
            align-items: center;
            justify-content: center;
            font-size: 18px;
            flex-shrink: 0;
        }

        .file-name {
            font-weight: 600;
            font-size: 14px;
        }

        .file-meta {
            font-size: 12px;
            color: #888;
        }

        .status-badge {
            padding: 6px 16px;
            border-radius: 20px;
            font-size: 12px;
            font-weight: 700;
            text-transform: uppercase;
            letter-spacing: 0.5px;
        }

        .badge-pending {
            background: #fef3c7;
            color: #92400e;
        }

        .badge-in_progress {
            background: #dbeafe;
            color: #1e40af;
        }

        .badge-completed {
            background: #dcfce7;
            color: #166534;
        }

        .badge-rejected {
            background: #fee2e2;
            color: #991b1b;
        }
    </style>

    @php
        $logoTypeLabels = [
            'symbol_icon' => 'Symbol or Icon',
            'lettermark' => 'Letter Mark',
            'character_based' => 'Character Based',
            'wordmark' => 'Word Mark',
            'combination_mark' => 'Combination Mark',
            'emblem' => 'Emblem',
        ];
        $colorLabels = [
            'blue' => ['label' => 'Blue', 'hex' => '#0072ff'],
            'yellow' => ['label' => 'Yellow', 'hex' => '#ffd200'],
            'red' => ['label' => 'Red', 'hex' => '#f7374f'],
            'purple' => ['label' => 'Purple', 'hex' => '#7c3aed'],
            'green' => ['label' => 'Green', 'hex' => '#22c55e'],
            'maroon' => ['label' => 'Maroon', 'hex' => '#7f1734'],
            'neutrals' => ['label' => 'Neutrals', 'hex' => '#9ca3af'],
            'aqua' => ['label' => 'Aqua', 'hex' => '#06b6d4'],
            'pink' => ['label' => 'Pink', 'hex' => '#ec4899'],
            'designers_choice' => ['label' => "Designer's Choice", 'hex' => '#6366f1'],
        ];
    @endphp

    <div class="container-fluid">

        {{-- Header --}}
        <div class="d-flex justify-content-between align-items-center mb-4">
            <div>
                <h4 class="mb-1"><i class="fa-solid fa-palette me-2"></i> Logo Brief #{{ $logoBrief->id }}</h4>
                <nav aria-label="breadcrumb">
                    <ol class="breadcrumb mb-0 small">
                        <li class="breadcrumb-item"><a href="{{ route('admin.dashboard') }}">Dashboard</a></li>
                        <li class="breadcrumb-item"><a href="{{ route('admin.logo-briefs.index') }}">Logo Briefs</a></li>
                        <li class="breadcrumb-item active">Brief #{{ $logoBrief->id }}</li>
                    </ol>
                </nav>
            </div>
            <div class="d-flex gap-2">
                <button class="btn btn-outline-primary" onclick="window.print()">
                    <i class="fa-solid fa-print"></i> Print
                </button>
                <a href="{{ route('admin.logo-briefs.index') }}" class="btn btn-outline-secondary">
                    <i class="fa-solid fa-arrow-left"></i> Back
                </a>
            </div>
        </div>

        <div class="row">

            {{-- LEFT COLUMN --}}
            <div class="col-lg-8">

                {{-- Contact Information --}}
                <div class="detail-card">
                    <h5 class="detail-title"><i class="fa-solid fa-user"></i> Contact Information</h5>
                    <div class="info-row">
                        <div class="info-label">Contact Name</div>
                        <div class="info-value">{{ $logoBrief->name }}</div>
                    </div>
                    <div class="info-row">
                        <div class="info-label">Email</div>
                        <div class="info-value"><a href="mailto:{{ $logoBrief->email }}">{{ $logoBrief->email }}</a></div>
                    </div>
                    <div class="info-row">
                        <div class="info-label">Personal Phone</div>
                        <div class="info-value">{{ $logoBrief->personal_phone }}</div>
                    </div>
                    <div class="info-row">
                        <div class="info-label">Company Phone</div>
                        <div class="info-value">{{ $logoBrief->company_phone ?? '—' }}</div>
                    </div>
                </div>

                {{-- Logo & Company --}}
                <div class="detail-card">
                    <h5 class="detail-title"><i class="fa-solid fa-building"></i> Logo & Company</h5>
                    <div class="info-row">
                        <div class="info-label">Logo Name</div>
                        <div class="info-value"><strong>{{ $logoBrief->logo_name }}</strong></div>
                    </div>
                    <div class="info-row">
                        <div class="info-label">Company Slogan</div>
                        <div class="info-value">{{ $logoBrief->company_slogan ?? '—' }}</div>
                    </div>
                    <div class="info-row">
                        <div class="info-label">Industry</div>
                        <div class="info-value">{{ $logoBrief->industry ?? '—' }}</div>
                    </div>
                    <div class="info-row">
                        <div class="info-label">Business Description</div>
                        <div class="info-value" style="white-space: pre-wrap;">{{ $logoBrief->business_desc }}</div>
                    </div>
                    <div class="info-row">
                        <div class="info-label">Logo Requirements</div>
                        <div class="info-value" style="white-space: pre-wrap;">{{ $logoBrief->logo_description }}</div>
                    </div>
                </div>

                {{-- Competitor References --}}
                <div class="detail-card">
                    <h5 class="detail-title"><i class="fa-solid fa-trophy"></i> Competitor References</h5>
                    <div class="info-row">
                        <div class="info-label">Reference 1</div>
                        <div class="info-value">{{ $logoBrief->competitors_ref }}</div>
                    </div>
                    <div class="info-row">
                        <div class="info-label">Reference 2</div>
                        <div class="info-value">{{ $logoBrief->competitors_ref_two ?? '—' }}</div>
                    </div>
                    <div class="info-row">
                        <div class="info-label">Reference 3</div>
                        <div class="info-value">{{ $logoBrief->competitors_ref_three ?? '—' }}</div>
                    </div>
                </div>

                {{-- Design Preferences --}}
                <div class="detail-card">
                    <h5 class="detail-title"><i class="fa-solid fa-paintbrush"></i> Design Preferences</h5>

                    <div class="info-row">
                        <div class="info-label">Logo Type</div>
                        <div class="info-value">
                            <span class="badge"
                                style="background: #f3e8ff; color: #7c3aed; font-size: 13px; padding: 6px 14px; border-radius: 20px;">
                                {{ $logoTypeLabels[$logoBrief->logo_type] ?? ucfirst(str_replace('_', ' ', $logoBrief->logo_type)) }}
                            </span>
                        </div>
                    </div>

                    <div class="info-row">
                        <div class="info-label">Selected Fonts</div>
                        <div class="info-value">
                            @php
                                $fonts = $logoBrief->logo_fonts ?? [];
                                if (is_string($fonts)) {
                                    $fonts = array_filter(array_map('trim', explode(',', $fonts)));
                                }
                            @endphp
                            @if(!empty($fonts))
                                @foreach($fonts as $font)
                                    <span class="tag-pill">{{ ucfirst(str_replace('_', ' ', $font)) }}</span>
                                @endforeach
                            @else
                                <span class="text-muted">None selected</span>
                            @endif
                        </div>
                    </div>

                    <div class="info-row">
                        <div class="info-label">Selected Colors</div>
                        <div class="info-value">
                            <span style="display: inline-flex; align-items: center; gap: 4px; margin: 2px 4px 2px 0;">
                                <span class="tag-pill">{{ $logoBrief->logo_color }}</span>
                            </span>
                        </div>
                    </div>

                    @if($logoBrief->primary_color || $logoBrief->secondary_color)
                        <div class="info-row">
                            <div class="info-label">Custom Colors</div>
                            <div class="info-value">
                                @if($logoBrief->primary_color)
                                    <span class="badge bg-light text-dark border me-2">Primary:
                                        {{ $logoBrief->primary_color }}</span>
                                @endif
                                @if($logoBrief->secondary_color)
                                    <span class="badge bg-light text-dark border">Secondary:
                                        {{ $logoBrief->secondary_color }}</span>
                                @endif
                            </div>
                        </div>
                    @endif
                </div>

                {{-- Reference Files --}}
                <div class="detail-card">
                    <h5 class="detail-title"><i class="fa-solid fa-paperclip"></i> Reference Files</h5>
                    @if($logoBrief->files->count() > 0)
                        @foreach($logoBrief->files as $file)
                            <div class="file-card">
                                <div class="file-icon">
                                    @if(str_contains($file->mime_type, 'image')) 🖼️
                                    @elseif(str_contains($file->mime_type, 'pdf')) 📄
                                    @else 📎
                                    @endif
                                </div>
                                <div class="flex-1">
                                    <div class="file-name">{{ $file->original_name }}</div>
                                    <div class="file-meta">{{ $file->human_size }} &middot; {{ $file->mime_type }}</div>
                                </div>
                                <a href="{{ Storage::url($file->file_path) }}" target="_blank"
                                    class="btn btn-sm btn-outline-primary">
                                    <i class="fa-solid fa-download"></i> Download
                                </a>
                            </div>
                        @endforeach
                    @else
                        <div class="text-muted text-center py-3">
                            <i class="fa-solid fa-file-slash" style="font-size: 28px;"></i>
                            <p class="mt-2 mb-0">No reference files uploaded</p>
                        </div>
                    @endif
                </div>

            </div>

            {{-- RIGHT COLUMN --}}
            <div class="col-lg-4">

                {{-- Status Card --}}
                <div class="detail-card">
                    <h5 class="detail-title"><i class="fa-solid fa-circle-info"></i> Brief Status</h5>

                    <div class="info-row">
                        <div class="info-label">Submitted</div>
                        <div class="info-value">{{ $logoBrief->created_at->format('M d, Y H:i') }}</div>
                    </div>
                    <div class="info-row">
                        <div class="info-label">Last Updated</div>
                        <div class="info-value">{{ $logoBrief->updated_at->format('M d, Y H:i') }}</div>
                    </div>

                    <div class="mb-3">
                        <label class="form-label fw-bold">Current Status</label>
                        <select id="statusSelect" class="form-select" data-brief-id="{{ $logoBrief->id }}">
                            @foreach(['pending' => 'Pending', 'in_progress' => 'In Progress', 'completed' => 'Completed', 'rejected' => 'Rejected'] as $val => $label)
                                <option value="{{ $val }}" {{ $logoBrief->status == $val ? 'selected' : '' }}>{{ $label }}
                                </option>
                            @endforeach
                        </select>
                    </div>
                </div>

                {{-- Admin Notes --}}
                <div class="detail-card">
                    <h5 class="detail-title"><i class="fa-solid fa-pen-to-square"></i> Admin Notes</h5>
                    <textarea id="adminNotes" class="form-control mb-3" rows="5"
                        placeholder="Internal notes about this brief...">{{ $logoBrief->admin_notes }}</textarea>
                    <button class="btn btn-primary w-100" id="saveNotesBtn">
                        <i class="fa-solid fa-floppy-disk"></i> Save Notes
                    </button>
                </div>

                {{-- Quick Actions --}}
                <div class="detail-card">
                    <h5 class="detail-title"><i class="fa-solid fa-bolt"></i> Quick Actions</h5>
                    <div class="d-grid gap-2">
                        <a href="mailto:{{ $logoBrief->email }}" class="btn btn-outline-primary">
                            <i class="fa-solid fa-envelope"></i> Email Client
                        </a>
                        <button onclick="window.print()" class="btn btn-outline-secondary">
                            <i class="fa-solid fa-print"></i> Print Brief
                        </button>
                        <form action="{{ route('admin.logo-briefs.destroy', $logoBrief) }}" method="POST"
                            id="deleteBriefForm">
                            @csrf @method('DELETE')
                            <button type="button" class="btn btn-outline-danger w-100" id="deleteBriefBtn">
                                <i class="fa-solid fa-trash"></i> Delete Brief
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
                const select = $(this);
                const briefId = select.data('brief-id');
                const newStatus = select.val();

                select.prop('disabled', true);

                $.ajax({
                    url: '/admin/logo-briefs/' + briefId + '/status',
                    type: 'POST',
                    data: {
                        status: newStatus,
                        _token: '{{ csrf_token() }}'
                    },
                    success: function (res) {
                        if (res.success) {
                            Swal.fire({
                                icon: 'success',
                                title: 'Updated',
                                text: res.status_text || 'Status updated successfully',
                                toast: true,
                                position: 'top-end',
                                showConfirmButton: false,
                                timer: 2500,
                                timerProgressBar: true
                            });
                        } else {
                            Swal.fire({
                                icon: 'error',
                                title: 'Error',
                                text: res.message || 'Failed to update status'
                            });
                        }
                    },
                    error: function () {
                        Swal.fire({
                            icon: 'error',
                            title: 'Error',
                            text: 'Failed to update status.'
                        });
                    },
                    complete: function () {
                        select.prop('disabled', false);
                    }
                });
            });

            // Save admin notes
            $('#saveNotesBtn').click(function () {
                const btn = $(this);
                const notes = $('#adminNotes').val();

                btn.prop('disabled', true).html('<i class="fa-solid fa-spinner fa-spin"></i> Saving...');

                $.ajax({
                    url: '/admin/logo-briefs/{{ $logoBrief->id }}/notes',
                    type: 'POST',
                    data: {
                        admin_notes: notes,
                        _token: '{{ csrf_token() }}'
                    },
                    success: function (res) {
                        if (res.success) {
                            Swal.fire({
                                icon: 'success',
                                title: 'Saved',
                                text: 'Notes saved successfully.',
                                toast: true,
                                position: 'top-end',
                                showConfirmButton: false,
                                timer: 2000
                            });
                        } else {
                            Swal.fire({
                                icon: 'error',
                                title: 'Error',
                                text: res.message || 'Could not save notes.'
                            });
                        }
                    },
                    error: function () {
                        Swal.fire({
                            icon: 'error',
                            title: 'Error',
                            text: 'Could not save notes.'
                        });
                    },
                    complete: function () {
                        btn.prop('disabled', false).html('<i class="fa-solid fa-floppy-disk"></i> Save Notes');
                    }
                });
            });

            // Delete
            $('#deleteBriefBtn').click(function () {
                Swal.fire({
                    icon: 'warning',
                    title: 'Delete this brief?',
                    text: 'All files will also be deleted. This cannot be undone.',
                    showCancelButton: true,
                    confirmButtonColor: '#ef4444',
                    confirmButtonText: 'Yes, Delete',
                    cancelButtonText: 'Cancel'
                }).then(function (result) {
                    if (result.isConfirmed) {
                        $('#deleteBriefForm').submit();
                    }
                });
            });
        });
    </script>
@endsection