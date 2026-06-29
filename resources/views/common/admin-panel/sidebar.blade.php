<nav id="sidebarMenu" class="bg-dark text-white p-3">
            <div class="d-flex align-items-center mb-4">
                <div class="fs-4 fw-bold">Admin Panel</div>
                <button class="btn btn-sm btn-outline-light ms-auto d-lg-none" id="sidebarCloseBtn" type="button">
                    <i class="bi bi-x-lg"></i>
                </button>
            </div>
            <p class="text-muted small">Manage users, services, reports and settings</p>
            <hr class="border-secondary">
            <ul class="nav nav-pills flex-column mb-4">
                <li class="nav-item">
                    <a class="nav-link" aria-current="page" href="/admin-panel"><i class="bi bi-speedometer2 me-2"></i>Dashboard</a>
                </li>
                <li class="nav-item">
                    <a class="nav-link" aria-current="page" href="/admin-panel/add-service"><i class="bi bi-bag-plus-fill me-2"></i></i>Add Service</a>
                </li>
                <li class="nav-item">
                    <a class="nav-link" aria-current="page" href="/admin-panel/service-list"><i class="bi bi-person-lines-fill me-2"></i></i>Service List</a>
                </li>
                <li class="nav-item">
                    <a class="nav-link" aria-current="page" href="/admin-panel/booking-list"><i class="bi bi-journal-plus me-2"></i></i>Booking List</a>
                </li>
                <li class="nav-item">
                    <a class="nav-link" aria-current="page" href="/admin-panel/add-team"><i class="bi bi-person-plus-fill me-2"></i></i>Add Team</a>
                </li>
                <li class="nav-item">
                    <a class="nav-link" aria-current="page" href="/admin-panel/team-list"><i class="bi bi-person-lines-fill me-2"></i></i>Team List</a>
                </li>
                <li class="nav-item">
                    <a class="nav-link {{ Route::is('admin.users.list') ? 'active' : '' }}" href="{{ route('admin.users.list') }}">
                       <i class="bi bi-people-fill me-2"></i>Users
                    </a>
                </li>
                <li class="nav-item">
                    <a class="nav-link" href="#"><i class="bi bi-card-checklist me-2"></i>Reports</a>
                </li>
                <li class="nav-item">
                    <a class="nav-link" href="#"><i class="bi bi-gear me-2"></i>Settings</a>
                </li>
                <li class="nav-item">
                    <a class="nav-link" href="/logout"><i class="bi bi-gear me-2"></i>Logout</a>
                </li>
            </ul>
            <div class="mt-auto pt-3 border-top border-secondary">
                <div class="d-flex align-items-center">
                    <div class="me-3 bg-primary rounded-circle d-flex align-items-center justify-content-center" style="width:44px;height:44px;">
                        <i class="bi bi-person-fill"></i>
                    </div>
                    <div>
                        <div class="fw-semibold">Admin User</div>
                        <small class="text-muted">Last active 2h ago</small>
                    </div>
                </div>
            </div>
        </nav>
