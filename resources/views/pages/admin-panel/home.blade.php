@extends("admin-panel")
@section("content")
<main class="p-4">
    <div class="row g-4 mb-5">
        <div class="col-12 col-sm-6 col-xl-4">
            <div class="card admin-card p-4 h-100 border-0 shadow-sm rounded-4 bg-white">
                <div class="d-flex align-items-center justify-content-between mb-2">
                    <div>
                        <h6 class="text-uppercase text-muted small fw-bold mb-1">Total Users</h6>
                        <h2 class="mb-0 fw-extrabold text-dark">{{ $totalUsers }}</h2>
                    </div>
                    <div class="rounded-circle p-3 d-flex align-items-center justify-content-center" style="background-color: rgba(52, 142, 56, 0.1); width: 60px; height: 60px;">
                        <i class="bi bi-people-fill text-success fs-3"></i>
                    </div>
                </div>
                <small class="text-muted"><i class="bi bi-shield-check text-success me-1"></i> Registered clients</small>
            </div>
        </div>

        <div class="col-12 col-sm-6 col-xl-4">
            <div class="card admin-card p-4 h-100 border-0 shadow-sm rounded-4 bg-white">
                <div class="d-flex align-items-center justify-content-between mb-2">
                    <div>
                        <h6 class="text-uppercase text-muted small fw-bold mb-1">Team Members</h6>
                        <h2 class="mb-0 fw-extrabold text-dark">{{ $totalTeam }}</h2>
                    </div>
                    <div class="rounded-circle p-3 d-flex align-items-center justify-content-center" style="background-color: rgba(13, 110, 253, 0.1); width: 60px; height: 60px;">
                        <i class="bi bi-person-badge-fill text-primary fs-3"></i>
                    </div>
                </div>
                <small class="text-muted"><i class="bi bi-check-circle-fill text-primary me-1"></i> Active staff group</small>
            </div>
        </div>

        <div class="col-12 col-sm-6 col-xl-4">
            <div class="card admin-card p-4 h-100 border-0 shadow-sm rounded-4 bg-white">
                <div class="d-flex align-items-center justify-content-between mb-2">
                    <div>
                        <h6 class="text-uppercase text-muted small fw-bold mb-1">Our Active Services</h6>
                        <h2 class="mb-0 fw-extrabold text-dark">{{ $totalServices }}</h2>
                    </div>
                    <div class="rounded-circle p-3 d-flex align-items-center justify-content-center" style="background-color: rgba(25, 135, 84, 0.1); width: 60px; height: 60px;">
                        <i class="bi bi-gear-fill text-success fs-3"></i>
                    </div>
                </div>
                <small class="text-muted"><i class="bi bi-tree-fill text-success me-1"></i> Gardening packages</small>
            </div>
        </div>
    </div>

    <h5 class="fw-bold text-dark mb-4"><i class="bi bi-calendar-check-fill text-success me-2"></i>Booking Status Overview</h5>
    <div class="row g-4 mb-4">
        <div class="col-12 col-md-4">
            <div class="card admin-card p-4 h-100 border-0 shadow-sm rounded-4 bg-white" style="border-left: 5px solid #ffc107 !important;">
                <div class="d-flex align-items-center justify-content-between">
                    <div>
                        <h6 class="text-uppercase text-muted small fw-bold mb-1">Pending Orders</h6>
                        <h2 class="mb-0 fw-bold text-warning">{{ $pendingBookings }}</h2>
                    </div>
                    <div class="p-3 bg-warning bg-opacity-10 rounded-3 text-warning">
                        <i class="bi bi-hourglass-split fs-3"></i>
                    </div>
                </div>
            </div>
        </div>

        <div class="col-12 col-md-4">
            <div class="card admin-card p-4 h-100 border-0 shadow-sm rounded-4 bg-white" style="border-left: 5px solid #0dcaf0 !important;">
                <div class="d-flex align-items-center justify-content-between">
                    <div>
                        <h6 class="text-uppercase text-muted small fw-bold mb-1">Approved/Active</h6>
                        <h2 class="mb-0 fw-bold text-info">{{ $approvedBookings }}</h2>
                    </div>
                    <div class="p-3 bg-info bg-opacity-10 rounded-3 text-info">
                        <i class="bi bi-check2-circle fs-3"></i>
                    </div>
                </div>
            </div>
        </div>

        <div class="col-12 col-md-4">
            <div class="card admin-card p-4 h-100 border-0 shadow-sm rounded-4 bg-white" style="border-left: 5px solid #198754 !important;">
                <div class="d-flex align-items-center justify-content-between">
                    <div>
                        <h6 class="text-uppercase text-muted small fw-bold mb-1">Jobs Completed (Done)</h6>
                        <h2 class="mb-0 fw-bold text-success">{{ $doneBookings }}</h2>
                    </div>
                    <div class="p-3 bg-success bg-opacity-10 rounded-3 text-success">
                        <i class="bi bi-shield-fill-check fs-3"></i>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <h5 class="fw-bold text-dark mb-4 mt-5"><i class="bi bi-inbox-fill text-success me-2"></i>Needs Your Attention</h5>
    <div class="row g-4 mb-4">
        <div class="col-12 col-md-6">
            <a href="{{ route('admin.messages.list') }}" class="text-decoration-none">
                <div class="card admin-card p-4 h-100 border-0 shadow-sm rounded-4 bg-white" style="border-left: 5px solid #dc3545 !important;">
                    <div class="d-flex align-items-center justify-content-between">
                        <div>
                            <h6 class="text-uppercase text-muted small fw-bold mb-1">Unread Messages</h6>
                            <h2 class="mb-0 fw-bold text-danger">{{ $unreadMessages }}</h2>
                        </div>
                        <div class="p-3 bg-danger bg-opacity-10 rounded-3 text-danger">
                            <i class="bi bi-envelope-exclamation-fill fs-3"></i>
                        </div>
                    </div>
                </div>
            </a>
        </div>
        <div class="col-12 col-md-6">
            <a href="{{ route('admin.reviews.list') }}" class="text-decoration-none">
                <div class="card admin-card p-4 h-100 border-0 shadow-sm rounded-4 bg-white" style="border-left: 5px solid #ffc107 !important;">
                    <div class="d-flex align-items-center justify-content-between">
                        <div>
                            <h6 class="text-uppercase text-muted small fw-bold mb-1">Reviews Waiting Approval</h6>
                            <h2 class="mb-0 fw-bold text-warning">{{ $pendingReviews }}</h2>
                        </div>
                        <div class="p-3 bg-warning bg-opacity-10 rounded-3 text-warning">
                            <i class="bi bi-star-half fs-3"></i>
                        </div>
                    </div>
                </div>
            </a>
        </div>
    </div>
</main>
@endsection
