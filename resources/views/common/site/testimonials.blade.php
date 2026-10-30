{{--
    Testimonial carousel. Shows customer reviews the admin has approved;
    falls back to the original sample testimonials when there are none yet.
--}}
<div class="owl-carousel testimonial-carousel">
    @forelse(($reviews ?? collect()) as $review)
        <div class="testimonial-item">
            <div class="mb-3 text-warning">
                @for($s = 1; $s <= 5; $s++)
                    <i class="fa{{ $s <= $review->rating ? 's' : 'r' }} fa-star"></i>
                @endfor
            </div>
            <p class="fs-5">"{{ $review->comment }}"</p>
            <h4>{{ $review->user->name ?? 'Customer' }}</h4>
            <span>{{ $review->booking->service->title ?? 'Customer' }}</span>
        </div>
    @empty
        <div class="testimonial-item">
            <img loading="lazy" decoding="async" class="img-fluid rounded mb-3" src="{{ asset('site/img/testimonial-1.jpg') }}" alt="">
            <p class="fs-5">{{ __('"Excellent service from start to finish. They fixed our broken lawn drainage system and designed a stunning modern garden layout. Highly recommended!"') }}</p>
            <h4>{{ __('Sarah Jenkins') }}</h4>
            <span>{{ __('Villa Owner') }}</span>
        </div>
        <div class="testimonial-item">
            <img loading="lazy" decoding="async" class="img-fluid rounded mb-3" src="{{ asset('site/img/testimonial-2.jpg') }}" alt="">
            <p class="fs-5">{{ __('"Gardener completely transformed our boring backyard into a lush green paradise! Their team was fast, incredibly professional, and very transparent about prices. Our family absolutely loves our new space!"') }}</p>
            <h4>{{ __('John Doe') }}</h4>
            <span>{{ __('Homeowner') }}</span>
        </div>
    @endforelse
</div>
