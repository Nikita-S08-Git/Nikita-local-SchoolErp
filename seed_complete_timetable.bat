@echo off
echo ========================================
echo  Seeding Complete Timetable Data
echo ========================================
echo.
echo This will create a complete timetable with:
echo - Each division gets a full weekly schedule
echo - Each day has 5 periods (09:00-16:00)
echo - Each period has: Teacher, Subject, Room
echo - Lunch break: 13:00-14:00
echo.
pause
echo.

echo [1/2] Clearing old timetable data...
php artisan tinker --execute="App\Models\Attendance\Timetable::truncate();"
echo.

echo [2/2] Creating new timetable schedules...
php artisan db:seed --class=DetailedTimetableSeeder
echo.

echo ========================================
echo  Timetable Created Successfully!
echo ========================================
echo.
echo View your timetable at:
echo http://127.0.0.1:8000/academic/timetable
echo.
echo Select a division to see the weekly schedule
echo with all teachers, subjects, and rooms assigned.
echo.
pause
