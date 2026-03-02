@echo off
REM ============================================
REM SchoolERP - Complete Setup Script
REM ============================================

echo.
echo ========================================
echo SchoolERP Complete Setup
echo ========================================
echo.

echo Step 1: Checking MySQL connection...
php artisan db:show
if errorlevel 1 (
    echo.
    echo [ERROR] MySQL is not running!
    echo Please start MySQL in XAMPP Control Panel
    echo Then run this script again.
    pause
    exit /b 1
)

echo.
echo Step 2: Running migrations...
php artisan migrate
if errorlevel 1 (
    echo [ERROR] Migration failed!
    pause
    exit /b 1
)

echo.
echo Step 3: Seeding grades...
php artisan db:seed --class=GradeSeeder
if errorlevel 1 (
    echo [WARNING] Grade seeding failed (may already exist)
)

echo.
echo Step 4: Installing PDF package...
composer require barryvdh/laravel-dompdf
if errorlevel 1 (
    echo [WARNING] PDF package installation failed
)

echo.
echo Step 5: Clearing caches...
php artisan config:clear
php artisan route:clear
php artisan view:clear
php artisan cache:clear

echo.
echo Step 6: Creating storage link...
php artisan storage:link

echo.
echo Step 7: Listing routes...
php artisan route:list | findstr "examinations results library staff leaves"

echo.
echo ========================================
echo Setup Complete!
echo ========================================
echo.
echo New modules available:
echo - Examinations: http://localhost:8000/examinations
echo - Results: http://localhost:8000/results
echo - Library: http://localhost:8000/library/books
echo - Staff: http://localhost:8000/staff
echo - Leaves: http://localhost:8000/leaves
echo.
echo To start the server, run:
echo php artisan serve
echo.
pause
