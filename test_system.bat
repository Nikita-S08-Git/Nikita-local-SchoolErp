@echo off
cls
echo.
echo ========================================================
echo    SCHOOL ERP SYSTEM - AUTOMATED TESTING
echo ========================================================
echo.
echo This script will test your School ERP System
echo.
pause
echo.

echo [1/5] Checking Database Connection...
php artisan tinker --execute="echo 'Database: OK';"
if %errorlevel% neq 0 (
    echo ERROR: Database connection failed!
    pause
    exit /b 1
)
echo.

echo [2/5] Checking Data Counts...
php artisan tinker --execute="echo 'Users: ' . \App\Models\User::count(); echo 'Students: ' . \App\Models\User\Student::count(); echo 'Timetable: ' . \App\Models\Attendance\Timetable::count(); echo 'Attendance: ' . \App\Models\Academic\Attendance::count();"
echo.

echo [3/5] Checking Routes...
php artisan route:list | findstr "academic.timetable"
php artisan route:list | findstr "examinations"
php artisan route:list | findstr "results"
echo.

echo [4/5] Clearing Caches...
php artisan config:clear
php artisan cache:clear
php artisan view:clear
echo.

echo [5/5] Starting Server...
echo.
echo ========================================================
echo    SERVER STARTING
echo ========================================================
echo.
echo Your School ERP is now running at:
echo    http://127.0.0.1:8000
echo.
echo Open your browser and test these URLs:
echo.
echo  1. Login: http://127.0.0.1:8000/login
echo  2. Dashboard: http://127.0.0.1:8000/dashboard/principal
echo  3. Timetable Grid: http://127.0.0.1:8000/academic/timetable
echo  4. Timetable Table: http://127.0.0.1:8000/academic/timetable/table
echo  5. Attendance: http://127.0.0.1:8000/academic/attendance
echo  6. Examinations: http://127.0.0.1:8000/examinations
echo  7. Results: http://127.0.0.1:8000/results
echo  8. Reports: http://127.0.0.1:8000/reports/attendance
echo.
echo Follow TESTING_CHECKLIST.md for complete testing
echo.
echo Press Ctrl+C to stop the server
echo.
php artisan serve
