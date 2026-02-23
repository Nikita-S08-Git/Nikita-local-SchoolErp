<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>@yield('title', 'School ERP System')</title>
    
    <!-- Bootstrap 5 CSS -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <!-- Bootstrap Icons -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.3/font/bootstrap-icons.min.css" rel="stylesheet">
    <!-- Font Awesome 6 Free -->
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css" integrity="sha512-papVtJ6Qoyt7rO8zOoaJ+R/0ZoHtn+J8EYBrmPQOiEz2+qyj/MoF+jV7qZHb3T7j6xFwfsPWTf6yoA08VUcZ2g==" crossorigin="anonymous" referrerpolicy="no-referrer" />

    <!-- Custom CSS -->
    <style>
        :root {
            --sidebar-width: 260px;
            --sidebar-bg: #1a1d20;
            --sidebar-hover: #2d3238;
            --sidebar-active: #000;
        }

        body {
            overflow-x: hidden;
        }

        /* Sidebar Styles */
        .sidebar {
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

        .sidebar-header {
            padding: 1.5rem 1rem;
            border-bottom: 1px solid rgba(255, 255, 255, 0.1);
            background: rgba(0,0,0,0.2);
        }

        .sidebar-header h5 {
            color: #fff;
            font-weight: 700;
            margin: 0;
            font-size: 1.3rem;
            text-shadow: 0 1px 3px rgba(0,0,0,0.3);
        }

        .sidebar .nav {
            padding: 1rem 0;
        }

        .sidebar .nav-link {
            color: #adb5bd;
            padding: 0.75rem 1.25rem;
            margin: 0.25rem 0.75rem;
            border-radius: 8px;
            transition: all 0.3s ease;
            display: flex;
            align-items: center;
            font-weight: 500;
            border: 1px solid transparent;
        }

        .sidebar .nav-link i {
            width: 20px;
            margin-right: 0.75rem;
            font-size: 1.1rem;
            text-align: center;
        }

        .sidebar .nav-link:hover {
            color: #fff;
            background: linear-gradient(135deg, var(--sidebar-hover) 0%, #3a4248 100%);
            border-color: rgba(255,255,255,0.1);
            transform: translateX(3px);
        }

        .sidebar .nav-link.active {
            color: #fff;
            background: linear-gradient(135deg, #007bff 0%, #0056b3 100%);
            border-color: #007bff;
            box-shadow: 0 2px 8px rgba(0,123,255,0.3);
        }

        .sidebar .collapse {
            margin-left: 0;
        }

        .sidebar .dropdown-menu {
            background: rgba(0, 0, 0, 0.3);
            border: none;
            margin: 0.5rem 1rem 0 1rem;
            width: calc(100% - 2rem);
            border-radius: 8px;
            backdrop-filter: blur(10px);
        }

        .sidebar .dropdown-item {
            color: #adb5bd;
            padding: 0.6rem 1rem;
            border-radius: 6px;
            margin: 0.25rem;
            font-weight: 500;
            transition: all 0.3s ease;
            border: 1px solid transparent;
        }

        .sidebar .dropdown-item:hover {
            background: rgba(255,255,255,0.1);
            color: #fff;
            border-color: rgba(255,255,255,0.2);
            transform: translateX(2px);
        }

        .sidebar .dropdown-item.active {
            background: linear-gradient(135deg, #28a745 0%, #1e7e34 100%);
            color: #fff;
            border-color: #28a745;
            box-shadow: 0 2px 6px rgba(40,167,69,0.3);
        }

        .sidebar .dropdown-item i {
            width: 16px;
            text-align: center;
        }

        /* Main Content */
        .main-content {
            margin-left: var(--sidebar-width);
            min-height: 100vh;
            transition: margin-left 0.3s ease-in-out;
        }

        /* Top Navbar */
        .top-navbar {
            background: #fff;
            border-bottom: 1px solid #e9ecef;
            padding: 1rem 1.5rem;
            position: sticky;
            top: 0;
            z-index: 999;
        }

        .mobile-toggle {
            display: none;
            background: #000;
            color: #fff;
            border: none;
            padding: 0.5rem 1rem;
            border-radius: 0.375rem;
            cursor: pointer;
        }

        .mobile-toggle:hover {
            background: #222;
        }

        /* Sidebar Overlay */
        .sidebar-overlay {
            display: none;
            position: fixed;
            top: 0;
            left: 0;
            right: 0;
            bottom: 0;
            background: rgba(0, 0, 0, 0.5);
            z-index: 999;
        }

        /* User Profile Dropdown */
        .user-profile-btn {
            background: #000 !important;
            border: none !important;
            border-radius: 25px !important;
            padding: 0.5rem 1rem !important;
        }

        .user-profile-btn:hover {
            background: #222 !important;
        }

        /* Mobile Responsive */
        @media (max-width: 991px) {
            .sidebar {
                transform: translateX(-100%);
            }

            .sidebar.show {
                transform: translateX(0);
                box-shadow: 0 0 20px rgba(0, 0, 0, 0.3);
            }

            .main-content {
                margin-left: 0;
            }

            .mobile-toggle {
                display: inline-flex;
                align-items: center;
                gap: 0.5rem;
            }

            .sidebar-overlay.show {
                display: block;
            }

            .top-navbar {
                padding: 0.75rem 1rem;
            }
        }

        @media (max-width: 576px) {
            .sidebar {
                width: 280px;
            }

            .top-navbar h1 {
                font-size: 1.25rem !important;
            }

            .user-profile-btn {
                padding: 0.4rem 0.75rem !important;
            }

            .user-profile-btn .user-info {
                display: none;
            }
        }

        /* Scrollbar for sidebar */
        .sidebar::-webkit-scrollbar {
            width: 6px;
        }

        .sidebar::-webkit-scrollbar-track {
            background: rgba(255, 255, 255, 0.05);
        }

        .sidebar::-webkit-scrollbar-thumb {
            background: rgba(255, 255, 255, 0.2);
            border-radius: 3px;
        }

        .sidebar::-webkit-scrollbar-thumb:hover {
            background: rgba(255, 255, 255, 0.3);
        }

        /* Logout button in sidebar */
        .sidebar .logout-btn {
            background: transparent;
            border: none;
            width: 100%;
            text-align: left;
            color: #dc3545;
            font-weight: 500;
        }

        .sidebar .logout-btn:hover {
            background: linear-gradient(135deg, #dc3545 0%, #c82333 100%);
            color: #fff;
            transform: translateX(3px);
        }

        /* Section dividers */
        .sidebar .nav-section {
            border-top: 1px solid rgba(255,255,255,0.1);
            margin-top: 1rem;
            padding-top: 1rem;
        }

        .sidebar .nav-section:first-child {
            border-top: none;
            margin-top: 0;
            padding-top: 0;
        }
    </style>
    @stack('styles')
</head>
<body>
    <!-- Sidebar Overlay (Mobile) -->
    <div class="sidebar-overlay" id="sidebarOverlay" onclick="toggleSidebar()"></div>

    <!-- Sidebar -->
    <nav class="sidebar" id="sidebar">
        <div class="sidebar-header">
            <h5><i class="bi bi-mortarboard-fill me-2"></i>School ERP</h5>
        </div>
        
        <ul class="nav flex-column">
            @php
                $role = auth()->check() ? (auth()->user()->roles->first()->name ?? 'student') : 'student';
            @endphp
            
            <!-- Dashboard Section -->
            <div class="nav-section">
                <li class="nav-item">
                    <a class="nav-link {{ request()->routeIs('dashboard.*') ? 'active' : '' }}" href="{{ route('dashboard.' . $role) }}">
                        <i class="bi bi-house-door"></i>
                        <span>Dashboard</span>
                    </a>
                </li>
            </div>
            
            @if($role === 'admin')
            <!-- System Administration Section -->
            <div class="nav-section">
                <li class="nav-item dropdown">
                    <a class="nav-link dropdown-toggle {{ request()->routeIs('dashboard.students*') || request()->routeIs('dashboard.teachers*') || request()->routeIs('staff.*') ? 'active' : '' }}" 
                       href="#" data-bs-toggle="collapse" data-bs-target="#systemAdmin" aria-expanded="{{ request()->routeIs('dashboard.students*') || request()->routeIs('dashboard.teachers*') || request()->routeIs('staff.*') ? 'true' : 'false' }}">
                        <i class="bi bi-shield-lock"></i>
                        <span>System Administration</span>
                    </a>
                    <div class="collapse {{ request()->routeIs('dashboard.students*') || request()->routeIs('dashboard.teachers*') || request()->routeIs('staff.*') ? 'show' : '' }}" id="systemAdmin">
                        <div class="dropdown-menu show">
                            <a class="dropdown-item {{ request()->routeIs('dashboard.students*') ? 'active' : '' }}"
                               href="{{ route('dashboard.students.index') }}">
                                <i class="bi bi-mortarboard me-2"></i>Students
                            </a>
                            <a class="dropdown-item {{ request()->routeIs('dashboard.teachers*') ? 'active' : '' }}"
                               href="{{ route('dashboard.teachers.index') }}">
                                <i class="bi bi-person-badge me-2"></i>Teachers
                            </a>
                            <a class="dropdown-item {{ request()->routeIs('staff.*') ? 'active' : '' }}"
                               href="{{ route('staff.index') }}">
                                <i class="bi bi-people me-2"></i>Staff Management
                            </a>
                        </div>
                    </div>
                </li>
            </div>

            <div class="nav-section">
                <li class="nav-item dropdown">
                    <a class="nav-link dropdown-toggle {{ request()->routeIs('academic.*') || request()->routeIs('fees.*') ? 'active' : '' }}" 
                       href="#" data-bs-toggle="collapse" data-bs-target="#academicConfig" aria-expanded="{{ request()->routeIs('academic.*') || request()->routeIs('fees.*') ? 'true' : 'false' }}">
                        <i class="bi bi-gear"></i>
                        <span>Configuration</span>
                    </a>
                    <div class="collapse {{ request()->routeIs('academic.*') || request()->routeIs('fees.*') ? 'show' : '' }}" id="academicConfig">
                        <div class="dropdown-menu show">
                            <a class="dropdown-item {{ request()->routeIs('academic.programs.*') ? 'active' : '' }}"
                               href="{{ route('academic.programs.index') }}">
                                <i class="bi bi-mortarboard me-2"></i>Programs
                            </a>
                            <a class="dropdown-item {{ request()->routeIs('academic.subjects.*') ? 'active' : '' }}"
                               href="{{ route('academic.subjects.index') }}">
                                <i class="bi bi-journal-text me-2"></i>Subjects
                            </a>
                            <a class="dropdown-item {{ request()->routeIs('academic.divisions.*') ? 'active' : '' }}"
                               href="{{ route('academic.divisions.index') }}">
                                <i class="bi bi-grid-3x3-gap me-2"></i>Divisions
                            </a>
                            <a class="dropdown-item {{ request()->routeIs('academic.sessions.*') ? 'active' : '' }}"
                               href="{{ route('academic.sessions.index') }}">
                                <i class="bi bi-calendar-range me-2"></i>Academic Sessions
                            </a>
                            <a class="dropdown-item {{ request()->routeIs('fees.structures.*') ? 'active' : '' }}"
                               href="{{ route('fees.structures.index') }}">
                                <i class="bi bi-gear me-2"></i>Fee Structures
                            </a>
                        </div>
                    </div>
                </li>
            </div>

            <div class="nav-section">
                <li class="nav-item dropdown">
                    <a class="nav-link dropdown-toggle {{ request()->routeIs('examinations.*') || request()->routeIs('library.*') ? 'active' : '' }}" 
                       href="#" data-bs-toggle="collapse" data-bs-target="#operations" aria-expanded="{{ request()->routeIs('examinations.*') || request()->routeIs('library.*') ? 'true' : 'false' }}">
                        <i class="bi bi-clipboard-data"></i>
                        <span>Operations</span>
                    </a>
                    <div class="collapse {{ request()->routeIs('examinations.*') || request()->routeIs('library.*') ? 'show' : '' }}" id="operations">
                        <div class="dropdown-menu show">
                            <a class="dropdown-item {{ request()->routeIs('examinations.*') ? 'active' : '' }}"
                               href="{{ route('examinations.index') }}">
                                <i class="bi bi-pencil-square me-2"></i>Examinations
                            </a>
                            <a class="dropdown-item {{ request()->routeIs('library.books.*') ? 'active' : '' }}"
                               href="{{ route('library.books.index') }}">
                                <i class="bi bi-book me-2"></i>Library
                            </a>
                        </div>
                    </div>
                </li>
            </div>
            @endif
            
            @if(in_array($role, ['principal', 'office']))
            <!-- User Management Section -->
            <div class="nav-section">
                <li class="nav-item dropdown">
                    <a class="nav-link dropdown-toggle {{ request()->routeIs('dashboard.students*') || request()->routeIs('dashboard.teachers*') ? 'active' : '' }}" 
                       href="#" data-bs-toggle="collapse" data-bs-target="#userManagement" aria-expanded="{{ request()->routeIs('dashboard.students*') || request()->routeIs('dashboard.teachers*') ? 'true' : 'false' }}">
                        <i class="bi bi-people"></i>
                        <span>User Management</span>
                    </a>
                    <div class="collapse {{ request()->routeIs('dashboard.students*') || request()->routeIs('dashboard.teachers*') ? 'show' : '' }}" id="userManagement">
                        <div class="dropdown-menu show">
                            <a class="dropdown-item {{ request()->routeIs('dashboard.students*') ? 'active' : '' }}"
                               href="{{ route('dashboard.students.index') }}">
                                <i class="bi bi-mortarboard me-2"></i>Students
                            </a>
                            <a class="dropdown-item {{ request()->routeIs('dashboard.teachers*') ? 'active' : '' }}"
                               href="{{ route('dashboard.teachers.index') }}">
                                <i class="bi bi-person-badge me-2"></i>Teachers
                            </a>
                        </div>
                    </div>
                </li>
            </div>

            <!-- Academic Management Section -->
            <div class="nav-section">
                <li class="nav-item dropdown">
                    <a class="nav-link dropdown-toggle {{ request()->routeIs('academic.*') ? 'active' : '' }}" 
                       href="#" data-bs-toggle="collapse" data-bs-target="#academicManagement" aria-expanded="{{ request()->routeIs('academic.*') ? 'true' : 'false' }}">
                        <i class="bi bi-book"></i>
                        <span>Academic Management</span>
                    </a>
                    <div class="collapse {{ request()->routeIs('academic.*') ? 'show' : '' }}" id="academicManagement">
                        <div class="dropdown-menu show">
                            <a class="dropdown-item {{ request()->routeIs('academic.programs.*') ? 'active' : '' }}"
                               href="{{ route('academic.programs.index') }}">
                                <i class="bi bi-mortarboard me-2"></i>Programs
                            </a>
                            <a class="dropdown-item {{ request()->routeIs('academic.subjects.*') ? 'active' : '' }}"
                               href="{{ route('academic.subjects.index') }}">
                                <i class="bi bi-journal-text me-2"></i>Subjects
                            </a>
                            <a class="dropdown-item {{ request()->routeIs('academic.divisions.*') ? 'active' : '' }}"
                               href="{{ route('academic.divisions.index') }}">
                                <i class="bi bi-grid-3x3-gap me-2"></i>Divisions
                            </a>
                            <a class="dropdown-item {{ request()->routeIs('academic.sessions.*') ? 'active' : '' }}"
                               href="{{ route('academic.sessions.index') }}">
                                <i class="bi bi-calendar-range me-2"></i>Academic Sessions
                            </a>
                        </div>
                    </div>
                </li>
            </div>

            <!-- Fee Management Section -->
            <div class="nav-section">
                <li class="nav-item dropdown">
                    <a class="nav-link dropdown-toggle {{ request()->routeIs('fees.*') ? 'active' : '' }}" 
                       href="#" data-bs-toggle="collapse" data-bs-target="#feeManagement" aria-expanded="{{ request()->routeIs('fees.*') ? 'true' : 'false' }}">
                        <i class="bi bi-credit-card"></i>
                        <span>Fee Management</span>
                    </a>
                    <div class="collapse {{ request()->routeIs('fees.*') ? 'show' : '' }}" id="feeManagement">
                        <div class="dropdown-menu show">
                            <a class="dropdown-item {{ request()->routeIs('fees.structures.*') ? 'active' : '' }}"
                               href="{{ route('fees.structures.index') }}">
                                <i class="bi bi-gear me-2"></i>Fee Structures
                            </a>
                            <a class="dropdown-item {{ request()->routeIs('fees.assignments.*') ? 'active' : '' }}"
                               href="{{ route('fees.assignments.index') }}">
                                <i class="bi bi-person-plus me-2"></i>Assign Fees
                            </a>
                            <a class="dropdown-item {{ request()->routeIs('fees.payments.*') ? 'active' : '' }}"
                               href="{{ route('fees.payments.index') }}">
                                <i class="bi bi-cash-coin me-2"></i>Collect Payments
                            </a>
                            <a class="dropdown-item {{ request()->routeIs('fees.outstanding.*') ? 'active' : '' }}"
                               href="{{ route('fees.outstanding.index') }}">
                                <i class="bi bi-exclamation-triangle me-2"></i>Outstanding Fees
                            </a>
                            <a class="dropdown-item {{ request()->routeIs('fees.scholarships.*') ? 'active' : '' }}"
                               href="{{ route('fees.scholarships.index') }}">
                                <i class="bi bi-award me-2"></i>Scholarships
                            </a>
                        </div>
                    </div>
                </li>
            </div>

            <!-- Attendance & Schedule Section -->
            <div class="nav-section">
                <li class="nav-item dropdown">
                    <a class="nav-link dropdown-toggle {{ request()->routeIs('academic.attendance*') || request()->routeIs('academic.timetable*') ? 'active' : '' }}" 
                       href="#" data-bs-toggle="collapse" data-bs-target="#attendanceSchedule" aria-expanded="{{ request()->routeIs('academic.attendance*') || request()->routeIs('academic.timetable*') ? 'true' : 'false' }}">
                        <i class="bi bi-calendar-check"></i>
                        <span>Attendance & Schedule</span>
                    </a>
                    <div class="collapse {{ request()->routeIs('academic.attendance*') || request()->routeIs('academic.timetable*') ? 'show' : '' }}" id="attendanceSchedule">
                        <div class="dropdown-menu show">
                            <a class="dropdown-item {{ request()->routeIs('academic.attendance.*') ? 'active' : '' }}"
                               href="{{ route('academic.attendance.index') }}">
                                <i class="bi bi-check2-square me-2"></i>Attendance
                            </a>
                            <a class="dropdown-item {{ request()->routeIs('academic.timetable.*') ? 'active' : '' }}"
                               href="{{ route('academic.timetable.index') }}">
                                <i class="bi bi-table me-2"></i>Timetable
                            </a>
                        </div>
                    </div>
                </li>
            </div>

            <!-- Examinations Section -->
            <div class="nav-section">
                <li class="nav-item">
                    <a class="nav-link {{ request()->routeIs('examinations.*') ? 'active' : '' }}" href="{{ route('examinations.index') }}">
                        <i class="bi bi-clipboard-check"></i>
                        <span>Examinations</span>
                    </a>
                </li>
            </div>

            <!-- Library Management Section -->
            <div class="nav-section">
                <li class="nav-item dropdown">
                    <a class="nav-link dropdown-toggle {{ request()->routeIs('library.*') ? 'active' : '' }}" 
                       href="#" data-bs-toggle="collapse" data-bs-target="#libraryManagement" aria-expanded="{{ request()->routeIs('library.*') ? 'true' : 'false' }}">
                        <i class="bi bi-book"></i>
                        <span>Library</span>
                    </a>
                    <div class="collapse {{ request()->routeIs('library.*') ? 'show' : '' }}" id="libraryManagement">
                        <div class="dropdown-menu show">
                            <a class="dropdown-item {{ request()->routeIs('library.books.*') ? 'active' : '' }}"
                               href="{{ route('library.books.index') }}">
                                <i class="bi bi-journal-bookmark me-2"></i>Books
                            </a>
                            <a class="dropdown-item {{ request()->routeIs('library.issues.*') ? 'active' : '' }}"
                               href="{{ route('library.issues.index') }}">
                                <i class="bi bi-arrow-left-right me-2"></i>Issue/Return
                            </a>
                        </div>
                    </div>
                </li>
            </div>

            <!-- Staff Management Section -->
            <div class="nav-section">
                <li class="nav-item">
                    <a class="nav-link {{ request()->routeIs('staff.*') ? 'active' : '' }}" href="{{ route('staff.index') }}">
                        <i class="bi bi-people"></i>
                        <span>Staff Management</span>
                    </a>
                </li>
            </div>
            @endif
            
            @if($role === 'student')
            <!-- Student Section -->
            <div class="nav-section">
                <li class="nav-item">
                    <a class="nav-link" href="#">
                        <i class="bi bi-calendar-check"></i>
                        <span>My Attendance</span>
                    </a>
                </li>
                
                <li class="nav-item">
                    <a class="nav-link {{ request()->routeIs('student.fees.*') ? 'active' : '' }}" href="{{ route('student.fees.index') }}">
                        <i class="bi bi-credit-card"></i>
                        <span>My Fees</span>
                    </a>
                </li>
            </div>
            @endif
            
            @if($role === 'teacher')
            <!-- Teacher Section -->
            <div class="nav-section">
                <li class="nav-item">
                    <a class="nav-link {{ request()->routeIs('teacher.students') ? 'active' : '' }}" href="{{ route('teacher.students') }}">
                        <i class="bi bi-people"></i>
                        <span>My Students</span>
                    </a>
                </li>
                
                <li class="nav-item">
                    <a class="nav-link {{ request()->routeIs('teacher.attendance') ? 'active' : '' }}" href="{{ route('teacher.attendance') }}">
                        <i class="bi bi-calendar-check"></i>
                        <span>Attendance Report</span>
                    </a>
                </li>
                
                <li class="nav-item">
                    <a class="nav-link {{ request()->routeIs('academic.timetable.*') ? 'active' : '' }}" href="{{ route('academic.timetable.index') }}">
                        <i class="bi bi-table"></i>
                        <span>Timetable</span>
                    </a>
                </li>
            </div>
            @endif
            
            <!-- Logout Section -->
            <div class="nav-section">
                <li class="nav-item">
                    <form method="POST" action="{{ route('logout') }}">
                        @csrf
                        <button type="submit" class="nav-link logout-btn">
                            <i class="bi bi-box-arrow-right"></i>
                            <span>Logout</span>
                        </button>
                    </form>
                </li>
            </div>
        </ul>
    </nav>
    
    <!-- Main Content -->
    <main class="main-content">
        <!-- Top Navbar -->
        <div class="top-navbar d-flex justify-content-between align-items-center flex-wrap gap-3">
            <div class="d-flex align-items-center gap-3">
                <button class="mobile-toggle" onclick="toggleSidebar()">
                    <i class="bi bi-list"></i>
                    <span class="d-none d-sm-inline">Menu</span>
                </button>
                <h1 class="h3 mb-0 fw-bold text-gray-800">@yield('page-title', 'Dashboard')</h1>
            </div>
            
            <!-- User Profile Dropdown -->
            <div class="dropdown">
                <button class="btn user-profile-btn dropdown-toggle d-flex align-items-center" type="button" data-bs-toggle="dropdown">
                    <i class="bi bi-person-circle fs-5 me-2"></i>
                    <div class="text-start user-info">
                        <div class="fw-semibold text-white small">{{ auth()->user()->name ?? 'User' }}</div>
                        <small class="text-light opacity-75" style="font-size: 0.7rem;">{{ ucfirst(auth()->user()->roles->first()->name ?? 'Role') }}</small>
                    </div>
                </button>
                <ul class="dropdown-menu dropdown-menu-end shadow-lg" style="min-width: 220px;">
                    <li class="px-3 py-2 border-bottom">
                        <div class="fw-semibold">{{ auth()->user()->name ?? 'User' }}</div>
                        <small class="text-muted">{{ ucfirst(auth()->user()->roles->first()->name ?? 'Role') }}</small>
                    </li>
                    <li><a class="dropdown-item" href="#"><i class="bi bi-person me-2"></i>Profile</a></li>
                    <li><a class="dropdown-item" href="#"><i class="bi bi-gear me-2"></i>Settings</a></li>
                    <li><hr class="dropdown-divider"></li>
                    <li>
                        <form method="POST" action="{{ route('logout') }}">
                            @csrf
                            <button type="submit" class="dropdown-item text-danger">
                                <i class="bi bi-box-arrow-right me-2"></i>Logout
                            </button>
                        </form>
                    </li>
                </ul>
            </div>
        </div>
        
        <!-- Content Area -->
        <div class="content-area">
            <!-- Flash Messages -->
            @if(session('success'))
                <div class="alert alert-success alert-dismissible fade show mx-4 mt-3" role="alert">
                    <i class="bi bi-check-circle me-2"></i>{{ session('success') }}
                    <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
                </div>
            @endif
            
            @if(session('error'))
                <div class="alert alert-danger alert-dismissible fade show mx-4 mt-3" role="alert">
                    <i class="bi bi-exclamation-triangle me-2"></i>{{ session('error') }}
                    <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
                </div>
            @endif
            
            @if($errors->any())
                <div class="alert alert-danger alert-dismissible fade show mx-4 mt-3" role="alert">
                    <ul class="mb-0">
                        @foreach($errors->all() as $error)
                            <li>{{ $error }}</li>
                        @endforeach
                    </ul>
                    <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
                </div>
            @endif
            
            @yield('content')
        </div>
    </main>
    
    <!-- Bootstrap 5 JS -->
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js" integrity="sha384-YvpcrYf0tY3lHB60NNkmXc5s9fDVZLESaAA55NDzOxhy9GkcIdslK1eN7N6jIeHz" crossorigin="anonymous"></script>
    
    <script>
        function toggleSidebar() {
            const sidebar = document.getElementById('sidebar');
            const overlay = document.getElementById('sidebarOverlay');
            
            sidebar.classList.toggle('show');
            overlay.classList.toggle('show');
        }

        // Close sidebar when clicking outside on mobile
        document.addEventListener('click', function(event) {
            const sidebar = document.getElementById('sidebar');
            const overlay = document.getElementById('sidebarOverlay');
            const toggle = document.querySelector('.mobile-toggle');
            
            if (window.innerWidth <= 991) {
                if (!sidebar.contains(event.target) && !toggle.contains(event.target) && sidebar.classList.contains('show')) {
                    sidebar.classList.remove('show');
                    overlay.classList.remove('show');
                }
            }
        });

        // Close sidebar on route change (for mobile)
        window.addEventListener('beforeunload', function() {
            if (window.innerWidth <= 991) {
                document.getElementById('sidebar').classList.remove('show');
                document.getElementById('sidebarOverlay').classList.remove('show');
            }
        });

        function editProfile() {
            alert('Edit Profile functionality - to be implemented');
            // window.location.href = '/profile/edit';
        }
    </script>
    
    @stack('scripts')
    @yield('scripts')
</body>
</html>