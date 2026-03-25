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
