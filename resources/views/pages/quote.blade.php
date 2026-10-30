<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">

<head>
    <meta charset="utf-8">
    <title>{{ __('Get a Free Quote — Gardener') }}</title>
    <meta content="width=device-width, initial-scale=1.0" name="viewport">
    <meta content="lawn care, gardening, landscaping, lawn mowing, garden maintenance" name="keywords">
    <meta content="Request a lawn care or gardening quote and pick a date and time that suits you." name="description">

    <!-- 💡 CSRF Token added for AJAX -->
    <meta name="csrf-token" content="{{ csrf_token() }}">

    <!-- Favicon -->
    <link href="site/img/favicon.ico" rel="icon">

    <!-- Google Web Fonts -->
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link rel="preconnect" href="https://cdnjs.cloudflare.com" crossorigin>
    <link rel="preconnect" href="https://cdn.jsdelivr.net" crossorigin>
    <link rel="preconnect" href="https://code.jquery.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Jost:wght@500;600;700&family=Open+Sans:wght@400;500&display=swap" rel="stylesheet">

    <!-- Icon Font Stylesheet -->
    <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/5.10.0/css/all.min.css" rel="stylesheet">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.4.1/font/bootstrap-icons.css" rel="stylesheet">

    <!-- Libraries Stylesheet -->
    <link href="site/lib/animate/animate.min.css" rel="stylesheet">
    <link href="site/lib/owlcarousel/assets/owl.carousel.min.css" rel="stylesheet">
    <link href="site/lib/lightbox/css/lightbox.min.css" rel="stylesheet">

    <!-- Customized Bootstrap Stylesheet -->
    <link href="site/css/bootstrap.min.css" rel="stylesheet">

    <!-- Template Stylesheet -->
    <link href="site/css/style.css" rel="stylesheet">
    <link href="{{ asset('site/css/motion.css') }}" rel="stylesheet">
</head>

<body>

    <!-- Topbar Start -->
    <div class="container-fluid bg-dark text-light px-0 py-2">
        <div class="row gx-0 d-none d-lg-flex">
            <div class="col-lg-7 px-5 text-start">
                <div class="h-100 d-inline-flex align-items-center me-4">
                    <span class="fa fa-phone-alt me-2"></span>
                    <span>+8801846650807</span>
                </div>
                <div class="h-100 d-inline-flex align-items-center">
                    <span class="far fa-envelope me-2"></span>
                    <span>mdmahamudul894@gmail.com</span>
                </div>
            </div>
            <div class="col-lg-5 px-5 text-end">
                <div class="h-100 d-inline-flex align-items-center mx-n2">
                    <span>{{ __('Follow Us:') }}</span>
                    <a class="btn btn-link text-light" href="https://wa.me/{{ config('gardener.whatsapp_number') }}" target="_blank" rel="noopener" title="WhatsApp"><i class="fab fa-whatsapp"></i></a>
                    <a class="btn btn-link text-light" href="mailto:mdmahamudul894@gmail.com" title="E-mail"><i class="fa fa-envelope"></i></a>
                </div>
            </div>
        </div>
      </div>
    <!-- Topbar End -->

    <!-- Navbar Start -->
    <div class="container-fluid position-relative p-0">
        @include('common.site.navbar')
    </div>
    <!-- Navbar End -->

    <!-- Page Header Start -->
    <div class="container-fluid page-header py-5">
        <div class="container text-center py-5">
            <h1 class="display-3 text-white mb-4 animated slideInDown">{{ __('Get A Quote') }}</h1>
            <nav aria-label="breadcrumb animated slideInDown">
                <ol class="breadcrumb justify-content-center mb-0">
                    <li class="breadcrumb-item"><a href="#">{{ __('Home') }}</a></li>
                    <li class="breadcrumb-item"><a href="#">{{ __('Pages') }}</a></li>
                    <li class="breadcrumb-item active" aria-current="page">{{ __('Get A Quote') }}</li>
                </ol>
            </nav>
        </div>
    </div>
    <!-- Page Header End -->

    <!-- Quote Start -->
    <div class="container-fluid quote py-5" data-parallax="scroll" data-image-src="{{ asset('site/img/carousel-2.jpg') }}">
        <div class="container py-5">
            <div class="row justify-content-center">
                <div class="col-lg-7">
                    <div class="bg-white rounded p-4 p-sm-5 wow fadeIn" data-wow-delay="0.5s">
                        <h1 class="display-5 text-center mb-3">{{ __('Request A Quote') }}</h1>
                        <p class="fs-5 fw-bold text-primary text-center mb-4">{{ __('Please complete and submit the form below to get started.') }}</p>

                        @include('common.site.booking-form', ['formId' => 'quote'])
                    </div>
                </div>
            </div>
        </div>
    </div>
    <!-- Quote End -->

    <!-- Footer Start -->
    @include('common.site.footer')

    <!-- Back to Top -->
    <a href="#" class="btn btn-lg btn-primary btn-lg-square rounded-circle back-to-top"><i class="bi bi-arrow-up"></i></a>

    <!-- JavaScript Libraries -->
    <script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.0.0/dist/js/bootstrap.bundle.min.js"></script>
    <script src="site/lib/wow/wow.min.js"></script>
    <script src="site/lib/easing/easing.min.js"></script>
    <script src="site/lib/waypoints/waypoints.min.js"></script>
    <script src="site/lib/owlcarousel/owl.carousel.min.js"></script>
    <script src="site/lib/counterup/counterup.min.js"></script>
    <script src="site/lib/parallax/parallax.min.js"></script>
    <script src="site/lib/isotope/isotope.pkgd.min.js"></script>
    <script src="site/lib/lightbox/js/lightbox.min.js"></script>

    <!-- Template Javascript -->
    <script src="site/js/main.js"></script>

    @include('common.site.booking-form-script')

    @include('sweetalert::alert')
</body>
</html>
