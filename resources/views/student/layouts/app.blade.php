<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>@yield('title', 'Student Dashboard') - School ERP</title>
    
    <!-- Bootstrap 5 CSS -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <!-- Bootstrap Icons -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.3/font/bootstrap-icons.min.css" rel="stylesheet">
    <!-- Font Awesome 6 Free -->
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    
    <style>
        :root {
            --sidebar-width: 260px;
            --sidebar-bg: #1a1d20;
            --sidebar-hover: #2d3238;
            --primary-color: #007bff;
        }
        
        body {
            background-color: #f8f9fa;
            overflow-x: hidden;
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
        
        .sidebar-menu-section {
            padding: 10px 0;
        }
        
        .sidebar-menu-title {
            padding: 10px 20px 5px;
            font-size: 0.7rem;
            text-transform: uppercase;
            letter-spacing: 1px;
            color: #6c757d;
            font-weight: 600;
        }
        
        #sidebar ul.components {
            padding: 15px 0;
        }
        
        #sidebar ul li a {
            padding: 12px 20px;
            display: flex;
            align-items: center;
            color: #adb5bd;
            text-decoration: none;
            transition: all 0.3s;
            border-left: 3px solid transparent;
            border-radius: 8px;
            margin: 0.25rem 0.75rem;
            border: 1px solid transparent;
        }
        
        #sidebar ul li a:hover,
        #sidebar ul li a.active {
            color: #fff;
            background: linear-gradient(135deg, var(--sidebar-hover) 0%, #3a4248 100%);
            border-left-color: var(--primary-color);
            transform: translateX(3px);
        }
        
        #sidebar ul li a i {
            margin-right: 12px;
            font-size: 1.1rem;
            width: 20px;
            text-align: center;
        }
        
        #sidebar ul li a .menu-text {
            flex: 1;
        }
        
        #sidebar ul li a .badge {
            font-size: 0.7rem;
            padding: 0.25em 0.6em;
        }
        
        .notification-badge {
            position: absolute;
            top: 5px;
            right: 5px;
            font-size: 0.65rem;
            padding: 2px 6px;
        }
        
        /* Content Styles */
        #content {
            width: 100%;
            margin-left: var(--sidebar-width);
            transition: all 0.3s;
        }
        
        #content.active {
            margin-left: 0;
        }
        
        /* Navbar */
        .navbar {
            background: #fff;
            box-shadow: 0 2px 4px rgba(0,0,0,0.08);
            padding: 0.75rem 1.5rem;
        }
        
        .navbar-btn {
            border: none;
            background: linear-gradient(135deg, #007bff 0%, #0056b3 100%);
            color: #fff;
            padding: 8px 16px;
            border-radius: 8px;
            transition: all 0.3s;
        }
        
        .navbar-btn:hover {
            background: linear-gradient(135deg, #0056b3 0%, #004494 100%);
            color: #fff;
        }
        
        .user-info {
            display: flex;
            align-items: center;
            gap: 10px;
        }
        
        .user-avatar {
            width: 40px;
            height: 40px;
            border-radius: 50%;
            background: linear-gradient(135deg, #007bff 0%, #0056b3 100%);
            color: #fff;
            display: flex;
            align-items: center;
            justify-content: center;
            font-weight: 600;
        }
        
        /* Main Content */
        .main-content {
            padding: 1.5rem;
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
        
        /* Cards */
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
        
        .stats-card {
            border: none;
            border-radius: 10px;
            box-shadow: 0 1px 3px rgba(0,0,0,0.1);
            transition: all 0.3s ease;
        }
        
        .stats-card:hover {
            transform: translateY(-2px);
            box-shadow: 0 4px 12px rgba(0,0,0,0.15);
        }
        
        /* Button Styles */
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
        
        /* Enhanced Animations */
        .sidebar-header {
            padding: 20px;
            background: rgba(0,0,0,0.2);
            border-bottom: 1px solid rgba(255,255,255,0.1);
        }
        
        .sidebar-header h3 {
            color: #fff;
            font-size: 1.3rem;
            font-weight: 700;
            margin: 0;
        }
        
        .sidebar-header small {
            color: #94a3b8;
            font-size: 0.75rem;
        }
        
        /* Logout button styling */
        .logout-btn {
            background: linear-gradient(135deg, #dc3545 0%, #c82333 100%);
            border: none;
            padding: 10px 20px;
            border-radius: 8px;
            color: white;
            transition: all 0.3s;
        }
        
        .logout-btn:hover {
            background: linear-gradient(135deg, #c82333 0%, #bd2130 100%);
            transform: translateY(-2px);
            box-shadow: 0 4px 15px rgba(220, 53, 69, 0.4);
        }
        
        /* Card hover effects */
        .card {
            transition: all 0.3s ease;
        }
        
        .card:hover {
            transform: translateY(-2px);
            box-shadow: 0 4px 15px rgba(0,0,0,0.1) !important;
        }
        
        /* Smooth transitions */
        a, button {
            transition: all 0.3s ease;
        }
    </style>
    
    @stack('styles')
</head>
<body>
    <div class="wrapper">
        <!-- Sidebar -->
        <nav id="sidebar">
            <div class="sidebar-header">
                <h3><i class="bi bi-mortarboard"></i> School ERP</h3>
                <small>Student Portal</small>
            </div>

            <ul class="list-unstyled components">
                <!-- Dashboard -->
                <li>
                    <a href="{{ route('student.dashboard') }}" class="{{ request()->routeIs('student.dashboard') ? 'active' : '' }}">
                        <i class="bi bi-speedometer2"></i>
                        <span class="menu-text">Dashboard</span>
                    </a>
                </li>

                <!-- Academic Section -->
                <div class="sidebar-menu-section">
                    <div class="sidebar-menu-title">Academic</div>
                </div>

                <!-- Timetable -->
                <li>
                    <a href="{{ route('student.timetable') }}" class="{{ request()->routeIs('student.timetable*') ? 'active' : '' }}">
                        <i class="bi bi-calendar-week"></i>
                        <span class="menu-text">Timetable</span>
                    </a>
                </li>

                <!-- Attendance -->
                <li>
                    <a href="{{ route('student.attendance') }}" class="{{ request()->routeIs('student.attendance*') ? 'active' : '' }}">
                        <i class="bi bi-calendar-check"></i>
                        <span class="menu-text">Attendance</span>
                    </a>
                </li>

                <!-- Results -->
                <li>
                    <a href="{{ route('student.results') }}" class="{{ request()->routeIs('student.results*') ? 'active' : '' }}">
                        <i class="bi bi-clipboard-data"></i>
                        <span class="menu-text">Results</span>
                    </a>
                </li>

                <!-- Fees Section -->
                <div class="sidebar-menu-section">
                    <div class="sidebar-menu-title">Payments</div>
                </div>

                <!-- Fees -->
                <li>
                    <a href="{{ route('student.fees') }}" class="{{ request()->routeIs('student.fees*') ? 'active' : '' }}">
                        <i class="bi bi-currency-dollar"></i>
                        <span class="menu-text">Fees</span>
                        @if(isset($pendingFees) && $pendingFees > 0)
                            <span class="badge bg-warning text-dark">{{ $pendingFees }}</span>
                        @endif
                    </a>
                </li>

                <!-- Personal Section -->
                <div class="sidebar-menu-section">
                    <div class="sidebar-menu-title">Personal</div>
                </div>

                <!-- My Profile -->
                <li>
                    <a href="{{ route('student.profile') }}" class="{{ request()->routeIs('student.profile*') ? 'active' : '' }}">
                        <i class="bi bi-person-circle"></i>
                        <span class="menu-text">My Profile</span>
                    </a>
                </li>

                <!-- Notifications -->
                <li>
                    <a href="{{ route('student.notifications') }}" class="{{ request()->routeIs('student.notifications') ? 'active' : '' }}">
                        <i class="bi bi-bell"></i>
                        <span class="menu-text">Notifications</span>
                        @php $authStudent = \Illuminate\Support\Facades\Auth::guard('student')->user(); @endphp
                        @if($authStudent && $authStudent->unreadNotificationsCount() > 0)
                            <span class="badge bg-danger">{{ $authStudent->unreadNotificationsCount() }}</span>
                        @endif
                    </a>
                </li>

                <!-- Library -->
                <li>
                    <a href="{{ route('student.library') }}" class="{{ request()->routeIs('student.library*') ? 'active' : '' }}">
                        <i class="bi bi-book"></i>
                        <span class="menu-text">Library</span>
                    </a>
                </li>
            </ul>

            <div class="mt-auto p-3 border-top border-secondary">
                <div class="d-flex align-items-center justify-content-between">
                    <small class="text-muted">Logged in as Student</small>
                </div>
                <form action="{{ route('student.logout') }}" method="POST" class="mt-2">
                    @csrf
                    <button type="submit" class="btn logout-btn w-100">
                        <i class="bi bi-box-arrow-right me-2"></i>Logout
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
                        <a href="{{ route('student.notifications') }}" class="btn btn-link me-3 position-relative">
                            <i class="bi bi-bell" style="font-size: 1.3rem;"></i>
                            @if($student->unreadNotificationsCount() > 0)
                                <span class="position-absolute top-0 start-100 translate-middle badge rounded-pill bg-danger">
                                    {{ $student->unreadNotificationsCount() }}
                                </span>
                            @endif
                        </a>

                        <!-- User Info -->
                        <div class="dropdown">
                            <a href="#" class="d-flex align-items-center text-decoration-none dropdown-toggle user-info" 
                               data-bs-toggle="dropdown">
                                @if($student->photo)
                                    <img src="{{ asset('storage/' . $student->photo) }}" 
                                         alt="{{ $student->name }}" 
                                         class="rounded-circle" 
                                         width="40" 
                                         height="40"
                                         style="object-fit: cover;">
                                @else
                                    <div class="user-avatar">
                                        {{ substr($student->name, 0, 1) }}
                                    </div>
                                @endif
                                <div class="ms-2 d-none d-md-block">
                                    <div class="fw-semibold">{{ $student->name }}</div>
                                    <small class="text-muted">{{ $student->roll_number ?? 'Student' }}</small>
                                </div>
                            </a>
                            <ul class="dropdown-menu dropdown-menu-end">
                                <li><a class="dropdown-item" href="{{ route('student.profile') }}">
                                    <i class="bi bi-person me-2"></i> My Profile
                                </a></li>
                                <li><a class="dropdown-item" href="{{ route('student.profile.change-password') }}">
                                    <i class="bi bi-key me-2"></i> Change Password
                                </a></li>
                                <li><hr class="dropdown-divider"></li>
                                <li>
                                    <form action="{{ route('student.logout') }}" method="POST">
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

            <!-- Main Content Area -->
            <div class="main-content">
                @if(session('success'))
                    <div class="alert alert-success alert-dismissible fade show" role="alert">
                        <i class="bi bi-check-circle me-2"></i>{{ session('success') }}
                        <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
                    </div>
                @endif

                @if(session('error'))
                    <div class="alert alert-danger alert-dismissible fade show" role="alert">
                        <i class="bi bi-exclamation-circle me-2"></i>{{ session('error') }}
                        <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
                    </div>
                @endif

                @yield('content')
            </div>
        </div>
    </div>

    <!-- Bootstrap 5 JS -->
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
    
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
        });
    </script>

    @stack('scripts')
</body>
</html>
