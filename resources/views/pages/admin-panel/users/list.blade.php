@extends('admin-panel')
@section("content")
<div class="card admin-card shadow-sm mx-3 border-0 rounded-4">
    <div class="card-body p-4">
        <div class="d-flex align-items-center justify-content-between mb-4">
            <div>
                <h5 class="card-title fw-bold text-dark mb-1"><i class="bi bi-people-fill text-success me-2"></i>User Management</h5>
                <p class="text-muted small mb-0">Overview of registered users, platform roles, and privileges.</p>
            </div>
            <div class="bg-light px-3 py-2 rounded-pill text-secondary small fw-bold">
                Total Registered: {{ $users->count() }}
            </div>
        </div>

        <div class="table-responsive">
            <table class="table table-hover align-middle mb-0">
                <thead class="table-light">
                    <tr>
                        <th class="ps-3">SL</th>
                        <th>Full Name</th>
                        <th>Email Address</th>
                        <th>Phone Number</th>
                        <th>Current Role</th>
                        <th class="text-end pe-3">Actions</th>
                    </tr>
                </thead>
                <tbody>
                    @foreach($users as $key => $user)
                    <tr>
                        <td class="ps-3 text-muted">{{ $key + 1 }}</td>
                        <td>
                            <div class="d-flex align-items-center gap-2">
                                <div class="rounded-circle bg-success bg-opacity-10 text-success d-flex align-items-center justify-content-center" style="width: 35px; height: 35px;">
                                    <i class="bi bi-person"></i>
                                </div>
                                <span class="fw-bold text-dark">{{ $user->name }}</span>
                            </div>
                        </td>
                        <td class="text-muted">{{ $user->email }}</td>
                        <td class="text-muted">{{ $user->phone ?? 'N/A' }}</td>
                        <td>
                            @if($user->isAdmin == 1)
                                <span class="badge bg-danger text-white rounded-pill px-3 py-2 fw-bold d-inline-flex align-items-center justify-content-center" style="font-size: 0.85rem; min-width: 90px; height: 32px;">
                                    <i class="bi bi-shield-lock-fill me-1"></i> Admin
                                </span>
                            @else
                                <span class="badge bg-success text-white rounded-pill px-3 py-2 fw-bold d-inline-flex align-items-center justify-content-center" style="font-size: 0.85rem; min-width: 90px; height: 32px;">
                                    <i class="bi bi-person-fill me-1"></i> User
                                </span>
                            @endif
                        </td>
                        <td class="text-end pe-3">
                            @if(auth()->id() !== $user->id)
                                @if($user->isAdmin == 1)
                                    <a href="{{ route('admin.users.toggleRole', $user->id) }}" class="btn btn-outline-primary btn-sm rounded-pill px-3">
                                        <i class="bi bi-person-down me-1"></i> Demote to User
                                    </a>
                                @else
                                    <a href="{{ route('admin.users.toggleRole', $user->id) }}" class="btn btn-success btn-sm rounded-pill px-3 text-white">
                                        <i class="bi bi-shield-arrow me-1"></i> Make Admin
                                    </a>
                                @endif
                            @else
                                <span class="text-muted small italic pe-2">Logged In <i class="bi bi-check-circle-fill text-success"></i></span>
                            @endif
                        </td>
                    </tr>
                    @endforeach
                </tbody>
            </table>
        </div>
    </div>
</div>
@endsection
