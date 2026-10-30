<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">

<head>
    <meta charset="utf-8">
    <title>{{ __('Gardener — Lawn Care & Gardening Services') }}</title>
    <meta content="width=device-width, initial-scale=1.0" name="viewport">
    <meta content="lawn care, gardening, landscaping, lawn mowing, garden maintenance" name="keywords">
    <meta content="Professional lawn mowing, landscaping, pruning and garden maintenance. Book a trusted gardener online in minutes." name="description">

    <!-- Favicon -->
    <link href="{{ asset('site/img/favicon.ico') }}" rel="icon">

    <!-- Start downloading the hero image immediately (phones get the small copy) -->
    <link rel="preload" as="image" href="{{ asset('site/img/carousel-1.jpg') }}"
          imagesrcset="{{ asset('site/img/carousel-1-sm.jpg') }} 800w, {{ asset('site/img/carousel-1.jpg') }} 1920w"
          imagesizes="100vw" fetchpriority="high">

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
    <link href="{{ asset('site/lib/animate/animate.min.css') }}" rel="stylesheet">
    <link href="{{ asset('site/lib/owlcarousel/assets/owl.carousel.min.css') }}" rel="stylesheet">
    <link href="{{ asset('site/lib/lightbox/css/lightbox.min.css') }}" rel="stylesheet">

    <!-- Customized Bootstrap Stylesheet -->
    <link href="{{ asset('site/css/bootstrap.min.css') }}" rel="stylesheet">

    <!-- Template Stylesheet -->
    <link href="{{ asset('site/css/style.css') }}" rel="stylesheet">
    <style>
        .team-member-img {
            width: 100%;
            height: 300px;
            object-fit: cover;
            display: block;
        }

        .team-item .team-text {
            padding: 1.5rem;
        }

        .team-item .team-social .btn {
            width: 2.5rem;
            height: 2.5rem;
            display: inline-flex;
            align-items: center;
            justify-content: center;
        }

        /* Price Badge Style */
        .service-price-badge {
            position: absolute;
            top: 15px;
            right: 15px;
            background: #348E38;
            color: #ffffff;
            font-weight: 700;
            padding: 6px 14px;
            border-radius: 20px;
            font-size: 0.95rem;
            box-shadow: 0 4px 10px rgba(0,0,0,0.15);
            z-index: 2;
        }
    </style>
</head>

<body>
    <!-- Spinner Start -->
    <div id="spinner" class="show bg-white position-fixed translate-middle w-100 vh-100 top-50 start-50 d-flex align-items-center justify-content-center">
        <div class="spinner-border text-primary" role="status" style="width: 3rem; height: 3rem;"></div>
    </div>
    <!-- Spinner End -->

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
                    <a class="btn btn-link text-light" href=""><i class="fab fa-facebook-f"></i></a>
                    <a class="btn btn-link text-light" href=""><i class="fab fa-twitter"></i></a>
                    <a class="btn btn-link text-light" href=""><i class="fab fa-linkedin-in"></i></a>
                    <a class="btn btn-link text-light" href=""><i class="fab fa-instagram"></i></a>
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

    <!-- Carousel Start -->
    <div class="container-fluid p-0 wow fadeIn" data-wow-delay="0.1s">
        <div id="header-carousel" class="carousel slide" data-bs-ride="carousel">
            <div class="carousel-inner">
                <div class="carousel-item active">
                    <img class="w-100" src="{{ asset('site/img/carousel-1.jpg') }}"
                         srcset="{{ asset('site/img/carousel-1-sm.jpg') }} 800w, {{ asset('site/img/carousel-1.jpg') }} 1920w"
                         sizes="100vw" width="1920" height="1080" fetchpriority="high" decoding="async" alt="{{ __('Beautiful garden') }}">
                    <div class="carousel-caption">
                        <div class="container">
                            <div class="row justify-content-center">
                                <div class="col-lg-8">
                                    <h1 class="display-1 text-white mb-5 animated slideInDown">{{ __('Make Your Home Like Garden') }}</h1>
                                    <a href="" class="btn btn-primary py-sm-3 px-sm-4">{{ __('Explore More') }}</a>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
                <div class="carousel-item">
                    <img class="w-100" src="{{ asset('site/img/carousel-2.jpg') }}"
                         srcset="{{ asset('site/img/carousel-2-sm.jpg') }} 800w, {{ asset('site/img/carousel-2.jpg') }} 1920w"
                         sizes="100vw" width="1920" height="1080" fetchpriority="low" decoding="async" alt="{{ __('Beautiful garden') }}">
                    <div class="carousel-caption">
                        <div class="container">
                            <div class="row justify-content-center">
                                <div class="col-lg-7">
                                    <h1 class="display-1 text-white mb-5 animated slideInDown">{{ __('Create Your Own Small Garden At Home') }}</h1>
                                    <a href="" class="btn btn-primary py-sm-3 px-sm-4">{{ __('Explore More') }}</a>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
            <button class="carousel-control-prev" type="button" data-bs-target="#header-carousel" data-bs-slide="prev">
                <span class="carousel-control-prev-icon" aria-hidden="true"></span>
                <span class="visually-hidden">{{ __('Previous') }}</span>
            </button>
            <button class="carousel-control-next" type="button" data-bs-target="#header-carousel" data-bs-slide="next">
                <span class="carousel-control-next-icon" aria-hidden="true"></span>
                <span class="visually-hidden">{{ __('Next') }}</span>
            </button>
        </div>
    </div>
    <!-- Carousel End -->


    <!-- Top Feature Start -->
    <div class="container-fluid top-feature py-5 pt-lg-0">
        <div class="container py-5 pt-lg-0">
            <div class="row gx-0">
                <div class="col-lg-4 wow fadeIn" data-wow-delay="0.1s">
                    <div class="bg-white shadow d-flex align-items-center h-100 px-5" style="min-height: 160px;">
                        <div class="d-flex">
                            <div class="flex-shrink-0 btn-lg-square rounded-circle bg-light">
                                <i class="fa fa-times text-primary"></i>
                            </div>
                            <div class="ps-3">
                                <h4>{{ __('No Hidden Cost') }}</h4>
                                <span>{{ __('No hidden fees, no surprise charges—just 100% upfront honesty.') }}</span>
                            </div>
                        </div>
                    </div>
                </div>
                <div class="col-lg-4 wow fadeIn" data-wow-delay="0.3s">
                    <div class="bg-white shadow d-flex align-items-center h-100 px-5" style="min-height: 160px;">
                        <div class="d-flex">
                            <div class="flex-shrink-0 btn-lg-square rounded-circle bg-light">
                                <i class="fa fa-users text-primary"></i>
                            </div>
                            <div class="ps-3">
                                <h4>{{ __('Dedicated Team') }}</h4>
                                <span>{{ __('Our expert team doesn\'t just cut grass; they understand plant health and modern landscape layouts.') }}</span>
                            </div>
                        </div>
                    </div>
                </div>
                <div class="col-lg-4 wow fadeIn" data-wow-delay="0.5s">
                    <div class="bg-white shadow d-flex align-items-center h-100 px-5" style="min-height: 160px;">
                        <div class="d-flex">
                            <div class="flex-shrink-0 btn-lg-square rounded-circle bg-light">
                                <i class="fa fa-phone text-primary"></i>
                            </div>
                            <div class="ps-3">
                                <h4>{{ __('24/7 Available') }}</h4>
                                <span>{{ __('From storm emergencies to quick bookings, we are always just one call away.') }}</span>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
    <!-- Top Feature End -->


    <!-- About Start -->
    <div class="container-xxl py-5">
        <div class="container">
            <div class="row g-5 align-items-end">
                <div class="col-lg-3 col-md-5 wow fadeInUp" data-wow-delay="0.1s">
                    <img loading="lazy" decoding="async" class="img-fluid rounded" data-wow-delay="0.1s" src="{{ asset('site/img/about.jpg') }}"
                         srcset="{{ asset('site/img/about-sm.jpg') }} 400w, {{ asset('site/img/about.jpg') }} 500w"
                         sizes="(max-width: 991px) 90vw, 500px" width="500" height="1000" alt="{{ __('About us') }}">
                </div>
                <div class="col-lg-6 col-md-7 wow fadeInUp" data-wow-delay="0.3s">
                    <h1 class="display-1 text-primary mb-0">10</h1>
                    <p class="text-primary mb-4">{{ __('Year of Experience') }}</p>
                    <h1 class="display-5 mb-4">{{ __('We Make Your Home Like A Garden') }}</h1>
                    <p class="mb-4">{{ __('At Gardener, we don\'t just plant trees; we sculpt emotions into your backyards. We believe your outdoor space should be a living, breathing extension of your living room—a peaceful sanctuary where you can escape the digital noise of the world. Blending the science of modern botany with the art of luxury landscaping, we craft eco-friendly ecosystems that grow more beautiful with every passing season.') }}</p>
                    <a class="btn btn-primary py-3 px-4" href="">{{ __('Explore More') }}</a>
                </div>
                <div class="col-lg-3 col-md-12 wow fadeInUp" data-wow-delay="0.5s">
                    <div class="row g-5">
                        <div class="col-12 col-sm-6 col-lg-12">
                            <div class="border-start ps-4">
                                <i class="fa fa-award fa-3x text-primary mb-3"></i>
                                <h4 class="mb-3">{{ __('Award Winning') }}</h4>
                                <span>{{ __('Recognized for excellence. Celebrated for transforming dull concrete spaces into vibrant, award-winning green ecosystems') }}</span>
                            </div>
                        </div>
                        <div class="col-12 col-sm-6 col-lg-12">
                            <div class="border-start ps-4">
                                <i class="fa fa-users fa-3x text-primary mb-3"></i>
                                <h4 class="mb-3">{{ __('Dedicated Team') }}</h4>
                                <span>{{ __('Our team of experienced professionals is committed to delivering exceptional results for every client.') }}</span>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
    <!-- About End -->


    <!-- Facts Start -->
    <div class="container-fluid facts my-5 py-5" data-parallax="scroll" data-image-src="{{ asset('site/img/carousel-1.jpg') }}">
        <div class="container py-5">
            <div class="row g-5">
                <div class="col-sm-6 col-lg-3 text-center wow fadeIn" data-wow-delay="0.1s">
                    <h1 class="display-4 text-white" data-toggle="counter-up">1234</h1>
                    <span class="fs-5 fw-semi-bold text-light">{{ __('Happy Clients') }}</span>
                </div>
                <div class="col-sm-6 col-lg-3 text-center wow fadeIn" data-wow-delay="0.3s">
                    <h1 class="display-4 text-white" data-toggle="counter-up">1234</h1>
                    <span class="fs-5 fw-semi-bold text-light">{{ __('Garden Complated') }}</span>
                </div>
                <div class="col-sm-6 col-lg-3 text-center wow fadeIn" data-wow-delay="0.5s">
                    <h1 class="display-4 text-white" data-toggle="counter-up">1234</h1>
                    <span class="fs-5 fw-semi-bold text-light">{{ __('Dedicated Staff') }}</span>
                </div>
                <div class="col-sm-6 col-lg-3 text-center wow fadeIn" data-wow-delay="0.7s">
                    <h1 class="display-4 text-white" data-toggle="counter-up">1234</h1>
                    <span class="fs-5 fw-semi-bold text-light">{{ __('Awards Achieved') }}</span>
                </div>
            </div>
        </div>
    </div>
    <!-- Facts End -->


    <!-- Features Start -->
    <div class="container-xxl py-5">
        <div class="container">
            <div class="row g-5 align-items-center">
                <div class="col-lg-6 wow fadeInUp" data-wow-delay="0.1s">
                    <p class="fs-5 fw-bold text-primary">{{ __('Why Choosing Us!') }}</p>
                    <h1 class="display-5 mb-4">{{ __('Few Reasons Why People Choose Us!') }}</h1>
                    <p class="mb-4">{{ __('Ready to experience the ultimate gardening and landscaping service? Our dedicated team blends modern tools with eco-friendly practices to keep your garden blooming in every season. From residential yards to massive commercial projects, we treat every lawn with unmatched care and perfection. Discover why hundreds of homeowners trust us to make their homes look like a paradise.') }}</p>
                    <a class="btn btn-primary py-3 px-4" href="">{{ __('Explore More') }}</a>
                </div>
                <div class="col-lg-6">
                    <div class="row g-4 align-items-center">
                        <div class="col-md-6">
                            <div class="row g-4">
                                <div class="col-12 wow fadeIn" data-wow-delay="0.3s">
                                    <div class="text-center rounded py-5 px-4" style="box-shadow: 0 0 45px rgba(0,0,0,.08);">
                                        <div class="btn-square bg-light rounded-circle mx-auto mb-4" style="width: 90px; height: 90px;">
                                            <i class="fa fa-check fa-3x text-primary"></i>
                                        </div>
                                        <h4 class="mb-0">{{ __('100% Satisfaction') }}</h4>
                                    </div>
                                </div>
                                <div class="col-12 wow fadeIn" data-wow-delay="0.5s">
                                    <div class="text-center rounded py-5 px-4" style="box-shadow: 0 0 45px rgba(0,0,0,.08);">
                                        <div class="btn-square bg-light rounded-circle mx-auto mb-4" style="width: 90px; height: 90px;">
                                            <i class="fa fa-users fa-3x text-primary"></i>
                                        </div>
                                        <h4 class="mb-0">{{ __('Dedicated Team') }}</h4>
                                    </div>
                                </div>
                            </div>
                        </div>
                        <div class="col-md-6 wow fadeIn" data-wow-delay="0.7s">
                            <div class="text-center rounded py-5 px-4" style="box-shadow: 0 0 45px rgba(0,0,0,.08);">
                                <div class="btn-square bg-light rounded-circle mx-auto mb-4" style="width: 90px; height: 90px;">
                                    <i class="fa fa-tools fa-3x text-primary"></i>
                                </div>
                                <h4 class="mb-0">{{ __('Modern Equipment') }}</h4>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
    <!-- Features End -->


    <!-- Service Start -->
    <div class="container-xxl py-5">
        <div class="container">
            <div class="text-center mx-auto wow fadeInUp" data-wow-delay="0.1s" style="max-width: 500px;">
                <p class="fs-5 fw-bold text-primary">{{ __('Our Services') }}</p>
                <h1 class="display-5 mb-5">{{ __('Services That We Offer For You') }}</h1>
            </div>
            <div class="row g-4">
              @foreach($services as $service)
                <div class="col-lg-4 col-md-6 wow fadeInUp" data-wow-delay="0.1s">
                    <div class="service-item rounded d-flex h-100 position-relative">

                        <!-- 💡 Pricing Badge Added -->
                        @if(!empty($service->price))
                            <div class="service-price-badge">
                                {{ money($service->price) }}
                            </div>
                        @endif

                        <div class="service-img rounded">
                            <img loading="lazy" decoding="async" class="img-fluid" src="{{ asset($service->image_url) }}" alt="{{ $service->title }}">
                        </div>
                        <div class="service-text rounded p-5">
                            <div class="btn-square rounded-circle mx-auto mb-3">
                                <img loading="lazy" decoding="async" class="img-fluid" src="{{ asset($service->icon_url) }}" alt="Icon">
                            </div>
                            <h4 class="mb-2"><a href="{{ $service->slug ? route('service.show', $service->slug) : route('service') }}" class="text-dark">{{ $service->title }}</a></h4>

                            <!-- 💡 Optional Price Sub-display -->
                            @if(!empty($service->price))
                                <p class="text-primary fw-bold mb-3">{{ money($service->price) }}</p>
                            @endif

                            <p class="mb-4">{{ $service->description }}</p>
                            <a class="btn btn-sm" href="{{ route('quote', ['service_id' => $service->id]) }}">
                                <i class="fa fa-plus text-primary me-2"></i>{{ __('Book Now') }}
                            </a>
                        @if($service->slug)<a class="btn btn-sm" href="{{ route('service.show', $service->slug) }}"><i class="fa fa-info-circle text-primary me-2"></i>{{ __('Details') }}</a>@endif
                        </div>
                    </div>
                </div>
             @endforeach
            </div>
        </div>
    </div>
    <!-- Service End -->



    <!-- Quote Start -->
    <div class="container-fluid quote my-5 py-5" data-parallax="scroll" data-image-src="{{ asset('site/img/carousel-2.jpg') }}">
        <div class="container py-5">
            <div class="row justify-content-center">
                <div class="col-lg-7">
                    <div class="bg-white rounded p-4 p-sm-5 wow fadeIn" data-wow-delay="0.5s">
                        <h1 class="display-5 text-center mb-3">{{ __('Request A Quote') }}</h1>
                        <p class="fs-5 fw-bold text-primary text-center mb-4">{{ __('Please complete and submit the form below to get started.') }}</p>

                        @include('common.site.booking-form', ['formId' => 'home'])
                    </div>
                </div>
            </div>
        </div>
    </div>
    <!-- Quote End -->


    <!-- Projects Start -->
    <div class="container-xxl py-5">
        <div class="container">
            <div class="text-center mx-auto wow fadeInUp" data-wow-delay="0.1s" style="max-width: 500px;">
                <p class="fs-5 fw-bold text-primary">{{ __('Our Projects') }}</p>
                <h1 class="display-5 mb-5">{{ __('Some Of Our Wonderful Projects') }}</h1>
            </div>
            @include('common.site.projects-grid', ['limit' => 3])
        </div>
    </div>
    <!-- Projects End -->


    <!-- Team Start -->
    <div class="container-xxl py-5">
        <div class="container">
            <div class="text-center mx-auto wow fadeInUp" data-wow-delay="0.1s" style="max-width: 500px;">
                <p class="fs-5 fw-bold text-primary">{{ __('Our Team') }}</p>
                <h1 class="display-5 mb-5">{{ __('Dedicated & Experienced Team Members') }}</h1>
            </div>
            <div class="row g-4">
                @forelse($team as $member)
                <div class="col-lg-4 col-md-6 wow fadeInUp" data-wow-delay="0.1s">
                    <div class="team-item rounded overflow-hidden shadow-sm h-100 bg-white">
                        <img loading="lazy" decoding="async" class="team-member-img" src="{{ asset($member->image_url) }}" alt="{{ $member->title }}">
                        <div class="team-text">
                            <h4 class="mb-2">{{ $member->title }}</h4>
                            <p class="text-primary mb-3">{{ $member->position }}</p>
                            <div class="team-social d-flex">
                                <a class="btn btn-square rounded-circle me-2" href="#"><i class="fab fa-facebook-f"></i></a>
                                <a class="btn btn-square rounded-circle me-2" href="#"><i class="fab fa-twitter"></i></a>
                                <a class="btn btn-square rounded-circle me-2" href="#"><i class="fab fa-instagram"></i></a>
                            </div>
                        </div>
                    </div>
                </div>
                @empty
                <div class="col-12">
                    <div class="text-center py-5">
                        <p class="mb-0 text-muted">{{ __('No team members available at the moment.') }}</p>
                    </div>
                </div>
                @endforelse
            </div>
        </div>
    </div>
    <!-- Team End -->


    <!-- Testimonial Start -->
    <div class="container-xxl py-5">
        <div class="container">
            <div class="row g-5">
                <div class="col-lg-5 wow fadeInUp" data-wow-delay="0.1s">
                    <p class="fs-5 fw-bold text-primary">{{ __('Testimonial') }}</p>
                    <h1 class="display-5 mb-5">{{ __('What Our Clients Say About Us!') }}</h1>
                    <p class="mb-4">{{ __('Real stories from real homeowners. Discover how our passion for landscaping and premium garden care has helped hundreds of clients create their dream outdoor sanctuaries.') }}</p>
                    <a class="btn btn-primary py-3 px-4" href="{{ route('quote') }}">{{ __('Book A Service') }}</a>
                </div>
                <div class="col-lg-7 wow fadeInUp" data-wow-delay="0.5s">
                    @include('common.site.testimonials')
                </div>
            </div>
        </div>
    </div>
    <!-- Testimonial End -->


    <!-- Footer Start -->
    @include('common.site.footer')
    <!-- Footer End -->


    <!-- Back to Top -->
    <a href="#" class="btn btn-lg btn-primary btn-lg-square rounded-circle back-to-top"><i class="bi bi-arrow-up"></i></a>


    <!-- JavaScript Libraries -->
    <script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.0.0/dist/js/bootstrap.bundle.min.js"></script>
    <script src="{{ asset('site/lib/wow/wow.min.js') }}"></script>
    <script src="{{ asset('site/lib/easing/easing.min.js') }}"></script>
    <script src="{{ asset('site/lib/waypoints/waypoints.min.js') }}"></script>
    <script src="{{ asset('site/lib/owlcarousel/owl.carousel.min.js') }}"></script>
    <script src="{{ asset('site/lib/counterup/counterup.min.js') }}"></script>
    <script src="{{ asset('site/lib/parallax/parallax.min.js') }}"></script>
    <script src="{{ asset('site/lib/isotope/isotope.pkgd.min.js') }}"></script>
    <script src="{{ asset('site/lib/lightbox/js/lightbox.min.js') }}"></script>

    <!-- Template Javascript -->
    <script src="{{ asset('site/js/main.js') }}"></script>

    @include('common.site.booking-form-script')

    @include('sweetalert::alert')
</body>

</html>
