@extends('admin-panel')
@section("content")
<div class="card shadow-sm mx-3">
    <div class="card-body">
        <div class="mb-3">
            <h5 class="card-title mb-1">Contact Messages</h5>
            <p class="text-muted mb-0">Messages sent from the Contact page.</p>
        </div>
        <div class="table-responsive">
            <table class="table table-hover align-middle mb-0">
                <thead class="table-light">
                    <tr>
                        <th>From</th>
                        <th>Subject &amp; Message</th>
                        <th>Received</th>
                        <th>Actions</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse($messages as $msg)
                    <tr class="{{ $msg->isRead ? '' : 'table-warning' }}">
                        <td>
                            <div class="fw-semibold">{{ $msg->name }}</div>
                            <a href="mailto:{{ $msg->email }}?subject={{ rawurlencode('Re: '.($msg->subject ?: 'Your message')) }}" class="small">{{ $msg->email }}</a>
                        </td>
                        <td style="max-width: 420px;">
                            <div class="fw-semibold">{{ $msg->subject ?: '(no subject)' }}</div>
                            <div class="small text-muted" style="white-space: pre-line;">{{ $msg->message }}</div>
                        </td>
                        <td class="small text-nowrap">{{ $msg->created_at->format('d M Y, h:i A') }}</td>
                        <td class="text-nowrap">
                            <form action="{{ route('admin.messages.toggleRead', $msg) }}" method="POST" class="d-inline">
                                @csrf
                                <button type="submit" class="btn btn-sm {{ $msg->isRead ? 'btn-outline-secondary' : 'btn-success' }}">
                                    {{ $msg->isRead ? 'Mark unread' : 'Mark read' }}
                                </button>
                            </form>
                            <form action="{{ route('admin.messages.delete', $msg) }}" method="POST" class="d-inline" onsubmit="return confirm('Delete this message?')">
                                @csrf
                                @method('DELETE')
                                <button type="submit" class="btn btn-sm btn-danger">Delete</button>
                            </form>
                        </td>
                    </tr>
                    @empty
                    <tr><td colspan="4" class="text-center text-muted py-4">No messages yet.</td></tr>
                    @endforelse
                </tbody>
            </table>
        </div>
        <div class="mt-3">{{ $messages->links() }}</div>
    </div>
</div>
@endsection
