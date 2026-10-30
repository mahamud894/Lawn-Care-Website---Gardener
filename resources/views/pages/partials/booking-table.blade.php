<div class="card dashboard-card p-4">
    <div class="table-responsive">
        <table class="table table-hover align-middle mb-0">
            <thead class="table-light">
                <tr>
                    <th scope="col" class="ps-3 text-secondary">{{ __('Service Name') }}</th>
                    <th scope="col" class="text-secondary">{{ __('Preferred Date') }}</th>
                    <th scope="col" class="text-secondary">{{ __('Time Slot') }}</th>
                    <th scope="col" class="text-secondary">{{ __('Assigned Worker') }}</th>
                    <th scope="col" class="text-secondary">{{ __('Status') }}</th>
                    <th scope="col" class="text-secondary">{{ __('Price') }}</th>
                    <th scope="col" class="text-secondary">{{ __('Your Message Note') }}</th>
                    <th scope="col" class="pe-3 text-secondary">{{ __('Actions') }}</th>
                </tr>
            </thead>
            <tbody>
                @forelse($filteredBookings as $booking)
                <tr>
                    <td class="fw-bold ps-3 text-dark">
                        <i class="fa fa-seedling text-success me-2"></i>
                        {{ $booking->service?->title ?? __('Custom Gardening Request') }}
                    </td>
                    <td class="text-muted">
                        {{ $booking->preferred_date ? \Illuminate\Support\Carbon::parse($booking->preferred_date)->format('d M Y') : __('Not Specified') }}
                    </td>

                    <td>
                        <span class="badge bg-light text-dark border px-2 py-1" style="font-size: 0.85rem;">
                            <i class="fa fa-clock text-muted me-1"></i> {{ $booking->time_slot ?? 'N/A' }}
                        </span>
                    </td>

                    <td>
                        @if($booking->teamMember)
                            {{-- color: #212529 !important দিয়ে লেখা কালো করা হলো এবং কলামের নাম মাল্টিপল চেক করা হলো --}}
                            <span class="fw-bold" style="font-size: 0.9rem; color: #212529 !important;">
                                <i class="fa fa-user text-success me-1"></i>
                                {{ $booking->teamMember->title }}
                            </span>
                        @else
                            <span class="text-muted small italic">{{ __('Not Assigned Yet') }}</span>
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
                            {{ __(ucfirst($booking->status)) }}
                        </span>
                        @if($booking->recurrence && $booking->recurrence !== 'none')
                            <div class="small text-muted mt-1"><i class="fa fa-redo me-1"></i>{{ __(\App\Models\Booking::RECURRENCES[$booking->recurrence] ?? '') }}</div>
                        @endif
                    </td>
                    <td class="text-nowrap">
                        @if($booking->amountDue() > 0)
                            <div class="fw-bold">{{ money($booking->amountDue()) }}</div>
                            @if($booking->isPaid())
                                <span class="badge bg-success">{{ __('Paid') }}</span>
                            @elseif(!in_array($booking->status, ['cancelled', 'rejected']))
                                <span class="badge bg-warning text-dark">{{ __('Unpaid') }}</span>
                            @endif
                        @else
                            <span class="text-muted small">—</span>
                        @endif
                    </td>
                    <td class="text-muted">{{ Str::limit($booking->message, 60, '...') }}</td>
                    <td class="pe-3">
                      <div class="d-flex flex-wrap gap-1">
                        @if($booking->status === 'pending')
                            <button type="button" class="btn btn-sm btn-outline-primary rounded-pill" data-bs-toggle="modal" data-bs-target="#rescheduleModal{{ $booking->id }}">
                                <i class="fa fa-calendar-alt me-1"></i> {{ __('Reschedule') }}
                            </button>
                        @endif

                        @if(in_array($booking->status, ['pending', 'approved']) && ! $booking->isPaid())
                            <form action="{{ route('user.bookings.cancel', $booking) }}" method="POST" class="d-inline" onsubmit="return confirm('{{ __('Cancel this booking?') }}')">
                                @csrf
                                <button type="submit" class="btn btn-sm btn-outline-danger rounded-pill">
                                    <i class="fa fa-times me-1"></i> {{ __('Cancel') }}
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
                                    <i class="fa fa-star me-1"></i> {{ __('Leave a Review') }}
                                </button>
                            @endif
                        @endif

                        @if(!$booking->isPaid() && $booking->amountDue() > 0 && in_array($booking->status, ['pending', 'approved', 'done']) && \App\Services\SslCommerz::enabled())
                            <form action="{{ route('user.bookings.pay', $booking) }}" method="POST" class="d-inline">
                                @csrf
                                <button type="submit" class="btn btn-sm btn-primary rounded-pill"><i class="fa fa-credit-card me-1"></i> {{ __('Pay Online') }}</button>
                            </form>
                        @endif

                        @if($booking->amountDue() > 0 && !in_array($booking->status, ['cancelled', 'rejected']))
                            <a href="{{ route('bookings.invoice', $booking) }}" target="_blank" class="btn btn-sm btn-outline-secondary rounded-pill"><i class="fa fa-file-invoice me-1"></i> {{ __('Invoice') }}</a>
                        @endif

                        @if($booking->recurrence && $booking->recurrence !== 'none' && in_array($booking->status, ['pending', 'approved', 'done']))
                            <form action="{{ route('user.bookings.stopRepeat', $booking) }}" method="POST" class="d-inline" onsubmit="return confirm('{{ __('Stop repeating this booking?') }}')">
                                @csrf
                                <button type="submit" class="btn btn-sm btn-outline-dark rounded-pill"><i class="fa fa-stop me-1"></i> {{ __('Stop repeating') }}</button>
                            </form>
                        @endif
                      </div>
                    </td>
                </tr>
                @empty
                <tr>
                    <td colspan="8" class="text-center py-5 text-muted">
                        <i class="bi bi-inbox display-4 text-muted mb-2 d-block"></i>
                        {{ __('No record available in this section.') }}
                    </td>
                </tr>
                @endforelse
            </tbody>
        </table>
    </div>
</div>
