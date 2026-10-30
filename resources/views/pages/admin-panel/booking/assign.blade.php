@extends('admin-panel')
@section("content")
<div class="container mt-4">
    <div class="card shadow border-0" style="max-width: 500px; margin: 0 auto; background-color: #ffffff;">
        <div class="card-body p-4">
            <h5 class="fw-bold text-dark mb-1">Assign Team Member</h5>
            <p class="text-muted small mb-4">Select a member for <strong>{{ $booking->name }}</strong>'s booking
                — {{ $booking->preferred_date ? \Illuminate\Support\Carbon::parse($booking->preferred_date)->format('d M Y') : '' }} {{ $booking->time_slot }}.</p>

            <form action="{{ route('admin.bookings.assignTeam', $booking->id) }}" method="POST">
                @csrf
                <div class="mb-4">
                    {{-- ১. Un-assign / Remove Team Member --}}
                    <div class="d-flex align-items-center p-3 mb-2 rounded border border-danger-subtle" style="background-color: #fff5f5;">
                        <input class="form-check-input me-3" type="radio" name="team_member_id" id="unassign" value="" {{ is_null($booking->team_member_id) ? 'checked' : '' }} style="transform: scale(1.2); cursor: pointer;">
                        <label class="form-check-label text-danger fw-bold mb-0" for="unassign" style="cursor: pointer; font-size: 1rem; width: 100%;">
                            <i class="bi bi-person-x-fill me-2"></i> Un-assign / Remove Team Member
                        </label>
                    </div>

                    {{-- ২. Assign Team Member --}}
                    @forelse($teamMembers as $member)
                        @php($busy = in_array($member->id, $busyIds))
                        <div class="d-flex align-items-center p-3 mb-2 rounded border {{ $busy ? 'opacity-50' : '' }}" style="background-color: #f8f9fa;">
                            <input class="form-check-input me-3" type="radio" name="team_member_id" id="member_{{ $member->id }}" value="{{ $member->id }}" {{ $booking->team_member_id == $member->id ? 'checked' : '' }} @disabled($busy) style="transform: scale(1.2); cursor: pointer;">
                            <label class="form-check-label text-dark fw-bold mb-0" for="member_{{ $member->id }}" style="cursor: pointer; font-size: 1rem; width: 100%; color: #212529 !important;">
                                {{ $member->title }} <span class="text-muted fw-normal small">— {{ $member->position }}</span>
                                @if($busy)<span class="badge bg-secondary ms-2">Busy at this time</span>@endif
                            </label>
                        </div>
                    @empty
                        <p class="text-dark text-center py-3">No team members found in database!</p>
                    @endforelse
                </div>

                <div class="d-flex gap-2">
                    <a href="{{ route('viewAllBookings') }}" class="btn btn-light w-50 rounded-pill">Back to List</a>
                    <button type="submit" class="btn btn-success w-50 rounded-pill fw-bold">Confirm Assignment</button>
                </div>
            </form>
        </div>
    </div>
</div>
@endsection
