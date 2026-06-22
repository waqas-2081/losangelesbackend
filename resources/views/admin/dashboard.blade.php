@extends('layouts.app')

@section('title', 'Dashboard')
@section('page-title', 'Welcome Back!')

@section('content')

    <div class="row">
        <div class="col-xl-3 col-md-6">
            <div class="card">
                <div class="card-body">
                    <div class="d-flex align-items-center">
                        <div class="avatar">
                            <div class="avatar-title rounded bg-primary-subtle">
                                <i class="bx bx-image font-size-24 mb-0 text-primary"></i>
                            </div>
                        </div>
                        <div class="flex-grow-1 ms-3">
                            <h6 class="mb-0 font-size-15">Logo Briefs</h6>
                        </div>
                    </div>
                    <div class="mt-4 pt-1">
                        <h4 class="font-size-22 mb-1">{{ $stats['logo_briefs'] }}</h4>
                        <p class="text-muted mb-0">Total submissions</p>
                    </div>
                </div>
            </div>
        </div>

        <div class="col-xl-3 col-md-6">
            <div class="card">
                <div class="card-body">
                    <div class="d-flex align-items-center">
                        <div class="avatar">
                            <div class="avatar-title rounded bg-primary-subtle">
                                <i class="bx bx-globe font-size-24 mb-0 text-primary"></i>
                            </div>
                        </div>
                        <div class="flex-grow-1 ms-3">
                            <h6 class="mb-0 font-size-15">Website Briefs</h6>
                        </div>
                    </div>
                    <div class="mt-4 pt-1">
                        <h4 class="font-size-22 mb-1">{{ $stats['website_briefs'] }}</h4>
                        <p class="text-muted mb-0">Total submissions</p>
                    </div>
                </div>
            </div>
        </div>

        <div class="col-xl-3 col-md-6">
            <div class="card">
                <div class="card-body">
                    <div class="d-flex align-items-center">
                        <div class="avatar">
                            <div class="avatar-title rounded bg-primary-subtle">
                                <i class="bx bx-envelope font-size-24 mb-0 text-primary"></i>
                            </div>
                        </div>
                        <div class="flex-grow-1 ms-3">
                            <h6 class="mb-0 font-size-15">Contacts</h6>
                        </div>
                    </div>
                    <div class="mt-4 pt-1">
                        <h4 class="font-size-22 mb-1">{{ $stats['contacts'] }}</h4>
                        <p class="text-muted mb-0">Total inquiries</p>
                    </div>
                </div>
            </div>
        </div>

        <div class="col-xl-3 col-md-6">
            <div class="card">
                <div class="card-body">
                    <div class="d-flex align-items-center">
                        <div class="avatar">
                            <div class="avatar-title rounded bg-primary-subtle">
                                <i class="bx bx-news font-size-24 mb-0 text-primary"></i>
                            </div>
                        </div>
                        <div class="flex-grow-1 ms-3">
                            <h6 class="mb-0 font-size-15">Total Blogs</h6>
                        </div>
                    </div>
                    <div class="mt-4 pt-1">
                        <h4 class="font-size-22 mb-1">{{ $stats['blogs'] }}</h4>
                        <p class="text-muted mb-0">Published posts</p>
                    </div>
                </div>
            </div>
        </div>
    </div>
    {{-- End Stats Row --}}

    {{-- Welcome Card --}}
    <div class="row">
        <div class="col-12">
            <div class="card">
                <div class="card-body">
                    <h5 class="card-title">Welcome, {{ auth()->user()->name }}!</h5>
                    <p class="text-muted mb-0">
                        You are logged in as <strong>{{ auth()->user()->email }}</strong>.
                        Use the sidebar to navigate through the admin panel.
                    </p>
                </div>
            </div>
        </div>
    </div>

@endsection