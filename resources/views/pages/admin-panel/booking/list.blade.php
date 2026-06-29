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
                            <ul>
                                <li>Name: {{$booking->user?->name ?? 'Guest'}}</li>
                                <li>Email: {{$booking->user?->email ?? 'N/A'}}</li>
                                <li>Phone: {{$booking->user?->phone ?? 'N/A'}}</li>
                            </ul>
                        </td>
                        <td>{{ $booking->preferred_date ? \Illuminate\Support\Carbon::parse($booking->preferred_date)->format('d M Y') : 'N/A' }}</td>
                        <td>{{$booking->message}}</td>
                        <td>
                            @if($booking->status == "pending")
                            <span class="badge bg-warning">{{$booking->status}}</span>
                            @elseif($booking->status == "rejected")
                            <span class="badge bg-danger">{{$booking->status}}</span>
                            @elseif($booking->status == "done")
                            <span class="badge bg-success">{{$booking->status}}</span>
                            @else
                            <span class="badge bg-info">{{$booking->status}}</span>
                            @endif
                        </td>
                        <td>
                            <a href="{{route("pendingBooking", $booking->id)}}" class="btn btn-warning text-white btn-sm">Pending</a>
                            <a href="{{route("doneBooking", $booking->id)}}" class="btn btn-success text-white btn-sm">Done</a>
                            <a href="{{route("approveBooking", $booking->id)}}" class="btn btn-info text-white btn-sm">Approve</a>
                            <a href="{{route("rejectBooking", $booking->id)}}" class="btn btn-danger text-white btn-sm">Reject</a>
                        </td>
                    </tr>
                    @endforeach

                </tbody>
            </table>
        </div>
    </div>
</div>
@endsection
