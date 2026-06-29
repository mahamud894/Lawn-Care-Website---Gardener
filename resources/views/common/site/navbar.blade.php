<nav class="navbar navbar-expand-lg bg-white navbar-light sticky-top p-0">
        <a href="/" class="navbar-brand d-flex align-items-center px-4 px-lg-5">
            <h1 class="m-0">Gardener</h1>
        </a>
        <button type="button" class="navbar-toggler me-4" data-bs-toggle="collapse" data-bs-target="#navbarCollapse">
            <span class="navbar-toggler-icon"></span>
        </button>
        <div class="collapse navbar-collapse" id="navbarCollapse">
            <div class="navbar-nav ms-auto p-4 p-lg-0">
                <a href="/" class="nav-item nav-link active">Home</a>
                <a href="/about" class="nav-item nav-link">About</a>
                <a href="/service" class="nav-item nav-link">Services</a>
                <a href="/project" class="nav-item nav-link">Projects</a>
                <a href="/contact" class="nav-item nav-link">Contact</a>
                   @auth
                      @if(Auth::user()->isAdmin == 0)
                         <a href="{{ route('user.dashboard') }}" class="nav-item nav-link {{ Route::is('user.dashboard') ? 'active' : '' }}">Dashboard</a>
                      @else
                         <a href="{{ route('viewAdminPanel') }}" class="nav-item nav-link">Admin Panel</a>
                      @endif
                   @endauth
              <a href="/quote" class="btn btn-primary py-4 px-lg-4 rounded-0 d-none d-lg-block">Get A Quote<i class="fa fa-arrow-right ms-3"></i></a>
            </div>

             <button type="button" class="btn text-primary ms-3 py-3 px-lg-3 rounded-0 d-none d-lg-block" data-bs-toggle="modal" data-bs-target="#searchModal"><i class="fa fa-search"></i></button>


          <!-- #region -->

                @if(Auth::user())
                    <a href="/logout" class="btn btn-danger py-4 px-lg-4 rounded-0 d-none d-lg-block">Logout</a>
                @else
                    <a href="/login" class="btn btn-primary py-4 px-lg-4 rounded-0 d-none d-lg-block">Login</a>
                @endif

        </div>
    </nav>
