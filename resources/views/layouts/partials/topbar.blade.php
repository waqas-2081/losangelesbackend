<header id="page-topbar" class="isvertical-topbar card">
    <div class="navbar-header">
        <div class="d-flex">
            <div class="navbar-brand-box">
                <a href="{{ route('admin.dashboard') }}" class="logo logo-dark">
                    <span class="logo-sm">
                        <img src="{{ asset('assets/images/logo-dark-sm.png') }}" alt="" height="26">
                    </span>
                    <span class="logo-lg">
                        <img src="{{ asset('assets/images/logo-dark-sm.png') }}" alt="" height="26">
                    </span>
                </a>
                <a href="{{ route('admin.dashboard') }}" class="logo logo-light">
                    <span class="logo-lg">
                        <img src="{{ asset('assets/images/logo-light.png') }}" alt="" height="30">
                    </span>
                    <span class="logo-sm">
                        <img src="{{ asset('assets/images/logo-light-sm.png') }}" alt="" height="26">
                    </span>
                </a>
            </div>

            <button type="button" class="btn btn-sm px-3 font-size-24 header-item waves-effect vertical-menu-btn">
                <i class="bx bx-menu align-middle"></i>
            </button>

            <div class="page-title-box align-self-center d-none d-md-block">
                <h4 class="page-title mb-0">@yield('page-title', 'Dashboard')</h4>
            </div>
        </div>

        <div class="d-flex">

            {{-- User Dropdown --}}
            <div class="dropdown d-inline-block">
                <button type="button" class="btn header-item user text-start d-flex align-items-center"
                    id="page-header-user-dropdown-v"
                    data-bs-toggle="dropdown" aria-haspopup="true" aria-expanded="false">
                    <img class="rounded-circle header-profile-user"
                        src="{{ asset('assets/images/users/avatar-3.jpg') }}" alt="Avatar">
                    <span class="d-none d-xl-inline-block ms-2 fw-medium font-size-15">
                        {{ auth()->user()->name }}
                    </span>
                </button>
                <div class="dropdown-menu dropdown-menu-end pt-0">
                    <div class="p-3 border-bottom">
                        <h6 class="mb-0">{{ auth()->user()->name }}</h6>
                        <p class="mb-0 font-size-11 text-muted">{{ auth()->user()->email }}</p>
                    </div>
                    <div class="dropdown-divider"></div>
                    <a class="dropdown-item" href="{{ route('logout') }}"
                        onclick="event.preventDefault(); document.getElementById('logout-form').submit();">
                        <i class="mdi mdi-logout text-muted font-size-16 align-middle me-2"></i>
                        <span class="align-middle">Logout</span>
                    </a>
                    <form id="logout-form" action="{{ route('logout') }}" method="POST" class="d-none">
                        @csrf
                    </form>
                </div>
            </div>

        </div>
    </div>
</header>