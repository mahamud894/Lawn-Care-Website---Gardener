@extends('admin-panel')
@section("content")

<div class="card admin-card">
    <div class="card-body">
        <div class="d-flex align-items-center justify-content-between mb-3">
            <div>
                <h5 class="card-title mb-1">Booking List</h5>
                <p class="text-muted mb-0">View all booking list.</p>
            </div>
        </div>
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
                        <th>Actions</th>
                    </tr>
                </thead>
                <tbody>
                    @foreach($bookings as $booking)
                    <tr>
                        <td>{{$booking->name}}</td>
                        <td>{{$booking->email}}</td>
                        <td>{{$booking->service?->title ?? 'N/A'}}</td>
                        <td>
                            <ul class="mb-0 ps-3" style="font-size: 0.85rem;">
                                <li>Name: {{$booking->user?->name ?? 'Guest'}}</li>
                                <li>Email: {{$booking->user?->email ?? 'N/A'}}</li>
                                <li>Phone: {{$booking->user?->phone ?? 'N/A'}}</li>
                                <li>Address: {{$booking->address ?? 'N/A'}}</li>
                            </ul>
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
                                        <span class="text-muted">({{ $booking->teamMember?->name ?? 'ID: '.$booking->team_member_id }})</span>
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
                            @else
                            <span class="badge bg-info text-capitalize">{{$booking->status}}</span>
                            @endif
                        </td>
                        <td>
                            <div class="d-flex gap-1">
                                <a href="{{route("pendingBooking", $booking->id)}}" class="btn btn-warning text-white btn-sm">Pending</a>
                                <a href="{{route("doneBooking", $booking->id)}}" class="btn btn-success text-white btn-sm">Done</a>
                                <a href="{{route("approveBooking", $booking->id)}}" class="btn btn-info text-white btn-sm">Approve</a>
                                <a href="{{route("rejectBooking", $booking->id)}}" class="btn btn-danger text-white btn-sm">Reject</a>
                            </div>
                        </td>
                    </tr>
                    @endforeach

                </tbody>
            </table>
        </div>
    </div>
</div>
@endsection
