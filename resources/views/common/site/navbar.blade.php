<nav class="navbar navbar-expand-lg bg-white navbar-light fixed-top p-0 shadow-sm">
    <a href="/" class="navbar-brand d-flex align-items-center px-4 px-lg-5">
        <h1 class="m-0">Gardener</h1>
    </a>

    <button type="button" class="navbar-toggler me-4" data-bs-toggle="collapse" data-bs-target="#navbarCollapse">
        <span class="navbar-toggler-icon"></span>
    </button>

    <div class="collapse navbar-collapse" id="navbarCollapse">
        <div class="navbar-nav ms-auto p-4 p-lg-0 align-items-lg-center">
            <a href="/" class="nav-item nav-link {{ Request::is('/') ? 'active' : '' }}">Home</a>
            <a href="/service" class="nav-item nav-link {{ Request::is('service') ? 'active' : '' }}">Services</a>
            <a href="/about" class="nav-item nav-link {{ Request::is('about') ? 'active' : '' }}">About</a>
            <a href="/project" class="nav-item nav-link {{ Request::is('project') ? 'active' : '' }}">Projects</a>
            <a href="/contact" class="nav-item nav-link {{ Request::is('contact') ? 'active' : '' }}">Contact</a>

            @auth
                @if(Auth::user()->isAdmin == 0)
                    <a href="{{ route('user.dashboard') }}" class="nav-item nav-link {{ Route::is('user.dashboard') ? 'active' : '' }}">Dashboard</a>
                @else
                    <a href="{{ route('viewAdminPanel') }}" class="nav-item nav-link {{ Route::is('viewAdminPanel') ? 'active' : '' }}">Admin Panel</a>
                @endif
            @endauth
        </div>

        <!-- Action Buttons & Search -->
        <div class="d-flex align-items-center p-4 p-lg-0">
            <button type="button" class="btn text-primary me-2 py-3 px-3 rounded-0" data-bs-toggle="modal" data-bs-target="#searchModal">
                <i class="fa fa-search"></i>
            </button>

            <a href="/quote" class="btn btn-primary py-3 px-lg-4 rounded-0 me-2 d-none d-lg-block">
                Get A Quote <i class="fa fa-arrow-right ms-2"></i>
            </a>

            @auth
                <form action="{{ route('logout') }}" method="POST" class="d-inline">
                    @csrf
                    <button type="submit" class="btn btn-danger py-3 px-lg-4 rounded-0">Logout</button>
                </form>
            @else
                <a href="/login" class="btn btn-primary py-3 px-lg-4 rounded-0">Login</a>
            @endauth
        </div>
    </div>
</nav>
