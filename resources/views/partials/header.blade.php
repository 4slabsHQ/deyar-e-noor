<div class="nav-header">
    <a href="{{ route('dashboard') }}" class="brand-logo">
        <img src="{{ asset('images/logo.png') }}" alt="Logo" class="logo-abbr" style="height:40px;">
        <div class="brand-title">
            <h2>Travel ERP</h2>
            <span class="brand-sub-title">Management System</span>
        </div>
    </a>
    <div class="nav-control">
        <div class="hamburger">
            <span class="line"></span><span class="line"></span><span class="line"></span>
        </div>
    </div>
</div>

<div class="header border-bottom">
    <div class="header-content">
        <nav class="navbar navbar-expand">
            <div class="collapse navbar-collapse justify-content-between">
                <div class="header-left">
                    <div class="dashboard_bar">
                        @yield('page-title', 'Dashboard')
                    </div>
                </div>
                <ul class="navbar-nav header-right">
                    <li class="nav-item dropdown header-profile">
                        <a class="nav-link" href="javascript:void(0);" role="button" data-bs-toggle="dropdown">
                            <img src="{{ asset('images/user.jpg') }}" width="20" alt=""/>
                            <div class="header-info ms-3">
                                <span class="fs-18 font-w500 mb-2">{{ auth()->user()->name ?? 'Guest' }}</span>
                                <small class="fs-12 font-w400">{{ auth()->user()->email ?? '' }}</small>
                            </div>
                        </a>
                        <div class="dropdown-menu dropdown-menu-end">
                            <a href="{{ route('admin.profile.edit') }}" class="dropdown-item ai-icon">
                                <i class="fas fa-user text-primary"></i>
                                <span class="ms-2">Profile</span>
                            </a>
                            <form method="POST" action="{{ route('logout') }}">
                                @csrf
                                <button type="submit" class="dropdown-item ai-icon">
                                    <i class="fas fa-sign-out-alt text-danger"></i>
                                    <span class="ms-2">Logout</span>
                                </button>
                            </form>
                        </div>
                    </li>
                </ul>
            </div>
        </nav>
    </div>
</div>