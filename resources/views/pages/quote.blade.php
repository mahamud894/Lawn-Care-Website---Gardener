<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="utf-8">
    <title>Gardener - Gardening Website Template</title>
    <meta content="width=device-width, initial-scale=1.0" name="viewport">
    <meta content="" name="keywords">
    <meta content="" name="description">

    <!-- Favicon -->
    <link href="site/img/favicon.ico" rel="icon">

    <!-- Google Web Fonts -->
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
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
                    <span>Follow Us:</span>
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


    <!-- Page Header Start -->
    <div class="container-fluid page-header py-5 wow fadeIn" data-wow-delay="0.1s">
        <div class="container text-center py-5">
            <h1 class="display-3 text-white mb-4 animated slideInDown">Get A Quote</h1>
            <nav aria-label="breadcrumb animated slideInDown">
                <ol class="breadcrumb justify-content-center mb-0">
                    <li class="breadcrumb-item"><a href="#">Home</a></li>
                    <li class="breadcrumb-item"><a href="#">Pages</a></li>
                    <li class="breadcrumb-item active" aria-current="page">Get A Quote</li>
                </ol>
            </nav>
        </div>
    </div>
    <!-- Page Header End -->


    <!-- Quote Start -->
    <div class="container-fluid quote py-5" data-parallax="scroll" >
        <div class="container py-5">
            <div class="row justify-content-center">
                <div class="col-lg-7">
                    <div class="bg-white rounded p-4 p-sm-5 wow fadeIn" data-wow-delay="0.5s">
                        <h1 class="display-5 text-center mb-3">Request A Quote</h1>
                        <p class="fs-5 fw-bold text-primary text-center mb-5">Please complete and submit the form below to get started.</p>

                        @if ($errors->any())
                        <div class="alert alert-danger">
                            <ul>
                                @foreach ($errors->all() as $error)
                                <li>{{ $error }}</li>
                                @endforeach
                            </ul>
                        </div>
                        @endif

                        <form action="/admin-panel/create-booking" method="post">
                            @csrf
                            <div class="row g-3">
                                <div class="col-xl-12">
                                    <input name="name" value="{{old("name")}}" type="text" class="form-control bg-light border-0" placeholder="Your Name" style="height: 55px;" required>
                                </div>
                                <div class="col-12">
                                    <input name="email" type="email" class="form-control bg-light border-0" placeholder="Your Email" style="height: 55px;" required>
                                </div>
                                <div class="col-12">
                                    <select name="service" class="form-select bg-light border-0" style="height: 55px;" required>
                                        <option value="" selected disabled>Select A Service</option>
                                        @foreach($services as $service)
                                        <option value="{{$service->id}}" {{ $selectedServiceId == $service->id ? 'selected' : '' }}>
                                            {{$service->title}}
                                        </option>
                                        @endforeach
                                    </select>
                                </div>

                                <div class="col-12">
                                    <input name="preferred_date" id="booking_date" type="date" class="form-control bg-light border-0" style="height: 55px;" required>
                                </div>

                                {{-- 💡 থিম লেআউটের সাথে মিল রেখে বক্সটির গ্রিড স্ট্রাকচার ও ইনপুট ক্লাস পারফেক্ট করা হলো --}}
                                <div class="col-12">
                                    <select id="time_slot" name="time_slot" class="form-control bg-light border-0" style="height: 55px; color: #6c757d; background-color: #f8f9fa !important;" required disabled>
                                        <option value="">First select a date</option>
                                    </select>
                                </div>

                                <div class="col-12">
                                    <textarea name="message" class="form-control bg-light border-0" rows="3" placeholder="Message"></textarea>
                                </div>
                                <div class="col-12">
                                    @if(Auth::user())
                                    <button class="btn btn-dark w-100 py-3" type="submit">Submit</button>
                                    @else
                                    <a href="/login" class="btn btn-dark w-100 py-3">Submit</a>
                                    @endif
                                </div>
                            </div>
                        </form>
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

    <script>
        $('#booking_date').on('change', function() {
            var date = $(this).val();
            if(date) {
                // স্লট লোড হওয়ার সময় টেক্সটের কালার পরিবর্তন
                $('#time_slot').html('<option value="">Loading slots...</option>').prop('disabled', true).css('color', '#6c757d');

                $.ajax({
                    url: '/check-available-slots?date=' + date,
                    type: 'GET',
                    success: function(bookedSlots) {
                        var allSlots = [
                            "09:00 AM - 11:00 AM",
                            "11:00 AM - 01:00 PM",
                            "02:00 PM - 04:00 PM",
                            "04:00 PM - 06:00 PM"
                        ];

                        var html = '<option value="">Select a Time Slot</option>';
                        allSlots.forEach(function(slot) {
                            if (!bookedSlots.includes(slot)) {
                                html += '<option value="' + slot + '">' + slot + '</option>';
                            }
                        });

                        // স্লট একটিভ হলে টেক্সটের কালার ডার্ক হবে
                        $('#time_slot').html(html).prop('disabled', false).css('color', '#212529');
                    },
                    error: function() {
                        $('#time_slot').html('<option value="">Error loading slots</option>');
                    }
                });
            }
        });
    </script>
</body>
</html>
