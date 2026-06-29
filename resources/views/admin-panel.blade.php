<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>Admin Dashboard</title>
    <link href="/site/css/bootstrap.min.css" rel="stylesheet">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.4.1/font/bootstrap-icons.css" rel="stylesheet">
    <style>
        body {
            font-family: 'Nunito', sans-serif;
            background-color: #f8f9fa;
        }

        #sidebarMenu {
            width: 260px;
            min-height: 100vh;
            transition: transform .25s ease;
        }

        #sidebarMenu .nav-link {
            color: rgba(255, 255, 255, .85);
        }

        #sidebarMenu .nav-link.active,
        #sidebarMenu .nav-link:hover {
            color: #fff;
            background-color: rgba(255, 255, 255, .08);
        }

        .content-wrapper {
            flex: 1;
            transition: margin .25s ease;
        }

        .sidebar-backdrop {
            display: none;
            position: fixed;
            inset: 0;
            background: rgba(0, 0, 0, .45);
            z-index: 1040;
        }

        @media (max-width: 991.98px) {
            #sidebarMenu {
                position: fixed;
                top: 0;
                left: 0;
                z-index: 1050;
                transform: translateX(-100%);
                min-height: 100vh;
            }

            #sidebarMenu.show {
                transform: translateX(0);
            }

            .content-wrapper {
                margin-left: 0;
            }

            .sidebar-backdrop.show {
                display: block;
            }
        }

        .card-transparent {
            background: rgba(255, 255, 255, .95);
            border: 1px solid rgba(231, 231, 231, .85);
        }
    </style>
</head>

<body>
    @include('sweetalert::alert')

    <div class="sidebar-backdrop" id="sidebarBackdrop"></div>

    <div class="d-flex">
        @include('common.admin-panel.sidebar')

        <div class="content-wrapper w-100">
            <header class="bg-white border-bottom shadow-sm py-3 px-4 d-flex align-items-center justify-content-between">
                <div class="d-flex align-items-center gap-3">
                    <button class="btn btn-outline-secondary d-lg-none" id="sidebarToggleBtn" type="button"><i class="bi bi-list"></i></button>
                    <div>
                        <h1 class="h5 mb-1">Dashboard</h1>
                        <p class="mb-0 text-muted">Overview of the latest metrics and activity.</p>
                    </div>
                </div>
                <div class="d-flex align-items-center gap-3">
                    <button class="btn btn-outline-secondary d-none d-md-inline-flex align-items-center gap-2" type="button"><i class="bi bi-bell"></i> Notifications</button>
                    <button class="btn btn-primary" type="button"><i class="bi bi-cloud-upload"></i> Export</button>
                </div>
            </header>

            <div class="pt-3">
                @yield('content')
            </div>
        </div>
    </div>

    <script>
        const sidebarToggleBtn = document.getElementById('sidebarToggleBtn');
        const sidebarCloseBtn = document.getElementById('sidebarCloseBtn');
        const sidebarMenu = document.getElementById('sidebarMenu');
        const sidebarBackdrop = document.getElementById('sidebarBackdrop');

        function openSidebar() {
            sidebarMenu.classList.add('show');
            sidebarBackdrop.classList.add('show');
        }

        function closeSidebar() {
            sidebarMenu.classList.remove('show');
            sidebarBackdrop.classList.remove('show');
        }

        sidebarToggleBtn.addEventListener('click', openSidebar);
        sidebarCloseBtn.addEventListener('click', closeSidebar);
        sidebarBackdrop.addEventListener('click', closeSidebar);
    </script>
</body>

</html>
