<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>@yield('title', 'Teacher Dashboard') - School ERP</title>

    <!-- Google Fonts: DM Sans + DM Serif Display -->
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=DM+Sans:wght@300;400;500;600&family=DM+Serif+Display&display=swap" rel="stylesheet">

    <!-- Bootstrap 5 CSS -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <!-- Font Awesome 6 -->
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">

    <style>
        /* ─── Design Tokens - Same as Admin ─── */
        :root {
            --sidebar-w: 256px;

            /* Palette */
            --ink-900: #111318;
            --ink-700: #2d3139;
            --ink-500: #6b7280;
            --ink-300: #b0b7c3;
            --ink-100: #f0f2f5;
            --ink-50:  #f7f8fa;

            --accent:        #2563eb;
            --accent-light:  #eff4ff;
            --accent-mid:    #bfdbfe;
            --accent-dark:   #1d4ed8;

            --success:       #16a34a;
            --success-light: #f0fdf4;
            --warning:       #d97706;
            --warning-light: #fffbeb;
            --danger:        #dc2626;
            --danger-light:  #fff1f1;

            /* Sidebar specific */
            --sb-bg:         #ffffff;
            --sb-border:     #e5e7eb;
            --sb-text:       #374151;
            --sb-muted:      #9ca3af;
            --sb-hover-bg:   #f3f4f6;
            --sb-active-bg:  #eff4ff;
            --sb-active-txt: #2563eb;

            /* Surfaces */
            --card-bg:       #ffffff;
            --page-bg:       #f7f8fa;

            /* Radii */
            --r-sm: 6px;
            --r-md: 10px;
            --r-lg: 14px;

            /* Shadows */
            --shadow-xs: 0 1px 2px rgba(0,0,0,.05);
            --shadow-sm: 0 2px 6px rgba(0,0,0,.06);
            --shadow-md: 0 4px 16px rgba(0,0,0,.08);

            /* Font */
            --font: 'DM Sans', system-ui, sans-serif;
        }

        * { box-sizing: border-box; }

        body {
            font-family: var(--font);
            font-size: 14px;
            background: var(--page-bg);
            color: var(--ink-700);
            overflow-x: hidden;
            -webkit-font-smoothing: antialiased;
        }

        /* ═══════════════════════════════════════════
           SIDEBAR - Same as Admin
        ═══════════════════════════════════════════ */
        .sidebar {
            position: fixed;
            top: 0; left: 0; bottom: 0;
            width: var(--sidebar-w);
            background: var(--sb-bg);
            border-right: 1px solid var(--sb-border);
            display: flex;
            flex-direction: column;
            z-index: 1000;
            transition: transform .28s cubic-bezier(.4,0,.2,1);
            overflow: hidden;
        }

        /* Logo bar */
        .sidebar-brand {
            display: flex;
            align-items: center;
            gap: .75rem;
            padding: 1.25rem 1.5rem;
            border-bottom: 1px solid var(--sb-border);
            background: linear-gradient(135deg, #eff4ff 0%, #ffffff 100%);
        }

        .brand-logo {
            width: 42px; height: 42px;
            border-radius: 10px;
            background: linear-gradient(135deg, var(--accent) 0%, var(--accent-dark) 100%);
            display: grid; place-items: center;
            color: #fff;
            font-size: 1.5rem;
            box-shadow: 0 4px 12px rgba(37, 99, 235, 0.2);
        }

        .brand-text h5 {
            margin: 0;
            font-size: 1.1rem;
            font-weight: 700;
            color: var(--ink-900);
        }

        .brand-text small {
            font-size: 0.75rem;
            color: var(--sb-muted);
        }

        /* Scrollable nav area */
        .sidebar-nav {
            flex: 1;
            overflow-y: auto;
            padding: 1.25rem 1rem;
        }

        .sidebar-nav::-webkit-scrollbar { width: 6px; }
        .sidebar-nav::-webkit-scrollbar-thumb { background: #e5e7eb; border-radius: 3px; }

        .nav-label {
            font-size: 0.7rem;
            text-transform: uppercase;
            letter-spacing: 1px;
            color: var(--sb-muted);
            margin: 1.5rem 1.25rem .75rem;
            font-weight: 600;
        }

        .nav {
            display: flex;
            flex-direction: column;
            gap: .25rem;
            list-style: none;
            padding: 0;
            margin: 0;
        }

        .nav-link {
            display: flex;
            align-items: center;
            gap: .75rem;
            padding: .75rem 1rem;
            border-radius: var(--r-md);
            color: var(--sb-text);
            text-decoration: none;
            transition: all .2s ease;
            font-weight: 500;
            font-size: 0.9rem;
        }

        .nav-link:hover {
            background: var(--sb-hover-bg);
            color: var(--ink-900);
        }

        .nav-link.active {
            background: var(--sb-active-bg);
            color: var(--sb-active-txt);
            font-weight: 600;
        }

        .nav-icon {
            width: 20px;
            text-align: center;
            font-size: 1.1rem;
        }

        /* Footer with logout */
        .sidebar-footer {
            padding: 1rem;
            border-top: 1px solid var(--sb-border);
        }

        .btn-logout {
            width: 100%;
            display: flex;
            align-items: center;
            justify-content: center;
            gap: .5rem;
            padding: .65rem;
            border: 1px solid var(--sb-border);
            background: #fff;
            color: var(--danger);
            border-radius: var(--r-md);
            font-weight: 500;
            transition: all .2s;
        }

        .btn-logout:hover {
            background: var(--danger-light);
            border-color: var(--danger);
        }

        /* Main Content Area */
        .content-area {
            margin-left: var(--sidebar-w);
            min-height: 100vh;
            background: var(--page-bg);
            transition: margin-left .28s cubic-bezier(.4,0,.2,1);
        }

        /* Top Navigation Bar */
        .top-navbar {
            position: sticky;
            top: 0;
            z-index: 900;
            background: #fff;
            border-bottom: 1px solid var(--sb-border);
            padding: 0.875rem 1.5rem;
            display: flex;
            align-items: center;
            justify-content: space-between;
            box-shadow: var(--shadow-xs);
        }

        .menu-toggle {
            display: none;
            background: none;
            border: none;
            font-size: 1.5rem;
            color: var(--ink-700);
            cursor: pointer;
        }

        .user-menu {
            display: flex;
            align-items: center;
            gap: 1rem;
        }

        .user-avatar {
            width: 38px; height: 38px;
            border-radius: 50%;
            background: linear-gradient(135deg, var(--accent) 0%, var(--accent-dark) 100%);
            display: grid; place-items: center;
            color: #fff;
            font-weight: 600;
            font-size: 0.9rem;
        }

        /* Cards */
        .card {
            background: var(--card-bg);
            border: 1px solid var(--ink-100);
            border-radius: var(--r-lg);
            box-shadow: var(--shadow-sm);
        }

        .card-header {
            background: #fff;
            border-bottom: 1px solid var(--ink-100);
            padding: 1rem 1.25rem;
            font-weight: 600;
        }

        .card-body { padding: 1.25rem; }

        /* Buttons */
        .btn {
            border-radius: var(--r-md);
            padding: 0.5rem 1.25rem;
            font-weight: 500;
            transition: all 0.2s;
        }

        .btn-primary {
            background: var(--accent);
            border-color: var(--accent);
        }

        .btn-primary:hover {
            background: var(--accent-dark);
            border-color: var(--accent-dark);
        }

        /* Responsive */
        @media (max-width: 992px) {
            .sidebar { transform: translateX(-100%); }
            .sidebar.active { transform: translateX(0); }
            .content-area { margin-left: 0; }
            .menu-toggle { display: block; }
        }
    </style>

    @stack('styles')
</head>
<body>
    <div class="wrapper">
        <!-- Sidebar -->
        <nav class="sidebar" id="sidebar">
            <div class="sidebar-brand">
                <div class="brand-logo">
                    <i class="fas fa-chalkboard-teacher"></i>
                </div>
                <div class="brand-text">
                    <h5>School ERP</h5>
                    <small>Teacher Portal</small>
                </div>
            </div>

            @include('layouts.sidebar')

            <div class="sidebar-footer">
                <form method="POST" action="{{ route('logout') }}">
                    @csrf
                    <button type="submit" class="btn-logout">
                        <i class="fas fa-sign-out-alt"></i>
                        Logout
                    </button>
                </form>
            </div>
        </nav>

        <!-- Content Area -->
        <div class="content-area">
            <!-- Top Navbar -->
            <header class="top-navbar">
                <button class="menu-toggle" id="sidebarToggle">
                    <i class="fas fa-bars"></i>
                </button>

                <div class="user-menu">
                    <div class="text-end">
                        <div class="fw-semibold">{{ auth()->user()->name }}</div>
                        <small class="text-muted">Teacher</small>
                    </div>
                    <div class="user-avatar">
                        {{ substr(auth()->user()->name, 0, 1) }}
                    </div>
                </div>
            </header>

            <!-- Page Content -->
            <main class="p-4">
                @yield('content')
            </main>
        </div>
    </div>

    <!-- Bootstrap 5 JS -->
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>

    <script>
        // Sidebar toggle for mobile
        document.getElementById('sidebarToggle')?.addEventListener('click', function() {
            document.getElementById('sidebar').classList.toggle('active');
        });

        // Close sidebar on outside click (mobile)
        document.addEventListener('click', function(e) {
            const sidebar = document.getElementById('sidebar');
            const toggle = document.getElementById('sidebarToggle');
            if (window.innerWidth <= 992 && 
                !sidebar.contains(e.target) && 
                !toggle.contains(e.target)) {
                sidebar.classList.remove('active');
            }
        });
    </script>

    @stack('scripts')
</body>
</html>
        :root {
            --sidebar-width: 260px;
            --sidebar-bg: #1a1d20;
            --sidebar-hover: #2d3238;
            --sidebar-active: #000;
            --primary-color: #007bff;
        }
        
        body {
            overflow-x: hidden;
            background-color: #f8f9fa;
            font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif;
        }
        
        .wrapper {
            display: flex;
            width: 100%;
            min-height: 100vh;
        }
        
        /* Sidebar Styles - Matching Admin Layout */
        #sidebar {
            position: fixed;
            top: 0;
            left: 0;
            bottom: 0;
            width: var(--sidebar-width);
            background: linear-gradient(180deg, var(--sidebar-bg) 0%, #0f1419 100%);
            z-index: 1000;
            transition: transform 0.3s ease-in-out;
            overflow-y: auto;
            overflow-x: hidden;
            box-shadow: 2px 0 10px rgba(0,0,0,0.1);
        }
        
        #sidebar.active {
            margin-left: calc(-1 * var(--sidebar-width));
        }
        
        .sidebar-header {
            padding: 1.5rem 1rem;
            border-bottom: 1px solid rgba(255, 255, 255, 0.1);
            background: rgba(0,0,0,0.2);
        }
        
        .sidebar-header h3 {
            color: #fff;
            font-weight: 700;
            margin: 0;
            font-size: 1.3rem;
            text-shadow: 0 1px 3px rgba(0,0,0,0.3);
        }
        
        .sidebar-header small {
            color: #94a3b8;
            font-size: 0.75rem;
        }
        
        #sidebar .nav {
            padding: 1rem 0;
        }
        
        #sidebar .nav-link {
            color: #adb5bd;
            padding: 0.75rem 1.25rem;
            margin: 0.25rem 0.75rem;
            border-radius: 8px;
            transition: all 0.3s ease;
            display: flex;
            align-items: center;
            font-weight: 500;
            border: 1px solid transparent;
            text-decoration: none;
        }
        
        #sidebar .nav-link i {
            width: 20px;
            margin-right: 0.75rem;
            font-size: 1.1rem;
            text-align: center;
        }
        
        #sidebar .nav-link:hover {
            color: #fff;
            background: linear-gradient(135deg, var(--sidebar-hover) 0%, #3a4248 100%);
            border-color: rgba(255,255,255,0.1);
            transform: translateX(3px);
        }
        
        #sidebar .nav-link.active {
            color: #fff;
            background: linear-gradient(135deg, #007bff 0%, #0056b3 100%);
            border-color: #007bff;
            box-shadow: 0 2px 8px rgba(0,123,255,0.3);
        }
        
        .notification-badge {
            position: absolute;
            top: 5px;
            right: 5px;
            font-size: 0.65rem;
            padding: 2px 6px;
            border-radius: 50%;
            background: #dc3545;
            color: #fff;
        }
        
        /* Main Content */
        #content {
            margin-left: var(--sidebar-width);
            min-height: 100vh;
            transition: all 0.3s;
        }
        
        #content.active {
            margin-left: 0;
        }
        
        .topbar {
            background: #fff;
            padding: 0.75rem 1.5rem;
            box-shadow: 0 1px 3px rgba(0,0,0,0.1);
            display: flex;
            justify-content: space-between;
            align-items: center;
        }
        
        .card {
            border: none;
            border-radius: 10px;
            box-shadow: 0 1px 3px rgba(0,0,0,0.1);
            margin-bottom: 1.5rem;
        }
        
        .card-header {
            background: #fff;
            border-bottom: 1px solid #e9ecef;
            padding: 1rem 1.25rem;
            font-weight: 600;
        }
        
        .btn-primary {
            background: linear-gradient(135deg, #007bff 0%, #0056b3 100%);
            border: none;
            padding: 0.5rem 1.25rem;
            border-radius: 6px;
        }
        
        .btn-primary:hover {
            background: linear-gradient(135deg, #0056b3 0%, #004494 100%);
        }
        
        /* Table Styles */
        .table {
            margin-bottom: 0;
        }
        
        .table thead th {
            background: #f8f9fa;
            border-bottom: 2px solid #dee2e6;
            font-weight: 600;
            color: #495057;
            padding: 0.75rem;
        }
        
        .table tbody td {
            padding: 0.75rem;
            vertical-align: middle;
        }
        
        .table-hover tbody tr:hover {
            background-color: #f8f9fa;
        }
        
        /* Form Styles */
        .form-control:focus, .form-select:focus {
            border-color: #007bff;
            box-shadow: 0 0 0 0.2rem rgba(0,123,255,0.15);
        }
        
        /* Page Header */
        .page-header {
            background: #fff;
            padding: 1.5rem;
            margin-bottom: 1.5rem;
            border-radius: 10px;
            box-shadow: 0 1px 3px rgba(0,0,0,0.1);
        }
        
        .page-header h2 {
            margin: 0;
            font-size: 1.5rem;
            font-weight: 600;
            color: #212529;
        }

        /* Pagination Styles - Comprehensive Design */
        .pagination {
            gap: 6px;
            flex-wrap: wrap;
            margin: 0;
        }

        .pagination .page-item {
            border-radius: 8px;
            transition: all 0.3s cubic-bezier(0.4, 0, 0.2, 1);
        }

        .pagination .page-item:hover {
            transform: translateY(-2px);
        }

        .pagination .page-link {
            border: 2px solid #e9ecef;
            color: #495057;
            padding: 0.5rem 0.875rem;
            border-radius: 8px;
            transition: all 0.3s ease;
            display: flex;
            align-items: center;
            gap: 0.375rem;
            font-weight: 500;
            background: #fff;
            text-decoration: none;
        }

        .pagination .page-link:hover {
            background: linear-gradient(135deg, #007bff 0%, #0056b3 100%);
            color: #fff;
            border-color: #007bff;
            transform: translateY(-2px);
            box-shadow: 0 4px 12px rgba(0,123,255,0.35);
        }

        .pagination .page-item.active .page-link {
            background: linear-gradient(135deg, #007bff 0%, #0056b3 100%);
            border-color: #007bff;
            color: #fff;
            font-weight: 700;
            box-shadow: 0 4px 12px rgba(0,123,255,0.35);
            transform: translateY(-2px);
        }

        .pagination .page-item.disabled .page-link {
            color: #adb5bd;
            background: #f8f9fa;
            border-color: #e9ecef;
            cursor: not-allowed;
            opacity: 0.6;
            transform: none;
            box-shadow: none;
        }

        .pagination .page-item.disabled .page-link:hover {
            background: #f8f9fa;
            color: #adb5bd;
            border-color: #e9ecef;
            transform: none;
            box-shadow: none;
        }

        /* Pagination Sizes */
        .pagination-sm .page-link {
            padding: 0.375rem 0.625rem;
            font-size: 0.875rem;
            border-radius: 6px;
        }

        .pagination-lg .page-link {
            padding: 0.75rem 1.125rem;
            font-size: 1rem;
            border-radius: 10px;
        }

        /* Pagination Info */
        .pagination-info {
            color: #6c757d;
            font-size: 0.875rem;
            font-weight: 500;
            display: flex;
            align-items: center;
            gap: 0.5rem;
        }

        .pagination-info strong {
            color: #0d6efd;
            font-weight: 600;
        }

        /* Pagination Wrapper */
        .pagination-wrapper {
            display: flex;
            justify-content: flex-end;
            align-items: center;
        }

        @media (max-width: 767px) {
            .pagination-wrapper {
                justify-content: center;
                width: 100%;
            }
            
            .pagination-wrapper .pagination {
                justify-content: center;
            }
        }

        /* Mobile Responsive */
        @media (max-width: 576px) {
            .pagination {
                gap: 4px;
            }

            .pagination .page-link {
                padding: 0.375rem 0.5rem;
                font-size: 0.8rem;
                border-width: 1px;
            }

            .pagination .page-link span.d-none {
                display: none !important;
            }

            .pagination-info {
                font-size: 0.75rem;
                flex-direction: column;
                align-items: flex-start;
                gap: 0.25rem;
            }
        }

        /* Pagination with Borders */
        .pagination-bordered .page-link {
            border-width: 2px;
        }

        .pagination-bordered .page-item.active .page-link {
            border-color: #007bff;
        }

        /* Pagination Shadows */
        .pagination-shadow .page-link {
            box-shadow: 0 2px 4px rgba(0,0,0,0.08);
        }

        .pagination-shadow .page-item:hover .page-link {
            box-shadow: 0 4px 12px rgba(0,123,255,0.25);
        }

        /* Pagination Rounded */
        .pagination-rounded .page-item {
            border-radius: 50%;
        }

        .pagination-rounded .page-link {
            border-radius: 50%;
            width: 36px;
            height: 36px;
            justify-content: center;
            padding: 0;
        }

        .pagination-rounded.pagination-sm .page-link {
            width: 32px;
            height: 32px;
        }

        .pagination-rounded.pagination-lg .page-link {
            width: 42px;
            height: 42px;
        }

        /* Sidebar Navigation Styles */
        #sidebar ul.components {
            padding: 0;
            margin: 0;
            list-style: none;
        }

        #sidebar ul.components li {
            margin: 0;
            padding: 0;
        }

        #sidebar ul.components li a {
            color: #adb5bd;
            padding: 0.75rem 1.25rem;
            margin: 0.25rem 0.75rem;
            border-radius: 8px;
            display: flex;
            align-items: center;
            font-weight: 500;
            text-decoration: none;
            transition: all 0.3s;
        }

        #sidebar ul.components li a:hover,
        #sidebar ul.components li a.active {
            color: #fff;
            background: linear-gradient(135deg, var(--sidebar-hover) 0%, #3a4248 100%);
        }

        #sidebar ul.components li a i {
            width: 20px;
            margin-right: 0.75rem;
            font-size: 1.1rem;
            text-align: center;
        }

        .sidebar-heading {
            padding: 15px 20px 8px;
            font-size: 0.7rem;
            font-weight: 700;
            text-transform: uppercase;
            letter-spacing: 1px;
            color: #64748b;
        }

        /* Dropdown in sidebar */
        .sidebar-dropdown {
            background: rgba(0,0,0,0.1);
        }

        .sidebar-dropdown .dropdown-menu {
            background: rgba(0,0,0,0.2);
            border: none;
            padding: 0;
        }

        .sidebar-dropdown .dropdown-item {
            color: #adb5bd;
            padding: 0.6rem 1rem 0.6rem 3.5rem;
            text-decoration: none;
        }

        .sidebar-dropdown .dropdown-item:hover {
            background: var(--sidebar-hover);
            color: #fff;
        }

        /* Submenu Styles */
        .sidebar-dropdown-toggle {
            position: relative;
        }

        .sidebar-dropdown-toggle .bi-chevron-down {
            font-size: 0.75rem;
            transition: transform 0.3s ease;
        }

        .sidebar-dropdown-toggle[aria-expanded="true"] .bi-chevron-down {
            transform: rotate(180deg);
        }

        #sidebar ul.components ul.collapse {
            background: rgba(0,0,0,0.15);
            padding: 0;
            margin: 0;
            list-style: none;
        }

        #sidebar ul.components ul.collapse li a {
            padding: 0.6rem 1rem 0.6rem 3.5rem;
            font-size: 0.9rem;
            border-left: 3px solid transparent;
        }

        #sidebar ul.components ul.collapse li a:hover,
        #sidebar ul.components ul.collapse li a.active {
            background: rgba(255,255,255,0.05);
            border-left-color: #007bff;
        }

        /* Responsive */
        @media (max-width: 768px) {
            #sidebar {
                margin-left: calc(-1 * var(--sidebar-width));
            }

            #sidebar.active {
                margin-left: 0;
            }

            #content {
                margin-left: 0;
            }
        }
    </style>

    @yield('styles')
    @stack('styles')
</head>
<body>
    <div class="wrapper">
        <!-- Sidebar -->
        <nav id="sidebar">
            <div class="sidebar-header">
                <h3><i class="bi bi-mortarboard"></i> School ERP</h3>
                <small>Teacher Portal</small>
            </div>

            <ul class="list-unstyled components">
                <!-- Dashboard -->
                <li>
                    <a href="{{ route('teacher.dashboard') }}" class="{{ request()->routeIs('teacher.dashboard') ? 'active' : '' }}">
                        <i class="bi bi-speedometer2"></i>
                        <span>Dashboard</span>
                    </a>
                </li>

                <!-- My Profile -->
                <li>
                    <a href="{{ route('teacher.profile') }}" class="{{ request()->routeIs('teacher.profile*') ? 'active' : '' }}">
                        <i class="bi bi-person-circle"></i>
                        <span>My Profile</span>
                    </a>
                </li>

                <div class="sidebar-heading">Academic</div>

                <!-- My Divisions -->
                <li>
                    <a href="{{ route('teacher.divisions.index') }}" class="{{ request()->routeIs('teacher.divisions.*') ? 'active' : '' }}">
                        <i class="bi bi-collection"></i>
                        <span>My Divisions</span>
                    </a>
                </li>

                <div class="sidebar-heading">Assessment</div>

                <!-- Attendance -->
                <li>
                    <a href="#" class="sidebar-dropdown-toggle" data-bs-toggle="collapse" data-bs-target="#attendanceSubmenu" aria-expanded="{{ request()->routeIs('teacher.attendance.*') ? 'true' : 'false' }}">
                        <i class="bi bi-calendar-check"></i>
                        <span>Attendance</span>
                        <i class="bi bi-chevron-down ms-auto"></i>
                    </a>
                    <ul class="collapse {{ request()->routeIs('teacher.attendance.*') ? 'show' : '' }}" id="attendanceSubmenu">
                        <li>
                            <a href="{{ route('teacher.attendance.index') }}" class="{{ request()->routeIs('teacher.attendance.index') ? 'active' : '' }}">
                                <i class="bi bi-house"></i>
                                <span>Dashboard</span>
                            </a>
                        </li>
                        <li>
                            <a href="{{ route('teacher.attendance.history') }}" class="{{ request()->routeIs('teacher.attendance.history') ? 'active' : '' }}">
                                <i class="bi bi-clock-history"></i>
                                <span>History</span>
                            </a>
                        </li>
                        <li>
                            <a href="{{ route('teacher.attendance.report') }}" class="{{ request()->routeIs('teacher.attendance.report') ? 'active' : '' }}">
                                <i class="bi bi-graph-up"></i>
                                <span>Reports</span>
                            </a>
                        </li>
                    </ul>
                </li>

                <!-- Marks Entry -->
                <li>
                    <a href="#" class="sidebar-dropdown-toggle" data-bs-toggle="collapse" data-bs-target="#marksSubmenu" aria-expanded="{{ request()->routeIs('examinations.*') || request()->routeIs('teacher.results.*') ? 'true' : 'false' }}">
                        <i class="bi bi-clipboard-data"></i>
                        <span>Marks Entry</span>
                        <i class="bi bi-chevron-down ms-auto"></i>
                    </a>
                    <ul class="collapse {{ request()->routeIs('examinations.*') || request()->routeIs('teacher.results.*') ? 'show' : '' }}" id="marksSubmenu">
                        <li>
                            <a href="{{ route('teacher.results.index') }}" class="{{ request()->routeIs('teacher.results.index') ? 'active' : '' }}">
                                <i class="bi bi-bar-chart"></i>
                                <span>Teacher Results</span>
                            </a>
                        </li>
                    </ul>
                </li>
            </ul>

            <div class="mt-auto p-3 border-top border-secondary">
                <form action="{{ route('logout') }}" method="POST">
                    @csrf
                    <button type="submit" class="btn btn-outline-light w-100">
                        <i class="bi bi-box-arrow-right"></i> Logout
                    </button>
                </form>
            </div>
        </nav>

        <!-- Page Content -->
        <div id="content">
            <!-- Top Navbar -->
            <nav class="navbar navbar-expand-lg">
                <div class="container-fluid">
                    <button type="button" id="sidebarCollapse" class="navbar-btn">
                        <i class="bi bi-list"></i>
                    </button>

                    <div class="ms-auto d-flex align-items-center">
                        <!-- Notifications -->
                        <button class="btn btn-link me-3 position-relative">
                            <i class="bi bi-bell" style="font-size: 1.3rem;"></i>
                            <span class="position-absolute top-0 start-100 translate-middle badge rounded-pill bg-danger">
                                3
                            </span>
                        </button>

                        <!-- User Info -->
                        <div class="dropdown">
                            <a href="#" class="d-flex align-items-center text-decoration-none dropdown-toggle user-info" 
                               data-bs-toggle="dropdown">
                                <div class="user-avatar">
                                    {{ substr(auth()->user()->name, 0, 1) }}
                                </div>
                                <div class="ms-2 d-none d-md-block">
                                    <div class="fw-semibold">{{ auth()->user()->name }}</div>
                                    <small class="text-muted">{{ auth()->user()->roles->first()->name ?? 'Teacher' }}</small>
                                </div>
                            </a>
                            <ul class="dropdown-menu dropdown-menu-end">
                                <li><a class="dropdown-item" href="{{ route('teacher.profile') }}">
                                    <i class="bi bi-person me-2"></i> My Profile
                                </a></li>
                                <li><a class="dropdown-item" href="#">
                                    <i class="bi bi-gear me-2"></i> Settings
                                </a></li>
                                <li><hr class="dropdown-divider"></li>
                                <li>
                                    <form action="{{ route('logout') }}" method="POST">
                                        @csrf
                                        <button type="submit" class="dropdown-item text-danger">
                                            <i class="bi bi-box-arrow-right me-2"></i> Logout
                                        </button>
                                    </form>
                                </li>
                            </ul>
                        </div>
                    </div>
                </div>
            </nav>

            <!-- Flash Messages -->
            @if(session('success'))
                <div class="alert alert-success alert-dismissible fade show mx-4 mt-3 shadow-sm" role="alert" id="teacher-success-message" style="border-left: 4px solid #198754;">
                    <strong><i class="bi bi-check-circle-fill me-2"></i>Success!</strong>
                    <span class="ms-2">{{ session('success') }}</span>
                    <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
                </div>
                <script>
                    // Auto-hide success message after 5 seconds
                    setTimeout(function() {
                        var alert = document.getElementById('teacher-success-message');
                        if (alert) {
                            var bsAlert = new bootstrap.Alert(alert);
                            bsAlert.close();
                        }
                    }, 5000);
                </script>
            @endif
            
            @if(session('error'))
                <div class="alert alert-danger alert-dismissible fade show mx-4 mt-3 shadow-sm" role="alert" id="teacher-error-message" style="border-left: 4px solid #dc3545;">
                    <strong><i class="bi bi-exclamation-triangle-fill me-2"></i>Error!</strong>
                    <span class="ms-2">{{ session('error') }}</span>
                    <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
                </div>
                <script>
                    // Auto-hide error message after 7 seconds
                    setTimeout(function() {
                        var alert = document.getElementById('teacher-error-message');
                        if (alert) {
                            var bsAlert = new bootstrap.Alert(alert);
                            bsAlert.close();
                        }
                    }, 7000);
                </script>
            @endif

            <!-- Main Content Area -->
            <div class="main-content">
                @yield('content')
            </div>
        </div>
    </div>

    <!-- Bootstrap 5 JS -->
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
    <!-- Custom Teacher Dashboard JS -->
    <script src="{{ asset('js/teacher-dashboard.js') }}"></script>
    
    <script>
        document.addEventListener('DOMContentLoaded', function() {
            // Sidebar toggle
            document.getElementById('sidebarCollapse').addEventListener('click', function() {
                document.getElementById('sidebar').classList.toggle('active');
                document.getElementById('content').classList.toggle('active');
            });

            // Auto-close sidebar on mobile
            if (window.innerWidth <= 768) {
                document.getElementById('sidebar').classList.add('active');
            }

            // Active submenu handling
            const submenus = document.querySelectorAll('[data-bs-toggle="collapse"]');
            submenus.forEach(submenu => {
                submenu.addEventListener('click', function() {
                    const target = this.getAttribute('href').substring(1);
                    const element = document.getElementById(target);
                    if (element) {
                        element.classList.toggle('show');
                    }
                });
            });
        });
    </script>

    @stack('scripts')
</body>
</html>
