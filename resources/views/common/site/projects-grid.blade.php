{{--
    Project gallery used on the Home and Projects pages.
    Uses projects from the admin panel; if none have been added yet,
    falls back to the original sample images so the page never looks empty.
--}}
@php
    $projectItems = ($projects ?? collect())->map(fn ($p) => [
        'title' => $p->title,
        'image' => asset($p->image_url),
        'filter' => $p->category === 'ongoing' ? 'second' : 'first',
    ]);

    if ($projectItems->isEmpty()) {
        $projectItems = collect([
            ['title' => 'Landscaping', 'image' => asset('site/img/service-1.jpg'), 'filter' => 'first'],
            ['title' => 'Pruning plants', 'image' => asset('site/img/service-2.jpg'), 'filter' => 'second'],
            ['title' => 'Irrigation & Drainage', 'image' => asset('site/img/service-3.jpg'), 'filter' => 'first'],
            ['title' => 'Garden Maintenance', 'image' => asset('site/img/service-4.jpg'), 'filter' => 'second'],
            ['title' => 'Green Technology', 'image' => asset('site/img/service-5.jpg'), 'filter' => 'first'],
            ['title' => 'Urban Gardening', 'image' => asset('site/img/service-6.jpg'), 'filter' => 'second'],
        ]);
    }

    $projectItems = isset($limit) ? $projectItems->take($limit) : $projectItems;
@endphp

<div class="row wow fadeInUp" data-wow-delay="0.3s">
    <div class="col-12 text-center">
        <ul class="list-inline rounded mb-5" id="portfolio-flters">
            <li class="mx-2 active" data-filter="*">All</li>
            <li class="mx-2" data-filter=".first">Complete Projects</li>
            <li class="mx-2" data-filter=".second">Ongoing Projects</li>
        </ul>
    </div>
</div>
<div class="row g-4 portfolio-container">
    @foreach($projectItems as $i => $item)
    <div class="col-lg-4 col-md-6 portfolio-item {{ $item['filter'] }} wow fadeInUp" data-wow-delay="{{ 0.1 + ($i % 3) * 0.2 }}s">
        <div class="portfolio-inner rounded">
            <img class="img-fluid" src="{{ $item['image'] }}" alt="{{ $item['title'] }}">
            <div class="portfolio-text">
                <h4 class="text-white mb-4">{{ $item['title'] }}</h4>
                <div class="d-flex">
                    <a class="btn btn-lg-square rounded-circle mx-2" href="{{ $item['image'] }}" data-lightbox="portfolio" data-title="{{ $item['title'] }}"><i class="fa fa-eye"></i></a>
                    <a class="btn btn-lg-square rounded-circle mx-2" href="{{ route('quote') }}" title="Get a quote"><i class="fa fa-link"></i></a>
                </div>
            </div>
        </div>
    </div>
    @endforeach
</div>
