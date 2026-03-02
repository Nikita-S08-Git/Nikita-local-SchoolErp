<?php

use App\Http\Controllers\Student\AuthController;
use App\Http\Controllers\Student\DashboardController;
use Illuminate\Support\Facades\Route;

/*
|--------------------------------------------------------------------------
| Student Routes
|--------------------------------------------------------------------------
|
| These routes are for the student dashboard and are protected by
| the student auth middleware.
|
*/

// Guest routes (login)
Route::middleware('guest:student')->group(function () {
    Route::get('/student/login', [AuthController::class, 'showLogin'])->name('student.login');
    Route::post('/student/login', [AuthController::class, 'login']);
});

// Authenticated student routes
Route::middleware('auth:student')->prefix('student')->name('student.')->group(function () {
    
    // Logout
    Route::post('/logout', [AuthController::class, 'logout'])->name('logout');
    
    // Dashboard
    Route::get('/dashboard', [DashboardController::class, 'index'])->name('dashboard');
    
    // Profile
    Route::get('/profile', [DashboardController::class, 'profile'])->name('profile');
    Route::get('/profile/edit', [DashboardController::class, 'editProfile'])->name('profile.edit');
    Route::put('/profile', [DashboardController::class, 'updateProfile'])->name('profile.update');
    Route::get('/profile/change-password', [DashboardController::class, 'changePassword'])->name('profile.change-password');
    Route::post('/profile/change-password', [DashboardController::class, 'updatePassword']);
    
    // Timetable
    Route::get('/timetable', [DashboardController::class, 'timetable'])->name('timetable');
    
    // Attendance
    Route::get('/attendance', [DashboardController::class, 'attendance'])->name('attendance');
    
    // Notifications
    Route::get('/notifications', [DashboardController::class, 'notifications'])->name('notifications');
    Route::post('/notifications/{id}/read', [DashboardController::class, 'markNotificationAsRead'])->name('notifications.read');
    Route::post('/notifications/read-all', [DashboardController::class, 'markAllNotificationsAsRead'])->name('notifications.read-all');
});
