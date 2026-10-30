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
            <a class="nav-link" aria-current="page" href="/admin-panel"><i
                    class="bi bi-speedometer2 me-2"></i>Dashboard</a>
        </li>
        <li class="nav-item">
            <a class="nav-link" aria-current="page" href="/admin-panel/add-service"><i
                    class="bi bi-bag-plus-fill me-2"></i></i>Add Service</a>
        </li>
        <li class="nav-item">
            <a class="nav-link" aria-current="page" href="/admin-panel/service-list"><i
                    class="bi bi-person-lines-fill me-2"></i></i>Service List</a>
        </li>
        <li class="nav-item">
            <a class="nav-link" aria-current="page" href="/admin-panel/booking-list"><i
                    class="bi bi-journal-plus me-2"></i></i>Booking List</a>
        </li>
        <li class="nav-item">
            <a class="nav-link" aria-current="page" href="/admin-panel/add-team"><i
                    class="bi bi-person-plus-fill me-2"></i></i>Add Team Member</a>
        </li>
        <li class="nav-item">
            <a class="nav-link" aria-current="page" href="/admin-panel/team-list"><i
                    class="bi bi-person-lines-fill me-2"></i></i>Team Members</a>
        </li>
        <li class="nav-item">
            <a class="nav-link {{ Route::is('admin.projects.*') ? 'active' : '' }}" href="{{ route('admin.projects.list') }}">
                <i class="bi bi-images me-2"></i>Projects
            </a>
        </li>
        <li class="nav-item">
            <a class="nav-link {{ Route::is('admin.messages.*') ? 'active' : '' }}" href="{{ route('admin.messages.list') }}">
                <i class="bi bi-envelope-fill me-2"></i>Messages
                @php($unread = \App\Models\ContactMessage::where('isRead', false)->count())
                @if($unread)<span class="badge bg-danger ms-1">{{ $unread }}</span>@endif
            </a>
        </li>
        <li class="nav-item">
            <a class="nav-link {{ Route::is('admin.reviews.*') ? 'active' : '' }}" href="{{ route('admin.reviews.list') }}">
                <i class="bi bi-star-fill me-2"></i>Reviews
            </a>
        </li>
        <li class="nav-item">
            <a class="nav-link {{ Route::is('admin.users.list') ? 'active' : '' }}"
                href="{{ route('admin.users.list') }}">
                <i class="bi bi-people-fill me-2"></i>Users
            </a>
        </li>
        <li class="nav-item">
            <form action="{{ route('logout') }}" method="POST">
                @csrf
                <button type="submit" class="nav-link btn btn-link text-start w-100"><i class="bi bi-box-arrow-right me-2"></i>Logout</button>
            </form>
        </li>
    </ul>

    <div class="px-3 mb-2">
        <a href="{{ route('home') }}" target="_blank"
            class="btn btn-outline-success btn-sm w-100 rounded-pill py-2 d-flex align-items-center justify-content-center gap-2 fw-bold"
            style="font-size: 0.85rem; border-style: dashed; color: #198754;">
            <i class="bi bi-box-arrow-up-right"></i> View Live Site
        </a>
    </div>
    <div class="mt-auto pt-3 border-top border-secondary">
        <div class="d-flex align-items-center">
            <div class="me-3 bg-primary rounded-circle d-flex align-items-center justify-content-center"
                style="width:44px;height:44px;">
                <i class="bi bi-person-fill"></i>
            </div>
            <div>
                <div class="fw-semibold">{{ Auth::user()->name ?? 'Admin User' }}</div>
                <small class="text-muted">Active</small>
            </div>
        </div>
    </div>
</nav>
