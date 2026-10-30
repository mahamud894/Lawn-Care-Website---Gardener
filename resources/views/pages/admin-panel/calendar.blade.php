@extends('admin-panel')
@section("content")
<style>
    .cal-table { table-layout: fixed; }
    .cal-table td { height: 120px; vertical-align: top; padding: 6px; }
    .cal-table td.other-month { background: #f8f9fa; color: #adb5bd; }
    .cal-table td.today { outline: 2px solid #348E38; outline-offset: -2px; }
    .cal-day { font-weight: 700; font-size: .85rem; }
    .cal-event { display: block; font-size: .72rem; border-radius: 4px; padding: 2px 4px; margin-top: 3px; color: #fff; text-decoration: none; white-space: nowrap; overflow: hidden; text-overflow: ellipsis; }
    .cal-event.pending { background: #ffc107; color: #212529; }
    .cal-event.approved { background: #0dcaf0; color: #052c33; }
    .cal-event.done { background: #198754; }
</style>

<div class="card shadow-sm mx-3">
    <div class="card-body">
        <div class="d-flex align-items-center justify-content-between flex-wrap gap-2 mb-3">
            <div>
                <h5 class="card-title mb-1">Booking Calendar</h5>
                <p class="text-muted mb-0 small">Each time slot can take {{ $capacity }} booking(s) — one per active team member.</p>
            </div>
            <div class="d-flex align-items-center gap-2">
                <a class="btn btn-sm btn-outline-secondary" href="{{ route('admin.calendar', ['month' => $month->copy()->subMonth()->format('Y-m')]) }}">&larr; Prev</a>
                <span class="fw-bold px-2">{{ $month->format('F Y') }}</span>
                <a class="btn btn-sm btn-outline-secondary" href="{{ route('admin.calendar', ['month' => $month->copy()->addMonth()->format('Y-m')]) }}">Next &rarr;</a>
                <a class="btn btn-sm btn-dark" href="{{ route('admin.calendar') }}">Today</a>
            </div>
        </div>

        <div class="d-flex gap-3 small mb-2">
            <span><span class="cal-event pending d-inline-block">Pending</span></span>
            <span><span class="cal-event approved d-inline-block">Approved</span></span>
            <span><span class="cal-event done d-inline-block">Done</span></span>
        </div>

        <div class="table-responsive">
            <table class="table table-bordered cal-table mb-0">
                <thead class="table-light text-center small">
                    <tr>
                        @foreach(['Sat', 'Sun', 'Mon', 'Tue', 'Wed', 'Thu', 'Fri'] as $d)<th>{{ $d }}</th>@endforeach
                    </tr>
                </thead>
                <tbody>
                    @foreach($weeks as $week)
                        <tr>
                            @foreach($week as $day)
                                @php($dayBookings = $bookings->get($day->toDateString(), collect()))
                                <td class="{{ $day->month !== $month->month ? 'other-month' : '' }} {{ $day->isToday() ? 'today' : '' }}">
                                    <div class="d-flex justify-content-between">
                                        <span class="cal-day">{{ $day->day }}</span>
                                        @if($dayBookings->count())
                                            <a href="{{ route('viewAllBookings', ['date' => $day->toDateString()]) }}" class="badge bg-dark text-decoration-none">{{ $dayBookings->count() }}</a>
                                        @endif
                                    </div>
                                    @foreach($dayBookings->take(4) as $b)
                                        <a href="{{ route('viewAllBookings', ['date' => $day->toDateString(), 'search' => $b->email]) }}"
                                           class="cal-event {{ $b->status }}"
                                           title="{{ $b->time_slot }} — {{ $b->service?->title }} — {{ $b->name }}{{ $b->teamMember ? ' ('.$b->teamMember->title.')' : '' }}">
                                            {{ substr($b->time_slot, 0, 8) }} {{ $b->name }}
                                        </a>
                                    @endforeach
                                    @if($dayBookings->count() > 4)
                                        <a href="{{ route('viewAllBookings', ['date' => $day->toDateString()]) }}" class="small">+{{ $dayBookings->count() - 4 }} more</a>
                                    @endif
                                </td>
                            @endforeach
                        </tr>
                    @endforeach
                </tbody>
            </table>
        </div>
    </div>
</div>
@endsection
