<div class="vertical-menu">

    <div class="navbar-brand-box">
        <a href="{{ route('admin.dashboard') }}" class="logo logo-dark">
            <span class="logo-sm">
                <img src="{{ asset('assets/images/logo-dark-sm.png') }}" alt="" height="26">
            </span>
            <span class="logo-lg">
                <img src="{{ asset('assets/images/logo-dark.png') }}" alt="" height="70">
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

    <div data-simplebar class="sidebar-menu-scroll">
        <div id="sidebar-menu">

            <ul class="metismenu list-unstyled" id="side-menu">
                <li class="menu-title" data-key="t-menu">Dashboard</li>

                <li>
                    <a href="{{ route('admin.dashboard') }}">
                        <i class="bx bx-home-alt icon nav-icon"></i>
                        <span class="menu-item" data-key="t-dashboard">Dashboard</span>
                    </a>
                </li>

                <li class="menu-title" data-key="t-applications">Applications</li>

                <li>
                    <a href="{{ route('admin.logo-briefs.index') }}">
                        <i class="bx bx-image icon nav-icon"></i>
                        <span class="menu-item" data-key="t-logo-brief">Logo Briefs</span>
                    </a>
                </li>

                <li>
                    <a href="{{ route('admin.website-briefs.index') }}">
                        <i class="bx bx-globe icon nav-icon"></i>
                        <span class="menu-item" data-key="t-website-brief">Website Briefs</span>
                    </a>
                </li>

                <li>
                    <a href="{{ route('admin.promo-leads.index') }}">
                        <i class="bx bx-image icon nav-icon"></i>
                        <span class="menu-item" data-key="t-logo-brief">Popup Form</span>
                    </a>
                </li>

                <li>
                    <a href="{{ route('admin.contacts.index') }}">
                        <i class="bx bx-user icon nav-icon"></i>
                        <span class="menu-item" data-key="t-contacts">Contacts</span>
                    </a>
                </li>
                 

                <li>
                    <a href="javascript: void(0);" class="has-arrow">
                        <i class="bx bx-box icon nav-icon"></i>
                        <span class="menu-item" data-key="t-packages">Packages</span>
                    </a>
                    <ul class="sub-menu" aria-expanded="false">
                        <li><a href="{{ route('admin.packages.index') }}" data-key="t-manage-packages">Manage
                                Packages</a></li>
                        <li><a href="{{ route('admin.packages.create') }}" data-key="t-add-packages">Add Packages</a>
                        </li>
                    </ul>
                </li>
                <li>
                    <a href="javascript: void(0);" class="has-arrow">
                        <i class="bx bx-news icon nav-icon"></i>
                        <span class="menu-item" data-key="t-blogs">Blogs</span>
                    </a>
                    <ul class="sub-menu" aria-expanded="false">
                        <li><a href="{{ route('admin.blogs.index') }}" data-key="t-manage-blogs">Manage Blogs</a></li>
                        <li><a href="{{ route('admin.blogs.create') }}" data-key="t-add-blog">Add Blog</a></li>
                    </ul>
                </li>

                <li>
                    <a href="javascript: void(0);" class="has-arrow">
                        <i class="bx bx-briefcase icon nav-icon"></i>
                        <span class="menu-item" data-key="t-portfolio">Portfolio</span>
                    </a>
                    <ul class="sub-menu" aria-expanded="false">
                        <li>
                            <a href="{{ route('admin.portfolios.index') }}" data-key="t-manage-portfolio">
                                Manage Portfolio
                            </a>
                        </li>
                        <li>
                            <a href="{{ route('admin.portfolios.create') }}" data-key="t-add-portfolio">
                                Add Portfolio
                            </a>
                        </li>
                    </ul>
                </li>


            </ul>
        </div>
    </div>

</div>