<!doctype html>
<html lang="en">
<head>
    <meta charset="utf-8" />
    <title>Register | Admin Panel</title>
    <meta name="viewport" content="width=device-width, initial-scale=1.0">

    <link rel="shortcut icon" href="{{ asset('assets/images/favicon.ico') }}">
    <link href="{{ asset('assets/css/bootstrap.min.css') }}" rel="stylesheet" type="text/css" />
    <link href="{{ asset('assets/css/icons.min.css') }}" rel="stylesheet" type="text/css" />
    <link href="{{ asset('assets/css/app.min.css') }}" rel="stylesheet" type="text/css" />
</head>

<body>
    <div class="authentication-bg min-vh-100">
        <div class="bg-overlay bg-light"></div>
        <div class="container">
            <div class="d-flex flex-column min-vh-100 px-3 pt-4">
                <div class="row justify-content-center my-auto">
                    <div class="col-md-8 col-lg-6 col-xl-5">

                        <div class="mb-4 pb-2">
                            <a href="#" class="d-block auth-logo">
                                <img src="{{ asset('assets/images/logo-dark.png') }}" alt="" height="30" class="auth-logo-dark me-start">
                                <img src="{{ asset('assets/images/logo-light.png') }}" alt="" height="30" class="auth-logo-light me-start">
                            </a>
                        </div>

                        <div class="card">
                            <div class="card-body p-4">
                                <div class="text-center mt-2">
                                    <h5>Register Account</h5>
                                    <p class="text-muted">Get your free admin account now.</p>
                                </div>

                                <div class="p-2 mt-4">
                                    <form action="{{ route('register') }}" method="POST">
                                        @csrf

                                        {{-- Name --}}
                                        <div class="mb-3">
                                            <label class="form-label" for="name">Full Name</label>
                                            <div class="position-relative input-custom-icon">
                                                <input type="text"
                                                    class="form-control @error('name') is-invalid @enderror"
                                                    id="name"
                                                    name="name"
                                                    value="{{ old('name') }}"
                                                    placeholder="Enter full name"
                                                    autofocus>
                                                <span class="bx bx-user"></span>
                                                @error('name')
                                                    <div class="invalid-feedback">{{ $message }}</div>
                                                @enderror
                                            </div>
                                        </div>

                                        {{-- Email --}}
                                        <div class="mb-3">
                                            <label class="form-label" for="email">Email</label>
                                            <div class="position-relative input-custom-icon">
                                                <input type="email"
                                                    class="form-control @error('email') is-invalid @enderror"
                                                    id="email"
                                                    name="email"
                                                    value="{{ old('email') }}"
                                                    placeholder="Enter email">
                                                <span class="bx bx-mail-send"></span>
                                                @error('email')
                                                    <div class="invalid-feedback">{{ $message }}</div>
                                                @enderror
                                            </div>
                                        </div>

                                        {{-- Password --}}
                                        <div class="mb-3">
                                            <label class="form-label" for="password">Password</label>
                                            <div class="position-relative auth-pass-inputgroup input-custom-icon">
                                                <span class="bx bx-lock-alt"></span>
                                                <input type="password"
                                                    class="form-control @error('password') is-invalid @enderror"
                                                    id="password"
                                                    name="password"
                                                    placeholder="Enter password">
                                                @error('password')
                                                    <div class="invalid-feedback">{{ $message }}</div>
                                                @enderror
                                            </div>
                                        </div>

                                        {{-- Confirm Password --}}
                                        <div class="mb-3">
                                            <label class="form-label" for="password_confirmation">Confirm Password</label>
                                            <div class="position-relative input-custom-icon">
                                                <span class="bx bx-lock-alt"></span>
                                                <input type="password"
                                                    class="form-control"
                                                    id="password_confirmation"
                                                    name="password_confirmation"
                                                    placeholder="Confirm password">
                                            </div>
                                        </div>

                                        <div class="mt-3">
                                            <button class="btn btn-primary w-100 waves-effect waves-light" type="submit">
                                                Register
                                            </button>
                                        </div>

                                        <div class="mt-4 text-center">
                                            <p class="mb-0">
                                                Already have an account?
                                                <a href="{{ route('login') }}" class="fw-medium text-primary">Login</a>
                                            </p>
                                        </div>
                                    </form>
                                </div>
                            </div>
                        </div>

                    </div>
                </div>

                <div class="row">
                    <div class="col-lg-12">
                        <div class="text-center p-4">
                            <p>© {{ date('Y') }} Admin Panel. All rights reserved.</p>
                        </div>
                    </div>
                </div>

            </div>
        </div>
    </div>

    <script src="{{ asset('assets/libs/bootstrap/js/bootstrap.bundle.min.js') }}"></script>
    <script src="{{ asset('assets/libs/eva-icons/eva.min.js') }}"></script>
</body>
</html>