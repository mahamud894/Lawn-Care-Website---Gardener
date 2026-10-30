<div class="card dashboard-card p-4">
    <div class="table-responsive">
        <table class="table table-hover align-middle mb-0">
            <thead class="table-light">
                <tr>
                    <th scope="col" class="ps-3 text-secondary">Service Name</th>
                    <th scope="col" class="text-secondary">Preferred Date</th>
                    <th scope="col" class="text-secondary">Time Slot</th> {{-- 💡 নতুন হেডার --}}
                    <th scope="col" class="text-secondary">Assigned Worker</th> {{-- 💡 নতুন হেডার --}}
                    <th scope="col" class="text-secondary">Status</th>
                    <th scope="col" class="text-secondary">Your Message Note</th>
                    <th scope="col" class="pe-3 text-secondary">Actions</th>
                </tr>
            </thead>
            <tbody>
                @forelse($filteredBookings as $booking)
                <tr>
                    <td class="fw-bold ps-3 text-dark">
                        <i class="fa fa-seedling text-success me-2"></i>
                        {{ $booking->service?->title ?? 'Custom Gardening Request' }}
                    </td>
                    <td class="text-muted">
                        {{ $booking->preferred_date ? \Illuminate\Support\Carbon::parse($booking->preferred_date)->format('d M Y') : 'Not Specified' }}
                    </td>

                    {{-- 💡 নতুন ডাটা ১: টাইম স্লট দেখাবে --}}
                    <td>
                        <span class="badge bg-light text-dark border px-2 py-1" style="font-size: 0.85rem;">
                            <i class="fa fa-clock text-muted me-1"></i> {{ $booking->time_slot ?? 'N/A' }}
                        </span>
                    </td>

                    {{-- 💡 নতুন ডাটা ২: অ্যাসাইন করা টিমের নাম দেখাবে --}}
                   {{-- 💡 নতুন আপডেট করা Assigned Worker ডাটা ফিল্ড --}}
                    <td>
                        @if($booking->teamMember)
                            {{-- color: #212529 !important দিয়ে লেখা কালো করা হলো এবং কলামের নাম মাল্টিপল চেক করা হলো --}}
                            <span class="fw-bold" style="font-size: 0.9rem; color: #212529 !important;">
                                <i class="fa fa-user text-success me-1"></i>
                                {{ $booking->teamMember->title }}
                            </span>
                        @else
                            <span class="text-muted small italic">Not Assigned Yet</span>
                        @endif
                    </td>

                    <td>
                        <span class="status-badge badge-{{ $booking->status }}">
                            @if($booking->status == 'pending')
                                <i class="fa fa-spinner fa-spin"></i>
                            @elseif($booking->status == 'approved')
                                <i class="fa fa-check-circle"></i>
                            @elseif($booking->status == 'done')
                                <i class="fa fa-history"></i>
                            @elseif($booking->status == 'cancelled')
                                <i class="fa fa-ban"></i>
                            @else
                                <i class="fa fa-times-circle"></i>
                            @endif
                            {{ $booking->status }}
                        </span>
                    </td>
                    <td class="text-muted">{{ Str::limit($booking->message, 60, '...') }}</td>
                    <td class="pe-3 text-nowrap">
                        @if($booking->status === 'pending')
                            <button type="button" class="btn btn-sm btn-outline-primary rounded-pill" data-bs-toggle="modal" data-bs-target="#rescheduleModal{{ $booking->id }}">
                                <i class="fa fa-calendar-alt me-1"></i> Reschedule
                            </button>
                        @endif

                        @if(in_array($booking->status, ['pending', 'approved']))
                            <form action="{{ route('user.bookings.cancel', $booking) }}" method="POST" class="d-inline" onsubmit="return confirm('Cancel this booking?')">
                                @csrf
                                <button type="submit" class="btn btn-sm btn-outline-danger rounded-pill">
                                    <i class="fa fa-times me-1"></i> Cancel
                                </button>
                            </form>
                        @endif

                        @if($booking->status === 'done')
                            @if($booking->review)
                                <span class="text-warning small" title="Your rating">
                                    @for($s = 1; $s <= 5; $s++)<i class="fa{{ $s <= $booking->review->rating ? 's' : 'r' }} fa-star"></i>@endfor
                                </span>
                            @else
                                <button type="button" class="btn btn-sm btn-success rounded-pill" data-bs-toggle="modal" data-bs-target="#reviewModal{{ $booking->id }}">
                                    <i class="fa fa-star me-1"></i> Leave a Review
                                </button>
                            @endif
                        @endif
                    </td>
                </tr>
                @empty
                <tr>
                    <td colspan="7" class="text-center py-5 text-muted">
                        <i class="bi bi-inbox display-4 text-muted mb-2 d-block"></i>
                        No record available in this section.
                    </td>
                </tr>
                @endforelse
            </tbody>
        </table>
    </div>
</div>
