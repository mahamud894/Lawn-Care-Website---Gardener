<div class="container-fluid bg-dark text-light footer mt-5 py-5 wow fadeIn" data-wow-delay="0.1s">
        <div class="container py-5">
            <div class="row g-5">
                <div class="col-lg-4 col-md-6">
                    <h4 class="text-white mb-4">{{ __('My Location') }}</h4>
                    <p class="mb-2"><i class="fa fa-map-marker-alt me-3"></i>192 Mirzapur, Kalia, Narail</p>
                    <p class="mb-2"><i class="fa fa-phone-alt me-3"></i><a class="text-light" href="tel:+8801846650807">+880 1846650807</a></p>
                    <p class="mb-2"><i class="fa fa-envelope me-3"></i><a class="text-light" href="mailto:mdmahamudul894@gmail.com">mdmahamudul894@gmail.com</a></p>
                    <div class="d-flex pt-2">
                        @if(config('gardener.whatsapp_number'))
                            <a class="btn btn-square btn-outline-light rounded-circle me-2" href="https://wa.me/{{ config('gardener.whatsapp_number') }}" target="_blank" rel="noopener" title="WhatsApp"><i class="fab fa-whatsapp"></i></a>
                        @endif
                        <a class="btn btn-square btn-outline-light rounded-circle me-2" href="mailto:mdmahamudul894@gmail.com" title="E-mail"><i class="fa fa-envelope"></i></a>
                    </div>
                </div>
                <div class="col-lg-4 col-md-6">
                    <h4 class="text-white mb-4">{{ __('Services') }}</h4>
                    @forelse($footerServices ?? [] as $fs)
                        <a class="btn btn-link" href="{{ $fs->slug ? route('service.show', $fs->slug) : route('service') }}">{{ $fs->title }}</a>
                    @empty
                        <a class="btn btn-link" href="{{ route('service') }}">{{ __('Our Services') }}</a>
                    @endforelse
                </div>
                <div class="col-lg-4 col-md-6">
                    <h4 class="text-white mb-4">{{ __('Quick Links') }}</h4>
                    <a class="btn btn-link" href="{{ route('about') }}">{{ __('About Us') }}</a>
                    <a class="btn btn-link" href="{{ route('contact') }}">{{ __('Contact Us') }}</a>
                    <a class="btn btn-link" href="{{ route('service') }}">{{ __('Our Services') }}</a>
                    <a class="btn btn-link" href="{{ route('blog') }}">{{ __('Blog') }}</a>
                    <a class="btn btn-link" href="{{ route('faq') }}">{{ __('FAQ') }}</a>
                </div>

            </div>
        </div>
    </div>

<!-- Copyright Start -->
    <div class="container-fluid copyright py-4">
        <div class="container">
            <div class="row">
                <div class="col-md-6 text-center text-md-start mb-3 mb-md-0">
                    &copy; <a class="border-bottom" href="{{ route('home') }}">Gardener</a>, {{ __('All Right Reserved.') }}
                </div>
                <div class="col-md-6 text-center text-md-end">
                    <!--/*** This template is free as long as you keep the footer author’s credit link/attribution link/backlink. If you'd like to use the template without the footer author’s credit link/attribution link/backlink, you can purchase the Credit Removal License from "https://htmlcodex.com/credit-removal". Thank you for your support. ***/-->
                    {{ __('Designed By') }} <a class="border-bottom" href="">Mahamud Hassan</a>
                </div>
            </div>
        </div>
    </div>
 <!-- Copyright End -->

@if(config('gardener.whatsapp_number'))
    {{-- Floating WhatsApp chat button --}}
    <a href="https://wa.me/{{ config('gardener.whatsapp_number') }}?text={{ rawurlencode(__('Hi! I would like to know about your gardening services.')) }}"
       target="_blank" rel="noopener" title="{{ __('Chat on WhatsApp') }}"
       style="position: fixed; left: 20px; bottom: 20px; z-index: 1050; width: 56px; height: 56px; border-radius: 50%; background: #25D366; color: #fff; display: flex; align-items: center; justify-content: center; font-size: 30px; box-shadow: 0 4px 12px rgba(0,0,0,.25);">
        <i class="fab fa-whatsapp"></i>
    </a>
@endif
