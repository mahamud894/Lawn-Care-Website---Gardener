@extends('layouts.site')

@section('title', $post->title)
@section('description', \Illuminate\Support\Str::limit($post->summary(), 155))
@section('header', $post->title)
@section('breadcrumb')
    <li class="breadcrumb-item"><a href="{{ route('blog') }}">{{ __('Blog') }}</a></li>
@endsection

@section('content')
<div class="container-xxl py-5">
    <div class="container" style="max-width: 860px;">
        <p class="text-muted"><i class="far fa-calendar-alt me-1"></i>{{ ($post->published_at ?? $post->created_at)->translatedFormat('d F Y') }}</p>
        @if($post->image_url)
            <img src="{{ asset($post->image_url) }}" alt="{{ $post->title }}" class="img-fluid rounded mb-4 w-100" style="max-height: 460px; object-fit: cover;">
        @endif

        {{-- Plain text with line breaks; links and HTML are not allowed for safety --}}
        <article class="fs-6 lh-lg" style="white-space: pre-line;">{{ $post->body }}</article>

        <div class="bg-light rounded p-4 mt-5 d-flex flex-wrap align-items-center justify-content-between gap-3">
            <div>
                <h5 class="mb-1">{{ __('Need help with your garden?') }}</h5>
                <p class="mb-0 text-muted">{{ __('Our gardeners can take care of it for you.') }}</p>
            </div>
            <a href="{{ route('quote') }}" class="btn btn-primary py-2 px-4">{{ __('Get A Quote') }}</a>
        </div>

        @if($recent->isNotEmpty())
            <h4 class="mt-5 mb-3">{{ __('More articles') }}</h4>
            <ul class="list-unstyled">
                @foreach($recent as $r)
                    <li class="mb-2"><a href="{{ route('blog.show', $r) }}"><i class="fa fa-leaf text-primary me-2"></i>{{ $r->title }}</a></li>
                @endforeach
            </ul>
        @endif
    </div>
</div>
@endsection
