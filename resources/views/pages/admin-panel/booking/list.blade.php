@extends('admin-panel')
@section("content")

<div class="card admin-card">
    <div class="card-body">
        <div class="d-flex align-items-center justify-content-between mb-3">
            <div>
                <h5 class="card-title mb-1">Booking List</h5>
                <p class="text-muted mb-0">View all booking list.</p>
            </div>
            <div class="d-flex align-items-center gap-2">
                <span class="badge bg-secondary">{{ $bookings->total() }} bookings</span>
                <a href="{{ route('admin.bookings.export', request()->query()) }}" class="btn btn-sm btn-outline-success"><i class="bi bi-file-earmark-spreadsheet"></i> Export CSV</a>
                <a href="{{ route('admin.calendar') }}" class="btn btn-sm btn-outline-dark"><i class="bi bi-calendar3"></i> Calendar</a>
            </div>
        </div>

        {{-- Search & filter --}}
        <form method="GET" action="{{ route('viewAllBookings') }}" class="row g-2 mb-3">
            <div class="col-md-3">
                <input type="text" name="search" value="{{ request('search') }}" class="form-control form-control-sm" placeholder="Search name, email, phone, address">
            </div>
            <div class="col-md-2">
                <select name="status" class="form-select form-select-sm">
                    <option value="">All statuses</option>
                    @foreach($statuses as $status)
                        <option value="{{ $status }}" @selected(request('status') === $status)>{{ ucfirst($status) }}</option>
                    @endforeach
                </select>
            </div>
            <div class="col-md-2">
                <select name="payment" class="form-select form-select-sm">
                    <option value="">Paid & unpaid</option>
                    <option value="paid" @selected(request('payment') === 'paid')>Paid</option>
                    <option value="unpaid" @selected(request('payment') === 'unpaid')>Unpaid</option>
                </select>
            </div>
            <div class="col-md-2">
                <input type="date" name="date" value="{{ request('date') }}" class="form-control form-control-sm">
            </div>
            <div class="col-md-3 d-flex gap-1">
                <button class="btn btn-sm btn-dark w-100" type="submit"><i class="bi bi-funnel"></i> Filter</button>
                <a href="{{ route('viewAllBookings') }}" class="btn btn-sm btn-outline-secondary" title="Clear filters">Clear</a>
            </div>
        </form>

        <div class="table-responsive">
            <table class="table table-hover align-middle mb-0">
                <thead class="table-light">
                    <tr>
                        <th>Name</th>
                        <th>Email</th>
                        <th>Service</th>
                        <th>User Info</th>
                        <th>Preferred Date</th>
                        <th>Time Slot</th>
                        <th>Assign Member</th>
                        <th>Message</th>
                        <th>Status</th>
                        <th>Payment</th>
                        <th>Actions</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse($bookings as $booking)
                    <tr>
                        <td>{{$booking->name}}</td>
                        <td>{{$booking->email}}</td>
                        <td>{{$booking->service?->title ?? 'N/A'}}</td>
                        <td>
                            <ul class="mb-0 ps-3" style="font-size: 0.85rem;">
                                <li>Name: {{$booking->user?->name ?? 'Guest'}}</li>
                                <li>Email: {{$booking->user?->email ?? 'N/A'}}</li>
                                <li>Phone: {{$booking->user?->phone ?? 'N/A'}}</li>
                                <li>Booking phone: {{$booking->phone ?? 'N/A'}}</li>
                                <li>Address: {{$booking->address ?? 'N/A'}}</li>
                                @if($booking->lawn_size)<li>Lawn: {{ number_format($booking->lawn_size) }} sq ft</li>@endif
                            </ul>
                            @if(!empty($booking->photos))
                                <div class="d-flex gap-1 mt-1">
                                    @foreach($booking->photos as $photo)
                                        <a href="{{ asset($photo) }}" target="_blank"><img loading="lazy" decoding="async" src="{{ asset($photo) }}" alt="Lawn photo" style="width: 40px; height: 40px; object-fit: cover;" class="rounded"></a>
                                    @endforeach
                                </div>
                            @endif
                        </td>
                        {{--  Preferred Date column --}}
                        <td>{{ $booking->preferred_date ? \Illuminate\Support\Carbon::parse($booking->preferred_date)->format('d M Y') : 'N/A' }}</td>

                        {{-- new Time Slot data --}}
                        <td>
                            <span class="badge bg-light text-dark border">{{ $booking->time_slot ?? 'N/A' }}</span>
                        </td>


                        <td>
                            {{--  Condition check: If a team member is already assigned --}}
                            @if($booking->team_member_id)
                                <div class="d-flex flex-column align-items-center">

                                    <span class="badge bg-success rounded-pill px-3 py-2 fw-bold text-white d-inline-flex align-items-center gap-1">
                                        <i class="bi bi-person-check-fill"></i> Member Assigned
                                    </span>

                                    <div class="small mt-1 d-flex align-items-center gap-1">
                                        <span class="text-muted">({{ $booking->teamMember?->title ?? 'ID: '.$booking->team_member_id }})</span>
                                        <a href="{{ route('admin.bookings.assignView', $booking->id) }}" class="text-primary text-decoration-none fw-bold ms-1" title="Change Team Member">
                                            <i class="bi bi-pencil-square"></i> Change
                                        </a>
                                    </div>
                                </div>
                            @else
                                {{--  Condition check: If no team member is assigned --}}
                                <div class="text-center">
                                    <a href="{{ route('admin.bookings.assignView', $booking->id) }}" class="btn btn-outline-dark btn-sm rounded-pill px-3 fw-bold">
                                        <i class="bi bi-person-plus-fill me-1"></i> Assign Team
                                    </a>
                                </div>
                            @endif
                        </td>

                        <td>{{$booking->message}}</td>

                        <td>
                            @if($booking->status == "pending")
                            <span class="badge bg-warning text-capitalize">{{$booking->status}}</span>
                            @elseif($booking->status == "rejected")
                            <span class="badge bg-danger text-capitalize">{{$booking->status}}</span>
                            @elseif($booking->status == "done")
                            <span class="badge bg-success text-capitalize">{{$booking->status}}</span>
                            @elseif($booking->status == "cancelled")
                            <span class="badge bg-secondary text-capitalize">{{$booking->status}}</span>
                            @else
                            <span class="badge bg-info text-capitalize">{{$booking->status}}</span>
                            @endif
                            @if($booking->recurrence && $booking->recurrence !== 'none')
                                <div class="small text-muted mt-1" title="Repeats"><i class="bi bi-arrow-repeat"></i> {{ \App\Models\Booking::RECURRENCES[$booking->recurrence] ?? '' }}</div>
                            @endif
                        </td>
                        <td class="text-nowrap">
                            <div class="fw-semibold">{{ $booking->amountDue() > 0 ? money($booking->amountDue()) : '—' }}</div>
                            @if($booking->isPaid())
                                <span class="badge bg-success" title="{{ $booking->paid_at?->format('d M Y') }}">Paid · {{ $booking->payment_method }}</span>
                                <form action="{{ route('admin.bookings.paid', $booking->id) }}" method="POST" class="d-inline" onsubmit="return confirm('Mark as unpaid?')">
                                    @csrf <input type="hidden" name="unpaid" value="1">
                                    <button class="btn btn-link btn-sm p-0 text-muted" type="submit" title="Undo"><i class="bi bi-arrow-counterclockwise"></i></button>
                                </form>
                            @else
                                <span class="badge bg-warning text-dark">Unpaid</span>
                                @if($booking->amountDue() > 0)
                                <form action="{{ route('admin.bookings.paid', $booking->id) }}" method="POST" class="d-inline">
                                    @csrf
                                    <button class="btn btn-outline-success btn-sm py-0" type="submit">Cash received</button>
                                </form>
                                @endif
                            @endif
                            @if($booking->amountDue() > 0)
                                <div><a href="{{ route('bookings.invoice', $booking) }}" target="_blank" class="small"><i class="bi bi-receipt"></i> Invoice</a></div>
                            @endif
                        </td>
                        <td>
                            <div class="d-flex gap-1">
                                @foreach(['pending' => 'warning', 'done' => 'success', 'approved' => 'info', 'rejected' => 'danger'] as $action => $color)
                                    <form action="{{ route('admin.bookings.status', $booking->id) }}" method="POST" class="d-inline">
                                        @csrf
                                        <input type="hidden" name="status" value="{{ $action }}">
                                        <button type="submit" class="btn btn-{{ $color }} text-white btn-sm" @disabled($booking->status === $action)>{{ $action === 'approved' ? 'Approve' : ($action === 'rejected' ? 'Reject' : ucfirst($action)) }}</button>
                                    </form>
                                @endforeach
                            </div>
                        </td>
                    </tr>
                    @empty
                    <tr>
                        <td colspan="11" class="text-center text-muted py-4">No bookings found.</td>
                    </tr>
                    @endforelse
                </tbody>
            </table>
        </div>

        <div class="mt-3">
            {{ $bookings->links() }}
        </div>
    </div>
</div>
@endsection
