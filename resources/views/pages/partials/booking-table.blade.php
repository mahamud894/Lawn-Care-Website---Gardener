<div class="card dashboard-card p-4">
    <div class="table-responsive">
        <table class="table table-hover align-middle mb-0">
            <thead class="table-light">
                <tr>
                    <th scope="col" class="ps-3 text-secondary">Service Name</th>
                    <th scope="col" class="text-secondary">Preferred Date</th>
                    <th scope="col" class="text-secondary">Status</th>
                    <th scope="col" class="pe-3 text-secondary">Your Message Note</th>
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
                    <td>
                        <span class="status-badge badge-{{ $booking->status }}">
                            @if($booking->status == 'pending')
                                <i class="fa fa-spinner fa-spin"></i>
                            @elseif($booking->status == 'approved')
                                <i class="fa fa-check-circle"></i>
                            @elseif($booking->status == 'done')
                                <i class="fa fa-history"></i>
                            @else
                                <i class="fa fa-times-circle"></i>
                            @endif
                            {{ $booking->status }}
                        </span>
                    </td>
                    <td class="text-muted pe-3">{{ Str::limit($booking->message, 60, '...') }}</td>
                </tr>
                @empty
                <tr>
                    <td colspan="4" class="text-center py-5 text-muted">
                        <i class="bi bi-inbox display-4 text-muted mb-2 d-block"></i>
                        No record available in this section.
                    </td>
                </tr>
                @endforelse
            </tbody>
        </table>
    </div>
</div>
