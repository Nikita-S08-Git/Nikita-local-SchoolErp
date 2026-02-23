<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\Web\AuthController;
use App\Http\Controllers\Web\DashboardController;
use App\Http\Controllers\Web\StudentController;
use App\Http\Controllers\Web\AdmissionController;
use App\Http\Controllers\Web\GuardianController; // ✅ Only this
use App\Http\Controllers\Web\Academic\AcademicSessionController;
use App\Http\Controllers\Web\DepartmentController;



// In routes/web.php, inside Route::middleware(['auth', 'admin'])->group(function () { ... });

Route::prefix('attendance')->name('attendance.')->group(function () {
    Route::get('/', [App\Http\Controllers\Web\AttendanceController::class, 'index'])->name('index');
    Route::post('/create', [App\Http\Controllers\Web\AttendanceController::class, 'create'])->name('create');
    Route::post('/', [App\Http\Controllers\Web\AttendanceController::class, 'store'])->name('store');
    Route::get('/report', [App\Http\Controllers\Web\AttendanceController::class, 'report'])->name('report');
});


Route::middleware(['auth'])->group(function () {
    // Web-specific route names with 'web.' prefix
    Route::prefix('departments')
        ->name('web.departments.') // ← 'web.' prefix add kiya
        ->group(function () {
            Route::get('/', [DepartmentController::class, 'index'])->name('index');
            Route::get('/create', [DepartmentController::class, 'create'])->name('create');
            Route::post('/', [DepartmentController::class, 'store'])->name('store');
            Route::get('/{department}', [DepartmentController::class, 'show'])->name('show');
            Route::get('/{department}/edit', [DepartmentController::class, 'edit'])->name('edit');
            Route::put('/{department}', [DepartmentController::class, 'update'])->name('update');
            Route::delete('/{department}', [DepartmentController::class, 'destroy'])->name('destroy');
        });
});

// Academic Management
Route::middleware(['auth'])->prefix('academic')->name('academic.')->group(function () {
    // Programs
    Route::resource('programs', \App\Http\Controllers\Web\ProgramController::class);
    Route::patch('programs/{program}/toggle-status', [\App\Http\Controllers\Web\ProgramController::class, 'toggleStatus'])
        ->name('programs.toggle-status');
    
    // Subjects
    Route::resource('subjects', \App\Http\Controllers\Web\SubjectController::class);
    
    // Divisions
    Route::resource('divisions', \App\Http\Controllers\Web\DivisionController::class);
    Route::patch('divisions/{division}/toggle-status', [\App\Http\Controllers\Web\DivisionController::class, 'toggleStatus'])
        ->name('divisions.toggle-status');
    Route::post('divisions/{division}/assign-students', [\App\Http\Controllers\Web\DivisionController::class, 'assignStudents'])
        ->name('divisions.assign-students');
    Route::delete('divisions/{division}/students/{student}', [\App\Http\Controllers\Web\DivisionController::class, 'removeStudent'])
        ->name('divisions.remove-student');
    Route::get('divisions/unassigned-students', [\App\Http\Controllers\Web\DivisionController::class, 'unassignedStudents'])
        ->name('divisions.unassigned-students');
    
    // Academic Sessions
    Route::resource('sessions', \App\Http\Controllers\Web\Academic\AcademicSessionController::class);
    Route::patch('sessions/{session}/toggle-status', [\App\Http\Controllers\Web\Academic\AcademicSessionController::class, 'toggleStatus'])
        ->name('sessions.toggle-status');
    
    // Attendance
    Route::get('attendance', [\App\Http\Controllers\Web\AttendanceController::class, 'index'])->name('attendance.index');
    Route::get('attendance/mark', [\App\Http\Controllers\Web\AttendanceController::class, 'create'])->name('attendance.create');
    Route::post('attendance/mark', [\App\Http\Controllers\Web\AttendanceController::class, 'mark'])->name('attendance.mark');
    Route::post('attendance/store', [\App\Http\Controllers\Web\AttendanceController::class, 'store'])->name('attendance.store');
    Route::get('attendance/report', [\App\Http\Controllers\Web\AttendanceController::class, 'report'])->name('attendance.report');
    
    // Timetable
    Route::get('timetable/table', [\App\Http\Controllers\Web\TimetableController::class, 'table'])->name('timetable.table');
    Route::post('timetable/check-availability', [\App\Http\Controllers\Web\TimetableController::class, 'checkSlotAvailability'])
        ->name('timetable.check-availability');
    Route::resource('timetable', \App\Http\Controllers\Web\TimetableController::class);
});

// Fee Management Routes
Route::middleware(['auth', 'role:admin|principal|office'])->prefix('fees')->name('fees.')->group(function () {
    // Fee Structures
    Route::resource('structures', \App\Http\Controllers\Web\FeeStructureController::class)
        ->names('structures');
    
    // Fee Assignments
    Route::get('assignments', [\App\Http\Controllers\Web\FeeAssignmentController::class, 'index'])->name('assignments.index');
    Route::post('assignments', [\App\Http\Controllers\Web\FeeAssignmentController::class, 'store'])->name('assignments.store');
    
    // Payment Collection
    Route::get('payments', [\App\Http\Controllers\Web\FeePaymentController::class, 'index'])->name('payments.index');
    Route::get('payments/create', [\App\Http\Controllers\Web\FeePaymentController::class, 'create'])->name('payments.create');
    Route::post('payments', [\App\Http\Controllers\Web\FeePaymentController::class, 'store'])->name('payments.store');
    Route::get('payments/{payment}/receipt', [\App\Http\Controllers\Web\FeePaymentController::class, 'receipt'])->name('payments.receipt');
    Route::get('payments/{payment}/download', [\App\Http\Controllers\Web\FeePaymentController::class, 'downloadReceipt'])->name('payments.download');
    
    // Outstanding Fees
    Route::get('outstanding', [\App\Http\Controllers\Web\FeeOutstandingController::class, 'index'])->name('outstanding.index');
    
    // Scholarships
    Route::resource('scholarships', \App\Http\Controllers\Web\ScholarshipController::class)
        ->names('scholarships');
});

// Student Fee Routes
Route::middleware(['auth', 'role:student'])->prefix('student/fees')->name('student.fees.')->group(function () {
    Route::get('/', [\App\Http\Controllers\Web\StudentFeeController::class, 'index'])->name('index');
    Route::get('/payment/{studentFee}', [\App\Http\Controllers\Web\StudentFeeController::class, 'payment'])->name('payment');
});

// Razorpay Payment Routes
Route::middleware(['auth'])->prefix('razorpay')->group(function () {
    Route::post('/create-order', [\App\Http\Controllers\Web\RazorpayController::class, 'createOrder']);
    Route::post('/verify-payment', [\App\Http\Controllers\Web\RazorpayController::class, 'verifyPayment']);
});
Route::post('/razorpay/webhook', [\App\Http\Controllers\Web\RazorpayController::class, 'webhook']);

// Scholarship Application Routes
Route::middleware(['auth', 'role:admin|principal|office'])->prefix('fees/scholarship-applications')->name('fees.scholarship-applications.')->group(function () {
    Route::get('/', [\App\Http\Controllers\Web\ScholarshipApplicationController::class, 'index'])->name('index');
    Route::get('/create', [\App\Http\Controllers\Web\ScholarshipApplicationController::class, 'create'])->name('create');
    Route::post('/', [\App\Http\Controllers\Web\ScholarshipApplicationController::class, 'store'])->name('store');
    Route::post('/{application}/approve', [\App\Http\Controllers\Web\ScholarshipApplicationController::class, 'approve'])->name('approve');
    Route::post('/{application}/reject', [\App\Http\Controllers\Web\ScholarshipApplicationController::class, 'reject'])->name('reject');
});


Route::middleware(['auth'])->group(function () {
    // Teacher Management
    Route::resource('dashboard/teachers', \App\Http\Controllers\Web\TeacherController::class)
        ->names('dashboard.teachers');
    
    // Test storage route
    Route::get('/test-storage', function() {
        $students = \App\Models\User\Student::with(['program', 'division'])->limit(5)->get();
        return view('test-storage', compact('students'));
    });
    
    // Dashboard student routes
    Route::get('/dashboard/students', [StudentController::class, 'index'])->name('dashboard.students.index');
    Route::get('/dashboard/students/create', [StudentController::class, 'create'])->name('dashboard.students.create');
    Route::post('/dashboard/students', [StudentController::class, 'store'])->name('dashboard.students.store');
    Route::get('/dashboard/students/{student}', [StudentController::class, 'show'])->name('dashboard.students.show');
    Route::get('/dashboard/students/{student}/edit', [StudentController::class, 'edit'])->name('dashboard.students.edit');
    Route::put('/dashboard/students/{student}', [StudentController::class, 'update'])->name('dashboard.students.update');
    Route::delete('/dashboard/students/{student}', [StudentController::class, 'destroy'])->name('dashboard.students.destroy');
});

// Guardian CRUD under student (dashboard prefixed) - use model binding
Route::middleware(['auth'])->prefix('dashboard/students/{student}/guardians')
    ->name('dashboard.students.guardians.')
    ->group(function () {
        Route::get('/create', [GuardianController::class, 'create'])->name('create');
        Route::post('/', [GuardianController::class, 'store'])->name('store');
        Route::get('/{guardian}/edit', [GuardianController::class, 'edit'])->name('edit');
        Route::put('/{guardian}', [GuardianController::class, 'update'])->name('update');
        Route::delete('/{guardian}', [GuardianController::class, 'destroy'])->name('destroy');
    });
// Authentication Routes
Route::get('/login', [AuthController::class, 'showLogin'])->name('login');
Route::post('/login', [AuthController::class, 'login']);
Route::post('/logout', [AuthController::class, 'logout'])->name('logout');

// Password Reset Routes
Route::get('/forgot-password', [\App\Http\Controllers\Web\PasswordResetController::class, 'showResetRequestForm'])->name('password.request');
Route::post('/forgot-password', [\App\Http\Controllers\Web\PasswordResetController::class, 'sendResetLink'])->name('password.email');
Route::get('/reset-password/{token}', [\App\Http\Controllers\Web\PasswordResetController::class, 'showResetForm'])->name('password.reset');
Route::post('/reset-password', [\App\Http\Controllers\Web\PasswordResetController::class, 'reset'])->name('password.update');

// Public admission form
Route::get('/apply', [AdmissionController::class, 'showApplyForm'])->name('admissions.apply.form');
Route::post('/apply', [AdmissionController::class, 'apply'])->name('admissions.apply');

// Root route - redirect to login if not authenticated
Route::get('/', function() {
    if (auth()->check()) {
        $role = auth()->user()->roles->first()->name ?? 'student';
        if ($role === 'teacher') {
            return redirect()->route('teacher.dashboard');
        }
        if ($role === 'admin') {
            return redirect()->route('dashboard.principal');
        }
        return redirect()->route("dashboard.{$role}");
    }
    return redirect()->route('login');
});

// Protected Routes
Route::middleware(['auth'])->group(function () {
    
    Route::get('/dashboard/principal', [\App\Http\Controllers\Web\PrincipalDashboardController::class, 'index'])
        ->name('dashboard.principal');
    
    Route::get('/dashboard/admin', [\App\Http\Controllers\Web\PrincipalDashboardController::class, 'index'])
        ->name('dashboard.admin');
    
    // Teacher Dashboard Routes
    Route::middleware('role:teacher')->prefix('teacher')->name('teacher.')->group(function () {
        Route::get('/dashboard', [\App\Http\Controllers\Web\TeacherDashboardController::class, 'index'])->name('dashboard');
        Route::get('/students', [\App\Http\Controllers\Web\TeacherDashboardController::class, 'students'])->name('students');
        Route::get('/attendance', [\App\Http\Controllers\Web\TeacherDashboardController::class, 'attendance'])->name('attendance');
    });
    
    Route::get('/dashboard/student', [DashboardController::class, 'student'])->name('dashboard.student');
    Route::get('/dashboard/teacher', [DashboardController::class, 'teacher'])->name('dashboard.teacher');
    Route::get('/dashboard/office', [DashboardController::class, 'office'])->name('dashboard.office');
    Route::get('/dashboard/accounts_staff', [DashboardController::class, 'accounts_staff'])->name('dashboard.accounts_staff');
    Route::get('/dashboard/librarian', [DashboardController::class, 'librarian'])->name('dashboard.librarian');
    
    // Student Management
    // (handled earlier with explicit dashboard routes)
    // Route::resource('students', StudentController::class);
    // Route::get('/dashboard/students', [StudentController::class, 'index'])->name('dashboard.students');
    
    // Admission Management
    Route::get('/admissions', [AdmissionController::class, 'index'])->name('admissions.index');
    Route::get('/admissions/{admission}', [AdmissionController::class, 'show'])->name('admissions.show');
    Route::post('/admissions/{admission}/verify', [AdmissionController::class, 'verify'])->name('admissions.verify');
    Route::post('/admissions/{admission}/reject', [AdmissionController::class, 'reject'])->name('admissions.reject');
});


// ============================================
// NEW MODULES - Added for Complete ERP System
// ============================================

use App\Http\Controllers\Web\ExaminationController;
use App\Http\Controllers\Web\LibraryController;
use App\Http\Controllers\Web\StaffController;

// Examination Management
Route::middleware(['auth'])->prefix('examinations')->name('examinations.')->group(function () {
    Route::get('/', [ExaminationController::class, 'index'])->name('index');
    Route::get('/create', [ExaminationController::class, 'create'])->name('create');
    Route::post('/', [ExaminationController::class, 'store'])->name('store');
    Route::get('/{examination}', [ExaminationController::class, 'show'])->name('show');
    Route::get('/{examination}/edit', [ExaminationController::class, 'edit'])->name('edit');
    Route::put('/{examination}', [ExaminationController::class, 'update'])->name('update');
    Route::get('/{examination}/marks-entry', [ExaminationController::class, 'marksEntry'])->name('marks-entry');
    Route::post('/{examination}/save-marks', [ExaminationController::class, 'saveMarks'])->name('save-marks');
    Route::delete('/{examination}', [ExaminationController::class, 'destroy'])->name('destroy');
});

// Results Management
Route::middleware(['auth'])->prefix('results')->name('results.')->group(function () {
    Route::get('/', [\App\Http\Controllers\Web\ResultController::class, 'index'])->name('index');
    Route::get('/generate', [\App\Http\Controllers\Web\ResultController::class, 'generate'])->name('generate');
    Route::get('/pdf', [\App\Http\Controllers\Web\ResultController::class, 'pdf'])->name('pdf');
    Route::get('/student/{student}', [\App\Http\Controllers\Web\ResultController::class, 'studentResult'])->name('student');
});

// Reports Management
Route::middleware(['auth'])->prefix('reports')->name('reports.')->group(function () {
    Route::get('/attendance', [\App\Http\Controllers\Web\ReportController::class, 'attendance'])->name('attendance');
    Route::get('/attendance/pdf', [\App\Http\Controllers\Web\ReportController::class, 'attendancePdf'])->name('attendance.pdf');
    Route::get('/attendance/excel', [\App\Http\Controllers\Web\ReportController::class, 'attendanceExcel'])->name('attendance.excel');
});

// Library Management
Route::middleware(['auth'])->prefix('library')->name('library.')->group(function () {
    // Books
    Route::prefix('books')->name('books.')->group(function () {
        Route::get('/', [LibraryController::class, 'index'])->name('index');
        Route::get('/create', [LibraryController::class, 'create'])->name('create');
        Route::post('/', [LibraryController::class, 'store'])->name('store');
        Route::get('/{book}/edit', [LibraryController::class, 'edit'])->name('edit');
        Route::put('/{book}', [LibraryController::class, 'update'])->name('update');
        Route::delete('/{book}', [LibraryController::class, 'destroy'])->name('destroy');
    });
    
    // Book Issues
    Route::prefix('issues')->name('issues.')->group(function () {
        Route::get('/', [LibraryController::class, 'issuesIndex'])->name('index');
        Route::get('/create', [LibraryController::class, 'issueForm'])->name('create');
        Route::post('/', [LibraryController::class, 'issue'])->name('store');
        Route::post('/{issue}/return', [LibraryController::class, 'returnBook'])->name('return');
    });
});

// Staff Management
Route::middleware(['auth'])->prefix('staff')->name('staff.')->group(function () {
    Route::get('/', [StaffController::class, 'index'])->name('index');
    Route::get('/create', [StaffController::class, 'create'])->name('create');
    Route::post('/', [StaffController::class, 'store'])->name('store');
    Route::get('/{staff}', [StaffController::class, 'show'])->name('show');
    Route::get('/{staff}/edit', [StaffController::class, 'edit'])->name('edit');
    Route::put('/{staff}', [StaffController::class, 'update'])->name('update');
    Route::delete('/{staff}', [StaffController::class, 'destroy'])->name('destroy');
});
