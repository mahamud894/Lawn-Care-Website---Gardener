@extends('admin-panel')
@section("content")
<div class="container-fluid px-3">

    {{-- ================= সেকশন ১: রেগুলার ইউজার বা কাস্টমার লিস্ট ================= --}}
    <div class="card admin-card shadow-sm border-0 rounded-4 mb-5 bg-white">
        <div class="card-body p-4">
            <div class="d-flex align-items-center justify-content-between mb-4">
                <div>
                    <h5 class="card-title fw-bold text-dark mb-1">
                        <i class="bi bi-person-fill text-success me-2"></i>Regular Users List
                    </h5>
                    <p class="text-muted small mb-0">Overview of registered customers and regular platform users.</p>
                </div>
                <div class="bg-success bg-opacity-10 text-white px-3 py-2 rounded-pill small fw-bold">
                    Total Users: {{ $users->where('isAdmin', 0)->count() }}
                </div>
            </div>

            <div class="table-responsive">
                <table class="table table-hover align-middle mb-0">
                    <thead class="table-light">
                        <tr>
                            <th class="ps-3" style="width: 80px;">SL</th>
                            <th>Full Name</th>
                            <th>Email Address</th>
                            <th>Phone Number</th>
                            <th class="text-center">Role</th>
                            <th class="text-center pe-3" style="width: 250px;">Actions</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse($users->where('isAdmin', 0) as $key => $user)
                        <tr>
                            <td class="ps-3 text-muted">{{ $loop->iteration }}</td>
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
                            <td class="text-center">
                                <span class="badge bg-success text-white rounded-pill px-3 py-2 fw-bold d-inline-flex align-items-center justify-content-center" style="font-size: 0.85rem; min-width: 110px; height: 32px;">
                                    <i class="bi bi-person-fill me-1"></i> User
                                </span>
                            </td>
                            <td class="text-center pe-3">
                                <div class="d-flex align-items-center justify-content-center gap-2">
                                    {{-- 💡 ব্লক/আনব্লক টগল বাটন --}}
                                    @if($user->isBlocked == 1)
                                        <a href="{{ route('admin.users.toggleBlock', $user->id) }}" class="btn btn-success btn-sm rounded-pill px-3 py-1 text-white fw-bold">
                                            <i class="bi bi-unlock-fill me-1"></i> Unblock
                                        </a>
                                    @else
                                        <a href="{{ route('admin.users.toggleBlock', $user->id) }}" class="btn btn-warning btn-sm rounded-pill px-3 py-1 text-dark fw-bold">
                                            <i class="bi bi-shield-slash-fill me-1"></i> Block
                                        </a>
                                    @endif

                                    {{-- ডিলিট বাটন --}}
                                    <a href="{{ route('admin.users.delete', $user->id) }}" onclick="return confirm('Are you sure you want to delete this user permanently?')" class="btn btn-danger btn-sm rounded-pill px-3 py-1 text-white">
                                        <i class="bi bi-trash-fill me-1"></i> Delete
                                    </a>
                                </div>
                            </td>
                        </tr>
                        @empty
                        <tr>
                            <td colspan="6" class="text-center py-4 text-muted">No regular users found.</td>
                        </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
        </div>
    </div>

    {{-- ================= সেকশন ২: অ্যাডমিনিস্ট্রেটর বা অ্যাসিস্ট্যান্ট লিস্ট ================= --}}
    <div class="card admin-card shadow-sm border-0 rounded-4 bg-white">
        <div class="card-body p-4">
            <div class="d-flex align-items-center justify-content-between mb-4">
                <div>
                    <h5 class="card-title fw-bold text-dark mb-1">
                        <i class="bi bi-shield-lock-fill text-danger me-2"></i>Administrators List
                    </h5>
                    <p class="text-muted small mb-0">Management of system administrators and platform managers.</p>
                </div>
                <div class="bg-danger bg-opacity-10 text-white px-3 py-2 rounded-pill small fw-bold">
                    Total Admins: {{ $users->where('isAdmin', 1)->count() }}
                </div>
            </div>

            <div class="table-responsive">
                <table class="table table-hover align-middle mb-0">
                    <thead class="table-light">
                        <tr>
                            <th class="ps-3" style="width: 80px;">SL</th>
                            <th>Full Name</th>
                            <th>Email Address</th>
                            <th>Phone Number</th>
                            <th class="text-center">Role Status</th>
                            <th class="text-center pe-3" style="width: 250px;">Actions</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse($users->where('isAdmin', 1) as $key => $user)
                        <tr>
                            <td class="ps-3 text-muted">{{ $loop->iteration }}</td>
                            <td>
                                <div class="d-flex align-items-center gap-2">
                                    <div class="rounded-circle bg-danger bg-opacity-10 text-danger d-flex align-items-center justify-content-center" style="width: 35px; height: 35px;">
                                        <i class="bi bi-shield"></i>
                                    </div>
                                    <span class="fw-bold text-dark">{{ $user->name }}</span>
                                </div>
                            </td>
                            <td class="text-muted">{{ $user->email }}</td>
                            <td class="text-muted">{{ $user->phone ?? 'N/A' }}</td>

                            {{-- এডমিন স্ট্যাটাস কলাম --}}
                            <td class="text-center">
                                @if(isset($user->isAdminApproved) && $user->isAdminApproved == 0)
                                    <span class="badge bg-warning text-dark rounded-pill px-3 py-2 fw-bold d-inline-flex align-items-center justify-content-center" style="font-size: 0.85rem; min-width: 130px; height: 32px;">
                                        <i class="bi bi-hourglass-split me-1"></i> Pending Admin
                                    </span>
                                @else
                                    <span class="badge bg-danger text-white rounded-pill px-3 py-2 fw-bold d-inline-flex align-items-center justify-content-center" style="font-size: 0.85rem; min-width: 130px; height: 32px;">
                                        <i class="bi bi-shield-lock-fill me-1"></i> Admin
                                    </span>
                                @endif
                            </td>

                            {{-- অ্যাকশন বাটনসমূহ (এখানে অন্য এডমিনদের ডিলিট করার বাটন রাখা হয়েছে) --}}
                            <td class="text-center pe-3">
                                <div class="d-flex align-items-center justify-content-center gap-2">
                                    @if(auth()->id() !== $user->id)
                                        @if(isset($user->isAdminApproved) && $user->isAdminApproved == 0)
                                            <a href="{{ route('admin.users.toggleRole', $user->id) }}" class="btn btn-success btn-sm rounded-pill px-3 py-1 fw-bold text-white">
                                                <i class="bi bi-check-circle me-1"></i> Approve
                                            </a>
                                        @else
                                            <a href="{{ route('admin.users.toggleRole', $user->id) }}" class="btn btn-outline-primary btn-sm rounded-pill px-3 py-1">
                                                <i class="bi bi-person-down me-1"></i> Demote
                                            </a>
                                        @endif

                                        {{-- 💡 অন্য যেকোনো এডমিনকে মুছে ফেলার ডিলিট বাটন --}}
                                        <a href="{{ route('admin.users.delete', $user->id) }}" onclick="return confirm('Are you sure you want to delete this administrator permanently?')" class="btn btn-danger btn-sm rounded-pill px-3 py-1 text-white">
                                            <i class="bi bi-trash-fill me-1"></i> Delete
                                        </a>
                                    @else
                                        <span class="text-muted small fw-bold py-1 d-inline-block">
                                            Logged In <i class="bi bi-check-circle-fill text-success ms-1"></i>
                                        </span>
                                    @endif
                                </div>
                            </td>
                        </tr>
                        @empty
                        <tr>
                            <td colspan="6" class="text-center py-4 text-muted">No administrators found.</td>
                        </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
        </div>
    </div>

</div>
@endsection
