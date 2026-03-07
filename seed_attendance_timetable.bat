@echo off
echo ========================================
echo  Seeding Attendance and Timetable Data
echo ========================================
echo.

echo [1/2] Seeding Timetable Data...
php artisan db:seed --class=TimetableSeeder
echo.

echo [2/2] Seeding Attendance Data...
php artisan db:seed --class=AttendanceSeeder
echo.

echo ========================================
echo  Data Seeding Complete!
echo ========================================
echo.
echo You can now:
echo - View Timetable: http://127.0.0.1:8000/academic/timetable
echo - Mark Attendance: http://127.0.0.1:8000/academic/attendance
echo - View Reports: http://127.0.0.1:8000/reports/attendance
echo.
pause
