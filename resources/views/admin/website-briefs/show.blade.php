{{-- backend/resources/views/admin/website-briefs/show.blade.php --}}

@extends('layouts.app')

@section('title', 'Website Brief #' . $brief->id)

@section('content')

<link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
<link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/sweetalert2@11/dist/sweetalert2.min.css">

<style>
    .detail-card { background: white; border-radius: 16px; padding: 24px; box-shadow: 0 1px 3px rgba(0,0,0,0.08); margin-bottom: 24px; border: 1px solid #f0f0f0; }
    .detail-title { font-size: 14px; font-weight: 700; margin-bottom: 20px; padding-bottom: 12px; border-bottom: 1px solid #eee; display: flex; align-items: center; gap: 8px; letter-spacing: 0.5px; text-transform: uppercase; color: #333; }
    .info-row { display: flex; margin-bottom: 12px; font-size: 14px; }
    .info-label { width: 200px; color: #888; flex-shrink: 0; }
    .info-value { flex: 1; font-weight: 500; color: #1a1a1a; word-break: break-word; }
    .tag-pill { display: inline-block; padding: 3px 12px; border-radius: 20px; font-size: 12px; font-weight: 600; background: #e0f2fe; color: #0369a1; margin: 2px; }
    .addon-pill { display: inline-block; padding: 3px 12px; border-radius: 20px; font-size: 12px; font-weight: 600; background: #f0fdf4; color: #166534; margin: 2px; }
    .file-card { display: flex; align-items: center; gap: 12px; padding: 12px 16px; border: 1px solid #f0f0f0; border-radius: 10px; margin-bottom: 10px; }
    .file-icon { width: 40px; height: 40px; border-radius: 8px; background: #f3f4f6; display: flex; align-items: center; justify-content: center; font-size: 18px; flex-shrink: 0; }
    .badge-pending     { background: #fef3c7; color: #92400e; }
    .badge-in_progress { background: #dbeafe; color: #1e40af; }
    .badge-completed   { background: #dcfce7; color: #166534; }
    .badge-rejected    { background: #fee2e2; color: #991b1b; }
    .yes-badge { background:#dcfce7; color:#166534; padding:2px 10px; border-radius:20px; font-size:12px; font-weight:600; }
    .no-badge  { background:#fee2e2; color:#991b1b; padding:2px 10px; border-radius:20px; font-size:12px; font-weight:600; }
</style>

@php
    $websiteTypeLabels = [
        'informative_without_payment'       => 'Informative (without payment integration)',
        'informative_with_payment_services' => 'Informative (with payment – sell services)',
        'informative_with_payment_products' => 'Informative (with payment – sell products)',
        'ecommerce'                         => 'E-Commerce (online store)',
        'custom_web_app'                    => 'Custom Web App',
    ];

    $feelLabels = [
        'corporate' => 'Corporate', 'fun'      => 'Fun',    'trendy'   => 'Trendy',
        'friendly'  => 'Friendly',  'hi-tech'  => 'Hi-tech','minimal'  => 'Minimal',
        'dark'      => 'Dark',      'light'    => 'Light',
    ];

    $addonLabels = [
        'chat_integration'   => 'Chat Integration',    'custom_dashboard' => 'Custom Dashboard',
        'database'           => 'Database',            'hover_effects'    => 'Hover Effects',
        'security_encryption'=> 'Security Encryption', 'sign_up_sign_in'  => 'Sign Up / Sign In',
        'newsletter'         => 'Newsletter',          'website_content'  => 'Website Content',
        'ssl_certification'  => 'SSL Certification',   'custom_forms'     => 'Custom Forms',
        'social_media_feed'  => 'Social Media Feed',   'seo_optimization' => 'SEO Optimization',
        'ada_compliance'     => 'ADA Compliance',      'blogs'            => 'Blogs',
        '3rd_party_api'      => '3rd Party API',       'digital_marketing'=> 'Digital Marketing',
        'videos_animations'  => 'Videos & Animations',
    ];

    $yn = fn($val) => is_null($val)
        ? '<span class="text-muted">—</span>'
        : ($val ? '<span class="yes-badge">Yes</span>' : '<span class="no-badge">No</span>');
@endphp

<div class="container-fluid">

    {{-- Header --}}
    <div class="d-flex justify-content-between align-items-center mb-4">
        <div>
            <h4 class="mb-1"><i class="fa-solid fa-globe me-2"></i> Website Brief #{{ $brief->id }}</h4>
            <nav aria-label="breadcrumb">
                <ol class="breadcrumb mb-0 small">
                    <li class="breadcrumb-item"><a href="{{ route('admin.dashboard') }}">Dashboard</a></li>
                    <li class="breadcrumb-item"><a href="{{ route('admin.website-briefs.index') }}">Website Briefs</a></li>
                    <li class="breadcrumb-item active">Brief #{{ $brief->id }}</li>
                </ol>
            </nav>
        </div>
        <div class="d-flex gap-2">
            <button class="btn btn-outline-primary" onclick="window.print()">
                <i class="fa-solid fa-print"></i> Print
            </button>
            <a href="{{ route('admin.website-briefs.index') }}" class="btn btn-outline-secondary">
                <i class="fa-solid fa-arrow-left"></i> Back
            </a>
        </div>
    </div>

    <div class="row">

        {{-- ========== LEFT COLUMN ========== --}}
        <div class="col-lg-8">

            {{-- 1. Contact & Business --}}
            <div class="detail-card">
                <h5 class="detail-title"><i class="fa-solid fa-user"></i> Contact & Business</h5>
                <div class="info-row"><div class="info-label">Name</div><div class="info-value">{{ $brief->name }}</div></div>
                <div class="info-row"><div class="info-label">Email</div><div class="info-value"><a href="mailto:{{ $brief->email }}">{{ $brief->email }}</a></div></div>
                <div class="info-row"><div class="info-label">Business Name</div><div class="info-value"><strong>{{ $brief->business_name }}</strong></div></div>
            </div>

            {{-- 2. Website Type --}}
            <div class="detail-card">
                <h5 class="detail-title"><i class="fa-solid fa-laptop-code"></i> Website Type</h5>
                <div class="info-row">
                    <div class="info-label">Type</div>
                    <div class="info-value">
                        <span class="badge" style="background:#e0f2fe; color:#0369a1; font-size:13px; padding:6px 14px; border-radius:20px;">
                            {{ $websiteTypeLabels[$brief->website_type] ?? $brief->website_type }}
                        </span>
                    </div>
                </div>

                {{-- Conditional: informative without payment --}}
                @if($brief->website_type === 'informative_without_payment')
                    <div class="info-row"><div class="info-label">Products to Showcase</div><div class="info-value">{{ $brief->products_count ?? '—' }}</div></div>
                    <div class="info-row"><div class="info-label">Services to Showcase</div><div class="info-value">{{ $brief->services_count_no_payment ?? '—' }}</div></div>
                    <div class="info-row"><div class="info-label">Future Images/Products</div><div class="info-value" style="white-space:pre-wrap;">{{ $brief->future_images_products ?? '—' }}</div></div>
                @endif

                {{-- Conditional: informative with payment (services) --}}
                @if($brief->website_type === 'informative_with_payment_services')
                    <div class="info-row"><div class="info-label">Services & Prices</div><div class="info-value" style="white-space:pre-wrap;">{{ $brief->services_count_with_price ?? '—' }}</div></div>
                    <div class="info-row"><div class="info-label">Accept Online Payments</div><div class="info-value">{!! $yn($brief->accept_online_payments) !!}</div></div>
                    <div class="info-row"><div class="info-label">Payment Medium</div><div class="info-value">{{ $brief->payment_medium ?? '—' }}</div></div>
                    <div class="info-row"><div class="info-label">Future Images/Services</div><div class="info-value" style="white-space:pre-wrap;">{{ $brief->future_images_services ?? '—' }}</div></div>
                @endif
            </div>

            {{-- 3. Brand & Audience --}}
            <div class="detail-card">
                <h5 class="detail-title"><i class="fa-solid fa-bullseye"></i> Brand & Audience</h5>
                <div class="info-row">
                    <div class="info-label">Business Description</div>
                    <div class="info-value" style="white-space:pre-wrap;">{{ $brief->business_description }}</div>
                </div>
                <div class="info-row"><div class="info-label">Industry</div><div class="info-value">{{ $brief->business_industry ?? '—' }}</div></div>
                <div class="info-row"><div class="info-label">Target Audience</div><div class="info-value">{{ $brief->target_audience ?? '—' }}</div></div>
                <div class="info-row">
                    <div class="info-label">Overall Feel</div>
                    <div class="info-value">
                        @if($brief->overall_feel && count($brief->overall_feel))
                            @foreach($brief->overall_feel as $feel)
                                <span class="tag-pill">{{ $feelLabels[$feel] ?? ucfirst($feel) }}</span>
                            @endforeach
                        @else
                            <span class="text-muted">None selected</span>
                        @endif
                    </div>
                </div>
                <div class="info-row">
                    <div class="info-label">Competitor References</div>
                    <div class="info-value" style="white-space:pre-wrap;">{{ $brief->competitors_references ?? '—' }}</div>
                </div>
            </div>

            {{-- 4. Site Structure & Assets --}}
            <div class="detail-card">
                <h5 class="detail-title"><i class="fa-solid fa-sitemap"></i> Site Structure & Assets</h5>
                <div class="info-row"><div class="info-label">Has Domain?</div><div class="info-value">{!! $yn($brief->has_domain) !!}</div></div>
                <div class="info-row"><div class="info-label">Number of Pages</div><div class="info-value">{{ $brief->pages_count }}</div></div>
                <div class="info-row">
                    <div class="info-label">Pages List</div>
                    <div class="info-value" style="white-space:pre-wrap;">{{ $brief->pages_list ?? '—' }}</div>
                </div>
                <div class="info-row"><div class="info-label">Has Logo?</div><div class="info-value">{!! $yn($brief->has_logo) !!}</div></div>
                <div class="info-row"><div class="info-label">Wants Logo Revamp?</div><div class="info-value">{!! $yn($brief->wants_logo_revamp) !!}</div></div>
                <div class="info-row"><div class="info-label">Needs Hosting?</div><div class="info-value">{!! $yn($brief->needs_hosting) !!}</div></div>
                <div class="info-row"><div class="info-label">Needs Responsive?</div><div class="info-value">{!! $yn($brief->needs_responsive) !!}</div></div>
            </div>

            {{-- 5. Add-on Features --}}
            <div class="detail-card">
                <h5 class="detail-title"><i class="fa-solid fa-puzzle-piece"></i> Add-on Features</h5>
                @if($brief->addon_features && count($brief->addon_features))
                    <div class="d-flex flex-wrap gap-1">
                        @foreach($brief->addon_features as $addon)
                            <span class="addon-pill">{{ $addonLabels[$addon] ?? ucfirst(str_replace('_', ' ', $addon)) }}</span>
                        @endforeach
                    </div>
                @else
                    <p class="text-muted mb-0">No add-ons selected.</p>
                @endif
            </div>

            {{-- 6. Reference Files --}}
            <div class="detail-card">
                <h5 class="detail-title"><i class="fa-solid fa-paperclip"></i> Reference Files</h5>
                @if($brief->files->count())
                    @foreach($brief->files as $file)
                    <div class="file-card">
                        <div class="file-icon">
                            @if(str_contains($file->mime_type, 'image')) 🖼️
                            @elseif(str_contains($file->mime_type, 'pdf')) 📄
                            @else 📎
                            @endif
                        </div>
                        <div class="flex-grow-1">
                            <div style="font-weight:600; font-size:14px;">{{ $file->original_name }}</div>
                            <div style="font-size:12px; color:#888;">{{ $file->human_size }} &middot; {{ $file->mime_type }}</div>
                        </div>
                        <a href="{{ $file->url }}" target="_blank" class="btn btn-sm btn-outline-primary">
                            <i class="fa-solid fa-download"></i> Download
                        </a>
                    </div>
                    @endforeach
                @else
                    <div class="text-muted text-center py-3">
                        <i class="fa-solid fa-file-slash" style="font-size:28px;"></i>
                        <p class="mt-2 mb-0">No reference files uploaded</p>
                    </div>
                @endif
            </div>

        </div>

        {{-- ========== RIGHT COLUMN ========== --}}
        <div class="col-lg-4">

            {{-- Status Card --}}
            <div class="detail-card">
                <h5 class="detail-title"><i class="fa-solid fa-circle-info"></i> Brief Status</h5>
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
                <h5 class="detail-title"><i class="fa-solid fa-pen-to-square"></i> Admin Notes</h5>
                <textarea id="adminNotes" class="form-control mb-3" rows="5"
                          placeholder="Internal notes about this brief...">{{ $brief->admin_notes }}</textarea>
                <button class="btn btn-primary w-100" id="saveNotesBtn">
                    <i class="fa-solid fa-floppy-disk"></i> Save Notes
                </button>
            </div>

            {{-- Quick Actions --}}
            <div class="detail-card">
                <h5 class="detail-title"><i class="fa-solid fa-bolt"></i> Quick Actions</h5>
                <div class="d-grid gap-2">
                    <a href="mailto:{{ $brief->email }}" class="btn btn-outline-primary">
                        <i class="fa-solid fa-envelope"></i> Email Client
                    </a>
                    <button onclick="window.print()" class="btn btn-outline-secondary">
                        <i class="fa-solid fa-print"></i> Print Brief
                    </button>
                    <form action="{{ route('admin.website-briefs.destroy', $brief) }}" method="POST" id="deleteBriefForm">
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
        const select  = $(this);
        const briefId = select.data('brief-id');
        select.prop('disabled', true);
        $.ajax({
            url : '/admin/website-briefs/' + briefId + '/status',
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
        btn.prop('disabled', true).html('<i class="fa-solid fa-spinner fa-spin"></i> Saving...');
        $.ajax({
            url : '/admin/website-briefs/{{ $brief->id }}/notes',
            type: 'POST',
            data: { admin_notes: $('#adminNotes').val(), _token: '{{ csrf_token() }}' },
            success(res) {
                if (res.success) {
                    Swal.fire({ icon:'success', title:'Saved', text:'Notes saved.',
                        toast:true, position:'top-end', showConfirmButton:false, timer:2000 });
                }
            },
            error() { Swal.fire({ icon:'error', title:'Error', text:'Could not save notes.' }); },
            complete() { btn.prop('disabled', false).html('<i class="fa-solid fa-floppy-disk"></i> Save Notes'); }
        });
    });

    // Delete
    $('#deleteBriefBtn').click(function () {
        Swal.fire({
            icon:'warning', title:'Delete this brief?',
            text:'All files will also be deleted. This cannot be undone.',
            showCancelButton:true, confirmButtonColor:'#ef4444', confirmButtonText:'Yes, Delete',
        }).then(result => { if (result.isConfirmed) $('#deleteBriefForm').submit(); });
    });
});
</script>
@endsection