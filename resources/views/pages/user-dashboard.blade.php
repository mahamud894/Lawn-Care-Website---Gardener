<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="utf-8">
    <title>User Dashboard - Gardener</title>
    <meta content="width=device-width, initial-scale=1.0" name="viewport">

    <link href="/site/css/bootstrap.min.css" rel="stylesheet">
    <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/5.10.0/css/all.min.css" rel="stylesheet">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.4.1/font/bootstrap-icons.css" rel="stylesheet">
    <link href="/site/css/style.css" rel="stylesheet">

    <style>
        body {
            background-color: #f4f7f6;
        }
        .dashboard-header {
            background: linear-gradient(135deg, #348E38 0%, #1e5621 100%);
            border-radius: 15px;
            padding: 30px;
            color: white;
            box-shadow: 0 4px 15px rgba(52, 142, 56, 0.2);
        }
        .dashboard-card {
            border: none;
            border-radius: 15px;
            box-shadow: 0 5px 15px rgba(0,0,0,0.04);
            transition: all 0.3s ease;
            background: #fff;
        }
        .dashboard-card:hover {
            transform: translateY(-3px);
            box-shadow: 0 8px 20px rgba(0,0,0,0.08);
        }
        .status-badge {
            padding: 6px 14px;
            border-radius: 30px;
            font-size: 0.85rem;
            font-weight: 600;
            text-transform: capitalize;
            display: inline-flex;
            align-items: center;
            gap: 5px;
        }
        /* ট্র্যাকিং কালার স্কিম */
        .badge-pending { background-color: #fff3cd; color: #856404; border: 1px solid #ffeeba; }
        .badge-approved { background-color: #d1ecf1; color: #0c5460; border: 1px solid #bee5eb; }
        .badge-done { background-color: #d4edda; color: #155724; border: 1px solid #c3e6cb; }
        .badge-rejected { background-color: #f8d7da; color: #721c24; border: 1px solid #f5c6cb; }

        .nav-pills .nav-link {
            color: #555;
            font-weight: 600;
            border-radius: 8px;
            padding: 10px 20px;
            transition: all 0.2s;
        }
        .nav-pills .nav-link.active {
            background-color: #348E38;
            color: white;
            box-shadow: 0 4px 10px rgba(52, 142, 56, 0.25);
        }
        .icon-shape {
            width: 55px;
            height: 55px;
            display: flex;
            align-items: center;
            justify-content: center;
            border-radius: 12px;
        }
    </style>
</head>

<body>
    <div class="container-fluid position-relative p-0">
        @include('common.site.navbar')
    </div>

    <div class="container py-5 mt-5">
        <div class="dashboard-header mb-5">
            <div class="row align-items-center">
                <div class="col-md-8">
                    <h2 class="fw-bold text-white mb-1">Welcome Back, {{ Auth::user()->name }}!</h2>
                    <p class="mb-0 text-white-50">Here you can track your lawn care requests, active services, and history.</p>
                </div>
                <div class="col-md-4 text-md-end mt-3 mt-md-0">
                    <a href="{{ route('quote') }}" class="btn btn-light text-success fw-bold py-2 px-4 rounded-pill shadow-sm">
                        <i class="fa fa-plus me-2"></i>Book A New Service
                    </a>
                </div>
            </div>
        </div>

        <div class="row g-4 mb-5">
            <div class="col-md-4">
                <div class="card dashboard-card p-4 d-flex flex-row align-items-center justify-content-between">
                    <div>
                        <h6 class="text-muted text-uppercase small fw-bold mb-1">Pending Requests</h6>
                        <h2 class="fw-bold mb-0 text-dark">{{ $bookings->where('status', 'pending')->count() }}</h2>
                    </div>
                    <div class="icon-shape bg-warning bg-opacity-10 text-warning"><i class="fa fa-spinner fa-spin fa-2x"></i></div>
                </div>
            </div>
            <div class="col-md-4">
                <div class="card dashboard-card p-4 d-flex flex-row align-items-center justify-content-between">
                    <div>
                        <h6 class="text-muted text-uppercase small fw-bold mb-1">Accepted / Active</h6>
                        <h2 class="fw-bold mb-0 text-dark">{{ $bookings->where('status', 'approved')->count() }}</h2>
                    </div>
                    <div class="icon-shape bg-info bg-opacity-10 text-info"><i class="fa fa-check-circle fa-2x"></i></div>
                </div>
            </div>
            <div class="col-md-4">
                <div class="card dashboard-card p-4 d-flex flex-row align-items-center justify-content-between">
                    <div>
                        <h6 class="text-muted text-uppercase small fw-bold mb-1">My Job History (Done)</h6>
                        <h2 class="fw-bold mb-0 text-dark">{{ $bookings->where('status', 'done')->count() }}</h2>
                    </div>
                    <div class="icon-shape bg-success bg-opacity-10 text-success"><i class="fa fa-history fa-2x"></i></div>
                </div>
            </div>
        </div>

        <div class="bg-white p-3 rounded-3 shadow-sm mb-4">
            <ul class="nav nav-pills gap-2" id="dashboardTabs" role="tablist">
                <li class="nav-item" role="presentation">
                    <button class="nav-link active" id="all-tab" data-bs-toggle="tab" data-bs-target="#all" type="button" role="tab">All Orders</button>
                </li>
                <li class="nav-item" role="presentation">
                    <button class="nav-link" id="pending-tab" data-bs-toggle="tab" data-bs-target="#pending" type="button" role="tab">Pending</button>
                </li>
                <li class="nav-item" role="presentation">
                    <button class="nav-link" id="active-tab" data-bs-toggle="tab" data-bs-target="#active" type="button" role="tab">Accepted</button>
                </li>
                <li class="nav-item" role="presentation">
                    <button class="nav-link" id="history-tab" data-bs-toggle="tab" data-bs-target="#history" type="button" role="tab">Job History (Done)</button>
                </li>
            </ul>
        </div>

        <div class="tab-content" id="dashboardTabsContent">
            <div class="tab-pane fade show active" id="all" role="tabpanel">
                @include('pages.partials.booking-table', ['filteredBookings' => $bookings])
            </div>
            <div class="tab-pane fade" id="pending" role="tabpanel">
                @include('pages.partials.booking-table', ['filteredBookings' => $bookings->where('status', 'pending')])
            </div>
            <div class="tab-pane fade" id="active" role="tabpanel">
                @include('pages.partials.booking-table', ['filteredBookings' => $bookings->where('status', 'approved')])
            </div>
            <div class="tab-pane fade" id="history" role="tabpanel">
                @include('pages.partials.booking-table', ['filteredBookings' => $bookings->where('status', 'done')])
            </div>
        </div>
    </div>

    @include('common.site.footer')

    <script src="https://code.jquery.com/jquery-3.4.1.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.0.0/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>
