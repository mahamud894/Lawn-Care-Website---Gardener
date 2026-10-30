@extends('admin-panel')
@section("content")
<div class="card shadow-sm mx-3">
    <div class="card-body">
        <div class="mb-3">
            <h5 class="card-title mb-1">Customer Reviews</h5>
            <p class="text-muted mb-0">Approved reviews appear in the Testimonial section of the home page.</p>
        </div>
        <div class="table-responsive">
            <table class="table table-hover align-middle mb-0">
                <thead class="table-light">
                    <tr>
                        <th>Customer</th>
                        <th>Service</th>
                        <th>Rating</th>
                        <th>Comment</th>
                        <th>Status</th>
                        <th>Actions</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse($reviews as $review)
                    <tr>
                        <td>{{ $review->user->name ?? 'Deleted user' }}</td>
                        <td>{{ $review->booking->service->title ?? 'N/A' }}</td>
                        <td class="text-warning text-nowrap">
                            @for($s = 1; $s <= 5; $s++)<i class="bi bi-star{{ $s <= $review->rating ? '-fill' : '' }}"></i>@endfor
                        </td>
                        <td style="max-width: 380px;" class="small">{{ $review->comment }}</td>
                        <td>
                            @if($review->isApproved)
                                <span class="badge bg-success">Visible</span>
                            @else
                                <span class="badge bg-warning text-dark">Waiting</span>
                            @endif
                        </td>
                        <td class="text-nowrap">
                            <form action="{{ route('admin.reviews.toggleApprove', $review) }}" method="POST" class="d-inline">
                                @csrf
                                <button type="submit" class="btn btn-sm {{ $review->isApproved ? 'btn-outline-secondary' : 'btn-success' }}">
                                    {{ $review->isApproved ? 'Hide' : 'Approve' }}
                                </button>
                            </form>
                            <form action="{{ route('admin.reviews.delete', $review) }}" method="POST" class="d-inline" onsubmit="return confirm('Delete this review?')">
                                @csrf
                                @method('DELETE')
                                <button type="submit" class="btn btn-sm btn-danger">Delete</button>
                            </form>
                        </td>
                    </tr>
                    @empty
                    <tr><td colspan="6" class="text-center text-muted py-4">No reviews yet. Customers can review a booking once it is marked Done.</td></tr>
                    @endforelse
                </tbody>
            </table>
        </div>
        <div class="mt-3">{{ $reviews->links() }}</div>
    </div>
</div>
@endsection
