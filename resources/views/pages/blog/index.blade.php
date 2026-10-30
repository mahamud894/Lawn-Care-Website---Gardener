@extends('layouts.site')

@section('title', __('Blog'))
@section('description', __('Lawn and garden care tips from the Gardener team.'))
@section('header', __('Blog'))

@section('content')
<div class="container-xxl py-5">
    <div class="container">
        <div class="text-center mx-auto mb-5" style="max-width: 600px;">
            <p class="fs-5 fw-bold text-primary">{{ __('Garden Tips') }}</p>
            <h1 class="display-6">{{ __('Lawn & Garden Care Articles') }}</h1>
        </div>
        <div class="row g-4">
            @forelse($posts as $post)
                <div class="col-lg-4 col-md-6 wow fadeInUp" data-wow-delay="0.1s">
                    <div class="bg-light rounded overflow-hidden h-100 d-flex flex-column">
                        <a href="{{ route('blog.show', $post) }}">
                            <img loading="lazy" decoding="async" src="{{ $post->image_url ? asset($post->image_url) : asset('site/img/about.jpg') }}" alt="{{ $post->title }}" class="img-fluid w-100" style="height: 220px; object-fit: cover;">
                        </a>
                        <div class="p-4 d-flex flex-column flex-grow-1">
                            <small class="text-muted mb-2"><i class="far fa-calendar-alt me-1"></i>{{ ($post->published_at ?? $post->created_at)->translatedFormat('d M Y') }}</small>
                            <h4 class="mb-3"><a href="{{ route('blog.show', $post) }}" class="text-dark">{{ $post->title }}</a></h4>
                            <p class="flex-grow-1">{{ $post->summary() }}</p>
                            <a href="{{ route('blog.show', $post) }}" class="text-primary fw-bold">{{ __('Read more') }} <i class="fa fa-arrow-right ms-1"></i></a>
                        </div>
                    </div>
                </div>
            @empty
                <p class="text-center text-muted">{{ __('No articles yet — check back soon!') }}</p>
            @endforelse
        </div>
        @if($posts instanceof \Illuminate\Contracts\Pagination\Paginator)
            <div class="mt-5 d-flex justify-content-center">{{ $posts->links() }}</div>
        @endif
    </div>
</div>
@endsection
