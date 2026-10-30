<!DOCTYPE html>
<html lang="{{ app()->getLocale() }}">

<head>
    <meta charset="utf-8">
    <title>User Dashboard - Gardener</title>
    <meta content="width=device-width, initial-scale=1.0" name="viewport">

    <link href="/site/css/bootstrap.min.css" rel="stylesheet">
    <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/5.10.0/css/all.min.css" rel="stylesheet">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.4.1/font/bootstrap-icons.css" rel="stylesheet">
    <link href="/site/css/style.css" rel="stylesheet">
    <link href="{{ asset('site/css/motion.css') }}" rel="stylesheet">

    <style>
        body {
            background-color: #f4f7f6;
        }
        .dashboard-header {
            background: linear-gradient(135deg, #348E38 0%, #1e5621 100%);
            border-radius: 15px;
            padding: 30px;
            color: white;
            box-shadow: 0 4px 15px rgba(52, 142, 56, 0.2);
        }
        .dashboard-card {
            border: none;
            border-radius: 15px;
            box-shadow: 0 5px 15px rgba(0,0,0,0.04);
            transition: all 0.3s ease;
            background: #fff;
        }
        .dashboard-card:hover {
            transform: translateY(-3px);
            box-shadow: 0 8px 20px rgba(0,0,0,0.08);
        }
        .status-badge {
            padding: 6px 14px;
            border-radius: 30px;
            font-size: 0.85rem;
            font-weight: 600;
            text-transform: capitalize;
            display: inline-flex;
            align-items: center;
            gap: 5px;
        }

        .badge-pending { background-color: #fff3cd; color: #856404; border: 1px solid #ffeeba; }
        .badge-approved { background-color: #d1ecf1; color: #0c5460; border: 1px solid #bee5eb; }
        .badge-done { background-color: #d4edda; color: #155724; border: 1px solid #c3e6cb; }
        .badge-rejected { background-color: #f8d7da; color: #721c24; border: 1px solid #f5c6cb; }
        .badge-cancelled { background-color: #e2e3e5; color: #41464b; border: 1px solid #d3d6d8; }
        .star-picker { direction: rtl; display: inline-flex; gap: 4px; }
        .star-picker input { display: none; }
        .star-picker label { font-size: 1.8rem; color: #ccc; cursor: pointer; }
        .star-picker input:checked ~ label,
        .star-picker label:hover,
        .star-picker label:hover ~ label { color: #ffc107; }

        .nav-pills .nav-link {
            color: #555;
            font-weight: 600;
            border-radius: 8px;
            padding: 10px 20px;
            transition: all 0.2s;
        }
        .nav-pills .nav-link.active {
            background-color: #348E38;
            color: white;
            box-shadow: 0 4px 10px rgba(52, 142, 56, 0.25);
        }
        .icon-shape {
            width: 55px;
            height: 55px;
            display: flex;
            align-items: center;
            justify-content: center;
            border-radius: 12px;
        }
    </style>
</head>

<body>
    <div class="container-fluid position-relative p-0">
        @include('common.site.navbar')
    </div>

    <div class="container py-5 mt-5">
        <div class="dashboard-header mb-5">
            <div class="row align-items-center">
                <div class="col-md-8">
                    <h2 class="fw-bold text-white mb-1">{{ __('Welcome Back') }}, {{ Auth::user()->name }}!</h2>
                    <p class="mb-0 text-white-50">{{ __('Here you can track your lawn care requests, active services, and history.') }}</p>
                </div>
                <div class="col-md-4 text-md-end mt-3 mt-md-0">
                    <a href="{{ route('quote') }}" class="btn btn-light text-success fw-bold py-2 px-4 rounded-pill shadow-sm">
                        <i class="fa fa-plus me-2"></i>{{ __('Book A New Service') }}
                    </a>
                </div>
            </div>
        </div>

        @if(request('payment') === 'success')
            <div class="alert alert-success"><i class="fa fa-check-circle me-1"></i>{{ __('Payment successful — thank you!') }}</div>
        @elseif(in_array(request('payment'), ['failed', 'cancelled']))
            <div class="alert alert-warning"><i class="fa fa-exclamation-circle me-1"></i>{{ __('The payment was not completed. You can try again or pay in cash on the service day.') }}</div>
        @endif

        @if ($errors->any())
            <div class="alert alert-danger">
                <ul class="mb-0">
                    @foreach ($errors->all() as $error)
                        <li>{{ $error }}</li>
                    @endforeach
                </ul>
            </div>
        @endif

        <div class="row g-4 mb-5">
            <div class="col-md-4">
                <div class="card dashboard-card p-4 d-flex flex-row align-items-center justify-content-between">
                    <div>
                        <h6 class="text-muted text-uppercase small fw-bold mb-1">{{ __('Pending Requests') }}</h6>
                        <h2 class="fw-bold mb-0 text-dark">{{ $bookings->where('status', 'pending')->count() }}</h2>
                    </div>
                    <div class="icon-shape bg-warning bg-opacity-10 text-warning"><i class="fa fa-spinner fa-spin fa-2x"></i></div>
                </div>
            </div>
            <div class="col-md-4">
                <div class="card dashboard-card p-4 d-flex flex-row align-items-center justify-content-between">
                    <div>
                        <h6 class="text-muted text-uppercase small fw-bold mb-1">{{ __('Accepted / Active') }}</h6>
                        <h2 class="fw-bold mb-0 text-dark">{{ $bookings->where('status', 'approved')->count() }}</h2>
                    </div>
                    <div class="icon-shape bg-info bg-opacity-10 text-info"><i class="fa fa-check-circle fa-2x"></i></div>
                </div>
            </div>
            <div class="col-md-4">
                <div class="card dashboard-card p-4 d-flex flex-row align-items-center justify-content-between">
                    <div>
                        <h6 class="text-muted text-uppercase small fw-bold mb-1">{{ __('My Job History (Done)') }}</h6>
                        <h2 class="fw-bold mb-0 text-dark">{{ $bookings->where('status', 'done')->count() }}</h2>
                    </div>
                    <div class="icon-shape bg-success bg-opacity-10 text-success"><i class="fa fa-history fa-2x"></i></div>
                </div>
            </div>
        </div>

        <div class="bg-white p-3 rounded-3 shadow-sm mb-4">
            <ul class="nav nav-pills gap-2" id="dashboardTabs" role="tablist">
                <li class="nav-item" role="presentation">
                    <button class="nav-link active" id="all-tab" data-bs-toggle="tab" data-bs-target="#all" type="button" role="tab">{{ __('All Orders') }}</button>
                </li>
                <li class="nav-item" role="presentation">
                    <button class="nav-link" id="pending-tab" data-bs-toggle="tab" data-bs-target="#pending" type="button" role="tab">{{ __('Pending') }}</button>
                </li>
                <li class="nav-item" role="presentation">
                    <button class="nav-link" id="active-tab" data-bs-toggle="tab" data-bs-target="#active" type="button" role="tab">{{ __('Accepted') }}</button>
                </li>
                <li class="nav-item" role="presentation">
                    <button class="nav-link" id="history-tab" data-bs-toggle="tab" data-bs-target="#history" type="button" role="tab">{{ __('Job History (Done)') }}</button>
                </li>
            </ul>
        </div>

        <div class="tab-content" id="dashboardTabsContent">
            <div class="tab-pane fade show active" id="all" role="tabpanel">
                @include('pages.partials.booking-table', ['filteredBookings' => $bookings])
            </div>
            <div class="tab-pane fade" id="pending" role="tabpanel">
                @include('pages.partials.booking-table', ['filteredBookings' => $bookings->where('status', 'pending')])
            </div>
            <div class="tab-pane fade" id="active" role="tabpanel">
                @include('pages.partials.booking-table', ['filteredBookings' => $bookings->where('status', 'approved')])
            </div>
            <div class="tab-pane fade" id="history" role="tabpanel">
                @include('pages.partials.booking-table', ['filteredBookings' => $bookings->where('status', 'done')])
            </div>
        </div>
    </div>


    {{-- Reschedule & review pop-ups (one per booking) --}}
    @foreach($bookings as $booking)
        @if($booking->status === 'pending')
        <div class="modal fade" id="rescheduleModal{{ $booking->id }}" tabindex="-1" aria-hidden="true">
            <div class="modal-dialog modal-dialog-centered">
                <form action="{{ route('user.bookings.reschedule', $booking) }}" method="POST" class="modal-content">
                    @csrf
                    <div class="modal-header">
                        <h5 class="modal-title">Reschedule — {{ $booking->service?->title ?? 'Booking' }}</h5>
                        <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                    </div>
                    <div class="modal-body">
                        <label class="form-label fw-semibold">New date</label>
                        <input type="date" name="preferred_date" class="form-control mb-3 reschedule-date" data-target="#slot{{ $booking->id }}" min="{{ now()->addDay()->toDateString() }}" required>
                        <label class="form-label fw-semibold">Time slot</label>
                        <select name="time_slot" id="slot{{ $booking->id }}" class="form-select" required disabled>
                            <option value="">First select a date</option>
                        </select>
                    </div>
                    <div class="modal-footer">
                        <button type="button" class="btn btn-light" data-bs-dismiss="modal">Close</button>
                        <button type="submit" class="btn btn-success">Save new date</button>
                    </div>
                </form>
            </div>
        </div>
        @endif

        @if($booking->status === 'done' && ! $booking->review)
        <div class="modal fade" id="reviewModal{{ $booking->id }}" tabindex="-1" aria-hidden="true">
            <div class="modal-dialog modal-dialog-centered">
                <form action="{{ route('user.bookings.review', $booking) }}" method="POST" class="modal-content">
                    @csrf
                    <div class="modal-header">
                        <h5 class="modal-title">How was your {{ $booking->service?->title ?? 'service' }}?</h5>
                        <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                    </div>
                    <div class="modal-body">
                        <div class="star-picker mb-3">
                            @for($s = 5; $s >= 1; $s--)
                                <input type="radio" name="rating" id="r{{ $booking->id }}_{{ $s }}" value="{{ $s }}" required>
                                <label for="r{{ $booking->id }}_{{ $s }}" title="{{ $s }} star"><i class="fa fa-star"></i></label>
                            @endfor
                        </div>
                        <textarea name="comment" class="form-control" rows="4" minlength="10" maxlength="1000" placeholder="Tell others about your experience (at least 10 characters)" required></textarea>
                    </div>
                    <div class="modal-footer">
                        <button type="button" class="btn btn-light" data-bs-dismiss="modal">Close</button>
                        <button type="submit" class="btn btn-success">Submit Review</button>
                    </div>
                </form>
            </div>
        </div>
        @endif
    @endforeach

    @include('common.site.footer')

    <script src="https://code.jquery.com/jquery-3.4.1.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.0.0/dist/js/bootstrap.bundle.min.js"></script>
    <script>
        // Load free time slots when a new date is picked in the reschedule pop-up
        var allSlots = @json(\App\Models\Booking::TIME_SLOTS);
        // the server returns the slots that are already full
        var lastRequest = {};
        $('.reschedule-date').on('change', function () {
            var select = $($(this).data('target'));
            var key = $(this).data('target');
            var requestId = lastRequest[key] = (lastRequest[key] || 0) + 1;
            var keep = select.val();
            select.html('<option value="">Loading slots...</option>').prop('disabled', true);
            $.getJSON("{{ route('checkSlots') }}", { date: $(this).val() }, function (booked) {
                if (requestId !== lastRequest[key]) return; // a newer date was picked meanwhile
                var free = allSlots.filter(function (s) { return !booked.includes(s); });
                var html = free.length ? '<option value="">Select a Time Slot</option>' : '<option value="">No free slots — pick another date</option>';
                free.forEach(function (s) { html += '<option value="' + s + '"' + (s === keep ? ' selected' : '') + '>' + s + '</option>'; });
                select.html(html).prop('disabled', false);
            });
        });
    </script>
    @include('sweetalert::alert')
</body>
</html>
