{{-- resources/views/admin/site-settings/edit.blade.php --}}
@extends('layouts.app')

@section('title', 'Site Settings')

@section('content')

    <style>
        .card-box {
            background: #fff;
            border-radius: 10px;
            border: 1px solid #e5e7eb;
            padding: 28px;
        }

        .card-box h5 {
            font-size: 1rem;
            font-weight: 600;
            margin-bottom: 20px;
        }

        .popup-preview {
            width: 120px;
            height: 120px;
            object-fit: cover;
            border-radius: 8px;
            border: 1px solid #e5e7eb;
            margin-bottom: 10px;
        }
    </style>

    <div class="container" style="margin: 0 auto;">

        <div class="d-flex justify-content-between align-items-center mb-4">
            <h4 class="mb-0"><i class="fas fa-gear me-2 text-primary"></i>Site Settings</h4>
        </div>

        @if(session('success'))
            <div class="alert alert-success alert-dismissible fade show" role="alert">
                <i class="fas fa-check-circle me-2"></i> {{ session('success') }}
                <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
            </div>
        @endif

        <form action="{{ route('admin.site-settings.update') }}" method="POST" enctype="multipart/form-data">
            @csrf

            <div class="card-box mb-3">
                <h5>Promo Popup</h5>
                @if($setting->popup_image_url)
                    <img src="{{ $setting->popup_image_url }}" alt="Popup" class="popup-preview d-block">
                @endif
                <label class="form-label">Popup Image</label>
                <input type="file" name="popup_image" accept="image/*"
                    class="form-control @error('popup_image') is-invalid @enderror">
                @error('popup_image')
                    <div class="invalid-feedback">{{ $message }}</div>
                @enderror
            </div>

            <div class="card-box mb-3">
                <h5>Contact Info</h5>
                <div class="row g-3">
                    <div class="col-md-6">
                        <label class="form-label">Email</label>
                        <input type="email" name="email" class="form-control @error('email') is-invalid @enderror"
                            value="{{ old('email', $setting->email) }}">
                        @error('email')
                        <div class="invalid-feedback">{{ $message }}</div>@enderror
                    </div>
                    <div class="col-md-6">
                        <label class="form-label">Phone</label>
                        <input type="text" name="phone" class="form-control @error('phone') is-invalid @enderror"
                            value="{{ old('phone', $setting->phone) }}">
                        @error('phone')
                        <div class="invalid-feedback">{{ $message }}</div>@enderror
                    </div>
                    <div class="col-12">
                        <label class="form-label">Location</label>
                        <input type="text" name="location" class="form-control @error('location') is-invalid @enderror"
                            value="{{ old('location', $setting->location) }}">
                        @error('location')
                        <div class="invalid-feedback">{{ $message }}</div>@enderror
                    </div>
                </div>
            </div>

            <div class="card-box mb-3">
                <h5>Social Media</h5>
                <div class="row g-3">
                    @php
                        $socials = [
                            'facebook_url' => 'Facebook',
                            'instagram_url' => 'Instagram',
                            'x_url' => 'X (Twitter)',
                            'linkedin_url' => 'LinkedIn',
                            'tiktok_url' => 'TikTok',
                            'youtube_url' => 'YouTube',
                        ];
                    @endphp
                    @foreach($socials as $field => $label)
                        <div class="col-md-6">
                            <label class="form-label">{{ $label }}</label>
                            <input type="url" name="{{ $field }}" class="form-control @error($field) is-invalid @enderror"
                                value="{{ old($field, $setting->$field) }}" placeholder="https://">
                            @error($field)
                            <div class="invalid-feedback">{{ $message }}</div>@enderror
                        </div>
                    @endforeach
                </div>
                <div class="text-end mt-3">
                    <button type="submit" class="btn btn-primary px-4"><i class="fas fa-save me-1"></i> Save
                        Settings</button>
                </div>
            </div>


        </form>
    </div>

@endsection