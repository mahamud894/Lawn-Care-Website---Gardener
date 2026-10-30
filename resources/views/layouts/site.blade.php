{{--
    Layout for the public pages added later (service detail, blog, FAQ, profile).
    Sections: title, description, header (page title), content. Optional stack: scripts.
--}}
<!DOCTYPE html>
<html lang="{{ app()->getLocale() }}">
<head>
    <meta charset="utf-8">
    <title>@yield('title') — Gardener</title>
    <meta content="width=device-width, initial-scale=1.0" name="viewport">
    <meta content="@yield('description', __('Professional lawn care and gardening services.'))" name="description">
    <link href="{{ asset('site/img/favicon.ico') }}" rel="icon">

    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link rel="preconnect" href="https://cdnjs.cloudflare.com" crossorigin>
    <link rel="preconnect" href="https://cdn.jsdelivr.net" crossorigin>
    <link rel="preconnect" href="https://code.jquery.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Jost:wght@500;600;700&family=Open+Sans:wght@400;500&display=swap" rel="stylesheet">
    <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/5.10.0/css/all.min.css" rel="stylesheet">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.4.1/font/bootstrap-icons.css" rel="stylesheet">
    <link href="{{ asset('site/lib/animate/animate.min.css') }}" rel="stylesheet">
    <link href="{{ asset('site/lib/owlcarousel/assets/owl.carousel.min.css') }}" rel="stylesheet">
    <link href="{{ asset('site/lib/lightbox/css/lightbox.min.css') }}" rel="stylesheet">
    <link href="{{ asset('site/css/bootstrap.min.css') }}" rel="stylesheet">
    <link href="{{ asset('site/css/style.css') }}" rel="stylesheet">
    <link href="{{ asset('site/css/motion.css') }}" rel="stylesheet">
    @stack('styles')
</head>
<body>
    <div class="container-fluid position-relative p-0">
        @include('common.site.navbar')
    </div>

    <div class="container-fluid page-header py-5 mb-5">
        <div class="container text-center py-5">
            <h1 class="display-4 text-white mb-4 animated slideInDown">@yield('header')</h1>
            <nav aria-label="breadcrumb animated slideInDown">
                <ol class="breadcrumb justify-content-center mb-0">
                    <li class="breadcrumb-item"><a href="{{ route('home') }}">{{ __('Home') }}</a></li>
                    @hasSection('breadcrumb')
                        @yield('breadcrumb')
                    @endif
                    <li class="breadcrumb-item active" aria-current="page">@yield('header')</li>
                </ol>
            </nav>
        </div>
    </div>

    @yield('content')

    @include('common.site.footer')

    <a href="#" class="btn btn-lg btn-primary btn-lg-square rounded-circle back-to-top"><i class="bi bi-arrow-up"></i></a>

    <script src="https://code.jquery.com/jquery-3.4.1.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.0.0/dist/js/bootstrap.bundle.min.js"></script>
    <script src="{{ asset('site/lib/wow/wow.min.js') }}"></script>
    <script src="{{ asset('site/lib/easing/easing.min.js') }}"></script>
    <script src="{{ asset('site/lib/waypoints/waypoints.min.js') }}"></script>
    <script src="{{ asset('site/lib/owlcarousel/owl.carousel.min.js') }}"></script>
    <script src="{{ asset('site/lib/counterup/counterup.min.js') }}"></script>
    <script src="{{ asset('site/lib/parallax/parallax.min.js') }}"></script>
    <script src="{{ asset('site/lib/isotope/isotope.pkgd.min.js') }}"></script>
    <script src="{{ asset('site/lib/lightbox/js/lightbox.min.js') }}"></script>
    <script src="{{ asset('site/js/main.js') }}"></script>
    @stack('scripts')
    @include('sweetalert::alert')
</body>
</html>
