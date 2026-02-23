@echo off
cls
echo.
echo ========================================================
echo    SCHOOL ERP SYSTEM - COMPLETE SETUP
echo ========================================================
echo.
echo This will set up your School ERP with sample data:
echo.
echo  [1] Grades System
echo  [2] Academic Sessions
echo  [3] Programs (Classes)
echo  [4] Divisions (Sections)
echo  [5] Teachers
echo  [6] Students
echo  [7] Complete Timetable (with teachers, rooms, subjects)
echo  [8] Attendance Records (last 30 days)
echo.
echo ========================================================
echo.
pause
echo.

echo Starting complete setup...
echo.

php artisan db:seed --class=CompleteSchoolDataSeeder

echo.
echo ========================================================
echo    SETUP COMPLETE!
echo ========================================================
echo.
echo Your School ERP is now ready with sample data!
echo.
echo Start the server:
echo   php artisan serve
echo.
echo Then visit:
echo   http://127.0.0.1:8000
echo.
echo Login credentials are in CREDENTIALS.md file
echo.
pause
