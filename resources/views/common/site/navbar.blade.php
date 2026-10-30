<nav class="navbar navbar-expand-lg bg-white navbar-light fixed-top p-0 shadow-sm">
    <a href="{{ route('home') }}" class="navbar-brand d-flex align-items-center px-4 px-lg-5">
        <h1 class="m-0">Gardener</h1>
    </a>

    <button type="button" class="navbar-toggler me-4" data-bs-toggle="collapse" data-bs-target="#navbarCollapse">
        <span class="navbar-toggler-icon"></span>
    </button>

    <div class="collapse navbar-collapse" id="navbarCollapse">
        <div class="navbar-nav ms-auto p-4 p-lg-0 align-items-lg-center">
            <a href="{{ route('home') }}" class="nav-item nav-link {{ Request::is('/') ? 'active' : '' }}">{{ __('Home') }}</a>
            <a href="{{ route('service') }}" class="nav-item nav-link {{ Request::is('service*') ? 'active' : '' }}">{{ __('Services') }}</a>
            <a href="{{ route('about') }}" class="nav-item nav-link {{ Request::is('about') ? 'active' : '' }}">{{ __('About') }}</a>
            <a href="{{ route('project') }}" class="nav-item nav-link {{ Request::is('project') ? 'active' : '' }}">{{ __('Projects') }}</a>
            <a href="{{ route('blog') }}" class="nav-item nav-link {{ Request::is('blog*') ? 'active' : '' }}">{{ __('Blog') }}</a>
            <a href="{{ route('contact') }}" class="nav-item nav-link {{ Request::is('contact') ? 'active' : '' }}">{{ __('Contact') }}</a>

            @auth
                <div class="nav-item dropdown">
                    <a href="#" class="nav-link dropdown-toggle" data-bs-toggle="dropdown"><i class="fa fa-user-circle me-1"></i>{{ \Illuminate\Support\Str::words(Auth::user()->name, 1, '') }}</a>
                    <div class="dropdown-menu bg-light m-0">
                        @if(Auth::user()->isAdmin)
                            <a href="{{ route('viewAdminPanel') }}" class="dropdown-item">{{ __('Admin Panel') }}</a>
                        @endif
                        @if(Auth::user()->isGardener)
                            <a href="{{ route('gardener.dashboard') }}" class="dropdown-item">{{ __('My Jobs') }}</a>
                        @endif
                        @unless(Auth::user()->isAdmin)
                            <a href="{{ route('user.dashboard') }}" class="dropdown-item">{{ __('My Bookings') }}</a>
                        @endunless
                        <a href="{{ route('profile') }}" class="dropdown-item">{{ __('My Profile') }}</a>
                    </div>
                </div>
            @endauth

            {{-- Language switcher --}}
            <div class="nav-item dropdown">
                <a href="#" class="nav-link dropdown-toggle" data-bs-toggle="dropdown" title="Language"><i class="fa fa-globe me-1"></i>{{ app()->getLocale() === 'bn' ? 'বাং' : 'EN' }}</a>
                <div class="dropdown-menu bg-light m-0">
                    @foreach(config('gardener.locales') as $code => $label)
                        <a href="{{ route('locale', $code) }}" class="dropdown-item {{ app()->getLocale() === $code ? 'active' : '' }}">{{ $label }}</a>
                    @endforeach
                </div>
            </div>
        </div>

        <!-- Action Buttons -->
        <div class="d-flex align-items-center p-4 p-lg-0">
            <a href="{{ route('quote') }}" class="btn btn-primary py-3 px-lg-4 rounded-0 me-2 d-none d-lg-block">
                {{ __('Get A Quote') }} <i class="fa fa-arrow-right ms-2"></i>
            </a>

            @auth
                <form action="{{ route('logout') }}" method="POST" class="d-inline">
                    @csrf
                    <button type="submit" class="btn btn-danger py-3 px-lg-4 rounded-0">{{ __('Logout') }}</button>
                </form>
            @else
                <a href="{{ route('login') }}" class="btn btn-primary py-3 px-lg-4 rounded-0">{{ __('Login') }}</a>
            @endauth
        </div>
    </div>
</nav>
