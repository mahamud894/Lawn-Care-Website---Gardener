<!DOCTYPE html>
<html lang="{{ app()->getLocale() }}">
<head>
    <meta charset="utf-8">
    <title>{{ __('My Jobs') }} - Gardener</title>
    <meta content="width=device-width, initial-scale=1.0" name="viewport">
    <link href="{{ asset('site/css/bootstrap.min.css') }}" rel="stylesheet">
    <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/5.10.0/css/all.min.css" rel="stylesheet">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.4.1/font/bootstrap-icons.css" rel="stylesheet">
    <link href="{{ asset('site/css/style.css') }}" rel="stylesheet">
    <style>
        body { background-color: #f4f7f6; }
        .dashboard-header {
            background: linear-gradient(135deg, #348E38 0%, #1e5621 100%);
            border-radius: 15px; padding: 26px; color: white;
        }
        .job-card { border: none; border-radius: 14px; box-shadow: 0 5px 15px rgba(0,0,0,0.05); }
        .job-card .slot { font-weight: 700; color: #1e5621; }
        .job-photo { width: 64px; height: 64px; object-fit: cover; border-radius: 8px; }
        .section-title { font-weight: 700; margin: 28px 0 14px; }
    </style>
</head>
<body>
    <div class="container-fluid position-relative p-0">
        @include('common.site.navbar')
    </div>

    <div class="container py-5 mt-5" style="max-width: 900px;">
        <div class="dashboard-header mb-4">
            <h3 class="text-white fw-bold mb-1">{{ __('Hello') }}, {{ $member->title }}! 🌿</h3>
            <p class="mb-0 text-white-50">{{ __('Here are the jobs assigned to you.') }}</p>
        </div>

        <div class="row g-3 text-center mb-2">
            <div class="col-4"><div class="card job-card p-3"><div class="fs-3 fw-bold text-success">{{ $todayJobs->count() }}</div><small class="text-muted">{{ __('Today') }}</small></div></div>
            <div class="col-4"><div class="card job-card p-3"><div class="fs-3 fw-bold text-info">{{ $upcomingJobs->count() }}</div><small class="text-muted">{{ __('Upcoming') }}</small></div></div>
            <div class="col-4"><div class="card job-card p-3"><div class="fs-3 fw-bold text-secondary">{{ $doneJobs->count() }}</div><small class="text-muted">{{ __('Recently done') }}</small></div></div>
        </div>

        @foreach([
            ['title' => __('Overdue — please update'), 'jobs' => $overdueJobs, 'icon' => 'fa-exclamation-triangle text-danger', 'hideEmpty' => true],
            ['title' => __('Today\'s Jobs'), 'jobs' => $todayJobs, 'icon' => 'fa-sun text-warning', 'hideEmpty' => false],
            ['title' => __('Upcoming Jobs'), 'jobs' => $upcomingJobs, 'icon' => 'fa-calendar-alt text-info', 'hideEmpty' => false],
        ] as $section)
            @if($section['jobs']->isNotEmpty() || ! $section['hideEmpty'])
            <h5 class="section-title"><i class="fa {{ $section['icon'] }} me-2"></i>{{ $section['title'] }}</h5>

            @forelse($section['jobs'] as $job)
                <div class="card job-card p-3 mb-3">
                    <div class="d-flex justify-content-between flex-wrap gap-2">
                        <div>
                            <div class="slot">
                                {{ \Illuminate\Support\Carbon::parse($job->preferred_date)->translatedFormat('D, d M') }} · {{ $job->time_slot }}
                            </div>
                            <h5 class="mb-1">{{ $job->service?->title ?? __('Gardening job') }}</h5>
                            <div class="text-muted small">#{{ $job->id }} · {{ ucfirst($job->status) }}
                                @if($job->lawn_size) · {{ number_format($job->lawn_size) }} sq ft @endif
                            </div>
                        </div>
                        <div class="text-end">
                            <div class="fw-bold">{{ money($job->amountDue()) }}</div>
                            @if($job->isPaid())
                                <span class="badge bg-success">{{ __('Paid') }}</span>
                            @else
                                <span class="badge bg-warning text-dark">{{ __('Collect payment') }}</span>
                            @endif
                        </div>
                    </div>

                    <hr class="my-2">

                    <div class="small">
                        <div><i class="fa fa-user me-2 text-muted"></i>{{ $job->name }}</div>
                        @if($job->phone)
                            <div><i class="fa fa-phone me-2 text-muted"></i><a href="tel:{{ $job->phone }}">{{ $job->phone }}</a></div>
                        @endif
                        <div>
                            <i class="fa fa-map-marker-alt me-2 text-muted"></i>{{ $job->address }}
                            <a href="https://www.google.com/maps/search/?api=1&query={{ urlencode($job->address) }}" target="_blank" rel="noopener" class="ms-1">{{ __('Open map') }}</a>
                        </div>
                        @if($job->message)
                            <div class="mt-1"><i class="fa fa-comment me-2 text-muted"></i>{{ $job->message }}</div>
                        @endif
                    </div>

                    @if(!empty($job->photos))
                        <div class="d-flex gap-2 mt-2">
                            @foreach($job->photos as $photo)
                                <a href="{{ asset($photo) }}" target="_blank"><img loading="lazy" decoding="async" src="{{ asset($photo) }}" class="job-photo" alt="Lawn photo"></a>
                            @endforeach
                        </div>
                    @endif

                    @if($job->status === 'approved')
                        <form action="{{ route('gardener.jobs.done', $job) }}" method="POST" class="mt-3 d-flex flex-wrap align-items-center gap-3"
                              onsubmit="return confirm('{{ __('Mark this job as done?') }}')">
                            @csrf
                            @unless($job->isPaid())
                                <div class="form-check mb-0">
                                    <input type="checkbox" name="cash_collected" value="1" id="cash{{ $job->id }}" class="form-check-input">
                                    <label for="cash{{ $job->id }}" class="form-check-label small">{{ __('I collected the cash payment') }}</label>
                                </div>
                            @endunless
                            <button type="submit" class="btn btn-success rounded-pill px-4"><i class="fa fa-check me-1"></i> {{ __('Mark as done') }}</button>
                        </form>
                    @else
                        <div class="small text-muted mt-2"><i class="fa fa-info-circle me-1"></i>{{ __('Waiting for admin approval.') }}</div>
                    @endif
                </div>
            @empty
                <p class="text-muted">{{ __('No jobs here.') }}</p>
            @endforelse
            @endif
        @endforeach

        <h5 class="section-title"><i class="fa fa-history text-success me-2"></i>{{ __('Recently done') }}</h5>
        <div class="card job-card p-0">
            <ul class="list-group list-group-flush">
                @forelse($doneJobs as $job)
                    <li class="list-group-item d-flex justify-content-between small">
                        <span>{{ \Illuminate\Support\Carbon::parse($job->preferred_date)->format('d M') }} · {{ $job->service?->title }} · {{ $job->name }}</span>
                        <span class="text-success"><i class="fa fa-check"></i></span>
                    </li>
                @empty
                    <li class="list-group-item text-muted small">{{ __('No finished jobs yet.') }}</li>
                @endforelse
            </ul>
        </div>
    </div>

    @include('common.site.footer')
    <script src="https://code.jquery.com/jquery-3.4.1.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.0.0/dist/js/bootstrap.bundle.min.js"></script>
    @include('sweetalert::alert')
</body>
</html>
