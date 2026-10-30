@extends('layouts.site')

@section('title', __('FAQ'))
@section('description', __('Answers to common questions about our lawn care and gardening services.'))
@section('header', __('Frequently Asked Questions'))

@section('content')
<div class="container-xxl py-5">
    <div class="container" style="max-width: 860px;">
        @if($faqs->isEmpty())
            <p class="text-center text-muted">{{ __('No questions yet.') }}</p>
        @else
            <div class="accordion" id="faqAccordion">
                @foreach($faqs as $faq)
                    <div class="accordion-item mb-2 border rounded">
                        <h2 class="accordion-header" id="q{{ $faq->id }}">
                            <button class="accordion-button {{ $loop->first ? '' : 'collapsed' }} fw-bold" type="button" data-bs-toggle="collapse" data-bs-target="#a{{ $faq->id }}">
                                {{ $faq->question }}
                            </button>
                        </h2>
                        <div id="a{{ $faq->id }}" class="accordion-collapse collapse {{ $loop->first ? 'show' : '' }}" data-bs-parent="#faqAccordion">
                            <div class="accordion-body" style="white-space: pre-line;">{{ $faq->answer }}</div>
                        </div>
                    </div>
                @endforeach
            </div>
        @endif

        <div class="text-center mt-5">
            <p class="mb-3">{{ __('Still have a question?') }}</p>
            <a href="{{ route('contact') }}" class="btn btn-primary py-2 px-4 me-2">{{ __('Contact Us') }}</a>
            @if(config('gardener.whatsapp_number'))
                <a href="https://wa.me/{{ config('gardener.whatsapp_number') }}" target="_blank" rel="noopener" class="btn btn-outline-success py-2 px-4"><i class="fab fa-whatsapp me-1"></i> WhatsApp</a>
            @endif
        </div>
    </div>
</div>
@endsection

@push('scripts')
{{-- Search engines can show these answers directly in results --}}
<script type="application/ld+json">
{!! json_encode([
    '@context' => 'https://schema.org',
    '@type' => 'FAQPage',
    'mainEntity' => $faqs->map(fn ($f) => [
        '@type' => 'Question',
        'name' => $f->question,
        'acceptedAnswer' => ['@type' => 'Answer', 'text' => $f->answer],
    ])->values(),
], JSON_UNESCAPED_UNICODE | JSON_UNESCAPED_SLASHES | JSON_HEX_TAG) !!}
</script>
@endpush
