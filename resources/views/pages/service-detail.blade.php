@extends('layouts.site')

@section('title', $service->title)
@section('description', \Illuminate\Support\Str::limit(strip_tags($service->description), 155))
@section('header', $service->title)
@section('breadcrumb')
    <li class="breadcrumb-item"><a href="{{ route('service') }}">{{ __('Services') }}</a></li>
@endsection

@section('content')
<div class="container-xxl py-5">
    <div class="container">
        <div class="row g-5">
            <div class="col-lg-7 wow fadeInUp" data-wow-delay="0.1s">
                @if($service->image_url)
                    <img src="{{ asset($service->image_url) }}" alt="{{ $service->title }}" class="img-fluid rounded mb-4 w-100" style="max-height: 420px; object-fit: cover;">
                @endif
                <p class="fs-5 mb-4">{{ $service->description }}</p>
                @if($service->long_description)
                    <div class="mb-4" style="white-space: pre-line;">{{ $service->long_description }}</div>
                @endif
            </div>

            <div class="col-lg-5 wow fadeInUp" data-wow-delay="0.3s">
                <div class="bg-light rounded p-4 p-sm-5 position-sticky" style="top: 100px;">
                    <h4 class="mb-3">{{ __('Pricing') }}</h4>
                    @if($service->price)
                        <p class="mb-1">{{ __('Starting from') }}</p>
                        <h2 class="text-primary mb-3">{{ money($service->price) }}</h2>
                    @else
                        <p class="text-muted">{{ __('Price on request') }}</p>
                    @endif

                    @if($service->price_per_100_sqft)
                        <p class="small text-muted mb-2">+ {{ money($service->price_per_100_sqft) }} {{ __('per 100 sq ft') }}</p>
                        <label class="form-label small fw-bold" for="calcSize">{{ __('Your lawn size (sq ft)') }}</label>
                        <input type="number" id="calcSize" min="50" step="10" class="form-control mb-2" placeholder="e.g. 1000">
                        <div class="mb-3">{{ __('Estimated price') }}: <strong id="calcResult" class="text-primary">{{ money($service->price) }}</strong></div>
                    @endif

                    <a href="{{ route('quote', ['service_id' => $service->id]) }}" class="btn btn-primary w-100 py-3">
                        <i class="fa fa-calendar-check me-2"></i>{{ __('Book This Service') }}
                    </a>
                    @if(config('gardener.whatsapp_number'))
                        <a href="https://wa.me/{{ config('gardener.whatsapp_number') }}?text={{ rawurlencode(__('Hi! I have a question about').' '.$service->title) }}" target="_blank" rel="noopener" class="btn btn-outline-success w-100 py-2 mt-2">
                            <i class="fab fa-whatsapp me-2"></i>{{ __('Ask on WhatsApp') }}
                        </a>
                    @endif
                </div>
            </div>
        </div>

        @if($others->isNotEmpty())
            <h3 class="mt-5 mb-4">{{ __('Other Services') }}</h3>
            <div class="row g-4">
                @foreach($others as $other)
                    <div class="col-md-4">
                        <a href="{{ route('service.show', $other->slug) }}" class="text-decoration-none">
                            <div class="bg-light rounded overflow-hidden h-100">
                                @if($other->image_url)<img src="{{ asset($other->image_url) }}" class="img-fluid w-100" style="height: 180px; object-fit: cover;" alt="{{ $other->title }}">@endif
                                <div class="p-3">
                                    <h5 class="mb-1">{{ $other->title }}</h5>
                                    @if($other->price)<span class="text-primary fw-bold">{{ money($other->price) }}</span>@endif
                                </div>
                            </div>
                        </a>
                    </div>
                @endforeach
            </div>
        @endif
    </div>
</div>
@endsection

@push('scripts')
@if($service->price_per_100_sqft)
<script>
    $('#calcSize').on('input', function () {
        var size = parseInt(this.value, 10) || 0;
        var total = {{ (float) $service->price }} + size / 100 * {{ (float) $service->price_per_100_sqft }};
        $('#calcResult').text(@json(config('gardener.currency_symbol')) + total.toLocaleString(undefined, { minimumFractionDigits: 2, maximumFractionDigits: 2 }));
    });
</script>
@endif
@endpush
