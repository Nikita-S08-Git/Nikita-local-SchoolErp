<?php

// ============================================
// ADD THESE ROUTES TO routes/web.php
// ============================================

use App\Http\Controllers\Web\ExaminationController;
use App\Http\Controllers\Web\ResultController;
use App\Http\Controllers\Web\LibraryController;
use App\Http\Controllers\Web\StaffController;
use App\Http\Controllers\Web\LeaveController;

// Examination Management
Route::middleware(['auth'])->prefix('examinations')->name('examinations.')->group(function () {
    Route::get('/', [ExaminationController::class, 'index'])->name('index');
    Route::get('/create', [ExaminationController::class, 'create'])->name('create');
    Route::post('/', [ExaminationController::class, 'store'])->name('store');
    Route::get('/{examination}/marks-entry', [ExaminationController::class, 'marksEntry'])->name('marks-entry');
    Route::get('/{examination}/students', [ExaminationController::class, 'getStudents'])->name('get-students');
    Route::post('/{examination}/save-marks', [ExaminationController::class, 'saveMarks'])->name('save-marks');
    Route::delete('/{examination}', [ExaminationController::class, 'destroy'])->name('destroy');
});

// Results Management
Route::middleware(['auth'])->prefix('results')->name('results.')->group(function () {
    Route::get('/', [ResultController::class, 'index'])->name('index');
    Route::get('/student/{student}', [ResultController::class, 'studentResult'])->name('student');
    Route::get('/division/{division}', [ResultController::class, 'divisionResults'])->name('division');
    Route::get('/report-card/{student}', [ResultController::class, 'generateReportCard'])->name('report-card');
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

// Leave Management
Route::middleware(['auth'])->prefix('leaves')->name('leaves.')->group(function () {
    Route::get('/', [LeaveController::class, 'index'])->name('index');
    Route::get('/my-leaves', [LeaveController::class, 'myLeaves'])->name('my-leaves');
    Route::get('/create', [LeaveController::class, 'create'])->name('create');
    Route::post('/', [LeaveController::class, 'store'])->name('store');
    Route::post('/{leave}/approve', [LeaveController::class, 'approve'])->name('approve');
    Route::post('/{leave}/reject', [LeaveController::class, 'reject'])->name('reject');
    Route::delete('/{leave}', [LeaveController::class, 'destroy'])->name('destroy');
});
