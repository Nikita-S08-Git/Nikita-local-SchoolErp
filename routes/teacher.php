<?php

use App\Http\Controllers\Teacher\DashboardController;
use App\Http\Controllers\Teacher\AttendanceController;
use App\Http\Controllers\Web\Teacher\StudentsController;
use App\Http\Controllers\Web\ExaminationController;
use Illuminate\Support\Facades\Route;

/*
|--------------------------------------------------------------------------
| Teacher Dashboard Routes
|--------------------------------------------------------------------------
|
| These routes are for the teacher dashboard and are protected by
| the 'auth' and role middleware.
|
*/

Route::middleware(['auth', 'role:teacher|class_teacher|subject_teacher|hod_commerce|hod_science|hod_management|hod_arts'])->prefix('teacher')->name('teacher.')->group(function () {

    // Dashboard Home
    Route::get('/dashboard', [DashboardController::class, 'index'])->name('dashboard');

    // Teacher Profile
    Route::get('/profile', [DashboardController::class, 'profile'])->name('profile');
    Route::get('/profile/edit', [DashboardController::class, 'editProfile'])->name('profile.edit');
    Route::put('/profile', [DashboardController::class, 'updateProfile'])->name('profile.update');

    // Assigned Divisions
    Route::get('/divisions', [DashboardController::class, 'divisions'])->name('divisions.index');
    Route::get('/divisions/{divisionId}/students', [DashboardController::class, 'divisionStudents'])->name('divisions.students');

    // Student Details
    Route::get('/students/{studentId}', [DashboardController::class, 'studentDetails'])->name('students.details');

    // Results Management
    Route::prefix('results')->name('results.')->group(function () {
        // View all students' results for teacher's divisions
        Route::get('/', [ExaminationController::class, 'teacherResults'])->name('index');
        // View results by division
        Route::get('/division/{divisionId}', [ExaminationController::class, 'divisionResults'])->name('division');
        // Enter/Edit marks
        Route::get('/enter/{examinationId}/{divisionId}', [ExaminationController::class, 'enterMarks'])->name('enter');
        Route::post('/store-marks', [ExaminationController::class, 'storeMarks'])->name('store-marks');
    });

    // My Students (Resource routes)
    Route::get('/students', [StudentsController::class, 'index'])->name('students.index');
    Route::get('/students/{student}', [StudentsController::class, 'show'])->name('students.show');

    // Attendance Management
    Route::prefix('attendance')->name('attendance.')->group(function () {
        // Attendance Dashboard
        Route::get('/', [AttendanceController::class, 'index'])->name('index');

        // Mark Attendance (for specific timetable) - must be before wildcard routes
        Route::get('/create/{timetableId}', [AttendanceController::class, 'create'])->name('create');
        Route::post('/store/{timetableId}', [AttendanceController::class, 'store'])->name('store');

        // Attendance History
        Route::get('/history', [AttendanceController::class, 'history'])->name('history');

        // Attendance Report
        Route::get('/report', [AttendanceController::class, 'report'])->name('report');

        // Edit Attendance - wildcard routes at the end
        Route::get('/{attendanceId}/edit', [AttendanceController::class, 'edit'])->name('edit');
        Route::put('/{attendanceId}', [AttendanceController::class, 'update'])->name('update');
    });
});
