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
                <div class="service-item rounded d-flex h-100 position-relative">

                    <!-- 💡 Price Badge (পিকচারের ওপর সুন্দর ব্যাজ দেখাবে) -->
                    @if(!empty($service->price))
                        <div class="service-price-badge" style="position: absolute; top: 15px; right: 15px; background: #348E38; color: #ffffff; font-weight: 700; padding: 6px 14px; border-radius: 20px; font-size: 0.95rem; box-shadow: 0 4px 10px rgba(0,0,0,0.15); z-index: 2;">
                            ${{ number_format($service->price, 2) }}
                        </div>
                    @endif

                    <div class="service-img rounded">
                        <img class="img-fluid" src="{{ asset($service->image_url) }}" alt="{{ $service->title }}">
                    </div>
                    <div class="service-text rounded p-5">
                        <div class="btn-square rounded-circle mx-auto mb-3">
                            <img class="img-fluid" src="{{ asset($service->icon_url) }}" alt="Icon">
                        </div>
                        <h4 class="mb-2">{{ $service->title }}</h4>

                        <!-- 💡 টাইটেলের নিচেও প্রাইস দেখাবে -->
                        @if(!empty($service->price))
                            <p class="text-primary fw-bold mb-3">${{ number_format($service->price, 2) }}</p>
                        @endif

                        <p class="mb-4">{{ $service->description }}</p>
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
