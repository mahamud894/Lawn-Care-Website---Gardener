<!-- Service Start -->

    <div class="container-xxl py-5">
        <div class="container">
            <div class="text-center mx-auto wow fadeInUp" data-wow-delay="0.1s" style="max-width: 500px;">
                <p class="fs-5 fw-bold text-primary">Our Services</p>
                <h1 class="display-5 mb-5">Services That We Offer For You</h1>
            </div>
            <div class="row g-4">
              @foreach($services as $service)
                <div class="col-lg-4 col-md-6 wow fadeInUp" data-wow-delay="0.1s">
                    <div class="service-item rounded d-flex h-100">
                        <div class="service-img rounded">
                            <img class="img-fluid" src="{{$service->image_url}}" alt="">
                        </div>
                        <div class="service-text rounded p-5">
                            <div class="btn-square rounded-circle mx-auto mb-3">
                                <img class="img-fluid" src="{{$service->icon_url}}" alt="Icon">
                            </div>
                            <h4 class="mb-3">{{$service->title}}</h4>
                            <p class="mb-4">{{$service->description}}</p>
                            <a class="btn btn-sm" href="{{ route('quote', ['service_id' => $service->id]) }}">
                                 <i class="fa fa-plus text-primary me-2"></i>Read More
                            </a>
                        </div>
                    </div>

                </div>
             @endforeach
            </div>
        </div>
    </div>
    <!-- Service End -->
