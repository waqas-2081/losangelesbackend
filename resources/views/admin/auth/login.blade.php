<!doctype html>
<html lang="en">
<head>
    <meta charset="utf-8" />
    <title>Login New | Admin Panel</title>
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
                                    <h5>Welcome Back!</h5>
                                    <p class="text-muted">Sign in to continue to Admin Panel.</p>
                                </div>

                                <div class="p-2 mt-4">
                                    <form action="{{ route('login') }}" method="POST">
                                        @csrf

                                        {{-- Email --}}
                                        <div class="mb-3">
                                            <label class="form-label" for="email">Email</label>
                                            <div class="position-relative input-custom-icon">
                                                <input type="email"
                                                    class="form-control @error('email') is-invalid @enderror"
                                                    id="email"
                                                    name="email"
                                                    value="{{ old('email') }}"
                                                    placeholder="Enter email"
                                                    autofocus>
                                                <span class="bx bx-mail-send"></span>
                                                @error('email')
                                                    <div class="invalid-feedback">{{ $message }}</div>
                                                @enderror
                                            </div>
                                        </div>

                                        {{-- Password --}}
                                        <div class="mb-3">
                                            <div class="float-end">
                                                <a href="#" class="text-muted text-decoration-underline">Forgot password?</a>
                                            </div>
                                            <label class="form-label" for="password">Password</label>
                                            <div class="position-relative auth-pass-inputgroup input-custom-icon">
                                                <span class="bx bx-lock-alt"></span>
                                                <input type="password"
                                                    class="form-control @error('password') is-invalid @enderror"
                                                    id="password"
                                                    name="password"
                                                    placeholder="Enter password">
                                                <button type="button" class="btn btn-link position-absolute h-100 end-0 top-0" id="password-addon">
                                                    <i class="mdi mdi-eye-outline font-size-18 text-muted"></i>
                                                </button>
                                                @error('password')
                                                    <div class="invalid-feedback">{{ $message }}</div>
                                                @enderror
                                            </div>
                                        </div>

                                        {{-- Remember Me --}}
                                        <div class="form-check py-1">
                                            <input type="checkbox" class="form-check-input" id="remember" name="remember">
                                            <label class="form-check-label" for="remember">Remember me</label>
                                        </div>

                                        <div class="mt-3">
                                            <button class="btn btn-primary w-100 waves-effect waves-light" type="submit">
                                                Log In
                                            </button>
                                        </div>

                                        <div class="mt-4 text-center">
                                            <p class="mb-0">
                                                Don't have an account?
                                                <a href="{{ route('register') }}" class="fw-medium text-primary">Signup now</a>
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
    <script src="{{ asset('assets/js/pages/pass-addon.init.js') }}"></script>
</body>
</html>