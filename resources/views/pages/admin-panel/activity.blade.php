@extends('admin-panel')
@section("content")
<div class="card shadow-sm mx-3">
    <div class="card-body">
        <h5 class="card-title mb-1">Activity Log</h5>
        <p class="text-muted small">Who changed what in the admin panel, gardener portal and customer bookings.</p>
        <div class="table-responsive">
            <table class="table table-sm table-hover align-middle mb-0">
                <thead class="table-light">
                    <tr><th>When</th><th>Who</th><th>Action</th><th>Details</th><th>IP</th></tr>
                </thead>
                <tbody>
                    @forelse($logs as $log)
                        <tr>
                            <td class="text-nowrap small">{{ $log->created_at?->format('d M Y, h:i A') }}</td>
                            <td class="small">{{ $log->user->name ?? 'System' }}</td>
                            <td><span class="badge bg-light text-dark border">{{ $log->action }}</span></td>
                            <td class="small">{{ $log->description }}</td>
                            <td class="small text-muted">{{ $log->ip_address }}</td>
                        </tr>
                    @empty
                        <tr><td colspan="5" class="text-center text-muted py-4">Nothing logged yet.</td></tr>
                    @endforelse
                </tbody>
            </table>
        </div>
        <div class="mt-3">{{ $logs->links() }}</div>
    </div>
</div>
@endsection
